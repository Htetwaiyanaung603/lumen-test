<?php

namespace App\Handlers;

class ResumableJSUploadHandler extends \Pion\Laravel\ChunkUpload\Handler\ResumableJSUploadHandler
{
    public static function canUseSession()
    {
        return false;
    }
}