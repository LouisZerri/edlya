<?php

namespace App\Providers;

use App\Services\AnthropicService;
use Illuminate\Support\ServiceProvider;

class AnthropicServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AnthropicService::class, function () {
            return new AnthropicService();
        });
    }

    public function boot(): void
    {
        //
    }
}