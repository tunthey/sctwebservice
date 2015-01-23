<?php
require_once('includes/config.php');
require_once(LIB_PATH . 'Exception.class.php');
require_once(LIB_PATH . 'Database.class.php');
require_once(LIB_PATH . 'Logger.class.php');

$db = New Database();
$db->set_variables(DB_DRIVER, DB_HOST,DB_PORT, DB_NAME, DB_USER, DB_PASSWORD);
if(!$db)
	die('Database Connection Not Available');
?>