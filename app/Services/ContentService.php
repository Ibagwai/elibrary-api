<?php

namespace App\Services;

use App\Domain\Content\Models\ContentItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContentService
{
    public function createContent(array $data): ContentItem
    {
        return DB::transaction(function () use ($data) {
            $content = ContentItem::create([
                'type' => $data['type'],
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'description' => $data['description'] ?? null,
                'abstract' => $data['abstract'] ?? null,
                'language' => $data['language'] ?? 'en',
                'published_year' => $data['published_year'] ?? null,
                'access_level' => $data['access_level'],
                'status' => 'draft',
                'uploaded_by' => auth()->id(),
            ]);

            if (isset($data['categories'])) {
                $content->categories()->attach($data['categories']);
            }

            if (isset($data['tags'])) {
                $content->tags()->attach($data['tags']);
            }

            return $content->load(['categories', 'tags']);
        });
    }

    public function updateContent(int $id, array $data): ContentItem
    {
        $content = ContentItem::findOrFail($id);
        
        return DB::transaction(function () use ($content, $data) {
            $content->update($data);

            if (isset($data['categories'])) {
                $content->categories()->sync($data['categories']);
            }

            if (isset($data['tags'])) {
                $content->tags()->sync($data['tags']);
            }

            return $content->fresh(['categories', 'tags']);
        });
    }

    public function publishContent(int $id): ContentItem
    {
        $content = ContentItem::findOrFail($id);
        
        $content->update([
            'status' => 'published',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Trigger search index update
        app(SearchService::class)->indexContent($content);

        return $content;
    }

    public function deleteContent(int $id): bool
    {
        $content = ContentItem::findOrFail($id);
        
        // Remove from search index
        app(SearchService::class)->removeFromIndex($id);
        
        return $content->delete();
    }

    public function incrementViewCount(int $id): void
    {
        ContentItem::where('id', $id)->increment('view_count');
    }

    public function incrementDownloadCount(int $id): void
    {
        ContentItem::where('id', $id)->increment('download_count');
    }
}
