<?php
/**
 * Setup all routes for the project
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
 * Setup all routes for the project
 * Defined routes should be compatible with the MicroFramework rewrite engine
 * https://github.com/PeeHaa/MicroFramework/tree/master/MicroFW/MFW/Router
 *
 * @category   Example Project
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
$routes = array(

'index'                             => array('/',
                                             'index/index', array()),

);