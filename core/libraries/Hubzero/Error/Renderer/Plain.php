<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	public function render($error)
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
