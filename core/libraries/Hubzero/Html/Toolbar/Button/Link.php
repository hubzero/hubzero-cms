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
 * Renders a link button
 *
 * Inspired by Joomla's JButtonLink class
 */
class Link extends Button
{
	/**
	 * Button type
	 *
	 * @var  string
	 */
	protected $_name = 'Link';

	/**
	 * Fetch the HTML for the button
	 *
	 * @param   string  $type  Unused string.
	 * @param   string  $name  Name to be used as apart of the id
	 * @param   string  $text  Button text
	 * @param   string  $url   The link url
	 * @return  string  HTML string for the button
	 */
	public function fetchButton($type = 'Link', $name = 'back', $text = '', $url = null)
	{
		$text   = \Lang::txt($text);
		$class  = $this->fetchIconClass($name);
		$doTask = $this->_getCommand($url);

		$html  = "<a data-title=\"$text\" href=\"$doTask\">\n";
		$html .= "<span class=\"$class\">\n";
		$html .= "$text\n";
		$html .= "</span>\n";
		$html .= "</a>\n";

		return $html;
	}

	/**
	 * Get the button CSS Id
	 *
	 * @param   string  $type  The button type.
	 * @param   string  $name  The name of the button.
	 * @return  string  Button CSS Id
	 */
	public function fetchId($type = 'Link', $name = '')
	{
		return $this->_parent->getName() . '-' . $name;
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @param   object  $url  Button definition
	 * @return  string  JavaScript command string
	 */
	protected function _getCommand($url)
	{
		return $url;
	}
}
