<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem\Macro;

use Hubzero\Filesystem\Filesystem;
use Hubzero\Filesystem\MacroInterface;

/**
 * Abstract Filesystem macro.
 */
abstract class Base implements MacroInterface
{
	/**
	 * Filesystem
	 *
	 * @var  object
	 */
	protected $filesystem;

	/**
	 * Set the Filesystem object.
	 *
	 * @param   object  $filesystem  Filesystem
	 * @return  void
	 */
	public function setFilesystem(Filesystem $filesystem)
	{
		$this->filesystem = $filesystem;
	}
}
