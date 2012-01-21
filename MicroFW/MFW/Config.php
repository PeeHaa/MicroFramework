<?php
/**
 * Load project specific configuration options
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Config
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * Load project specific configuration options
 *
 * @todo       Implement multiple config paths
 * @category   MicroFramework
 * @package    Config
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Config
{
    /**
     * @var stdClass The configuration options
     */
    protected $config;

    /**
     * Create config instance
     *
     * @param string $configname The name of the configuration
     * @return void
     */
    public function __construct($configname)
    {
        $this->validateConfigFile($configname);

        $this->setConfig($configname);
    }

    /**
     * Set the config
     *
     * @param string $configname The name of the configuration
     * @return void
     * @throws RuntimeException If config file does not exists or is not readable
     */
    protected function validateConfigFile($configname)
    {
        if (!is_file(SITE_PATH . '/config/' . $configname . '.php')) {
            throw new RuntimeException('Cannot find config (`' . $configname . '`) file.');
        }

        if (!is_readable(SITE_PATH . '/config/' . $configname . '.php')) {
            throw new RuntimeException('Cannot read config (`' . $configname . '`) file.');
        }
    }

    /**
     * Set the config
     *
     * @param string $configname The name of the configuration
     * @return void
     */
    protected function setConfig($configname)
    {
        require(SITE_PATH.'/config/'.$configname.'.php');

        $this->config = $config;
    }

    /**
     * Gets a configuration option
     *
     * @param string $name The name of the configuration option
     * @return void
     */
    public function __get($name)
    {
        return $this->config->$name;
    }
}