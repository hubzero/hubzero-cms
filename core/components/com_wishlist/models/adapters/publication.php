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

require_once __DIR__ . DS . 'base.php';
require_once \Component::path('com_publications') . DS . 'tables' . DS . 'publication.php';

/**
 * Adapter class for a forum post link for group forum
 */
class Publication extends Base
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
	 * Scope name
	 *
	 * @var  string
	 */
	protected $_scope = 'publication';

	/**
	 * Constructor
	 *
	 * @param   integer  $referenceid  Scope ID (group, course, etc.)
	 * @return  void
	 */
	public function __construct($referenceid=0)
	{
		$this->set('referenceid', $referenceid)
		     ->set('category', 'publication')
		     ->set('option', $this->_segments['option']);

		$database = \App::get('db');
		$objP = new \Components\Publications\Tables\Publication($database);
		$this->_item = $objP->getPublication($referenceid, 'default');

		//$this->_segments['id']     = $this->_item->id;
		$this->_segments['active'] = 'wishlist';
	}

	/**
	 * Generate and return the title for this wishlist
	 *
	 * @return  string
	 */
	public function title()
	{
		return $this->_item->title;
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
				$segments['task'] = 'editwish';
				if ($this->get('wishid'))
				{
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
				Lang::txt('Publications'),
				'index.php?option=com_publications'
			);
			Pathway::append(
				stripslashes($this->_item->title),
				'index.php?option=com_publications&id=' . $this->get('referenceid')
			);
			Pathway::append(
				Lang::txt('Wishlist'),
				'index.php?option=com_publications&task=wishlist&id=' . $this->get('referenceid')
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
