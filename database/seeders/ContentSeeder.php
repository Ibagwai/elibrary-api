<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentItem;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        // Ebook 1
        $ebook1 = ContentItem::create([
            'type' => 'ebook',
            'title' => 'Introduction to Machine Learning',
            'slug' => 'introduction-to-machine-learning',
            'description' => 'Comprehensive guide to machine learning fundamentals',
            'abstract' => 'This book covers the essential concepts of machine learning including supervised and unsupervised learning.',
            'language' => 'en',
            'published_year' => 2023,
            'access_level' => 'public',
            'status' => 'published',
            'featured' => true,
            'uploaded_by' => $admin->id,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        DB::table('ebooks')->insert([
            'content_item_id' => $ebook1->id,
            'isbn' => '978-3-16-148410-0',
            'author' => 'Dr. James Okonkwo',
            'publisher' => 'Academic Press',
            'edition' => '1st',
            'pages' => 450,
            'file_format' => 'pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Journal 1
        $journal1 = ContentItem::create([
            'type' => 'journal',
            'title' => 'Advances in Neural Networks',
            'slug' => 'advances-in-neural-networks',
            'description' => 'Research paper on deep learning architectures',
            'abstract' => 'This paper presents novel approaches to neural network optimization.',
            'language' => 'en',
            'published_year' => 2024,
            'access_level' => 'public',
            'status' => 'published',
            'featured' => false,
            'uploaded_by' => $admin->id,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        DB::table('journals')->insert([
            'content_item_id' => $journal1->id,
            'issn' => '1234-5678',
            'volume' => '15',
            'issue' => '3',
            'doi' => '10.1234/example.2024.001',
            'journal_name' => 'Journal of AI Research',
            'peer_reviewed' => true,
            'authors' => json_encode(['Dr. Ada Nwosu', 'Prof. Chidi Eze']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Student Project 1
        $project1 = ContentItem::create([
            'type' => 'student_project',
            'title' => 'Smart Campus Management System',
            'slug' => 'smart-campus-management-system',
            'description' => 'Final year project on IoT-based campus management',
            'abstract' => 'This project implements a comprehensive system for managing campus resources using IoT sensors.',
            'language' => 'en',
            'published_year' => 2024,
            'access_level' => 'public',
            'status' => 'published',
            'featured' => true,
            'uploaded_by' => $admin->id,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        DB::table('student_projects')->insert([
            'content_item_id' => $project1->id,
            'student_name' => 'Chioma Adebayo',
            'supervisor_name' => 'Dr. Emeka Okafor',
            'degree_level' => 'undergraduate',
            'department' => 'Computer Science',
            'institution' => 'University of Lagos',
            'submission_year' => 2024,
            'grade' => 'A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Lecture 1
        $lecture1 = ContentItem::create([
            'type' => 'lecture',
            'title' => 'Introduction to Data Structures',
            'slug' => 'introduction-to-data-structures',
            'description' => 'Comprehensive lecture series on fundamental data structures',
            'abstract' => 'This lecture covers arrays, linked lists, stacks, queues, trees, and graphs.',
            'language' => 'en',
            'published_year' => 2024,
            'access_level' => 'public',
            'status' => 'published',
            'featured' => false,
            'uploaded_by' => $admin->id,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        DB::table('lectures')->insert([
            'content_item_id' => $lecture1->id,
            'instructor_name' => 'Prof. Ngozi Okeke',
            'course_code' => 'CSC201',
            'course_name' => 'Data Structures and Algorithms',
            'media_type' => 'video',
            'duration_seconds' => 3600,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
