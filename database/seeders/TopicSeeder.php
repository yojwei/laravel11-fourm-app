<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'slug' => 'general',
                'name' => 'General',
                'description' => 'General topics and discussions.'
            ],
            [
                'slug' => 'reviews',
                'name' => 'Reviews',
                'description' => 'Product and service reviews.'
            ],
            [
                'slug' => 'technology',
                'name' => 'Technology',
                'description' => 'Discussions about technology and gadgets.'
            ],
            [
                'slug' => 'lifestyle',
                'name' => 'Lifestyle',
                'description' => 'Topics related to lifestyle and wellness.'
            ],
            [
                'slug' => 'announcements',
                'name' => 'Announcements',
                'description' => 'Site-wide announcements and news.'
            ],
            [
                'slug' => 'support',
                'name' => 'Support',
                'description' => 'Help and troubleshooting for users.'
            ],
            [
                'slug' => 'jobs',
                'name' => 'Jobs',
                'description' => 'Job postings and career opportunities.'
            ],
            [
                'slug' => 'events',
                'name' => 'Events',
                'description' => 'Meetups, webinars, and community events.'
            ],
            [
                'slug' => 'photography',
                'name' => 'Photography',
                'description' => 'Share photos and photography tips.'
            ],
            [
                'slug' => 'gaming',
                'name' => 'Gaming',
                'description' => 'Gaming discussions and news.'
            ],
        ];

        // 預設主題依 `slug` 做 upsert，以避免重複。
        // 若相同 slug 已存在，則更新其 `name` 與 `description` 欄位。
        Topic::upsert($data, ['slug'], ['name', 'description']);
    }
}
