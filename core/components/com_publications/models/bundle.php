<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

require_once \Component::path('com_publications') . '/helpers/zipHelper.php';

use Components\Publications\Models\Publication;
use Components\Publications\Helpers\ZipHelper;
use Hubzero\Utility\Arr;

/*
 * Bundle model
 */
class Bundle
{
	/**
	 * Contents of archive
	 *
	 * @var  array
	 */
	protected $contents = null;

	/**
	 * Instance constructor
	 *
	 * @param   array  $args  Instantiation data
	 * @return  void
	 */
	public function __construct($args)
	{
		$this->publication_id = $args['publication_id'];
		$this->publication_version_id  = $args['publication_version_id'];
	}

	/**
	 * Gets size of corresponding bundle in bytes
	 *
	 * @return  integer
	 */
	public function getSize()
	{
		$path = $this->getWrapperPath();
		$size = null;

		if (file_exists($path))
		{
			$size = filesize($path);
		}

		return $size;
	}

	/**
	 * Gets checksum of corresponding bundle
	 *
	 * @return  string
	 */
	public function getMd5()
	{
		$path = $this->getWrapperPath();
		$checksum = null;

		if (file_exists($path))
		{
			$checksum = md5_file($path);
		}

		return $checksum;
	}

	/**
	 * Builds a hierarchical array of bundle's contents
	 *
	 * @return  array
	 */
	public function getContents()
	{
		if (!isset($this->contents))
		{
			$path = $this->getWrapperPath();
			$this->contents = ZipHelper::getArchiveContents($path);
			$this->expandBundleFile($this->contents);
		}

		return $this->contents;
	}

	/**
	 * Getter for path to bundle
	 *
	 * @return  string
	 */
	protected function getWrapperPath()
	{
		$path = $this->publication()->bundlePath();

		return $path;
	}

	/**
	 * Replace bundle element w/ hierarchical array of contents
	 *
	 * @param   array   $contents
	 * @return  void
	 */
	protected function expandBundleFile(&$contents)
	{
		$bundlePath = $this->getBundlePath();
		$bundleIndex = $this->getBundleIndex($contents);

		$bundleContents = [];
		$bundleContents['isDirectory'] = true;
		$bundleContents['name'] = 'bundle.zip';
		$bundleContents['contents'] = ZipHelper::getArchiveContents($bundlePath);

		unset($contents[$bundleIndex]);
		if ($bundleContents['contents'])
		{
			$contents['bundle'] = $bundleContents;
		}
	}

	/**
	 * Gets index of bundle element
	 *
	 * @param   array     $contents
	 * @return  integer
	 */
	protected static function getBundleIndex($contents)
	{
		$bundleIndex = null;

		foreach ($contents as $index => $entry)
		{
			if (isset($entry['name']) && $entry['name'] === 'bundle.zip')
			{
				$bundleIndex = $index;
				break;
			}
		}

		return $bundleIndex;
	}

	/**
	 * Builds path to bundle.zip archive
	 *
	 * @return  string
	 */
	protected function getBundlePath()
	{
		$wrapperPath = $this->getWrapperPath();
		$pathSegments = explode('/', $wrapperPath);
		array_pop($pathSegments);

		$bundlePath = implode('/', $pathSegments) . '/bundles/bundle.zip';

		return $bundlePath;
	}

	/**
	 * Getter for associated publication
	 *
	 * @return  object  \Publication
	 */
	public function publication()
	{
		if (!isset($this->publication))
		{
			$this->publication = new Publication($this->publication_id);
		}

		return $this->publication;
	}
}
