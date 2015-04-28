<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Resources\Models\Element;

use Components\Resources\Models\Element as Base;

/**
 * Renders a textarea element
 */
class Textarea extends Base
{
	/**
	* Element name
	*
	* @var  string
	*/
	protected $_name = 'Textarea';

	/**
	 * Return any options this element may have
	 *
	 * @param   string  $name          Name of the field
	 * @param   string  $value         Value to check against
	 * @param   object  $element       Data Source Object.
	 * @param   string  $control_name  Control name (eg, control[fieldname])
	 * @return  string  HTML
	 */
	public function fetchElement($name, $value, &$element, $control_name)
	{
		$rows = isset($element->rows) ? $element->rows : 6;
		$cols = isset($element->cols) ? $element->cols : 50;

		$cls = array();
		if (isset($element->class))
		{
			$cls[] = $element->class;
		}
		$cls[] = (\App::isAdmin() ? 'no-footer' : 'minimal no-footer');

		// convert <br /> tags so they are not visible when editing
		$value = str_replace('<br />', "\n", $value);

		return '<span class="field-wrap">' . \JFactory::getEditor()->display($control_name . '[' . $name . ']', $value, '', '', $cols, $rows, false, $control_name . '-' . $name, null, null, array('class' => implode(' ', $cls))) . '</span>';
	}
}