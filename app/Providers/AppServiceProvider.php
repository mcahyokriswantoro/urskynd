<?php

namespace App\Providers;

use App\Contracts\SkinAnalysisProviderInterface;
use App\Providers\DemoSkinAnalysisProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the Skin Analysis provider interface
        // Replace DemoSkinAnalysisProvider with a real AI provider when ready
        $this->app->bind(
            SkinAnalysisProviderInterface::class,
            DemoSkinAnalysisProvider::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
