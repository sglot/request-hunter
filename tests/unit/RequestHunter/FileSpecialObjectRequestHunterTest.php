<?php

namespace unit\RequestHunter;

use PHPUnit\Framework\TestCase;
use Tadzumi\RequestHunter\File\FileSpecialObjectRequestHunter;


class FileSpecialObjectRequestHunterTest extends TestCase
{
    public function testFileSpecialObjectRequestHunterDoesNothingUntilHeIsStub()
    {
        $dir_root = './';
        $nullRequestHunter = new FileSpecialObjectRequestHunter();
        $nullRequestHunter->run();
        $this->assertTrue(! file_exists($dir_root . 'storage/rh/' . date('Ymd')));
    }
}
