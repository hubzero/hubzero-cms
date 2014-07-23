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
 * Table class for support attachments (tickets, comments)
 */
class SupportAttachment extends JTable
{
	/**
	 * int(11) Primary key
	 *
	 * @var integer
	 */
	var $id          = NULL;

	/**
	 * int(11)
	 *
	 * @var string
	 */
	var $ticket      = NULL;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $filename    = NULL;

	/**
	 * varchar(255)
	 *
	 * @var string
	 */
	var $description = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $comment_id  = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $created  = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $created_by  = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__support_attachments', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->ticket = intval($this->ticket);
		if (!$this->ticket)
		{
			$this->setError(JText::_('SUPPORT_ERROR_NO_TICKET_ID'));
			return false;
		}
		if (trim($this->filename) == '')
		{
			$this->setError(JText::_('SUPPORT_ERROR_NO_FILENAME'));
			return false;
		}
		$this->comment_id = intval($this->comment_id);

		if (!$this->id)
		{
			$this->created_by = JFactory::getUser()->get('id');
			$this->created = JFactory::getDate()->toSql();
		}

		return true;
	}

	/**
	 * Get the ID of a record
	 *
	 * @return     integer
	 */
	public function getID()
	{
		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE filename=" . $this->_db->Quote($this->filename) . " AND description=" . $this->_db->Quote($this->description) . " AND ticket=" . $this->_db->Quote($this->ticket));
		$id = $this->_db->loadResult();
		$this->id = $id;
	}

	/**
	 * Scan text for attachment macros {attachment#}
	 *
	 * @param      string $text Text to search
	 * @return     string HTML
	 */
	public function parse($text)
	{
		$f = '/\{attachment#[0-9]*\}/sU';
		return preg_replace_callback($f, array(&$this,'getAttachment'), $text);
	}

	/**
	 * Process an attachment macro and output a link to the file
	 *
	 * @param      array $matches Macro info
	 * @return     string HTML
	 */
	public function getAttachment($matches)
	{
		$match = $matches[0];
		$tokens = explode('#', $match);
		$id = intval(end($tokens));

		$this->_db->setQuery("SELECT filename, description FROM $this->_tbl WHERE id=" . $this->_db->Quote($id));
		$a = $this->_db->loadRow();

		if ($this->output != 'web' && $this->output != 'email')
		{
			return $this->webpath . '/' . $a[0];
		}

		if (is_file($this->uppath . DS . $a[0]))
		{
			$juri = JURI::getInstance();
			$sef = JRoute::_('index.php?option=com_support&task=download&id=' . $id . '&file=' . $a[0]);
			$url = $juri->base() . trim($sef, DS);
			$url = str_replace('/administrator/administrator', '/administrator', $url);

			if ($this->output != 'email' && preg_match("/bmp|gif|jpg|jpe|jpeg|png/i", $a[0]))
			{
				$size = getimagesize($this->uppath . DS . $a[0]);
				if ($size[0] > 400)
				{
					$img = '<a href="' . $url . '" title="Click for larger version"><img src="' . $url . '" alt="' . $a[1] . '" width="400" /></a>';
				}
				else
				{
					$img = '<img src="' . $url . '" alt="' . $a[1] . '" />';
				}
				return $img;
			}
			else
			{
				$html  = '<a href="' . $url . '" title="' . $a[1] . '">';
				$html .= ($a[1]) ? $a[1] : $a[0];
				$html .= '</a>';
				return $html;
			}
		} else {
			return '[attachment #' . $id . ' not found]';
		}
	}

	/**
	 * Delete a record based on filename and ticket number
	 *
	 * @param      integer $filename File name
	 * @param      integer $ticket   Ticket ID
	 * @param      integer $comment  Comment ID
	 * @return     boolean True on success
	 */
	public function deleteAttachment($filename, $ticket, $comment=0)
	{
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE filename=" . $this->_db->Quote($filename) . " AND ticket=" . $this->_db->Quote($ticket) . " AND comment=" . $this->_db->Quote($comment));
		if (!$this->_db->query())
		{
			return $this->_db->getErrorMsg();
		}
		return true;
	}

	/**
	 * Delete all records based on ticket number
	 *
	 * @param      integer $ticket   Ticket ID
	 * @return     boolean True on success
	 */
	public function deleteAllForTicket($ticket)
	{
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE ticket=" . $this->_db->Quote($ticket));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$config = JComponentHelper::getParams('com_support');
		$path = JPATH_ROOT . DS . trim($config->get('webpath', '/site/tickets'), DS) . DS . $ticket;
		if (is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::delete($path))
			{
				$this->setError(JText::_('Unable to delete path'));
				return false;
			}
		}
		return true;
	}

	/**
	 * Load a record based on filename and ticket number and bind to $this
	 *
	 * @param      integer $filename File name
	 * @param      integer $ticket   Ticket ID
	 * @param      integer $comment  Comment ID
	 * @return     boolean True on success
	 */
	public function loadAttachment($filename=NULL, $ticket=NULL, $comment=0)
	{
		if ($filename === NULL)
		{
			return false;
		}
		if ($ticket === NULL)
		{
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE filename=" . $this->_db->Quote($filename) . " AND ticket=" . $this->_db->Quote($ticket) . " AND comment=" . $this->_db->Quote($comment));
		return $this->_db->loadObject($this);
	}

	/**
	 * Update the comment ID for multiple records
	 *
	 * @param   integer $before Old ID
	 * @param   integer $after  New ID
	 * @return  boolean  True on success.
	 */
	public function updateCommentId($before, $after)
	{
		$this->_db->setQuery("UPDATE $this->_tbl SET comment_id=" . $this->_db->Quote($after) . " WHERE comment_id=" . $this->_db->Quote($before));
		if (!$this->_db->query())
		{
			$this->setError($database->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Update the ticket ID for multiple records
	 *
	 * @param   integer $before Old ID
	 * @param   integer $after  New ID
	 * @return  boolean  True on success.
	 */
	public function updateTicketId($before, $after)
	{
		$this->_db->setQuery("UPDATE $this->_tbl SET ticket=" . $this->_db->Quote($after) . " WHERE ticket=" . $this->_db->Quote($before));
		if (!$this->_db->query())
		{
			$this->setError($database->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Get records
	 *
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function find($what='list', $filters=array())
	{
		switch (strtolower($what))
		{
			case 'count':
				$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

				$this->_db->setQuery($query);
				return $this->_db->loadResult();
			break;

			case 'all':
				$filters['limit'] = 0;

				return $this->find('list', $filters);
			break;

			case 'list':
			default:
				$query = "SELECT * " . $this->_buildQuery($filters);

				if (!isset($filters['sort']) || !$filters['sort'])
				{
					$filters['sort'] = 'id';
				}
				if (!isset($filters['sort_Dir']) || !in_array(strtoupper($filters['sort_Dir']), array('ASC', 'DESC')))
				{
					$filters['sort_Dir'] = 'ASC';
				}
				$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

				if (isset($filters['limit']) && $filters['limit'] != 0)
				{
					$query .= ' LIMIT ' . intval($filters['start']) . ',' . intval($filters['limit']);
				}

				$this->_db->setQuery($query);
				return $this->_db->loadObjectList();
			break;
		}
	}

	/**
	 * Build a query from filters passed
	 *
	 * @param      array $filters Filters to build query from
	 * @return     string SQL
	 */
	private function _buildQuery($filters)
	{
		$query  = "FROM $this->_tbl";

		$where = array();

		if (isset($filters['ticket']) && (int) $filters['ticket'] > 0)
		{
			$where[] = "ticket=" . $this->_db->Quote(intval($filters['ticket']));
		}
		if (isset($filters['comment_id'])) // && (int) $filters['comment_id'] >= 0)
		{
			$where[] = "comment_id=" . $this->_db->Quote(intval($filters['comment_id']));
		}
		if (isset($filters['filename']) && $filters['filename'])
		{
			$where[] = "filename=" . $this->_db->Quote($filters['filename']);
		}
		if (isset($filters['created_by']) && (int) $filters['created_by'] >= 0)
		{
			$where[] = "created_by=" . $this->_db->Quote(intval($filters['created_by']));
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "(LOWER(filename) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
					OR LOWER(description) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}
}
