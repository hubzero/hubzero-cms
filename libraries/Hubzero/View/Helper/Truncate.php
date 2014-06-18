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

use Hubzero\Utility\String;

/**
 * Helper for truncating text
 */
class Truncate extends AbstractHelper
{
	/**
	 * Truncate some text
	 *
	 * @param  string  $text    Text to truncate
	 * @param  integer $length  Length to truncate to
	 * @param  array   $options Options
	 * @return string
	 * @throws \InvalidArgumentException If no text is passed or length isn't a positive integer
	 */
	public function __invoke($text = null, $length = null, $options = array())
	{
		if (null === $text)
		{
			throw new \InvalidArgumentException(
				__CLASS__ .'::' . __METHOD__ . '(); No text passed.'
			);
		}

		if (!$length || !is_numeric($length))
		{
			throw new \InvalidArgumentException(
				__CLASS__ .'::' . __METHOD__ . '(); Length must be an integer'
			);
		}

		return String::truncate($text, $length, $options);
	}
}
