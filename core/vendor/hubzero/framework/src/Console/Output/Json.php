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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Output;

use Hubzero\Console\Output;

/**
 * Json output class for rendering content to command line (usually for ingestion to browser)
 **/
class Json extends Output
{
	/**
	 * Constructor - set mode
	 *
	 * @return  void
	 **/
	public function __construct()
	{
		// Assume minimal mode and non-interactivity
		$this->setMode('minimal');
		$this->makeNonInteractive();
	}

	/**
	 * Render out stored output to command line
	 *
	 * @param   bool  $newLine  Whether or not to include new line with each response (really only applies to interactive output)
	 * @return  void
	 **/
	public function render($newLine = true)
	{
		// Make sure there is something there
		if (isset($this->response) && count($this->response) > 0)
		{
			echo json_encode($this->response);

			// Reset response
			$this->response = array();
		}
	}

	/**
	 * Add a new line to the output buffer (not actually a real php output buffer)
	 *
	 * @param   string  $message  Text of line
	 * @param   mixed   $styles   Array of custom styles or string containing predefined term (see formatLine() for posibilities)
	 * @param   bool    $newLine  Whether or not line should end with a new line
	 * @return  $this
	 **/
	public function addLine($message, $styles = null, $newLine = true)
	{
		$styles  = null;
		$newLine = true;
		if (is_array($message))
		{
			$this->response[key($message)] = current($message);
		}
		else
		{
			$this->response[] = $message;
		}

		return $this;
	}
}