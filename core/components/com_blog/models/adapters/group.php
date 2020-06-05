<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Blog\Models\Adapters;

use Hubzero\User\Group as UserGroup;
use Component;
use Date;

require_once __DIR__ . DS . 'base.php';

/**
 * Adapter class for an entry link for the group blog
 */
class Group extends Base
{
	/**
	 * URL segments
	 *
	 * @var  string
	 */
	protected $_segments = array(
		'option' => 'com_groups',
	);

	/**
	 * Constructor
	 *
	 * @param   integer  $scope_id  Scope ID (group, course, etc.)
	 * @return  void
	 */
	public function __construct($scope_id=0)
	{
		$this->set('scope_id', $scope_id);

		$this->_item = UserGroup::getInstance($scope_id);
		if (!($this->_item instanceof UserGroup))
		{
			$this->_item = new UserGroup();
		}

		$this->_segments['cn']     = $this->_item->get('cn');
		$this->_segments['active'] = 'blog';

		$groupParams = Component::params('com_groups');
		$uploadpath = $groupParams->get('uploadpath', '/site/groups');
		$uploadpath = trim($uploadpath, DS) . DS . $this->get('scope_id') . DS . 'uploads' . DS . 'blog';

		$this->set('path', $uploadpath);
		$this->set('scope', $this->_segments['cn'] . '/blog');
		$this->set('option', $this->_segments['option']);
	}

	/**
	 * Retrieve a property from the internal item object
	 *
	 * @param   string  $key      Property to retrieve
	 * @param   mixed   $default
	 * @return  string
	 */
	public function item($key='', $default = null)
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

		return parent::item($key, $default);
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

		$anchor = '';

		// If it doesn't exist or isn't published
		switch (strtolower($type))
		{
			case 'base':
				return $this->_base . '?' . (string) $this->_build($this->_segments);
			break;

			case 'edit':
				$segments['action']  = 'edit';
				$segments['entry'] = $this->get('id');
			break;

			case 'delete':
				$segments['action']  = 'delete';
				$segments['entry'] = $this->get('id');
			break;

			case 'new':
				$segments['action'] = 'new';
			break;

			case 'comments':
				$segments['scope']  = Date::of($this->get('publish_up'))->format('Y') . '/';
				$segments['scope'] .= Date::of($this->get('publish_up'))->format('m') . '/';
				$segments['scope'] .= $this->get('alias');

				$anchor = '#comments';
			break;

			case 'permalink':
			default:
				$segments['scope']  = Date::of($this->get('publish_up'))->format('Y') . '/';
				$segments['scope'] .= Date::of($this->get('publish_up'))->format('m') . '/';
				$segments['scope'] .= $this->get('alias');
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
}
