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

/**
 * Table class for forum attachments
 */
class ForumTableAttachment extends JTable
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
	 * @var integer
	 */
	var $parent      = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $post_id     = NULL;

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
		parent::__construct('#__forum_attachments', 'id', $db);
	}

	/**
	 * Loads a record from the database and populates the current object with DB data
	 *
	 * @param      integer $post_id ID of post the file was attached to
	 * @return     mixed   Return ForumTableAttachment object on success, false on failure
	 */
	public function loadByPost($post_id=NULL)
	{
		return parent::load(array('post_id' => (int) $post_id));
	}

	/**
	 * Loads a record from the database and populates the current object with DB data
	 *
	 * @param      integer $parent   Thread the file was posted in
	 * @param      integer $filename Name of file
	 * @return     mixed   Return ForumTableAttachment object on success, false on failure
	 */
	public function loadByThread($parent=NULL, $filename=NULL)
	{
		$fields = array(
			'parent'   => intval($parent),
			'filename' => (string) $filename
		);

		return parent::load($fields);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->post_id = intval($this->post_id);
		if (!$this->post_id)
		{
			$this->setError(JText::_('COM_FORUM_ERROR_NO_POST_ID'));
			return false;
		}
		$this->filename = trim($this->filename);
		if (!$this->filename)
		{
			$this->setError(JText::_('COM_FORUM_ERROR_NO_FILENAME'));
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
		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE filename=" . $this->_db->Quote($this->filename) . " AND description=" . $this->_db->Quote($this->description) . " AND post_id=" . $this->_db->Quote(intval($this->post_id)));
		$this->id = $this->_db->loadResult();
		return $this->id;
	}

	/**
	 * Get all attachments for a thread
	 *
	 * @param      integer $parent Thread ID
	 * @return     array
	 */
	public function getAttachments($parent)
	{
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE parent=" . $this->_db->Quote(intval($parent)));
		return $this->_db->loadObjectList();
	}

	/**
	 * Generate the upload path for files
	 *
	 * @param      integer $id     Record ID
	 * @param      string  $url    URL to this post
	*  @param      object  $config Component config
	 * @return     string
	 */
	public function getAttachment($id, $url=null, $config=null)
	{
		foreach ($this->getProperties() as $name => $value)
		{
			$this->$name = null;
		}
		$this->loadByPost($id);

		$type = 'file';

		$path = $this->getUploadPath($this->parent, $config) . DS . $this->parent . DS . $this->post_id . DS . $this->filename;
		if ($this->filename && file_exists($path))
		{
			$url = JRoute::_($url . $this->filename);

			$this->description = htmlentities(stripslashes($this->description), ENT_COMPAT, 'UTF-8');

			if (preg_match("#bmp|gif|jpg|jpe|jpeg|png#i", $this->filename))
			{
				$type = 'img';
				$html  = '<span class="figure">';
				$size = getimagesize($path);

				if ($size[0] > 400)
				{
					$ratio = $size[0] / $size[1];

					$targetWidth = $targetHeight = min(400, max($size[0], $size[1]));

					if ($ratio < 1) {
					    $targetWidth = $targetHeight * $ratio;
					} else {
					    $targetHeight = $targetWidth / $ratio;
					}

					//$srcWidth = $originalWidth;
					//$srcHeight = $originalHeight;
					//$srcX = $srcY = 0;

					$html .= '<a href="' . $url . '" title="'. JText::_('Click for larger version') . '">';
					$html .= '<img src="' . $url . '" alt="' . $this->description . '" width="' . $targetWidth . '" height="' . $targetHeight . '" />';
					$html .= '</a>';
				}
				else
				{
					$html .= '<img src="' . $url . '" alt="' . $this->description . '" width="' . $size[0] . '" height="' . $size[1] . '" />';
				}
				if ($this->description)
				{
					$html .= '<span class="figcaption">' . $this->description . '</span>';
				}
				$html .= '</span>';
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
			return '';
		}

		return '<p class="' . $type . ' attachment">' . $html . '</p>';
	}

	/**
	 * Generate the upload path for files
	 *
	 * @param      integer $id     Record ID
	 * @param      object  $config Component config
	 * @return     integer
	 */
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

	/**
	 * Deletea record based on filename and post ID
	 *
	 * @param      string  $filename Filename
	 * @param      integer $post_id  Post ID
	 * @return     boolean True on success
	 */
	public function deleteAttachment($filename, $post_id)
	{
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE filename=" . $this->_db->Quote($filename) . " AND post_id= " . $this->_db->Quote(intval($post_id)));
		if (!$this->_db->query())
		{
			return $this->_db->getErrorMsg();
		}
		return true;
	}

	/**
	 * Load a record based on filename and post ID
	 *
	 * @param      string  $filename Filename
	 * @param      integer $post_id  Post ID
	 * @return     object
	 */
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
