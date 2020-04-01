<?php

namespace Okipa\MediaLibraryExt\Tests\Models;

use Okipa\MediaLibraryExt\ExtendsMediaAbilities;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class InteractsWithMediaModel implements HasMedia
{
    use InteractsWithMedia;
    use ExtendsMediaAbilities;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar');
    }
}
