<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Debug\Dumper;

use InvalidArgumentException;

/**
 * Abstract renderer
 */
class AbstractRenderer implements Renderable
{
	/**
	 * Messages
	 *
	 * @var  array
	 */
	protected $_messages = array();

	/**
	 * Constructor
	 *
	 * @param   array  $messages
	 * @return  void
	 */
	public function __construct($messages = null)
	{
		if ($messages)
		{
			$this->setMessages($messages);
		}
	}

	/**
	 * Returns renderer name
	 *
	 * @return  string
	 */
	public function getName()
	{
		return '__abstract__';
	}

	/**
	 * Get the list of messages
	 *
	 * @return  array
	 */
	public function getMessages()
	{
		return $this->_messages;
	}

	/**
	 * Set the list of messages
	 *
	 * @param   mixed  $messages
	 * @return  object
	 */
	public function setMessages($messages)
	{
		if (!is_array($messages))
		{
			throw new InvalidArgumentException(sprintf(
				'Messages must be an array. Type of "%s" passed.',
				gettype($messages)
			));
		}

		$this->_messages = $messages;

		return $this;
	}

	/**
	 * Render a list of messages
	 *
	 * @param   array  $messages
	 * @return  string
	 */
	public function render($messages = null)
	{
		if ($messages)
		{
			$this->setMessages($messages);
		}

		$messages = $this->getMessages();

		$output = array();
		foreach ($messages as $item)
		{
			$output[] = print_r($item['var'], true);
		}

		return implode("\n", $output);
	}

	/**
	 * Turn an array into a pretty print format
	 *
	 * @param   array  $arr
	 * @return  string
	 */
	protected function _deflate($arr)
	{
		$arr = str_replace(array("\n", "\r", "\t"), ' ', $arr);
		return preg_replace('/\s+/', ' ', $arr);
	}
}
