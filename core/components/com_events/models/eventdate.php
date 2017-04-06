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
 * @author    Patrick Mulligan <jpmulligan@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Models;

use Hubzero\Utility\Date;

class EventDate extends Date
{
	/**
	 * Same method as parent Date. I had to explicitly include this since the parent returns self,
	 * therefore wouldn't actually instantiate the EventDate object.
	 *
	 * @param   string  $date  String in a format accepted by strtotime(), defaults to "now".
	 * @param   mixed   $tz    Time zone to be used for the date.
	 * @return  object
	 */
	public static function of($date = 'now', $tz = null)
	{
		return new self($date, $tz);
	}

	/**
	 * Function to explicitly convert a date to the timezone and format provided.
	 * @param   mixed  $timezone The numeric key on the Date static offsets array (a short list of common timezones) 
	 * 		or a timezone string accepted by the TimeZone PHP object (see {@link PHP_MANUAL#timezones})
	 * @param   string  $format  The date format specification string (see {@link PHP_MANUAL#date})
	 * @return  string
	 */
	public function toTimezone($timezone, $format = null)
	{
		$format = $format ?: parent::$format;
		if (!($timezone instanceof DateTimeZone))
		{
			if (is_numeric($timezone))
			{
				$timezone = parent::$offsets[(string) $timezone];
			}
		}

		$this->setTimezone($timezone);
		return $this->format($format, true);
	}
}
