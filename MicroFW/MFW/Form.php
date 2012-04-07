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
     * @var bool Whether bot protection is enabled in the form
     */
    protected $botProtection;

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
     * Creates an instance of the form
     *
     * @param array $args The arguments to initialize the field
     */
    public function __construct($botProtection = True, $csrfProtection = True)
    {
        $this->setBotProtection($botProtection);
        $this->setCsrfProtection($csrfProtection);
    }

    /**
     * Set whether bot protection is enabled
     */
    protected function setBotProtection($botProtection)
    {
        $this->botProtection = $botProtection;
    }

    /**
     * Get whether bot protection is enabled
     *
     * @return bool Whether bot protection is enabled
     */
    protected function getBotProtection()
    {
        return $this->botProtection;
    }

    /**
     * Set whether csrf protection is enabled
     */
    protected function setCsrfProtection($csrfProtection)
    {
        $this->csrfProtection = $csrfProtection;
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
            throw new UnexpectedValueException('tried to access in undefined field (`' . $name . '`).');
        }

        return $this->fields[$name];
    }

    /**
     * Clean the submitted data of all the field
     */
    public function clean($data)
    {
        foreach($this->getFields() as $name => $field) {
            $field->clean();
        }
    }

    /**
     * Verifies whether the submitted form is valid
     *
     * @return bool Whether the form is valid
     */
    public function isValid()
    {
        foreach($this->getFields() as $name => $field) {
            if (!$fiel->isValid()) {
                $this->errors = True;
            }
        }

        return !$this->errors;
    }
}
