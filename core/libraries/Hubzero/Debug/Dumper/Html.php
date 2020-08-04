<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Debug\Dumper;

/**
 * Html renderer
 */
class Html extends AbstractRenderer
{
	/**
	 * Returns renderer name
	 *
	 * @return  string
	 */
	public function getName()
	{
		return 'html';
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
		$output[] = '<div class="debug-varlist">';
		foreach ($messages as $item)
		{
			$output[] = $this->line($item);
		}
		$output[] = '</div>';
		return implode("\n", $output);
	}

	/**
	 * Render a list of messages
	 *
	 * @param   array  $messages
	 * @return  string
	 */
	public function line(array $item)
	{
		$val = print_r($item['var'], true);
		$val = preg_replace('/\[(.+?)\] =>/i', '<code class="ky">[$1]</code> <code class="op">=></code>', $val);
		return '<pre>' . $val . '</pre>';
	}

	/**
	 * Turn an array into a pretty print format
	 *
	 * @param   array  $arr
	 * @return  string
	 */
	protected function _deflate($arr)
	{
		if (is_string($arr))
		{
			$arr = htmlentities($arr, ENT_COMPAT, 'UTF-8');
			$arr = preg_replace('/\[(.+?)\] =&gt;/i', '<code class="ky">[$1]</code> <code class="op">=></code>', $arr);
			return $arr;
		}

		$output = 'Array( ' . "\n";
		$a = array();
		if (is_array($arr))
		{
			foreach ($arr as $key => $val)
			{
				if (is_array($val))
				{
					$a[] = "\t" . '<code class="ky">' . $key . '</code> <code class="op">=></code> <code class="vl">' . $this->_deflate($val) . '</code>';
				}
				else
				{
					$val = htmlentities($val, ENT_COMPAT, 'UTF-8');
					$val = preg_replace('/\[(.+?)\] =&gt;/i', '<code class="ky">[$1]</code> <code class="op">=></code>', $val);
					$a[] = "\t" . '<code class="ky">' . $key . '</code> <code class="op">=></code> <code class="vl">' . $val . '</code>';
				}
			}
		}
		$output .= implode(", \n", $a) . "\n" . ' )' . "\n";

		return $output;
	}
}
