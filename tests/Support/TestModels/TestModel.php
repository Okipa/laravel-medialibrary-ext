<?php

namespace Spatie\MediaLibrary\Tests\Support\TestModels;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class TestModel extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $table = 'test_models';
    protected $guarded = [];
    public $timestamps = false;

    /**
     * Register the conversions that should be performed.
     *
     * @return array
     */
    public function registerMediaConversions(Media $media = null)
    {
    }

    /**
     * Register the media collections and conversions.
     *
     * @return void
     */
    public function registerMediaCollections()
    {
        $this
            ->addMediaCollection('avatar')
            ->useFallbackUrl('/default.jpg')
            ->useFallbackPath('/default.jpg');
    }
}
