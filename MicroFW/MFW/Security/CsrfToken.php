<?php
/**
 * Generates and validates csrf tokens
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Security
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * HTML hidden input type
 *
 * @category   MicroFramework
 * @package    Security
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Security_CsrfToken
{
    /**
     * @var string The CSRF token
     */
    protected $token;

    /**
     * Gets the current token or generates a new one if none is available
     *
     * @return string The token
     */
    public function getToken()
    {
        if (!isset($_SESSION['MFW_csrf-token'])) {
            $this->generateNewToken(128);
        }

        return $_SESSION['MFW_csrf-token'];
    }

    /**
     * Generates a new token and adds it to the session
     *
     * @param int $length The length of the string to generate
     *
     * @return void
     */
    public function generateNewToken($length)
    {
        $chars = $this->getRandomCharsString();

        $count = strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= substr($chars, $index, 1);
        }

        $_SESSION['MFW_csrf-token'] = sha1($result);
    }

    /**
     * Retrieves the string with characters which may be used to generate the token
     *
     * @return string The chracters to be used
     */
    protected function getRandomCharsString()
    {
        return 'bcdfghjklmnpqrstvwxzBCDFGHJKLMNPQRSTVWXZ0123456789!@#$%^&*()-_=+[]{}|;:,.<>/?';
    }

    /**
     * Validates the given token with the stored token
     *
     * @param string $token The input token
     *
     * @return bool Whether the given token is valid
     */
    public function validateToken($token)
    {
        if (isset($_SESSION['MFW_csrf-token']) && $_SESSION['MFW_csrf-token'] == $token) {
            return true;
        }

        return false;
    }
}