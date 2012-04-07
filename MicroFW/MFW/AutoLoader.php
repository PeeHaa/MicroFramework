<?php
/**
 * AutoLoader class provides autoload functions for spl_autoload
 *
 * This autoloader class provides autoload functions for project models and library code
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Autoloader
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * AutoLoader class provides autoload functions for spl_autoload
 *
 * This autoloader class provides autoload functions for project models and library code
 *
 * @category   MicroFramework
 * @package    Autoloader
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_AutoLoader
{
    /**
     * Load project models
     *
     * @param string $class The name of the class
     */
    public function loadProject($class)
    {
        $filename = MFW_SITE_PATH . '/code/models/' . $class . '.php';

        if (!is_readable($filename)) {
            return false;
        }

        include($filename);
    }

    /**
     * Load project forms
     *
     * @param string $class The name of the class
     */
    public function loadForms($class)
    {
        $parts = explode('_', $class);

        if (end($parts) != 'Form') {
            return false;
        }

        include(MFW_SITE_PATH . '/code/models/Forms.php');
    }

    /**
     * Load library classes
     *
     * @param string $class The name of the class
     */
    public function loadLibrary($class)
    {
        $path = explode('_', $class);

        $filename = MFW_LIB_PATH . '/' . implode('/', $path) . '.php';

        if (!is_readable($filename)) {
            return false;
        }

        include($filename);
    }
}