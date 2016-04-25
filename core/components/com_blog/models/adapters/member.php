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

namespace Components\Blog\Models\Adapters;

use Hubzero\User\User;
use Hubzero\Utility\String;
use Plugin;
use Date;

require_once(__DIR__ . DS . 'base.php');

/**
 * Adapter class for an entry link for member blog
 */
class Member extends Base
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
	 * Constructor
	 *
	 * @param      integer $scope_id Scope ID (group, course, etc.)
	 * @return     void
	 */
	public function __construct($scope_id=0)
	{
		$this->set('scope_id', $scope_id);

		$this->_segments['id']     = $scope_id;
		$this->_segments['active'] = 'blog';

		$this->_item = User::oneOrNew($scope_id);

		$config = Plugin::params('members', 'blog');

		$id = String::pad($this->get('scope_id'));

		$this->set('path', str_replace('{{uid}}', $id, $config->get('uploadpath', '/site/members/{{uid}}/blog')));
		$this->set('scope', $this->get('scope_id') . '/blog');
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
				$key = 'name';
			break;

			case 'alias':
				$key = 'username';
			break;

			case 'uidNumber':
				$key = 'id';
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
	 * @param      string $type   The type of link to return
	 * @param      mixed  $params Optional string or associative array of params to append
	 * @return     string
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
