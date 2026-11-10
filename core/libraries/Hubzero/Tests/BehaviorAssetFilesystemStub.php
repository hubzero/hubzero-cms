<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests;

class BehaviorAssetFilesystemStub
{
    public function extension(string $file): string
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }
}
