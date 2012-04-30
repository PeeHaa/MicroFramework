<?php
/**
 * Provides a route used by the rewrite engine
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
 * Provides a route to the Rewrite engine
 *
 * @todo       Create and use Uri class to better separate logic
 * @category   MicroFramework
 * @package    Router
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Router_Route
{
    /**
     * @var string The name of the route
     */
    protected $name;

    /**
     * @var string The URI of the route
     */
    protected $uri;

    /**
     * @var array The variable parameters (both required and optional) with index based on position in url
     */
    protected $variableParams = array();

    /**
     * @var array The required parameters of the route
     */
    protected $requiredParams = array();

    /**
     * @var array The optional parameters of the route
     */
    protected $optionalParams = array();

    /**
     * @var MFW_Router_Route_Handler The handler of the route (controller/action)
     */
    protected $handler;

    /**
     * @var array The default value of parameters of the route
     */
    protected $defaults;

    /**
     * @var array The requirements of the parameters of the route
     */
    protected $requirements;

    /**
     * Set all properties of the route
     *
     * @param string $name The name of the route
     * @param string $uri The uri of the route
     * @param MFW_Router_Route_HandlerInterface $handler The handler of the route (controller/action)
     * @param array $defaults The default values of parameters of the route
     * @param array $req The requirements of the parameters of the route
     */
    public function __construct($name, $uri, MFW_Router_Route_HandlerInterface $handler, array $defaults = array(), array $req = array())
    {
        $this->setName($name);

        $this->setUri($uri);

        $this->setHandler($handler);

        $this->setDefaults($defaults);

        $this->setRequirements($req);

        $this->setParams($uri);
    }

    /**
     * Set the route name
     *
     * @param string $name The name of the route
     */
    protected function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the route name
     *
     * @return string The name of the route
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the route uri
     *
     * @param string $uri The uri of the route
     */
    protected function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * Get the route uri
     *
     * @return string The uri of the route
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Get the normalized uri
     *
     * @return string The normalized uri of the route
     */
    protected function getNormalizedUri()
    {
        return $this->normalizeUri($this->getUri());
    }

    /**
     * Normalize an uri
     *
     * @param string $uri The uri to normalize
     *
     * @return string A normalized uri
     */
    protected function normalizeUri($uri)
    {
        return trim($uri, '/');
    }

    /**
     * Set the handler of the route
     *
     * @param MFW_Router_Route_HandlerInterface $handler The handler of the route
     */
    protected function setHandler(MFW_Router_Route_HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Get the handler of the route
     *
     * @return MFW_Router_Route_Handler The handler of the route
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Set defaults of route parameters
     *
     * @param array $defaults The default values of parameters of the route
     */
    protected function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * Get defaults of route parameters
     *
     * @return array The default values of parameters of the route
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Set requirements of route parameters
     *
     * @param array $req The requirements of parameters of the route
     */
    protected function setRequirements(array $req)
    {
        $this->requirements = $req;
    }

    /**
     * Get requirements of route parameters
     *
     * @return array The requirements of parameters of the route
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * Set route parameters
     *
     * @param string $uri The uri which may contain parameters
     */
    protected function setParams($uri)
    {
        $uriparts = $this->getUriParts();
        $defaults = $this->getDefaults();

        if ($uriparts === null) {
            return;
        }

        foreach($uriparts as $index => $part) {
            if (!$this->isUriPartVariable($part)) {
                continue;
            }

            $param = substr($part, 1);

            $this->addVariableParam($index, $param);

            if (array_key_exists($param, $defaults)) {
                $this->addOptionalParam($param);
            } else {
                $this->addRequiredParam($param);
            }
        }
    }

    /**
     * Add a variable parameters
     *
     * @param int $index The position of the parameter in the url
     * @param string $name The name of the parameter
     */
    protected function addVariableParam($index, $name)
    {
        $this->variableParams[$index] = $name;
    }

    /**
     * Gets all the variable parameters of the route
     *
     * @return array All variable parameters in the route with an index based on position in the url
     */
    public function getVariableParams()
    {
        return $this->variableParams;
    }

    /**
     * Get parts of the uri of the route
     *
     * @return null|array null when there are no parts or array with parts
     */
    protected function getUriParts()
    {
        if ($this->getNormalizedUri() == '') {
            return null;
        }

        return explode('/', $this->getNormalizedUri());
    }

    /**
     * Check whether uri part is variable
     *
     * @return boolean
     */
    protected function isUriPartVariable($part)
    {
        if (strpos($part, ':') !== 0) {
            return false;
        }

        return true;
    }

    /**
     * Check whether variable is a required variable
     *
     * @return boolean
     */
    protected function isUriVariableRequired($name)
    {
        $required = $this->getRequiredParams();

        if (in_array($name, $required)) {
            return true;
        }

        return false;
    }

    /**
     * Check whether variable is an optional variable
     *
     * @return boolean
     */
    protected function isUriVariableOptional($name)
    {
        $optional = $this->getOptionalParams();

        if (in_array($name, $optional)) {
            return true;
        }

        return false;
    }

    /**
     * Add optional parameter to route
     *
     * @param string $param The parameter to add
     */
    protected function addOptionalParam($param)
    {
        $this->optionalParams[] = $param;
    }

    /**
     * Get optional parameters of route
     *
     * @return array The optional parameters of route
     */
    protected function getOptionalParams()
    {
        return $this->optionalParams;
    }

    /**
     * Get optional parameter value
     *
     * @return string The default value of the parameter
     */
    protected function getDefaultParamValue($param)
    {
        $defaults = $this->getDefaults();

        return $defaults[$param];
    }

    /**
     * Add required parameter to route
     *
     * @param string $param The parameter to add
     */
    protected function addRequiredParam($param)
    {
        $this->requiredParams[] = $param;
    }

    /**
     * Get required parameters of route
     *
     * @return array The required parameters of route
     */
    protected function getRequiredParams()
    {
        return $this->requiredParams;
    }

    /**
     * Build uri based on parameters
     *
     * @param array $params The parameters to build the uri with
     *
     * @return string The parsed uri
     */
    public function getParsedUri(array $params)
    {
        $this->validateParams($params);

        $uriparts = $this->getUriParts();

        if ($uriparts === null) {
            return '';
        }

        $uri = '';
        foreach($uriparts as $part) {
            if ($this->isUriPartVariable($part)) {
                $paramname = substr($part, 1);

                if (array_key_exists($paramname, $params)) {
                    $uri.= '/' . $params[$paramname];
                } elseif ($this->getDefaultParamValue($paramname) !== false) {
                    $uri.= '/' . $this->getDefaultParamValue($paramname);
                }
            } else {
                $uri.= '/' . $part;
            }
        }

        return $uri;
    }

    /**
     * Validate all the user parameters of the route
     *
     * @param array $params The parameters to test against the route
     */
    protected function validateParams(array $params)
    {
        $this->validateRequiredParams($params);

        $this->validateMissingParams($params);

        $this->validateParamRequirements($params);
    }

    /**
     * Validate whether all required parameters are given
     *
     * @throws DomainException If there is a required parameter missing
     */
    protected function validateRequiredParams(array $params)
    {
        $required = $this->getRequiredParams();

        $missing = array_diff($required, array_keys($params));

        if (!empty($missing)) {
            throw new DomainException('Missing required parameter (`' . reset($missing) . '`) in route.');
        }
    }

    /**
     * Validate whether all optional parameters are given to prevent a missing parameter in the URL, e.g.: /param1//param3
     * Loops through all parts in the uri to find missing parts. If a missing parts is found it check for trailing parts
     * Trailingparts are either static parts, required variable or optional variables with a value
     *
     * @throws DomainException If there is an optional parameter missing
     */
    protected function validateMissingParams($params)
    {
        $defaults = $this->getDefaults();
        $uriparts = $this->getUriParts();

        if ($uriparts === null) {
            return;
        }

        $missing = false;
        foreach($uriparts as $part) {
            if ((!$this->isUriPartVariable($part) || $this->isUriVariableRequired(substr($part, 1))) && $missing !== false) {
                throw new DomainException('Missing optional parameter (`' . $missing . '`) in route.');
            } elseif ($this->isUriVariableOptional(substr($part, 1))) {
                $variableparam = substr($part, 1);

                if (array_key_exists($variableparam, $params) || (array_key_exists($variableparam, $defaults) && $defaults[$variableparam] !== false)) {
                    if ($missing !== false) {
                        throw new DomainException('Missing optional parameter (`' . $missing . '`) in route.');
                    }
                } else {
                    $missing = $variableparam;
                }
            }
        }
    }

    /**
     * Validate whether all requirements are met of the parameters
     *
     * @throws UnexpectedValueException If a parameter doesn't meet the requirements
     */
    protected function validateParamRequirements($params)
    {
        $requirements = $this->getRequirements();

        foreach($requirements as $param => $req) {
            if (array_key_exists($param, $params)) {
                $pattern = '/' . $req . '/';
                if (!preg_match($pattern, $params[$param])) {
                    throw new UnexpectedValueException('Parameter `' . $param . '` doesn\'t match the required pattern.');
                }
            }
        }
    }

    /**
     * Compares the route against an URL
     *
     * @return boolean True if route matches the url
     */
    public function matchesUrl($url)
    {
        $urlParts = explode('/', $this->normalizeUri($url));
        $routeParts = explode('/', $this->normalizeUri($this->getUri()));

        if (!$this->doStaticPartsMatch($urlParts, $routeParts)) {
            return false;
        }

        if (!$this->doesDepthMatch($urlParts, $routeParts)) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether all static parts of URL match the static parts of the route
     *
     * @return boolean True if static parts match
     */
    protected function doStaticPartsMatch($urlParts, $routeParts)
    {
        foreach($urlParts as $index => $urlPart) {
            if (!isset($routeParts[$index])) {
                return false;
            }

            if ($this->isUriPartVariable($routeParts[$index])) {
                continue;
            }

            if ($urlPart != $routeParts[$index]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks whether the URL has the correct depth to match the route
     *
     * @return boolean True if depth of the URL matches with the route
     */
    protected function doesDepthMatch($urlParts, $routeParts)
    {
        $numUrlParts = count($urlParts);
        $numRouteParts = count($routeParts);

        if (count($urlParts) == count($routeParts)) {
            return true;
        }

        if (count($urlParts) > count($routeParts)) {
            return false;
        }

        $match = true;

        for ($i = $numUrlParts; $i < $numRouteParts; $i++) {
            if (!$this->isUriPartVariable($routeParts[$i])) {
                return false;
            }
        }

        return true;
    }
}