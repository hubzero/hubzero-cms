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
 * @package   hubzero-cms
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
class Filelist extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Filelist';

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
		// path to images directory
		$path = PATH_ROOT . DS . (string) $node['directory'];

		$filter   = (string) $node['filter'];
		$exclude  = (string) $node['exclude'];
		$stripExt = (string) $node['stripext'];
		$files    = App::get('filesystem')->files($path, $filter);

		$options = array();

		if (!$node['hide_none'])
		{
			$options[] = Builder\Select::option('-1', App::get('language')->txt('JOPTION_DO_NOT_USE'));
		}

		if (!$node['hide_default'])
		{
			$options[] = Builder\Select::option('', App::get('language')->txt('JOPTION_USE_DEFAULT'));
		}

		if (is_array($files))
		{
			foreach ($files as $file)
			{
				if ($exclude)
				{
					if (preg_match(chr(1) . $exclude . chr(1), $file))
					{
						continue;
					}
				}
				if ($stripExt)
				{
					$file = App::get('filesystem')->extension($file);
				}
				$options[] = Builder\Select::option($file, $file);
			}
		}

		return Builder\Select::genericlist(
			$options,
			$control_name . '[' . $name . ']',
			array('id' => 'param' . $name, 'list.attr' => 'class="inputbox"', 'list.select' => $value)
		);
	}
}
