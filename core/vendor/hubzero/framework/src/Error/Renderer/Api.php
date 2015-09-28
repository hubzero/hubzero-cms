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

namespace Hubzero\Error\Renderer;

use Hubzero\Error\RendererInterface;
use Hubzero\Http\Response;
use Exception;

/**
 * Displays plain error info when an uncaught exception occurs.
 */
class Api implements RendererInterface
{
	/**
	 * Response object
	 *
	 * @var  object
	 */
	protected $response;

	/**
	 * Debugging turned on?
	 *
	 * @var  bool
	 */
	protected $debug;

	/**
	 * Create a new exception renderer.
	 *
	 * @param   object  $response
	 * @param   bool    $debug
	 * @return  void
	 */
	public function __construct(Response $response, $debug = false)
	{
		$this->response = $response;
		$this->debug    = $debug;
	}

	/**
	 * Display the given exception to the user.
	 *
	 * @param   object  $exception
	 * @return  void
	 */
	public function render(Exception $error)
	{
		$status = $error->getCode() ? $error->getCode() : 500;
		$status = ($status < 100 || $status >= 600) ? 500 : $status;

		$content = new \StdClass;
		$content->message = $error->getMessage();
		$content->code    = $status;

		if ($this->debug)
		{
			$content->trace = array();

			$backtrace = $error->getTrace();

			if (is_array($backtrace))
			{
				$backtrace = array_reverse($backtrace);

				for ($i = count($backtrace) - 1; $i >= 0; $i--)
				{
					if (isset($backtrace[$i]['class']))
					{
						$line = "[$i] " . sprintf("%s %s %s()", $backtrace[$i]['class'], $backtrace[$i]['type'], $backtrace[$i]['function']);
					}
					else
					{
						$line = "[$i] " . sprintf("%s()", $backtrace[$i]['function']);
					}

					if (isset($backtrace[$i]['file']))
					{
						$line .= sprintf(' @ %s:%d', str_replace(PATH_ROOT, '', $backtrace[$i]['file']), $backtrace[$i]['line']);
					}

					$content->trace[] = $line;
				}
			}
		}

		$this->response->setStatusCode($content->code);
		$this->response->setContent($content);
		$this->response->send();

		exit();
	}
}
