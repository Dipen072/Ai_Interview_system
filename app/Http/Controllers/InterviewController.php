<?php

namespace App\Http\Controllers;

use App\Http\Requests\StartInterviewRequest;
use App\Http\Requests\SaveAnswerRequest;
use App\Services\InterviewService;
use App\Services\EvaluationService;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\InterviewRepositoryInterface;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    protected $categoryRepo;
    protected $interviewRepo;
    protected $interviewService;
    protected $evaluationService;

    public function __construct(
        CategoryRepositoryInterface $categoryRepo,
        InterviewRepositoryInterface $interviewRepo,
        InterviewService $interviewService,
        EvaluationService $evaluationService
    ) {
        $this->categoryRepo = $categoryRepo;
        $this->interviewRepo = $interviewRepo;
        $this->interviewService = $interviewService;
        $this->evaluationService = $evaluationService;
    }

    /**
     * Show the interview creation/setup form.
     */
    public function setup()
    {
        $categories = $this->categoryRepo->all();
        return view('interviews.setup', compact('categories'));
    }

    /**
     * Start a new mock interview session and redirect to the arena.
     */
    public function start(StartInterviewRequest $request)
    {
        $userId = auth()->id();
        $interview = $this->interviewService->startInterview($userId, $request->validated());

        return redirect()->route('interviews.arena', $interview->id)
            ->with('success', 'Your AI Mock Interview has started! Good luck.');
    }

    /**
     * Display the distraction-free interview arena.
     */
    public function arena(int $id)
    {
        $interview = $this->interviewRepo->find($id);

        // Ensure user owns this interview
        if ($interview->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this interview.');
        }

        // Redirect if already completed
        if ($interview->status === 'completed') {
            return redirect()->route('results.show', $interview->id)
                ->with('info', 'This interview has already been completed.');
        }

        // Calculate remaining duration in seconds
        $totalDurationSeconds = $interview->duration_minutes * 60;
        $elapsedSeconds = now()->diffInSeconds($interview->started_at);
        $remainingSeconds = max(0, $totalDurationSeconds - $elapsedSeconds);

        // Auto-submit if time has run out
        if ($remainingSeconds <= 0) {
            $this->evaluationService->evaluateInterview($interview->id);
            return redirect()->route('results.show', $interview->id)
                ->with('warning', 'Time limit exceeded. Your answers were auto-submitted.');
        }

        $questions = $interview->questions;
        $savedAnswers = $interview->answers->keyBy('question_id');

        return view('interviews.arena', compact('interview', 'questions', 'savedAnswers', 'remainingSeconds'));
    }

    /**
     * Save an answer to a question in real-time via AJAX.
     */
    public function saveAnswer(SaveAnswerRequest $request, int $id)
    {
        $interview = $this->interviewRepo->find($id);

        if ($interview->user_id !== auth()->id() || $interview->status !== 'ongoing') {
            return response()->json(['error' => 'Action unauthorized or interview is locked.'], 403);
        }

        $this->interviewService->saveAnswer(
            $interview->id,
            auth()->id(),
            $request->question_id,
            $request->answer_text
        );

        return response()->json(['success' => true, 'message' => 'Answer saved successfully.']);
    }

    /**
     * Submit all answers and trigger the AI evaluation service.
     */
    public function submit(int $id)
    {
        $interview = $this->interviewRepo->find($id);

        if ($interview->user_id !== auth()->id()) {
            abort(403, 'Unauthorized.');
        }

        if ($interview->status === 'completed') {
            return redirect()->route('results.show', $interview->id);
        }

        // Show a processing screen while AI evaluates
        return view('interviews.evaluating', compact('interview'));
    }

    /**
     * AJAX endpoint to trigger AI evaluation in the background.
     */
    public function triggerEvaluation(int $id)
    {
        $interview = $this->interviewRepo->find($id);

        if ($interview->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        try {
            $result = $this->evaluationService->evaluateInterview($interview->id);
            return response()->json([
                'success' => true, 
                'redirect_url' => route('results.show', $interview->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Evaluation failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
