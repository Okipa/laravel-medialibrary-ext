<?php

namespace Okipa\MediaLibraryExt\Tests;

use Orchestra\Testbench\TestCase;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

class MediaLibraryExtTestCase extends TestCase
{
    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    protected function getPackageProviders($app): array
    {
        return [MediaLibraryServiceProvider::class];
    }
}
