<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Plugins\Content\Formathtml\Macros\Group;

require_once dirname(__DIR__) . DS . 'group.php';

use Plugins\Content\Formathtml\Macros\GroupMacro;

/**
 * Group events Macro
 */
class Redirect extends GroupMacro
{
	/**
	 * Allow macro in partial parsing?
	 *
	 * @var string
	 */
	public $allowPartial = true;

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return  array
	 */
	public function description()
	{
		return '<p>Redirects to a URL with an optional delay (in seconds).</p>
				<p>Examples:</p>
					<ul>
						<li><code>[[Group.Redirect(http://google.com)]]</code></li>
						<li><code>[[Group.Redirect(http://google.com, 5)]]</code> - Wait 5 seconds before redirecting.</li>
					</ul>';
	}

	/**
	 * Generate macro output
	 *
	 * @return  string
	 */
	public function render()
	{
		// Check if we can render
		if (!parent::canRender())
		{
			return \JText::_('[This macro is designed for Groups only]');
		}

		// Get the arguments
		$args = $this->getArgs();

		// No arguments passed? Can't do anything.
		if (empty($args))
		{
			return;
		}

		// Clean up the args
		$args = array_map('trim', $args);
		@list($url, $delay) = $args;
		$delay = intval($delay);

		// No delay time? Redirect now.
		if (!$delay)
		{
			return \JFactory::getApplication()->redirect($url);
		}

		// Delayed redirect
		return '<script type="text/javascript">setTimeout(function () { window.location.href = "' . str_replace(array("'", '"'), array('%27', '%22'), $url) . '"; }, ' . ($delay * 1000) . ');</script>
				<p class="warning">' . \JText::sprintf('This page will redirect in %s seconds', $delay) . '</p>';
	}
}

