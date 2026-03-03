<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany, HasOne, MorphMany};
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ContentItem extends Model implements HasMedia
{
    use Searchable, InteractsWithMedia;

    protected $fillable = [
        'type', 'title', 'slug', 'description', 'abstract', 'language',
        'published_year', 'access_level', 'status', 'featured',
        'view_count', 'download_count', 'meta', 'uploaded_by', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'meta' => 'array',
        'featured' => 'boolean',
        'approved_at' => 'datetime',
        'published_year' => 'integer',
    ];

    public function ebook(): HasOne
    {
        return $this->hasOne(Ebook::class);
    }

    public function journal(): HasOne
    {
        return $this->hasOne(Journal::class);
    }

    public function studentProject(): HasOne
    {
        return $this->hasOne(StudentProject::class);
    }

    public function lecture(): HasOne
    {
        return $this->hasOne(Lecture::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'content_category');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'content_tag');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'abstract' => $this->abstract,
            'type' => $this->type,
            'status' => $this->status,
        ];
    }
}
