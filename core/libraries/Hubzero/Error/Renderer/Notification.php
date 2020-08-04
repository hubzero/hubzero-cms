<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Error\Renderer;

use Hubzero\Error\RendererInterface;
use Hubzero\Notification\Handler;
use Exception;

/**
 * Displays the custom error page when an uncaught exception occurs.
 */
class Notification implements RendererInterface
{
	/**
	 * Notification handler
	 *
	 * @var  object
	 */
	protected $notifier;

	/**
	 * Create a new Notification exception displayer.
	 *
	 * @param   object  $notifier
	 * @return  void
	 */
	public function __construct(Handler $notifier)
	{
		$this->notifier = $notifier;
	}

	/**
	 * Render the error page based on an exception.
	 *
	 * @param   object  $error  The exception for which to render the error page.
	 * @return  void
	 */
	public function render($error)
	{
		$this->notifier->message($error->getMessage(), ($error->getCode() == 500 ? 'error' : 'warning'));
	}
}
