<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share brand settings with all views
        View::composer('*', function ($view) {
            try {
                $settings = \App\Models\Setting::allAsArray();
                $view->with('brand', [
                    'name' => $settings['app_name'] ?? config('app.name', 'Tweek'),
                    'primary_color' => $settings['primary_color'] ?? '#FFF9E8',
                    'secondary_color' => $settings['secondary_color'] ?? '#EBF4FF',
                    'accent_color' => $settings['accent_color'] ?? '#F3EEFF',
                    'logo_path' => $settings['logo_path'] ?? null,
                    'social_instagram' => $settings['social_instagram'] ?? '',
                    'social_twitter' => $settings['social_twitter'] ?? '',
                    'social_tiktok' => $settings['social_tiktok'] ?? '',
                    'social_github' => $settings['social_github'] ?? '',
                    'hero_image_path' => $settings['hero_image_path'] ?? null,
                ]);
            } catch (\Exception $e) {
                // Settings table may not exist yet during migration
                $view->with('brand', [
                    'name' => config('app.name', 'Tweek'),
                    'primary_color' => '#FFF9E8',
                    'secondary_color' => '#EBF4FF',
                    'accent_color' => '#F3EEFF',
                    'logo_path' => null,
                    'social_instagram' => '',
                    'social_twitter' => '',
                    'social_tiktok' => '',
                    'social_github' => '',
                    'hero_image_path' => null,
                ]);
            }
        });
    }
}
