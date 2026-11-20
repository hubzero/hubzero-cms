<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     * @var  \Hubzero\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Set the Filesystem object.
     *
     * @param   \Hubzero\Filesystem\Filesystem $filesystem  Filesystem
     * @return  void
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }
}
