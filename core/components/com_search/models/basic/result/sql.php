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
use Exception;

/**
 * Search result SQL
 */
class Sql extends SearchResult
{
	/**
	 * Constructor
	 *
	 * @param   string  $sql
	 * @return  void
	 */
	public function __construct($sql = NULL)
	{
		$this->sql = $sql;
	}

	/**
	 * Get the SQL
	 *
	 * @return  string
	 */
	public function get_sql()
	{
		return $this->sql;
	}

	/**
	 * Return results as associative array
	 *
	 * @return  object
	 * @throws  SearchPluginError
	 */
	public function to_associative()
	{
		$db = \JFactory::getDBO();
		$db->setQuery($this->sql);

		if (!($rows = $db->loadAssocList()))
		{
			if ($error = $db->getErrorMsg())
			{
				throw new Exception('Invalid SQL in ' . $this->sql . ': ' . $error);
			}
			return new Blank();
		}
		return new AssocList($rows, $this->get_plugin());
	}
}

