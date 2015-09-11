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

namespace Components\Wiki\Tables;

use Lang;
use Date;

/**
 * Wiki table class for logging links
 */
class Link extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct($db)
	{
		parent::__construct('#__wiki_page_links', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if valid, false if not
	 */
	public function check()
	{
		$this->page_id = intval($this->page_id);
		if (!$this->page_id)
		{
			$this->setError(Lang::txt('COM_WIKI_LOGS_MUST_HAVE_PAGE_ID'));
		}

		$this->scope = strtolower($this->scope);
		if (!$this->scope)
		{
			$this->setError(Lang::txt('COM_WIKI_LOGS_MUST_HAVE_SCOPE'));
		}

		if ($this->getError())
		{
			return false;
		}

		if (!$this->id)
		{
			$this->timestamp = Date::toSql();
		}

		return true;
	}

	/**
	 * Retrieve all entries for a specific page
	 *
	 * @param   integer  $page_id  Page ID
	 * @return  array
	 */
	public function find($page_id=null)
	{
		$page_id = $page_id ?: $this->page_id;

		if (!$page_id)
		{
			return null;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE page_id=" . $this->_db->quote($page_id) . " ORDER BY `timestamp` DESC");
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete all entries for a specific page
	 *
	 * @param   integer  $pid  Page ID
	 * @return  boolean  True on success
	 */
	public function deleteByPage($page_id=null)
	{
		$page_id = $page_id ?: $this->page_id;

		if (!$page_id)
		{
			return false;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE page_id=" . $this->_db->quote($page_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Delete all entries for a specific page
	 *
	 * @param   integer  $pid  Page ID
	 * @return  boolean  True on success
	 */
	public function addLinks($links=array())
	{
		if (count($links) <= 0)
		{
			return true;
		}

		$timestamp = Date::toSql();

		$query = "INSERT INTO $this->_tbl (`page_id`, `timestamp`, `scope`, `scope_id`, `link`, `url`) VALUES ";

		$inserts = array();
		foreach ($links as $link)
		{
			$inserts[] = "(" . $this->_db->quote($link['page_id']) . "," .
								$this->_db->quote($timestamp) . "," .
								$this->_db->quote($link['scope']) . "," .
								$this->_db->quote($link['scope_id']) . "," .
								$this->_db->quote($link['link']) . "," .
								$this->_db->quote($link['url']) .
							")";
		}

		$query .= implode(',', $inserts) . ";";

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Update entries
	 *
	 * @param   integer  $pid   Page ID
	 * @param   array    $links  Entries
	 * @return  boolean  True on success
	 */
	public function updateLinks($page_id, $data=array())
	{
		$links = array();
		foreach ($data as $data)
		{
			// Eliminate duplicates
			$links[$data['link']] = $data;
		}

		if ($rows = $this->find($page_id))
		{
			foreach ($rows as $row)
			{
				if (!isset($links[$row->link]))
				{
					// Link wasn't found, delete it
					$this->delete($row->id);
				}
				else
				{
					unset($links[$row->link]);
				}
			}
		}

		if (count($links) > 0)
		{
			$this->addLinks($links);
		}
		return true;
	}
}

