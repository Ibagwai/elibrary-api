<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProject extends Model
{
    protected $fillable = ['content_item_id', 'student_name', 'supervisor_name', 'degree_level', 'department', 'submission_date'];
    public $timestamps = false;
}
