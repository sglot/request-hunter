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
    protected $sut;
    protected $refClass;
    protected $refReadingPrepare;
    protected $refCountingPrepare;

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

        $this->sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path('/dir/to/storage')
            ->build();

        $this->refClass = new \ReflectionClass(get_class($this->sut));
        $this->refReadingPrepare = $this->refClass->getMethod('readingPrepare');
        $this->refReadingPrepare->setAccessible(true);

        $this->refCountingPrepare = $this->refClass->getMethod('countingPrepare');
        $this->refCountingPrepare->setAccessible(true);
    }

    public function testFileServerRequestHunterBuildCorrectlySetProperties()
    {
        $this->assertTrue(
            $this->sut->getMethod() === $this->method
                    && $this->sut->getRoute() === $this->route
                    && $this->sut->getFile() === $this->sut->makeFileName($this->remoteAddr)
        );
    }

    public function testReadingPrepareWhenFileNotExist()
    {
        $notExistedFile = $this->dir_root . 'storage/rh/testNotExistedFile.php';
        $this->sut->setFile($notExistedFile);

        $preparingResult = $this->refReadingPrepare->invoke($this->sut);

        $this->assertFalse($preparingResult);

        TestHelper::rmAll($notExistedFile);
    }

    public function testReadingPrepareMakeDefaultData()
    {
        $notExistedFile = $this->dir_root . 'storage/rh/testNotExistedFile.php';
        $this->sut->setFile($notExistedFile);
        $this->refReadingPrepare->invoke($this->sut);
        $dataAfterPrepare = $this->sut->read();

        $this->assertTrue($dataAfterPrepare == $this->defaultData);

        TestHelper::rmAll($notExistedFile);
    }

    public function testReadingPrepareWhenFileExist()
    {
        $existedFile = $this->file;
        $this->sut->setFile($existedFile);
        $this->sut->createFile();
        $preparingResult = $this->refReadingPrepare->invoke($this->sut);

        $this->assertTrue($preparingResult);

        TestHelper::rmAll($existedFile);
    }

    public function testCountingPrepareOnNotExistRoute()
    {
        $this->sut->setFile($this->file);
        $preparingResult = $this->refCountingPrepare->invoke($this->sut, []);

        $this->assertFalse($preparingResult);

        TestHelper::rmAll($this->file);
    }

    public function testCountingPrepareOnExistRoute()
    {
        $this->sut->setFile($this->file);
        $preparingResult = $this->refCountingPrepare->invoke($this->sut, $this->defaultData);

        $this->assertTrue($preparingResult);

        TestHelper::rmAll($this->file);
    }

    public function testCountingPrepareMakeDefaultDataOnNotExistRoute()
    {
        $this->sut->setFile($this->file);
        $this->refCountingPrepare->invoke($this->sut, []);
        $dataAfterPrepare = $this->sut->read();

        $this->assertTrue($dataAfterPrepare == $this->defaultData);

        TestHelper::rmAll($this->file);
    }

    public function testSetDefaultData()
    {
        $this->sut->setFile($this->file);
        $this->sut->setDefaultData($this->method, $this->route);
        $result = $this->sut->read();

        $this->assertTrue($result === $this->defaultData);

        TestHelper::rmAll($this->file);
    }
}
