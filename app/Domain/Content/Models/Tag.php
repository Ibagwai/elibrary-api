<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name', 'slug'];

    public function contentItems()
    {
        return $this->belongsToMany(ContentItem::class, 'content_tag');
    }
}
