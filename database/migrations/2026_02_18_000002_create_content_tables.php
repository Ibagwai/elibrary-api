<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->string('icon', 50)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->timestamps();
        });

        Schema::create('content_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['ebook', 'journal', 'student_project', 'lecture']);
            $table->string('title', 500);
            $table->string('slug', 500);
            $table->text('description')->nullable();
            $table->text('abstract')->nullable();
            $table->string('language', 10)->default('en');
            $table->year('published_year')->nullable();
            $table->enum('access_level', ['public', 'authenticated', 'faculty_only', 'admin_only'])->default('authenticated');
            $table->enum('status', ['draft', 'under_review', 'published', 'archived'])->default('draft');
            $table->boolean('featured')->default(false);
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->json('meta')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index(['type', 'status']);
            $table->index(['access_level', 'status']);
            $table->index(['featured', 'status']);
        });

        Schema::create('ebooks', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('content_item_id')->constrained()->onDelete('cascade');
            $table->string('isbn', 20)->nullable();
            $table->string('author', 500);
            $table->string('publisher', 255)->nullable();
            $table->string('edition', 50)->nullable();
            $table->integer('pages')->nullable();
            $table->enum('file_format', ['pdf', 'epub', 'both'])->default('pdf');
            $table->string('call_number', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('content_item_id')->constrained()->onDelete('cascade');
            $table->string('issn', 20)->nullable();
            $table->string('volume', 20)->nullable();
            $table->string('issue', 20)->nullable();
            $table->string('doi', 255)->nullable()->unique();
            $table->string('journal_name', 500);
            $table->boolean('peer_reviewed')->default(true);
            $table->json('authors')->nullable();
            $table->timestamps();
        });

        Schema::create('student_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('content_item_id')->constrained()->onDelete('cascade');
            $table->string('student_name', 255);
            $table->string('supervisor_name', 255)->nullable();
            $table->enum('degree_level', ['undergraduate', 'masters', 'phd', 'diploma']);
            $table->string('department', 150);
            $table->string('institution', 255);
            $table->year('submission_year');
            $table->string('grade', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('lectures', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('content_item_id')->constrained()->onDelete('cascade');
            $table->string('instructor_name', 255);
            $table->string('course_code', 50)->nullable();
            $table->string('course_name', 255);
            $table->enum('media_type', ['video', 'audio', 'slides', 'mixed']);
            $table->integer('duration_seconds')->nullable();
            $table->string('transcript_url', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('content_category', function (Blueprint $table) {
            $table->foreignUuid('content_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->primary(['content_item_id', 'category_id']);
        });

        Schema::create('content_tag', function (Blueprint $table) {
            $table->foreignUuid('content_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->primary(['content_item_id', 'tag_id']);
        });

        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('content_item_id')->constrained()->onDelete('cascade');
            $table->text('note')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'content_item_id']);
        });

        Schema::create('download_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignUuid('content_item_id')->constrained()->onDelete('cascade');
            $table->ipAddress('ip_address');
            $table->text('user_agent')->nullable();
            $table->timestamp('downloaded_at')->useCurrent();
            
            $table->index(['content_item_id', 'downloaded_at']);
        });

        Schema::create('reading_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('content_item_id')->constrained()->onDelete('cascade');
            $table->integer('current_page')->default(1);
            $table->integer('total_pages')->nullable();
            $table->decimal('progress_percent', 5, 2)->default(0);
            $table->timestamp('last_read_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['user_id', 'content_item_id']);
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('content_item_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned();
            $table->text('review_text')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
            
            $table->unique(['user_id', 'content_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('reading_progress');
        Schema::dropIfExists('download_logs');
        Schema::dropIfExists('bookmarks');
        Schema::dropIfExists('content_tag');
        Schema::dropIfExists('content_category');
        Schema::dropIfExists('lectures');
        Schema::dropIfExists('student_projects');
        Schema::dropIfExists('journals');
        Schema::dropIfExists('ebooks');
        Schema::dropIfExists('content_items');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('categories');
    }
};
