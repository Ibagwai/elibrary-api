<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lecture extends Model
{
    protected $fillable = ['content_item_id', 'instructor_name', 'course_code', 'course_name', 'duration_seconds', 'video_url'];
    public $timestamps = false;
}
