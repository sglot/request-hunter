<?php

namespace Tadzumi\RequestHunter\File;

use Exception;
use Tadzumi\RequestHunter\Exception\RequestHunterException;
use Tadzumi\RequestHunter\RequestHunterInterface;

/**
 * An abstract class for implementing request processing
 * with storing the collected information in files.
 */
abstract class FileRequestHunterBase implements RequestHunterInterface
{
    protected $path;
    protected $hunter;
    protected $method;
    protected $route;
    protected $file;

    abstract public function run();

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route): void
    {
        $this->route = $route;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    public function path(string $path): RequestHunterInterface
    {
        $this->path = $path;

        return $this;
    }

    public function build(): RequestHunterInterface
    {
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function makeFileName($uid): string
    {
        return $this->path . '/' . date('Ymd') . '/' . $uid . '/statistics.php';
    }

    public function makeDirPath($uid): string
    {
        return $this->path . '/' . date('Ymd') . '/' . $uid;
    }

    public function write(array $data): void
    {
        $f = fopen($this->file, "w");
        fwrite($f, json_encode($data, JSON_UNESCAPED_SLASHES));
        fclose($f);
    }

    public function getCount($method, $url): int
    {
        $data = $this->read();

        return $data[$method][$url]['count'];
    }

    public function read()
    {
        return json_decode(file_get_contents($this->file), true);
    }

    public function createFile(): void
    {
        $this->write([]);
    }

    public function createDir($path): void
    {
        try {
            mkdir($path, 0777, true);

        } catch (Exception $exception) {
            if (! file_exists($path)) {
                throw new RequestHunterException('Directory creating failed');
            }
        }


    }
}
