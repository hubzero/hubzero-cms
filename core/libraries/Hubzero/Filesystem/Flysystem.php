<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem;

use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem as LeagueFilesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;

/**
 * A Flysystem 3 filesystem that still answers to the Flysystem 1 method
 * names the rest of the CMS was written against.
 *
 * Flysystem 3 renamed most operations (rename→move, deleteDir→
 * deleteDirectory, getSize→fileSize, …), returns void and throws instead of
 * returning false, and lists directories as StorageAttributes objects rather
 * than arrays. Everything in com_projects, the file handlers and the Entity
 * classes expects the old names, bool returns and array metadata, so this
 * class wraps a League filesystem and translates both ways. The v3 names are
 * available too, for new code.
 */
class Flysystem
{
    /**
     * The underlying League filesystem
     *
     * @var  LeagueFilesystem
     */
    protected $filesystem;

    /**
     * The adapter the filesystem was built on
     *
     * @var  FilesystemAdapter
     */
    protected $adapter;

    /**
     * Constructor
     *
     * @param   FilesystemAdapter  $adapter
     * @param   array              $config
     * @return  void
     */
    public function __construct(FilesystemAdapter $adapter, array $config = [])
    {
        $this->adapter    = $adapter;
        $this->filesystem = new LeagueFilesystem($adapter, $config);
    }

    /**
     * Get the League adapter
     *
     * @return  FilesystemAdapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Get the League filesystem (for MountManager and other v3 consumers)
     *
     * @return  LeagueFilesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * Is this a local-disk filesystem?
     *
     * @return  bool
     */
    public function isLocal()
    {
        return $this->adapter instanceof \League\Flysystem\Local\LocalFilesystemAdapter;
    }

    /**
     * Turn a path relative to this filesystem into an absolute one.
     *
     * Only a local adapter has a meaningful answer; the relative path is
     * returned unchanged for anything else.
     *
     * @param   string  $path
     * @return  string
     */
    public function applyPathPrefix($path)
    {
        if (method_exists($this->adapter, 'applyPathPrefix')) {
            return $this->adapter->applyPathPrefix($path);
        }

        return $path;
    }

    /**
     * Get the root path of a local adapter
     *
     * @return  string|null
     */
    public function getPathPrefix()
    {
        if (method_exists($this->adapter, 'getPathPrefix')) {
            return $this->adapter->getPathPrefix();
        }

        return null;
    }

