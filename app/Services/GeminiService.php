<?php

namespace App\Services;

use App\Services\Contracts\AiServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService implements AiServiceInterface
{
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        $this->apiKey = \App\Models\Setting::getVal('gemini_api_key') ?? config('services.gemini.key') ?? env('GEMINI_API_KEY');
        $this->model = \App\Models\Setting::getVal('gemini_model') ?? config('services.gemini.model') ?? env('GEMINI_MODEL', 'gemini-1.5-flash');
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
            $response = Http::timeout(10)->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
                $decoded = json_decode($text, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }

            Log::error("Gemini API Error: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Gemini API Exception: " . $e->getMessage());
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
            $response = Http::timeout(10)->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json'
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                $decoded = json_decode($text, true);
                if (is_array($decoded) && isset($decoded['overall_score'])) {
                    return $decoded;
                }
            }

            Log::error("Gemini API Error in evaluation: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Gemini API Exception in evaluation: " . $e->getMessage());
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
            $response = Http::timeout(10)->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                if (!empty($text)) {
                    return trim($text);
                }
            }

            Log::error("Gemini API Error in guidance: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Gemini API Exception in guidance: " . $e->getMessage());
        }

        return "I'm having trouble connecting to my knowledge base right now, but try breaking the question down into smaller parts and explaining what you know so far.";
    }

    protected function getMockQuestions(string $category, string $difficulty, int $count): array
    {
        $mockQuestions = [
            'PHP' => [
                'What is dependency injection and how is it used in PHP?',
                'Explain the difference between interface and abstract class.',
                'What are traits in PHP and when should you use them?',
                'How does PHP handle memory management and garbage collection?',
                'What is the difference between == and === in PHP?',
                'Explain PHP namespaces and autoloading mechanisms.'
            ],
            'Laravel' => [
                'Explain the Laravel service container and service provider concepts.',
                'What is the difference between lazy loading and eager loading in Eloquent?',
                'How does middleware work in Laravel, and how do you write a custom one?',
                'Explain the concept of queueing in Laravel and why it is useful.',
                'What are Laravel events and listeners, and how do they decouple code?',
                'Explain route model binding and how to customize the resolution logic.'
            ],
            'MySQL' => [
                'What is database indexing, and how does it improve query performance?',
                'Explain the differences between INNER JOIN, LEFT JOIN, and RIGHT JOIN.',
                'What are transactions in database management systems, and what does ACID stand for?',
                'How do you identify and optimize slow queries in MySQL?',
                'Explain foreign key constraints and referential integrity.'
            ],
            'JavaScript' => [
                'Explain closures in JavaScript and provide a practical use case.',
                'What is the event loop in JavaScript and how does it handle asynchronous code?',
                'Explain the difference between let, const, and var.',
                'What is prototype inheritance in JavaScript, and how does it work?',
                'What is the difference between Promise.all() and Promise.allSettled()?'
            ]
        ];

        $list = $mockQuestions[ucfirst(strtolower($category))] ?? [
            "Explain key concepts and best practices in '{$category}'.",
            "What are the most challenging aspects of working with '{$category}'?",
            "Discuss a complex problem you solved recently using '{$category}'.",
            "What security considerations should you keep in mind when working with '{$category}'?",
            "How do you optimize performance when developing solutions with '{$category}'?"
        ];

        shuffle($list);
        $result = [];
        for ($i = 0; $i < min($count, count($list)); $i++) {
            $result[] = ['question_text' => $list[$i]];
        }

        while (count($result) < $count) {
            $result[] = ['question_text' => "Describe advanced aspects and design considerations for '{$category}' applications."];
        }

        return $result;
    }

    protected function getMockEvaluation(string $question, string $answer): array
    {
        $len = strlen($answer);
        $score = 5.0;
        if ($len > 200) $score = 9.0;
        elseif ($len > 100) $score = 8.0;
        elseif ($len > 50) $score = 7.0;
        elseif ($len > 10) $score = 6.0;

        return [
            'overall_score' => $score,
            'percentage' => $score * 10,
            'summary_feedback' => "This is a simulated evaluation (API Offline or Key Missing). The candidate attempted the question: '{$question}'.",
            'ai_suggestions' => "Configure GEMINI_API_KEY in the .env file to enable live AI evaluations and get detailed breakdowns.",
            'criteria_feedback' => [
                'accuracy' => [
                    'score' => (int)floor($score),
                    'feedback_text' => 'The answer shows a standard understanding of the topic.',
                    'suggestions' => 'Provide more precise definitions.'
                ],
                'technical_knowledge' => [
                    'score' => (int)floor($score),
                    'feedback_text' => 'The technical vocabulary is appropriate.',
                    'suggestions' => 'Incorporate core technical architecture concepts.'
                ],
                'communication' => [
                    'score' => (int)min(10, floor($score + 1)),
                    'feedback_text' => 'Expression is clear and readable.',
                    'suggestions' => 'Keep the response concise and structured.'
                ],
                'completeness' => [
                    'score' => (int)floor($score),
                    'feedback_text' => 'The response addresses the key points.',
                    'suggestions' => 'Add code snippets or real-world examples to be fully comprehensive.'
                ]
            ]
        ];
    }
}
