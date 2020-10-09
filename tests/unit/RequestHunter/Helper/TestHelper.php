<?php


namespace unit\RequestHunter\Helper;


class TestHelper
{
    public static function rmAll($path)
    {
        if (is_file($path)) return unlink($path);

        if (is_dir($path)) {
            foreach (scandir($path) as $p) if (($p != '.') && ($p != '..'))
                self::rmAll($path . DIRECTORY_SEPARATOR . $p);
            return rmdir($path);
        }
        return false;
    }
}