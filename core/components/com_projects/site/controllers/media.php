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

namespace Components\Projects\Site\Controllers;

use Components\Projects\Tables;
use Components\Projects\Helpers;

/**
 * Projects media controller class
 */
class Media extends Base
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Set the default task
		$this->registerTask('__default', 'media');
		$this->registerTask('thumb', 'media');

		parent::execute();
	}

	/**
	 * Upload a file to the profile via AJAX
	 *
	 * @return  string
	 */
	public function doajaxuploadTask()
	{
		//allowed extensions for uplaod
		$allowedExtensions = array('png', 'jpe', 'jpeg', 'jpg', 'gif');

		//max upload size
		$sizeLimit = $this->config->get('maxAllowed', '40000000');

		// get the file
		if (isset($_GET['qqfile']))
		{
			$stream = true;
			$file = $_GET['qqfile'];
			$size = (int) $_SERVER["CONTENT_LENGTH"];
		}
		elseif (isset($_FILES['qqfile']))
		{
			$stream = false;
			$file = $_FILES['qqfile']['name'];
			$size = (int) $_FILES['qqfile']['size'];
		}
		else
		{
			echo json_encode(array('error' => Lang::txt('Please select a file to upload')));
			return;
		}

		//check to make sure we have a file and its not too big
		if ($size == 0)
		{
			echo json_encode(array('error' => Lang::txt('File is empty')));
			return;
		}
		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));
			echo json_encode(array('error' => Lang::txt('File is too large. Max file upload size is ') . $max));
			return;
		}

		//check to make sure we have an allowable extension
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];
		$ext      = $pathinfo['extension'];
		if ($allowedExtensions && !in_array(strtolower($ext), $allowedExtensions))
		{
			$these = implode(', ', $allowedExtensions);
			echo json_encode(array('error' => Lang::txt('File has an invalid extension, it should be one of '. $these . '.')));
			return;
		}

		// Make the filename safe
		$file = Filesystem::clean($file);

		// Check project exists
		if (!$this->model->exists())
		{
			echo json_encode(array('error' => Lang::txt('Error loading project')));
			return;
		}

		// Make sure user is authorized (project manager)
		if (!$this->model->access('manager') && !($this->model->access('content') && $this->config->get('edit_description')))
		{
			echo json_encode(array('error' => Lang::txt('Unauthorized action')));
			return;
		}

		// Build project image path
		$path  = PATH_APP . DS . trim($this->config->get('imagepath', '/site/projects'), DS);
		$path .= DS . $this->model->get('alias') . DS . 'images';

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path, 0755, true, true))
			{
				echo json_encode(array('error' => Lang::txt('COM_PROJECTS_UNABLE_TO_CREATE_UPLOAD_PATH')));
				return;
			}
		}

		// Delete older file with same name
		if (file_exists($path . DS . $file))
		{
			Filesystem::delete($path . DS . $file);
		}

		if ($stream)
		{
			//read the php input stream to upload file
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);

			if (Helpers\Html::virusCheck($temp))
			{
				echo json_encode(array('error' => Lang::txt('Virus detected, refusing to upload')));
				return;
			}

			//move from temp location to target location which is user folder
			$target = fopen($path . DS . $file , "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
		}
		else
		{
			move_uploaded_file($_FILES['qqfile']['tmp_name'], $path . DS . $file);
		}

		// Perform the upload
		if (!is_file($path . DS . $file))
		{
			echo json_encode(array('error' => Lang::txt('COM_PROJECTS_ERROR_UPLOADING')));
			return;
		}
		else
		{
			//resize image to max 200px and rotate in case user didnt before uploading
			$hi = new \Hubzero\Image\Processor($path . DS . $file);
			if (count($hi->getErrors()) == 0)
			{
				try
				{
					$hi->autoRotate();
					$hi->resize(200);
					$hi->setImageType(IMAGETYPE_PNG);
					$hi->save($path . DS . $file);
				}
				catch (\Exception $e)
				{
					if (strpos($e->getMessage(), 'Illegal IFD size') !== false)
					{
						// PURR #1186, QUBES #618
						continue;
					}
					else
					{
						App::abort('500', $e);
					}
				}
			}
			else
			{
				echo json_encode(array('error' => $hi->getError()));
				return;
			}

			// Delete previous thumb
			if (file_exists($path . DS . 'thumb.png'))
			{
				Filesystem::delete($path . DS . 'thumb.png');
			}

			// create thumb
			$hi = new \Hubzero\Image\Processor($path . DS . $file);
			if (count($hi->getErrors()) == 0)
			{
				$hi->resize(50, false, true, true);
				try
				{
					$hi->save($path . DS . 'thumb.png');
				}
				catch (\Exception $e)
				{
					error_log($e->getMessage());
				}
			}
			else
			{
				echo json_encode(array('error' => $hi->getError()));
				return;
			}

			// Save picture name
			$this->model->set('picture', $file);
			if (!$this->model->store())
			{
				echo json_encode(array('error' => $this->model->getError()));
				return;
			}
			elseif (!$this->model->inSetup())
			{
				// Record activity
				$this->model->recordActivity(Lang::txt('COM_PROJECTS_REPLACED_PROJECT_PICTURE'));
			}
		}

		echo json_encode(array(
			'success'   => true
		));
		return;
	}

	/**
	 * Delete image
	 *
	 * @return  void
	 */
	public function deleteimgTask()
	{
		// Incoming
		$ajax = Request::getInt('ajax', 0);

		// Check if they are logged in
		if (User::isGuest())
		{
			if ($ajax)
			{
				echo json_encode(array('error' => Lang::txt('User login required')));
				return;
			}
			$this->_showError();
			return;
		}

		// Incoming project ID
		$hasAccess = ($this->model->access('manager') || ($this->model->access('content') && $this->config->get('edit_description')));

		if (!$this->model->exists() || !$hasAccess)
		{
			$this->setError(Lang::txt('COM_PROJECTS_ERROR_NO_ID'));
			if ($ajax)
			{
				echo json_encode(array('error' => $this->getError()));
				return;
			}
			$this->_showError();
			return;
		}

		// Incoming file
		$file = Request::getVar('file', '');
		$file = $file ? $file : $this->model->get('picture');
		if (!$file)
		{
			$this->setError(Lang::txt('COM_PROJECTS_FILE_NOT_FOUND'));
			if ($ajax)
			{
				echo json_encode(array('error' => $this->getError()));
				return;
			}
			$this->_showError();
			return;
		}

		// Build path
		$webdir = DS . trim($this->config->get('imagepath', '/site/projects'), DS);
		$path   = PATH_APP . $webdir . DS . $this->model->get('alias') . DS . 'images';

		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(Lang::txt('COM_PROJECTS_FILE_NOT_FOUND'));
			if ($ajax)
			{
				echo json_encode(array('error' => $this->getError()));
				return;
			}
		}
		else
		{
			// Attempt to delete the file
			if (!Filesystem::delete($path . DS . $file))
			{
				$this->setError(Lang::txt('COM_PROJECTS_UNABLE_TO_DELETE_FILE'));
				if ($ajax)
				{
					echo json_encode(array('error' => $this->getError()));
					return;
				}
				$this->_showError();
				return;
			}

			// Delete thumbnail
			$curthumb = Helpers\Html::createThumbName($file);
			$curthumb = file_exists($path . DS . $curthumb) ? $curthumb : 'thumb.png';
			if (file_exists($path . DS . $curthumb))
			{
				if (!Filesystem::delete($path . DS . $curthumb))
				{
					$this->setError(Lang::txt('COM_PROJECTS_UNABLE_TO_DELETE_FILE'));
					if ($ajax)
					{
						echo json_encode(array('error' => $this->getError()));
						return;
					}
					$this->_showError();
					return;
				}
			}

			// Instantiate a project, change some info and save
			if (!file_exists($path . DS . $file))
			{
				$this->model->set('picture', '');
				if (!$this->model->store())
				{
					$this->setError($this->model->getError());
					if ($ajax)
					{
						echo json_encode(array('error' => $this->model->getError()));
						return;
					}
					return;
				}
			}
		}

		if ($ajax && $this->getError())
		{
			echo json_encode(array('error' => $this->getError()));
			return;
		}
		elseif ($ajax)
		{
			echo json_encode(array(
				'success'   => true
			));
			return;
		}

		// Go to error page
		if ($this->getError())
		{
			$this->_showError();
		}

		// Return to project page
		App::redirect(Route::url($this->model->link()));
		return;
	}

	/**
	 * Show images within projects
	 *
	 * @return  void
	 */
	public function mediaTask()
	{
		// Incoming
		$media    = trim(Request::getVar('media', 'thumb'));
		$source   = NULL;
		$redirect = false;
		$dir      = 'preview';

		if (!$this->model->exists())
		{
			return false;
		}

		$uri = Request::getVar('SCRIPT_URL', '', 'server');
		if (strstr($uri, 'Compiled:'))
		{
			$media = str_replace('Compiled:', '', strstr($uri, 'Compiled:'));
			$dir   = 'compiled';
		}
		if (strstr($uri, 'Tool:'))
		{
			$media = str_replace('Tool:', '', strstr($uri, 'Tool:'));
			$media = $media ? $media : 'default';
			$dir   = 'tools';
		}

		// Show project thumbnail
		if ($media == 'thumb')
		{
			$source = $this->getThumbSrc();
		}
		elseif ($media)
		{
			if ($media == 'master')
			{
				// Public picture
				$source = $this->getProjectImageSrc();
			}
			elseif ($dir == 'tools')
			{
				$path     = trim($this->config->get('imagepath', '/site/projects'), DS);
				$source   = PATH_APP . DS . $path . DS . $this->model->get('alias') . DS . $dir . DS . $media . '.png';

				if (!is_file($source))
				{
					// Get default tool image
					$source = PATH_CORE . DS . trim($this->config->get('toolpic', 'plugins/projects/tools/images/default.gif'), DS);
				}
			}
			else
			{
				// Authorization required
				if (!$this->model->access('member'))
				{
					return;
				}

				$path     = trim($this->config->get('imagepath', '/site/projects'), DS);
				$source   = PATH_APP . DS . $path . DS . $this->model->get('alias') . DS . $dir . DS . $media;
				$redirect = true;
			}
		}

		if (is_file($source))
		{
			$server = new \Hubzero\Content\Server();
			$server->filename($source);
			$server->serve_inline($source);
			exit;
		}
		elseif ($redirect)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
		}
	}

	/**
	 * Get project image source
	 *
	 * @return  string
	 */
	public function getProjectImageSrc()
	{
		if (!$this->model->exists())
		{
			return false;
		}

		$path      = trim($this->config->get('imagepath', '/site/projects'), DS) . DS . $this->model->get('alias') . DS . 'images';
		$masterpic = trim($this->config->get('masterpic', 'components/com_projects/site/assets/img/projects-large.gif'), DS);
		if ($masterpic == 'components/com_projects/assets/img/projects-large.gif')
		{
			$masterpic = 'components/com_projects/site/assets/img/projects-large.gif';
		}
		$default = PATH_CORE . DS . $masterpic;

		$default = is_file($default) ? $default : NULL;

		$src  = $this->model->get('picture')
				&& is_file(PATH_APP . DS . $path . DS . $this->model->get('picture'))
				? PATH_APP . DS . $path . DS . $this->model->get('picture')
				: $default;
		return $src;
	}

	/**
	 * Get project thumbnail source
	 *
	 * @return  string
	 */
	public function getThumbSrc()
	{
		if (!$this->model->exists())
		{
			return false;
		}

		$src  = '';
		$path = PATH_APP . DS . trim($this->config->get('imagepath', '/site/projects'), DS) . DS . $this->model->get('alias') . DS . 'images';

		if (file_exists($path . DS . 'thumb.png'))
		{
			return $path . DS . 'thumb.png';
		}

		if ($this->model->get('picture'))
		{
			$thumb = Helpers\Html::createThumbName($this->model->get('picture'));
			$src = $thumb && file_exists($path . DS . $thumb) ? $path . DS . $thumb : NULL;
		}

		if (!$src)
		{
			$path = trim($this->config->get('defaultpic', 'components/com_projects/site/assets/img/project.png'), DS);
			if ($path == 'components/com_projects/assets/img/project.png')
			{
				$path = 'components/com_projects/site/assets/img/project.png';
			}
			$src = PATH_CORE . DS . $path;
		}

		return $src;
	}
}
