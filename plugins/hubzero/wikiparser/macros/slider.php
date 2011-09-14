<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class SliderMacro extends WikiMacro
{
	public function description()
	{
		$txt = array();
		$txt['wiki'] = "Creates a slider with the images passed in.";
		$txt['html'] = '<p>Creates a slider with the images passed in. Enter uploaded image names seperated by commas.</p>
						<p>Examples:</p>
						<ul>
							<li><code>[[Slider(image1.jpg, image2.gif, image3.png)]]</code></li>
						</ul>';

		return $txt['html'];
	}

	public function render()
	{
		//get the args passed in
		$content = $this->args;

		// args will be null if the macro is called without parenthesis.
		if (!$content) {
			return;
		}

		//generate a unique id for the slider
		$id = uniqid();

		//get the group
		$gid = JRequest::getVar('gid');

		//import the Hubzero Group Library
		ximport('Hubzero_Group');

		//get the group object based on gid
		$group = Hubzero_Group::getInstance($gid);

		//check to make sure we have a valid group
		if(!is_object($group)) {
			return;
		}

		//define a base url
		$base_url = DS . "site" . DS . "groups" . DS . $group->get('gidNumber');

		//seperate image list into array of images
		$slides = explode(",",$content);

		//array for checked slides
		$final_slides = array();

		//check each passed in slide
		foreach($slides as $slide) {
			//check to see if image is external
			if(strpos($slide,"http") === false) {
				//check if internal file actually exists
				if(is_file( JPATH_ROOT . $base_url . DS . $slide)) {
					$final_slides[] = $base_url . DS . $slide;
				}
			} else {
				$headers = get_headers($slide);
				if(strpos($headers[0],"OK") !== false) {
					$final_slides[] = $slide;
				}
			}
		}

		$html = "";
		$html .= "<div class=\"wiki_slider\">";
			$html .= "<div id=\"slider_{$id}\">";
			foreach($final_slides as $fs) {
				$html .= "<img src=\"{$fs}\" alt=\"\" />";
			}
			$html .= "</div>";
			$html .= "<div class=\"wiki_slider_pager\" id=\"slider_{$id}_pager\"></div>";
		$html .= "</div>";

		$document =& JFactory::getDocument();
		$document->addStyleSheet('plugins/hubzero/wikiparser/macros/macro-assets/slider/slider.css');
		$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js');
		$document->addScript('plugins/hubzero/wikiparser/macros/macro-assets/slider/slider.js');
		$document->addScriptDeclaration('
			var $jQ = jQuery.noConflict();
			
			$jQ(function() {	
				$jQ("#slider_'.$id.'").cycle({
					fx: \'scrollHorz\',
					speed: 450,
					pager: \'#slider_'.$id.'_pager\'
				});
			});
		');

		return $html;
	}
}
?>