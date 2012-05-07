<?php
/**
 * Set the absolute base path of the project
 */
define ('MFW_SITE_PATH', realpath(dirname(__FILE__)));

/**
 * Set the absolute public path of the project
 */
define('MFW_PUBLIC_PATH', MFW_SITE_PATH.'/public');

/**
 * Setup the settings of the project
 */
require_once(MFW_SITE_PATH.'/init-deployment.php');

/**
 * Get the request
 */
$request = new MFW_Http_Request($_SERVER['SCRIPT_URI']);

/**
 * Get the rewrite-engine
 */
 $router = new MFW_Router_Rewrite($routes, $request);

/**
 * Get the database connection
 * Uncomment this line when you want to use a database connection in your project
 */
//$databaseConnection = new MFW_Db_Connection(MFW_DB_ENGINE, MFW_DB_NAME, MFW_DB_HOST, MFW_DB_PORT, MFW_DB_USERNAME, MFW_DB_PASSWORD);

/**
 * Get the view
 */
$view = new MFW_View($router, MFW_SITE_PATH.'/code/views');

/**
 * Instantiate the user
 * Uncomment the following lines if you want to use a user object in your project
 * Note that the user objects requires a valid database connection
 */
/*
$user = new MFW_Auth_User(new MFW_Db_Table($databaseConnection));
$view->user = $user->getAuthenticatedUser();
*/

/**
 * Instantiate CSRF token
 */
$csrfToken = new MFW_Security_CsrfToken();
$view->csrfToken = $csrfToken->getToken();

/**
 * Create an instance of the front controller
 */
$frontController = new MFW_Controller_Dispatcher($router, $view, $request);

/**
 * Set the controller path
 */
$frontController->setControllerPath(MFW_SITE_PATH.'/code/controllers');

/**
 * Dispatch the controller
 */
$frontController->dispatch();