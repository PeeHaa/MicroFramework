<?php
/**
 * Date class
 *
 * PHP version 5.3
 *
 * @category   MicroFramework
 * @package    Date
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version    1.0.0
 */

/**
 * Date class
 *
 * @category   MicroFramework
 * @package    Date
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class MFW_Date
{
    /**
     * Parse an SQL timestamp
     *
     * @param string The SQL timestamp
     * @param string The format to return the date in
     *
     * @return string The formatted date
     */
    public static function parseSqlTimestamp($timestamp, $format = 'd-m-Y')
    {
        $date = new DateTime($timestamp);

        return $date->format($format);
    }
}