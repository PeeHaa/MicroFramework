<?php
/**
 * Base controller class
 * The controller should handle the request and render the view
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Controller
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * Base controller class
 *
 * @category   MicroFramework
 * @package    Controller
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Controller
{
    /**
     * @var MFW_Router_Rewrite The rewrite engine
     */
    protected $router;

    /**
     * @var MFW_View The view
     */
    protected $view;

    /**
     * @var array request parameters
     */
    protected $requestParams = array();

    /**
     * Create controller instance
     *
     * @param MFW_Router_Rewrite $router The router
     * @param MFW_View $view The view instance
     *
     * @return void
     */
    public function __construct(MFW_Router_Rewrite $router, MFW_View $view)
    {
        $this->setRouter($router);

        $this->setView($view);
    }

    /**
     * Set the router
     *
     * @param MFW_Router_Rewrite $router The router
     */
    protected function setRouter(MFW_Router_Rewrite $router)
    {
        $this->router = $router;
    }

    /**
     * Get the router instance
     *
     * @return MFW_Router_Rewrite The router instance
     */
    protected function getRouter()
    {
        return $this->router;
    }

    /**
     * Set the view
     *
     * @param MFW_View $view The view instance
     */
    protected function setView(MFW_View $view)
    {
        $this->view = $view;
    }

    /**
     * Get the view instance
     *
     * @return MFW_View The view
     */
    protected function getView()
    {
        return $this->view;
    }

    /**
     * Get the current or built url
     *
     * @param null|string $name The name of the route
     * @param array $params The parameters to build the url
     * @return string The current url or the built url based on routename and params
     */
    protected function url($name = null, $params = array())
    {
        return $this->getRouter()->getUri($name, $params);
    }

    /**
     * Get the variables of the request
     *
     * @ todo This stuff should be handled by a request class
     *
     * @return void
     */
    protected function getRequest()
    {
        $this->setRequestParams();

        $route = $this->getRouter()->getRouteByUrl($this->url());

        $this->setUrlParams($route);
    }

    /**
     * Adds the parameters of the current request to the requestParams
     *
     * @return void
     */
    protected function setRequestParams()
    {
        $requestParams = $this->getRequestParams();

        $this->requestParams = array_merge($requestParams, $_REQUEST, $_FILES);
    }

    /**
     * Get the request parameters
     *
     * @return array The request parameters currently saved
     */
    protected function getRequestParams()
    {
        return $this->requestParams;
    }

    /**
     * Adds the URL parameters of the current request to the requestParams
     *
     * @param MFW_Router_Route $route The route to get the parameters from
     *
     * @return void
     */
    protected function setUrlParams($route)
    {
        $cleanUrl = $this->normalizeUrl($this->url());
        $urlparts = explode('/', $cleanUrl);

        $routevars = $route->getVariableParams();

        $params = array();
        foreach($routevars as $index => $var) {
            if (!isset($urlparts[$index])) {
                continue;
            }
            $params[$var] = $urlparts[$index];
        }

        $requestParams = $this->getRequestParams();
        $this->requestParams = array_merge($requestParams, $params);
    }

    /**
     * Normalizes the URL the make it easy to use an explode('/', $url) on it
     *
     * @param string $url The url
     *
     * @return string The normalized url
     */
    protected function normalizeUrl($url)
    {
        $cleanUrl = $this->trimQueryString($url);
        $cleanUrl = $this->trimSlashes($url);

        return $cleanUrl;
    }

    /**
     * Trim leading and trailing slashes from url
     *
     * @param string $url The url
     *
     * @return string The url without leading and trailing slashes
     */
    protected function trimSlashes($url)
    {
        return trim($url, '/');
    }

    /**
     * Trim query string from an url
     *
     * @param string $url The url with (possibly) a query string
     *
     * @return string The url without a querystring
     */
    protected function trimQueryString($url)
    {
        return strtok($url, '?');
    }

    /**
     * Gets all the parameters in the request
     *
     * @return array All the parameters in the request
     */
    protected function getParams()
    {
        return $this->requestParams;
    }

    /**
     * Get a request parameter
     *
     * @param string $name The parameter to get
     * @return null|string Null if parameter doesn't exists or value
     */
    protected function getParam($name)
    {
        if(array_key_exists($name, $this->requestParams)) {
            return $this->requestParams[$name];
        } else {
            return NULL;
        }
    }

    /**
     * Renders a view
     *
     * @param string $name The (relative) filename of the view
     * @return void
     */
    protected function render($filename)
    {
        print($this->getView()->render($filename));
    }

    /**
     * Renders a ATOM view (XML)
     *
     * @param string $name The (relative) filename of the view
     * @return void
     */
    protected function renderXml($filename)
    {
        print($this->getView()->renderXml($filename));
    }

    /**
     * Renders a ATOM view (atom-xml)
     *
     * @param string $name The (relative) filename of the view
     * @return void
     */
    protected function renderAtom($filename)
    {
        print($this->getView()->renderAtom($filename));
    }

    /**
     * Redirects to an URL
     *
     * @param string $uri The URL to redirect to
     * @return void
     */
    protected function redirect($uri)
    {
        header('Location: ' . $uri);
        exit();
    }
}