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
 * Displays the custom error page when an uncaught exception occurs.
 */
class Page implements RendererInterface
{
	/**
	 * Document instance
	 *
	 * @var  object
	 */
	protected $document;

	/**
	 * Template name
	 *
	 * @var  string
	 */
	protected $template;

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
	public function __construct($document, $template = 'system', $debug = false)
	{
		$this->document = $document;
		$this->template = $template;
		$this->debug    = $debug;
	}

	/**
	 * Render the error page based on an exception.
	 *
	 * @param   object  $error  The exception for which to render the error page.
	 * @return  void
	 */
	public function render(Exception $error)
	{
		try
		{
			if (!$this->document)
			{
				// We're probably in an CLI environment
				exit($error->getMessage());
			}

			$this->document->setType('error');

			// Push the error object into the document
			$this->document->setError($error);

			if (ob_get_contents())
			{
				ob_end_clean();
			}

			$this->document->setTitle(\Lang::txt('Error') . ': ' . $error->getCode());

			$path = PATH_APP . DS . 'templates';
			if (!is_dir($path . DS . $this->template))
			{
				$path = PATH_CORE . DS . 'templates';
			}

			$data = $this->document->render(
				false,
				array(
					'template'  => $this->template,
					'directory' => $path,
					'debug'     => $this->debug
				)
			);

			// Failsafe to get the error displayed.
			if (empty($data))
			{
				exit($error->getMessage() . ' in ' . $error->getFile() . ':' . $error->getLine());
			}
			else
			{
				$status = $error->getCode() ? $error->getCode() : 500;
				$status = ($status < 100 || $status >= 600) ? 500 : $status;

				$response = new Response($data, $status);
				$response->send();

				exit();
			}
		}
		catch (Exception $e)
		{
			$plain = new Plain($this->debug);
			$plain->render($e);
		}
	}
}
