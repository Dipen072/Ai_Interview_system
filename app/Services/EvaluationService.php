<?php

namespace App\Services;

use App\Models\Interview;
use App\Models\Result;
use App\Models\Feedback;
use App\Repositories\Contracts\InterviewRepositoryInterface;
use App\Repositories\Contracts\ResultRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EvaluationService
{
    protected $interviewRepo;
    protected $resultRepo;

    public function __construct(
        InterviewRepositoryInterface $interviewRepo,
        ResultRepositoryInterface $resultRepo
    ) {
        $this->interviewRepo = $interviewRepo;
        $this->resultRepo = $resultRepo;
    }

    /**
     * Complete and evaluate an interview session.
     */
    public function evaluateInterview(int $interviewId)
    {
        $interview = $this->interviewRepo->find($interviewId);

        // Skip if already evaluated
        if ($interview->status === 'completed' && $interview->result) {
            return $interview->result;
        }

        $questions = $interview->questions;
        $answers = $interview->answers->keyBy('question_id');
        $aiService = AiServiceFactory::make();

        $totalScore = 0;
        $criteriaTotals = [
            'accuracy' => ['score' => 0, 'feedback' => [], 'suggestions' => []],
            'technical_knowledge' => ['score' => 0, 'feedback' => [], 'suggestions' => []],
            'communication' => ['score' => 0, 'feedback' => [], 'suggestions' => []],
            'completeness' => ['score' => 0, 'feedback' => [], 'suggestions' => []],
        ];

        $countEvaluated = 0;

        // Perform external API calls OUTSIDE of the database transaction
        foreach ($questions as $q) {
            $ans = $answers->get($q->id);
            $answerText = $ans ? ($ans->answer_text ?? '') : '';

            // Evaluate current answer
            $eval = $aiService->evaluateAnswer($q->question_text, $answerText);

            $totalScore += $eval['overall_score'];
            $countEvaluated++;

            // Accumulate criteria scores and feedback
            foreach ($criteriaTotals as $crit => &$data) {
                $critData = $eval['criteria_feedback'][$crit] ?? ['score' => 0, 'feedback_text' => '', 'suggestions' => ''];
                $data['score'] += $critData['score'];
                if (!empty($critData['feedback_text'])) {
                    $data['feedback'][] = "Q" . ($q->order_index + 1) . ": " . $critData['feedback_text'];
                }
                if (!empty($critData['suggestions'])) {
                    $data['suggestions'][] = "Q" . ($q->order_index + 1) . ": " . $critData['suggestions'];
                }
            }
        }

        // Calculate overall averages
        $avgScore = $countEvaluated > 0 ? ($totalScore / $countEvaluated) : 0;
        $percentage = $avgScore * 10;
        $durationSeconds = now()->diffInSeconds($interview->started_at);

        // Construct summary
        $overallFeedback = "Your mock interview has been evaluated. You scored " . number_format($avgScore, 1) . "/10 overall.";
        $overallSuggestions = "Focus on categories with lower scores. See criteria breakdowns for details.";

        // Save data atomically inside transaction
        return DB::transaction(function() use ($interview, $avgScore, $percentage, $durationSeconds, $overallFeedback, $overallSuggestions, $criteriaTotals, $countEvaluated) {
            // Save results
            $result = $this->resultRepo->create([
                'interview_id' => $interview->id,
                'user_id' => $interview->user_id,
                'overall_score' => $avgScore,
                'percentage' => $percentage,
                'duration_seconds' => $durationSeconds,
                'summary_feedback' => $overallFeedback,
                'ai_suggestions' => $overallSuggestions,
            ]);

            // Save feedback breakdowns
            foreach ($criteriaTotals as $crit => $data) {
                $critAvg = $countEvaluated > 0 ? ($data['score'] / $countEvaluated) : 0;
                Feedback::create([
                    'result_id' => $result->id,
                    'criteria' => $crit,
                    'score' => (int)round($critAvg),
                    'feedback_text' => implode("\n", $data['feedback']),
                    'suggestions' => implode("\n", $data['suggestions']),
                ]);
            }

            // Update interview status
            $interview->update([
                'status' => 'completed',
                'overall_score' => $avgScore,
                'completed_at' => now(),
            ]);

            return $result;
        });
    }
}
