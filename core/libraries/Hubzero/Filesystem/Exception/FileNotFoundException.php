<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem\Exception;

/**
 * File not found exception
 */
class FileNotFoundException extends \Exception
{
	/**
	 * @var string
	 */
	protected $path;

	/**
	 * Constructor.
	 *
	 * @param  string  $path
	 * @param  int     $code
	 * @param  object  $previous  \Exception
	 */
	public function __construct($path, $code = 0, \Exception $previous = null)
	{
		$this->path = $path;

		parent::__construct('File not found at path: ' . $this->getPath(), $code, $previous);
	}

	/**
	 * Get the path which was not found.
	 *
	 * @return  string
	 */
	public function getPath()
	{
		return $this->path;
	}
}
