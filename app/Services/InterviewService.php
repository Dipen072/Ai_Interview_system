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
        
        return DB::transaction(function() use ($userId, $category, $data) {
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

            // Retrieve AI service and generate questions
            $aiService = AiServiceFactory::make();
            $questionsData = $aiService->generateQuestions($category->name, $data['difficulty'], $data['total_questions']);

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
