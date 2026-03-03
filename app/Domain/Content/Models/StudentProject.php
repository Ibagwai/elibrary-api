<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProject extends Model
{
    protected $fillable = ['content_item_id', 'student_name', 'supervisor_name', 'degree_level', 'department', 'institution', 'submission_year', 'grade'];

    public function contentItem()
    {
        return $this->belongsTo(ContentItem::class);
    }
}
