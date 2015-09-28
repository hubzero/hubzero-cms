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
 * @package   framework
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	 * Database
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
		$this->_database = \App::get('db');
		$this->_user     = \User::getRoot();

		// Create objects
		$this->record = new stdClass;

		// Message bags for user
		$this->record->errors  = array();
		$this->record->notices = array();

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