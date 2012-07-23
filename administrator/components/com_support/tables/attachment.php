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
		if ($this->ticket == NULL) 
		{
			$this->setError(JText::_('SUPPORT_ERROR_NO_TICKET_ID'));
			return false;
		}
		if (trim($this->filename) == '') 
		{
			$this->setError(JText::_('SUPPORT_ERROR_NO_FILENAME'));
			return false;
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
		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE filename='" . $this->filename . "' AND description='" . $this->description . "' AND ticket=" . $this->ticket);
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

		$this->_db->setQuery("SELECT filename, description FROM $this->_tbl WHERE id=" . $id);
		$a = $this->_db->loadRow();

		if ($this->output != 'web') 
		{
			return $this->webpath . '/' . $a[0];
		}

		if (is_file($this->uppath . DS . $a[0])) 
		{
			$juri =& JURI::getInstance();
			$sef = JRoute::_('index.php?option=com_support&task=download&id=' . $id . '&file=' . $a[0]);
			$url = $juri->base() . trim($sef, DS);

			if (preg_match("/bmp|gif|jpg|jpe|jpeg|png/i", $a[0])) 
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
	 * @return     boolean True on success
	 */
	public function deleteAttachment($filename, $ticket)
	{
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE filename='" . $filename . "' AND ticket=" . $ticket);
		if (!$this->_db->query()) 
		{
			return $this->_db->getErrorMsg();
		}
		return true;
	}

	/**
	 * Load a record based on filename and ticket number and bind to $this
	 * 
	 * @param      integer $filename File name
	 * @param      integer $ticket   Ticket ID
	 * @return     boolean True on success
	 */
	public function loadAttachment($filename=NULL, $ticket=NULL)
	{
		if ($filename === NULL) 
		{
			return false;
		}
		if ($ticket === NULL) 
		{
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE filename='$filename' AND ticket='$ticket'");
		return $this->_db->loadObject($this);
	}
}
