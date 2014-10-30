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
 * Helper for highlighting a phrase or word in a body of text
 */
class Highlight extends AbstractHelper
{
	/**
	 * Highlight some text
	 *
	 * @param   string   $text     Text to find phrases in
	 * @param   integer  $phrase   Phrase to highlight
	 * @param   array    $options  Options for highlighting
	 * @return  string
	 * @throws  \InvalidArgumentException If no text was passed
	 */
	public function __invoke($text=null, $phrase=null, $options = array())
	{
		if (null === $text)
		{
			throw new \InvalidArgumentException(__METHOD__ . '(); No text passed.');
		}

		return String::highlight($text, $phrase, $options);
	}
}
