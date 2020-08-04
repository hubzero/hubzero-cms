<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Debug\Dumper;

/**
 * Javascript renderer
 */
class Javascript extends AbstractRenderer
{
	/**
	 * Returns renderer name
	 *
	 * @return  string
	 */
	public function getName()
	{
		return 'javascript';
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
		$output[] = '<script type="text/javascript">';
		$output[] = 'if (!window.console) console = {};';
		$output[] = 'console.log   = console.log   || function(){};';
		$output[] = 'console.warn  = console.warn  || function(){};';
		$output[] = 'console.error = console.error || function(){};';
		$output[] = 'console.info  = console.info  || function(){};';
		$output[] = 'console.debug = console.debug || function(){};';
		foreach ($messages as $item)
		{
			$output[] = 'console.' . $item['label'] . '("' . addslashes($this->_deflate($item['message'])) . '");';
		}
		$output[] = '</script>';
		return implode("\n", $output);
	}
}
