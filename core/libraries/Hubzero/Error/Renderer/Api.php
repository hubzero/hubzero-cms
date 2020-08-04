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
	public function render($error)
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
