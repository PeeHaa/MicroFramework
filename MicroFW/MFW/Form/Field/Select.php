<?php
/**
 * HTML select element
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
 * HTML select element
 *
 * @category   MicroFramework
 * @package    Form
 * @subpackage Fields
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Form_Field_Select extends MFW_Form_Field_FieldAbstract
{
    /**
     * @var array The options in the select element
     */
    protected $options = array();

    /**
     * Creates an instance of the field
     *
     * @param array $args The arguments to initialize the field
     */
    public function __construct($args = array())
    {
        parent::__construct($args);

        if (array_key_exists('options', $args)) {
            $this->setOptions($args['options']);
        }
    }

    /**
     * Set the fieldtype
     *
     * @return void
     */
    protected function setFieldType()
    {
        $this->fieldType = 'select'
    }

    /**
     * Set the options of the select element
     *
     * @param array $options The options
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Set the options of the select element
     *
     * @return array The options
     */
    public function getOptions()
    {
        return $this->options;
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

        if ($this->getData() && !$this->isValidOption()) {
            $this->addError('form.field.invalid-choice');
        }

        if (!empty($this->getErrors())) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether the user provided option is valid
     *
     * $param string $option The user specified option
     * @return boolean
     */
    protected function isValidOption($option)
    {
        $validOptions = $this->getOptions();

        if (array_key_exists($option, $validOptions)) {
            return true;
        }

        return false;
    }
}