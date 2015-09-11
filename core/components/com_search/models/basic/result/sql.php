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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		$db = \App::get('db');
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

