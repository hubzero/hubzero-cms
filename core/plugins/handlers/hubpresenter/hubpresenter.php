<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Plugin\Plugin;
use Hubzero\Filesystem\Manager;
use Hubzero\Filesystem\File;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin class for hubpresenter file handling
 */
class plgHandlersHubpresenter extends Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Determines if the given collection can be handled by this plugin
	 *
	 * @param   \Hubzero\Filesystem\Collection  $collection  The file collection to assess
	 * @return  void
	 **/
	public function canHandle(\Hubzero\Filesystem\Collection $collection)
	{
		// Do we have everything we need?
		$need = [
			'json'   => '1',
			'videos' => function ($ext, $files) {
				// If we dont have all the necessary media formats
				if (array_key_exists('mp4', $ext))
				{
					// We have a video, and we also need an ogv and a webm
					if (!array_key_exists('ogv', $ext) || !array_key_exists('webm', $ext))
					{
						return false;
					}
				}
				else if (array_key_exists('mp3', $ext))
				{
					if (!array_key_exists('ogg', $ext))
					{
						return false;
					}
				}
			},
			'slideVideos' => function ($ext, $files) use ($collection) {
				// See if we even have a slides directory
				if ($slides = $collection->find('slides'))
				{
					$slides = $slides->listContents();

					// Array to hold slides with video clips
					$slideVideos = [];

					// Build array for checking slide video formats
					foreach ($slides as $s)
					{
						if ($s->isFile())
						{
							$extension = $s->getExtension();

							if (in_array($extension, array('mp4', 'm4v', 'webm', 'ogv')))
							{
								$slideVideos[$s->getDisplayName()][$extension] = $s->getName();
							}
						}
					}

					// Make sure for each of the slide videos we have all three formats and has a backup image for the slide
					foreach ($slideVideos as $k => $v)
					{
						if (count($v) < 3)
						{
							return false;
						}

						if (!$slides->has($k . '.png') && !$slides->has($k . '.jpg'))
						{
							return false;
						}
					}
				}
			}
		];

		if (!$collection->hasExtensions($need))
		{
			return false;
		}

		return true;
	}

	/**
	 * Handles view events for hubpresenter files
	 *
	 * @param   \Hubzero\Filesystem\Collection  $collection  The file collection to view
	 * @param   int     $entityId    The entity id being loaded (if applicable)
	 * @param   string  $entityType  The entity type being loaded (if applicable)
	 * @return  void
	 **/
	public function onHandleView(\Hubzero\Filesystem\Collection $collection, $entityId = null, $entityType = null)
	{
		if (!$this->canHandle($collection))
		{
			return false;
		}

		// Create view
		$view = new \Hubzero\Plugin\View([
			'folder'  => 'handlers',
			'element' => 'hubpresenter',
			'name'    => 'hubpresenter',
			'layout'  => 'view'
		]);

		$view->items      = $collection;
		$view->entityId   = $entityId;
		$view->entityType = $entityType;

		return $view;
	}
}
