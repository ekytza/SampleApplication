<?php

namespace SampleApplication;

trait DBSettings
{
    /**
        * @var array Database connection settings
    */
    protected static $dsn = array(
        'user'    => 'test',
        'pass'    => 'test',
        'db'      => 'test'
    );

    /**
        * @var string Database table prefix
    */
    protected static $tablePrefix = 'tz_';

    /**
        * @var mixed Database connection pointer
    */
    protected static $dbConn = null;

    /**
         * Database settings getter
         *
         * @param string option name
         * @return string|null
    */
    public static function getDBSettings($var)
    {
        if (isset(static::$$var))    return static::$$var;
        else return null;
    }

    /**
         * Database connection singleton
         *
         * @return mixed Database connection pointer
    */
    public static function getInstance()
    {
        return (static::$dbConn) ? static::$dbConn : static::$dbConn = new \SafeMySQL(static::$dsn);
    }
}

?>