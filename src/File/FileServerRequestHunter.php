<?php

namespace Tadzumi\RequestHunter\File;

use Tadzumi\RequestHunter\Exception\RequestHunterException;
use Tadzumi\RequestHunter\RequestHunterInterface;

/**
 * The class provides collection of information from $ _SERVER
 * and storage of the received data in files.
 */
final class FileServerRequestHunter extends FileRequestHunterBase
{

    public function build(): RequestHunterInterface
    {
        $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '0';
        $_SERVER['PATH_INFO'] = $_SERVER['PATH_INFO'] ?? '/';
        $_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $_SERVER['REMOTE_ADDR'] = strlen($_SERVER['REMOTE_ADDR']) > 6 ? $_SERVER['REMOTE_ADDR'] : 'other';

        $this->setFile($this->makeFileName($_SERVER['REMOTE_ADDR']));
        $this->setRoute($_SERVER['PATH_INFO']);
        $this->setMethod($_SERVER['REQUEST_METHOD']);

        return $this;
    }

    /**
     * @throws RequestHunterException
     */
    public function run()
    {
        if (! $this->readingPrepare()) {
            return;
        }

        $data = $this->read();

        if (! $this->countingPrepare($data)) {
            return;
        }

        $data[$this->getMethod()][$this->getRoute()]['count']++;

        $this->write($data);
    }

    /**
     * @throws RequestHunterException
     */
    private function readingPrepare(): bool
    {
        if (! file_exists($this->file)) {
            $this->createDir($this->makeDirPath($_SERVER['REMOTE_ADDR']));
            $this->setDefaultData($this->getMethod(), $this->getRoute());

            return false;
        }

        return true;
    }

    private function countingPrepare(array $data): bool
    {
        if (! isset($data[$this->getMethod()][$this->getRoute()]['count'])) {
            $data = array_merge($data, $default = [
                $this->getMethod() => [
                    $this->getRoute() => [
                        'count' => 1
                    ]
                ]
            ]);
            $this->write($data);

            return false;
        }

        return true;
    }

    public function setDefaultData($method, $route): void
    {
        $data = [
            $method => [
                $route => ['count' => 1]
            ]
        ];
        $this->write($data);
    }
}
