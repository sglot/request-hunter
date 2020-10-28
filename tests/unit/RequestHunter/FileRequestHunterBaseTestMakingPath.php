<?php

namespace unit\RequestHunter;

use PHPUnit\Framework\TestCase;
use Tadzumi\RequestHunter\Factory\DriverRequestHunterFactory;
use Tadzumi\RequestHunter\Factory\TypeRequestHunterFactoryBase;

class FileRequestHunterBaseTestMakingPath extends TestCase
{
    protected $factory;
    protected $dir_root;
    protected $sut;

    protected function setUp(): void
    {
        $this->dir_root = './';
        $this->factory = new DriverRequestHunterFactory();
        $this->sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();
    }

    public function testMakeFileName()
    {
        $file = $this->sut->makeFileName('remote_addr');
        $customFile = "./storage/rh/" . date('Ymd') . '/remote_addr/statistics.php';
        $this->assertTrue($file === $customFile);
    }

    public function testMakeDirPath()
    {
        $dir = $this->sut->makeDirPath('remote_addr');
        $customDir = "./storage/rh/" . date('Ymd') . '/remote_addr';
        $this->assertTrue($dir === $customDir);
    }
}
