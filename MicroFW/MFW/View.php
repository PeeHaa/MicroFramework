<?php
/**
 * Base view class
 * The view is responsible for the representation layer
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    View
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * Base view class
 *
 * @category   MicroFramework
 * @package    View
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_View
{
    /**
     * @var MFW_Router_Rewrite The router engine
     */
    protected $router;

    /**
     * @var string The (root) path of the view files
     */
    protected $viewPath;

    /**
     * @var array The variables used in the view
     */
    protected $vars = array();

    /**
     * @var array The slots used in the view. These can be used to pass variable to partial (sub) views
     */
    protected $slots = array();

    /**
     * Create view instance
     *
     * @param MFW_Router_Rewrite $router The router
     * @param string $viewPath The (root) path of the view files
     *
     * @return void
     */
    public function __construct(MFW_Router_Rewrite $router, $viewPath, $modelFactory)
    {
        $this->setRouter($router);

        $this->setViewPath($viewPath);

        $this->vars['modelFactory'] = $modelFactory;
    }

    /**
     * Sets the router instance
     *
     * @param MFW_Router_Rewrite $router The router
     * @return void
     */
    protected function setRouter(MFW_Router_Rewrite $router)
    {
        $this->router = $router;
    }

    /**
     * Gets the router instance
     *
     * @return MFW_Router_Rewrite The router instance
     */
    protected function getRouter()
    {
        return $this->router;
    }

    /**
     * Sets the view path
     *
     * @param string $viewPath The (root) path of the view files
     *
     * @throws RuntimeException If an invalid path is specified
     * @return void
     */
    protected function setViewPath($viewPath)
    {
        if (!file_exists($viewPath)) {
            throw new RuntimeException('Invalid view path specified: `' . $path . '`');
        }

        $this->viewPath = $viewPath;
    }

    /**
     * Gets the view path
     *
     * @return string $viewPath The (root) path of the view files
     */
    protected function getViewPath()
    {
        return $this->viewPath;
    }

    /**
     * Sets a view variable
     *
     * @param string $key The name of the variable
     * @param mixed $value The value of the variable
     *
     * @return void
     */
    public function __set($key, $value) {
        $this->vars[$key] = $value;
    }

    /**
     * Gets a view variable
     *
     * @param string $key The name of the variable
     *
     * @throws OutOfBoundsException If the variable does not exist
     * @return mixed The value of the variable
     */
    public function &__get($key)
    {
        if (!array_key_exists($key, $this->vars)) {
            throw new OutOfBoundsException('View variable (`'.$key.'`) does not exist.');
        }

        return $this->vars[$key];
    }

    /**
     * Check whether a view variable isset
     *
     * @param string $key The name of the variable
     *
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->vars[$key]);
    }

    /**
     * Checks whether a view filename is found and return it
     *
     * @param string $name The name of the view file
     *
     * @throws RuntimeException If an invalid view is specified (view file not found)
     * @return string The filename of the vire
     */
    protected function getViewFilename($name)
    {
        $path = $this->getViewPath() . '/' . $name;

        if (!file_exists($path)) {
            throw new Exception('View (`' . $name . '`) not found in `' . $path . '`.');
        }

        return $path;
    }

    /**
     * Renders a view
     *
     * @param string $name The name of the view file
     *
     * @return string The content of the view
     */
    public function render($name)
    {
        ob_start();
        require($this->getViewFilename($name));
        return ob_get_clean();
    }

    /**
     * Renders an atom view (with the correct content-type)
     *
     * @param string $name The name of the view file
     *
     * @return string The content of the view
     */
    public function renderAtom($name)
    {
        header('Content-Type:application/atom+xml');

        return $this->render($name);
    }

    /**
     * Renders an xml view (with the correct content-type)
     *
     * @param string $name The name of the view file
     *
     * @return string The content of the view
     */
    public function renderXml($name)
    {
        header('Content-Type:text/xml');

        $this->render($name);
    }

    /**
     * Sets a slot variable
     *
     * @param string $name The name of the slot
     * @param mixed $value The value of the slot
     *
     * @return void
     */
    public function setSlot($name, $value)
    {
        $this->slots[$name] = $value;
    }

    /**
     * Gets a slot variable
     *
     * @param string $name The name of the slot
     *
     * @throws OutOfBoundsException If slot is not set
     * @return mixed $value The value of the slot
     */
    public function getSlot($name)
    {
        if (!array_key_exists($name, $this->slots)) {
            throw new OutOfBoundsException('Slot (`' . $name . '`) does not exist.');
        }

        return $this->slots[$name];
    }

    /**
     * Gets a slot variable or the default value of the slot variable does not exist
     *
     * @param string $name The name of the slot
     * @param mixed $value The default value
     *
     * @return mixed $value The value (or default value) of the slot
     */
    public function defaultSlot($name, $value)
    {
        if (array_key_exists($name, $this->slots)) {
            return $this->slots[$name];
        } else {
            return $value;
        }
    }

    /**
     * Get the current or built url
     *
     * @param null|string $name The name of the route
     * @param array $params The parameters to build the url
     *
     * @return string The current url or the built url based on routename and params
     */
    protected function url($name = null, $params = array())
    {
        return $this->getRouter()->getUri($name, $params);
    }
}