<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Debug\Dumper;

/**
 * Renderable interface to be extended
 */
interface Renderable
{
	/**
	 * Returns renderer name
	 *
	 * @return  string
	 */
	public function getName();

	/**
	 * Set the list of messages
	 *
	 * @param   mixed  $messages
	 * @return  object
	 */
	public function setMessages($messages);

	/**
	 * Get the list of messages
	 *
	 * @return  array
	 */
	public function getMessages();

	/**
	 * Render a list of messages
	 *
	 * @param   array  $messages
	 * @return  string
	 */
	public function render($messages=null);
}
