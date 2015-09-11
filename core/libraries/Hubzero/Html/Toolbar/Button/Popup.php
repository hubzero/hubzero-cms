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
use Hubzero\Html\Builder\Behavior;

/**
 * Renders a popup window button
 *
 * Inspired by Joomla's JButtonPopup class
 */
class Popup extends Button
{
	/**
	 * Button type
	 *
	 * @var    string
	 */
	protected $_name = 'Popup';

	/**
	 * Fetch the HTML for the button
	 *
	 * @param   string   $type     Unused string, formerly button type.
	 * @param   string   $name     Button name
	 * @param   string   $text     The link text
	 * @param   string   $url      URL for popup
	 * @param   integer  $width    Width of popup
	 * @param   integer  $height   Height of popup
	 * @param   integer  $top      Top attribute.
	 * @param   integer  $left     Left attribute
	 * @param   string   $onClose  JavaScript for the onClose event.
	 * @return  string   HTML string for the button
	 */
	public function fetchButton($type = 'Popup', $name = '', $text = '', $url = '', $width = 640, $height = 480, $top = 0, $left = 0, $onClose = '')
	{
		Behavior::modal();

		$text   = \Lang::txt($text);
		$class  = $this->fetchIconClass($name);
		$doTask = $this->_getCommand($name, $url, $width, $height, $top, $left);

		$html  = "<a data-title=\"$text\" class=\"modal\" href=\"$doTask\" rel=\"{size: {width: $width, height: $height}, onClose: function() {" . $onClose . "}}\">\n";
		$html .= "<span class=\"$class\">\n";
		$html .= "$text\n";
		$html .= "</span>\n";
		$html .= "</a>\n";

		return $html;
	}

	/**
	 * Get the button id
	 *
	 * @param   string  $type  Button type
	 * @param   string  $name  Button name
	 * @return  string	Button CSS Id
	 */
	public function fetchId($type, $name)
	{
		return $this->_parent->getName() . '-popup-' . $name;
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @param   string   $name    Button name
	 * @param   string   $url     URL for popup
	 * @param   integer  $width   Unused formerly width.
	 * @param   integer  $height  Unused formerly height.
	 * @param   integer  $top     Unused formerly top attribute.
	 * @param   integer  $left    Unused formerly left attribure.
	 * @return  string   JavaScript command string
	 */
	protected function _getCommand($name, $url, $width, $height, $top, $left)
	{
		if (substr($url, 0, 4) !== 'http')
		{
			$url = rtrim(\Request::base(), '/') . '/' . ltrim($url, '/');
		}

		return $url;
	}
}
