<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Debug\Dumper;

/**
 * Monolog renderer
 */
class Logs extends AbstractRenderer
{
	/**
	 * Logger
	 *
	 * @var  object
	 */
	protected $_logger = null;

	/**
	 * Constructor
	 *
	 * @param   array  $messages
	 * @return  void
	 */
	public function __construct($messages = null)
	{
		parent::__construct($messages);

		$this->_logger = \Log::getRoot();
	}

	/**
	 * Returns renderer name
	 *
	 * @return  string
	 */
	public function getName()
	{
		return 'logs';
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

		foreach ($messages as $item)
		{
			$this->_logger->debug(print_r($item['var'], true));
		}
	}

	/**
	 * Turn an array into a pretty print format
	 *
	 * @param   array  $arr
	 * @return  string
	 */
	protected function _deflate($arr)
	{
		$output = 'Array( ' . "\n";
		$a = array();
		foreach ($arr as $key => $val)
		{
			if (is_array($val))
			{
				$a[] = "\t" . '<code class="ky">' . $key . '</code> <code class="op">=></code> <code class="vl">' . $this->_deflate($val) . '</code>';
			}
			else
			{
				$a[] = "\t" . '<code class="ky">' . $key . '</code> <code class="op">=></code> <code class="vl">' . htmlentities($val, ENT_COMPAT, 'UTF-8') . '</code>';
			}
		}
		$output .= implode(", \n", $a) . "\n" . ' )' . "\n";

		return $output;
	}
}
