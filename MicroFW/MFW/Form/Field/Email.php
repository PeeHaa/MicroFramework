<?php
/**
 * HTML text input type (with email validation)
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
 * HTML text input type (with email validation)
 *
 * @category   MicroFramework
 * @package    Form
 * @subpackage Fields
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Form_Field_Email extends MFW_Form_Field_Text
{
    /**
     * Creates an instance of the field
     *
     * @param array $args The arguments to initialize the field
     */
    public function __construct($args = array())
    {
        parent::__construct($args);
    }

    /**
     * Checks whether the user provided data is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        parent::isValid();

        if (!MFW_Mail_Address::isValidAddress($this->getData())) {
            $this->addError('form.field.invalid-emailaddress');
        }

        if (!empty($this->getErrors())) {
            return false;
        }

        return true;
    }
}