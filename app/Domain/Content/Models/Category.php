<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['parent_id', 'name', 'slug', 'icon', 'description'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function contentItems()
    {
        return $this->belongsToMany(ContentItem::class, 'content_category');
    }
}