    /**
     * Pass anything else straight through to the League filesystem
     *
     * @param   string  $method
     * @param   array   $arguments
     * @return  mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->filesystem, $method], $arguments);
    }

    /**
     * Does a file or directory exist?
     *
     * @param   string  $path
     * @return  bool
     */
    public function has($path)
    {
        try {
            return $this->filesystem->has(self::path($path));
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Read a file
     *
     * @param   string  $path
     * @return  string|false
     */
    public function read($path)
    {
        try {
            return $this->filesystem->read(self::path($path));
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Read a file as a stream
     *
     * @param   string  $path
     * @return  resource|false
     */
    public function readStream($path)
    {
        try {
            return $this->filesystem->readStream(self::path($path));
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Read a file and then delete it
     *
     * @param   string  $path
     * @return  string|false
     */
    public function readAndDelete($path)
    {
        $contents = $this->read($path);

        if ($contents !== false) {
            $this->delete($path);
        }

        return $contents;
    }

    /**
     * Write a new file. Fails if the file already exists.
     *
     * @param   string  $path
     * @param   string  $contents
     * @param   array   $config
     * @return  bool
     */
    public function write($path, $contents, array $config = [])
    {
        if ($this->fileExists($path)) {
            return false;
        }

        return $this->put($path, $contents, $config);
    }

    /**
     * Write a new file from a stream. Fails if the file already exists.
     *
     * @param   string    $path
     * @param   resource  $resource
     * @param   array     $config
     * @return  bool
     */
    public function writeStream($path, $resource, array $config = [])
    {
        if ($this->fileExists($path)) {
            return false;
        }

        return $this->putStream($path, $resource, $config);
    }

    /**
     * Overwrite an existing file. Fails if the file does not exist.
     *
     * @param   string  $path
     * @param   string  $contents
     * @param   array   $config
     * @return  bool
     */
    public function update($path, $contents, array $config = [])
    {
        if (!$this->fileExists($path)) {
            return false;
        }

        return $this->put($path, $contents, $config);
    }

    /**
     * Overwrite an existing file from a stream. Fails if the file does not exist.
     *
     * @param   string    $path
     * @param   resource  $resource
     * @param   array     $config
     * @return  bool
     */
    public function updateStream($path, $resource, array $config = [])
    {
        if (!$this->fileExists($path)) {
            return false;
        }

        return $this->putStream($path, $resource, $config);
    }

    /**
     * Create or overwrite a file
     *
     * @param   string  $path
     * @param   string  $contents
     * @param   array   $config
     * @return  bool
     */
    public function put($path, $contents, array $config = [])
    {
        try {
            $this->filesystem->write(self::path($path), (string) $contents, $config);
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Create or overwrite a file from a stream
     *
     * @param   string    $path
     * @param   resource  $resource
     * @param   array     $config
     * @return  bool
     */
    public function putStream($path, $resource, array $config = [])
    {
        try {
            $this->filesystem->writeStream(self::path($path), $resource, $config);
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Move a file
     *
     * @param   string  $path
     * @param   string  $newpath
     * @return  bool
     */
    public function rename($path, $newpath)
    {
        try {
            $this->filesystem->move(self::path($path), self::path($newpath));
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Copy a file
     *
     * @param   string  $path
     * @param   string  $newpath
     * @return  bool
     */
    public function copy($path, $newpath)
    {
        try {
            $this->filesystem->copy(self::path($path), self::path($newpath));
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Delete a file
     *
     * @param   string  $path
     * @return  bool
     */
    public function delete($path)
    {
        try {
            $this->filesystem->delete(self::path($path));
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Delete a directory and everything in it
     *
     * @param   string  $dirname
     * @return  bool
     */
    public function deleteDir($dirname)
    {
        try {
            $this->filesystem->deleteDirectory(self::path($dirname));
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Create a directory
     *
     * @param   string  $dirname
     * @param   array   $config
     * @return  bool
     */
    public function createDir($dirname, array $config = [])
    {
        try {
            $this->filesystem->createDirectory(self::path($dirname), $config);
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Get a file's MIME type
     *
     * @param   string  $path
     * @return  string|false
     */
    public function getMimetype($path)
    {
        try {
            return $this->filesystem->mimeType(self::path($path));
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Get a file's last-modified time
     *
     * @param   string  $path
     * @return  int|false
     */
    public function getTimestamp($path)
    {
        try {
            return $this->filesystem->lastModified(self::path($path));
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Get a file's size in bytes
     *
     * @param   string  $path
     * @return  int|false
     */
    public function getSize($path)
    {
        try {
            return $this->filesystem->fileSize(self::path($path));
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Get a file's visibility
     *
     * @param   string  $path
     * @return  string|false
     */
    public function getVisibility($path)
    {
        try {
            return $this->filesystem->visibility(self::path($path));
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Set a file's visibility
     *
     * @param   string  $path
     * @param   string  $visibility
     * @return  bool
     */
    public function setVisibility($path, $visibility)
    {
        try {
            $this->filesystem->setVisibility(self::path($path), $visibility);
            return true;
        } catch (FilesystemException $e) {
            return false;
        }
    }

    /**
     * Get a file's or directory's metadata in the Flysystem 1 array shape
     *
     * @param   string  $path
     * @return  array|false
     */
    public function getMetadata($path)
    {
        $path = self::path($path);

        try {
            if ($this->filesystem->fileExists($path)) {
                $attributes = new FileAttributes(
                    $path,
                    $this->quietly(function () use ($path) {
                        return $this->filesystem->fileSize($path);
                    }),
                    null,
                    $this->quietly(function () use ($path) {
                        return $this->filesystem->lastModified($path);
                    })
                );
            } elseif ($this->filesystem->directoryExists($path)) {
                $attributes = new DirectoryAttributes(
                    $path,
                    null,
                    $this->quietly(function () use ($path) {
                        return $this->filesystem->lastModified($path);
                    })
                );
            } else {
                return false;
            }
        } catch (FilesystemException $e) {
            return false;
        }

        return $this->metadata($attributes);
    }

    /**
     * List a directory as a Collection of Entity objects
     *
     * @param   string  $directory
     * @param   bool    $recursive
     * @return  Collection
     */
    public function listContents($directory = '', $recursive = false)
    {
        $items = [];

        try {
            $listing = $this->filesystem->listContents(self::path($directory), (bool) $recursive);

            foreach ($listing as $attributes) {
                $items[] = Entity::fromMetadata($this->metadata($attributes), $this);
            }
        } catch (FilesystemException $e) {
            // An unreadable directory lists as empty, as it did before
        }

        return new Collection($items);
    }

    /**
     * Turn Flysystem 3 attributes into the Flysystem 1 metadata array
     *
     * @param   StorageAttributes  $attributes
     * @return  array
     */
    public function metadata(StorageAttributes $attributes)
    {
        $path = $attributes->path();
        $info = pathinfo($path);

        $metadata = [
            'type'     => $attributes->isDir() ? 'dir' : 'file',
            'path'     => $path,
            'dirname'  => (isset($info['dirname']) && $info['dirname'] !== '.') ? $info['dirname'] : '',
            'basename' => $info['basename'],
            'filename' => $info['filename'],
        ];

        if (isset($info['extension'])) {
            $metadata['extension'] = $info['extension'];
        }

        if ($attributes->lastModified() !== null) {
            $metadata['timestamp'] = $attributes->lastModified();
        }

        if ($attributes->visibility() !== null) {
            $metadata['visibility'] = $attributes->visibility();
        }

        if ($attributes instanceof FileAttributes) {
            if ($attributes->fileSize() !== null) {
                $metadata['size'] = $attributes->fileSize();
            }

            if ($attributes->mimeType() !== null) {
                $metadata['mimetype'] = $attributes->mimeType();
            }
        }

        $metadata += $attributes->extraMetadata();

        if ($this->isLocal() && !isset($metadata['owner'])) {
            $owner = @fileowner($this->applyPathPrefix($path));
            $metadata['owner'] = $owner === false ? 0 : $owner;
        }

        return $metadata;
    }

    /**
     * Run a metadata lookup that is allowed to fail
     *
     * @param   callable  $fn
     * @return  mixed|null
     */
    private function quietly(callable $fn)
    {
        try {
            return $fn();
        } catch (FilesystemException $e) {
            return null;
        }
    }

    /**
     * Flysystem 3 wants strings; callers here pass null for the root
     *
     * @param   mixed  $path
     * @return  string
     */
    private static function path($path)
    {
        return $path === null ? '' : (string) $path;
    }
}
