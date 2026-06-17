<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * List all published projects (public endpoint).
     */
    public function index(): AnonymousResourceCollection
    {
        $projects = Project::published()
            ->with('technologies')
            ->latest()
            ->get();

        return ProjectResource::collection($projects);
    }

    /**
     * List all projects including drafts (admin endpoint).
     */
    public function adminIndex(): AnonymousResourceCollection
    {
        $projects = Project::with('technologies')
            ->latest()
            ->get();

        return ProjectResource::collection($projects);
    }

    /**
     * Display a single published project.
     */
    public function show(Project $project): ProjectResource|JsonResponse
    {
        if ($project->status !== 'published') {
            return response()->json(['message' => 'Project not found.'], 404);
        }

        $project->load('technologies');

        return new ProjectResource($project);
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('projects', 'public');
        }

        $project = Project::create($data);

        if (! empty($data['technologies'])) {
            $project->technologies()->sync($data['technologies']);
        }

        $project->load('technologies');

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update the specified project.
     */
    public function update(UpdateProjectRequest $request, Project $project): ProjectResource
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($project->image) {
                Storage::disk('public')->delete($project->image);
            }
            $data['image'] = $request->file('image')->store('projects', 'public');
        }

        $project->update($data);

        if (array_key_exists('technologies', $data)) {
            $project->technologies()->sync($data['technologies'] ?? []);
        }

        $project->load('technologies');

        return new ProjectResource($project);
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project): JsonResponse
    {
        if ($project->image) {
            Storage::disk('public')->delete($project->image);
        }

        $project->technologies()->detach();
        $project->delete();

        return response()->json(['message' => 'Project deleted successfully.']);
    }
}
