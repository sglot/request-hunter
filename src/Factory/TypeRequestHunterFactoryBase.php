<?php


namespace Tadzumi\RequestHunter\Factory;

use Tadzumi\RequestHunter\RequestHunterInterface;


abstract class TypeRequestHunterFactoryBase
{
    const SERVER_ARRAY = 'server';
    const REQUEST_OBJECT = 'request';

    abstract public function type(string $type): RequestHunterInterface;

}