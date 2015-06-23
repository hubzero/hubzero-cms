<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
		$this->_db = \JFactory::getDBO();
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