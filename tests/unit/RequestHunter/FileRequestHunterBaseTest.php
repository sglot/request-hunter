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
    protected $sut;

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

        $this->sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();
    }

    protected function tearDown(): void
    {
        TestHelper::rmAll($this->dir_root . 'storage/rh/' . date('Ymd'));
    }

    public function testRead()
    {
        $file = $this->dir_root . 'storage/rh/testWriteFile.php';
        $data = ['testKey' => 'testValue'];

        $this->sut->setFile($file);
        $this->sut->write($data);
        $result = $this->sut->read();

        $this->assertTrue($result === $data);
    }

    public function testWrite()
    {
        $file = $this->dir_root . 'storage/rh/testWriteFile.php';
        $data = ['testKey' => 'testValue'];

        $this->sut->setFile($file);
        $this->sut->write($data);

        $result = json_decode(file_get_contents($file), true);

        $this->assertTrue($result === $data);
    }

    public function testCreateFile()
    {
        $file = $this->dir_root . 'storage/rh/testWriteFile.php';

        $this->sut->setFile($file);
        $this->sut->createFile();

        $result = json_decode(file_get_contents($file), true);

        $this->assertTrue($result === []);
    }

    public function testCreateDir()
    {
        $path = $this->sut->makeDirPath($_SERVER['REMOTE_ADDR']);
        $this->sut->createDir($path);

        $this->assertTrue(file_exists($path));
    }

    public function testCreateDirWhenAlreadyExist()
    {
        $path = $this->sut->makeDirPath($_SERVER['REMOTE_ADDR']);

        $this->sut->createDir($path);
        $this->sut->createDir($path);
//        $this->expectException('RequestHunterException'); // if access denied
        $this->assertTrue(file_exists($path));
    }
}
