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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Publications controller class for media
 */
class PublicationsControllerMedia extends \Hubzero\Component\SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		$parts = explode('/', $_SERVER['REQUEST_URI']);

		$file = array_pop($parts);

		if (substr(strtolower($file), 0, 5) == 'image'
		 || substr(strtolower($file), 0, 4) == 'file')
		{
			JRequest::setVar('task', 'download');
		}

		parent::execute();
	}

	/**
	 * Download a file
	 *
	 * @return     void
	 */
	public function downloadTask()
	{
		// Incoming
		$pid 	= JRequest::getInt('id', 0);
		$vid 	= JRequest::getInt( 'v', 0 );
		$source = NULL;

		// Need pub and version ID
		if (!$pid || $pid == 0 || !$vid)
		{
			return;
		}

		// Get the file name
		$uri = JRequest::getVar('REQUEST_URI', '', 'server');
		if (strstr($uri, 'Image:'))
		{
			$file = str_replace('Image:', '', strstr($uri, 'Image:'));
		}
		elseif (strstr($uri, 'File:'))
		{
			$file = str_replace('File:', '', strstr($uri, 'File:'));
		}

		//decode file name
		$file = urldecode($file);

		$pubHelper = new PublicationHelper(
			$this->database,
			$vid,
			$pid
		);

		if (strtolower($file) == 'thumb')
		{
			// Get publication thumbnail
			$source = $pubHelper->getThumb($pid, $vid, $this->config );
		}
		else
		{
			// Build publication path
			$path = $pubHelper->buildPath($pid, $vid, $this->config->get('webpath'));

			if (strtolower($file) == 'master')
			{
				// Get master image
				$source = $path . DS . 'master.png';

				// Default image
				if (!is_file(JPATH_ROOT . DS . $source))
				{
					// Grab first bigger image in gallery
					if (is_dir(JPATH_ROOT . DS . $path . DS . 'gallery'))
					{
						$file_list = scandir(JPATH_ROOT . DS . $path . DS . 'gallery');
						foreach ($file_list as $file)
						{
							list($width, $height, $type, $attr) = getimagesize(JPATH_ROOT . DS . $path . DS . 'gallery' . DS . $file);
							if ($width > 200)
							{
								$source = $path . DS . 'gallery' . DS . $file;
								break;
							}
						}
					}
					if (!is_file(JPATH_ROOT . DS . $source))
					{
						$source = $this->config->get('masterimage',
						'/components/com_publications/assets/img/master.png');
					}
				}
			}
			else
			{
				// Load from gallery
				$source = $path . DS . 'gallery' . DS . $file;

				// Default image
				if (!is_file(JPATH_ROOT . DS . $source))
				{
					$source = $this->config->get('gallery_thumb',
					'/components/com_publications/assets/img/gallery_thumb.gif');
				}
			}
		}

		if (is_file(JPATH_ROOT . DS . $source))
		{
			$xserver = new \Hubzero\Content\Server();
			$xserver->filename($source);
			$xserver->serve_inline(JPATH_ROOT . DS . $source);
			exit;
		}

		return;
	}
}