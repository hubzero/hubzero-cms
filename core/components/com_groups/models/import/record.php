<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Import;

use Components\Groups\Models\Orm\Group;
use Hubzero\Utility\Validate;
use Exception;
use stdClass;
use Component;
use Request;
use Config;
use Lang;
use User;
use Date;

include_once dirname(__DIR__) . '/orm/group.php';
include_once dirname(__DIR__) . '/tags.php';

/**
 * Member Record importer
 */
class Record extends \Hubzero\Content\Import\Model\Record
{
	/**
	 * Profile
	 *
	 * @var  array
	 */
	private $_description = array();

	/**
	 * Handlers instances container
	 *
	 * @var  array
	 */
	protected static $handlers = array();

	/**
	 *  Constructor
	 *
	 * @param   mixes   $raw      Raw data
	 * @param   array   $options  Import options
	 * @param   string  $mode     Operation mode (update|patch)
	 * @return  void
	 */
	public function __construct($raw, $options = array(), $mode = 'UPDATE')
	{
		// store our incoming data
		$this->raw      = $raw;
		$this->_options = $options;
		$this->_mode    = strtoupper($mode);

		// Core objects
		$this->_description = array();

		// Create objects
		$this->record = new stdClass;
		$this->record->entry = Group::blank();

		// Messages
		$this->record->errors  = array();
		$this->record->notices = array();

		// bind data
		$this->bind();
	}

	/**
	 * Bind all raw data
	 *
	 * @return  $this  Current object
	 */
	public function bind()
	{
		// Wrap in try catch to avoid breaking in middle of import
		try
		{
			// Map profile data
			$this->_mapEntryData();

			// Map extras
			$this->_mapExtraData();
		}
		catch (Exception $e)
		{
			array_push($this->record->errors, $e->getMessage());
		}

		return $this;
	}

	/**
	 * Check Data integrity
	 *
	 * @return  $this  Current object
	 */
	public function check()
	{
		// Run save check method
		if (!$this->record->entry->validate())
		{
			array_push($this->record->errors, $this->record->entry->getErrors());
			return $this;
		}

		/*$keys = array();
		if ($this->_mode == 'PATCH')
		{
			foreach ($this->_description as $k => $p)
			{
				if (!$p)
				{
					continue;
				}

				if (is_array($p) && empty($p))
				{
					continue;
				}

				$keys[] = $k;
			}
		}

		// Validate profile data
		$f = \Components\Groups\Models\Description\Field::all()
			->including(['options', function ($option){
				$option
					->select('*');
			}])
			->where('action_edit', '!=', \Components\Groups\Models\Description\Field::STATE_HIDDEN);

		if (!empty($keys))
		{
			$f->whereIn('name', $keys);
		}

		$fields = $f
			->ordered()
			->rows();

		$form = new \Hubzero\Form\Form('groups', array('control' => 'group'));
		$form->load(\Components\Groups\Models\Description\Field::toXml($fields, 'edit'));
		$form->bind(new \Hubzero\Config\Registry($this->_description));

		if (!$form->validate($this->_description))
		{
			foreach ($form->getErrors() as $key => $error)
			{
				array_push($this->record->errors, (string)$error);
			}
		}*/

		return $this;
	}

	/**
	 * Store data
	 *
	 * @param   integer  $dryRun  Dry Run mode
	 * @return  $this    Current object
	 */
	public function store($dryRun = 1)
	{
		// Are we running in dry run mode?
		if ($dryRun || count($this->record->errors) > 0)
		{
			$entry = $this->record->entry->toArray();

			if (!isset($entry['gidNumber']))
			{
				$entry['gidNumber'] = 0;
			}

			$this->record->entry = new stdClass;

			foreach ($entry as $field => $value)
			{
				$this->record->entry->$field = $value;
			}
			foreach ($this->_description as $field => $value)
			{
				$this->record->entry->$field = $value;
			}

			return $this;
		}

		// Attempt to save all data
		// Wrap in try catch to avoid break mid import
		try
		{
			// Save profile
			$this->_saveEntryData();

			// Save extras
			$this->_saveExtraData();
		}
		catch (Exception $e)
		{
			array_push($this->record->errors, $e->getMessage());
		}

		$entry = $this->record->entry->toArray();

		$this->record->entry = new stdClass;

		foreach ($entry as $field => $value)
		{
			$this->record->entry->$field = $value;
		}

		//$this->record->entry = $this->record->entry->toObject();
		foreach ($this->_description as $field => $value)
		{
			$this->record->entry->$field = $value;
		}

		return $this;
	}

