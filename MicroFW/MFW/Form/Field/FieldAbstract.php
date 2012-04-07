<?php
/**
 * Provides a base class for form fields
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
 * Provides a base class for form fields
 *
 * @category   MicroFramework
 * @package    Form
 * @subpackage Fields
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
abstract class MFW_Form_Field_FieldAbstract
{
    /**
     * @var string The type of the input field
     */
    protected $fieldType;

    /**
     * @var boolean Whether the field is required
     */
    protected $required = false;

    /**
     * @var null|string The initial value of the field if any
     */
    protected $initial = null;

    /**
     * @var array The (HTML) attributes of the field
     */
    protected $attributes = array();

    /**
     * @var null|string The requirements the value should meet if any
     */
    protected $requirements = null;

    /**
     * @var string The raw user-specified value of the field
     */
    protected $rawData = null;

    /**
     * @var null|string The user-specified value of the field
     */
    protected $data = null;

    /**
     * @var array The errors after submit if any
     */
    protected $errors = array();

    /**
     * Creates an instance of the field
     *
     * @param array $args The arguments to initialize the field
     */
    public function __construct($args = array())
    {
        $this->setFieldType();

        if (array_key_exists('required', $args)) {
            $this->setRequired($args['required']);
        }

        if (array_key_exists('initial', $args)) {
            $this->setInitial($args['initial']);
        }

        if (array_key_exists('attributes', $args)) {
            $this->setAttributes($args['attributes']);
        }

        if (array_key_exists('requirements', $args)) {
            $this->setRequirements($args['requirements']);
        }

        if (array_key_exists('data', $args)) {
            $this->setRawData($args['data']);
        }
    }

    /**
     * Set the fieldtype
     *
     * @return void
     */
    protected abstract function setFieldType();

    /**
     * Get the fieldtype
     *
     * @return string The type of the field
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }

    /**
     * Set the required flag on the field
     *
     * @param boolean $required Whether the field is required
     * @return void
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * Get the required flag on the field
     *
     * @return boolean Whether the field is required
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * Set the initial value
     *
     * @param string $initial The initial value
     * @return void
     */
    public function setInitial($initial)
    {
        $this->initial = $initial;
    }

    /**
     * Get the initial value
     *
     * @return string The initial value
     */
    public function getInitial()
    {
        return $this->initial;
    }

    /**
     * Set the attributes
     *
     * @param array $attrs The attributes of the field
     * @return void
     */
    public function setAttributes(array $attrs)
    {
        $this->attributes = $attrs;
    }

    /**
     * Get the attributes
     *
     * @return array The attributes of the field
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the requirements
     *
     * @param string $reqs The requirements of the field value
     * @return void
     */
    public function setRequirements($reqs)
    {
        $this->requirements = $reqs;
    }

    /**
     * Get the requirements
     *
     * @return string The requirements of the value field
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * Set the raw data
     *
     * @param string $rawData The raw data
     * @return void
     */
    protected function setRawData($rawData)
    {
        $this->rawData = $rawData;
    }

    /**
     * Get the raw data
     *
     * @return string The raw data
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * Set the cleaned data and the initial value of the field
     *
     * @param string $data The cleaned data
     * @return void
     */
    protected function setData($data)
    {
        $this->setInitial($data);

        $this->data = $data;
    }

    /**
     * Get the cleaned data
     *
     * @return string The cleaned data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Add an error
     *
     * @param string $msg The error message
     * @return void
     */
    protected function addError($msg)
    {
        $this->errors[] = $msg;
    }

    /**
     * Get the errors
     *
     * @return array The errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get the first error
     *
     * @return null|array The errors
     */
    public function getFirstError()
    {
        if ($this->isValid()) {
            return null;
        }

        return $this->errors[0];
    }

    /**
     * Cleans the data provided by the user
     *
     * @return void
     */
    public abstract function clean();

    /**
     * Checks whether the user provided data is valid
     *
     * @return boolean
     */
    public abstract function isValid();

    /**
     * Checks whether the value meets the requirements
     *
     * @return boolean
     */
    protected function meetsRequirements()
    {
        $requirements = $this->getRequirements();

        if ($requirements === null) {
            return true;
        }

        $pattern = '/' . $requirements . '/';
        if (!preg_match($pattern, $this->getData())) {
            return false;
        }

        return true;
    }
}