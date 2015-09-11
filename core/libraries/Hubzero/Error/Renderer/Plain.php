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

namespace Hubzero\Error\Renderer;

use Hubzero\Error\RendererInterface;
use Exception;

/**
 * Displays plain error info when an uncaught exception occurs.
 */
class Plain implements RendererInterface
{
	/**
	 * Debugging turned on?
	 *
	 * @var  bool
	 */
	protected $debug;

	/**
	 * Create a new exception renderer.
	 *
	 * @param   object  $document  Document instance
	 * @param   string  $template  Template name
	 * @param   bool    $debug     Debugging turned on?
	 * @return  void
	 */
	public function __construct($debug = false)
	{
		$this->debug = $debug;
	}

	/**
	 * Display the given exception to the user.
	 *
	 * @param   object  $exception
	 * @return  void
	 */
	public function render(Exception $error)
	{
		if (!headers_sent())
		{
			header('HTTP/1.1 500 Internal Server Error');
		}

		$response = 'Error: ' . $error->getCode() . ' - ' . $error->getMessage() . "\n\n";

		if ($this->debug)
		{
			$backtrace = $error->getTrace();

			if (is_array($backtrace))
			{
				$backtrace = array_reverse($backtrace);

				for ($i = count($backtrace) - 1; $i >= 0; $i--)
				{
					if (isset($backtrace[$i]['class']))
					{
						$response .= "\n[$i] " . sprintf("%s %s %s()", $backtrace[$i]['class'], $backtrace[$i]['type'], $backtrace[$i]['function']);
					}
					else
					{
						$response .= "\n[$i] " . sprintf("%s()", $backtrace[$i]['function']);
					}

					if (isset($backtrace[$i]['file']))
					{
						$response .= sprintf(' @ %s:%d', str_replace(PATH_ROOT, '', $backtrace[$i]['file']), $backtrace[$i]['line']);
					}
				}
			}
		}

		echo (php_sapi_name() == 'cli') ? $response : nl2br($response);

		exit();
	}
}
