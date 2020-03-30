<?php

namespace Okipa\MediaLibraryExt\Tests;

use Orchestra\Testbench\TestCase;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

class MediaLibraryExtTestCase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [MediaLibraryServiceProvider::class];
    }
}
