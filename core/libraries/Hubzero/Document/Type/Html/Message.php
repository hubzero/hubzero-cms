<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
