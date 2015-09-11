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
