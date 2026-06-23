<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure the admin contact endpoint cannot be accessed without authentication.
     * Garantiza que el endpoint administrativo de contacto no pueda accederse sin autenticacion.
     */
    public function test_contacts_admin_requires_authentication(): void
    {
        $this->getJson('/api/contacts')->assertUnauthorized();
    }

    /**
     * Ensure an authenticated user can retrieve only their own contact data.
     * Garantiza que un usuario autenticado pueda obtener unicamente sus propios datos de contacto.
     */
    public function test_authenticated_user_can_fetch_own_contact_data(): void
    {
        $user = User::factory()->create([
            'phone1' => '111111111',
            'phone2' => '222222222',
            'contactEmail1' => 'primary@example.com',
            'contactEmail2' => 'secondary@example.com',
            'contactEmail3' => 'third@example.com',
            'github' => 'https://github.com/example',
            'linkedin' => 'https://linkedin.com/in/example',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/contacts');

        $response
            ->assertOk()
            ->assertJson([
                'phone1' => '111111111',
                'phone2' => '222222222',
                'contactEmail1' => 'primary@example.com',
                'contactEmail2' => 'secondary@example.com',
                'contactEmail3' => 'third@example.com',
                'github' => 'https://github.com/example',
                'linkedin' => 'https://linkedin.com/in/example',
            ]);
    }

    /**
     * Ensure an authenticated user can update their contact data and persist the changes.
     * Garantiza que un usuario autenticado pueda actualizar sus datos de contacto y persistir los cambios.
     */
    public function test_authenticated_user_can_update_own_contact_data(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/contacts', [
            'phone1' => '600123123',
            'contactEmail1' => 'contact@example.com',
            'github' => 'https://github.com/updated',
            'linkedin' => 'https://linkedin.com/in/updated',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'phone1' => '600123123',
                'contactEmail1' => 'contact@example.com',
                'github' => 'https://github.com/updated',
                'linkedin' => 'https://linkedin.com/in/updated',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'phone1' => '600123123',
            'contactEmail1' => 'contact@example.com',
            'github' => 'https://github.com/updated',
            'linkedin' => 'https://linkedin.com/in/updated',
        ]);
    }
}