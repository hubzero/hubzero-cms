<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Content\Import\Model;

use Hubzero\Base\Object;
use Exception;
use stdClass;

/**
 * Import Record Model
 */
class Record extends Object
{
	/**
	 * Title match
	 *
	 * @var  integer
	 */
	const TITLE_MATCH = 10;

	/**
	 * Raw record data
	 *
	 * @var  object
	 */
	public $raw;

	/**
	 * Record data
	 *
	 * @var  object
	 */
	public $record;

	/**
	 * Operation mode (update|patch)
	 *
	 * @var  string
	 */
	protected $_mode;

	/**
	 * List of options to be applied to record
	 *
	 * @var  array
	 */
	protected $_options;

	/**
	 * JDatabase
	 *
	 * @var  object
	 */
	protected $_database;

	/**
	 * JUser
	 *
	 * @var  object
	 */
	protected $_user;

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
		// Store our incoming data
		$this->raw      = $raw;
		$this->_options = $options;
		$this->_mode    = strtoupper($mode);

		// Create core objects
		$this->_database = \JFactory::getDBO();
		$this->_user     = \JFactory::getUser();

		// Create objects
		$this->record = new stdClass;

		// Message bags for user
		$this->record->errors       = array();
		$this->record->notices      = array();

		// Bind data
		$this->bind();
	}

	/**
	 * Bind all raw data
	 *
	 * @return  object  Current object
	 */
	public function bind()
	{
		return $this;
	}

	/**
	 * Check Data integrity
	 *
	 * @return  object  Current object
	 */
	public function check()
	{
		return $this;
	}

	/**
	 * Store Data
	 *
	 * @param   integer  $dryRun  Dry Run mode
	 * @return  object   Current object
	 */
	public function store($dryRun = 1)
	{
		// Are we running in dry run mode?
		if ($dryRun || count($this->record->errors) > 0)
		{
			return $this;
		}

		return $this;
	}

	/**
	 * Output object of string
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * To String object
	 *
	 * Removes private properties before returning
	 *
	 * @return  string
	 */
	public function toString()
	{
		// Reflect on class to get private or protected props
		$privateProperties = with(new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PROTECTED);

		// Remove each private or protected prop
		foreach ($privateProperties as $prop)
		{
			$name = (string) $prop->name;
			unset($this->$name);
		}

		// Output as json
		return json_encode($this);
	}
}