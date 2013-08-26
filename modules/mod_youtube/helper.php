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
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for displaying a YouTube feed
 */
class modYoutubeHelper extends JObject
{
	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Constructor
	 * 
	 * @param      object $params JParameter
	 * @param      object $module Database row
	 * @return     void
	 */
	public function __construct($params, $module)
	{
		$this->params = $params;
		$this->module = $module;
	}

	/**
	 * Set a property
	 * 
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->attributes[$property])) 
		{
			return $this->attributes[$property];
		}
	}

	/**
	 * Check if a property is set
	 * 
	 * @param      string $property Property to check
	 * @return     boolean True if set
	 */
	public function __isset($property)
	{
		return isset($this->_attributes[$property]);
	}

	/**
	 * Display module contents
	 * 
	 * @return     void
	 */
	public function display()
	{
		//get the document
		$jdocument =& JFactory::getDocument();

		//get the module id
		$id = $this->module->id;

		//define the base youtube url
		$youtube_url = 'https://gdata.youtube.com/feeds/api/';

		//default # of videos to display
		$default_num_videos = 3;

		//get the user defined num of videos
		$user_num_videos = $this->params->get('videos');

		//determine the final num of videos to show
		$num_videos = ($user_num_videos != '' && is_numeric($user_num_videos)) ? $user_num_videos : $default_num_videos;

		//get the type of feed we are displaying
		$type = $this->params->get('type');

		//get the username/playlist/search term
		$content = $this->params->get('q');

		//build the youtube url based on the type
		switch($type)
		{
			case 'playlists':
				$youtube_url .= 'playlists/' . $content . '?v=2';
			break;
			case 'users':
				$youtube_url .= 'users/' . $content . '/uploads?v=2';
			break;
			case 'videos':
				$youtube_url .= 'videos?q=' . $content . '&v=2';
			break;
		}
		
		//append the the return type and the callback function
		$youtube_url .= '&alt=json';
		
		//get title,desc,logo and link params
		$show_title = $this->params->get('title');
		$alt_title  = $this->params->get('alttitle');
		$show_desc  = $this->params->get('desc');
		$alt_desc   = $this->params->get('altdesc');
		$show_image = $this->params->get('image');
		$alt_image  = $this->params->get('altimage');
		$show_link  = $this->params->get('link');
		$alt_link   = $this->params->get('altlink');

		//are we randomizing videos
		$random = $this->params->get('random');

		//are we using js or PHP
		$lazy_loading = $this->params->get('lazy');

		//Push some CSS to the template
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStylesheet($this->module->module);

		//push the container that the feed with loaded in
		$this->id = $id;
		$this->lazy = $lazy_loading;

		//if we are lazy loading
		if ($lazy_loading) 
		{
			Hubzero_Document::addModuleScript($this->module->module);

			if (JPluginHelper::isEnabled('system', 'jquery'))
			{
				$jdocument->addScriptDeclaration("
					jQuery(document).ready(function($){
						var youtubefeed = $('#youtube_feed_" . $id . "').youtube({
							type: '" . $type . "',
							search: '" . $content . "',
							count: " . $num_videos . ",
							random: " . $random . ",
							details: {
								showLogo: " . $show_image . ",
								altLogo: '" . $alt_image . "',
								showTitle: " . $show_title . ",
								altTitle: '" . $alt_title . "',
								showDesc: " . $show_desc . ",
								altDesc: '" . $alt_desc . "',
								showLink: " . $show_link . ",
								altLink: '" . $alt_link . "'
							}
						});
					});
				");
			} 
			else 
			{
				$jdocument->addScriptDeclaration("
					window.addEvent('domready', function() {
						var youtubefeed = new HUB.Youtube('youtube_feed_" . $id . "',{
							type: '" . $type . "',
							search: '" . $content . "',
							count: " . $num_videos . ",
							random: " . $random . ",
							details: {
								showLogo: " . $show_image . ",
								altLogo: '" . $alt_image . "',
								showTitle: " . $show_title . ",
								altTitle: '" . $alt_title . "',
								showDesc: " . $show_desc . ",
								altDesc: '" . $alt_desc . "',
								showLink: " . $show_link . ",
								altLink: '" . $alt_link . "'
							}
						});
					});
				");
			}
		} 
		else 
		{
			//get the youtube url's headers
			$headers = get_headers($youtube_url);

			//load joomla folder and file libraries
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.file');

			//cache path
			$path = JPATH_ROOT . DS . 'cache' . DS . 'mod_youtube' . DS . $id;
			$data = $path . DS . $type . '.txt';

			//check if we have cached already
			if ($this->params->get('cache') && is_file($data) && filemtime($data) > strtotime('-' . $this->params->get('cache_time') . ' MINUTES')) 
			{
				$feed = file_get_contents($data);
			} 
			elseif (strpos($headers[0], 'OK') !== false) 
			{
				$feed = file_get_contents($youtube_url);
			} 
			else 
			{
				$this->html = '<p class="error">' . JText::_('An Error occurred while trying to parse the Youtube Feed.') . '</p>';
				return;
			}

			$full_feed = json_decode($feed, true);
			$feed = $full_feed['feed'];

			//get the entries from the feed
			$entries = $feed['entry'];

			//start building the html content
			$html = '';

			//get the title, subtitle, logo
			$title = $feed['title']['$t'];
			if ($type == 'playlists') 
			{
				$desc = $feed['subtitle']['$t'];
			}
			$logo = $feed['logo']['$t'];

			//show title based on params
			if ($show_title) 
			{
				if ($alt_title != '') 
				{
					$html .= '<h3>' . $alt_title . '</h3>';
				} 
				else 
				{
					$html .= '<h3>' . $title . '</h3>';
				}
			}

			//show the description based on params
			if ($show_desc) 
			{
				if ($alt_desc != '') 
				{
					$html .= '<p class="description">' . $alt_desc . '</p>';
				} 
				elseif($type == 'playlists') 
				{
					$html .= '<p class="description">' . $desc . '</p>';
				}
			}

			//show the logo based on your
			if ($show_image) 
			{
				if ($alt_image != '' && is_file(JPATH_ROOT . DS . $alt_image)) 
				{
					$html .= '<img class="logo" src="' . $alt_image . '" alt="Youtube" />';
				} 
				else 
				{
					$html .= '<img class="logo" src="' . $logo . '" alt="Youtube" />';
				}
			}

			//are we supposed to randomize
			if ($random) 
			{
				shuffle($entries);
			}

			//display the videos
			$html .= "<ul>";
			$counter = 1;
			foreach ($entries as $entry)
			{
				if ($counter <= $num_videos) 
				{
					$media = $entry['media$group'];
					$html .= "<li>";
					$html .= "<a class=\"entry-thumb\" rel=\"external\" href=\"{$entry['link'][0]['href']}\"><img src=\"{$media['media$thumbnail'][3]['url']}\" alt=\"\" /></a>";
					$html .= "<a class=\"entry-title\" rel=\"external\" href=\"{$entry['link'][0]['href']}\">{$entry['title']['$t']}</a>";
					$html .= "<br /><span class=\"entry-duration\">" . $this->_formatTime($media['yt$duration']['seconds']) . "</span>";
					$html .= "</li>";
				}
				$counter++;
			}
			$html .= "</ul>";

			//show the view more link based on params
			if ($show_link) 
			{
				if ($alt_link != '') 
				{
					$html .= "<p class=\"more\"><a rel=\"external\" title=\"More on Youtube\" href=\"{$alt_link}\">More Videos &rsaquo;</a></p><br class=\"clear\" />";
				} 
				else 
				{
					switch ($type)
					{
						case 'playlists':
							$link = "http://www.youtube.com/view_play_list?p=" . $content;
						break;
						case 'users':
							$link = "http://www.youtube.com/user/" . $content;
						break;
						case 'videos':
							$link = "http://www.youtube.com/results?search_query=" . $content;
						break;
					}
					$html .= "<p class=\"more\"><a rel=\"external\" title=\"More on Youtube\" href=\"{$link}\">More Videos &rsaquo;</a></p><br class=\"clear\" />";
				}
			}

			//if we want to use caching
			if ($this->params->get('cache')) 
			{
				//write to the cache folder
				if (!is_dir($path)) 
				{
					JFolder::create($path, 0777);
				}

				$full_feed = json_encode($full_feed);
				JFile::write($data, $full_feed);
			}
			
			$this->html = $html;
		}
		
		require(JModuleHelper::getLayoutPath($this->module->module));
	}

	/**
	 * Format a time
	 * 
	 * @param      integer $seconds Time to format
	 * @return     string
	 */
	private function _formatTime($seconds)
	{
		$minutes = floor($seconds / 60);
		$seconds = $seconds % 60;

		if ($seconds < 10) 
		{
			$seconds = "0{$seconds}";
		}

		return "<span>{$minutes}:{$seconds}</span>";
	}
}
