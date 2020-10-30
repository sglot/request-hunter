<?php


namespace Tadzumi\RequestHunter\Factory;

use Tadzumi\RequestHunter\NullRequestHunter;
use Tadzumi\RequestHunter\RequestHunterInterface;

class NullTypeFactory extends TypeRequestHunterFactoryBase
{
    public function type(string $type): RequestHunterInterface
    {
        return new NullRequestHunter();
    }

}