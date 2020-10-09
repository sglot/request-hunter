<?php

namespace unit\RequestHunter;

use PHPUnit\Framework\TestCase;
use Tadzumi\RequestHunter\Factory\DriverRequestHunterFactory;
use Tadzumi\RequestHunter\Factory\TypeRequestHunterFactoryBase;
use unit\RequestHunter\Helper\TestHelper;


class FileServerRequestHunterTest extends TestCase
{
    protected $factory;
    protected $dir_root;
    protected $method;
    protected $route;
    protected $remoteAddr;

    protected function setUp(): void
    {
        $this->dir_root = './';
        $this->factory = new DriverRequestHunterFactory();

        $this->method = 'GET';
        $this->route = '/test/path';
        $this->remoteAddr = 'testtest';

        $_SERVER['REMOTE_ADDR'] = $this->remoteAddr;
        $_SERVER['PATH_INFO'] = $this->route;
        $_SERVER['REQUEST_METHOD'] = $this->method;
    }

    public function testFileServerRequestHunterBuildCorrectlySetProperties()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path('/dir/to/storage')
            ->build();

        $this->assertTrue(
        $sut->getMethod() === $this->method
                && $sut->getRoute() === $this->route
                && $sut->getFile() === $sut->makeFileName($this->remoteAddr)
        );
    }

    public function testPrepareToRead()
    {
        $this->assertTrue(false);
    }

    public function testPrepareToCount()
    {
        $this->assertTrue(false);
    }

    public function testSetDefaultData()
    {
        $file = $this->dir_root . 'storage/rh/testWriteFile.php';
        $data = [
            $this->method => [
                $this->route => ['count' => 1]
            ]
        ];

        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $sut->setFile($file);
        $sut->setDefaultData($this->method, $this->route);
        $result = $sut->read();

        $this->assertTrue($result === $data);

        TestHelper::rmAll($file);
    }
}
