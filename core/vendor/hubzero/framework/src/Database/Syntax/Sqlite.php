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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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