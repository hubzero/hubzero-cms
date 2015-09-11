<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Html\Toolbar\Button;

use Hubzero\Html\Toolbar\Button;

/**
 * Renders a help popup window button
 *
 * Inspired by Joomla's JButtonHelp class
 */
class Help extends Button
{
	/**
	 * Button type
	 *
	 * @var  string
	 */
	protected $_name = 'Help';

	/**
	 * Fetches the button HTML code.
	 *
	 * @param   string   $type       Unused string.
	 * @param   string   $ref        The name of the help screen (its key reference).
	 * @param   boolean  $com        Use the help file in the component directory.
	 * @param   string   $override   Use this URL instead of any other.
	 * @param   string   $component  Name of component to get Help (null for current component)
	 * @return  string
	 */
	public function fetchButton($type = 'Help', $url = null, $width = 700, $height = 500)
	{
		$text   = \Lang::txt('JTOOLBAR_HELP');
		$class  = $this->fetchIconClass('help');
		if (!strstr('?', $url) && !strstr('&', $url) && substr($url, 0, 4) != 'http')
		{
			$url = \Route::url('index.php?option=com_help&component=' . \Request::getCmd('option') . '&page=' . $url);
			$doTask = "Joomla.popupWindow('$url', '" . \Lang::txt('JHELP', true) . "', {$width}, {$height}, 1)";
		}
		else
		{
			$doTask = $this->_getCommand($ref, $com, $override, $component);
		}

		$html  = '<a data-title="' . $text . '" href="#" onclick="' . $doTask . '" rel="help" class="toolbar">' . "\n";
		$html .= '<span class="' . $class . '">' . "\n" . $text . "\n" . '</span>' . "\n";
		$html .= '</a>' . "\n";

		return $html;
	}

	/**
	 * Get the button id
	 *
	 * @return  string	Button CSS Id
	 */
	public function fetchId()
	{
		return $this->_parent->getName() . '-' . 'help';
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @param   string   $ref        The name of the help screen (its key reference).
	 * @param   boolean  $com        Use the help file in the component directory.
	 * @param   string   $override   Use this URL instead of any other.
	 * @param   string   $component  Name of component to get Help (null for current component)
	 * @return  string   JavaScript command string
	 */
	protected function _getCommand($ref, $com, $override, $component)
	{
		// Get Help URL
		jimport('joomla.language.help');
		$url = \JHelp::createURL($ref, $com, $override, $component);
		$url = htmlspecialchars($url, ENT_QUOTES);
		$cmd = "Joomla.popupWindow('$url', '" . \Lang::txt('JHELP', true) . "', 700, 500, 1)";

		return $cmd;
	}
}
