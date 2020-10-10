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
    protected $file;
    protected $defaultData;

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

        $this->file = $this->dir_root . 'storage/rh/testWriteFile.php';
        $this->defaultData = [
            $this->method => [
                $this->route => [
                    'count' => 1
                ]
            ]
        ];
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

    public function testPrepareToReadWhenFileNotExist()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $notExistedFile = $this->dir_root . 'storage/rh/testNotExistedFile.php';
        $sut->setFile($notExistedFile);
        $preparingResult = $sut->prepareToRead();

        $this->assertFalse($preparingResult);

        TestHelper::rmAll($notExistedFile);
    }

    public function testPrepareToReadMakeDefaultData()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $notExistedFile = $this->dir_root . 'storage/rh/testNotExistedFile.php';
        $sut->setFile($notExistedFile);
        $sut->prepareToRead();
        $dataAfterPrepare = $sut->read();

        $this->assertTrue($dataAfterPrepare == $this->defaultData);

        TestHelper::rmAll($notExistedFile);
    }

    public function testPrepareToReadWhenFileExist()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $existedFile = $this->file;
        $sut->setFile($existedFile);
        $sut->createFile();
        $preparingResult = $sut->prepareToRead();

        $this->assertTrue($preparingResult);

        TestHelper::rmAll($existedFile);
    }

    public function testPrepareToCountOnNotExistRoute()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $sut->setFile($this->file);
        $preparingResult = $sut->prepareToCount([]);

        $this->assertFalse($preparingResult);

        TestHelper::rmAll($this->file);
    }

    public function testPrepareToCountOnExistRoute()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $sut->setFile($this->file);
        $preparingResult = $sut->prepareToCount($this->defaultData);

        $this->assertTrue($preparingResult);

        TestHelper::rmAll($this->file);
    }

    public function testPrepareToCountMakeDefaultDataOnNotExistRoute()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $sut->setFile($this->file);
        $sut->prepareToCount([]);
        $dataAfterPrepare = $sut->read();

        $this->assertTrue($dataAfterPrepare == $this->defaultData);

        TestHelper::rmAll($this->file);
    }

    public function testSetDefaultData()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $sut->setFile($this->file);
        $sut->setDefaultData($this->method, $this->route);
        $result = $sut->read();

        $this->assertTrue($result === $this->defaultData);

        TestHelper::rmAll($this->file);
    }
}
