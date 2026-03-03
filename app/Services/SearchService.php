<?php

namespace App\Services;

use App\Domain\Content\Models\ContentItem;
use Laravel\Scout\Searchable;

class SearchService
{
    public function search(string $query, array $filters = [], int $perPage = 20)
    {
        $search = ContentItem::search($query);

        if (isset($filters['type'])) {
            $search->where('type', $filters['type']);
        }

        if (isset($filters['language'])) {
            $search->where('language', $filters['language']);
        }

        if (isset($filters['access_level'])) {
            $search->where('access_level', $filters['access_level']);
        }

        return $search->paginate($perPage);
    }

    public function indexContent(ContentItem $content): void
    {
        $content->searchable();
    }

    public function removeFromIndex(int $id): void
    {
        $content = ContentItem::find($id);
        if ($content) {
            $content->unsearchable();
        }
    }

    public function rebuildIndex(): void
    {
        ContentItem::makeAllSearchable();
    }
}
