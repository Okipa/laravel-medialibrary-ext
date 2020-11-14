<?php

namespace Okipa\MediaLibraryExt;

use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

class MediaLibraryExtServiceProvider extends ServiceProvider
{
    /** Bootstrap the application services. */
    public function boot(): void
    {
        $this->app->register(MediaLibraryServiceProvider::class);
    }
}
