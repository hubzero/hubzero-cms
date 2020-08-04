<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Error\Renderer;

use Hubzero\Error\RendererInterface;
use Hubzero\Http\Response;
use Exception;
use Closure;

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
	 * @param   object  $template  Template loader
	 * @param   bool    $debug     Debugging turned on?
	 * @return  void
	 */
	public function __construct($document, $template = null, $debug = false)
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
	public function render($error)
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

			$template = $this->template->load();

			$data = $this->document->render(
				false,
				array(
					'template'  => $template->template,
					'directory' => dirname($template->path),
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
