<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class ForumAttachment extends JTable 
{
	var $id          = NULL;  // @var int(11) Primary key
	var $parent      = NULL;  // @var int(11)
	var $post_id     = NULL;  // @var int(11)
	var $filename    = NULL;  // @var varchar(255)
	var $description = NULL;  // @var varchar(255)

	public function __construct(&$db) 
	{
		parent::__construct('#__forum_attachments', 'id', $db);
	}
	
	/**
	 * Loads a record from the database and populates the current object with DB data
	 * 
	 * @param      integer $post_id ID of post the file was attached to
	 * @return     mixed   Return ForumAttachment object on success, false on failure
	 */
	public function loadByPost($post_id=NULL)
	{
		if ($post_id === NULL) 
		{
			return false;
		}
		$post_id = intval($post_id);
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE post_id='$post_id' LIMIT 1");
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
	
	/**
	 * Loads a record from the database and populates the current object with DB data
	 * 
	 * @param      integer $parent   Thread the file was posted in
	 * @param      integer $filename Name of file
	 * @return     mixed   Return ForumAttachment object on success, false on failure
	 */
	public function loadByThread($parent=NULL, $filename=NULL)
	{
		if ($parent === NULL || $filename === NULL) 
		{
			return false;
		}
		$parent = intval($parent);
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE parent='$parent' AND filename='$filename' LIMIT 1");
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
	
	//-----------
	
	public function check() 
	{
		if ($this->post_id == NULL) 
		{
			$this->setError(JText::_('COM_FORUM_ERROR_NO_POST_ID'));
			return false;
		}
		if (trim($this->filename) == '') 
		{
			$this->setError(JText::_('COM_FORUM_ERROR_NO_FILENAME'));
			return false;
		}

		return true;
	}
	
	//-----------
	
	public function getID() 
	{
		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE filename=" . $this->_db->Quote($this->filename) . " AND description=" . $this->_db->Quote($this->description) . " AND post_id=" . $this->_db->Quote(intval($this->post_id)));
		$this->id = $this->_db->loadResult();
		return $this->id;
	}
	
	//-----------

	public function getAttachments($parent)
	{
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE parent=" . $this->_db->Quote(intval($parent)));
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getAttachment($id, $url=null, $config=null)
	{
		//$this->reset();
		foreach ($this->getProperties() as $name => $value)
		{
			$this->$name = null;
		}
		$this->loadByPost($id);

		$path = $this->getUploadPath($this->parent, $config) . DS . $this->parent . DS . $this->post_id . DS . $this->filename;
		if ($this->filename && file_exists($path)) 
		{
			/*$juri = JURI::getInstance();
			$sef = JRoute::_('index.php?option=com_forum&category=' . $category . '&task=download&thread='. $this->post_id . '&file=' . $this->filename);
			$url = $juri->base() . trim($sef, '/');*/
			$url = JRoute::_($url . $this->filename);
			
			$this->description = htmlentities(stripslashes($this->description), ENT_COMPAT, 'UTF-8');
			
			if (preg_match("#bmp|gif|jpg|jpe|jpeg|png#i", $this->filename)) 
			{
				$size = getimagesize($path);
				if ($size[0] > 400) 
				{
					$html  = '<a href="' . $url . '" title="'. JText::_('Click for larger version') . '">';
					$html .= '<img src="' . $url . '" alt="' . $this->description . '" width="400" />';
					$html .= '</a>';
				} 
				else 
				{
					$html = '<img src="' . $url . '" alt="' . $this->description . '" />';
				}
			} 
			else 
			{
				$html  = '<a href="' . $url . '">';
				$html .= ($this->description) ? $this->description : $this->filename;
				$html .= '</a>';
			}
		} 
		else 
		{
			return ''; //'[attachment #' . $id . ' not found]';
		}
		
		return '<p class="attachment">' . $html . '</p>';
	}
	
	public function getUploadPath($id=0, $config=null)
	{
		if (!isset($this->_uppath))
		{
			if (!is_object($config))
			{
				$config = JComponentHelper::getParams('com_forum');
			}
			$this->_uppath = JPATH_ROOT . DS . trim($config->get('filepath', '/site/forum'), DS);
		}

		return $this->_uppath;
	}
	
	//-----------
	
	public function deleteAttachment($filename, $post_id) 
	{
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE filename=" . $this->_db->Quote($filename) . " AND post_id= " . $this->_db->Quote(intval($post_id)));
		if (!$this->_db->query()) 
		{
			return $this->_db->getErrorMsg();
		}
		return true;
	}
	
	//-----------
	
	public function loadAttachment($filename=NULL, $post_id=NULL)
	{
		if ($filename === NULL) 
		{
			return false;
		}
		if ($post_id === NULL) 
		{
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE filename=" . $this->_db->Quote($filename) . " AND post_id= " . $this->_db->Quote(intval($post_id)));
		return $this->_db->loadObject($this);
	}
}
