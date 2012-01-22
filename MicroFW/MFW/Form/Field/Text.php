<?php
/**
 * HTML text input type
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
 * HTML text input type
 *
 * @category   MicroFramework
 * @package    Form
 * @subpackage Fields
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Form_Field_Text extends MFW_Form_Field_FieldAbstract
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
     * Set the fieldtype
     *
     * @return void
     */
    protected function setFieldType()
    {
        $this->fieldType = 'text'
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
        if ($this->isRequired() && !$this->getData()) {
            $this->addError('form.field.required');
        }

        if ($this->getData() && !$this->meetsRequirements()) {
            $this->addError('form.field.invalid-format');
        }

        if (!empty($this->getErrors())) {
            return false;
        }

        return true;
    }
}