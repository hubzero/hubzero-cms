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
use Hubzero\Html\Builder;
use App;

/**
 * Renders a list of template styles.
 */
class TemplateStyle extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'TemplateStyle';

	/**
	 * Fetch a calendar element
	 *
	 * @param   string  $name          Element name
	 * @param   string  $value         Element value
	 * @param   object  &$node         XMLElement node object containing the settings for the element
	 * @param   string  $control_name  Control name
	 * @return  string
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$db = \App::get('db');

		$query = 'SELECT * FROM `#__template_styles` ' . 'WHERE client_id = 0 ' . 'AND home = 0';
		$db->setQuery($query);
		$data = $db->loadObjectList();

		$default = Builder\Select::option(0, App::get('language')->txt('JOPTION_USE_DEFAULT'), 'id', 'description');
		array_unshift($data, $default);

		$selected = $this->_getSelected();
		$html = Builder\Select::genericlist($data, $control_name . '[' . $name . ']', 'class="inputbox" size="6"', 'id', 'description', $selected);

		return $html;
	}

	/**
	 * Get the selected template style.
	 *
	 * @return  integer  The template style id.
	 */
	protected function _getSelected()
	{
		$id = App::get('request')->getVar('cid', 0);

		$db = \App::get('db');
		$query = $db->getQuery(true);
		$query->select($query->qn('template_style_id'))->from($query->qn('#__menu'))->where($query->qn('id') . ' = ' . (int) $id[0]);
		$db->setQuery($query);

		return $db->loadResult();
	}
}
