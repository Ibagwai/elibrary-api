<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    protected $fillable = ['content_item_id', 'instructor_name', 'course_code', 'course_name', 'media_type', 'duration_seconds', 'transcript_url'];

    public function contentItem()
    {
        return $this->belongsTo(ContentItem::class);
    }
}
