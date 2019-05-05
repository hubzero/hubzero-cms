<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Models\Adapters;

use Components\Resources\Models\Entry;
use Pathway;
use Lang;

require_once __DIR__ . DS . 'base.php';
require_once \Component::path('com_resources') . DS . 'models' . DS . 'entry.php';

/**
 * Adapter class for a forum post link for course forum
 */
class Resources extends Base
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
	protected $_scope = 'resource';

	/**
	 * Constructor
	 *
	 * @param   integer  $referenceid  Scope ID (group, course, etc.)
	 * @return  void
	 */
	public function __construct($referenceid=0)
	{
		$this->set('referenceid', $referenceid)
		     ->set('category', 'resource')
		     ->set('option', $this->_segments['option']);

		$this->_item = Entry::getInstance((int)$this->get('referenceid'));

		if ($this->_item->standalone != 1 || $this->_item->published != Entry::STATE_PUBLISHED)
		{
			$this->_item->id = 0;
		}

		$this->_segments['active'] = 'wishlist';
	}

	/**
	 * Get owners
	 *
	 * @return  array
	 */
	public function owners()
	{
		$owners = array();

		if ($this->_item->isTool())
		{
			$cons = $this->_item->contributors('tool');
		}
		else
		{
			$cons = $this->_item->contributors();
		}

		foreach ($cons as $contributor)
		{
			if (!isset($contributor->authorid) && isset($contributor->uid))
			{
				$contributor->authorid = $contributor->uid;
			}

			$owners[] = $contributor->authorid;
		}

		return $owners;
	}

	/**
	 * Get groups
	 *
	 * @return  array
	 */
	public function groups()
	{
		$groups = array();

		if ($this->_item->isTool())
		{
			$db = \App::get('db');
			$query = "SELECT g.cn FROM `#__tool_groups` AS g
				INNER JOIN `#__xgroups` AS xg ON g.cn=xg.cn
				INNER JOIN `#__tool` AS t ON g.toolid=t.id
				INNER JOIN `#__resources` as r ON r.alias = t.toolname
				WHERE r.id = " . $db->quote($this->_item->id) . " AND g.role=1";

			$db->setQuery($query);
			$toolgroup = $db->loadResult();

			if ($toolgroup)
			{
				$groups[] = $toolgroup;
			}
		}

		return $groups;
	}

	/**
	 * Generate and return the title for this wishlist
	 *
	 * @return  string
	 */
	public function title()
	{
		return ($this->_item->isTool() && isset($this->_item->alias))
				? Lang::txt('COM_WISHLIST_NAME_RESOURCE_TOOL') . ' ' . $this->_item->alias
				: Lang::txt('COM_WISHLIST_NAME_RESOURCE_ID') . ' ' . $this->_item->id;
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
				Lang::txt('Resources'),
				'index.php?option=com_resources'
			);
			Pathway::append(
				stripslashes($this->_item->title),
				'index.php?option=com_resources&id=' . $this->get('referenceid')
			);
			Pathway::append(
				Lang::txt('Wishlist'),
				'index.php?option=com_resources&active=wishlist&id=' . $this->get('referenceid')
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
