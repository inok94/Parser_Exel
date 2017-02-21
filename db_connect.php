<?php
require_once('Logger.php');


/**
 * Singleton
 */
class Database_connect
{
    private $_db;
    protected static $_instance;

    private function __construct()
    {
        try
        {
            $this->_db = new PDO(DB_DRIVER . ':host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
            $this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            Logger::log('connect to DB');
        }
        catch (Exception $e)
        {
            Logger::log("Sql error connection". $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __call($name, $arguments)
    {
        return $this->_db;
    }
}