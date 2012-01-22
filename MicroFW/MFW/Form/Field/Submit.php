<?php
/**
 * HTML submit input type
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
 * HTML submit input type
 *
 * @category   MicroFramework
 * @package    Form
 * @subpackage Fields
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Form_Field_Submit extends MFW_Form_Field_FieldAbstract
{
    function __construct($args = array())
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
        $this->fieldType = 'submit';
    }

    /**
     * Cleans the data provided by the user
     *
     * @return void
     */
    public function clean()
    {
        $value = trim($this->getRawData());

        if (!$value) $value = null;

        $this->setData($value);
    }

    /**
     * Checks whether the user provided data is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        return true;
    }
}