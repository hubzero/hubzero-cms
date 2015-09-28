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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Hubzero\Html\Toolbar\Button;

use Hubzero\Html\Toolbar\Button;
use Hubzero\Html\Builder\Behavior;

/**
 * Renders a standard button
 *
 * Inspired by Joomla's JButtonStandard class
 */
class Standard extends Button
{
	/**
	 * Button type
	 *
	 * @var  string
	 */
	protected $_name = 'Standard';

	/**
	 * Fetch the HTML for the button
	 *
	 * @param   string   $type  Unused string.
	 * @param   string   $name  The name of the button icon class.
	 * @param   string   $text  Button text.
	 * @param   string   $task  Task associated with the button.
	 * @param   boolean  $list  True to allow lists
	 * @return  string   HTML string for the button
	 */
	public function fetchButton($type = 'Standard', $name = '', $text = '', $task = '', $list = true)
	{
		$i18n_text = \Lang::txt($text);
		$class  = $this->fetchIconClass($name);
		$doTask = $this->_getCommand($text, $task, $list);

		$html  = "<a href=\"#\" onclick=\"$doTask\" class=\"toolbar\" data-title=\"$i18n_text\">\n";
		$html .= "<span class=\"$class\">\n";
		$html .= "$i18n_text\n";
		$html .= "</span>\n";
		$html .= "</a>\n";

		return $html;
	}

	/**
	 * Get the button CSS Id
	 *
	 * @param   string   $type      Unused string.
	 * @param   string   $name      Name to be used as apart of the id
	 * @param   string   $text      Button text
	 * @param   string   $task      The task associated with the button
	 * @param   boolean  $list      True to allow use of lists
	 * @param   boolean  $hideMenu  True to hide the menu on click
	 * @return  string   Button CSS Id
	 */
	public function fetchId($type = 'Standard', $name = '', $text = '', $task = '', $list = true, $hideMenu = false)
	{
		return $this->_parent->getName() . '-' . $name;
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @param   string   $name  The task name as seen by the user
	 * @param   string   $task  The task used by the application
	 * @param   boolean  $list  True is requires a list confirmation.
	 * @return  string   JavaScript command string
	 */
	protected function _getCommand($name, $task, $list)
	{
		Behavior::framework();

		$message = \Lang::txt('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
		$message = addslashes($message);

		if ($list)
		{
			$cmd = "if (document.adminForm.boxchecked.value==0){alert('$message');}else{ Joomla.submitbutton('$task')}";
		}
		else
		{
			$cmd = "Joomla.submitbutton('$task')";
		}

		return $cmd;
	}
}
