<?php


namespace Tadzumi\RequestHunter\Factory;

use Tadzumi\RequestHunter\RequestHunterInterface;

class NullTypeFactory extends TypeRequestHunterFactoryBase
{
    public function type(string $type): RequestHunterInterface
    {

    }

}