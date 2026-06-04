<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display current AI provider settings panel.
     */
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Save updated settings keys to database.
     */
    public function update(Request $request)
    {
        $request->validate([
            'ai_provider' => ['required', 'in:gemini,openai'],
            'gemini_model' => ['required', 'string'],
            'openai_model' => ['required', 'string'],
            'gemini_api_key' => ['nullable', 'string'],
            'openai_api_key' => ['nullable', 'string'],
            'system_prompt' => ['required', 'string'],
        ]);

        Setting::setVal('ai_provider', $request->ai_provider, 'Active AI Provider');
        Setting::setVal('gemini_model', $request->gemini_model, 'Active Gemini Model ID');
        Setting::setVal('openai_model', $request->openai_model, 'Active OpenAI Model ID');
        Setting::setVal('system_prompt', $request->system_prompt, 'Evaluation System Prompt');

        if ($request->filled('gemini_api_key')) {
            Setting::setVal('gemini_api_key', $request->gemini_api_key, 'Gemini API Key');
        }
        if ($request->filled('openai_api_key')) {
            Setting::setVal('openai_api_key', $request->openai_api_key, 'OpenAI API Key');
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'AI Configuration updated successfully.');
    }
}
