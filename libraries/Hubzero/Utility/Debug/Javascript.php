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
 * Javascript renderer
 */
class Javascript extends AbstractRenderer
{
	/**
	 * Returns renderer name
	 *
	 * @return  string
	 */
	public function getName()
	{
		return 'javascript';
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
		$output[] = '<script type="text/javascript">';
		$output[] = 'if (!window.console) console = {};';
		$output[] = 'console.log   = console.log   || function(){};';
		$output[] = 'console.warn  = console.warn  || function(){};';
		$output[] = 'console.error = console.error || function(){};';
		$output[] = 'console.info  = console.info  || function(){};';
		$output[] = 'console.debug = console.debug || function(){};';
		foreach ($messages as $item)
		{
			$output[] = 'console.' . $item['label'] . '("' . addslashes($this->_deflate($item['message'])) . '");';
			/*switch ($item['type'])
			{
				case 'string':
					$output[] = 'console.' . $item['label'] . '("' . $this->_deflate($item['message']). '");';
				break;

				case 'array':
					$output[] = 'console.log("' . json_encode($this->_deflate($item['message'])). '");';
				break;

				case 'object':
					$output[] = 'console.log(JSON.parse(\'' . json_encode($item['message']) . '\'));';
				break;
			}*/
		}
		$output[] = '</script>';
		return implode("\n", $output);
	}
}
