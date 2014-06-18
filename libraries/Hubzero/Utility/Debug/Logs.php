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

/**
 * Monolog renderer
 */
class Logs extends AbstractRenderer
{
	/**
	 * Logger
	 *
	 * @var object
	 */
	protected $_logger = null;

	/**
	 * Constructor
	 *
	 * @param array $messages
	 */
	public function __construct($messages = null)
	{
		parent::__construct($messages);

		$this->_logger = \JFactory::getLogger();
	}

	/**
	 * Returns renderer name
	 *
	 * @return  string
	 */
	public function getName()
	{
		return 'logs';
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

		foreach ($messages as $item)
		{
			//$type = $item['label'];
			//$this->_logger->$type($this->_deflate($item['message']));
			$this->_logger->debug(print_r($item['var'], true));
		}
	}

	/**
	 * Turn an array into a pretty print format
	 *
	 * @param   array  $arr
	 * @return  string
	 */
	protected function _deflate($arr)
	{
		$output = 'Array( ' . "\n";
		$a = array();
		foreach ($arr as $key => $val)
		{
			if (is_array($val))
			{
				$a[] = "\t" . '<code class="ky">' . $key . '</code> <code class="op">=></code> <code class="vl">' . $this->_deflate($val) . '</code>';
			}
			else
			{
				$a[] = "\t" . '<code class="ky">' . $key . '</code> <code class="op">=></code> <code class="vl">' . htmlentities($val, ENT_COMPAT, 'UTF-8') . '</code>';
			}
		}
		$output .= implode(", \n", $a) . "\n" . ' )' . "\n";

		return $output;
	}
}
