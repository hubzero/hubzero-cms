<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base;

/**
 * Abstract model class
 */
abstract class Model extends Obj
{
	/**
	 * Unpublished state
	 *
	 * @var  integer
	 */
	const APP_STATE_UNPUBLISHED = 0;

	/**
	 * Published state
	 *
	 * @var  integer
	 */
	const APP_STATE_PUBLISHED   = 1;

	/**
	 * Deleted state
	 *
	 * @var  integer
	 */
	const APP_STATE_DELETED     = 2;

	/**
	 * Flagged state
	 *
	 * @var  integer
	 */
	const APP_STATE_FLAGGED     = 3;

	/**
	 * Table class name
	 *
	 * @var  string
	 */
	protected $_tbl_name = null;

	/**
	 * Table
	 *
	 * @var object
	 */
	protected $_tbl = null;

	/**
	 * Database
	 *
	 * @var  object
	 */
	protected $_db = null;

	/**
	 * Model context.
	 * option.model(.content)
	 *
	 * @var  string
	 */
	protected $_context = null;

	/**
	 * Constructor
	 *
	 * @param   mixed  $oid  Integer (ID), string (alias), object or array
	 * @return  void
	 */
	public function __construct($oid=null)
	{
		$this->_db = $this->initDbo();

		if ($this->_tbl_name)
		{
			$cls = $this->_tbl_name;
			$this->_tbl = new $cls($this->_db);

			if (!($this->_tbl instanceof \JTable) && !($this->_tbl instanceof \Hubzero\Database\Table))
			{
				$this->_logError(
					__CLASS__ . '::' . __FUNCTION__ . '(); ' . \Lang::txt('Table class must be an instance of JTable.')
				);
				throw new \LogicException(\Lang::txt('Table class must be an instance of JTable.'));
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
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $property  The name of the property
	 * @param   mixed   $default   The default value
	 * @return  mixed   The value of the property
  */
	public function get($property, $default=null)
	{
		if (isset($this->_tbl->$property))
		{
			return $this->_tbl->$property;
		}
		else if (isset($this->_tbl->{'__' . $property}))
		{
			return $this->_tbl->{'__' . $property};
		}
		return $default;
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param   string  $property  The name of the property
	 * @param   mixed   $value     The value of the property to set
	 * @return  object  This current model
	 */
	public function set($property, $value = null)
	{
		if (!array_key_exists($property, $this->_tbl->getProperties()))
		{
			$property = '__' . $property;
		}
		$this->_tbl->$property = $value;
		return $this;
	}

	/**
	 * Method to get the database connection.
	 *
	 * If detected that the code is being run in a super group
	 * component, it will return the super group DB connection
	 * instead of the site connection.
	 *
	 * @return  object  Database
	 */
	public function initDbo()
	{
		if (defined('JPATH_GROUPCOMPONENT'))
		{
			$r = new \ReflectionClass($this);
			if (substr($r->getFileName(), 0, strlen(JPATH_GROUPCOMPONENT)) == JPATH_GROUPCOMPONENT)
			{
				return \Hubzero\User\Group\Helper::getDbo();
			}
		}
		return \App::get('db');
	}

	/**
	 * Method to get the Database connector object.
	 *
	 * @return  object  The internal database connector object.
	 */
	public function getDbo()
	{
		return $this->_db;
	}

	/**
	 * Method to set the database connector object.
	 *
	 * @param   object   &$db  A database connector object to be used by the table object.
	 * @return  boolean  True on success.
	 */
	public function setDbo(&$db)
	{
		if (!($db instanceof \Hubzero\Database\Driver))
		{
			return false;
		}

		$this->_db = $db;
		$this->_tbl->setDBO($this->_db);

		return true;
	}

	/**
	 * Check if the entry exists (i.e., has a database record)
	 *
	 * @return  boolean  True if record exists, False if not
	 */
	public function exists()
	{
		if (!array_key_exists('id', $this->_tbl->getFields()))
		{
			return true;
		}
		if ($this->get('id') && (int) $this->get('id') > 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Has the offering started?
	 *
	 * @return  boolean
	 */
	public function isPublished()
	{
		if (!array_key_exists('state', $this->_tbl->getFields()))
		{
			return true;
		}
		if ($this->get('state') == self::APP_STATE_PUBLISHED)
		{
			return true;
		}
		return false;
	}

	/**
	 * Has the offering started?
	 *
	 * @return  boolean
	 */
	public function isUnpublished()
	{
		if (!array_key_exists('state', $this->_tbl->getFields()))
		{
			return false;
		}
		if ($this->get('state') == self::APP_STATE_UNPUBLISHED)
		{
			return true;
		}
		return false;
	}

	/**
	 * Has the offering started?
	 *
	 * @return  boolean
	 */
	public function isDeleted()
	{
		if (!array_key_exists('state', $this->_tbl->getFields()))
		{
			return false;
		}
		if ($this->get('state') == self::APP_STATE_DELETED)
		{
			return true;
		}
		return false;
	}

	/**
	 * Bind data to the model
	 *
	 * @param   mixed    $data  Object or array
	 * @return  boolean  True on success, False on error
	 */
	public function bind($data=null)
	{
		if (is_object($data))
		{
			$res = $this->_tbl->bind($data);

			if ($res)
			{
				$properties = $this->_tbl->getProperties();
				foreach (get_object_vars($data) as $key => $property)
				{
					if (!array_key_exists($key, $properties))
					{
						$this->_tbl->set('__' . $key, $property);
					}
				}
			}
		}
		else if (is_array($data))
		{
			$res = $this->_tbl->bind($data);

			if ($res)
			{
				$properties = $this->_tbl->getProperties();
				foreach (array_keys($data) as $key)
				{
					if (!array_key_exists($key, $properties))
					{
						$this->_tbl->set('__' . $key, $data[$key]);
					}
				}
			}
		}
		else
		{
			$this->_logError(
				__CLASS__ . '::' . __FUNCTION__ . '(); ' . \Lang::txt('Data must be of type object or array. Type given was %s', gettype($data))
			);
			throw new \InvalidArgumentException(\Lang::txt('Data must be of type object or array. Type given was %s', gettype($data)));
		}

		return $res;
	}

	/**
	 * Log an error message
	 *
	 * @param   string  $message  Message to log
	 * @return  void
	 */
	protected function _logError($message)
	{
		return $this->_log('error', $message);
	}

	/**
	 * Log an error message
	 *
	 * @param   string  $message  Message to log
	 * @return  void
	 */
	protected function _logDebug($message)
	{
		return $this->_log('debug', $message);
	}

	/**
	 * Log an error message
	 *
	 * @param   string  $message  Message type to log
	 * @param   string  $message  Message to log
	 * @return  void
	 */
	protected function _log($type='error', $message)
	{
		if (!$message)
		{
			return;
		}

		if (\App::get('config')->get('debug'))
		{
			$message = '[' . \App::get('request')->getVar('REQUEST_URI', '', 'server') . '] [' . $message . ']';
		}

		$type = strtolower($type);
		if (!in_array($type, array('error', 'debug', 'critical', 'warning', 'notice', 'alert', 'emergency', 'info')))
		{
			return;
		}

		$logger = \Log::getRoot();
		$logger->$type($message);
	}

	/**
	 * Perform data validation
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function check()
	{
		// Is data valid?
		if (!$this->_tbl->check())
		{
			$this->_errors = $this->_tbl->getErrors();
			return false;
		}
		return true;
	}

	/**
	 * Store changes to this database entry
	 *
	 * @param   boolean  $check  Perform data validation check?
	 * @return  boolean  False if error, True on success
	 */
	public function store($check=true)
	{
		// Validate data?
		if ($check)
		{
			// Is data valid?
			if (!$this->check())
			{
				return false;
			}

			if ($this->_context)
			{
				$results = \Event::trigger('content.onContentBeforeSave', array(
					$this->_context,
					&$this,
					$this->exists()
				));
				foreach ($results as $result)
				{
					if ($result === false)
					{
						$this->setError(\App::get('language')->txt('Content failed validation.'));
						return false;
					}
				}
			}
		}

		// Attempt to store data
		if (!$this->_tbl->store())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		return true;
	}

	/**
	 * Delete a record
	 *
	 * @return  boolean  True on success, false on error
	 */
	public function delete()
	{
		// Can't delete what doesn't exist
		if (!$this->exists())
		{
			return true;
		}

		// Remove record from the database
		if (!$this->_tbl->delete())
		{
			$this->setError($this->_tbl->getError());
			return false;
		}

		// Hey, no errors!
		return true;
	}

	/**
	 * Import a set of plugins
	 *
	 * @return  object
	 */
	public function importPlugin($type='')
	{
		\Plugin::import($type);

		return $this;
	}

	/**
	 * Import a set of plugins
	 *
	 * @return  object
	 */
	public function trigger($event='', $params=array())
	{
		return \Event::trigger($event, $params);
	}

	/**
	 * Turn the object into a string
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Turn the object into a string
	 *
	 * @return  string
	 */
	public function toString($ignore=array('_db'))
	{
		return $this->_print_r($this, $ignore);
	}


	/**
	 * Special print_r to strip out any vars passed in $ignore
	 *
	 * @param   object   $subject   Object to print_r
	 * @param   array    $ignore    Property names to ignore
	 * @param   integer  $depth     Recursion depth
	 * @param   array    $refChain  Reference chain
	 * @return  string
	 */
	private function _print_r($subject, $ignore = array(), $depth = 1, $refChain = array())
	{
		$str = '';

		if ($depth > 20)
		{
			return $str;
		}

		if (is_object($subject))
		{
			foreach ($refChain as $refVal)
			{
				if ($refVal === $subject)
				{
					$str .= "*RECURSION*\n";
					return $str;
				}
			}

			array_push($refChain, $subject);

			$str .= get_class($subject) . " Object ( \n";
			$subject = (array) $subject;
			foreach ($subject as $key => $val)
			{
				if (is_array($ignore) && !in_array($key, $ignore, 1))
				{
					if ($key{0} == "\0")
					{
						$keyParts = explode("\0", $key);
						if (is_array($ignore) && in_array($keyParts[2], $ignore, 1))
						{
							continue;
						}
						$str .= str_repeat(" ", $depth * 4) . '[';
						$str .= $keyParts[2] . (($keyParts[1] == '*')  ? ':protected' : ':private');
					}
					else
					{
						$str .= str_repeat(" ", $depth * 4) . '[';
						$str .= $key;
					}
					$str .= '] => ';
					$str .= $this->_print_r($val, $ignore, $depth + 1, $refChain);
				}
			}
			$str .= str_repeat(" ", ($depth - 1) * 4) . ")\n";

			array_pop($refChain);
		}
		elseif (is_array($subject))
		{
			$str .= "Array ( \n";
			foreach ($subject as $key => $val)
			{
				if (is_array($ignore) && !in_array($key, $ignore, 1))
				{
					$str .= str_repeat(" ", $depth * 4) . '[' . $key . '] => ';
					$str .= $this->_print_r($val, $ignore, $depth + 1, $refChain);
				}
			}
			$str .= str_repeat(" ", ($depth - 1) * 4) . ")\n";
		}
		else
		{
			$str .= $subject . "\n";
		}
		return $str;
	}

	/**
	 * Method to return model values in array format
	 *
	 * @param  boolean $verbose Include prefixed "__" vars in output
	 * @return array            Array of model values.
	 */
	public function toArray($verbose = false)
	{
		$output = $this->_tbl->getProperties();

		if ($verbose)
		{
			foreach ($this->_tbl as $key => $value)
			{
				if (substr($key, 0, 2) == '__')
				{
					$output[substr($key, 2)] = $value;
				}
			}
		}

		return $output;
	}

	/**
	 * Dynamically handle error additions.
	 *
	 * @param   string  $method
	 * @param   array   $parameters
	 * @return  mixed
	 */
	public function __call($method, $parameters)
	{
		throw new \BadMethodCallException(sprintf(__CLASS__ . '; Method [%s] does not exist.', $method));
	}
}
