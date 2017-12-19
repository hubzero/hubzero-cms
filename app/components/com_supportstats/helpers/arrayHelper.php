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
 * @author    Anthony Fuentes <fuentesa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Supportstats\Helpers;

/**
 * Provides array utility methods
 */
class ArrayHelper
{

	/**
	 * Extracts array elements returning a one-dimensional array
	 *
	 * @param   string   $original 	Array to be flattened
	 * @param   array    $flattened	Array to return
	 * @return  array
	 */
	public static function flatten($original, $flattened = array())
	{
		foreach ($original as $element)
		{
			if (is_array($element))
			{
				$flattened = self::flatten($element, $flattened);
			}
			else
			{
				$flattened[] = $element;
			}
		}

		return $flattened;
	}

	/**
	 * Maps array elements by specified attribute
	 *
	 * @param   string   $array 			Array with elements to be mapped
	 * @param   array    $attribute		Attribute to map elements by
	 * @return  array
	 */
	public static function mapByAttribute($array, $attribute)
	{
		$mappedArray = array();

		foreach ($array	as $element)
		{
			$attributeValue = $element->get($attribute);
			$mappedArray[$attributeValue] = $element;
		}

		return $mappedArray;
	}

}
