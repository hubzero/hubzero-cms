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
	 * @return	void
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
	 * @return     string
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
		jimport('joomla.filesystem.file');
		$file = \JFile::makeSafe($file);

		// Check project exists
		if (!$this->model->exists())
		{
			echo json_encode(array('error' => Lang::txt('Error loading project')));
			return;
		}

		// Make sure user is authorized (project manager)
		if (!$this->model->access('manager'))
		{
			echo json_encode(array('error' => Lang::txt('Unauthorized action')));
			return;
		}

		// Build project image path
		$path  = PATH_APP . DS . trim($this->config->get('imagepath', '/site/projects'), DS);
		$path .= DS . $this->model->get('alias') . DS . 'images';

		if (!is_dir( $path ))
		{
			jimport('joomla.filesystem.folder');
			if (!\JFolder::create( $path ))
			{
				echo json_encode(array('error' => Lang::txt('COM_PROJECTS_UNABLE_TO_CREATE_UPLOAD_PATH')));
				return;
			}
		}

		// Delete older file with same name
		if (file_exists($path . DS . $file))
		{
			\JFile::delete($path . DS . $file);
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
				$hi->autoRotate();
				$hi->resize(200);
				$hi->setImageType(IMAGETYPE_PNG);
				$hi->save($path . DS . $file);
			}
			else
			{
				echo json_encode(array('error' => $hi->getError()));
				return;
			}

			// Delete previous thumb
			if (file_exists($path . DS . 'thumb.png'))
			{
				\JFile::delete($path . DS . 'thumb.png');
			}

			// create thumb
			$hi = new \Hubzero\Image\Processor($path . DS . $file);
			if (count($hi->getErrors()) == 0)
			{
				$hi->resize(50, false, true, true);
				$hi->save($path . DS . 'thumb.png');
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
	 * @return     void
	 */
	public function deleteimgTask()
	{
		// Incoming
		$ajax = Request::getInt( 'ajax', 0 );

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
		if (!$this->model->exists() || !$this->model->access('manager'))
		{
			$this->setError( Lang::txt('COM_PROJECTS_ERROR_NO_ID') );
			if ($ajax)
			{
				echo json_encode(array('error' => $this->getError()));
				return;
			}
			$this->_showError();
			return;
		}

		// Incoming file
		$file = Request::getVar( 'file', '' );
		$file = $file ? $file : $this->model->get('picture');
		if (!$file)
		{
			$this->setError( Lang::txt('COM_PROJECTS_FILE_NOT_FOUND') );
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
			$this->setError( Lang::txt('COM_PROJECTS_FILE_NOT_FOUND') );
			if ($ajax)
			{
				echo json_encode(array('error' => $this->getError()));
				return;
			}
		}
		else
		{
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!\JFile::delete($path . DS . $file))
			{
				$this->setError( Lang::txt('COM_PROJECTS_UNABLE_TO_DELETE_FILE') );
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
				if (!\JFile::delete($path . DS . $curthumb))
				{
					$this->setError( Lang::txt('COM_PROJECTS_UNABLE_TO_DELETE_FILE') );
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
					$this->setError( $this->model->getError() );
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
		$this->_redirect = Route::url('index.php?option=' . $this->_option . '&alias=' . $this->model->get('alias'));
		return;
	}

	/**
	 * Show images within projects
	 *
	 * @return     void
	 */
	public function mediaTask()
	{
		// Incoming
		$media   = trim(Request::getVar( 'media', 'thumb' ));
		$source	 = NULL;
		$redirect= false;
		$dir	 = 'preview';

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

		// Show project thumbnail
		if ($media == 'thumb')
		{
			$source = Helpers\Html::getThumbSrc( $this->model->get('alias'), '', $this->config );
		}
		elseif ($media)
		{
			if ($media == 'master')
			{
				// Public picture
				$source = Helpers\Html::getProjectImageSrc( $this->model->get('alias'), $this->model->get('picture'), $this->config );
			}
			else
			{
				// Authorization required
				if (!$this->model->access('member'))
				{
					return;
				}

				$path     = trim($this->config->get('imagepath', '/site/projects'), DS);
				$source   = $path . DS . $this->model->get('alias') . DS . $dir . DS . $media;
				$redirect = true;
			}
		}

		if (is_file(PATH_APP . DS . $source))
		{
			$xserver = new \Hubzero\Content\Server();
			$xserver->filename($source);
			$xserver->serve_inline(PATH_APP . DS . $source);
			exit;
		}
		elseif ($redirect)
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option)
			);
		}

		return;
	}
}
