<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\PathPrefixer;

/**
 * Local adapter that remembers its root, so the CMS can turn a path relative
 * to the filesystem back into an absolute one (Flysystem 3 keeps the prefix
 * private).
 */
class LocalAdapter extends LocalFilesystemAdapter
{
    /**
     * @var  PathPrefixer
     */
    private $pathPrefixer;

    /**
     * Constructor
     *
     * @param   string  $root
     * @return  void
     */
    public function __construct(string $root)
    {
        parent::__construct($root);

        $this->pathPrefixer = new PathPrefixer($root, DIRECTORY_SEPARATOR);
    }

    /**
     * Get the root path
     *
     * @return  string
     */
    public function getPathPrefix()
    {
        return $this->pathPrefixer->prefixPath('');
    }

    /**
     * Turn a path relative to the root into an absolute path
     *
     * @param   string  $path
     * @return  string
     */
    public function applyPathPrefix($path)
    {
        return $this->pathPrefixer->prefixPath((string) $path);
    }
}
