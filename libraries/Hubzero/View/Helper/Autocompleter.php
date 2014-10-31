<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\View\Helper;

/**
 * Instantiate and return a form field for autocompleting some value
 */
class Autocompleter extends AbstractHelper
{
	/**
	 * Output the autocompleter
	 *
	 * @param   string  $what   The component to call
	 * @param   string  $name   Name of the input field
	 * @param   string  $value  The value of the input field
	 * @param   string  $id     ID of the input field
	 * @param   string  $class  CSS class(es) for the input field
	 * @param   string  $size   The size of the input field
	 * @param   string  $wsel   AC autopopulates a select list based on choice?
	 * @param   string  $type   Allow single or multiple entries
	 * @param   string  $dsabl  Readonly input
	 * @return  string
	 * @throws  \InvalidArgumentException  If wrong type passed
	 */
	public function __invoke($what=null, $name=null, $value=null, $id=null, $class=null, $size=null, $wsel=false, $type='multi', $dsabl=false)
	{
		if (!in_array($what, array('tags', 'members', 'groups')))
		{
			throw new \InvalidArgumentException(__METHOD__ . '(); ' . \JText::sprintf('Autocompleter for "%s" not supported.', $what));
		}

		$id = ($id ?: str_replace(array('[', ']'), '', $name));

		switch ($type)
		{
			case 'multi':
				$event = 'onGetMultiEntry';
			break;
			case 'single':
				$event = 'onGetSingleEntry';
				if ($wsel)
				{
					$event = 'onGetSingleEntryWithSelect';
				}
			break;
			default:
				throw new \InvalidArgumentException(__METHOD__ . '(); ' . \JText::sprintf('Autocompleter type "%s" not supported.', $type));
			break;
		}

		\JPluginHelper::importPlugin('hubzero');
		$results = \JDispatcher::getInstance()->trigger(
			$event,
			array(
				array($what, $name, $id, $class, $value, $size, $wsel, $type, $dsabl)
			)
		);

		if (count($results) > 0)
		{
			$results = implode("\n", $results);
		}
		else
		{
			$results = '<input type="text" name="' . $name . '" id="' . $id . '" value="' . $value . '" />';
		}

		return $results;
	}
}
