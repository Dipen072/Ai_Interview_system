<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Contracts\InterviewRepositoryInterface;
use App\Repositories\Eloquent\InterviewRepository;
use App\Repositories\Contracts\ResultRepositoryInterface;
use App\Repositories\Eloquent\ResultRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(InterviewRepositoryInterface::class, InterviewRepository::class);
        $this->app->bind(ResultRepositoryInterface::class, ResultRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS on Render (and any other proxy-terminated TLS host).
        // Render forwards the original scheme via X-Forwarded-Proto header.
        if (app()->environment('production')) {
            URL::forceScheme('https');

            // Trust Render's load balancer so Request::secure() works correctly
            $this->app['request']->server->set('HTTPS', 'on');
        }
    }
}
