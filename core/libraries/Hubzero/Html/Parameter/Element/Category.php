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

/**
 * Renders a category element
 */
class Category extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Category';

	/**
	 * Fetch the element
	 *
	 * @param   string  $name          Element name
	 * @param   string  $value         Element value
	 * @param   object  &$node         XMLElement node object containing the settings for the element
	 * @param   string  $control_name  Control name
	 * @return  string  HTML string for a calendar
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$db = \App::get('db');

		$extension = (string) $node['extension'];
		$class     = (string) $node['class'];
		$filter    = explode(',', (string) $node['filter']);

		if (!isset($extension))
		{
			// Alias for extension
			$extension = (string) $node['scope'];
			if (!isset($extension))
			{
				$extension = 'com_content';
			}
		}

		if (!$class)
		{
			$class = "inputbox";
		}

		if (count($filter) < 1)
		{
			$filter = null;
		}

		return \JHtml::_(
			'list.category',
			$control_name . '[' . $name . ']',
			$extension,
			$extension . '.view',
			$filter,
			(int) $value,
			$class,
			null,
			1,
			$control_name . $name
		);
	}
}
