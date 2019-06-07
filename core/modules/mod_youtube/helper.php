<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Youtube;

use Hubzero\Module\Module;
use Filesystem;
use Lang;

/**
 * Module class for displaying a YouTube feed
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
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

		//get the user's api key
		$google_api_browser_key = $this->params->get('google_api_browser_key');

		//build the youtube url based on the type
		switch ($type)
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
		$this->css();

		//push the container that the feed with loaded in
		$this->id = $id;
		$this->lazy = $lazy_loading;

		//if we are lazy loading
		if ($lazy_loading)
		{
			$this->js();
			$this->js("
				jQuery(document).ready(function($){
					var youtubefeed = $('#youtube_feed_" . $id . "').youtube({
						google_api_browser_key: '" . $google_api_browser_key . "',
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
			// load feed
			$feed = $this->_feed($youtube_url, $this->params);
			if (!$feed)
			{
				$this->html = '<p class="error">' . Lang::txt('MOD_YOUTUBE_ERROR_PARSING_FEED') . '</p>';
				require $this->getLayoutPath();
				return;
			}

			// access youtubes weird feed item
			$feed = $feed['feed'];

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
				elseif ($type == 'playlists')
				{
					$html .= '<p class="description">' . $desc . '</p>';
				}
			}

			//show the logo based on your
			if ($show_image)
			{
				if ($alt_image != '' && is_file(PATH_APP . DS . $alt_image))
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
					$html .= "<p class=\"more\"><a rel=\"external\" title=\"" . Lang::txt('MOD_YOUTUBE_MORE_ON_YOUTUBE') . "\" href=\"{$alt_link}\">" . Lang::txt('MOD_YOUTUBE_MORE_VIDEOS') . "</a></p><br class=\"clear\" />";
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
					$html .= "<p class=\"more\"><a rel=\"external\" title=\"" . Lang::txt('MOD_YOUTUBE_MORE_ON_YOUTUBE') . "\" href=\"{$link}\">" . Lang::txt('MOD_YOUTUBE_MORE_VIDEOS') . "</a></p><br class=\"clear\" />";
				}
			}

			$this->html = $html;
		}

		require $this->getLayoutPath();
	}

	/**
	 * Get feed info
	 *
	 * @param   string  $url
	 * @param   object  $params
	 * @return  object
	 */
	private function _feed($url, $params)
	{
		// var to hold feed
		$feed = null;

		// cache path
		$cachePath = PATH_APP . DS . 'cache' . DS . 'mod_youtube' . DS . $this->module->id;
		$cacheFile = $cachePath . DS . $params->get('type') . '.txt';

		// do we want to load a cached version
		if ($this->params->get('cache')
			&& file_exists($cacheFile)
			&& filemtime($cacheFile) > strtotime('-' . $this->params->get('cache_time') . ' MINUTES'))
		{
			$feed = file_get_contents($cacheFile);
			$feed = json_decode($feed);
		}
		else
		{
			// get the feed with curl
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_REFERER, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$feed = curl_exec($ch);
			curl_close($ch);

			//if we want to use caching
			if ($params->get('cache'))
			{
				//write to the cache folder
				if (!is_dir($cachePath))
				{
					Filesystem::makedirectory($cachePath);
				}
				$f = json_encode($feed);

				Filesystem::write($cacheFile, $f);
			}
		}

		// return jsto
		return json_decode($feed, true);
	}

	/**
	 * Format a time
	 *
	 * @param   integer  $seconds  Time to format
	 * @return  string
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
