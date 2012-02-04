<?php
/**
 * An example Controller
 *
 * PHP version 5.3
 *
 * @category   Example Project
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license
 * @version    1.0.0
 */

/**
 * An example Controller
 *
 * @category   Example Project
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class IndexController extends MFW_Controller
{
    /**
     * Set a view variable and render a view
     *
     * @category   Example Project
     * @author     Pieter Hordijk <info@pieterhordijk.com>
     */
    public function indexAction()
    {
        $this->view->someVar = 'Some value';

        $this->render('index.phtml');
    }
}