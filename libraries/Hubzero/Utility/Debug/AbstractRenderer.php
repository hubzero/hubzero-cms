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

namespace Hubzero\Utility\Debug;

use InvalidArgumentException;

/**
 * Abstract renderer
 */
class AbstractRenderer implements Renderable
{
	/**
	 * Messages
	 *
	 * @var array
	 */
	protected $_messages = array();

	/**
	 * @param array $messages
	 */
	public function __construct($messages = null)
	{
		if ($messages)
		{
			$this->setMessages($messages);
		}
	}

	/**
	 * Returns renderer name
	 *
	 * @return  string
	 */
	public function getName()
	{
		return '__abstract__';
	}

	/**
	 * Get the list of messages
	 *
	 * @return  array
	 */
	public function getMessages()
	{
		return $this->_messages;
	}

	/**
	 * Set the list of messages
	 *
	 * @param   mixed $messages
	 * @return  object
	 */
	public function setMessages($messages)
	{
		if (!is_array($messages))
		{
			throw new InvalidArgumentException(\JText::sprintf(
				'Messages must be an array. Type of "%s" passed.',
				gettype($messages)
			));
		}

		$this->_messages = $messages;

		return $this;
	}

	/**
	 * Render a list of messages
	 *
	 * @param   array  $messages
	 * @return  string
	 */
	public function render($messages = null)
	{
		if ($messages)
		{
			$this->setMessages($messages);
		}

		$messages = $this->getMessages();

		$output = array();
		foreach ($messages as $item)
		{
			$output[] = print_r($item['var'], true);
		}

		return implode("\n", $output);
	}

	/**
	 * Turn an array into a pretty print format
	 *
	 * @param   array  $arr
	 * @return  string
	 */
	protected function _deflate($arr)
	{
		$arr = str_replace(array("\n", "\r", "\t"), ' ', $arr);
		return preg_replace('/\s+/', ' ', $arr);
	}
}
