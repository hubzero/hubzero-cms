<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem\Macro;

/**
 * Filesystem macro for emptying a directory.
 */
class EmptyDirectory extends Base
{
    /**
     * Get the method name.
     *
     * @return  string
     */
    public function getMethod()
    {
        return 'emptyDirectory';
    }

    /**
     * Empty a directory's contents.
     *
     * @param   string   $dirname
     * @return  boolean  True if everything in it was deleted
     */
    public function handle($dirname)
    {
        $listing = $this->filesystem->listContents($dirname);
        $success = true;

        foreach ($listing as $item) {
            if ($item['type'] === 'dir') {
                $deleted = $this->filesystem->deleteDirectory($dirname . $item['path']);
            } else {
                $deleted = $this->filesystem->delete($dirname . $item['path']);
            }

            if (!$deleted) {
                $success = false;
            }
        }

        return $success;
    }
}
