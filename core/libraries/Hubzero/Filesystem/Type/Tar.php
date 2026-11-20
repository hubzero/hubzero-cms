<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem\Type;

use Hubzero\Filesystem\Manager;
use Hubzero\Filesystem\Entity;

/**
 * Tar model
 */
class Tar extends Expandable
{
    /**
     * Expand archive
     *
     * @param   bool  $cleanup  Whether or not to clean up after expansion (i.e. removing known OS files, etc...)
     * @return  bool
     */
    public function expand($cleanup = true)
    {
        // Create local tmp copy of the archive that's being expanded
        $temp = Manager::getTempFile($this->getName());
        $this->copy($temp);

        $archive = new \PharData($temp->getAbsolutePath());

        foreach ($archive as $file) {
            // Add 7 to the length for the 'phar://' prefix to the file
            $path   = substr($file, strlen($temp->getAbsolutePath()) + 7);
            $entity = Entity::fromPath($this->getParent() . $path, $this->getAdapter());

            if ($entity instanceof \Hubzero\Filesystem\File) {
                // Open
                $item = fopen($file, 'r');
                if ($item === false) {
                    continue;
                }

                // Write stream
                $entity->putStream($item);

                // Close
                fclose($item);
            } else {
                // Create the directory
                $entity->create();
            }
        }

        // Clean up
        $temp->delete();

        return parent::expand($cleanup);
    }
}
