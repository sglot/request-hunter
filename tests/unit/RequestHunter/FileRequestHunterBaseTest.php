<?php

namespace unit\RequestHunter;

use PHPUnit\Framework\TestCase;
use Tadzumi\RequestHunter\Exception\RequestHunterException;
use Tadzumi\RequestHunter\Factory\DriverRequestHunterFactory;
use Tadzumi\RequestHunter\Factory\TypeRequestHunterFactoryBase;
use unit\RequestHunter\Helper\TestHelper;


class FileRequestHunterBaseTest extends TestCase
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

    public function testRead()
    {
        $file = $this->dir_root . 'storage/rh/testWriteFile.php';
        $data = ['testKey' => 'testValue'];

        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $sut->setFile($file);
        $sut->write($data);
        $result = $sut->read();

        $this->assertTrue($result === $data);

        TestHelper::rmAll($file);
    }

    public function testWrite()
    {
        $file = $this->dir_root . 'storage/rh/testWriteFile.php';
        $data = ['testKey' => 'testValue'];

        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $sut->setFile($file);
        $sut->write($data);

        $result = json_decode(file_get_contents($file), true);

        $this->assertTrue($result === $data);

        TestHelper::rmAll($file);
    }

    public function testCreateFile()
    {
        $file = $this->dir_root . 'storage/rh/testWriteFile.php';

        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $sut->setFile($file);
        $sut->createFile();

        $result = json_decode(file_get_contents($file), true);

        $this->assertTrue($result === []);

        TestHelper::rmAll($file);
    }

    public function testCreateDir()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $path = $sut->makeDirPath($_SERVER['REMOTE_ADDR']);
        $sut->createDir($path);

        $this->assertTrue(file_exists($path));

        TestHelper::rmAll($path);
    }

    public function testCreateDirWhenAlreadyExist()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $path = $sut->makeDirPath($_SERVER['REMOTE_ADDR']);

        $sut->createDir($path);
        $sut->createDir($path);
//        $this->expectException('RequestHunterException'); // if access denied
        $this->assertTrue(file_exists($path));
        TestHelper::rmAll($path);
    }

    public function testMakeFileName()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $file = $sut->makeFileName('remote_addr');
        $customFile = "./storage/rh/" . date('Ymd') . '/remote_addr/statistics.php';
        $this->assertTrue($file === $customFile);
    }

    public function testMakeDirPath()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $dir = $sut->makeDirPath('remote_addr');
        $customDir = "./storage/rh/" . date('Ymd') . '/remote_addr';
        $this->assertTrue($dir === $customDir);
    }
}
