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

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;

/**
 * Renders a spacer element
 */
class Spacer extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Spacer';

	/**
	 * Fetch tooltip for a radio button
	 *
	 * @param   string  $label         Element label
	 * @param   string  $description   Element description for tool tip
	 * @param   object  &$node         XMLElement node object containing the settings for the element
	 * @param   string  $control_name  Control name
	 * @param   string  $name          The name.
	 * @return  string
	 */
	public function fetchTooltip($label, $description, &$node, $control_name, $name)
	{
		return '&#160;';
	}

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
		if ($value)
		{
			return \App::get('language')->txt($value);
		}

		return ' ';
	}
}
