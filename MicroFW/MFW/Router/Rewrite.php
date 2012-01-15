<?php
/**
 * Rewrites urls to controllers and action. Mimics Apache's or IIS' rewrite engine
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Router
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * Rewrites urls to controllers and action. Mimics Apache's or IIS' rewrite engine
 *
 * @category   MicroFramework
 * @package    Router
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Router_Rewrite
{
    /**
     * @var array The routes
     */
    protected $routes = array();

    /**
     * @var string The location of the controller files
     */
    protected $controller_path;

    /**
     * Add route to rewrite engine
     *
     * @param MFW_Router_Route $route The route to add
     */
    public function addRoute(MFW_Router_Route $route)
    {
        $this->routes[$route->getName()] = $route;
    }

    /**
     * Get all routes
     *
     * @return array The routes in the rewrite engine
     */
    protected function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Get the uri of the route based on the parameters
     *
     * @param string $name The name of the route
     * @param array $params The parameters to build the uri
     *
     * @return string The current URI if name is null or the parsed URI
     */
    public function getUri($name = null, array $params = array())
    {
        if ($name === null) {
            return $this->getCurrentUri();
        }

        $this->validateRoute($name);

        return $this->routes[$name]->getParsedUri($params);
    }

    /**
     * Get the current URI
     *
     * @return string The current URI
     */
    protected function getCurrentUri()
    {
        return rtrim($_SERVER['REQUEST_URI'], '/');
    }

    /**
     * Validate a route
     *
     * @param string $name The name of the route
     *
     * @throws OutOfBoundsException If the route isn't loaded in the rewrite engine
     */
    protected function validateRoute($name)
    {
        if(!array_key_exists($name, $this->routes)) {
            throw new OutOfBoundsException('Unknown route: `' . $name . '`.');
        }
    }

    /**
     * Set the path (the location) of the controller files
     *
     * @todo Add better exception
     *
     * @param string $path The path of the controller files
     *
     * @throws Exception If the path doesn't exists
     */
    public function setControllerPath($path)
    {
        if (!is_dir($path)) {
            throw new InvalidArgumentException('The specified controller directory (`' . $path . '`) does not exist.');
        }

        $this->controller_path = $path;
    }
}