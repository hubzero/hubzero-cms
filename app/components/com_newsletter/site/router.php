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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Site;

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

		if (!empty($query['id']))
		{
			$database = \App::get('db');
			$sql = "SELECT `alias` FROM `#__newsletters` WHERE `id`=" . $database->quote( $query['id'] );
			$database->setQuery($sql);
			$campaign = $database->loadResult();
			$segments[] = strtolower(str_replace(" ", "", $campaign));
			unset($query['id']);
		}

		if (!empty($query['task']))
		{
			if (in_array($query['task'], array('subscribe', 'unsubscribe', 'resendconfirmation')))
			{
				$segments[] = $query['task'];
				unset($query['task']);
			}
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
			$database = \App::get('db');
			$sql = "SELECT `id` FROM `#__newsletters` WHERE `alias`=" . $database->quote($segments[0]);
			$database->setQuery($sql);
			$campaignId = $database->loadResult();

			if ($campaignId)
			{
				$vars['id'] = $campaignId;
			}
			else
			{
				switch ($segments[0])
				{
					case 'track':
						$vars['task'] = 'track';
						$vars['type'] = $segments[1];
						$vars['controller'] = 'mailings';
						break;
					case 'confirm':
					case 'remove':
					case 'subscribe':
					case 'dosubscribe':
					case 'unsubscribe':
					case 'dounsubscribe':
					case 'resendconfirmation':
						$vars['task'] = $segments[0];
						$vars['controller'] = 'mailinglists';
						break;
					default:
						$vars['task'] = $segments[0];
				}
			}
		}

		return $vars;
	}
}
