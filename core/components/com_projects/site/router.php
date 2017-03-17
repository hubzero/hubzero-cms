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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Site;

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
		$scope = 0;

		if (!empty($query['controller']))
		{
			$segments[] = $query['controller'];
			unset($query['controller']);
		}
		if (!empty($query['alias']))
		{
			$segments[] = $query['alias'];
			unset($query['alias']);
		}
		if (!empty($query['id']))
		{
			$segments[] = $query['id'];
			unset($query['id']);
		}
		if (!empty($query['task']))
		{
			if (empty($query['scope']))
			{
				$segments[] = $query['task'];
				unset($query['task']);
			}
		}
		if (!empty($query['active']))
		{
			$segments[] = $query['active'];
			unset($query['active']);
		}
		if (!empty($query['pid']))
		{
			$segments[] = $query['pid'];
			unset($query['pid']);
		}
		// Publications
		if (!empty($query['section']))
		{
			$segments[] = $query['section'];
			unset($query['section']);
		}
		if (!empty($query['move']))
		{
			$segments[] = $query['move'];
			unset($query['move']);
		}
		if (!empty($query['step']))
		{
			$segments[] = $query['step'];
			unset($query['step']);
		}
		if (!empty($query['tool']))
		{
			$segments[] = $query['tool'];
			unset($query['tool']);
		}
		if (!empty($query['scope']))
		{
			// For wiki routing
			$segments = array();
			$scope = 1;
			$parts = explode ( '/', $query['scope'] );
			if (count($parts) >= 3)
			{
				$segments[] = $parts[1]; // alias
				$segments[] = 'notes'; // active

				for ( $i = 3; $i < count($parts); $i++ )
				{
					$segments[] = $parts[$i]; // inlcude parent page names
				}
			}
			unset($query['scope']);
		}
		if (!empty($query['pagename']))
		{
			$segments[] = $query['pagename'];
			unset($query['pagename']);
		}
		if (!empty($query['action']))
		{
			$segments[] = $query['action'];
			unset($query['action']);
		}
		elseif ($scope == 1)
		{
			$segments[] = !empty($query['task']) ? $query['task'] : ''; // wiki action
			if (!empty($query['task']))
			{
				unset($query['task']);
			}
		}
		if (!empty($query['media']))
		{
			$segments[] = $query['media'];
			unset($query['media']);
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
		$vars  = array();

		// General project tasks
		$tasks = array(
			'edit', 'browse', 'intro',
			'features', 'auth', 'delete',
			'fixownership', 'suspend', 'reinstate',
			'process', 'activate'
		);

		// Media tasks
		$mediaTasks = array( 'deleteimg', 'media', 'thumb', 'doajaxupload' );

		// Tasks managed by Setup controller
		$setupTasks = array( 'setup', 'start', 'edit', 'save', 'verify', 'suggestalias' );

		if (empty($segments[0]))
		{
			return $vars;
		}

		// Id?
		if (is_numeric($segments[0]))
		{
			$vars['id'] = $segments[0];

			if (empty($segments[1]))
			{
				$vars['task'] = 'view';
				return $vars;
			}
		}

		// Alias?
		if (!is_numeric($segments[0]))
		{
			if ($segments[0] == 'get')
			{
				$vars['controller'] = 'get';
				return $vars;
			}
			if ($segments[0] == 'reports')
			{
				$vars['controller'] = 'reports';
				if (!empty($segments[1]))
				{
					$vars['task'] = $segments[1];
				}
				return $vars;
			}
			elseif (in_array($segments[0], $setupTasks))
			{
				$vars['controller'] = 'setup';
				$vars['task'] = $segments[0];
				return $vars;
			}
			elseif (in_array($segments[0], $tasks))
			{
				$vars['task'] = $segments[0];
				if (!empty($segments[1]))
				{
					$vars['action']  = $segments[1];
				}
				return $vars;
			}
			elseif ($segments[0] == 'media')
			{
				$vars['task'] = 'media';
				$vars['controller'] = 'media';
				if (!empty($segments[1]))
				{
					$vars['alias']  = $segments[1];
				}
				if (!empty($segments[2]))
				{
					$vars['media']  = $segments[2];
				}

				return $vars;
			}
			else
			{
				// Project page view
				$vars['alias']  = $segments[0];
				if (empty($segments[1]))
				{
					$vars['task'] = 'view';
					return $vars;
				}
			}
		}

		if (!empty($segments[1]))
		{
			if ($segments[1] == 'view')
			{
				$vars['task'] = $segments[1];
				if (!empty($segments[2]))
				{
					$vars['active'] = $segments[2];
				}
				return $vars;
			}
			elseif (in_array($segments[1], $setupTasks))
			{
				$vars['controller'] = 'setup';
				$vars['task'] = $segments[1];
				if (!empty($segments[2]))
				{
					$vars['active'] = $segments[2];
				}
				if (isset($segments[3]))
				{
					$vars['action'] = $segments[3];
				}

				return $vars;
			}
			elseif (in_array($segments[1], $tasks))
			{
				$vars['task'] = $segments[1];
				return $vars;
			}
			elseif (in_array($segments[1], $mediaTasks))
			{
				$vars['controller'] = 'media';
				$vars['task'] = $segments[1];
				if (!empty($segments[2]))
				{
					$vars['media'] = $segments[2];
				}

				return $vars;
			}
			else
			{
				$vars['active'] = $segments[1];
				$vars['task'] = 'view';

				// Publications
				if (!empty($segments[2]) && $vars['active'] == 'publications')
				{
					if (is_numeric($segments[2]))
					{
						$vars['pid'] = $segments[2];
						$blocks = array();

						if (is_file(PATH_CORE . DS . 'components'
							. DS . 'com_publications' . DS . 'tables' . DS . 'block.php'))
						{
							include_once(PATH_CORE . DS . 'components'
								. DS . 'com_publications' . DS . 'tables' . DS . 'block.php');
							$database = \App::get('db');

							$b = new \Components\Publications\Tables\Block($database);
							$blocks = $b->getBlocks('block');
							$blocks[] = 'status';
						}

						if (!empty($segments[3]) && in_array($segments[3], $blocks))
						{
							$vars['section'] = $segments[3];

							if (!empty($segments[4]) && $segments[4] == 'continue')
							{
								$vars['move'] = $segments[4];

								if (!empty($segments[5]))
								{
									if (is_numeric($segments[5]))
									{
										$vars['step'] = $segments[5];

										if (!empty($segments[6]))
										{
											$vars['action'] = $segments[6];
										}
									}
									else
									{
										$vars['action'] = $segments[5];
									}
								}
							}
							elseif (!empty($segments[4]))
							{
								if (is_numeric($segments[4]))
								{
									$vars['step'] = $segments[4];

									if (!empty($segments[5]))
									{
										$vars['action'] = $segments[5];
									}
								}
								else
								{
									$vars['action'] = $segments[4];
								}
							}
						}
						else
						{
							if (!empty($segments[3]))
							{
								$vars['action'] = $segments[3];
							}
						}
					}
					else
					{
						$vars['action'] = $segments[2];
					}
					return $vars;
				}

				// Tools
				if (!empty($segments[2]) && $vars['active'] == 'tools')
				{
					// App actions
					$appActions = array('status', 'history', 'wiki', 'browse',
						'edit', 'start', 'save', 'register', 'attach', 'source',
						'cancel', 'update', 'message', 'verify', 'addimage'
					);
					if (in_array( $segments[2], $appActions ))
					{
						$vars['action'] = $segments[2];
					}
					else
					{
						$vars['tool'] = $segments[2];
						$vars['action'] = 'status';
					}
					if (!empty($segments[3]) && in_array( $segments[3], $appActions ))
					{
						$vars['action'] = $segments[3];
					}
					return $vars;
				}

				// Notes
				elseif (!empty($segments[2]) && $vars['active'] == 'notes') //!is_numeric($segments[2]) && 
				{
					// Wiki actions
					$wiki_actions = array('media', 'list', 'upload',
						'deletefolder', 'deletefile', 'view',
						'new', 'edit', 'save', 'cancel',
						'delete', 'deleteversion', 'approve',
						'rename', 'saverename', 'history',
						'compare', 'comments', 'editcomment',
						'addcomment', 'savecomment', 'removecomment',
						'reportcomment', 'deleterevision', 'pdf'
					);

					$remaining = array_slice($segments, 2);
					$action = array_pop($remaining);
					$pagename = '';

					if (in_array( $action, $wiki_actions ))
					{
						$vars['action'] = $action;
						$pagename = array_pop($remaining);
					}
					else
					{
						$vars['action'] = 'view';
						$pagename = $action;
					}
					$vars['pagename'] = $pagename;

					// Collect scope
					if (isset($vars['alias']))
					{
						if (count($remaining) > 0)
						{
							$scope = 'projects' . DS . $vars['alias'] . DS . 'notes';

							for ( $i = 0; $i < count($remaining); $i++ )
							{
								$scope .= DS . $remaining[$i]; // inlcude parent page names
							}
							if ($vars['action'] == 'new')
							{
								$scope .= DS . $pagename;
							}
							$vars['scope'] = $scope;
						}
						elseif ($vars['action'] == 'new')
						{
							$scope = 'projects' . DS . $vars['alias'] . DS . 'notes' . DS . $pagename;
							$vars['scope'] = $scope;
						}
					}

					return $vars;
				}

				// Links
				if (!empty($segments[2]) && $vars['active'] == 'links')
				{
					if (!empty($segments[2]) && is_numeric($segments[2]))
					{
						$vars['pid'] = $segments[2];
					}
					if (!empty($segments[3]))
					{
						$vars['action'] = $segments[3];
					}
				}
				// Team
				if (!empty($segments[2]) && $vars['active'] == 'team')
				{
					if (!empty($segments[2]) && is_numeric($segments[2]))
					{
						$vars['pid'] = $segments[2];
						if (!empty($segments[3]))
						{
							$vars['action'] = $segments[3];
						}
					}
					else
					{
						$vars['action'] = $segments[2];
					}
				}

				// All other plugins
				elseif (!empty($segments[2]) && !is_numeric($segments[2]))
				{
					$vars['action'] = $segments[2];
				}
			}
		}

		return $vars;
	}
}