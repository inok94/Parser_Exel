<?php

class Logger
{
    public static function log($string)
    {
        $time_now = time();
        $file = 'log.txt';
        $string_log = date("Y-m-d H:i:s", $time_now) . " " . $string . "\n";
        file_put_contents($file, $string_log, FILE_APPEND | LOCK_EX);
    }
}