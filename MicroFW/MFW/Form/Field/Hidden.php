<?php
/**
 * HTML hidden input type
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Form
 * @subpackage Fields
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * HTML hidden input type
 *
 * @category   MicroFramework
 * @package    Form
 * @subpackage Fields
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Form_Field_Hidden extends MFW_Form_Field_Text
{
    /**
     * Creates an instance of the field
     *
     * @param array $args The arguments to initialize the field
     */
    public function __construct($args = array())
    {
        parent::__construct();
    }

    /**
     * Set the fieldtype
     *
     * @return void
     */
    protected function setFieldType()
    {
        $this->fieldType = 'hidden';
    }
}