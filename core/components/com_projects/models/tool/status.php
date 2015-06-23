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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Projects\Models\Tool;

use Hubzero\Base\Model;
use Components\Projects\Tables;

/**
 * Project Tool Status model
 */
class Status extends Model
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Projects\\Tables\\ToolStatus';

	/**
	 * Registry
	 *
	 * @var object
	 */
	public $config = NULL;

	/**
	 * Constructor
	 *
	 * @return     void
	 */
	public function __construct($oid = NULL)
	{
		$this->_db = \JFactory::getDBO();

		if (!isset($this->_tbl))
		{
			$this->_tbl = new Tables\ToolStatus($this->_db);
		}
		if (!isset($this->_statuses))
		{
			$this->_statuses = array();
			$statuses = $this->_tbl->getItems();
			foreach ($statuses as $status)
			{
				$this->_statuses[$status->id] = $status;
			}
		}

		if (is_numeric($oid))
		{
			if (isset($this->_statuses[$oid]))
			{
				$this->_tbl->bind($this->_statuses[$oid]);
			}
			else
			{
				$this->_tbl->load($oid);
			}
		}
	}

	/**
	 * Returns a reference to the model
	 *
	 * @param      mixed $oid status ID
	 * @return     object Todo
	 */
	public function getStatus($oid=null)
	{
		if (isset($this->_statuses[$oid]))
		{
			$this->_tbl->bind($this->_statuses[$oid]);
		}
	}

	/**
	 * Returns a reference to the model
	 *
	 * @param      mixed $oid status ID
	 * @return     object Todo
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_object($oid))
		{
			$key = $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $oid['id'];
		}
		else
		{
			$key = $oid;
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid);
		}

		return $instances[$key];
	}
}