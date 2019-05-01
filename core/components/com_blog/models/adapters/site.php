<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Blog\Models\Adapters;

use Component;
use Date;

require_once(__DIR__ . DS . 'base.php');

/**
 * Adapter class for an entry link for the site-wide blog
 */
class Site extends Base
{
	/**
	 * URL segments
	 *
	 * @var string
	 */
	protected $_segments = array(
		'option' => 'com_blog',
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

		$config = Component::params($this->_segments['option']);

		$this->set('path', $config->get('uploadpath', '/site/blog'));
		$this->set('scope', '');
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
				return Lang::txt('COM_BLOG');
			break;

			case 'alias':
				return 'site';
			break;

			case 'id':
				return $this->get('scope_id');
			break;

			default:
			break;
		}

		return '';
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
				$segments['task']  = 'edit';
				$segments['entry'] = $this->get('id');
			break;

			case 'delete':
				$segments['task']  = 'delete';
				$segments['entry'] = $this->get('id');
			break;

			case 'new':
				$segments['task'] = 'new';
			break;

			case 'comments':
				$segments['task']  = Date::of($this->get('publish_up'))->format('Y') . '/';
				$segments['task'] .= Date::of($this->get('publish_up'))->format('m') . '/';
				$segments['task'] .= $this->get('alias');

				$anchor = '#comments';
			break;

			case 'permalink':
			default:
				$segments['task']  = Date::of($this->get('publish_up'))->format('Y') . '/';
				$segments['task'] .= Date::of($this->get('publish_up'))->format('m') . '/';
				$segments['task'] .= $this->get('alias');
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
