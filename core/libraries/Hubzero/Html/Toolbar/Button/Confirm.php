<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Toolbar\Button;

use Hubzero\Html\Toolbar\Button;
use Hubzero\Html\Builder\Behavior;

/**
 * Renders a standard button with a confirm dialog
 */
class Confirm extends Button
{
	/**
	 * Button type
	 *
	 * @var  string
	 */
	protected $_name = 'Confirm';

	/**
	 * Fetch the HTML for the button
	 *
	 * @param   string   $type      Unused string.
	 * @param   string   $msg       Message to render
	 * @param   string   $name      Name to be used as apart of the id
	 * @param   string   $text      Button text
	 * @param   string   $task      The task associated with the button
	 * @param   boolean  $list      True to allow use of lists
	 * @param   boolean  $hideMenu  True to hide the menu on click
	 * @return  string   HTML string for the button
	 */
	public function fetchButton($type = 'Confirm', $msg = '', $name = '', $text = '', $task = '', $list = true, $hideMenu = false)
	{
		$text   = \Lang::txt($text);
		$msg    = \Lang::txt($msg, true);
		$class  = $this->fetchIconClass($name);
		$message = $this->_getCommand($msg, $name, $task, $list);

		$cls = 'toolbar toolbar-confirm';

		$attr   = array();
		$attr[] = 'data-title="' . $text . '"';
		$attr[] = 'data-task="' . $task . '"';
		$attr[] = 'data-confirm="' . $msg . '"';

		if ($list)
		{
			$cls .= ' toolbar-list';

			$attr[] = ' data-message="' . $message . '"';
		}

		$html  = "<a href=\"#\" class=\"$cls\" " . implode(' ', $attr) . ">\n";
		$html .= "<span class=\"$class\">\n";
		$html .= "$text\n";
		$html .= "</span>\n";
		$html .= "</a>\n";

		return $html;
	}

	/**
	 * Get the button CSS Id
	 *
	 * @param   string   $type      Button type
	 * @param   string   $name      Name to be used as apart of the id
	 * @param   string   $text      Button text
	 * @param   string   $task      The task associated with the button
	 * @param   boolean  $list      True to allow use of lists
	 * @param   boolean  $hideMenu  True to hide the menu on click
	 * @return  string  Button CSS Id
	 */
	public function fetchId($type = 'Confirm', $name = '', $text = '', $task = '', $list = true, $hideMenu = false)
	{
		return $this->_parent->getName() . '-' . $name;
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @param   object   $msg   The message to display.
	 * @param   string   $name  Not used.
	 * @param   string   $task  The task used by the application
	 * @param   boolean  $list  True is requires a list confirmation.
	 * @return  string
	 */
	protected function _getCommand($msg, $name, $task, $list)
	{
		Behavior::framework();

		$message = \Lang::txt('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
		$message = str_replace('"', '&quot;', $message);

		return $message;
	}
}
