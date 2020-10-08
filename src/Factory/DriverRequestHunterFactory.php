<?php


namespace Tadzumi\RequestHunter\Factory;

/**
 * You may want to store the collected information about requests not only in files,
 * but also send it to a database, queue or other host.
 * Then implement your factory for the new driver.
 */
class DriverRequestHunterFactory
{
    const FILE_DRIVER = 'file';
    const DB_DRIVER = 'db';

    public static function driver(string $driver): TypeRequestHunterFactoryBase
    {
        if ($driver === self::FILE_DRIVER) {
            return new FileRequestHunterFactory();
        }

        return new FileRequestHunterFactory();
    }
}