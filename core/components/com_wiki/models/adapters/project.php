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

use Request;
use Route;

require_once(__DIR__ . DS . 'base.php');

/**
 * Adapter class for a project note
 */
class Project extends Base
{
	/**
	 * URL segments
	 *
	 * @var  array
	 */
	protected $_segments = array(
		'option' => 'com_projects',
		'alias'  => '',
		'active' => 'notes'
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

		//$project = Project::getInstance($this->_scope_id);
		$project = Request::getVar('project', NULL);
		if (is_object($project))
		{
			$project = $project->get('alias');
		}

		$this->_segments['alias'] = $project;
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
		$project = Request::getVar('project', NULL);
		$segments['scope'] = Request::getVar('scope', NULL);
		if (!$segments['scope'] && is_object($project))
		{
			$segments['scope'] = 'projects/' . $project->get('alias') . '/notes';
		}
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
			case 'save':
				$segments['task'] = $type;
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
		$segments['t'] = 1;

		return Route::url($this->_base . '?' . (string) $this->_build($segments) . (string) $anchor);
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
			'alias'  => $this->_segments['alias'],
			'active' => $this->_segments['active'],
			'action' => $task
		);
	}
}
