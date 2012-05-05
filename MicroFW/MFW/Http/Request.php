<?php
/**
 * Parses requests
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Request
 * @subpackage Http
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * Parses requests
 *
 * @category   MicroFramework
 * @package    Request
 * @subpackage Http
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Http_Request
{
    /**
     * @var string The request
     */
    protected $request;

    /**
     * @var array The request parts
     */
    protected $requestParts;

    /**
     * @var null|string The request scheme
     */
    protected $requestScheme = null;

    /**
     * @var null|string The request host
     */
    protected $requestHost = null;

    /**
     * @var null|string The request port
     */
    protected $requestPort = null;

    /**
     * @var null|string The request user
     */
    protected $requestUser = null;

    /**
     * @var null|string The request pass
     */
    protected $requestPass = null;

    /**
     * @var array The request path
     */
    protected $requestPath = array();

    /**
     * @var array The request query
     */
    protected $requestQuery = array();

    /**
     * @var null|string The request fragment
     */
    protected $requestFragment = null;

    /**
     * Creates the instance
     *
     * @param null|string $response The raw response
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
        $this->requestParts = parse_url($this->request);

        $this->parseUrl();
    }

    /**
     * Parse the current request
     *
     * @return void
     */
    protected function parseUrl()
    {
        foreach($this->requestParts as $name => $value) {
            switch($name) {
                case 'scheme':
                    $this->scheme = $value;
                    break;

                case 'host':
                    $this->host = $value;
                    break;

                case 'port':
                    $this->port = $value;
                    break;

                case 'user':
                    $this->user = $value;
                    break;

                case 'pass':
                    $this->pass = $value;
                    break;

                case 'path':
                    $this->path = explode('/', rtrim($value, '/'));
                    break;

                case 'query':
                    $this->query = explode('&', $this->trimQueryString($value));
                    break;

                case 'fragment':
                    $this->fragment = $value;
                    break;

                default:
                    break;
            }
        }
    }

    public function getPath()
    {
        return implode('/', $this->path);
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
}