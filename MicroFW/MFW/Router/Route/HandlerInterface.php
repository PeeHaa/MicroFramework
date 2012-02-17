<?php
/**
 * Route handler interface
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Router
 * @subpackage Route
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * Route handler interface
 *
 * @category   MicroFramework
 * @package    Router
 * @subpackage Route
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
interface MFW_Router_Route_HandlerInterface
{
    public function getController();

    public function getAction();
}