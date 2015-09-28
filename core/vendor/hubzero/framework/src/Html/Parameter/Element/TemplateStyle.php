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
