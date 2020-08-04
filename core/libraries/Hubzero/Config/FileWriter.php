<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config;

/**
 * File writer class
 */
class FileWriter
{
	/**
	 * The format to write
	 *
	 * @var  string
	 */
	protected $format = 'php';

	/**
	 * The default configuration path.
	 *
	 * @var  string
	 */
	protected $path;

	/**
	 * Formatting options for the specified format
	 *
	 * @var  array
	 */
	protected $options = array();

	/**
	 * Create a new file configuration loader.
	 *
	 * @param   string  $format
	 * @param   string  $path
	 * @param   array   $options  Formatting options
	 * @return  void
	 */
	public function __construct($format, $path, $options = array('format' => 'array'))
	{
		$this->format  = $format;
		$this->path    = $path;
		$this->options = $options;
	}

	/**
	 * Create a new file configuration loader.
	 *
	 * @param   object  $contents
	 * @param   string  $group
	 * @param   string  $client
	 * @return  boolean
	 */
	public function write($contents, $group, $client = null)
	{
		$path = $this->getPath($client, $group);

		if (!$path)
		{
			return false;
		}

		$contents = $this->toContent($contents, $this->format, $this->options);

		return !($this->putContent($path, $contents) === false);
	}

	/**
	 * Generate the path to write
	 *
	 * @param   string  $client
	 * @param   string  $group
	 * @return  string
	 */
	private function getPath($client, $group)
	{
		$path = $this->path;

		if (is_null($path))
		{
			return null;
		}

		$file = $path . DIRECTORY_SEPARATOR . ($client ? $client . DIRECTORY_SEPARATOR : '') . $group . '.' . $this->format;

		return $file;
	}

	/**
	 * Turn contents into a string of the correct format
	 *
	 * @param   mixed   $content
	 * @param   string  $format
	 * @param   array   $options
	 * @return  string
	 */
	public function toContent($contents, $format, $options = array())
	{
		if (!($contents instanceof Registry))
		{
			$contents = new Registry($contents);
		}

		return $contents->toString($format, $options);
	}

	/**
	 * Write the contents of a file.
	 *
	 * @param   string   $path
	 * @param   string   $contents
	 * @param   mixed    $mode
	 * @return  boolean
	 */
	public function putContent($file, $contents, $mode = '0640')
	{
		$path = dirname($file);

		if (!is_dir($path))
		{
			if (!@mkdir($path, 0750))
			{
				return false;
			}
		}

		$result = @file_put_contents($file, $contents);

		if ($result)
		{
			@chmod($file, octdec($mode));
		}

		return $result;
	}
}
