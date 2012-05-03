<?php
/**
 * Authentication class - provides a way to handle user objects in a project
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Auth
 * @subpackage User
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * Authentication class
 *
 * @category   MicroFramework
 * @package    Auth
 * @subpackage User
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Auth_User extends MFW_Model
{
    /**
     * Create instance and setup table connection
     *
     * @param MFW_Db_Table $table Instance of the table class
     */
    public function __construct(MFW_Db_Table $table)
    {
        $table->setTable('users');

        $this->table = $table;
    }

    /**
     * Encrypts a string using the chosen algo
     *
     * @param string $password The password to encrypt
     * @param string $type The encryption algo to use
     *
     * @return string The encrypted password
     */
    protected function cryptPassword($password, $algo = 'blowfish')
    {
        $cryptAlgo = '';

        switch($algo) {
            case 'blowfish':
                $cryptAlgo = '$2a$10$';
                break;

            default:
                return false;
        }

        $salt = $this->generateSalt(CRYPT_SALT_LENGTH);

        $cryptPass = crypt($password, $cryptAlgo . $salt);

        return $cryptPass;
    }

    /**
     * Generates a salt
     *
     * @param int $length The length of the salt
     *
     * @return string The generated salt
     */
    protected function generateSalt($length)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./';

        $salt = '';
        for($i = 0; $i <= $length; $i ++) {
            $chars = str_shuffle($chars);

            $salt.= $chars[rand(0,63)];
        }

        return $salt;
    }

    /**
     * Validates the provided password against the stored password
     *
     * @param string $inputPassword The provided password
     * @param string $storedPassword The stored password
     *
     * @return bool Whether the provided password is valid
     */
    protected function validatePassword($inputPassword, $storedPassword)
    {
        return (crypt($inputPassword, $storedPassword) == $storedPassword);
    }

    /**
     * Validates the provided login credentials and logs in the user
     *
     * @param string $username The provided username
     * @param string $password The stored password
     *
     * @return bool Whether the provided credentials are valid
     */
    function login($username, $password)
    {
        $andWhere = array();
        $andWhere[] = $this->table->where('lower(username) = ?', strtolower($username));
        $andWhere[] = $this->table->where('active = ?', 1);

        $recordset = $this->table->select('username, password',
                                          $this->table->andWhereArray($andWhere));

        if (!$recordset) return false;

        if ($this->validatePassword($password, $recordset[0]['password'])) {
            $user = $this->getUserByUsername($recordset[0]['username']);

            $_SESSION['MFW_authenticated_user'] = $user;

            return true;
        }

        return false;
    }

    /**
     * logs out the user
     *
     * @return void
     */
    function logout()
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) return;

        unset($_SESSION['MFW_authenticated_user']);
    }

    /**
     * Retrieves the current logged in user
     *
     * @return null|array Array of userdata or null if user is not logged in
     */
    function getAuthenticatedUser()
    {
        if (isset($_SESSION['MFW_authenticated_user'])) {
            return $_SESSION['MFW_authenticated_user'];
        }

        return null;
    }
}