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

namespace Components\Wiki\Models;

use Components\Wiki\Tables;
use Hubzero\Base\Model;

require_once(dirname(__DIR__) . DS . 'tables' . DS . 'author.php');

/**
 * Wiki model for a page author
 */
class Author extends Model
{
	/**
	 * Constructor
	 *
	 * @param   mixed  $oid  Integer, object, or array
	 * @return  void
	 */
	public function __construct($oid=null)
	{
		$this->_db = \App::get('db');

		$this->_tbl = new Tables\Author($this->_db);

		if (is_numeric($oid) || is_string($oid))
		{
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

	/**
	 * Returns a reference to this model
	 *
	 * @param   mixed  $oid  Integer, object, or array
	 * @return  object
	 */
	static function &getInstance($oid=0)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new self($oid);
		}

		return $instances[$oid];
	}
}

