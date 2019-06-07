<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Site\Controllers;
use Hubzero\Component\SiteController;
use Components\Publications\Helpers;
use Components\Publications\Models;

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
		$source = null;

		// Need pub and version ID
		if (!$pid || $pid == 0 || !$vid)
		{
			return;
		}

		// Get the file name
		$uri = Request::getString('REQUEST_URI', '', 'server');
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
				$publication = new Models\Publication($pid, null, $vid);
				// Get the image if exists
				$source = $publication->hasImage('master');

				// Default image
				if (!$source)
				{
					$source = PATH_CORE . DS . trim($this->config->get('masterimage', 'components/com_publications/site/assets/img/master.png'), DS);
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
