<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure the public projects endpoint returns only published projects.
     * Garantiza que el endpoint publico de proyectos devuelva unicamente proyectos publicados.
     */
    public function test_public_projects_endpoint_returns_only_published_projects(): void
    {
        $published = Project::create([
            'title' => 'Published Project',
            'description' => 'Visible project',
            'status' => 'published',
        ]);

        $draft = Project::create([
            'title' => 'Draft Project',
            'description' => 'Hidden project',
            'status' => 'draft',
        ]);

        $technology = Technology::create([
            'name' => 'Laravel',
            'icon' => 'laravel-icon',
        ]);

        $published->technologies()->attach($technology);
        $draft->technologies()->attach($technology);

        $response = $this->getJson('/api/projects');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'title' => 'Published Project',
                'status' => 'published',
                'name' => 'Laravel',
            ])
            ->assertJsonMissing([
                'title' => 'Draft Project',
            ]);
    }

    /**
     * Ensure a published project can be retrieved from the public detail endpoint.
     * Garantiza que un proyecto publicado pueda obtenerse desde el endpoint publico de detalle.
     */
    public function test_public_show_returns_a_published_project(): void
    {
        $project = Project::create([
            'title' => 'Portfolio Project',
            'description' => 'Project description',
            'project_url' => 'https://example.com',
            'github_url' => 'https://github.com/example/project',
            'status' => 'published',
        ]);

        $technology = Technology::create([
            'name' => 'Next.js',
            'icon' => 'next-icon',
        ]);

        $project->technologies()->attach($technology);

        $response = $this->getJson('/api/projects/'.$project->id);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'title' => 'Portfolio Project',
                'status' => 'published',
                'name' => 'Next.js',
            ]);
    }

    /**
     * Ensure draft projects are not exposed through the public detail endpoint.
     * Garantiza que los proyectos en borrador no se expongan en el endpoint publico de detalle.
     */
    public function test_public_show_returns_not_found_for_draft_projects(): void
    {
        $project = Project::create([
            'title' => 'Draft Project',
            'description' => 'Draft description',
            'status' => 'draft',
        ]);

        $this->getJson('/api/projects/'.$project->id)
            ->assertNotFound()
            ->assertJson([
                'message' => 'Project not found.',
            ]);
    }

    /**
     * Ensure admin project listing is protected by authentication.
     * Garantiza que el listado administrativo de proyectos este protegido por autenticacion.
     */
    public function test_admin_project_listing_requires_authentication(): void
    {
        $this->getJson('/api/projects-admin')->assertUnauthorized();
    }

    /**
     * Ensure an authenticated user can list all projects including drafts.
     * Garantiza que un usuario autenticado pueda listar todos los proyectos incluyendo borradores.
     */
    public function test_authenticated_user_can_list_all_projects_including_drafts(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Project::create([
            'title' => 'Published Project',
            'description' => 'Visible project',
            'status' => 'published',
        ]);

        Project::create([
            'title' => 'Draft Project',
            'description' => 'Hidden project',
            'status' => 'draft',
        ]);

        $response = $this->getJson('/api/projects-admin');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment([
                'title' => 'Published Project',
            ])
            ->assertJsonFragment([
                'title' => 'Draft Project',
                'status' => 'draft',
            ]);
    }

    /**
     * Ensure an authenticated user can create a project and attach technologies.
     * Garantiza que un usuario autenticado pueda crear un proyecto y asociarle tecnologias.
     */
    public function test_authenticated_user_can_create_a_project(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $technologyA = Technology::create([
            'name' => 'Laravel',
            'icon' => 'laravel-icon',
        ]);

        $technologyB = Technology::create([
            'name' => 'PostgreSQL',
            'icon' => 'postgres-icon',
        ]);

        $response = $this->postJson('/api/projects', [
            'title' => 'New Project',
            'description' => 'A new project description',
            'project_url' => 'https://example.com/project',
            'github_url' => 'https://github.com/example/new-project',
            'status' => 'published',
            'technologies' => [$technologyA->id, $technologyB->id],
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'title' => 'New Project',
                'status' => 'published',
                'name' => 'Laravel',
            ])
            ->assertJsonFragment([
                'name' => 'PostgreSQL',
            ]);

        $this->assertDatabaseHas('projects', [
            'title' => 'New Project',
            'status' => 'published',
        ]);

        $this->assertDatabaseHas('project_technology', [
            'technology_id' => $technologyA->id,
        ]);
    }

    /**
     * Ensure an authenticated user can update a project and resync its technologies.
     * Garantiza que un usuario autenticado pueda actualizar un proyecto y resincronizar sus tecnologias.
     */
    public function test_authenticated_user_can_update_a_project(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $project = Project::create([
            'title' => 'Original Project',
            'description' => 'Original description',
            'status' => 'draft',
        ]);

        $oldTechnology = Technology::create([
            'name' => 'Vue',
            'icon' => 'vue-icon',
        ]);

        $newTechnology = Technology::create([
            'name' => 'React',
            'icon' => 'react-icon',
        ]);

        $project->technologies()->attach($oldTechnology);

        $response = $this->putJson('/api/projects/'.$project->id, [
            'title' => 'Updated Project',
            'status' => 'published',
            'technologies' => [$newTechnology->id],
        ]);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'title' => 'Updated Project',
                'status' => 'published',
                'name' => 'React',
            ])
            ->assertJsonMissing([
                'name' => 'Vue',
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'Updated Project',
            'status' => 'published',
        ]);

        $this->assertDatabaseMissing('project_technology', [
            'project_id' => $project->id,
            'technology_id' => $oldTechnology->id,
        ]);

        $this->assertDatabaseHas('project_technology', [
            'project_id' => $project->id,
            'technology_id' => $newTechnology->id,
        ]);
    }

    /**
     * Ensure an authenticated user can delete a project and its pivot rows.
     * Garantiza que un usuario autenticado pueda eliminar un proyecto y sus registros pivot.
     */
    public function test_authenticated_user_can_delete_a_project(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $project = Project::create([
            'title' => 'Disposable Project',
            'description' => 'To be removed',
            'status' => 'published',
        ]);

        $technology = Technology::create([
            'name' => 'Docker',
            'icon' => 'docker-icon',
        ]);

        $project->technologies()->attach($technology);

        $response = $this->deleteJson('/api/projects/'.$project->id);

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Project deleted successfully.',
            ]);

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);

        $this->assertDatabaseMissing('project_technology', [
            'project_id' => $project->id,
            'technology_id' => $technology->id,
        ]);
    }
}