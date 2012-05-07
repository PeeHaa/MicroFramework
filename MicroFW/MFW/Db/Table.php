<?php
/**
 * Retrieves and mutates data of a database table
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Db
 * @subpackage Table
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * Retrieves and mutates data of a database table
 *
 * @todo setup a table interface
 *
 * @category   MicroFramework
 * @package    Db
 * @subpackage Table
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Db_Table
{
    /**
     * @var MFW_Db_Connection The database connection
     */
    protected $connection;

    /**
     * @var string The name of the table
     */
    protected $table;

    /**
     * @var null|string The sequence of the table if any
     */
    protected $sequence = null;

    /**
     * @var array The errors of the database actions
     */
    protected $errors = array();

    /**
     * Create table instance
     *
     * @param MFW_Db_Connection $connection The database connection
     * @param string $table The name of the table
     * @param string $sequence The name of the sequence
     *
     * @return void
     */
    public function __construct(MFW_Db_Connection $connection, $table = null, $sequence = null)
    {
        $this->setConnection($connection);

        $this->setTable($table);
        $this->setSequence($sequence);
    }

    /**
     * Set the database connection
     *
     * @param MFW_Db_Connection $connection The database connection
     *
     * @return void
     */
    protected function setConnection(MFW_Db_Connection $connection)
    {
        $this->connection = $connection->getConnection();
    }

    /**
     * Get the database connection
     *
     * @return MFW_Db_Connection The database connection
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set the table name
     *
     * @param string $table The name of the table
     *
     * @return void
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * Get the table name
     *
     * @return string The name of the table
     */
    protected function getTable()
    {
        return $this->table;
    }

    /**
     * Set the table sequence
     *
     * @param string $sequence The sequence of the table
     *
     * @return void
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     * Get the table sequence
     *
     * @return string The sequence of the table
     */
    protected function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Add an error to the error stack
     *
     * @param string $errorMessage The error message
     * @param string $sql The sql string
     * @param array $params The parameters used in the query
     *
     * @return void
     */
    protected function addError($errorMessage, $sql, $params = array())
    {
        $this->errors[] = array($errorMessage, $sql);
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
     * Get the last error
     *
     * @return array The last error
     */
    public function getLastError()
    {
        return end($this->errors);
    }

    /**
     * Execute a select query
     *
     * @param string $columns The fields to retrieve
     * @param null|array $where The where clause
     * @param null|string $orderby The orderby clause
     * @param null|string $groupby The groupby clause
     * @param null|int $offset The offset
     * @param null|int $limit The limit
     * @param false|string $having The having clause
     *
     * @return array The recordset
     */
    public function select($columns, $where = null, $orderby = null, $groupby = null, $offset = null, $limit = null, $having = False)
    {
        $queryOptions = $this->buildSelectQueryOptions($orderby, $groupby, $offset, $limit, $having);

        $whereSql = '';
        if ($where !== null) {
            $whereSql = ' WHERE ' . $where[0];
        }

        $sql = 'SELECT ' . $columns . ' FROM ' . $this->getTable() . $whereSql . $queryOptions;

        try {
            $query = $this->getConnection()->prepare($sql);

            if ($where === null) {
                $query->execute();
            } else {
                $query->execute($where[1]);
            }
        } catch(PDOException $e) {
            $this->addError($e->getMessage(), $sql);
        }

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Build the SQL string using the options
     *
     * @param null|string $orderby The orderby clause
     * @param null|string $groupby The groupby clause
     * @param null|int $offset The offset
     * @param null|int $limit The limit
     * @param false|string $having The having clause
     *
     * @return string The SQL string of the option
     */
    protected function buildSelectQueryOptions($orderby = null, $groupby = null, $offset = null, $limit = null, $having = False)
    {
        $query = '';

        if ($groupby !== null) {
            $query.= ' GROUP BY '.$groupby;
        }

        if ($having !== False) {
            $query.= ' HAVING '.$having;
        }

        if ($orderby !== null) {
            $query.= ' ORDER BY '.$orderby;
        }

        if ($limit !== null) {
            $query.= ' LIMIT '.$limit;
        }

        if ($offset !== null) {
            $query.= ' OFFSET '.$offset;
        }

        return $query;
    }

    /**
     * Parse where condition into sql
     *
     * @param string $sql The sql statement
     * @param mixed $params The paramaters
     *
     * @return array The where statement
     */
    public function where($sql, $params = array())
    {
        if (!is_array($params)) {
            $params = array($params);
        }

        if (empty($params)) {
            if (strpos($sql, '?') !== False) {
                throw new UnderflowException('Missing parameters for query `' . $sql . '`.');
            }
        } else {
            $placeholders = implode(", ", array_fill(0, count($params), "?"));
            $sql = str_replace("??", $placeholders, $sql);
        }

        return array($sql, $params);
    }

    /**
     * Parse an array of where conditions into sql string
     *
     * @param array $wherearray The where statements
     * @param string $combine The 'glue' used to merge the statements (e.g. AND / OR)
     *
     * @return array The where statements
     */
    protected function whereArray(array $wherearray, $combine)
    {
        if (empty($wherearray)) {
            return array('', array());
        }

        $whereparams = array();
        foreach($wherearray as $index=>$whereentry) {
            if (is_array($whereentry) && count($whereentry==2)) {
                $wherearray[$index] = $whereentry[0];
                $whereparams = array_merge($whereparams, $whereentry[1]);
            }
        }

        if (count($wherearray) == 1) {
            return array($wherearray[$index], $whereparams);
        }

        return array('('.implode(' '.$combine.' ', $wherearray).')', $whereparams);
    }

    /**
     * Parse AND where conditions into sql
     *
     * @param array $statements The statements
     *
     * @return array The statements
     */
    public function andWhere($statements)
    {
        $wherearray = func_get_args();

        return $this->andWhereArray($wherearray);
    }

    /**
     * Parse where condition into sql using AND
     *
     * @param array $wherearray The statements
     *
     * @return array The statements
     */
    public function andWhereArray($wherearray)
    {
        return $this->whereArray($wherearray, 'AND');
    }

    /**
     * Parse where condition into sql using AND
     *
     * @param array $wherearray The statements
     *
     * @return array The statements
     */
    public function orWhere($statements)
    {
        $wherearray = func_get_args();

        return $this->orWhereArray($wherearray);
    }

    /**
     * Parse OR where conditions into sql
     *
     * @param array $wherearray The statements
     *
     * @return array The statements
     */
    public function orWhereArray($wherearray)
    {
        return $this->whereArray($wherearray, 'OR');
    }

    /**
     * Insert data into the table
     *
     * @param array $data The data to insert
     *
     * @return mixed|boolean If sequence if available will return last inserted id or true on success
     */
    public function insert(array $data)
    {
        $sql = 'INSERT INTO '.$this->getTable();
        $sql.= ' ('.implode(', ', array_keys($data)).')';
        $sql.= ' VALUES';
        $sql.= ' ('.implode(', ', array_fill(0, count($data), '?')).')';

        $params = array_values($data);
        foreach($params as $index=>$param) {
            if ($param === '') {
                $params[$index] = NULL;
            }
        }

        try {
            $query = $this->getConnection()->prepare($sql);
            $result = $query->execute($params);
            if ($this->getSequence() !== null) {
                return $this->getConnection()->lastInsertId($this->sequence);
            }
        } catch(PDOException $e) {
            $this->addError($e->getMessage(), $sql, $params);

            return False;
        }

        return True;
    }

    /**
     * Update records
     *
     * @param array $data The data to update
     * @param mixed $where Teh where clause
     *
     * @return boolean True on success
     */
    public function update($data, $where = null)
    {
        $sql = 'UPDATE '.$this->getTable();
        list($set, $params) = $this->parseUpdateData($data);
        $sql.= ' SET '.$set;

        foreach($params as $index=>$param) {
            if ($param === '') {
                $params[$index] = NULL;
            }
        }

        if ($where !== null) {
            list($wheresql, $whereparams) = $where;
            $sql.= ' WHERE '.$wheresql.N;
            $params = array_merge($params, $whereparams);
        }

        try {
            $query = $this->getConnection()->prepare($sql);
            $query->execute($params);
        }
        catch(PDOException $e) {
            $this->addError($e->getMessage(), $sql, $params);

            return False;
        }
        return True;
    }

    /**
     * Parse the data into sql format
     *
     * @param array $data The data to update
     *
     * @return array The data to be updated
     */
    protected function parseUpdateData($data)
    {
        $set = '';
        $params = array();
        $comma = '';
        foreach($data as $field => $value) {
            $set.= $comma.$field.' = ?';
            $params[] = $value;

            $comma = ', ';
        }

        return array($set, $params);
    }

    /**
     * Delete records
     *
     * @param mixed $where The where clause
     *
     * @return boolean True on success
     */
    public function delete($where)
    {
        $sql = 'DELETE FROM '.$this->getTable();
        $params = array();
        if ($where) {
            list($wheresql, $whereparams) = $where;

            $sql.= ' WHERE '.$wheresql.N;
            $params = array_merge($params, $whereparams);
        }

        try {
            $query = $this->getConnection()->prepare($sql);
            $result = $query->execute($params);
        }
        catch(PDOException $e) {
            $this->addError($e->getMessage(), $sql, $params);

            return False;
        }

        return True;
    }

    /**
     * Delete all records from table
     *
     * @return boolean True on success
     */
    public function deleteAll()
    {
        $sql = 'DELETE FROM '.$this->getConnection().N;

        try {
            $query = $this->getConnection()->query($sql);
        } catch(PDOException $e) {
            $this->addError($e->getMessage(), $sql);

            return False;
        }

        return True;
    }

    /**
     * Truncate the table
     *
     * @return boolean True on success
     */
    public function truncate()
    {
        $sql = 'TRUNCATE TABLE '.$this->getTable().N;

        try {
            $query = $this->db->query($sql);
        } catch(PDOException $e) {
            $this->addError($e->getMessage(), $sql);

            return False;
        }

        return True;
    }
}