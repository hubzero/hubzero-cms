<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
