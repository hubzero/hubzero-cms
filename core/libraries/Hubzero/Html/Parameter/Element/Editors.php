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

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;
use Hubzero\Html\Builder;

/**
 * Renders a editors element
 */
class Editors extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Editors';

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
		// compile list of the editors
		$query = 'SELECT `element` AS `value`, `name` AS `text` FROM `#__extensions` WHERE folder = "editors" AND type = "plugin" AND enabled = 1 ORDER BY ordering, name';
		$db = \App::get('db');
		$db->setQuery($query);
		$editors = $db->loadObjectList();

		array_unshift($editors, Builder\Select::option('', \App::get('language')->txt('JOPTION_SELECT_EDITOR')));

		return Builder\Select::genericlist(
			$editors,
			$control_name . '[' . $name . ']',
			array(
				'id'          => $control_name . $name,
				'list.attr'   => 'class="inputbox"',
				'list.select' => $value
			)
		);
	}
}
