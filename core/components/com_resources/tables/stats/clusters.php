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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Tables\Stats;

/**
 * Resources table class for cluster stats
 */
class Clusters extends \JTable
{
	/**
	 * Construct
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__resource_stats_clusters', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if valid, False if not
	 */
	public function check()
	{
		if (trim($this->resid) == '')
		{
			$this->setError(\Lang::txt('Your entry must have a resource ID.'));
			return false;
		}
		return true;
	}

	/**
	 * Load data for a resource
	 *
	 * @param   integer  $resid  Resource ID
	 * @return  boolean  True on success, False on error
	 */
	public function loadStats($resid=NULL)
	{
		if ($resid == NULL)
		{
			$resid = $this->resid;
		}
		if ($resid == NULL)
		{
			return false;
		}

		$sql = "SELECT COUNT(DISTINCT uidNumber, username) AS users, COUNT(DISTINCT cluster) AS classes
				FROM $this->_tbl
				WHERE resid=" . $this->_db->quote($resid);

		$this->_db->setQuery($sql);

		$this->users = 0;
		$this->classes = 0;

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
