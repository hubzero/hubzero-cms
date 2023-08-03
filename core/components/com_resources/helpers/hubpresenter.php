<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Helpers;

use Hubzero\Base\Obj;
use Filesystem;

/**
 * Helper class for HUB Presenter
 */
class Hubpresenter extends Obj
{
	/**
	 * Generates JSON Manifest from XML doc uploaded
	 *
	 * @param   string  $resource_path  Path to resources files
	 * @param   string  $xml_path       Path to XML doc
	 * @return  string  Check to make sure manifest is created successfully
	 */
	public function createJsonManifest($resource_path, $xml_path)
	{
		// Verify once again the file exists
		if (file_exists($xml_path))
		{
			$manifest = simplexml_load_file($xml_path);
		}

		// Set the media
		$old_media = $manifest->media;
		foreach ($old_media->source as $source)
		{
			$old_media_parts = explode('\\', $source);
			$source          = array_pop($old_media_parts);
			$parts           = explode('.', $source);
			$ext             = array_pop($parts);
			$new_media[]     = array(
				'source' => $source,
				'type'   => $ext
			);
		}

		// Set the title
		$new_title = (string)$manifest->webtitle;

		// Set the type
		$new_type = (in_array($ext, array('webm','mp4','ogv'))) ? 'Video' : 'Audio';

		$new_slides = array();
		for ($i=0; $i<count($manifest->event); $i++)
		{
			$title = (string)$manifest->event[$i]->title;
			$type  = (string)$manifest->event[$i]->type;
			$media = 'slides' . DS . (string)$manifest->event[$i]->path;
			$time  = (string)$manifest->event[$i]->start;
			$slide = (string)$manifest->event[$i]->slide;

			if (strtolower($type) == 'video')
			{
				$orig_media = $media;

				$media = array();

				$media[0]['source'] = $orig_media;
				$media[0]['type']   = Filesystem::extension($orig_media);

				$name = Filesystem::name($orig_media);

				if (file_exists(PATH_APP . DS . $resource_path . DS . 'content' . DS . $name . '.webm'))
				{
					$media[1]['source'] = $name . '.webm';
					$media[1]['type']   = 'webm';
				}

				if (file_exists(PATH_APP . DS . $resource_path . DS . 'content' . DS . $name . '.ogv'))
				{
					$media[2]['source'] = $name . '.ogv';
					$media[2]['type']   = 'ogg';
				}

				if (file_exists(PATH_APP . DS . $resource_path . DS . 'content' . DS . $name . '.png'))
				{
					$media[3]['source'] = $name . '.png';
					$media[3]['type']   = 'imagereplacement';
				}
			}

			$new_slides[] = array(
				'title' => $title,
				'type'  => $type,
				'media' => $media,
				'time'  => $time,
				'slide' => $slide
			);
		}

		$data = array(
			'presentation' => array()
		);
		$data['presentation']['title']  = $new_title;
		$data['presentation']['type']   = $new_type;
		$data['presentation']['media']  = $new_media;
		$data['presentation']['slides'] = $new_slides;

		$json = json_encode($data);

		$new_file    = PATH_APP . DS . $resource_path . DS . 'presentation.json';
		$file_handle = fopen($new_file, 'w') or die("An Error Occurred While Trying to Create the Presentation Manifest.");
		fwrite($file_handle, $json);
	}
}
