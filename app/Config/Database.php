<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations
     * and Seeds directories.
     *
     * @var string
     */
    public $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to
     * use if no other is specified.
     *
     * @var string
     */
    public $defaultGroup = 'default';

    /**
     * The default database connection.
     *
     * @var array
     */
    // public $default = [
    //  'DSN'      => '',
    //  'hostname' => 'pndra.ct2unygpjnnw.ap-south-1.rds.amazonaws.com', //'156.67.222.85',
    //  'username' => 'pndra', // 'u388497631_dev_rpsqf',
    //  'password' => 'yh1Oh1!8',//'Git@rps2021',
    //  'database' => 'pndra', //'u388497631_dev_rpsqf',
    //  'DBDriver' => 'MySQLi',
    //  'DBPrefix' => '',
    //  'pConnect' => false,
    //  'DBDebug'  => (ENVIRONMENT !== 'production'),
    //  'charset'  => 'utf8',
    //  'DBCollat' => 'utf8_general_ci',
    //  'swapPre'  => '',
    //  'encrypt'  => false,
    //  'compress' => false,
    //  'strictOn' => false,
    //  'failover' => [],
    //  'port'     => 3306,   
    // ];



    public $default = [
        'DSN'      => '',
        'hostname' => 'localhost', //'156.67.222.85',
        'username' => 'root', // 'u388497631_dev_rpsqf',
        'password' => '',//'Git@rps2021',
        'database' => 'rpsqf_api', //'u388497631_dev_rpsqf',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => (ENVIRONMENT !== 'production'),
        'charset'  => 'utf8',
        'DBCollat' => 'utf8_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 3306,   
    ];



    public $api_db = [
        'DSN'      => '',
        'hostname' => 'localhost', //'156.67.222.85',
        'username' => 'root', // 'u388497631_dev_rpsqf',
        'password' => '',//'Git@rps2021',
        'database' => 'rpsqf_api', //'u388497631_dev_rpsqf',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => (ENVIRONMENT !== 'production'),
        'charset'  => 'utf8',
        'DBCollat' => 'utf8_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 3306,   
    ];

    /**
     * This database connection is used when
     * running PHPUnit database tests.
     *
     * @var array
     */
    public $tests = [
        'DSN'      => '',
        'hostname' => '127.0.0.1',
        'username' => '',
        'password' => '',
        'database' => ':memory:',
        'DBDriver' => 'SQLite3',
        'DBPrefix' => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
        'pConnect' => false,
        'DBDebug'  => (ENVIRONMENT !== 'production'),
        'charset'  => 'utf8',
        'DBCollat' => 'utf8_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 3306,
    ];

    //--------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        // Ensure that we always set the database group to 'tests' if
        // we are currently running an automated test suite, so that
        // we don't overwrite live data on accident.
        if (ENVIRONMENT === 'testing')
        {
            $this->defaultGroup = 'tests';
        }
    }

    //--------------------------------------------------------------------

}
