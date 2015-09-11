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
