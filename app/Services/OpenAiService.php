<?php

namespace App\Services;

use App\Services\Contracts\AiServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiService implements AiServiceInterface
{
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        $this->apiKey = \App\Models\Setting::getVal('openai_api_key') ?? config('services.openai.key') ?? env('OPENAI_API_KEY');
        $this->model = \App\Models\Setting::getVal('openai_model') ?? config('services.openai.model') ?? env('OPENAI_MODEL', 'gpt-4o-mini');
    }

    public function generateQuestions(string $category, string $difficulty, int $count): array
    {
        if (empty($this->apiKey)) {
            return $this->getMockQuestions($category, $difficulty, $count);
        }

        $prompt = "Generate exactly {$count} interview questions for a candidate applying for a position requiring expertise in '{$category}' at a '{$difficulty}' level of difficulty.
Return the output strictly in a JSON array of objects, where each object has a single key 'question_text'.
For example:
[
  {\"question_text\": \"What is the difference between interface and abstract class in PHP?\"}
]";

        try {
            $response = Http::withToken($this->apiKey)
                ->post("https://api.openai.com/v1/chat/completions", [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'response_format' => ['type' => 'json_object']
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['choices'][0]['message']['content'] ?? '{"questions": []}';
                $decoded = json_decode($text, true);
                
                // OpenAI returns a JSON object, so if the AI wraps the array inside an object, extract it
                if (isset($decoded['questions']) && is_array($decoded['questions'])) {
                    return $decoded['questions'];
                } elseif (is_array($decoded) && array_values($decoded) === $decoded) {
                    return $decoded;
                } elseif (is_array($decoded)) {
                    // Try to find any array inside the object
                    foreach ($decoded as $key => $val) {
                        if (is_array($val)) {
                            return $val;
                        }
                    }
                }
            }

            Log::error("OpenAI API Error: " . $response->body());
        } catch (\Exception $e) {
            Log::error("OpenAI API Exception: " . $e->getMessage());
        }

        return $this->getMockQuestions($category, $difficulty, $count);
    }

    public function evaluateAnswer(string $question, string $answer): array
    {
        if (empty($this->apiKey) || empty($answer)) {
            return $this->getMockEvaluation($question, $answer);
        }

        $prompt = "You are a technical interviewer evaluating a candidate's response to an interview question.
Question: '{$question}'
Candidate's Answer: '{$answer}'

Evaluate the answer on four criteria:
1. Accuracy (how factually correct the answer is)
2. Technical Knowledge (technical depth and use of correct terms)
3. Communication (clarity and professionalism of expression)
4. Completeness (how thoroughly the question is answered)

Provide an overall score out of 10.
Return the evaluation strictly in the following JSON format:
{
  \"overall_score\": 7.5,
  \"percentage\": 75.0,
  \"summary_feedback\": \"Provide a general assessment of the response...\",
  \"ai_suggestions\": \"Provide specific suggestions for overall improvement...\",
  \"criteria_feedback\": {
    \"accuracy\": {
      \"score\": 7,
      \"feedback_text\": \"Feedback on accuracy...\",
      \"suggestions\": \"How to improve accuracy...\"
    },
    \"technical_knowledge\": {
      \"score\": 8,
      \"feedback_text\": \"Feedback on technical knowledge...\",
      \"suggestions\": \"How to improve technical depth...\"
    },
    \"communication\": {
      \"score\": 8,
      \"feedback_text\": \"Feedback on communication...\",
      \"suggestions\": \"How to express better...\"
    },
    \"completeness\": {
      \"score\": 7,
      \"feedback_text\": \"Feedback on completeness...\",
      \"suggestions\": \"What details were missing...\"
    }
  }
}";

        try {
            $response = Http::withToken($this->apiKey)
                ->post("https://api.openai.com/v1/chat/completions", [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'response_format' => ['type' => 'json_object']
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['choices'][0]['message']['content'] ?? '{}';
                $decoded = json_decode($text, true);
                if (is_array($decoded) && isset($decoded['overall_score'])) {
                    return $decoded;
                }
            }

            Log::error("OpenAI API Error in evaluation: " . $response->body());
        } catch (\Exception $e) {
            Log::error("OpenAI API Exception in evaluation: " . $e->getMessage());
        }

        return $this->getMockEvaluation($question, $answer);
    }

    public function provideGuidance(string $question, string $currentAnswer = ''): string
    {
        if (empty($this->apiKey)) {
            return "Mock Guidance: Think about the core principles related to this topic. Start by defining the term and then provide an example.";
        }

        $prompt = "You are a helpful and encouraging technical interviewer. 
The candidate is struggling with or has asked for help on the following question: '{$question}'
Their current answer (if any) is: '{$currentAnswer}'

Provide a brief, encouraging hint or explanation to guide them in the right direction without giving away the full answer completely. Keep it to 2-3 sentences max.";

        try {
            $response = Http::withToken($this->apiKey)
                ->post("https://api.openai.com/v1/chat/completions", [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['choices'][0]['message']['content'] ?? '';
                if (!empty($text)) {
                    return trim($text);
                }
            }

            Log::error("OpenAI API Error in guidance: " . $response->body());
        } catch (\Exception $e) {
            Log::error("OpenAI API Exception in guidance: " . $e->getMessage());
        }

        return "I'm having trouble connecting to my knowledge base right now, but try breaking the question down into smaller parts and explaining what you know so far.";
    }

    protected function getMockQuestions(string $category, string $difficulty, int $count): array
    {
        // Reuse same helper method for consistency
        return (new GeminiService())->generateQuestions($category, $difficulty, $count);
    }

    protected function getMockEvaluation(string $question, string $answer): array
    {
        return (new GeminiService())->evaluateAnswer($question, $answer);
    }
}
