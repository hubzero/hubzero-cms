<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'PresenterHelper'
 *
 * Long description (if any) ...
 */
class PresenterHelper extends JObject
{
	/**
	 * Displays Error Messages to the User
	 *
	 * @param 	array 	Array of error messages
	 * @return 	string	Html data for errors
	 */
	public function errorMessage( $errors = array() )
	{
		//if we have errors
		if(!empty($errors)) {
			//HUBpresenter Error Messages
			$html  = "<div id=\"hubpresenter-error\">";
			$html .= "<div id=\"title\">Oops, We Encountered an Error.</div>";
			$html .= "<p>Use the error messages below to try and resolve the issue. If you are still unable to fix the problem report your problem to the system administrator by entering a <a href=\"/feedback/report_problems\">support ticket.</a></p>";
			$html .= "<ol>";
			foreach($errors as $e) {
				$html .= "<li>{$e}</li>";
			}
			$html .= "</ol>";
			$html .= "</div>";

			//echo out content;
			return $html;
		}
	}

	/**
	 * Generates JSON Manifest from XML doc uploaded
	 *
	 * @param 	string 	Path to resources files
	 * @param 	string 	Path to XML doc
	 * @return 	string	Check to make sure manifest is created successfully
	 */
	public function createJsonManifest( $resource_path, $xml_path )
	{
		//verify once again the file exists
		if (file_exists( $xml_path )) {
		    $manifest = simplexml_load_file( $xml_path );
		}

		//set the media
		$old_media = $manifest->media;
		foreach($old_media->source as $source) {
			$old_media_parts = explode('\\', $source);
			$source = array_pop($old_media_parts);
			$ext = array_pop(explode(".", $source));

			$new_media[] = array("source" => $source, "type" => $ext);
		}

		//set the title
		$new_title = (string)$manifest->webtitle;

		//set the type
		$new_type = (in_array( $ext, array("webm","mp4","ogv") )) ? "Video" : "Audio";

		$new_slides = array();
		for($i=0;$i<count($manifest->event);$i++) {
			$title = (string)$manifest->event[$i]->title;
			$type = (string)$manifest->event[$i]->type;
			$media = "slides" . DS . (string)$manifest->event[$i]->path;
			$time = (string)$manifest->event[$i]->start;
			$slide = (string)$manifest->event[$i]->slide;

			if(strtolower($type) == "video") {
				$orig_media = $media;

				$media = array();

				$media[0]["source"] = $orig_media;
				$media[0]["type"] = PresenterHelper::getExt($orig_media);

				$name = PresenterHelper::getName($orig_media);

				if(file_exists( JPATH_ROOT . DS . $resource_path . DS . 'content' . DS . $name . '.webm' )) {
					$media[1]["source"] = $name.".webm";
					$media[1]["type"] = "webm";
				}

				if(file_exists( JPATH_ROOT . DS . $resource_path . DS . 'content' . DS . $name . '.ogv' )) {
					$media[2]["source"] = $name.".ogv";
					$media[2]["type"] = "ogg";
				}

				if(file_exists( JPATH_ROOT . DS . $resource_path . DS . 'content' . DS . $name . '.png' )) {
					$media[3]["source"] = $name.".png";
					$media[3]["type"] = "imagereplacement";
				}
			}

			$new_slides[] = array(
								"title" => $title,
								"type"	=> $type,
								"media" => $media,
								"time"	=> $time,
								"slide" => $slide
								);
		}

		$data = array();
		$data['presentation']['title'] = $new_title;
		$data['presentation']['type'] = $new_type;
		$data['presentation']['media'] = $new_media;
		$data['presentation']['slides'] = $new_slides;

		$json = json_encode( $data );

		$new_file = JPATH_ROOT . DS . $resource_path . DS . "presentation.json";
		$file_handle = fopen( $new_file, 'w') or die("An Error Occured While Trying to Create the Presentation Manifest.");
		fwrite( $file_handle, $json );
	}

	/**
	 * Gets the file extension from filename
	 *
	 * @param 	string	Name of file
	 * @return 	string	File Extension
	 */
	protected function getExt( $filename ) {
		$parts = explode(".",$filename);
		return array_pop($parts);
	}

	/**
	 * Gets just the name of file from filename
	 *
	 * @param 	string	Name of file
	 * @return 	string	File name
	 */
	protected function getName( $filename ) {
		$parts = explode(".", $filename);
		return $parts[0];
	}
}

?>
