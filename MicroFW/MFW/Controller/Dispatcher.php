<?php
/**
 * Dispatches the request to the correct controller and action
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
 * Dispatches the request to the correct controller and action
 *
 * @category   MicroFramework
 * @package    Controller
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Controller_Dispatcher
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
     * @var string The controller path
     */
    protected $controllerPath;

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
     * Set the controller path
     *
     * @param string $path The controller path
     *
     * @throws RuntimeException If an invalid path is specified
     * @return void
     */
    public function setControllerPath($controllerPath)
    {
        if (!file_exists($controllerPath)) {
            throw new RuntimeException('Invalid controller path specified: `' . $path . '`');
        }

        $this->controllerPath = $controllerPath;
    }

    /**
     * Get the controller path
     *
     * @return string The controller path
     */
    protected function getControllerPath()
    {
        return $this->controllerPath;
    }

    /**
     * Dispatch the request
     * Execute the action as requested
     *
     * @throws RuntimeException If an invalid controller or action is requested
     * @return void
     */
    public function dispatch()
    {
        $requestedRoute = $this->getRouter()->getRouteByUrl($_SERVER['REQUEST_URI']);

        $controllerName = $this->getControllerName($requestedRoute->getHandler()->getController());
        $controllerFile = $this->getControllerFile($controllerName);

        include $controllerFile;

        $controller = new $controllerName($this->getRouter(), $this->getView());

        $actionName = $this->getActionName($requestedRoute->getHandler()->getAction());
        if (is_callable(array($controller, $actionName)) === false) {
            throw new Exception ('Unknown action specified: `'.$controllerName.'::'.$actionName.'`');
        }

        $controller->$actionName();
    }

    /**
     * Get the controller file based on the controller name
     *
     * @param string $controllerName The name of the controller
     *
     * @throws RuntimeException If an invalid controller is specified
     * @return string The controller file
     */
    protected function getControllerFile($controllerName)
    {
        $controllerFile = $this->getControllerPath() . '/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            throw new RuntimeException('Controller file (`' . $controllerFile . '`) not found.');
        }

        return $controllerFile;
    }

    /**
     * Get the controllername
     *
     * @param string $controller The controller handler
     *
     * @return string The controller name
     */
    protected function getControllerName($controller)
    {
        return ucfirst($controller) . 'Controller';
    }

    /**
     * Parser the action to get a name
     *
     * @param string $action The action handler
     *
     * @return string The action name
     */
    protected function getActionName($action)
    {
        $actionParts = explode('-', $action);

        $makeUppercase = False;
        $actionName = '';
        foreach($actionParts as $part) {
            if ($makeUppercase === True) {
                $actionName.= ucfirst($part);
            } else {
                $actionName.= $part;
            }

            $makeUppercase = True;
        }

        return $actionName . 'Action';
    }
}