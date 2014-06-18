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

/**
 * Abstract adapter class for a wishlist
 */
abstract class WishlistModelAdapterAbstract extends \Hubzero\Base\Object
{
	/**
	 * The object the referenceid references
	 *
	 * @var object
	 */
	protected $_item = null;

	/**
	 * Script name
	 *
	 * @var string
	 */
	protected $_base = 'index.php';

	/**
	 * URL segments
	 *
	 * @var string
	 */
	protected $_segments = array();

	/**
	 * Constructor
	 *
	 * @param      integer $referenceid Scope ID (group, course, etc.)
	 * @return     void
	 */
	public function __construct($referenceid=0)
	{
		$this->set('referenceid', $referenceid);
	}

	/**
	 * Generate and return the title for this wishlist
	 *
	 * @return     string
	 */
	public function title()
	{
		return JText::_('COM_WISHLIST');
	}

	/**
	 * Retrieve a property from the internal item object
	 *
	 * @param      string $key Property to retrieve
	 * @return     string
	 */
	public function item($key='')
	{
		if ($key && is_object($this->_item))
		{
			return $this->_item->$key;
		}
		return $this->_item;
	}

	/**
	 * Does the item exists?
	 *
	 * @return     boolean
	 */
	public function exists()
	{
		if ($this->item() && $this->item('id'))
		{
			return true;
		}
		return false;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param      string $type   The type of link to return
	 * @param      mixed  $params Optional string or associative array of params to append
	 * @return     string
	 */
	public function link($type='', $params=null)
	{
		return $this->_base;
	}

	/**
	 * Flatten array of segments into querystring
	 *
	 * @param      array $segments An associative array of querystring bits
	 * @return     string
	 */
	protected function _build(array $segments)
	{
		$bits = array();
		foreach ($segments as $key => $param)
		{
			if ($param)
			{
				$bits[] = $key . '=' . $param;
			}
		}
		return implode('&', $bits);
	}

	/**
	 * Append an item to the breadcrumb trail.
	 * If no item is provided, it will build the trail up to the list
	 *
	 * @param      string $title Breadcrumb title
	 * @param      string $url   Breadcrumb URL
	 * @return     string
	 */
	public function pathway($title=null, $url=null)
	{
		$pathway = JFactory::getApplication()->getPathway();

		if (!$title)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->get('option'))),
				'index.php?option=' . $this->get('option')
			);
		}
		else
		{
			$pathway->addItem(
				$title,
				$url
			);
		}

		return $this;
	}
}
