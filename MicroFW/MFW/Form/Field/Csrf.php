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

    protected function getToken()
    {
        if (!isset($_SESSION['MFW_csrf-token'])) {
            $_SESSION['MFW_csrf-token'] = $this->getRandomString($this->getRandomCharsString());
        }

        return $_SESSION['MFW_csrf-token'];
    }

    protected function getRandomCharsString()
    {
        return 'bcdfghjklmnpqrstvwxzBCDFGHJKLMNPQRSTVWXZ0123456789!@#$%^&*()-_=+[]{}|;:,.<>/?';
    }

    protected function getRandomString($chars, $length = 128)
    {
        $count = strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= substr($chars, $index, 1);
        }

        return sha1($result);
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