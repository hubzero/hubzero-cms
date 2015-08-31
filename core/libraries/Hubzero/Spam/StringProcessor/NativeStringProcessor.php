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

namespace Hubzero\Spam\StringProcessor;

/**
 * Spam string processor.
 *
 * Based on work by Laju Morrison <morrelinko@gmail.com>
 */
class NativeStringProcessor implements StringProcessorInterface
{
	/**
	 * Perform ASCII conversion?
	 *
	 * @var  bool
	 */
	protected $asciiConversion = true;

	/**
	 * Aggressive processing?
	 *
	 * @var  bool
	 */
	protected $aggressive = false;

	/**
	 * Constructor
	 *
	 * @param   array  $options
	 * @return  void
	 */
	public function __construct(array $options = array())
	{
		foreach ($options as $option => $value)
		{
			switch ($option)
			{
				case 'ascii_conversion':
					$this->asciiConversion = (bool) $value;
				break;
				case 'aggressive':
					$this->aggressive = (bool) $value;
				break;
				default:
				break;
			}
		}
	}

	/**
	 * Prepare a string
	 *
	 * @param   string  $string
	 * @return  mixed
	 */
	public function prepare($string)
	{
		if ($this->asciiConversion)
		{
			setlocale(LC_ALL, 'en_us.UTF8');
			$string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
		}

		if ($this->aggressive)
		{
			// Convert some characters that 'MAY' be used as alias
			$string = str_replace(array('@', '$', '[dot]', '(dot)'), array('at', 's', '.', '.'), $string);

			// Remove special characters
			$string = preg_replace("/[^a-zA-Z0-9-\.]/", "", $string);

			// Strip multiple dots (.) to one. eg site......com to site.com
			$string = preg_replace("/\.{2,}/", '.', $string);
		}

		$string = trim(strtolower($string));
		$string = str_replace(array("\t", "\r\n", "\r", "\n"), '', $string);

		return $string;
	}
}
