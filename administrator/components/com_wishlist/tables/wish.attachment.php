<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for wish attachments
 */
class WishAttachment extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wish_attachments', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (!$this->wish)
		{
			$this->setError(JText::_('Error: wish not found.'));
			return false;
		}

		$this->filename = trim($this->filename);
		if ($this->filename == '')
		{
			$this->setError(JText::_('Error: attachment not found.'));
			return false;
		}

		return true;
	}

	/**
	 * Get the ID for the record matching all specified columns
	 *
	 * @return  void
	 */
	public function getID()
	{
		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE filename=" . $this->_db->quote($this->filename) . " AND description=" . $this->_db->quote($this->description) . " AND wish=" . $this->_db->quote($this->wish));
		$id = $this->_db->loadResult();
		$this->id = $id;
	}

	/**
	 * Look for attachment string and replace with file/link
	 *
	 * @param   string  $text  Text to parse
	 * @return  string
	 */
	public function parse($text)
	{
		return preg_replace_callback('/{attachment#[0-9]*}/sU', array(&$this,'getAttachment'), $text);
	}

	/**
	 * Find a record and generate a linkt o the file
	 *
	 * @param   array   $matches  preg_replace_callback matches
	 * @return  string  HTML
	 */
	public function getAttachment($matches)
	{
		$match = $matches[0];

		if (!$match)
		{
			return '';
		}

		$tokens = explode('#', $match);
		$id = intval(end($tokens));

		$this->_db->setQuery(
			"SELECT a.filename, a.description, a.wish, l.category, l.referenceid
			FROM `$this->_tbl` AS a
			JOIN `#__wishlist_item` AS i ON i.id=a.wish
			JOIN `#__wishlist` AS l ON l.id=i.wishlist
			WHERE a.id=" . $id
		);
		$a = $this->_db->loadRow();

		if ($this->output == 'web')
		{
			if (is_file($this->uppath . DS . $a[0]))
			{
				$path = rtrim(JRoute::_('index.php?option=com_wishlist&task=wish&category=' . $a[3] . '&rid=' . $a[4] . '&wishid=' . $a[2]), DS);

				if (preg_match("/bmp|gif|jpg|jpe|jpeg|tif|tiff|png/i", $a[0]))
				{
					$size = getimagesize($this->uppath . DS . $a[0]);
					if ($size[0] > 300)
					{
						$img = '<a href="' . $path . '/' . $a[0] . '" rel="lightbox" title="' . $a[1] . '"><img src="' . $path . '/' . $a[0] . '" alt="' . $a[1] . '" width="300" /></a>';
					}
					else
					{
						$img = '<img src="' . $path . '/' . $a[0] . '" alt="' . $a[1] . '" />';
					}
					$this->description = $img;
				}
				else
				{
					$html  = '<a href="' . $path . '/' . $a[0] . '" title="' . $a[1] . '">';
					$html .= ($a[1]) ? $a[1] : $a[0];
					$html .= '</a>';
					$this->description = $html;
				}
			}
			else
			{
				$this->description = ''; //'[attachment #' . $id . ' not found]';
			}
		}
		else
		{
			$this->description = $this->webpath . '/' . $a[0];
		}

		return '';
	}

	/**
	 * Remove a record
	 *
	 * @param   string   $filename  File to remove
	 * @param   integer  $wish      Wish ID
	 * @return  mixed    String if error, True otherwise
	 */
	public function deleteAttachment($filename, $wish)
	{
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE filename=" . $this->_db->Quote($filename) . " AND wish=" . $this->_db->Quote($wish));
		if (!$this->_db->query())
		{
			return $this->_db->getErrorMsg();
		}
		return true;
	}

	/**
	 * Get a record based off of filename and wish ID
	 *
	 * @param   string   $filename  File name
	 * @param   integer  $wish      Wish ID
	 * @return  mixed    False if error, object otherwise
	 */
	public function loadAttachment($filename=NULL, $wish=NULL)
	{
		if ($filename === NULL || $wish === NULL)
		{
			return false;
		}

		$fields = array(
			'filename' => $filename,
			'wish'     => $wish
		);

		return parent::load($fields);
	}
}

