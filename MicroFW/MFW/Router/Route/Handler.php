<?php
/**
 * Gets the controller and action of a route
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
 * Gets the controller and action of a route
 *
 * @category   MicroFramework
 * @package    Router
 * @subpackage Route
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Router_Route_Handler
{
    /**
     * @var string The name of the controller
     */
    protected $controller;

    /**
     * @var string The name of the action
     */
    protected $action;

    /**
     * Setup handler instance
     *
     * @param string $handler The string which contains the controller and action
     */
    public function __construct($handler)
    {
        $this->validateHandler($handler);

        $parts = explode('/', $handler);

        $this->setController($parts[0]);
        $this->setAction($parts[1]);
    }

    /**
     * Validate the handler string
     *
     * @param string The handler
     *
     * @throws UnexpectedValueException If the handler string isn't valid
     */
    protected function validateHandler($handler)
    {
        if (substr_count($handler, '/') !== 1) {
            throw new UnexpectedValueException('Invalid route handler `' . $handler . '`');
        }
    }

    /**
     * Set controller
     *
     * @param string $controller The controller
     */
    protected function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Get controller
     *
     * @return string The controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set action
     *
     * @param string $action The action
     */
    protected function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Get action
     *
     * @return string The action
     */
    public function getAction()
    {
        return $this->action;
    }
}