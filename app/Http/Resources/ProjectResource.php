<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'description'  => $this->description,
            'image_url'    => $this->image
                ? Storage::disk('public')->url($this->image)
                : null,
            'project_url'  => $this->project_url,
            'github_url'   => $this->github_url,
            'status'       => $this->status,
            'technologies' => $this->whenLoaded('technologies', function () {
                return $this->technologies->map(fn ($tech) => [
                    'id'   => $tech->id,
                    'name' => $tech->name,
                    'icon' => $tech->icon,
                ]);
            }),
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
        ];
    }
}
