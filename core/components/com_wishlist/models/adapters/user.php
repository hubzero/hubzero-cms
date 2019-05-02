<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Models\Adapters;

use Pathway;
use Lang;

require_once __DIR__ . DS . 'base.php';

/**
 * Adapter class for an entry link for member blog
 */
class User extends Base
{
	/**
	 * URL segments
	 *
	 * @var string
	 */
	protected $_segments = array(
		'option' => 'com_members',
	);

	/**
	 * Scope name
	 *
	 * @var  string
	 */
	protected $_scope = 'user';

	/**
	 * Constructor
	 *
	 * @param   integer  $scope_id  Scope ID (group, course, etc.)
	 * @return  void
	 */
	public function __construct($referenceid=0)
	{
		$this->set('referenceid', $referenceid)
		     ->set('category', 'user')
		     ->set('option', $this->_segments['option']);

		$this->_segments['id']     = $referenceid;
		$this->_segments['active'] = 'wishlist';

		$this->_item = \Hubzero\User\User::oneOrNew($this->get('scope_id'));
	}

	/**
	 * Generate and return the title for this wishlist
	 *
	 * @return  string
	 */
	public function title()
	{
		return Lang::txt('COM_WISHLIST_NAME_MY_WISHLIST');
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
				$key = 'name';
			break;

			case 'alias':
				$key = 'username';
			break;

			case 'id':
				$key = 'uidNumber';
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
				$segments['task']  = 'deletewish';
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
	 * @param   string  $title  Breadcrumb title
	 * @param   string  $url    Breadcrumb URL
	 * @return  string
	 */
	public function pathway($title=null, $url=null)
	{
		if (!$title)
		{
			Pathway::append(
				Lang::txt('Members'),
				'index.php?option=' . $this->get('option')
			);
			Pathway::append(
				stripslashes($this->_item->get('name')),
				'index.php?option=' . $this->get('option') . '&id=' . $this->get('referenceid')
			);
			Pathway::append(
				Lang::txt('Wishlist'),
				'index.php?option=' . $this->get('option') . '&active=wishlist&category=' . $this->get('category') . '&rid=' . $this->get('referenceid')
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
