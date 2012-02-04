<?php
/**
 * Initializes the project
 *
 * PHP version 5.3
 *
 * @category   Example Project
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license
 * @version    1.0.0
 */

/**
 * Initializes the project
 *
 * @category   Example Project
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */

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
 * Get the rewrite-engine
 */
 $router = new MFW_Router_Rewrite($routes);

/**
 * Get the view
 */
$view = new MFW_View($router, MFW_SITE_PATH.'/code/views');

/**
 * Create an instance of the front controller
 */
$frontController = new MFW_Controller_Dispatcher($router, $view);

/**
 * Set the controller patj
 */
$frontController->setControllerPath(MFW_SITE_PATH.'/code/controllers');

/**
 * Dispatch the controller
 */
$frontController->dispatch();