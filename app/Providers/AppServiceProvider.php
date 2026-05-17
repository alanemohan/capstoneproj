<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
use App\Observers\ContentTranslationObserver;
use App\Services\TranslationService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TranslationService::class, fn() => new TranslationService());
    }

    public function boot(): void
    {
        RateLimiter::for('login', fn(Request $r) => Limit::perMinute(5)->by($r->ip()));
        RateLimiter::for('otp', fn(Request $r) => Limit::perMinute(3)->by($r->ip()));
        RateLimiter::for('chatbot', fn(Request $r) => Limit::perMinute(10)->by($r->user()?->id ?: $r->ip()));
        RateLimiter::for('checkout', fn(Request $r) => Limit::perMinute(5)->by($r->user()?->id ?: $r->ip()));

        Announcement::observe(ContentTranslationObserver::class);
        Course::observe(ContentTranslationObserver::class);
        Lesson::observe(ContentTranslationObserver::class);
        Quiz::observe(ContentTranslationObserver::class);
        Question::observe(ContentTranslationObserver::class);
        \App\Models\LiveClass::observe(ContentTranslationObserver::class);
    }
}
