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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;
use Lang;

/**
 * Table class for publication stats
 */
class Stats extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__publication_stats', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (trim($this->publication_id) == '')
		{
			$this->setError(Lang::txt('Your entry must have a publication ID.'));
			return false;
		}
		return true;
	}

	/**
	 * Load record
	 *
	 * @param   integer  $publication_id  Pub ID
	 * @param   integer  $period          Period
	 * @param   integer  $dthis
	 * @return  mixed    False if error, Object on success
	 */
	public function loadStats($publication_id = null, $period = null, $dthis = null)
	{
		if ($publication_id == null)
		{
			$publication_id = $this->publication_id;
		}
		if ($publication_id == null)
		{
			return false;
		}

		$sql = "SELECT *
				FROM $this->_tbl
				WHERE period =" . $this->_db->quote($period) . "
				AND publication_id =" . $this->_db->quote($publication_id);
		$sql.= $dthis ? " AND datetime='" . $dthis . "-00 00:00:00'" : '';
		$sql.= " ORDER BY datetime DESC LIMIT 1";

		$this->_db->setQuery($sql);

		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}
}
