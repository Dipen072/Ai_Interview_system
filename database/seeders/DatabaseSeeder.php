<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Run Spatie Role & Permission Seeder
        $this->call(RolePermissionSeeder::class);

        // 2. Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@interview.com'],
            [
                'name' => 'Admin System',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('Admin');

        // 3. Create Regular User
        $user = User::firstOrCreate(
            ['email' => 'user@interview.com'],
            [
                'name' => 'John Doe',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $user->assignRole('User');

        // 4. Seed Categories
        $categories = [
            ['name' => 'PHP', 'description' => 'Core PHP features, syntax, OOP, OOP principles, and basic design patterns.', 'icon_class' => 'bi-code-slash'],
            ['name' => 'Laravel', 'description' => 'MVC architecture, Eloquent ORM, Routing, Controllers, Middleware, and Services in Laravel.', 'icon_class' => 'bi-bootstrap'],
            ['name' => 'MySQL', 'description' => 'Database designing, relations, normalization, indexing, joins, and complex queries in MySQL.', 'icon_class' => 'bi-database'],
            ['name' => 'JavaScript', 'description' => 'ES6+, DOM Manipulation, Async/Await, Closures, Promises, and modern JS standards.', 'icon_class' => 'bi-filetype-js'],
            ['name' => 'React', 'description' => 'React component lifecycle, Hooks, State Management, Virtual DOM, and Redux.', 'icon_class' => 'bi-box-seam'],
            ['name' => 'HTML', 'description' => 'Semantic structure, metadata, standard layouts, accessibility, and modern HTML5 elements.', 'icon_class' => 'bi-filetype-html'],
            ['name' => 'CSS', 'description' => 'Flexbox, CSS Grid, positioning, layout strategies, media queries, and responsive web design.', 'icon_class' => 'bi-filetype-css'],
            ['name' => 'API Development', 'description' => 'REST APIs, JSON, headers, authentication methods (JWT, Sanctum), status codes, and security.', 'icon_class' => 'bi-router'],
            ['name' => 'HR Interview', 'description' => 'Behavioral questions, career objectives, conflict resolution, values alignment, and situation handling.', 'icon_class' => 'bi-person-lines-fill'],
            ['name' => 'Aptitude', 'description' => 'Mathematical puzzles, logical reasoning, problem-solving techniques, and algorithmic design concepts.', 'icon_class' => 'bi-lightning-charge'],
            ['name' => 'Communication Skills', 'description' => 'Professional vocabulary, presentation strategies, active listening, and soft skills under pressure.', 'icon_class' => 'bi-chat-left-text'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'icon_class' => $cat['icon_class']
                ]
            );
        }

        // 5. Seed System Settings
        $settings = [
            ['key' => 'ai_provider', 'value' => 'gemini', 'description' => 'Active AI provider for question generation and evaluation (gemini or openai)'],
            ['key' => 'gemini_model', 'value' => 'gemini-1.5-flash', 'description' => 'Active Gemini model ID'],
            ['key' => 'openai_model', 'value' => 'gpt-4o-mini', 'description' => 'Active OpenAI model ID'],
            ['key' => 'system_prompt', 'value' => "You are an expert AI interviewer, evaluator, and tech lead. When generating questions or evaluating answers, prioritize accuracy, technical depth, communication skills, and completeness. You must output all evaluations in structured JSON format.", 'description' => 'Primary system instruction prompt for AI evaluations'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'description' => $setting['description']
                ]
            );
        }
    }
}
