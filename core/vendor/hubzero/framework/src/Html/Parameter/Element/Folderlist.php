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
 * Renders a filelist element
 */
class Folderlist extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Folderlist';

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
		// Initialise variables.
		$path = PATH_ROOT . DS . (string) $node['directory'];

		$filter  = (string) $node['filter'];
		$exclude = (string) $node['exclude'];
		$folders = App::get('filesystem')->folders($path, $filter);

		$options = array();
		foreach ($folders as $folder)
		{
			if ($exclude)
			{
				if (preg_match(chr(1) . $exclude . chr(1), $folder))
				{
					continue;
				}
			}
			$options[] = Builder\Select::option($folder, $folder);
		}

		if (!$node['hide_none'])
		{
			array_unshift($options, Builder\Select::option('-1', App::get('language')->txt('JOPTION_DO_NOT_USE')));
		}

		if (!$node['hide_default'])
		{
			array_unshift($options, Builder\Select::option('', App::get('language')->txt('JOPTION_USE_DEFAULT')));
		}

		return Builder\Select::genericlist(
			$options,
			$control_name . '[' . $name . ']',
			array('id' => 'param' . $name, 'list.attr' => 'class="inputbox"', 'list.select' => $value)
		);
	}
}
