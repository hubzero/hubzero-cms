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

// include needed tables
require_once dirname(__DIR__) . DS . 'tables' . DS . 'log.php';

/**
 * Group log model class
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
		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_object($oid) || is_array($oid))
		{
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
	 * @return  object
	 */
	private function log(array $options = null)
	{
		// merge defaults with passed in options
		$details = array_merge(self::logDefaults(), $options);

		// if we passed in a string lets normalize to array
		if (is_string($details['comments']))
		{
			$details['comments'] = array('message' => $details['comments']);
		}

		// json encode comments
		$details['comments'] = json_encode($details['comments']);

		// bind log details
		$this->bind($details);

		// store log details
		if (!$this->store(true))
		{
			return $this->getError();
		}

		return $this;
	}

	/**
	 * Overloading Static Method Call
	 *
	 * Resolves instance of log model and runs method on instance with args
	 *
	 * @param    string $method  Static method name
	 * @param    array  $args    Method args passed
	 * @return   mixed
	 */
	public static function __callStatic($method, $args)
	{
		// resolve instance
		$instance = new self();

		// run method on instance
		switch (count($args))
		{
			case 0:
				return $instance->$method();
			case 1:
				return $instance->$method($args[0]);
			case 2:
				return $instance->$method($args[0], $args[1]);
			case 3:
				return $instance->$method($args[0], $args[1], $args[2]);
			case 4:
				return $instance->$method($args[0], $args[1], $args[2], $args[3]);
			default:
				return call_user_func_array(array($instance, $method), $args);
		}
	}
}
