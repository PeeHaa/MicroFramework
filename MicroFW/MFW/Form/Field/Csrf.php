<?php
/**
 * HTML scrf input type
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
class MFW_Form_Field_Csrf extends MFW_Form_Field_Hidden
{
    /**
     * Creates an instance of the field
     *
     * @param array $args The arguments to initialize the field
     */
    public function __construct($args = array())
    {
        parent::__construct();

        $this->initial = $this->getToken();
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

    /**
     * Gets the current CSRF token
     *
     * @return string The CSRF token
     * @throws RuntimeException When there is no token available
     */
    protected function getToken()
    {
        if (!isset($_SESSION['MFW_csrf-token'])) {
            throw new RuntimeException('There is no CSRF token generated.');
        }

        return $_SESSION['MFW_csrf-token'];
    }

    /**
     * Checks whether the user provided data is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        if ($this->getData() != $this->getToken()) {
            $this->addError('form.field.invalid-format');
        }

        $errors = $this->getErrors();
        if (!empty($errors)) {
            return false;
        }

        return true;
    }
}