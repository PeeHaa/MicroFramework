<?php
/**
 * Contains a valid emailaddress
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Mail
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * Contains a valid emailaddress
 *
 * @category   MicroFramework
 * @package    Mail
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Mail_Address
{
    /**
     * @var string The emailaddress
     */
    protected $address;

    /**
     * @var string The name
     */
    protected $name;

    /**
     * @var string The RFC 2822 name
     */
    protected $rfcString;

    /**
     * Create instance
     *
     * @param string $address The emailaddress
     * @param string $name The name
     */
    public function __construct($address, $name = null)
    {
        $this->setAddress($address);

        $this->setName($name);

        $this->setRfcString();
    }

    /**
     * Set the emailaddress if valid
     *
     * @param string $address The emailaddress
     *
     * @throws UnexpectedValueException if invalid emailaddress is given
     */
    protected function setAddress($address)
    {
        if (!self::isValidAddress($address)) {
            throw new UnexpectedValueException('Invalid emailaddress specified: `' . $address . '`.');
        }

        $this->address = $address;
    }

    /**
     * Get the emailaddress
     *
     * @return string The emailaddress
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the name
     *
     * @param string $name The name
     */
    protected function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the name
     *
     * @return string The name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the RFC 2822 string
     */
    protected function setRfcString()
    {
        if ($this->getName() === null) {
            $rfcName = $this->getAddress();
        } else {
            $rfcName = $this->getName();
            $rfcName.= ' <' . $this->getAddress() . '>';
        }

        $this->rfcString = $rfcName;
    }

    /**
     * Get the RFC 2822 string
     *
     * @return string The RFC 2822 string
     */
    public function getRfcString()
    {
        return $this->rfcString;
    }

    /**
     * Validate an emailaddress (as compatible with the RFC 3696 as feasible)
     *
     * Code by Douglas Lovell http://www.linuxjournal.com/article/9585
     * Please note that there are some issue with this check
     *
     * @param string $address The emailaddress to validate
     *
     * @return boolean
     */
    public static function isValidAddress($address)
    {
       $isValid = true;
       $atIndex = strrpos($address, '@');
       if (is_bool($atIndex) && !$atIndex)
       {
          $isValid = false;
       } else {
          $domain = substr($address, $atIndex+1);
          $local = substr($address, 0, $atIndex);
          $localLen = strlen($local);
          $domainLen = strlen($domain);
          if ($localLen < 1 || $localLen > 64) {
             // local part length exceeded
             $isValid = false;
          } elseif ($domainLen < 1 || $domainLen > 255) {
             // domain part length exceeded
             $isValid = false;
          } elseif ($local[0] == '.' || $local[$localLen-1] == '.') {
             // local part starts or ends with '.'
             $isValid = false;
          } elseif (strpos($local, '..') !== false) {
             // local part has two consecutive dots
             $isValid = false;
          } elseif (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
             // character not valid in domain part
             $isValid = false;
          } elseif (strpos($domain, '..') !== false) {
             // domain part has two consecutive dots
             $isValid = false;
          } elseif(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", '', $local))) {
             // character not valid in local part unless
             // local part is quoted
             if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", '', $local))) {
                $isValid = false;
             }
          }
          if ($isValid && !(checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A'))) {
             // domain not found in DNS
             $isValid = false;
          }
       }

       return $isValid;
    }
}