<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Assets;

use Request;

/**
 * Url Asset handler class
 */
class Url extends Content
{
	/**
	 * Class info
	 *
	 * Action message - what the user will see if presented with multiple handlers for this extension
	 * Responds to    - what extensions this handler responds to
	 *
	 * @var array
	 **/
	protected static $info = array(
			'action_message' => 'As a standard link',
			'responds_to'    => array('url')
		);

	/**
	 * Create method for this handler
	 *
	 * @return array of assets created
	 **/
	public function create()
	{
		$url = Request::getString('content');

		// Allow for multiple entries at once
		if (strstr($url, ','))
		{
			$urls = explode(',', $url);
			$urls = array_map('trim', $urls);
		}
		elseif (strstr($url, ' '))
		{
			$urls = explode(' ', $url);
			$urls = array_map('trim', $urls);
		}
		elseif (strstr($url, "\n"))
		{
			$urls = explode("\n", $url);
			$urls = array_map('trim', $urls);
		}
		elseif (strstr($url, "\r\n"))
		{
			$urls = explode("\r\n", $url);
			$urls = array_map('trim', $urls);
		}
		else
		{
			$urls = (array) $url;
		}

		$return = array();

		foreach ($urls as $url)
		{
			if (!preg_match('/^(http[s]*\:\/\/)?([0-9A-Za-z\.\/\-\=\:\?\_\&\%\~\(\)]+)$/', $url, $matches))
			{
				return array('error' => 'Content did not match the pre-defined filter');
			}

			// Try to help users out by being a little smarter about url provided
			if (!preg_match('/^http[s]*\:\/\//', $url) && strstr($url, '.'))
			{
				$url = 'http://' . $url;
			}

			$this->asset['title']   = $matches[2];
			$this->asset['type']    = 'url';
			$this->asset['subtype'] = 'link';
			$this->asset['url']     = $url;

			// Return info
			$r = parent::create();
			$return[] = $r['assets'];
		}

		return $return;
	}

	/**
	 * Preview method for this handler
	 *
	 * @param  object $asset - asset
	 * @return array((string) type, (string) text)
	 **/
	public function preview($asset)
	{
		if (preg_match('/http[s]*:\/\/(www\.)?youtube.com\/watch\?v=([0-9A-Za-z_-]+)/', $asset->get('url'), $matches))
		{
			if (isset($matches[2]) && !empty($matches[2]))
			{
				$content  = '';
				$content .= '<iframe width="560" height="315" src="https://www.youtube.com/embed/';
				$content .= $matches[2];
				$content .= '?rel=0" frameborder="0" allowfullscreen></iframe>';
				return array('type' => 'content', 'value' => $content);
			}
			else
			{
				return array('type' => 'default');
			}
		}
		else
		{
			return array('type' => 'default');
		}
	}
}
