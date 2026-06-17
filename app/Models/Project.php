<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'image',
        'project_url',
        'github_url',
        'status',
    ];

    /**
     * The technologies that belong to the project.
     */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class, 'project_technology');
    }

    /**
     * Scope to filter only published projects.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
