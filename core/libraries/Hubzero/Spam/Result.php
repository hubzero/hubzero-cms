<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Spam;

/**
 * Spam result.
 *
 * Based on work by Laju Morrison <morrelinko@gmail.com>
 */
class Result
{
	/**
	 * @var  bool
	 */
	protected $isSpam = false;

	/**
	 * @var  array
	 */
	protected $messages = array();

	/**
	 * Constructor
	 *
	 * @param   bool   $isSpam    Result from spam detectors
	 * @param   array  $messages  Messages to pass along
	 * @return  void
	 */
	public function __construct($isSpam, array $messages = array())
	{
		$this->isSpam   = $isSpam;
		$this->messages = $messages;
	}

	/**
	 * Alias of SpamResult::failed();
	 *
	 * @return  bool
	 */
	public function isSpam()
	{
		return $this->failed();
	}

	/**
	 * Did the content pass?
	 *
	 * @return  bool
	 */
	public function passed()
	{
		return $this->isSpam == false;
	}

	/**
	 * Did the content fail?
	 *
	 * @return  bool
	 */
	public function failed()
	{
		return !$this->passed();
	}

	/**
	 * Get the list of messages
	 *
	 * @return  array
	 */
	public function getMessages()
	{
		return $this->messages;
	}
}
