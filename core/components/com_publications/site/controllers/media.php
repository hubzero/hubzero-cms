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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Site\Controllers;
use Hubzero\Component\SiteController;
use Components\Publications\Helpers;

/**
 * Publications controller class for media
 */
class Media extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$parts = explode('/', $_SERVER['REQUEST_URI']);

		$file = array_pop($parts);

		if (substr(strtolower($file), 0, 5) == 'image'
		 || substr(strtolower($file), 0, 4) == 'file')
		{
			Request::setVar('task', 'download');
		}

		parent::execute();
	}

	/**
	 * Download a file
	 *
	 * @return  void
	 */
	public function downloadTask()
	{
		// Incoming
		$pid    = Request::getInt('id', 0);
		$vid    = Request::getInt( 'v', 0 );
		$source = NULL;

		// Need pub and version ID
		if (!$pid || $pid == 0 || !$vid)
		{
			return;
		}

		// Get the file name
		$uri = Request::getVar('REQUEST_URI', '', 'server');
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

		if (strtolower($file) == 'thumb')
		{
			// Get publication thumbnail
			$source = Helpers\Html::getThumb($pid, $vid, $this->config );
		}
		else
		{
			// Build publication path
			$path = Helpers\Html::buildPubPath($pid, $vid, $this->config->get('webpath'));

			if (strtolower($file) == 'master')
			{
				// Get master image
				$source = $path . DS . 'master.png';

				// Default image
				if (!is_file(PATH_APP . DS . $source))
				{
					// Grab first bigger image in gallery
					if (is_dir(PATH_APP . DS . $path . DS . 'gallery'))
					{
						$file_list = scandir(PATH_APP . DS . $path . DS . 'gallery');
						foreach ($file_list as $file)
						{
							list($width, $height, $type, $attr) = getimagesize(PATH_APP . DS . $path . DS . 'gallery' . DS . $file);
							if ($width > 200)
							{
								$source = $path . DS . 'gallery' . DS . $file;
								break;
							}
						}
					}
					if (!is_file(PATH_APP . DS . $source))
					{
						$source = PATH_CORE . DS . trim($this->config->get('masterimage', 'components/com_publications/site/assets/img/master.png'), DS);
					}
				}
			}
			else
			{
				// Load from gallery
				$source = PATH_APP . DS . $path . DS . 'gallery' . DS . $file;

				// Default image
				if (!is_file($source))
				{
					$source = PATH_CORE . DS . trim($this->config->get('gallery_thumb', 'components/com_publications/site/assets/img/gallery_thumb.gif'), DS);
				}
			}
		}

		if (is_file($source))
		{
			$server = new \Hubzero\Content\Server();
			$server->filename($source);
			$server->serve_inline($source);
			exit;
		}

		return;
	}
}