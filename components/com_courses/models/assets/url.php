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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
* Url Asset handler class
*/
class UrlAssetHandler extends ContentAssetHandler
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
		$url = JRequest::getVar('content');

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
			if (!preg_match('/^(http[s]*\:\/\/)?([0-9A-Za-z\.\/\-\=\:\?\_\&\%\~]+)$/', $url, $matches))
			{
				return array('error'=>'Content did not match the pre-defined filter');
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
				$content .= '<iframe width="560" height="315" src="http://www.youtube.com/embed/';
				$content .= $matches[2];
				$content .= '?rel=0" frameborder="0" allowfullscreen></iframe>';
				return array('type'=>'content', 'value'=>$content);
			}
			else
			{
				return array('type'=>'default');
			}
		}
		else
		{
			return array('type'=>'default');
		}
	}
}