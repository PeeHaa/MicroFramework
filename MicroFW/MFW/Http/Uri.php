<?php
/**
 * The URI class validates URI's (incl. all the different parts or it) and
 * provides a way to change the URI
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Http
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * The URI class validates URI's (incl. all the different parts or it) and
 * provides a way to change the URI
 *
 * @category   MicroFramework
 * @package    Http
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Http_Uri
{
    /**
     * @var string The complete URI
     */
    protected $uri;

    /**
     * @var null|string The scheme of the URI
     */
    protected $scheme = null;

    /**
     * @var null|string The hostname of the URI
     */
    protected $host = null;

    /**
     * @var null|string The user of the URI
     */
    protected $user = null;

    /**
     * @var null|string The password of the URI
     */
    protected $pass = null;

    /**
     * @var array The path parts of the URI
     */
    protected $path = array();

    /**
     * @var string The query string parts of the URI
     */
    protected $query = array();

    /**
     * @var null|string The fragment of the URI
     */
    protected $fragment = null;

    /**
     * Creates the instance
     *
     * @param string $uri The URI to use
     * @return void
     */
    public function __construct($uri = null)
    {
        if ($uri !== null) {
            $this->setUri($uri);

            $this->setUriParts();
        }
    }

    /**
     * Sets the URI
     *
     * @param string $uri The URI to use
     * @return void
     */
    protected function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * Gets the URI
     *
     * @return string The URI
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Sets all the different URI parts
     *
     * @throws UnexpectedValueException If the URI provided is invalid
     * @return void
     */
    protected function setUriParts()
    {
        $parts = parse_url($this->getUri());

        if (!array_key_exists('scheme', $parts)) {
            throw new Exception ('You need to provide a full (absolute) URI.');
        }

        $this->setScheme($parts['scheme']);
        $this->setHost($parts['host']);

        if (array_key_exists('user', $parts)) {
            $this->setUser($parts['user']);
        }

        if (array_key_exists('pass', $parts)) {
            $this->setPass($parts['pass']);
        }

        if (array_key_exists('path', $parts)) {
            $this->setPath($parts['path']);
        }

        if (array_key_exists('query', $parts)) {
            $this->setQuery($parts['query']);
        }

        if (array_key_exists('fragment', $parts)) {
            $this->setFragment($parts['fragment']);
        }
    }

    /**
     * Sets the scheme of the URI
     *
     * @param string $scheme The scheme of the URI
     * @return void
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * Gets the scheme of the URI
     *
     * @return string The scheme of the URI
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Sets the hostname of the URI
     *
     * @param string $host The hostname of the URI
     * @return void
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Gets the hostname of the URI
     *
     * @return string The scheme of the URI
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Sets the user of the URI
     *
     * @param string $user The user of the URI
     * @return void
     */
    public function setUser($user)
    {
        $this->user = $host;
    }

    /**
     * Gets the user of the URI
     *
     * @return string The user of the URI
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the password of the URI
     *
     * @param string $pass The password of the URI
     * @return void
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
    }

    /**
     * Gets the password of the URI
     *
     * @return string The password of the URI
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * Sets the path parts of the URI
     *
     * @param string The path of the URI
     * @return void
     */
    public function setPath($path)
    {
        $this->path = explode('/', trim($path, '/'));
    }

    /**
     * Gets the path parts of the URI
     *
     * @return array The path parts of the URI
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the query variables of the URI
     *
     * @param string The query-string of the URI
     * @return void
     */
    public function setQuery($query)
    {
        $query_params = explode('&', $query);

        foreach($query_params as $param) {
            $namevaluepair = explode('=', $param);

            $this->query[$namevaluepair[0]] = $namevaluepair[1];
        }
    }

    /**
     * Gets the query variables of the URI
     *
     * @return array The query variables of the URI
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Sets the fragment of the URI
     *
     * @param string The fragment of the URI
     * @return void
     */
    public function setFragment($fragment)
    {
        $this->fragment = $fragment;
    }

    /**
     * Gets the fragment of the URI
     *
     * @return string The fragment of the URI
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Sets the scheme of the URI
     *
     * @param string The scheme of the URI
     * @return void
     */
    public function getBaseUri()
    {
        return $this->getScheme().'://'.$this->getHost();
    }

    /**
     * Updates (/ builds) the URI using all the (changed) information
     *
     * @todo Need to check if user without pass is valid. Same goes for pass without user
     * @return void
     */
    public function updateUri()
    {
        $uri = '';
        $uri.= $this->getScheme().'://';

        if ($this->getUser() !== null) {
            $uri.= $this->getUser();
        }

        if ($this->getPass() !== null) {
            $uri.= ':'.$this->getPass();
        }

        if ($this->getUser() !== null || $this->getPass() !== null) {
            $uri.= '@';
        }

        $uri.= $this->getHost();

        if ($this->getPath()) {
            $uri.= '/'.implode('/', $this->getPath());
        }

        if ($this->getQuery()) {
            $prefix = '?';
            $query = $this->getQuery();
            foreach($query as $name => $value) {
                $uri.= $prefix.$name.'='.$value;

                $prefix = '&';
            }
        }

        if ($this->getFragment()) {
            $uri.= '#'.$this->getFragment();
        }

        $this->setUri($uri);
    }

    /**
     * Check whether the URI is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        return filter_var($this->getUri(), FILTER_VALIDATE_URL);
    }
}