<?php

namespace unit\RequestHunter;

use PHPUnit\Framework\TestCase;
use Tadzumi\RequestHunter\Factory\DriverRequestHunterFactory;
use Tadzumi\RequestHunter\Factory\TypeRequestHunterFactoryBase;
use Tadzumi\RequestHunter\File\FileRequestHunter;
use Tadzumi\RequestHunter\File\FileServerRequestHunter;
use Tadzumi\RequestHunter\NullRequestHunter;
use Tadzumi\RequestHunter\RequestHunterInterface;


class RequestHunterBuildTest extends TestCase
{
    protected $factory;
    protected $dir_root;

    protected function setUp(): void
    {
        $this->dir_root = './';
        $this->factory = new DriverRequestHunterFactory();
    }

    public function testReturnRightInterface()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $this->assertTrue(is_subclass_of($sut, RequestHunterInterface::class));
    }

    public function testReturnRightClassForFileDriverServerType()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $this->assertTrue(is_a($sut, FileServerRequestHunter::class));
    }

    public function testReturnRightClassForFileDriverRequestType()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::REQUEST_OBJECT)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        $this->assertTrue(is_a($sut, FileRequestHunter::class));
    }

    public function testReturnRightClassForNotExistDriver()
    {
        $sut = $this->factory
            ->driver('not existing driver');

        $this->assertTrue(is_a($sut, NullTypeFactory::class));
    }
    
    public function testReturnRightClassForNotExistType()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type('not exist');

        $this->assertTrue(is_a($sut, NullRequestHunter::class));
    }

    public function testPath()
    {
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::REQUEST_OBJECT)
            ->path('/dir/to/storage')
            ->build();

        $this->assertTrue($sut->getPath() == "/dir/to/storage");
    }

    public function testFileServerOneTypeCounter()
    {
        $_SERVER['REMOTE_ADDR'] = 'testtest';
        $_SERVER['PATH_INFO'] = '/test/path';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        for ($i = 0; $i < 11; $i++) {
            $sut->run();
        }

        $count = $sut->getCount('GET', '/test/path');
        $this->assertTrue(11 === $count);
        $this->rmAll($sut->makeDirPath('testtest'));
    }

    public function testFileServerDifferentTypeCounter()
    {
        $_SERVER['REMOTE_ADDR'] = 'testtest';
        $_SERVER['PATH_INFO'] = '/test/path';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::SERVER_ARRAY)
            ->path($this->dir_root . 'storage/rh')
            ->build();

        for ($i = 0; $i < 11; $i++) {
            $sut->run();
        }

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $sut->build();
        for ($i = 0; $i < 11; $i++) {
            $sut->run();
        }

        $countGet = $sut->getCount('GET', '/test/path');
        $countPost = $sut->getCount('POST', '/test/path');
        $this->assertTrue($countGet === $countPost and $countPost === 11);
        $this->rmAll($sut->makeDirPath('testtest'));
    }

    function rmAll($path)
    {
        if (is_file($path)) return unlink($path);
        if (is_dir($path)) {
            foreach (scandir($path) as $p) if (($p != '.') && ($p != '..'))
                $this->rmAll($path . DIRECTORY_SEPARATOR . $p);
            return rmdir($path);
        }
        return false;
    }
    
    //FileServerRequestHunter 
    
    public function testFileServerRequestHunterBuildCorrectlySetProperties()
    {
        $method = 'GET';
        $route = '/test/path';
        $remoteAddr = 'testtest';
        
        $_SERVER['REMOTE_ADDR'] = $remoteAddr;
        $_SERVER['PATH_INFO'] = $route;
        $_SERVER['REQUEST_METHOD'] = $method;
        
        $sut = $this->factory
            ->driver(DriverRequestHunterFactory::FILE_DRIVER)
            ->type(TypeRequestHunterFactoryBase::REQUEST_OBJECT)
            ->path('/dir/to/storage')
            ->build();
        
        $this->assertTrue(
            $sut->getMethod() === $method 
            && $sut->getRoute() === $route
            && $sut->getFile() === $this->makeFileName($remoteAddr)
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
        $this->assertTrue(false);
    }
    
    //FileRequestHunterBase 
    public function testRead()
    {
        $this->assertTrue(false);
    }
    
    public function testWrite()
    {
        $this->assertTrue(false);
    }
    
        public function testCreateFile()
    {
        $this->assertTrue(false);
    }
    
    public function testCreateDir()
    {
        $this->assertTrue(false);
    }
    
    public function testGetCount()
    {
        $this->assertTrue(false);
    }
    
    public function testMakeFileName()
    {
        $this->assertTrue(false);
    }
    
    public function testMakeDirPath()
    {
        $this->assertTrue(false);
    }
}
