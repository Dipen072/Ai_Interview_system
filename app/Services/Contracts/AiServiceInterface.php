<?php

namespace App\Services\Contracts;

interface AiServiceInterface
{
    /**
     * Generate interview questions based on category, difficulty, and count.
     *
     * @param string $category
     * @param string $difficulty
     * @param int $count
     * @return array Array of questions: [['question_text' => '...'], ...]
     */
    public function generateQuestions(string $category, string $difficulty, int $count): array;

    /**
     * Evaluate a user's answer to a specific question.
     *
     * @param string $question
     * @param string $answer
     * @return array Structured evaluation data:
     *               [
     *                  'overall_score' => float,
     *                  'percentage' => float,
     *                  'summary_feedback' => string,
     *                  'ai_suggestions' => string,
     *                  'criteria_feedback' => [
     *                      'accuracy' => ['score' => int, 'feedback_text' => string, 'suggestions' => string],
     *                      'technical_knowledge' => ['score' => int, 'feedback_text' => string, 'suggestions' => string],
     *                      'communication' => ['score' => int, 'feedback_text' => string, 'suggestions' => string],
     *                      'completeness' => ['score' => int, 'feedback_text' => string, 'suggestions' => string]
     *                  ]
     *               ]
     */
    public function evaluateAnswer(string $question, string $answer): array;
}
