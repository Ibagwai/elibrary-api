<?php

namespace App\Services;

use App\Domain\Content\Models\ContentItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getDashboardStats(): array
    {
        return [
            'total_content' => ContentItem::count(),
            'total_users' => User::count(),
            'total_downloads' => ContentItem::sum('download_count'),
            'total_views' => ContentItem::sum('view_count'),
            'pending_approval' => ContentItem::where('status', 'under_review')->count(),
            'content_by_type' => ContentItem::selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get(),
        ];
    }

    public function getDownloadTrend(string $period = 'daily', int $limit = 30): array
    {
        $dateFormat = match($period) {
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        return ContentItem::selectRaw("
            DATE_FORMAT(created_at, '{$dateFormat}') as period,
            SUM(download_count) as total_downloads,
            COUNT(*) as content_count
        ")
        ->groupBy('period')
        ->orderBy('period', 'desc')
        ->limit($limit)
        ->get()
        ->toArray();
    }

    public function getTopContent(int $limit = 10): array
    {
        return ContentItem::with(['categories', 'tags'])
            ->orderBy('download_count', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getUserActivitySummary(int $userId): array
    {
        return [
            'bookmarks' => DB::table('bookmarks')->where('user_id', $userId)->count(),
            'downloads' => DB::table('download_logs')->where('user_id', $userId)->count(),
            'reviews' => DB::table('reviews')->where('user_id', $userId)->count(),
            'reading_progress' => DB::table('reading_progress')->where('user_id', $userId)->count(),
        ];
    }
}
