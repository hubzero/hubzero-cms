<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem;

/**
 * Filesystem macro interface.
 */
interface MacroInterface
{
    /**
     * Get the method name.
     *
     * @return  string
     */
    public function getMethod();

    /**
     * Set the Filesystem object.
     *
     * @param  Filesystem  $filesystem
     * @return void
     */
    public function setFilesystem(Filesystem $filesystem);
}
