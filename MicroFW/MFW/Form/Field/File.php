<?php
/**
 * HTML file input type
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
 * HTML file input type
 *
 * @todo       Implement decent mime type sniffing rather than using the extensions
 *             although this method is broken I think it's fine for now
 *             Once the FW is upgraded to PHP 5.4 we will use Fileinfo and
 *             make it decent. So beware when counting on this you should always
 *             verify the file type before using it anywhere!
 * @category   MicroFramework
 * @package    Form
 * @subpackage Fields
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Form_Field_File extends MFW_Form_Field_FieldAbstract
{
    /**
     * @var string The allowed extensions
     */
    protected $allowedExtensions = array();

    /**
     * @var int The maximum allowed filesize
     */
    protected $maxSize = 0;

    /**
     * @var string The path to which the file will be saved
     */
    protected $savePath = null;

    /**
     * @var string The filename to which the file will be saved
     */
    protected $saveName = null;

    /**
     * Creates an instance of the field
     *
     * @param array $args The arguments to initialize the field
     *
     * @throws
     */
    public function __construct($args = array())
    {
        parent::__construct($args);

        if (array_key_exists('allowed_extensions', $args)) {
            $this->allowedExtensions = $args['allowed_extensions'];
        }
        if (array_key_exists('max_size', $args)) {
            $this->maxSize = $args['max_size'];
        }
        if (array_key_exists('save_path', $args)) {
            $this->savePath = $args['save_path'];
        }
        if (array_key_exists('save_name', $args)) {
            $this->saveName = $args['save_name'];
        }

        $this->testSaveLocation();
    }

    /**
     * Set the fieldtype
     *
     * @return void
     */
    protected function setFieldType()
    {
        $this->fieldType = 'file';
    }

    /**
     * Tests the folder to which we want to save
     *
     * @throws RuntimeException If no path is specified
     * @throws RuntimeException If the specified path does not exists
     * @throws RuntimeException If the path is not writable
     *
     * @return void
     */
    protected function testSaveLocation()
    {
        if (!$this->savePath) {
            throw new RuntimeException('No save path specified.');
        }

        if (!is_dir($this->savePath)) {
            throw new RuntimeException('Directory (`' . $this->savePath . '`) does not exist.');
        }

        if (!is_writeable($this->savePath)) {
            throw new RuntimeException('Directory (`' . $this->savePath . '`) is not writeable.');
        }
    }

    /**
     * Cleans the data provided by the user
     *
     * @return void
     */
    public function clean($data)
    {
        $this->setRawData($data);

        $this->setData(null);
    }

    /**
     * Checks whether the user provided file is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        $fileData = $this->getRawData();

        if ($this->isRequired() && !$fileData['tmp_name']) {
            $this->addError('form.field.required');
        }

        if ($fileData['tmp_name']) {
            if ($this->maxSize > 0 && $fileData['size'] > $this->maxSize) {
                $this->addError('form.field.filesize-exceeds-limits');
            }

            if (!empty($this->allowedExtensions) && !in_array($this->getExtension($fileData['name']), $this->allowedExtensions)) {
                $this->addError('form.field.invalid-filetype');
            }
        }

        $errors = $this->getErrors();
        if (!empty($errors)) {
            return false;
        }

        return true;
    }

    /**
     * Saves the file
     *
     * @return boolean True on success
     */
    public function save()
    {
        $fileData = $this->getRawData();

        if (!$fileData['tmp_name']) {
            return true;
        }

        $saveName = $this->generateSaveName($this->saveName, $fileData['tmp_name']);
        $fullSavePath = $this->getFullPath($this->savePath) . $saveName;

        if(!move_uploaded_file($fileData['tmp_name'], $fullSavePath)) {
            $this->addError('form.field.upload.failed');
        }

        $errors = $this->getErrors();
        if (!empty($errors)) {
            return false;
        }

        $this->setData($saveName);

        return true;
    }

    /**
     * Gets the extension of a file if any
     *
     * @param string $file The file
     *
     * @return string The extension (always in lowercase)
     */
    protected function getExtension($file)
    {
        if (strpos($file, '.') !== false) {
            $fileParts = explode('.', $file);
            return strtolower(end($fileParts));
        }

        return '';
    }

    /**
     * Generates the name and fullpath of the file to be saved based on method
     *
     * @param string $name The name / method to generate a save filename
     * @param string $file The uploaded file
     *
     * @return string The fullpath including the filename to be saved
     */
    protected function generateSaveName($name, $file)
    {
        $baseFilename = '';
        switch($name) {
            case 'hash':
                $baseFilename = $this->getFileHash($file);
                break;

            default:
                $baseFilename = $name;
        }

        $fileData = $this->getRawData();

        $saveFile = $baseFilename;
        if ($this->getExtension($fileData['name'])) {
            $saveFile.= '.' . $this->getExtension($fileData['name']);
        }

        return $saveFile;
    }

    /**
     * Gets the full path
     *
     * @param string $path The path which is going to be the base of the full path
     *
     * @return string The fullpath
     */
    protected function getFullPath($path)
    {
        if (strrpos($path, '/') !== 0) {
            $path.= '/';
        }

        return $path;
    }

    /**
     * Generates a hash of the file using both md5() and sha1() to prevent
     * unwanted collisions
     *
     * @param string $file The file
     *
     * @return string The hashed file
     */
    protected function getFileHash($file)
    {
        return hash_file('md5', $file).hash_file('sha1', $file);
    }
}