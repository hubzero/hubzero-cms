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
