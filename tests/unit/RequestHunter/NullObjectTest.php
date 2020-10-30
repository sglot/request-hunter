<?php

namespace unit\RequestHunter;

use PHPUnit\Framework\TestCase;
use Tadzumi\RequestHunter\Factory\DriverRequestHunterFactory;
use Tadzumi\RequestHunter\Factory\TypeRequestHunterFactoryBase;
use Tadzumi\RequestHunter\NullRequestHunter;


class NullObjectTest extends TestCase
{
    public function testNullRequestHunterDoesNothing()
    {
        $dir_root = './';
        $nullRequestHunter = new NullRequestHunter();
        $nullRequestHunter->run();
        $this->assertTrue(! file_exists($dir_root . 'storage/rh/' . date('Ymd')));
    }

    public function testNullTypeFactoryReturnNullRequestHunterAnyway()
    {
        $factory = new DriverRequestHunterFactory();
        $sut = $factory
            ->driver('Non existed driver')
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY);
        $this->assertTrue(is_a($sut, NullRequestHunter::class));
    }
}
