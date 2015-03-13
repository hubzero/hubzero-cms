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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Forum\Models;

use LogicException;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'attachment.php');
require_once(__DIR__ . DS . 'base.php');

/**
 * Model class for a forum post attachment
 */
class Attachment extends Base
{
	/**
	 * Table class name
	 *
	 * @var  object
	 */
	protected $_tbl_name = '\\Components\\Forum\\Tables\\Attachment';

	/**
	 * Constructor
	 *
	 * @param   mixed    $oid  ID (integer), alias (string), array or object
	 * @param   integer  $pid  Post ID
	 * @return  void
	 */
	public function __construct($oid=null, $pid=null)
	{
		$this->_db = \JFactory::getDBO();

		$cls = $this->_tbl_name;
		$this->_tbl = new $cls($this->_db);

		if (!($this->_tbl instanceof \JTable))
		{
			$this->_logError(
				__CLASS__ . '::' . __FUNCTION__ . '(); ' . Lang::txt('Table class must be an instance of JTable.')
			);
			throw new LogicException(Lang::txt('Table class must be an instance of JTable.'));
		}

		if ($oid)
		{
			if (is_numeric($oid))
			{
				$this->_tbl->load($oid);
			}
			else if (is_string($oid))
			{
				$this->_tbl->loadByAlias($oid, $section_id);
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
		else if ($pid)
		{
			$this->_tbl->loadByPost($pid);
		}
	}

	/**
	 * Returns a reference to a forum post attachment model
	 *
	 * @param   mixed    $oid  ID (int), alias (string), array, or object
	 * @param   integer  $pid  Post ID
	 * @return  object
	 */
	static function &getInstance($oid=0, $pid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $pid . '_' . $oid;
		}
		else if (is_object($oid))
		{
			$key = $pid . '_' . $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $pid . '_' . $oid['id'];
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $pid);
		}

		return $instances[$key];
	}
}

