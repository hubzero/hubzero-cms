<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Helpers;

use Hubzero\Base\Object;

/**
 * Helper class for HUB Presenter
 */
class Hubpresenter extends Object
{
	/**
	 * Generates JSON Manifest from XML doc uploaded
	 *
	 * @param 	string 	Path to resources files
	 * @param 	string 	Path to XML doc
	 * @return 	string	Check to make sure manifest is created successfully
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
			$ext             = array_pop(explode('.', $source));
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
		for ($i=0;$i<count($manifest->event);$i++)
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
				$media[0]['type']   = self::getExt($orig_media);

				$name = self::getName($orig_media);

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
		$file_handle = fopen($new_file, 'w') or die("An Error Occured While Trying to Create the Presentation Manifest.");
		fwrite($file_handle, $json);
	}

	/**
	 * Gets the file extension from filename
	 *
	 * @param 	string	Name of file
	 * @return 	string	File Extension
	 */
	protected function getExt($filename)
	{
		$parts = explode('.', $filename);
		return array_pop($parts);
	}

	/**
	 * Gets just the name of file from filename
	 *
	 * @param 	string	Name of file
	 * @return 	string	File name
	 */
	protected function getName($filename)
	{
		$parts = explode('.', $filename);
		return $parts[0];
	}
}