<?php

namespace Okipa\MediaLibraryExt;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

class MediaLibraryExtServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->register(MediaLibraryServiceProvider::class);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}
