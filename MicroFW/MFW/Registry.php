<?php
/**
 * Provides a registry
 *
 * PHP version 5.3
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Registry
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * Provides a registry
 *
 * @category   MicroFramework
 * @package    Registry
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Registry
{
    /**
     * @var array The contents of the registry
     */
    private $vars = array();

    /**
     * Add or set a registry variable
     *
     * @param string $key The key of the element
     * @param mixed $value The contents of the variable
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->vars[$key] = $value;
    }

    /**
     * Get a registry variable
     *
     * @param string $key The key of the element
     *
     * @return mixed The value
     */
    public function __get($key)
    {
        return $this->vars[$key];
    }
}