<?php
/**
 * Makes a PDO connection to the database
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Db
 * @subpackage Connection
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * Makes a PDO connection to the database
 *
 * @category   MicroFramework
 * @package    Db
 * @subpackage Connection
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Db_Connection
{
    /**
     * @var string The database engine
     */
    protected $engine;

    /**
     * @var string The database name
     */
    protected $databaseName;

    /**
     * @var string The database host
     */
    protected $host;

    /**
     * @var int The database port
     */
    protected $port;

    /**
     * @var string The database username
     */
    protected $username;

    /**
     * @var string The database password
     */
    protected $password;

    /**
     * @var PDO The connection
     */
    protected $db;

    /**
     * Create connection instance and
     *
     * @todo Need to find out whether it will be better suited to use dependency injection here
     *
     * @return void
     */
    public function __construct()
    {
        $this->setEngine(MFW_DB_ENGINE);
        $this->setDatabasename(MFW_DB_NAME);
        $this->setHost(MFW_DB_HOST);
        $this->setPort(MFW_DB_PORT);
        $this->setUsername(MFW_DB_USERNAME);
        $this->setPassword(MFW_DB_PASSWORD);
    }

    /**
     * Set the database engine
     *
     * @param string $engine The engine
     *
     * @throws OverflowException If the database engine isn't installed on the system
     * @return void
     */
    protected function setEngine($engine)
    {
        if (!in_array($engine, PDO::getAvailableDrivers())) {
            throw new OverflowException('Database engine ('.$engine.') is not installed!');
        }

        $this->engine = $engine;
    }

    /**
     * Get the database engine
     *
     * @return string The database engine
     */
    protected function getEngine()
    {
        return $this->engine;
    }

    /**
     * Set the database name
     *
     * @param string $databaseName The database name
     *
     * @return void
     */
    protected function setDatabaseName($databaseName)
    {
        $this->databaseName = $databaseName;
    }

    /**
     * Get the database name
     *
     * @return string The database name
     */
    protected function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * Set the database host
     *
     * @param string $host The host
     *
     * @return void
     */
    protected function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Get the database host
     * Return the ip address rather than the localhost name to make sure the port works
     *
     * @return string The database host
     */
    protected function getHost()
    {
        if ($this->host == 'localhost') {
            return '127.0.0.1';
        }

        return $this->host;
    }

    /**
     * Set the database port
     *
     * @param string $port The port
     *
     * @return void
     */
    protected function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * Get the database port
     *
     * @return string The database port
     */
    protected function getPort()
    {
        return $this->port;
    }

    /**
     * Set the database username
     *
     * @param string $username The username
     *
     * @return void
     */
    protected function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get the database username
     *
     * @return string The username
     */
    protected function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the database password
     *
     * @param string $password The password
     *
     * @return void
     */
    protected function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get the database password
     *
     * @return void
     */
    protected function getPassword()
    {
        return $this->password;
    }

    /**
     * Connect to the database
     * We need to try to make a connection in a try/catch block or the database credentials
     * may be displayed in plain text
     *
     * @throws PDOException If the connection cannot be established
     * @return void
     */
    public function connect()
    {
        try {
            $this->db = new PDO($this->buildConnectString(),
                                $this->getUsername(),
                                $this->getPassword()
                                );
        } catch (PDOException $e) {
            throw new PDOException($e);
        }

        if (MFW_ENV_MODE === MFW_ENV_DEBUG) {
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        }
    }

    /**
     * Build the connection string
     *
     * @return string The connection string
     */
    protected function buildConnectString()
    {
        $connectString = '';
        $connectString.= $this->getEngine();
        $connectString.= ':host=' . $this->getHost() . ';';
        if ($this->getPort() !== null) {
            $connectionString.= 'port=' . $this->getPort() . ';';
        }
        $connectString.= 'dbname=' . $this->getDatabaseName();

        return $connectString;
    }
}