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

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;

/**
 * Renders a list of support ticket statuses
 */
class Ticketstate extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Ticketstate';

	/**
	 * Fetch the element
	 *
	 * @param   string  $name          Element name
	 * @param   string  $value         Element value
	 * @param   object  &$node         XMLElement node object containing the settings for the element
	 * @param   string  $control_name  Control name
	 * @return  string
	 * @since   1.3.1
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$db = \App::get('db');

		$html = array();

		$html[] = '<select name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '">';

		include_once(PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'status.php');
		$sr = new \Components\Support\Tables\Status($db);

		$status = $sr->find('list', array('sort' => 'open', 'sort_Dir' => 'DESC'));

		$html[] = '<option value=""' . ($value === '' || $value === null ? ' selected="selected"' : '') . '>--</option>';
		$html[] = '<option value="0"' . ($value === 0 || $value === '0' ? ' selected="selected"' : '') . '>open: New</option>';

		$switched = false;
		foreach ($status as $anode)
		{
			if (!$anode->open && !$switched)
			{
				$html[] = '<option value="-1"' . ($value == -1 ? ' selected="selected"' : '') . '>closed: No resolution</option>';
				$switched = true;
			}
			$html[] = '<option value="' . $anode->id . '"' . ($value == $anode->id ? ' selected="selected"' : '') . '>' . ($anode->open ? 'open: ' : 'closed: ') . stripslashes($anode->title) . '</option>';
		}

		$html[] = '</select>';

		return implode("\n", $html);
	}
}
