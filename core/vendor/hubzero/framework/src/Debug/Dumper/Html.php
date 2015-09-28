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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Debug\Dumper;

/**
 * Html renderer
 */
class Html extends AbstractRenderer
{
	/**
	 * Returns renderer name
	 *
	 * @return  string
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
