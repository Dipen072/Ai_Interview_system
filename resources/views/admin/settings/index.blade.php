@extends('layouts.app')

@slot('title')
    AI Configuration
@endslot

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <div class="mb-4">
        <h2 class="fw-bold m-0 text-white"><i class="bi bi-gear text-indigo me-2"></i> System AI Configuration</h2>
        <p class="text-muted-custom m-0">Toggle your active AI provider, configure API access credentials, and adjust core evaluation prompts.</p>
    </div>

    <div class="card p-4">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf

            <!-- 1. Active Engine Switcher -->
            <div class="mb-4">
                <label class="form-label text-white fw-bold fs-5">Active AI Evaluation Provider</label>
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="radio" class="btn-check" name="ai_provider" id="provider_gemini" value="gemini" 
                            {{ old('ai_provider', $settings->get('ai_provider')->value ?? 'gemini') === 'gemini' ? 'checked' : '' }}>
                        <label class="btn btn-outline-info w-100 py-3" for="provider_gemini">
                            <i class="bi bi-google fs-3 d-block mb-1"></i>
                            <span class="fw-bold text-uppercase" style="font-size: 0.85rem;">Google Gemini</span>
                            <span class="d-block text-muted-custom fs-7" style="font-size: 0.75rem;">Fast performance, structured JSON outputs</span>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <input type="radio" class="btn-check" name="ai_provider" id="provider_openai" value="openai" 
                            {{ old('ai_provider', $settings->get('ai_provider')->value ?? 'gemini') === 'openai' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary w-100 py-3" for="provider_openai">
                            <i class="bi bi-cpu fs-3 d-block mb-1"></i>
                            <span class="fw-bold text-uppercase" style="font-size: 0.85rem;">OpenAI GPT</span>
                            <span class="d-block text-muted-custom fs-7" style="font-size: 0.75rem;">Alternative model, standard GPT architectures</span>
                        </label>
                    </div>
                </div>
                @error('ai_provider')
                    <div class="text-danger mt-2" style="font-size: 0.85rem;">{{ $message }}</div>
                @enderror
            </div>

            <hr class="border-secondary my-4">

            <!-- 2. Google Gemini Parameters -->
            <div class="mb-4">
                <h5 class="fw-bold text-info mb-3"><i class="bi bi-google me-2"></i>Google Gemini Settings</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="gemini_model" class="form-label text-white fw-bold">Gemini Model ID</label>
                        <input type="text" name="gemini_model" id="gemini_model" class="form-control @error('gemini_model') is-invalid @enderror" 
                            value="{{ old('gemini_model', $settings->get('gemini_model')->value ?? 'gemini-1.5-flash') }}" 
                            style="background-color: #1f2937; border-color: #374151; color: #fff;">
                        @error('gemini_model')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="gemini_api_key" class="form-label text-white fw-bold">Gemini API Key</label>
                        <input type="password" name="gemini_api_key" id="gemini_api_key" class="form-control @error('gemini_api_key') is-invalid @enderror" 
                            placeholder="••••••••••••••••••••••••" 
                            style="background-color: #1f2937; border-color: #374151; color: #fff;">
                        <span class="text-muted-custom" style="font-size: 0.75rem;">Leave empty to keep current configuration key.</span>
                        @error('gemini_api_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- 3. OpenAI Parameters -->
            <div class="mb-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-cpu me-2"></i>OpenAI Settings</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="openai_model" class="form-label text-white fw-bold">OpenAI Model ID</label>
                        <input type="text" name="openai_model" id="openai_model" class="form-control @error('openai_model') is-invalid @enderror" 
                            value="{{ old('openai_model', $settings->get('openai_model')->value ?? 'gpt-4o-mini') }}" 
                            style="background-color: #1f2937; border-color: #374151; color: #fff;">
                        @error('openai_model')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="openai_api_key" class="form-label text-white fw-bold">OpenAI API Key</label>
                        <input type="password" name="openai_api_key" id="openai_api_key" class="form-control @error('openai_api_key') is-invalid @enderror" 
                            placeholder="••••••••••••••••••••••••" 
                            style="background-color: #1f2937; border-color: #374151; color: #fff;">
                        <span class="text-muted-custom" style="font-size: 0.75rem;">Leave empty to keep current configuration key.</span>
                        @error('openai_api_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <!-- 4. Global System Instruction -->
            <div class="mb-4">
                <label for="system_prompt" class="form-label text-white fw-bold fs-5">System Evaluation Prompt Template</label>
                <textarea name="system_prompt" id="system_prompt" rows="8" class="form-control font-monospace @error('system_prompt') is-invalid @enderror" 
                    style="background-color: #0b0f19; border-color: #374151; color: #fff; line-height: 1.5; resize: none; font-size: 0.85rem;"
                >{{ old('system_prompt', $settings->get('system_prompt')->value ?? "You are a professional technical interviewer assessing responses based on correctness, terminology depth, and clarity.") }}</textarea>
                @error('system_prompt')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">
                <i class="bi bi-save me-1"></i> Save Configuration Keys
            </button>
        </form>
    </div>
</div>
@endsection
