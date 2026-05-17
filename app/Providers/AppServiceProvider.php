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

        // Real-Time Dynamic Synchronization: automatically clear dashboard caches when records are added or updated
        $clearAdminCache = function () {
            \Illuminate\Support\Facades\Cache::forget('admin_stats');
            \Illuminate\Support\Facades\Cache::forget('admin_trends_3');
            \Illuminate\Support\Facades\Cache::forget('admin_trends_6');
            \Illuminate\Support\Facades\Cache::forget('admin_trends_12');
            \Illuminate\Support\Facades\Cache::forget('admin_weekly_activity');
            \Illuminate\Support\Facades\Cache::forget('admin_subject_stats');
        };

        $syncedModels = [
            \App\Models\User::class,
            \App\Models\Course::class,
            \App\Models\Lesson::class,
            \App\Models\Quiz::class,
            \App\Models\QuizAttempt::class,
            \App\Models\Payment::class,
            \App\Models\Complaint::class,
            \App\Models\Scholarship::class,
            \App\Models\GovernmentScheme::class,
        ];

        foreach ($syncedModels as $model) {
            $model::saved($clearAdminCache);
            $model::deleted($clearAdminCache);
        }
    }
}
