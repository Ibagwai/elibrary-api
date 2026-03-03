<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContentItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'content_items';
    
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'type', 'title', 'slug', 'description', 'abstract',
        'language', 'published_year', 'access_level', 'status',
        'featured', 'view_count', 'download_count', 'meta',
        'uploaded_by', 'approved_by', 'approved_at',
        'file_path', 'file_size'
    ];

    protected $casts = [
        'meta' => 'array',
        'featured' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the columns that should receive a unique identifier.
     */
    public function uniqueIds(): array
    {
        return ['id'];
    }

    public function ebook()
    {
        return $this->hasOne(Ebook::class);
    }

    public function journal()
    {
        return $this->hasOne(Journal::class);
    }

    public function student_project()
    {
        return $this->hasOne(StudentProject::class);
    }

    public function lecture()
    {
        return $this->hasOne(Lecture::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'content_category');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'content_tag');
    }

    public function uploader()
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }
}
