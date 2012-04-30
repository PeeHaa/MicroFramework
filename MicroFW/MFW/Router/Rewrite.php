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
     * @var MGW_Http_Request The request
     */
    protected $request;

    /**
     * Creates an instance of the rewrite engine
     *
     * @param array $routes The routes as defined in the routes.php of the project
     * @param MFW_Http_Request $request The request
     *
     * @return void
     */
    public function __construct(array $routes, MFW_Http_Request $request)
    {
        $this->parseRoutes($routes);

        $this->request = $request;
    }

    /**
     * Parses the project routes and adds them to the rewrite engine
     * The array should have the format Array $name => $uri, $handler, [$defaults], [$requirements]
     *
     * @param array $routes The routes as defined in the routes.php of the project
     *
     * throws UnderflowException If no route is defined
     * @return void
     */
    protected function parseRoutes(array $routes)
    {
        if (empty($routes)) {
            throw new UnderflowException('At least one route should be defined in the project.');
        }

        foreach($routes as $name => $properties) {
            $routeDefaults = array();
            if (isset($properties[2])) {
                $routeDefaults = $properties[2];
            }

            $routeRequirements = array();
            if (isset($properties[3])) {
                $routeDefaults = $properties[3];
            }

            $route = new MFW_Router_Route($name,
                                          $properties[0],
                                          new MFW_Router_Route_Handler($properties[1]),
                                          $routeDefaults,
                                          $routeRequirements
                                          );
            $this->addRoute($route);
        }
    }

    /**
     * Add route to rewrite engine
     *
     * @param MFW_Router_Route $route The route to add
     */
    protected function addRoute(MFW_Router_Route $route)
    {
        $this->routes[$route->getName()] = $route;
    }

    /**
     * Get a routes based on name
     *
     * @throws OutOfBoundsException if no route matches
     *
     * @return MFW_Router_Route The route
     */
    protected function getRoute($name)
    {
        if (!array_key_exists($name, $this->routes)) {
            throw new OutOfBoundsException('No route matches with name: `' . $name . '`');
        }

        return $this->routes[$name];
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
        return rtrim($this->request->getPath(), '/');
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
     * Finds the route given an url
     *
     * @param MFW_HTTP_Request $request The info of the current request
     *
     * @throws RuntimeException If no routes matches the url
     * @return MFW_Router_Route The first route that matches
     */
    public function getRouteByUrl(MFW_HTTP_Request $request)
    {
        $routes = $this->getRoutes();

        foreach($routes as $route) {
            if ($route->matchesUrl($request->getPath())) {
                return $route;
            }
        }

        throw new RuntimeException('No route matches url: `' . $url . '`');
    }
}