<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Hubzero\Document\Type\Html;

use Hubzero\Document\Renderer;

/**
 * System message renderer
 *
 * Inspired by Joomla's JDocumentRendererMessage class
 */
class Message extends Renderer
{
	/**
	 * Renders the error stack and returns the results as a string
	 *
	 * @param   string  $name     Not used.
	 * @param   array   $params   Associative array of values
	 * @param   string  $content  Not used.
	 * @return  string  The output of the script
	 */
	public function render($name, $params = array (), $content = null)
	{
		// Initialise variables.
		$buffer = array();
		$lists  = array();

		// Get the message queue
		$messages = \App::get('notification')->messages();

		// Build the sorted message list
		if (is_array($messages) && !empty($messages))
		{
			foreach ($messages as $msg)
			{
				if (isset($msg['type']) && isset($msg['message']))
				{
					$lists[$msg['type']][] = $msg['message'];
				}
			}
		}

		$lnEnd = $this->doc->_getLineEnd();
		$tab   = $this->doc->_getTab();

		// Build the return string
		$buffer[] = '<div id="system-message-container">';

		// If messages exist render them
		if (!empty($lists))
		{
			$buffer[] = $tab . '<dl id="system-message">';
			foreach ($lists as $type => $msgs)
			{
				if (count($msgs))
				{
					$buffer[] = $tab . $tab . '<dt class="' . strtolower($type) . '">' . \App::get('language')->txt($type) . '</dt>';
					$buffer[] = $tab . $tab . '<dd class="' . strtolower($type) . ' message">';
					$buffer[] = $tab . $tab . $tab . '<ul>';
					foreach ($msgs as $msg)
					{
						$buffer[] = $tab . $tab . $tab . $tab . '<li>' . $msg . '</li>';
					}
					$buffer[] = $tab . $tab . $tab . '</ul>';
					$buffer[] = $tab . $tab . '</dd>';
				}
			}
			$buffer[] = $tab . '</dl>';
		}

		$buffer[] = '</div>';

		return implode($lnEnd, $buffer);
	}
}
