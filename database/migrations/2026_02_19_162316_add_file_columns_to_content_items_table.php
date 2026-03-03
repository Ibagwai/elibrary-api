<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_items', function (Blueprint $table) {
            $table->string('file_path')->nullable()->after('slug');
            $table->bigInteger('file_size')->nullable()->after('file_path');
            $table->string('thumbnail_path')->nullable()->after('file_size');
        });
    }

    public function down(): void
    {
        Schema::table('content_items', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_size', 'thumbnail_path']);
        });
    }
};
