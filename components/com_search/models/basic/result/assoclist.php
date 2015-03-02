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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Search\Models\Basic\Result;

use Components\Search\Models\Basic\Result as SearchResult;
use Iterator;

/**
 * Associative list
 */
class AssocList extends Assoc implements Iterator
{
	/**
	 * Description for 'rows'
	 *
	 * @var array
	 */
	private $rows = array();

	/**
	 * Description for 'pos'
	 *
	 * @var integer
	 */
	private $pos = 0;

	/**
	 * Short description for 'is_scalar'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	public function is_scalar()
	{
		return false;
	}

	/**
	 * Short description for 'set_plugin'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $plugin Parameter description (if any) ...
	 * @param      boolean $skip_cleanup Parameter description (if any) ...
	 * @return     void
	 */
	public function set_plugin($plugin, $skip_cleanup = false)
	{
		foreach ($this->rows as $row)
		{
			$row->set_plugin($plugin, $skip_cleanup);
		}
	}

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $rows Parameter description (if any) ...
	 * @param      unknown $plugin Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($rows, $plugin = NULL)
	{
		$this->rows = is_array($rows) ? $rows : array($rows);
		$scale = 1;
		foreach ($this->rows as $idx=>&$row)
		{
			if (!($row instanceof SearchResult))
			{
				$row = new AssocScalar($row);
				$row->set_plugin($plugin);
			}

			if ($idx == 0 && ($weight = $row->get_weight()) > 1)
			{
				$scale = $weight;
			}

			if ($scale > 1)
			{
				$row->scale_weight($scale, 'normalizing within plugin');
			}
		}
	}

	/**
	 * Short description for 'at'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $idx Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function &at($idx)
	{
		return $this->rows[$idx];
	}

	/**
	 * Short description for 'to_associative'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function to_associative()
	{
		return $this;
	}

	/**
	 * Short description for 'get_items'
	 *
	 * Long description (if any) ...
	 *
	 * @return     array Return description (if any) ...
	 */
	public function get_items()
	{
		return $this->rows;
	}

	/**
	 * Short description for 'rewind'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	public function rewind()
	{
		$this->pos = 0;
	}

	/**
	 * Short description for 'current'
	 *
	 * Long description (if any) ...
	 *
	 * @return     array Return description (if any) ...
	 */
	public function current()
	{
		return $this->rows[$this->pos];
	}

	/**
	 * Short description for 'key'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function key()
	{
		return $this->pos;
	}

	/**
	 * Short description for 'next'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	public function next()
	{
		++$this->pos;
	}

	/**
	 * Short description for 'valid'
	 *
	 * Long description (if any) ...
	 *
	 * @return     array Return description (if any) ...
	 */
	public function valid()
	{
		return isset($this->rows[$this->pos]);
	}
}
