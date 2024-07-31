<?php

namespace App\Services;

use Tinify\Source;
use Tinify\Tinify;
use Tinify\AccountException;
use Exception;

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
        try {
            $source = Source::fromFile($sourcePath);
            $source->toFile($destinationPath ?? $sourcePath);
        } catch (AccountException $e) {
            // Manejar el caso donde se excede el límite de compresiones
            // throw new Exception("Se ha alcanzado el límite de compresiones mensuales de TinyPNG: " . $e->getMessage());
        } catch (Exception $e) {
            // Manejar otras excepciones
            // throw new Exception("Ocurrió un error durante la compresión de la imagen: " . $e->getMessage());
        }
    }

    public function totalCompressions()
    {
        Tinify::getCompressionCount();
    }
}
