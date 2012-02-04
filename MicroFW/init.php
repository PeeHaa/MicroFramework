<?php

session_start();

require_once(MFW_LIB_PATH.'/define.php');

require_once(MFW_SITE_PATH.'/routes.php');

require_once(MFW_LIB_PATH.'/MFW/AutoLoader.php');

$autoloader = new MFW_AutoLoader();

spl_autoload_register(array($autoloader, 'loadProject'));
spl_autoload_register(array($autoloader, 'loadLibrary'));