<?php

namespace App\Services;

use App\Models\Setting;
use App\Services\Contracts\AiServiceInterface;

class AiServiceFactory
{
    /**
     * Resolve the active AI Service based on database settings.
     *
     * @return AiServiceInterface
     */
    public static function make(): AiServiceInterface
    {
        $provider = Setting::getVal('ai_provider', 'gemini');

        if (strtolower($provider) === 'openai') {
            return app(OpenAiService::class);
        }

        return app(GeminiService::class);
    }
}
