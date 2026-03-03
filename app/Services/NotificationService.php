<?php

namespace App\Services;

use App\Models\User;
use App\Domain\Content\Models\ContentItem;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    public function notifyAdminNewSubmission(ContentItem $content): void
    {
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        
        foreach ($admins as $admin) {
            // In production, send actual email
            // Mail::to($admin)->send(new NewSubmissionMail($content));
            
            // For now, log it
            \Log::info("New submission notification sent to {$admin->email} for content: {$content->title}");
        }
    }

    public function notifyUserApproved(ContentItem $content): void
    {
        $user = $content->uploader;
        
        if ($user) {
            // Mail::to($user)->send(new ContentApprovedMail($content));
            \Log::info("Approval notification sent to {$user->email} for content: {$content->title}");
        }
    }

    public function notifyUserRejected(ContentItem $content, string $reason): void
    {
        $user = $content->uploader;
        
        if ($user) {
            // Mail::to($user)->send(new ContentRejectedMail($content, $reason));
            \Log::info("Rejection notification sent to {$user->email} for content: {$content->title}");
        }
    }

    public function sendWelcomeEmail(User $user): void
    {
        // Mail::to($user)->send(new WelcomeMail($user));
        \Log::info("Welcome email sent to {$user->email}");
    }

    public function sendPasswordResetEmail(User $user, string $token): void
    {
        // Mail::to($user)->send(new PasswordResetMail($token));
        \Log::info("Password reset email sent to {$user->email}");
    }
}
