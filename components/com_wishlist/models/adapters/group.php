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

require_once(__DIR__ . DS . 'abstract.php');

/**
 * Adapter class for a forum post link for group forum
 */
class WishlistModelAdapterGroup extends WishlistModelAdapterAbstract
{
	/**
	 * URL segments
	 *
	 * @var string
	 */
	protected $_segments = array(
		'option' => 'com_wishlist',
	);

	/**
	 * Constructor
	 *
	 * @param      integer $referenceid Scope ID (group, course, etc.)
	 * @return     void
	 */
	public function __construct($referenceid=0)
	{
		$this->set('referenceid', $referenceid)
		     ->set('category', 'group')
		     ->set('option', $this->_segments['option']);

		$this->_item = \Hubzero\User\Group::getInstance($referenceid);

		$this->_segments['cn']     = $this->_item->get('cn');
		$this->_segments['active'] = 'wishlist';
	}

	/**
	 * Generate and return the title for this wishlist
	 *
	 * @return     string
	 */
	public function title()
	{
		return $this->_item->get('cn') . ' ' . JText::_('COM_WISHLIST_NAME_GROUP');
	}

	/**
	 * Retrieve a property from the internal item object
	 *
	 * @param      string $key Property to retrieve
	 * @return     string
	 */
	public function item($key)
	{
		switch (strtolower($key))
		{
			case 'title':
				$key = 'description';
			break;

			case 'alias':
				$key = 'cn';
			break;

			case 'id':
				$key = 'gidNumber';
			break;

			default:
			break;
		}

		return parent::item($key);
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
		$segments = $this->_segments;

		if ($this->get('category'))
		{
			$segments['category'] = $this->get('category');
		}
		if ($this->get('referenceid'))
		{
			$segments['rid'] = $this->get('referenceid');
		}

		$anchor = '';

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'base':
				return $this->_base . '?' . (string) $this->_build($this->_segments);
			break;

			case 'edit':
				if ($this->get('wishid'))
				{
					$segments['task'] = 'edit';
					$segments['wishid'] = $this->get('wishid');
				}
			break;

			case 'delete':
				$segments['task'] = 'delete';
				if ($this->get('wishid'))
				{
					$segments['wishid'] = $this->get('wishid');
				}
			break;

			case 'add':
			case 'addwish':
			case 'new':
				$segments['task'] = 'addwish';
			break;

			case 'settings':
				unset($segments['category']);
				unset($segments['rid']);

				$segments['task'] = 'settings';
				$segments['id'] = $this->get('wishlist');
			break;

			case 'permalink':
			default:
				$segments['task'] = 'wishlist';
				if ($this->get('wishid'))
				{
					$segments['task'] = 'wish';
					$segments['wishid'] = $this->get('wishid');
				}
			break;
		}

		if (is_string($params))
		{
			$params = str_replace('&amp;', '&', $params);

			if (substr($params, 0, 1) == '#')
			{
				$anchor = $params;
			}
			else
			{
				if (substr($params, 0, 1) == '?')
				{
					$params = substr($params, 1);
				}
				parse_str($params, $parsed);
				$params = $parsed;
			}
		}

		$segments = array_merge($segments, (array) $params);

		return $this->_base . '?' . (string) $this->_build($segments) . (string) $anchor;
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
				JText::_('Groups'),
				'index.php?option=' . $this->get('option')
			);
			$pathway->addItem(
				stripslashes($this->_item->title),
				'index.php?option=' . $this->get('option') . '&cn=' . $this->_segments['cn']
			);
			$pathway->addItem(
				JText::_('Wishlist'),
				'index.php?option=' . $this->get('option') . '&active=wishlist&category=' . $this->get('category') . '&rid=' . $this->get('referenceid')
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

