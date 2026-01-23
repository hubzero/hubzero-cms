<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * HUBzero extension of flysystem local adapter
 */
class LocalAdapter extends \League\Flysystem\Adapter\Local
{
    /**
     * Map file info to normalized key names
     *
     * @param   \SplFileInfo  $file  The original file info class
     * @return  array
     */
    protected function mapFileInfo(SplFileInfo $file)
    {
        $default = parent::mapFileInfo($file);

        $default['owner'] = $file->getOwner();

        return $default;
    }
}
