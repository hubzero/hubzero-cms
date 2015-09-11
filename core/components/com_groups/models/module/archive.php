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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Module;

use Components\Groups\Tables;
use Components\Groups\Models\Module;
use Hubzero\Base\Object;
use Hubzero\Base\Model\ItemList;

// include needed models
require_once dirname(__DIR__) . DS . 'module.php';
require_once __DIR__ . DS . 'menu.php';

/**
 * Group module archive model class
 */
class Archive extends Object
{
	/**
	 * \Hubzero\Base\Model
	 *
	 * @var object
	 */
	private $_modules = null;

	/**
	 * Modules count
	 *
	 * @var integer
	 */
	private $_modules_count = null;

	/**
	 * Database
	 *
	 * @var object
	 */
	private $_db = NULL;

	/**
	 * Registry
	 *
	 * @var object
	 */
	private $_config;

	/**
	 * Constructor
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->_db = \App::get('db');
	}

	/**
	 * Get Instance of Module Archive
	 *
	 * @param   string $key Instance Key
	 * @return  object \Components\Groups\Models\Module\Archive
	 */
	static function &getInstance($key=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self();
		}

		return $instances[$key];
	}

	/**
	 * Get a list of group modules
	 *
	 * @param   string  $rtrn    What data to return
	 * @param   array   $filters Filters to apply to data retrieval
	 * @param   boolean $boolean Clear cached data?
	 * @return  mixed
	 */
	public function modules($rtrn = 'list', $filters = array(), $clear = false)
	{
		switch (strtolower($rtrn))
		{
			case 'unapproved':
				$unapproved = array();
				if ($results = $this->modules('list', $filters, true))
				{
					foreach ($results as $k => $result)
					{
						// if module is unapproved return it
						if ($result->get('approved') == 0)
						{
							$unapproved[] = $result;
						}
					}
				}
				return new ItemList($unapproved);
			break;
			case 'list':
			default:
				$tbl = new Tables\Module($this->_db);
				if ($results = $tbl->find( $filters ))
				{
					foreach ($results as $key => $result)
					{
						$results[$key] = new Module($result);
					}
				}
				$this->_modules = new ItemList($results);
				return $this->_modules;
			break;
		}
	}
}