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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Models\Adapters;

use User;

require_once __DIR__ . DS . 'base.php';

/**
 * Adapter class for a group wiki
 */
class Group extends Base
{
	/**
	 * URL segments
	 *
	 * @var  array
	 */
	protected $_segments = array(
		'option' => 'com_groups',
		'cn'     => '',
		'active' => 'wiki'
	);

	/**
	 * Constructor
	 *
	 * @param   string   $pagename
	 * @param   string   $path
	 * @param   integer  $scope_id
	 * @return  void
	 */
	public function __construct($pagename=null, $path=null, $scope_id=0)
	{
		$pagename = ($path ? $path . '/' : '') . $pagename;

		$this->_segments['pagename'] = $pagename;

		$this->_scope_id = $scope_id;

		$group = \Hubzero\User\Group::getInstance($this->_scope_id);
		if (!$group)
		{
			$group = new \Hubzero\User\Group();
			$group->set('gidNumber', $this->_scope_id);
		}

		$this->_segments['cn'] = $group->get('cn');
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
				unset($segments['pagename']);
				return $this->_base . '?' . (string) $this->_build($segments);
			break;

			case 'pdf':
			case 'new':
			case 'rename':
			case 'edit':
			case 'delete':
			case 'history':
			case 'compare':
			case 'approve':
			case 'comments':
			case 'deleterevision':
			case 'addcomment':
				$segments['action'] = $type;
			break;

			case 'permalink':
			default:

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
	 * Get an array of routing inputs
	 *
	 * @param   string  $task
	 * @return  array
	 */
	public function routing($task='save')
	{
		return array(
			'option' => $this->_segments['option'],
			'cn'     => $this->_segments['cn'],
			'active' => $this->_segments['active'],
			'action' => $task
		);
	}

	/**
	 * Get permissions for a user
	 *
	 * @param   object  $page
	 * @return  boolean
	 */
	public function authorise($page)
	{
		if ($page->config('access-check-done', false))
		{
			return true;
		}

		$group = \Hubzero\User\Group::getInstance($this->_scope_id);

		if (!$group)
		{
			$group = new \Hubzero\User\Group();
			$group->set('gidNumber', $this->_scope_id);
		}

		// Is this a group manager?
		if ($group && $group->published == 1)
		{
			// Is this a group manager?
			if ($group->is_member_of('managers', User::get('id')))
			{
				// Allow access to all options
				$page->config()->set('access-page-manage', true);
				$page->config()->set('access-page-create', true);
				$page->config()->set('access-page-delete', true);
				$page->config()->set('access-page-edit', true);
				$page->config()->set('access-page-modify', true);

				$page->config()->set('access-comment-view', true);
				$page->config()->set('access-comment-create', true);
				$page->config()->set('access-comment-delete', true);
				$page->config()->set('access-comment-edit', true);
			}
			else
			{
				// Check permissions based on the page mode (knol/wiki)
				switch ($page->param('mode'))
				{
					// Knowledge article
					// This means there's a defined set of authors
					case 'knol':
						if ($page->get('created_by') == User::get('id')
						 || $page->isAuthor(User::get('id')))
						{
							$page->config()->set('access-page-create', true);
							$page->config()->set('access-page-delete', true);
							$page->config()->set('access-page-edit', true);
							$page->config()->set('access-page-modify', true);
						}
						else if ($page->param('allow_changes'))
						{
							$page->config()->set('access-page-modify', true); // This allows users to suggest changes
						}

						if ($page->param('allow_comments'))
						{
							$page->config()->set('access-comment-view', true);
							$page->config()->set('access-comment-create', true);
						}
					break;

					// Standard wiki
					default:
						if ($group->is_member_of('members', User::get('id')))
						{
							$page->config()->set('access-page-create', true);

							if (!$page->isLocked())
							{
								$page->config()->set('access-page-delete', true);
								$page->config()->set('access-page-edit', true);
								$page->config()->set('access-page-modify', true);
							}

							$page->config()->set('access-comment-view', true);
							$page->config()->set('access-comment-create', true);
						}
					break;
				}
			}
		}

		$page->config()->set('access-check-done', true);

		return true;
	}
}
