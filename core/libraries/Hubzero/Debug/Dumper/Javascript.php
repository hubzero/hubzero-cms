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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Debug\Dumper;

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
		}
		$output[] = '</script>';
		return implode("\n", $output);
	}
}
