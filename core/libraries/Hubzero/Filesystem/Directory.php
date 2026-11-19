<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem;

/**
 * Directory model
 */
class Directory extends Entity
{
    /**
     * @var  mixed
     */
    public $subDirs;
    /**
     * Grabs the entity extension
     *
     * @return  string
     **/
    public function getExtension()
    {
        return 'folder';
    }

    /**
     * Lists directory contents
     *
     * @param   bool  $recursive  Whether or not to dive down recursively
     * @return  \Hubzero\Filesystem\Collection
     **/
    public function listContents($recursive = false)
    {
        return $this->adapterOrFail()->listContents($this->getPath(), $recursive);
    }

    /**
     * Create the directory
     *
     * @return  bool
     **/
    public function create()
    {
        return $this->adapterOrFail()->createDir($this->getPath());
    }

    /**
     * Deletes the directory
     *
     * @return  bool
     **/
    public function delete()
    {
        return $this->adapterOrFail()->deleteDir($this->getPath());
    }

    /**
     * @return  bool
     */
    public function hasSubDirs()
    {
        return count($this->getSubDirs()) > 0;
    }

    /**
     * Gets a list of directory objects with a depth for walking a directory structure
     *
     * @param       int $depth  How deep you currently are since beginning to walk
     * @return  array<int, mixed>
     **/
    public function getSubDirs($depth = 0)
    {
        $dirs = [];

        $contents = $this->adapterOrFail()->listContents($this->getPath(), false);
        foreach ($contents as $item) {
            if ($item->isDir()) {
                $thisDir = new \stdClass();
                $thisDir->depth = $depth;
                $thisDir->subdirs = $item->getSubDirs($depth + 1);
                $thisDir->name = $item->getDisplayName();
                $thisDir->path = $item->getPath();
                $dirs[] = $thisDir;
            }
        }
        return $dirs;
    }
}
