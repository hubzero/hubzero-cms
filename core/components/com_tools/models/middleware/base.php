<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Models\Middleware;

use Components\Tools\Helpers\Utils;
use Hubzero\Base\Model;
use Lang;

/**
 * Abstract model class
 */
class Base extends Model
{
	/**
	 * Registry
	 *
	 * @var  object
	 */
	private $_config = null;

	/**
	 * Constructor
	 *
	 * @param   mixed  $oid  Integer (ID), string (alias), object or array
	 * @return  void
	 */
	public function __construct($oid=null)
	{
		$this->_db = Utils::getMWDBO();

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (!($this->_tbl instanceof \Hubzero\Database\Table))
			{
				$this->_logError(
					__CLASS__ . '::' . __FUNCTION__ . '(); ' . Lang::txt('Table class must be an instance of \\Hubzero\\Database\\Table.')
				);
				throw new \LogicException(Lang::txt('Table class must be an instance of \\Hubzero\\Database\\Table.'));
			}

			if (is_numeric($oid) || is_string($oid))
			{
				// Make sure $oid isn't empty
				// This saves a database call
				if ($oid)
				{
					$this->_tbl->load($oid);
				}
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
	}

	/**
	 * Get a config value
	 *
	 * @param   string  $key      Property to return
	 * @param   mixed   $default  Default value
	 * @return  mixed
	 */
	public function config($key='', $default=null)
	{
		if (!isset($this->_config))
		{
			$this->_config = \Component::params('com_tools');
		}

		if ($key)
		{
			return $this->_config->get((string) $key, $default);
		}
		return $this->_config;
	}
}
