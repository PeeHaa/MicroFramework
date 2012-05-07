<?php

/*
 * Routes are defined as array elements in the following format:
 *
 * Array key is route name (must be unique)
 * First element is the url to route (use a colon for variable parts e.g. /some/url/:variable)
 * Second element is the controller/action to route the request to
 * Third element are the default values for the url variables (e.g. array('variable'=>'value') or to make the variable optional array('variable'=>false))
 * Fourth optional element are the requirements to the variable should follow, this is a regex pattern
*/

$routes = array(

'index'                             => array('/',
                                             'index/index', array()),

);