<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Models\Adapters;

use Pathway;
use Lang;

require_once(__DIR__ . DS . 'base.php');

/**
 * Adapter class for a forum post link for group forum
 */
class Group extends Base
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
	 * @param   integer  $referenceid  Scope ID (group, course, etc.)
	 * @return  void
	 */
	public function __construct($referenceid=0)
	{
		$this->set('referenceid', $referenceid)
		     ->set('category', 'group')
		     ->set('option', $this->_segments['option']);

		$this->_item = \Hubzero\User\Group::getInstance($referenceid);
		if (!$this->_item)
		{
			$this->_item = new \Hubzero\User\Group();
		}

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
		return $this->_item->get('cn') . ' ' . Lang::txt('COM_WISHLIST_NAME_GROUP');
	}

	/**
	 * Retrieve a property from the internal item object
	 *
	 * @param   string  $key  Property to retrieve
	 * @return  string
	 */
	public function item($key = '')
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
	 * @param   string  $type    The type of link to return
	 * @param   mixed   $params  Optional string or associative array of params to append
	 * @return  string
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
					$segments['task'] = 'editwish';
					$segments['wishid'] = $this->get('wishid');
				}
			break;

			case 'delete':
				$segments['task'] = 'deletewish';
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

			case 'savesettings':
				unset($segments['category']);
				unset($segments['rid']);

				$segments['task'] = 'savesettings';
				$segments['listid'] = $this->get('wishlist');
			break;

			case 'comments':
				if ($this->get('wishid'))
				{
					$segments['task'] = 'wish';
					$segments['wishid'] = $this->get('wishid');
					$segments['com'] = 1;
					$anchor = '#comments';
				}
			break;

			case 'changestatus':
				if ($this->get('wishid'))
				{
					$segments['task'] = 'wish';
					$segments['wishid'] = $this->get('wishid');
					$segments['action'] = 'changestatus';
					$anchor = '#action';
				}
			break;

			case 'withdraw':
				if ($this->get('wishid'))
				{
					$segments['task'] = 'wish';
					$segments['wishid'] = $this->get('wishid');
					$segments['action'] = 'delete';
					$anchor = '#action';
				}
			break;

			case 'addbonus':
				if ($this->get('wishid'))
				{
					$segments['task'] = 'wish';
					$segments['wishid'] = $this->get('wishid');
					$segments['action'] = 'addbonus';
					$anchor = '#action';
				}
			break;

			case 'privacy':
				if ($this->get('wishid'))
				{
					$segments['task'] = 'editprivacy';
					$segments['wishid'] = $this->get('wishid');
				}
			break;

			case 'move':
				if ($this->get('wishid'))
				{
					$segments['task'] = 'wish';
					$segments['wishid'] = $this->get('wishid');
					$segments['action'] = 'move';
					$anchor = '#action';
				}
			break;

			case 'comment':
				if ($this->get('wishid'))
				{
					$segments['task'] = 'wish';
					$segments['wishid'] = $this->get('wishid');
					$segments['cat'] = 'wish';
					$anchor = '#commentform';
				}
			break;

			case 'editplan':
				if ($this->get('wishid'))
				{
					$segments['task'] = 'wish';
					$segments['wishid'] = $this->get('wishid');
					$segments['action'] = 'editplan';
					$anchor = '#plan';
				}
			break;

			case 'rank':
				if ($this->get('wishid'))
				{
					$segments['task'] = 'wish';
					$segments['wishid'] = $this->get('wishid');
					$segments['action'] = 'rank';
					$anchor = '#action';
				}
			break;

			case 'report':
			case 'reportabuse':
				return 'index.php?option=com_support&task=reportabuse&category=wish&id=' . $this->get('wishid') . '&parent=' . $this->get('wishlist');
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
	 * @param   string  $title  Breadcrumb title
	 * @param   string  $url    Breadcrumb URL
	 * @return  string
	 */
	public function pathway($title=null, $url=null)
	{
		if (!$title)
		{
			Pathway::append(
				Lang::txt('Groups'),
				'index.php?option=' . $this->get('option')
			);
			Pathway::append(
				stripslashes($this->_item->get('description')),
				'index.php?option=com_groups&cn=' . $this->_segments['cn']
			);
			Pathway::append(
				Lang::txt('Wishlist'),
				'index.php?option=com_groups&cn=' . $this->_segments['cn'] . '&active=wishlist'
			);
		}
		else
		{
			Pathway::append(
				$title,
				$url
			);
		}

		return $this;
	}
}

