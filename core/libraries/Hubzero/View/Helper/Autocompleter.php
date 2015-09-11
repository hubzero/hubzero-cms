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
			throw new \InvalidArgumentException(__METHOD__ . '(); ' . \Lang::txt('Autocompleter for "%s" not supported.', $what));
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
				throw new \InvalidArgumentException(__METHOD__ . '(); ' . \Lang::txt('Autocompleter type "%s" not supported.', $type));
			break;
		}

		$results = \Event::trigger(
			'hubzero.' . $event,
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
