<?php

namespace App\Services;

use Tinify\Source;
use Tinify\Tinify;

class TinifyService
{
    public function __construct()
    {
        Tinify::setKey(env('TINIFY_API_KEY'));
    }

    /**
     * Si el segundo parametro es null, a imagen se guardará en el mismo lugar de donde se obtuvo
     */
    public function optimizeImage($sourcePath, $destinationPath = null)
    {
        $source = Source::fromFile($sourcePath);
        $source->toFile($destinationPath ?? $sourcePath);
    }

    public function totalCompressions()
    {
        Tinify::getCompressionCount();
    }
}
