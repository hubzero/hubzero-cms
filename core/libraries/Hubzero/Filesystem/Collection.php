<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem;

use Hubzero\Base\ItemList;

/**
 * Iterable class for filesystem objects
 *
 * This is not necessarily a directory.  It could simply be a list of files
 * from multiple locations...maybe even multiple filesystems?
 */
class Collection extends ItemList
{
	/**
	 * A flat list of all data and nested files
	 *
	 * @var  array
	 **/
	private $flatData = null;

	/**
	 * A flat list of all file extensions
	 *
	 * @var  array
	 **/
	private $flatExtentions = null;

	/**
	 * Sorts items by a given field and direction
	 *
	 * @param   string  $key  The key to sort by
	 * @param   string  $asc  Whether or not to sort asc or descending
	 * @return  static
	 **/
	public function sort($key, $asc = true)
	{
		return $this->sortByCallback(function($a, $b) use ($key, $asc)
		{
			if (!isset($a->$key) || !isset($b->$key))
			{
				if (!isset($a->$key))
				{
					return ($asc) ? -1 :  1;
				}

				if (!isset($b->$key))
				{
					return ($asc) ?  1 : -1;
				}

				return 0;
			}

			if ($asc)
			{
				return strnatcmp($a->$key, $b->$key);
			}
			else
			{
				return strnatcmp($b->$key, $a->$key);
			}
		});
	}

	/**
	 * Sorts items using the provided callback function
	 *
	 * @param   closure  $callback  The sorting function to use
	 * @return  static
	 **/
	public function sortByCallback($callback)
	{
		$cache = $this->_data;

		usort($cache, $callback);

		return new static($cache);
	}

	/**
	 * Finds the first entity with a given name, optionally returning it
	 *
	 * @param   string  $name    The name of the entity to find
	 * @param   bool    $return  Whether or not to return the found entity
	 * @return  \Hubzero\Filesystem\File|\Hubzero\Filesystem\Directory|bool
	 **/
	public function find($name, $return = true)
	{
		foreach ($this as $entity)
		{
			if ($entity->isFile())
			{
				if ($entity->getName() == $name)
				{
					return ($return) ? $entity : true;
				}
			}
			else
			{
				// The result of this is already flat, so no need for recursion
				foreach ($entity->listContents(true) as $sub)
				{
					if ($sub->getName() == $name)
					{
						return ($return) ? $sub : true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Checks to see if the collection contains a named item
	 *
	 * @param   string  $name  The named item to look for
	 * @return  bool
	 **/
	public function has($name)
	{
		return $this->find($name, false);
	}

	/**
	 * Checks to see whether or not the required extensions are in the current collection
	 *
	 * @param   array  $requirements  The extension requirements to locate
	 * @return  bool
	 **/
	public function hasExtensions($requirements)
	{
		$files      = $this->getFlatListOfFiles();
		$extensions = $this->getFlatListOfExtensions();
		foreach ($requirements as $type => $constraint)
		{
			if (is_numeric($constraint))
			{
				if (!array_key_exists($type, $extensions) || $extensions[$type] < $constraint)
				{
					return false;
				}
			}
			else if (is_callable($constraint))
			{
				if (call_user_func_array($constraint, [$extensions, $files]) === false)
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Finds the first item with the given extension
	 *
	 * @param   string  $extension  The extension to look for
	 * @return  \Hubzero\Filesystem\File|bool
	 **/
	public function findFirstWithExtension($extension)
	{
		foreach ($this->getFlatListOfFiles() as $file)
		{
			if ($file->hasExtension($extension))
			{
				return $file;
			}
		}

		return false;
	}

	/**
	 * Finds all items with the given extension
	 *
	 * @param   string|array  $extension  The extension(s) to look for
	 * @return  \Hubzero\Filesystem\File
	 **/
	public function findAllWithExtension($extensions)
	{
		$found = [];

		if (!is_array($extensions))
		{
			$extensions = [$extensions];
		}

		foreach ($this->getFlatListOfFiles() as $file)
		{
			$ext = $file->getExtension();
			if (in_array($ext, $extensions))
			{
				$found[] = $file;
			}
		}

		return $found;
	}

	/**
	 * Builds a flat list of files by diving down recursively
	 *
	 * @return  array
	 **/
	public function getFlatListOfFiles()
	{
		if (!isset($this->flatData))
		{
			$this->flatData = [];

			foreach ($this as $entity)
			{
				if ($entity->isFile())
				{
					$this->flatData[] = $entity;
				}
				else
				{
					// The result of this is already flat, so no need for recursion
					foreach ($entity->listContents(true) as $sub)
					{
						if ($sub->isfile())
						{
							$this->flatData[] = $sub;
						}
					}
				}
			}
		}

		return $this->flatData;
	}

	/**
	 * Builds a flat list of extensions based on files list
	 *
	 * @return  array
	 **/
	public function getFlatListOfExtensions()
	{
		if (!isset($this->flatExtentions))
		{
			$this->flatExtentions = [];

			foreach ($this->getFlatListOfFiles() as $file)
			{
				$this->flatExtentions[] = $file->getExtension();
			}

			$this->flatExtentions = array_count_values($this->flatExtentions);
		}

		return $this->flatExtentions;
	}

	/**
	 * Compresses/archives entities in collection
	 *
	 * @param   bool  $structure  Whether or not to retain directory location of files being zipped
	 * @param   bool  $upload     Whether or not to reupload compressed to filesystem location
	 * @return  string|bool
	 */
	public function compress($structure = false, $upload = false)
	{
		if (!extension_loaded('zip'))
		{
			return false;
		}

		// Get temp directory
		$adapter  = null;
		$temp     = sys_get_temp_dir();
		$uniqueId = uniqid();
		$tarname  = uniqid() . '.zip';
		$zip      = new \ZipArchive;

		if ($zip->open($temp . DS . $tarname, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true)
		{
			foreach ($this->_data as $entity)
			{
				if ($entity->isFile())
				{
					$fileSrc = $entity->readStream();
					$parent = $entity->getParent();
					if (!empty($parent))
					{
						$parent = $temp . DS . $uniqueId . DS . $parent;
						if (!is_dir($parent))
						{
							mkdir($parent, 0755, true);
						}
					}
					$tmpFilePath = $temp . DS . $uniqueId . DS . $entity->getPath();
					$bufferSize = 1024 * 8;
					$tmpSrc = fopen($tmpFilePath, 'w');
					while (!feof($fileSrc))
					{
						$buffer = fread($fileSrc, $bufferSize);
						fwrite($tmpSrc, $buffer);
					}
					fclose($tmpSrc);
					fclose($fileSrc);
					$zip->addFile($tmpFilePath, $structure ? $entity->getPath() : $entity->getFileName());
				}
				else if ($entity->isDir() && $structure)
				{
					$zip->addEmptyDir($entity->getPath());
				}

				// Set some vars in case we need them later
				$adapter = $adapter ?: $entity->getAdapter();
			}

			$zip->close();

			$local = Manager::getTempPath($tarname);

			if ($upload)
			{
				// @FIXME: use manager copy?
				$entity = Entity::fromPath($tarname, $adapter);
				$entity->put($local->readAndDelete());

				return $entity;
			}
			else
			{
				return $local;
			}
		}
		else
		{
			return false;
		}
	}
}
