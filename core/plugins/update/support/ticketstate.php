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

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;

/**
 * Renders a list of support ticket statuses
 */
class Ticketstate extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Ticketstate';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$value = $this->value;
		$name  = $this->name;
		$id    = $this->id;

		$html = array();

		$html[] = '<select name="' . $name . '" id="' . $id . '">';

		include_once \Component::path('com_support') . DS . 'models' . DS . 'status.php';

		$status = \Components\Support\Models\Status::all()
			->order('open', 'desc')
			->rows();

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
