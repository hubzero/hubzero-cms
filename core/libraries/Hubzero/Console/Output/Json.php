<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
