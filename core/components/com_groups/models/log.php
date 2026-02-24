<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models;

use Hubzero\Base\Model;
use Date;
use User;

/**
 * Group log model class
 *
 * @phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore
 */
class Log extends Model
{
    /**
     * Table object
     *
     * @var object
     */
    protected $_tbl = null;

    /**
     * Table name
     *
     * @var string
     */
    protected $_tbl_name = '\\Components\\Groups\\Tables\\Log';

    /**
     * Constructor
     *
     * @param      mixed $oid Object Id
     * @return     void
     */
    public function __construct($oid = null)
    {
        // create database object
        $this->_db = \App::get('db');

        // create page cateogry table object
        $this->_tbl = new $this->_tbl_name($this->_db);

        // load object
        if (is_numeric($oid)) {
            $this->_tbl->load($oid);
        } elseif (is_object($oid) || is_array($oid)) {
            $this->bind($oid);
        }
    }

    /**
     * Returns array of log defaults
     *
     * @return    array
     */
    protected static function logDefaults()
    {
        return array(
            'gidNumber' => null,
            'timestamp' => Date::toSql(),
            'userid'    => User::get('id'),
            'action'    => '',
            'comments'  => '',
            'actorid'   => User::get('id')
        );
    }

    /**
     * Log a Group action
     *
     * @param   array  $options
     * @return  mixed
     */
    public static function log(array $options = null)
    {
        $instance = new self();

        // merge defaults with passed in options
        $details = array_merge(self::logDefaults(), $options);

        // if we passed in a string lets normalize to array
        if (is_string($details['comments'])) {
            $details['comments'] = array('message' => $details['comments']);
        }

        // json encode comments
        $details['comments'] = json_encode($details['comments']);

        // bind log details
        $instance->bind($details);

        // store log details
        if (!$instance->store(true)) {
            return $instance->getError();
        }

        return $instance;
    }
}
