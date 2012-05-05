<?php
/**
 * HTML forms
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Form
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * HTML text input type
 *
 * @todo       Implement checkbox, richtext and file fields
 *
 * @category   MicroFramework
 * @package    Form
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */

class MFW_Form
{
    /**
     * @var bool Whether csrf protection is enabled in the form
     */
    protected $csrfProtection;

    /**
     * @var bool Whether captcha is enabled in the form
     */
    protected $captcha;

    /**
     * @var array The fields in the form
     */
    protected $fields = array();

    /**
     * @var bool Whether there are erros in the form
     */
    protected $errors = False;

    /**
     * @var bool Whether the form is submitted
     */
    protected $isSubmitted = False;

    /**
     * Creates an instance of the form
     *
     * @param array $args The arguments to initialize the field
     */
    public function __construct($csrfProtection = True)
    {
        $this->setCsrfProtection($csrfProtection);
    }

    /**
     * Set whether csrf protection is enabled
     */
    protected function setCsrfProtection($csrfProtection)
    {
        $this->csrfProtection = $csrfProtection;

        if ($csrfProtection == true) {
            $this->addField('csrf-token', new MFW_Form_Field_Csrf());
        }
    }

    /**
     * Get whether csrf protection is enabled
     *
     * @return bool Whether csrf protection is enabled
     */
    protected function getCsrfProtection()
    {
        return $this->csrfProtection;
    }

    /**
     * Add a field to the form
     *
     * @param string $name The name of the field
     * @param MFW_Form_Field_FieldAbstract $field The field
     *
     * @throws InvalidArgumentException if the field is already added
     * @return bool Whether csrf protection is enabled
     */
    protected function addField($name, MFW_Form_Field_FieldAbstract $field)
    {
        if (array_key_exists($name, $this->fields)) {
            throw new InvalidArgumentException('Field names must be unique. There is already a field with the name `' . $name . '`.');
        }

        $this->fields[$name] = $field;
    }

    /**
     * Get all fields defined in the form
     *
     * @return array All the fields in the form
     */
    protected function getFields()
    {
        return $this->fields;
    }

    /**
     * Get a specific field
     *
     * throws UnexpectedValueException When the field is not in the form
     * @return MFW_Form_Field_FieldAbstract The field
     */
    public function getField($name)
    {
        if (!array_key_exists($name, $this->fields)) {
            throw new UnexpectedValueException('Trying to access an undefined field (`' . $name . '`).');
        }

        return $this->fields[$name];
    }

    /**
     * Clean the submitted data of all the field
     */
    public function clean($data)
    {
        foreach($this->getFields() as $name => $field) {
            $field->clean($data[$name]);
        }
    }

    /**
     * Verifies whether the submitted form is valid
     *
     * @return bool Whether the form is valid
     */
    public function isValid()
    {
        $this->isSubmitted = true;

        foreach($this->getFields() as $name => $field) {
            if (!$field->isValid()) {
                $this->errors = True;
            }
        }

        if ($this->processFileUploads() === false) {
            $this->errors = true;
        }

        return !$this->errors;
    }

    /**
     * Processes the file uploads when everything else in the form is valid
     *
     * @return bool Whether the file(s) are successfully processed
     */
    protected function processFileUploads()
    {
        if ($this->errors) {
            return false;
        }

        foreach($this->getFields() as $name => $field) {
            if ($this->getField($name)->getFieldType() != 'file') {
                continue;
            }

            if ($this->getField($name)->save() === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks whether the form has been submitted
     *
     * @return bool Whether the form is submitted
     */
    public function isSubmitted()
    {
        return $this->isSubmitted;
    }
}
