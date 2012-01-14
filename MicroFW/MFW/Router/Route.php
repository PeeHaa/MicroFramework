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
     * @var array The required parameters of the route
     */
    protected $required_params = array();

    /**
     * @var array The optional parameters of the route
     */
    protected $optional_params = array();

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
     * @param MFW_Router_Route_Handler $handler The handler of the route (controller/action)
     * @param array $defaults The default values of parameters of the route
     * @param array $req The requirements of the parameters of the route
     */
    public function __construct($name, $uri, MFW_Router_Route_Handler $handler, array $defaults = array(), array $req = array())
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
     * @param MFW_Router_Route_Handler $handler The handler of the route
     */
    protected function setHandler(MFW_Router_Route_Handler $handler)
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

        foreach($uriparts as $part) {
            if ($this->isUriPartVariable($part)) {
                continue;
            }

            $param = substr($part, 1);

            if (array_key_exists($param, $defaults)) {
                $this->addOptionalParam($param);
            } else {
                $this->addRequiredParam($param);
            }
        }
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
     * Add optional parameter to route
     *
     * @param string $param The parameter to add
     */
    protected function addOptionalParam($param)
    {
        $this->optional_params[] = $param;
    }

    /**
     * Get optional parameters of route
     *
     * @return array The optional parameters of route
     */
    protected function getOptionalParams()
    {
        return $this->optional_params;
    }

    /**
     * Add required parameter to route
     *
     * @param string $param The parameter to add
     */
    protected function addRequiredParam($param)
    {
        $this->required_params[] = $param;
    }

    /**
     * Get required parameters of route
     *
     * @return array The required parameters of route
     */
    protected function getRequiredParams()
    {
        return $this->required_params;
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

        if ($uriparts === false) {
            return '';
        }

        $uri = '';
        foreach($uriparts as $part) {
            if (!array_key_exists($part, $params)) {
                break;
            }

            if ($this->isUriPartVariable($part)) {
                $uri.= '/' . $params[substr($part, 1)];
            } else {
                $uri.= '/' . $part;
            }
        }

        if ($uri == '') {
            $uri = '/';
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

        // check whether there isn't a required-optional param, e.g. /my/uri/{missing optional param1}/param2
        $this->validateOptionalParams($params);

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
     *
     * @throws DomainException If there is an optional parameter missing
     */
    protected function validateOptionalParams($params)
    {
        $optional = $this->getOptionalParams();

        $missing_optional = false;
        foreach($optional as $opt) {
            if (array_key_exists($opt, $params)) {
                if ($missing_optional !== false) {
                    throw new DomainException('Missing optional parameter (`' . $missing_optional . '`) in route.');
                }
            } else {
                $missing_optional = $opt;
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
        $requirements = $this->getRequiredParams();

        foreach($requirements as $param => $req) {
            if (array_key_exists($param, $params)) {
                $pattern = '/' . $req . '/';
                if (!preg_match($pattern, $params[$param])) {
                    throw new UnexpectedValueException('Parameter `' . $param . '` doesn\'t match the required pattern.');
                }
            }
        }
    }
}