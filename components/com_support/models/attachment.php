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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'attachment.php');

/**
 * Support mdoel for a ticket resolution
 */
class SupportModelAttachment extends \Hubzero\Base\Model
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'SupportAttachment';

	/**
	 * URL for this entry
	 *
	 * @var string
	 */
	private $_base = 'index.php?option=com_support';

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
	 * Scan text for attachment macros {attachment#}
	 *
	 * @param      string $text Text to search
	 * @return     string HTML
	 */
	public function parse($text)
	{
		return preg_replace_callback('/\{attachment#[0-9]*\}/sU', array(&$this,'getAttachment'), $text);
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

		$this->_tbl->load($id);

		if ($this->output != 'web' && $this->output != 'email')
		{
			return $this->link();
		}

		if (is_file($this->link('filepath')))
		{
			$url = $this->link();

			if ($this->output != 'email' && $this->isImage())
			{
				$size = getimagesize($this->link('filepath'));
				if ($size[0] > 400)
				{
					$img = '<a href="' . $url . '"><img src="' . $url . '" alt="' . $this->get('description') . '" width="400" /></a>';
				}
				else
				{
					$img = '<img src="' . $url . '" alt="' . $this->get('description') . '" />';
				}
				return $img;
			}
			else
			{
				return '<a href="' . $url . '" title="' . $this->get('description') . '">' . $this->get('description', $this->get('filename')) . '</a>';
			}
		}

		return '[attachment #' . $id . ' not found]';
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
	public function link($type='', $absolute=false)
	{
		static $path;

		$link = $this->_base;

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'base':
			case 'component':
				return $this->_base;
			break;

			case 'filepath':
				if (!$path)
				{
					$config = JComponentHelper::getParams('com_support');
					$path = JPATH_ROOT . DS . trim($config->get('webpath', '/site/tickets'), DS);
				}
				return $path . DS . $this->get('ticket') . DS . $this->get('filename');
			break;

			case 'permalink':
			default:
				$link .= '&task=download&id=' . $this->get('id') . '&file=' . $this->get('filename');
			break;
		}

		if ($absolute)
		{
			$link = rtrim(JURI::getInstance()->base(), '/') . '/' . trim(JRoute::_($link), '/');
		}

		return $link;
	}

	/**
	 * Delete redord and associated file
	 *
	 * @return     boolean
	 */
	public function delete()
	{
		$file = $this->get('filename');
		$path = $this->link('filepath');

		if (!parent::delete())
		{
			return false;
		}

		if (is_dir($path))
		{
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path))
			{
				$this->setError(JText::sprintf('COM_SUPPORT_ERROR_UNABLE_TO_DELETE_FILE', $file));
				return false;
			}
		}

		return true;
	}
}

