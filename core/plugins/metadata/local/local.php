<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

require_once __DIR__ . DS . 'models' . DS . 'metadata.php';

/**
 * Plugin class for fez metadata handling
 */
class plgMetadataLocal extends \Hubzero\Plugin\Plugin
{
	/**
	 * Responds to events for saving file metadata
	 *
	 * @param   Hubzero\Filesystem\File  $file      The file to which the metadata pertains
	 * @param   array                    $metadata  The metadata itself
	 * @return  void
	 **/
	public function onMetadataSave(Hubzero\Filesystem\File $file, $metadata)
	{
		foreach ($metadata as $key => $value)
		{
			if (!$file->isLocal())
			{
				return false;
			}

			$metadata = Metadata::oneOrNewByPathAndKey($file->getAbsolutePath(), $key);

			$metadata->set('value', (string) $value)->save();
		}
	}

	/**
	 * Responds to events for getting the latest metadata annotation set
	 *
	 * @param   Hubzero\Filesystem\File  $file        The file to which the metadata pertains
	 * @param   int                      $maxEntries  The maximum number of entries to return
	 * @return  array
	 **/
	public function onMetadataGet(Hubzero\Filesystem\File $file, $maxEntries = 1)
	{
		if (!$file->isLocal())
		{
			return false;
		}

		$metadata = Metadata::loadAllByPath($file->getAbsolutePath());
		$results  = [];

		foreach ($metadata as $data)
		{
			$results[$data->key] = $data->value;
		}

		return $results;
	}

	/**
	 * Responds to file move events so that we can rename applicable metadata paths
	 *
	 * @param   string  $old  The starting path name
	 * @param   string  $new  The ending path name
	 * @return  bool
	 **/
	public function onFileMove($old, $new)
	{
		return Metadata::relocateByPath($old, $new);
	}
}
