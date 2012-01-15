<?php

define ('SITE_PATH', realpath(dirname(__FILE__)));

define('OS_WINDOWS', 1);
define('OS_LINUX', 2);
define('OS_MAC', 4);

$current_path = realpath(dirname(__FILE__));
define('LIB_PATH', $current_path.'/../MicroFW');

require_once(LIB_PATH.'/MFW/AutoLoader.php');

$autoloader = new MFW_AutoLoader();

spl_autoload_register(array($autoloader, 'loadLibrary'));