<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam\StringProcessor;

/**
 * Spam string processor.
 *
 * Based on work by Laju Morrison <morrelinko@gmail.com>
 */
class NativeStringProcessor implements StringProcessorInterface
{
	/**
	 * Perform ASCII conversion?
	 *
	 * @var  bool
	 */
	protected $asciiConversion = true;

	/**
	 * Aggressive processing?
	 *
	 * @var  bool
	 */
	protected $aggressive = false;

	/**
	 * Constructor
	 *
	 * @param   array  $options
	 * @return  void
	 */
	public function __construct(array $options = array())
	{
		if (isset($options['ascii_conversion']))
		{
			$this->asciiConversion = (bool) $options['ascii_conversion'];
		}

		if (isset($options['aggressive']))
		{
			$this->aggressive = (bool) $options['aggressive'];
		}
	}

	/**
	 * Prepare a string
	 *
	 * @param   string  $string
	 * @return  string
	 */
	public function prepare($string)
	{
		if ($this->asciiConversion)
		{
			setlocale(LC_ALL, 'en_us.UTF8');
			$string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
		}

		if ($this->aggressive)
		{
			// Convert some characters that 'MAY' be used as alias
			$string = str_replace(array('@', '$', '[dot]', '(dot)'), array('at', 's', '.', '.'), $string);

			// Remove special characters
			$string = preg_replace("/[^a-zA-Z0-9-\.]/", '', $string);

			// Strip multiple dots (.) to one. eg site......com to site.com
			$string = preg_replace("/\.{2,}/", '.', $string);
		}

		$string = trim(strtolower($string));
		$string = str_replace(array("\t", "\r\n", "\r", "\n"), '', $string);

		return $string;
	}
}
