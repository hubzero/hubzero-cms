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

namespace Components\Courses\Site;

use Hubzero\Component\Router\Base;

/**
 * Routing class for the component
 */
class Router extends Base
{
	/**
	 * Build the route for the component.
	 *
	 * @param   array  &$query  An array of URL arguments
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 */
	public function build(&$query)
	{
		$segments = array();

		if (!empty($query['controller']))
		{
			if ($query['controller'] == 'certificate')
			{
				$segments[] = $query['controller'];
			}
			unset($query['controller']);
		}

		if (!empty($query['gid']))
		{
			$segments[] = $query['gid'];
			unset($query['gid']);
		}
		if (!empty($query['offering']))
		{
			$segments[] = $query['offering'];
			unset($query['offering']);
		}
		if (!empty($query['active']))
		{
			$segments[] = $query['active'];
			if ($query['active'] == '' && !empty($query['task']))
			{
				$segments[] = $query['task'];
				unset($query['task']);
			}
			unset($query['active']);
		}
		elseif (!empty($query['asset']))
		{
			$segments[] = 'asset';
			$segments[] = $query['asset'];
			unset($query['asset']);
		}
		else
		{
			if ((empty($query['scope']) || $query['scope'] == '') && !empty($query['task']))
			{
				$segments[] = $query['task'];
				unset($query['task']);
			}
		}
		if (!empty($query['unit']))
		{
			$segments[] = $query['unit'];
			unset($query['unit']);
		}
		if (!empty($query['b']))
		{
			$segments[] = $query['b'];
			unset($query['b']);
		}
		if (!empty($query['c']))
		{
			$segments[] = $query['c'];
			unset($query['c']);
		}

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 * @return  array  The URL attributes to be used by the application.
	 */
	public function parse(&$segments)
	{
		$vars = array();

		if (empty($segments))
		{
			return $vars;
		}

		if (isset($segments[0]))
		{
			if (in_array($segments[0], array('intro', 'browse', 'badge')))
			{
				$vars['controller'] = 'courses';
				$vars['task'] = $segments[0];

				if ($segments[0] == 'badge' && isset($segments[1]) && is_numeric($segments[1]))
				{
					$vars['badge_id'] = $segments[1];

					if (in_array($segments[2], array('image', 'criteria', 'validation')))
					{
						$vars['action'] = $segments[2];

						if ($segments[2] == 'validation' && isset($segments[3]))
						{
							$vars['validation_token'] = $segments[3];
						}
					}
					return $vars;
				}
			}
			else if ($segments[0] == 'certificate')
			{
				$vars['controller'] = $segments[0];
				if (isset($segments[1]))
				{
					$vars['course'] = $segments[1];
				}
				if (isset($segments[2]))
				{
					$vars['offering'] = $segments[2];
				}
				return $vars;
			}
			else
			{
				if ($segments[0] == 'new')
				{
					$vars['task'] = $segments[0];
				}
				else
				{
					$vars['gid'] = $segments[0];
					$vars['task'] = 'display';
				}
				$vars['controller'] = 'course';
			}
		}

		if (isset($segments[1]))
		{
			$vars['controller'] = 'course';
			switch ($segments[1])
			{
				case 'overview':
				case 'reviews':
				case 'offerings':
				case 'faq':
					$vars['active'] = $segments[1];
				break;

				case 'logo':
				case 'edit':
				case 'newoffering':
				case 'saveoffering':
				case 'deletepage':
					$vars['task'] = $segments[1];
				break;

				case 'instructors':
					$vars['controller'] = 'managers';
				break;

				case 'delete':
				case 'join':
				case 'accept':
				case 'cancel':
				case 'invite':
				case 'customize':
				case 'manage':
					if (isset($segments[2]))
					{
						$vars['task'] = 'editoutline';
						$vars['offering'] = $segments[2];
					}
					$vars['controller'] = 'offering';
					if (isset($segments[3]))
					{
						$vars['task'] = 'manage';
						$vars['controller'] = $segments[3];
					}
					return $vars;
				break;

				case 'editoutline':
				case 'offerings':
				//case 'managemodules':
				case 'ajaxupload':
					$vars['task'] = $segments[1];
					$vars['controller'] = 'media';
				break;

				// Defaults
				default:
					$pagefound = false;
					require_once(dirname(__DIR__) . DS . 'models' . DS . 'course.php');
					$course = \Components\Courses\Models\Course::getInstance($vars['gid']);
					if ($course->exists())
					{
						$pages = $course->pages(array('active' => 1));

						foreach ($pages as $page)
						{
							if ($page->get('url') == $segments[1])
							{
								$pagefound = true;
								$vars['active'] = $segments[1];
								break;
							}
						}
					}

					if (!$pagefound)
					{
						$vars['offering'] = $segments[1];
						$vars['controller'] = 'offering';
					}
				break;
			}
		}

		if (isset($segments[2]))
		{
			if ($segments[2] == 'form.index'
				|| $segments[2] == 'form.layout'
				|| $segments[2] == 'form.saveLayout'
				|| $segments[2] == 'form.upload'
				|| $segments[2] == 'form.deploy'
				|| $segments[2] == 'form.showDeployment'
				|| $segments[2] == 'form.complete')
			{
				$vars['controller'] = 'form';
				$vars['task']       = substr($segments[2], 5);
			}
			elseif ($segments[2] == 'asset' && isset($segments[3]) && is_numeric($segments[3]))
			{
				$vars['controller'] = 'offering';
				$vars['task']       = 'asset';
				$vars['asset_id']   = $segments[3];

				if (isset($segments[4]))
				{
					$vars['file'] = $segments[4];
				}
			}
			else if ($vars['controller'] == 'course' && isset($vars['active']))
			{
				$vars['task'] = 'download';
				$vars['file'] = $segments[2];
			}
			else
			{
				if ($segments[2] == 'enroll' || $segments[2] == 'logo')
				{
					$vars['task'] = $segments[2];
				}
				else
				{
					$vars['active'] = $segments[2];
				}
				$vars['controller'] = 'offering';
			}
		}
		if (isset($segments[3]))
		{
			$vars['unit'] = $segments[3];
		}
		if (isset($segments[4]))
		{
			$vars['group'] = $segments[4];
		}
		if (isset($segments[5]))
		{
			$vars['asset'] = $segments[5];
		}
		if (isset($segments[6]))
		{
			$vars['d'] = $segments[6];
		}

		return $vars;
	}
}
