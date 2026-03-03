<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $fillable = ['content_item_id', 'journal_name', 'volume', 'issue', 'doi', 'publication_date'];
    public $timestamps = false;
}
