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

namespace Components\Content\Admin\Helpers\Html;

use Hubzero\Utility\Arr;
use Lang;

/**
 * HTML helper
 */
abstract class ContentAdministrator
{
	/**
	 * @param   int    $value  The state value
	 * @param   int    $i
	 * @param   bool   $canChange
	 * @return  string
	 */
	static function featured($value = 0, $i, $canChange = true)
	{
		// Array of image, task, title, action
		$states	= array(
			0 => array(
				'disabled.png',
				'articles.featured',
				'COM_CONTENT_UNFEATURED',
				'COM_CONTENT_TOGGLE_TO_FEATURE'
			),
			1 => array(
				'featured.png',
				'articles.unfeatured',
				'COM_CONTENT_FEATURED',
				'COM_CONTENT_TOGGLE_TO_UNFEATURE'
			),
		);
		$state = Arr::getValue($states, (int) $value, $states[1]);
		$html  = \Html::asset('image', 'admin/'.$state[0], Lang::txt($state[2]), null, true);
		if ($canChange)
		{
			$html = '<a href="#" class="state ' . ($value ? 'yes' : 'no') . '" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.Lang::txt($state[3]).'">'. $html.'</a>';
		}

		return $html;
	}
}