	/**
	 * Map raw data to profile object
	 *
	 * @return  void
	 */
	private function _mapEntryData()
	{
		// Do we have an ID?
		// Either passed in the raw data or gotten from the title match
		if (isset($this->raw->gidNumber) && $this->raw->gidNumber > 1)
		{
			$this->record->entry = Group::oneOrNew($this->raw->gidNumber);
		}
		else if (isset($this->raw->id) && $this->raw->id > 1)
		{
			$this->record->entry = Group::oneOrNew($this->raw->id);
		}
		else if (isset($this->raw->cn) && $this->raw->cn)
		{
			$entry = Group::oneByCn($this->raw->cn);
			if (!$entry)
			{
				$entry = Group::blank();
			}
			$this->record->entry = $entry;
		}

		$d = Date::of('now');

		if (isset($this->raw->created))
		{
			try
			{
				$d = Date::of($this->raw->created);
			}
			catch (Exception $e)
			{
				array_push($this->record->errors, $e->getMessage());
			}
			$this->raw->created = $d->toSql();
		}

		if (!$this->record->entry->get('gidNumber') && !isset($this->raw->created))
		{
			$this->raw->created = $d->toSql();
		}

		$columns = $this->record->entry->getStructure()->getTableColumns($this->record->entry->getTableName());

		$this->handlers();

		foreach (get_object_vars($this->raw) as $key => $val)
		{
			// These two need some extra loving and care, so we skip them for now...
			if (substr($key, 0, 1) == '_' || $key == 'cn' || $key == 'gidNumber' || $this->hasHandler($key))
			{
				continue;
			}

			if (function_exists('mb_convert_encoding'))
			{
				$val = mb_convert_encoding($val, 'UTF-8');
			}

			// In PATCH mode, skip fields with no values
			if ($this->_mode == 'PATCH' && !$val)
			{
				continue;
			}

			if (isset($columns[$key]))
			{
				$this->record->entry->set($key, $val);
			}
			else
			{
				$this->_description[$key] = $val;
			}
		}

		// Set multi-value fields
		//
		// This will split a string based on delimiter(s) and turn the 
		// values into an array.
		/*foreach (array('members', 'tags', 'projects') as $key)
		{
			if (isset($this->raw->$key))
			{
				// In PATCH mode, skip fields with no values
				if ($this->_mode == 'PATCH' && (!isset($this->_description[$key]) || !$this->_description[$key]))
				{
					continue;
				}

				$this->_description[$key] = $this->_multiValueField($this->_description[$key]);
			}
		}*/

		// If we're updating an existing record...
		if ($this->record->entry->get('gidNumber'))
		{
			// Check if the username passed if the same for the record we're updating
			$cn = $this->record->entry->get('cn');
			if ($cn && isset($this->raw->cn) && $cn != $this->raw->cn)
			{
				// Uh-oh. Notify the user.
				array_push($this->record->notices, Lang::txt('Group aliases (CNs) for existing groups cannot be changed at this time.'));
			}
		}
		else if (isset($this->raw->cn) && $this->raw->cn)
		{
			$this->record->entry->set('cn', $this->raw->cn);
		}

		if (isset($this->_options['approved']))
		{
			$this->record->entry->set('approved', (int)$this->_options['approved']);
		}
	}

	/**
	 * Split a string into multiple values based on delimiter(s)
	 *
	 * @param   mixed   $data   String or array of field values
	 * @param   string  $delim  List of delimiters, separated by a pipe "|"
	 * @return  array
	 */
	private function _multiValueField($data, $delim=',|;')
	{
		if (is_string($data))
		{
			$data = array_map('trim', preg_split("/($delim)/", $data));
			$data = array_values(array_filter($data));
		}

		return $data;
	}

	/**
	 * Save profile
	 *
	 * @return  void
	 */
	private function _saveEntryData()
	{
		$isNew = (!$this->record->entry->get('gidNumber'));

		if ($isNew)
		{
			if (!$this->record->entry->get('cn'))
			{
				$valid = false;

				// Try to create from name
				$cn = preg_replace('/[^a-z9-0_]/i', '', strtolower($this->record->entry->get('description')));
				if (Validate::group($cn))
				{
					if (!$this->_cnExists($cn))
					{
						$valid = true;
					}
				}

				if ($valid)
				{
					$this->record->entry->set('cn', $cn);
				}
			}

			$this->record->entry->set('type', 1);

			$d = Date::of('now');
			if ($this->raw->created)
			{
				try
				{
					$d = Date::of($this->raw->created);
				}
				catch (Exception $e)
				{
					array_push($this->record->errors, $e->getMessage());
				}
			}

			$this->record->entry->set('gidNumber', 0);
			$this->record->entry->set('created', $d->toSql());
		}

		if (!$this->record->entry->save())
		{
			throw new Exception(Lang::txt('Unable to save the entry data.'));
		}

		if (!empty($this->_description))
		{
			if (!$this->record->entry->saveDescription($this->_description))
			{
				throw new Exception($this->record->entry->getError());
			}
		}
	}

	/**
	 * Check if a username exists
	 *
	 * @return  integer
	 */
	private function _cnExists($cn)
	{
		return Group::oneByCn($cn)->get('gidNumber');
	}

	/**
	 * Map extra data
	 *
	 * @return  void
	 */
	private function _mapExtraData()
	{
		foreach ($this->handlers() as $handler)
		{
			$this->record = $handler->bind($this->raw, $this->record, $this->_mode);

			foreach ($handler->getErrors() as $error)
			{
				array_push($this->record->notices, $error);
			}
		}
	}

	/**
	 * Save extra data
	 *
	 * @return  void
	 */
	private function _saveExtraData()
	{
		foreach ($this->handlers() as $handler)
		{
			$this->record = $handler->store($this->raw, $this->record, $this->_mode);

			foreach ($handler->getErrors() as $error)
			{
				array_push($this->record->notices, $error);
			}
		}
	}

	/**
	 * Return a list of all available processors.
	 *
	 * @return  array
	 */
	public function handlers()
	{
		foreach (glob(__DIR__ . DIRECTORY_SEPARATOR . 'handler' . DIRECTORY_SEPARATOR . '*.php') as $path)
		{
			$type = basename($path, '.php');

			if (!isset(self::$handlers[$type]))
			{
				$class = __NAMESPACE__ . '\\Handler\\' . ucfirst($type);

				if (!class_exists($class))
				{
					include_once $path;
				}

				self::$handlers[$type] = new $class;
			}
		}

		return self::$handlers;
	}

	/**
	 * Is there a handler for this type?
	 *
	 * @param   string  $type
	 * @return  bool
	 */
	public function hasHandler($type)
	{
		return isset(self::$handlers[$type]);
	}
}
