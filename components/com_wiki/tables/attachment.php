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
 * Wiki table class for file attachments
 */
class WikiTableAttachment extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wiki_attachments', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   mixed    $keys    Alias or ID
	 * @param   inteher  $pageid  Parent page ID
	 * @return  boolean  True on success
	 */
	public function load($oid=NULL, $pageid=NULL)
	{
		if ($oid === NULL)
		{
			return false;
		}

		if (is_string($oid))
		{
			return parent::load(array(
				'filename' => $oid,
				'pageid'   => $pageid
			));
		}

		return parent::load($oid);
	}

	/**
	 * Get a record ID based on filename and page ID
	 *
	 * @param   string  $filename  File name
	 * @param   string  $pageid    Parent page ID
	 * @return  array
	 */
	public function getID($filename, $pageid)
	{
		$this->_db->setQuery("SELECT id, description FROM $this->_tbl WHERE filename=" . $this->_db->Quote($filename) . " AND pageid=" . $this->_db->Quote($pageid));
		return $this->_db->loadRow();
	}

	/**
	 * Delete a record based on parent page and filename
	 *
	 * @param   string   $filename  File name
	 * @param   string   $pageid    Parent page ID
	 * @return  boolean  False if errors, true on success
	 */
	public function deleteFile($filename, $pageid)
	{
		if (!$filename || !$pageid)
		{
			return false;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE filename=" . $this->_db->Quote($filename) . " AND pageid=" . $this->_db->Quote($pageid));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Turn attachment syntax into links
	 *
	 * @param   string  $text  Text to look for attachments in
	 * @return  string
	 */
	public function parse($text)
	{
		//$f = '/\{file#[0-9]*\}/sU';
		$f = '/\[\[file#[0-9]*\]\]/sU';
		return preg_replace_callback($f, array(&$this, 'getAttachment'), $text);
	}

	/**
	 * Processor for parse()
	 *
	 * @param   array   $matches  Attachment syntax string
	 * @return  string
	 */
	public function getAttachment($matches)
	{
		$match  = $matches[0];
		$tokens = preg_split('/#/', $match);
		$id = intval(end($tokens));

		$this->_db->setQuery("SELECT filename, description FROM $this->_tbl WHERE id=" . $this->_db->Quote($id));
		$a = $this->_db->loadRow();

		if (is_file(JPATH_ROOT . $this->path . DS . $this->pageid . DS . $a[0]))
		{
			if (preg_match("#bmp|gif|jpg|jpe|jpeg|tif|tiff|png#i", $a[0]))
			{
				return '<img src="' . $this->path . DS . $this->pageid . DS . $a[0] . '" alt="' . $a[1] . '" />';
			}
			else
			{
				$html  = '<a href="' . $this->path . DS . $this->pageid . DS . $a[0] . '" title="' . $a[1] . '">';
				$html .= ($a[1]) ? $a[1] : $a[0];
				$html .= '</a>';
				return $html;
			}
		}

		return '[file #' . $id . ' not found]';
	}

	/**
	 * Set the page ID for a record
	 *
	 * @param   integer  $oldid  Old page ID
	 * @param   integer  $newid  New page ID
	 * @return  boolean  False if errors, true on success
	 */
	public function setPageID($oldid=null, $newid=null)
	{
		if (!$oldid || !$newid)
		{
			return false;
		}

		$this->_db->setQuery("UPDATE $this->_tbl SET pageid=" . $this->_db->Quote($newid) . " WHERE pageid=" . $this->_db->Quote($oldid));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}

