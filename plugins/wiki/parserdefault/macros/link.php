<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * A wiki macro for embedding links
 */
class LinkMacro extends WikiMacro
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
	 * @return     string
	 */
	public function description()
	{
		$txt = array();
		$txt['wiki'] = "Embed a link.

Examples:

{{{
[[Link(/SomePage SomePage)]]
}}}
";
		$txt['html'] = '<p>Embed a link.</p>
<p>Examples:</p>
<ul>
<li><code>[[Link(/SomePage SomePage)]]</code></li>
</ul>';

		return $txt['html'];
	}

	/**
	 * Generate macro output based on passed arguments
	 *
	 * @return     string HTML image tag on success or error message on failure
	 */
	public function render()
	{
		$content = $this->args;

		// args will be null if the macro is called without parenthesis.
		if (!$content)
		{
			return '';
		}

		$cls = 'wiki';

		// Parse arguments
        // We expect the 1st argument to be a filename
		$args  = explode(' ', $content);
		$href  = array_shift($args);
		$title = (count($args) > 0) ? implode(' ', $args) : $href;

		$title = preg_replace('/\(.*?\)/', '', $title);
		$title = preg_replace('/^.*?\:/', '', $title);

		return '<a class="' . $cls . '" href="' . $href . '">' . trim($title) . '</a>';
	}
}

