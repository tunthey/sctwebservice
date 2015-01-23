<?php
// Config file for the Web Service Application
defined('ENVIRONMENT')
    || define('ENVIRONMENT', 'development');

defined('BASE_URL')
    || define('BASE_URL', 'http://localhost:81/sctwebservice/');

defined('DS')
    || define('DS', DIRECTORY_SEPARATOR);

defined('ROOT_PATH')
    || define('ROOT_PATH', realpath(dirname(__FILE__). DS . ".." . DS));

defined('LIB_PATH')
    || define('LIB_PATH', ROOT_PATH . DS . 'libs'. DS);

defined('LOG_PATH')
    || define('LOG_PATH', ROOT_PATH . DS . 'tmp' . DS . 'logs' . DS);

defined('DB_HOST')
    || define('DB_HOST', 'localhost');

defined('DB_PORT')
    || define('DB_PORT', '5432');

defined('DB_NAME')
    || define('DB_NAME', 'sctmobile');

defined('DB_USER')
    || define('DB_USER', 'postgres');

defined('DB_PASSWORD')
    || define('DB_PASSWORD', 'root');

defined('DB_DRIVER')
    || define('DB_DRIVER', 'pgsql');
?>