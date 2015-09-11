<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 * @since     Class available since release 2.0.0
 */

namespace Hubzero\Database\Syntax;

/**
 * Database sqlite query syntax class
 */
class Sqlite extends Mysql
{
	/**
	 * Builds an insert statement from the set params
	 *
	 * @return  string
	 * @since   2.0.0
	 **/
	public function buildInsert()
	{
		return 'INSERT ' . (($this->ignore) ? 'OR IGNORE ' : '') . 'INTO ' . $this->connection->quoteName($this->insert);
	}

	/**
	 * Returns the proper query for generating a list of table columns per this syntax
	 *
	 * @param   string  $table  The name of the database table
	 * @return  array
	 * @since   2.0.0
	 */
	public function getColumnsQuery($table)
	{
		return 'PRAGMA table_info(' . $this->connection->quoteName($table) . ')';
	}

	/**
	 * Normalizes the results of the above query
	 *
	 * @param   array  $data      The raw column data
	 * @param   bool   $typeOnly  True (default) to only return field types
	 * @return  array
	 * @since   2.0.0
	 **/
	public function normalizeColumns($data, $typeOnly = true)
	{
		$results = [];

		// If we only want the type as the value add just that to the list
		if ($typeOnly)
		{
			foreach ($data as $field)
			{
				// @FIXME: should we try to normalize types too?
				$results[$field->name] = $field->type;
			}
		}
		// If we want the whole field data object add that to the list
		else
		{
			foreach ($data as $field)
			{
				$results[$field->name] =
				[
					'name'      => $field->name,
					'type'      => $field->type,
					'allownull' => $field->notnull ? false : true,
					'default'   => $field->dflt_value,
					'pk'        => $field->pk ? true : false
				];
			}
		}

		return $results;
	}
}