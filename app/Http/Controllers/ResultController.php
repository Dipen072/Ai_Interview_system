<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\ResultRepositoryInterface;
use App\Models\Interview;
use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    protected $resultRepo;

    public function __construct(ResultRepositoryInterface $resultRepo)
    {
        $this->resultRepo = $resultRepo;
    }

    /**
     * Display the comprehensive results card for a completed interview.
     */
    public function show(int $interviewId)
    {
        $result = $this->resultRepo->findByInterviewId($interviewId);

        if (!$result) {
            return redirect()->route('dashboard')
                ->with('error', 'Results not found or evaluation is still pending.');
        }

        // Ensure authorization
        if ($result->user_id !== auth()->id() && !auth()->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized access to this result.');
        }

        $interview = $result->interview;
        $questions = $interview->questions;
        $answers = $interview->answers->keyBy('question_id');
        $feedback = $result->feedback->keyBy('criteria');

        return view('interviews.results', compact('result', 'interview', 'questions', 'answers', 'feedback'));
    }

    /**
     * Export the evaluation report card to a standard Excel CSV format.
     */
    public function exportCsv(int $interviewId)
    {
        $result = $this->resultRepo->findByInterviewId($interviewId);

        if (!$result) {
            abort(404, 'Result not found.');
        }

        if ($result->user_id !== auth()->id() && !auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        $interview = $result->interview;
        $questions = $interview->questions;
        $answers = $interview->answers->keyBy('question_id');
        $feedbacks = $result->feedback->keyBy('criteria');

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=interview_result_{$interviewId}.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($questions, $answers, $result, $feedbacks) {
            $file = fopen('php://output', 'w');
            
            // Header information
            fputcsv($file, ['AI INTERVIEW REPORT CARD']);
            fputcsv($file, ['Candidate Name', $result->user->name]);
            fputcsv($file, ['Category', $result->interview->category->name]);
            fputcsv($file, ['Difficulty', ucfirst($result->interview->difficulty)]);
            fputcsv($file, ['Overall Score', $result->overall_score . ' / 10']);
            fputcsv($file, ['Percentage', $result->percentage . '%']);
            fputcsv($file, ['Time Elapsed (seconds)', $result->duration_seconds]);
            fputcsv($file, ['Date Completed', $result->created_at->format('Y-m-d H:i:s')]);
            fputcsv($file, []);

            // Criteria metrics
            fputcsv($file, ['EVALUATION CRITERIA BREAKDOWN']);
            fputcsv($file, ['Criteria', 'Score (0-10)', 'Comments']);
            foreach ($feedbacks as $criteria => $fb) {
                fputcsv($file, [
                    ucwords(str_replace('_', ' ', $criteria)),
                    $fb->score,
                    $fb->feedback_text
                ]);
            }
            fputcsv($file, []);

            // Question responses
            fputcsv($file, ['RESPONSES LOG']);
            fputcsv($file, ['Question No', 'Question Text', 'Candidate Answer']);
            foreach ($questions as $index => $q) {
                $ansText = $answers->has($q->id) ? $answers->get($q->id)->answer_text : 'N/A';
                fputcsv($file, [
                    $index + 1,
                    $q->question_text,
                    $ansText
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
