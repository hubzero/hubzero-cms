<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
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
	const TITLE_MATCH = 10;

	public $raw;
	public $record;
	private $_mode;
	private $_options;
	private $_database;
	private $_user;

	/**
	 *  Constructor
	 *
	 * @param   mixes  $raw      Raw data
	 * @param   array  $options  Import options
	 * @return  void
	 */
	public function __construct($raw, $options = array(), $mode = 'UPDATE')
	{
		// store our incoming data
		$this->raw      = $raw;
		$this->_options = $options;
		$this->_mode    = $mode;

		// create core objects
		$this->_database = \JFactory::getDBO();
		$this->_user     = \JFactory::getUser();

		// create resource objects
		$this->record = new stdClass;

		// message bags for user
		$this->record->errors       = array();
		$this->record->notices      = array();

		// bind data
		$this->bind();
	}

	/**
	 * Bind all raw data
	 *
	 * @return  object  Current object
	 */
	public function bind()
	{
		// chainability
		return $this;
	}

	/**
	 * Check Data integrity
	 *
	 * @return  object  Current object
	 */
	public function check()
	{
		// chainability
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
		// are we running in dry run mode?
		if ($dryRun || count($this->record->errors) > 0)
		{
			return $this;
		}

		// chainability
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
		// reflect on class to get private or protected props
		$privateProperties = with(new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PRIVATE);

		// remove each private or protected prop
		foreach ($privateProperties as $prop)
		{
			$name = (string) $prop->name;
			unset($this->$name);
		}

		// output as json
		return json_encode($this);
	}
}