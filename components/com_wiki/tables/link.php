<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Wiki table class for logging links
 */
class WikiLink extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id        = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $page_id   = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $timestamp = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $scope = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $scope_id = NULL;

	/**
	 * varchar(250)
	 * 
	 * @var string
	 */
	var $url      = NULL;

	/**
	 * varchar(250)
	 * 
	 * @var string
	 */
	var $link    = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wiki_page_links', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if valid, false if not
	 */
	public function check()
	{
		$this->page_id = intval($this->page_id);
		if (!$this->page_id) 
		{
			$this->setError(JText::_('COM_WIKI_LOGS_MUST_HAVE_PAGE_ID'));
			return false;
		}

		$this->scope = strtolower($this->scope);
		if (!$this->scope) 
		{
			$this->setError(JText::_('COM_WIKI_LOGS_MUST_HAVE_SCOPE'));
			return false;
		}

		if (!$this->id)
		{
			$this->timestamp = date('Y-m-d H:i:s', time());
		}

		return true;
	}

	/**
	 * Retrieve all entries for a specific page
	 * 
	 * @param      integer $pid Page ID
	 * @return     array
	 */
	public function find($page_id=null)
	{
		if (!$page_id) 
		{
			$page_id = $this->page_id;
		}
		if (!$page_id) 
		{
			return null;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE page_id=" . $this->_db->Quote($page_id) . " ORDER BY `timestamp` DESC");
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete all entries for a specific page
	 * 
	 * @param      integer $pid Page ID
	 * @return     boolean True on success
	 */
	public function deleteByPage($page_id=null)
	{
		if (!$page_id) 
		{
			$page_id = $this->page_id;
		}
		if (!$page_id) 
		{
			return false;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE page_id=" . $this->_db->Quote($page_id));
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
	 * @param      integer $pid Page ID
	 * @return     boolean True on success
	 */
	public function addLinks($links=array())
	{
		if (count($links) <= 0)
		{
			return true;
		}

		$timestamp = date('Y-m-d H:i:s', time());

		$query = "INSERT INTO $this->_tbl (`page_id`, `timestamp`, `scope`, `scope_id`, `link`, `url`) VALUES ";

		$inserts = array();
		foreach ($links as $link)
		{
			$inserts[] = "(" . $this->_db->Quote($link['page_id']) . "," . 
								$this->_db->Quote($timestamp) . "," . 
								$this->_db->Quote($link['scope']) . "," . 
								$this->_db->Quote($link['scope_id']) . "," . 
								$this->_db->Quote($link['link']) . "," . 
								$this->_db->Quote($link['url']) . 
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
	 * @param      integer $pid   Page ID
	 * @param      array   $links Entries
	 * @return     boolean True on success
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
			//print_r($rows);
			foreach ($rows as $row)
			{
				if (!isset($links[$row->link]))
				{
					// Link wasn't found, delete it
					$this->delete($row->id);
					//echo 'notfound: ' . $row->link . '<br />';
				}
				else
				{
					//echo 'found: ' . $row->link . '<br />';
					unset($links[$row->link]);
				}
			}
		}

		if (count($links) > 0)
		{
			$this->addLinks($links);
			/*foreach ($links as $link)
			{
				$obj = new WikiLink($this->_db);
				$obj->bind($link);
				if ($obj->check())
				{
					$obj->store();
				}
			}*/
		}
		return true;
	}
}

