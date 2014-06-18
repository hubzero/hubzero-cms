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
 * Html renderer
 */
class Html extends AbstractRenderer
{
	/**
	 * {@inheritDoc}
	 */
	public function getName()
	{
		return 'html';
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
		/*$output[] = '<ul class="debug-varlist">';
		foreach ($messages as $item)
		{
			$output[] = '<li>' . $this->line($item) . '</li>';
		}
		$output[] = '</ul>';*/
		$output[] = '<div class="debug-varlist">';
		foreach ($messages as $item)
		{
			$output[] = $this->line($item);
		}
		$output[] = '</div>';
		return implode("\n", $output);
	}

	/**
	 * Render a list of messages
	 *
	 * @param   array  $messages
	 * @return  string
	 */
	public function line(array $item)
	{
		//return '<span class="vl vl-' . $item['label']. '">' . $this->_deflate($item['message']). '</span> <span class="lbl">' . $item['label']. '</span>';
		$val = print_r($item['var'], true);
		$val = preg_replace('/\[(.+?)\] =>/i', '<code class="ky">[$1]</code> <code class="op">=></code>', $val);
		return '<pre>' . $val . '</pre>';
	}

	/**
	 * Turn an array into a pretty print format
	 *
	 * @param   array  $arr
	 * @return  string
	 */
	protected function _deflate($arr)
	{
		if (is_string($arr))
		{
			$arr = htmlentities($arr, ENT_COMPAT, 'UTF-8');
			$arr = preg_replace('/\[(.+?)\] =&gt;/i', '<code class="ky">[$1]</code> <code class="op">=></code>', $arr);
			return $arr;
		}

		$output = 'Array( ' . "\n";
		$a = array();
		if (is_array($arr))
		{
			foreach ($arr as $key => $val)
			{
				if (is_array($val))
				{
					$a[] = "\t" . '<code class="ky">' . $key . '</code> <code class="op">=></code> <code class="vl">' . $this->_deflate($val) . '</code>';
				}
				else
				{
					$val = htmlentities($val, ENT_COMPAT, 'UTF-8');
					$val = preg_replace('/\[(.+?)\] =&gt;/i', '<code class="ky">[$1]</code> <code class="op">=></code>', $val);
					$a[] = "\t" . '<code class="ky">' . $key . '</code> <code class="op">=></code> <code class="vl">' . $val . '</code>';
				}
			}
		}
		$output .= implode(", \n", $a) . "\n" . ' )' . "\n";

		return $output;
	}
}
