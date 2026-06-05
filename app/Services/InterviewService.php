<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Answer;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\InterviewRepositoryInterface;
use Illuminate\Support\Facades\DB;

class InterviewService
{
    protected $categoryRepo;
    protected $interviewRepo;

    public function __construct(
        CategoryRepositoryInterface $categoryRepo,
        InterviewRepositoryInterface $interviewRepo
    ) {
        $this->categoryRepo = $categoryRepo;
        $this->interviewRepo = $interviewRepo;
    }

    /**
     * Start a new mock interview session, generate AI questions, and persist them.
     */
    public function startInterview(int $userId, array $data)
    {
        $category = $this->categoryRepo->find($data['category_id']);
        
        // Custom Requirement: Specific questions for Laravel
        $customQuestions = [];
        if (strtolower($category->name) === 'laravel') {
            if (strtolower($data['difficulty']) === 'easy') {
                $pool = [
                    'What is Laravel?',
                    'What are the main features of Laravel?',
                    'What is MVC architecture in Laravel?',
                    'What are routes in Laravel?',
                    'What is the difference between GET and POST routes?',
                    'How do you create a controller in Laravel?',
                    'What is a model in Laravel?',
                    'What are migrations?',
                    'Why are migrations useful?',
                    'What is Artisan in Laravel?',
                    'Name some commonly used Artisan commands.',
                    'What is Eloquent ORM?',
                    'What is the difference between Eloquent and Query Builder?',
                    'What are middleware in Laravel?',
                    'How do you create custom middleware?',
                    'What are Blade templates?',
                    'What is the purpose of the .env file?',
                    'How do you connect Laravel to a database?',
                    'What is CSRF protection?',
                    'What is the use of @csrf in forms?',
                    'What is form validation in Laravel?',
                    'How do you display validation errors?',
                    'What is route model binding?',
                    'What are Laravel seeders?',
                    'What are factories in Laravel?',
                    'What is authentication in Laravel?',
                    'What is authorization in Laravel?',
                    'What is the difference between authentication and authorization?',
                    'What are sessions in Laravel?',
                    'What is caching in Laravel?',
                    'What are Laravel events and listeners?',
                    'What are queues in Laravel?',
                    'What is a service provider?',
                    'What is the Laravel service container?',
                    'How do you upload files in Laravel?',
                    'What is pagination?',
                    'How do you create an API in Laravel?',
                    'What are API resources?',
                    'What is the difference between hasOne and hasMany relationships?',
                    'What is the difference between belongsTo and belongsToMany?',
                    'What is eager loading?',
                    'What is lazy loading?',
                    'How do you handle exceptions in Laravel?',
                    'What is the purpose of the storage folder?',
                    'What is the purpose of the public folder?',
                    'What is Composer and why is it used with Laravel?',
                    'How do you install a Laravel package?',
                    'What is soft delete in Laravel?',
                    'What are Laravel policies and gates?',
                    'Explain the Laravel request lifecycle briefly.'
                ];
            } elseif (strtolower($data['difficulty']) === 'hard') {
                $pool = [
                    'Explain the Laravel Service Container in detail. How does dependency injection work internally?',
                    'What is the difference between Service Providers, Facades, and Dependency Injection?',
                    'How would you create a custom Service Provider and bind interfaces to implementations?',
                    'Explain Laravel\'s request lifecycle from the moment a request hits the application until a response is returned.',
                    'How does Laravel\'s IoC (Inversion of Control) container resolve dependencies automatically?',
                    'What are the differences between Singleton, Bind, and Instance methods in the Service Container?',
                    'Explain the Repository Pattern in Laravel and its advantages.',
                    'How would you implement a scalable multi-tenant application in Laravel?',
                    'What is the difference between eager loading, lazy loading, and lazy eager loading?',
                    'How do you identify and solve N+1 query problems?',
                    'Explain polymorphic relationships with a real-world example.',
                    'What are global scopes and local scopes? When should you use them?',
                    'How would you optimize a slow Eloquent query?',
                    'What is the difference between chunk(), chunkById(), cursor(), and lazy()?',
                    'Explain database transactions and when they are necessary.',
                    'How do you handle deadlocks in database transactions?',
                    'What is optimistic locking and how can it be implemented in Laravel?',
                    'Explain Laravel Queue architecture and different queue drivers.',
                    'What happens if a queued job fails? How do you retry failed jobs?',
                    'How would you design a high-volume email sending system using queues?',
                    'What is queue batching and when would you use it?',
                    'Explain Laravel Events and Listeners. How do they help decouple applications?',
                    'What is event broadcasting and how does it work?',
                    'Explain Laravel WebSockets and real-time applications.',
                    'What are Laravel Notifications and how are they different from Events?',
                    'How does Laravel Authentication work internally?',
                    'Explain Laravel Sanctum and Passport. When would you choose one over the other?',
                    'How would you implement Role-Based Access Control (RBAC) in Laravel?',
                    'Explain Policies and Gates with examples.',
                    'How do you secure a Laravel API against common attacks?',
                    'What security vulnerabilities should every Laravel developer know?',
                    'How does CSRF protection work internally?',
                    'Explain XSS, SQL Injection, and Mass Assignment vulnerabilities in Laravel.',
                    'What is the difference between fillable and guarded?',
                    'How would you implement API rate limiting?',
                    'Explain Laravel Cache drivers and caching strategies.',
                    'How do Redis and Laravel work together?',
                    'How would you cache expensive database queries?',
                    'What is cache invalidation and why is it difficult?',
                    'Explain Laravel\'s configuration caching and route caching.',
                    'How do you optimize Laravel for production environments?',
                    'What are the benefits and limitations of Octane?',
                    'Explain Laravel Octane with Swoole and RoadRunner.',
                    'How would you monitor and debug a production Laravel application?',
                    'What is Horizon and why is it useful?',
                    'Explain Laravel Scheduler and its internal working.',
                    'How would you design a microservices architecture using Laravel?',
                    'What challenges arise when scaling a Laravel application to millions of users?',
                    'How would you implement distributed caching in Laravel?'
                ];
            }
        } elseif (strtolower($category->name) === 'hr') {
            if (strtolower($data['difficulty']) === 'easy') {
                $pool = [
                    'Tell me about yourself.',
                    'What are your strengths?',
                    'What are your weaknesses?',
                    'Why should we hire you?',
                    'Why do you want to work in our company?',
                    'What do you know about our company?',
                    'Where do you see yourself in 5 years?',
                    'Are you willing to relocate?',
                    'Are you comfortable with night shifts or rotational shifts?',
                    'What motivates you to do a job?',
                    'Why did you choose this field (IT / BCA / Engineering etc.)?',
                    'What was your final year project?',
                    'What challenges did you face in your project and how did you handle them?',
                    'Are you a team player or prefer individual work?',
                    'How do you handle pressure or deadlines?',
                    'What are your hobbies?',
                    'What are your salary expectations?',
                    'Do you have any gaps in your education? If yes, why?',
                    'What are your career goals?',
                    'Do you have any questions for us?'
                ];
            } elseif (strtolower($data['difficulty']) === 'hard') {
                $pool = [
                    'What is something you are currently struggling with in yourself?',
                    'If your friends describe you negatively, what would they say?',
                    'What is your biggest failure so far, and what did you learn from it?',
                    'What is a mistake you made recently that you regret?',
                    'If you had to change one thing about your personality, what would it be?',
                    'Why is there a gap in your skills compared to job requirements?',
                    'Describe a situation where you worked under extreme pressure.',
                    'What will you do if you are not able to complete a deadline?',
                    'How do you manage multiple tasks with limited time?',
                    'What will you do if your team is not cooperating?'
                ];
            }
        }
        
        if (isset($pool)) {
            shuffle($pool);
            $selectedPool = array_slice($pool, 0, $data['total_questions']);
            
            foreach ($selectedPool as $qText) {
                $customQuestions[] = ['question_text' => $qText];
            }
        }
        
        return DB::transaction(function() use ($userId, $category, $data, $customQuestions) {
            // Create Interview session
            $interview = $this->interviewRepo->create([
                'user_id' => $userId,
                'category_id' => $category->id,
                'difficulty' => $data['difficulty'],
                'total_questions' => $data['total_questions'],
                'duration_minutes' => $data['total_questions'] * 2, // 2 minutes per question
                'status' => 'ongoing',
                'started_at' => now(),
            ]);

            if (!empty($customQuestions)) {
                $questionsData = $customQuestions;
            } else {
                // Retrieve AI service and generate questions
                $aiService = AiServiceFactory::make();
                $questionsData = $aiService->generateQuestions($category->name, $data['difficulty'], $data['total_questions']);
            }

            // Save generated questions
            foreach ($questionsData as $index => $q) {
                Question::create([
                    'interview_id' => $interview->id,
                    'question_text' => $q['question_text'],
                    'order_index' => $index,
                ]);
            }

            return $interview;
        });
    }

    /**
     * Auto-save or update user response to a specific question in real-time.
     */
    public function saveAnswer(int $interviewId, int $userId, int $questionId, ?string $answerText)
    {
        return Answer::updateOrCreate(
            [
                'interview_id' => $interviewId,
                'question_id' => $questionId,
                'user_id' => $userId
            ],
            [
                'answer_text' => $answerText
            ]
        );
    }
}
