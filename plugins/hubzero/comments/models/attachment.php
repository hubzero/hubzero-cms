<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Plugins\Hubzero\Comments\Models;

use Hubzero\Base\Model;
use Hubzero\User\Profile;
use Hubzero\Base\ItemList;
use Hubzero\Utility\String;

/**
 * Model class for a forum post attachment
 */
class Attachment extends Model
{
	/**
	 * Table class name
	 *
	 * @var object
	 */
	protected $_tbl_name = '\\Hubzero\\Item\\Comment\\File';

	/**
	 * File size
	 *
	 * @var string
	 */
	private $_size = null;

	/**
	 * Diemnsions for file (must be an image)
	 *
	 * @var array
	 */
	private $_dimensions = null;

	/**
	 * Constructor
	 *
	 * @param      mixed   $oid ID (integer), alias (string), array or object
	 * @param      integer $pid Post ID
	 * @return     void
	 */
	public function __construct($oid=null, $pid=null)
	{
		$this->_db = \JFactory::getDBO();

		$cls = $this->_tbl_name;
		$this->_tbl = new $cls($this->_db);

		if (!($this->_tbl instanceof \JTable))
		{
			$this->_logError(
				__CLASS__ . '::' . __FUNCTION__ . '(); ' . \JText::_('Table class must be an instance of JTable.')
			);
			throw new \LogicException(\JText::_('Table class must be an instance of JTable.'));
		}

		if ($oid)
		{
			if (is_numeric($oid) || is_string($oid))
			{
				$this->_tbl->load($oid);
			}
			else if (is_object($oid) || is_array($oid))
			{
				$this->bind($oid);
			}
		}
		else if ($pid)
		{
			$this->_tbl->loadByComment($pid);
		}
	}

	/**
	 * Returns a reference to a forum post attachment model
	 *
	 * @param      mixed   $oid ID (int), alias (string), array, or object
	 * @param      integer $pid Post ID
	 * @return     object ForumModelAttachment
	 */
	static function &getInstance($oid=0, $pid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (is_numeric($oid) || is_string($oid))
		{
			$key = $pid . '_' . $oid;
		}
		else if (is_object($oid))
		{
			$key = $pid . '_' . $oid->id;
		}
		else if (is_array($oid))
		{
			$key = $pid . '_' . $oid['id'];
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new self($oid, $pid);
		}

		return $instances[$key];
	}

	/**
	 * Is the file an image?
	 *
	 * @return     boolean
	 */
	public function isImage()
	{
		return preg_match("/\.(bmp|gif|jpg|jpe|jpeg|png)$/i", $this->get('filename'));
	}

	/**
	 * Is the file an image?
	 *
	 * @return     boolean
	 */
	public function size()
	{
		if ($this->_size === null)
		{
			$this->_size = 0;
			if (file_exists($this->link('filepath')))
			{
				$this->_size = filesize($this->link('filepath'));
			}
		}

		return $this->_size;
	}

	/**
	 * Is the file an image?
	 *
	 * @return     boolean
	 */
	public function width()
	{
		if (!$this->_dimensions)
		{
			$this->_dimensions = $this->isImage() ? getimagesize($this->link('filepath')) : array(0, 0);
		}

		return $this->_dimensions[0];
	}

	/**
	 * Is the file an image?
	 *
	 * @return     boolean
	 */
	public function height()
	{
		if (!$this->_dimensions)
		{
			$this->_dimensions = $this->isImage() ? getimagesize($this->link('filepath')) : array(0, 0);
		}

		return $this->_dimensions[1];
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type The type of link to return
	 * @return     string
	 */
	public function link($type='')
	{
		static $path;

		if (!$path)
		{
			$path = JPATH_ROOT . '/site/comments';
		}

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'base':
				$link = $path . DS . $this->get('comment_id');
			break;

			case 'path':
			case 'filepath':
				$link = $path . DS . $this->get('comment_id') . DS . $this->get('filename');
			break;

			case 'permalink':
			default:
				$link = rtrim(\JURI::getInstance()->base(), '/') . '/site/comments/' . $this->get('comment_id') . '/' . $this->get('filename');
			break;
		}

		return $link;
	}
}

