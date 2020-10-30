<?php

namespace Tadzumi\RequestHunter\Factory;

use Tadzumi\RequestHunter\File\FileSpecialObjectRequestHunter;
use Tadzumi\RequestHunter\File\FileServerRequestHunter;
use Tadzumi\RequestHunter\NullRequestHunter;
use Tadzumi\RequestHunter\RequestHunterInterface;

class FileRequestHunterFactory extends TypeRequestHunterFactoryBase
{
    public function type(string $type): RequestHunterInterface
    {
        if ($type === self::SERVER_ARRAY) {
            return new FileServerRequestHunter();
        }

        if ($type === self::REQUEST_OBJECT) {
            return new FileSpecialObjectRequestHunter();
        }

        return new NullRequestHunter();
    }
}