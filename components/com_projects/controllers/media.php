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
 * Projects media controller class
 */
class ProjectsControllerMedia extends ProjectsControllerBase
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
			echo json_encode(array('error' => 'Please select a file to upload'));
			return;
		}

		//check to make sure we have a file and its not too big
		if ($size == 0)
		{
			echo json_encode(array('error' => 'File is empty'));
			return;
		}
		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));
			echo json_encode(array('error' => 'File is too large. Max file upload size is ' . $max));
			return;
		}
		//check to make sure we have an allowable extension
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];
		$ext      = $pathinfo['extension'];
		if ($allowedExtensions && !in_array(strtolower($ext), $allowedExtensions))
		{
			$these = implode(', ', $allowedExtensions);
			echo json_encode(array('error' => 'File has an invalid extension, it should be one of '. $these . '.'));
			return;
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file = JFile::makeSafe($file);

		// Load project
		$obj = new Project( $this->database );
		if (!$obj->loadProject($this->_identifier))
		{
			echo json_encode(array('error' => 'Error loading project'));
			return;
		}

		// Make sure user is authorized (project manager)
		$authorized = $this->_authorize();
		if ($authorized != 1)
		{
			echo json_encode(array('error' => 'Unauthorized action'));
			return;
		}

		// Build project image path
		$path  = JPATH_ROOT . DS . trim($this->config->get('imagepath', '/site/projects'), DS);
		$path .= DS . $obj->alias . DS . 'images';

		if (!is_dir( $path ))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path ))
			{
				echo json_encode(array('error' => JText::_('COM_PROJECTS_UNABLE_TO_CREATE_UPLOAD_PATH')));
				return;
			}
		}

		// Delete older file with same name
		if (file_exists($path . DS . $file))
		{
			JFile::delete($path . DS . $file);
		}

		if ($stream)
		{
			//read the php input stream to upload file
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);

			if (ProjectsHelper::virusCheck($temp))
			{
				JFile::delete($temp);
				echo json_encode(array('error' => JText::_('Virus detected, refusing to upload')));
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
			echo json_encode(array('error' => JText::_('COM_PROJECTS_ERROR_UPLOADING')));
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
				JFile::delete($path . DS . 'thumb.png');
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
			$obj->picture = $file;
			if (!$obj->store())
			{
				echo json_encode(array('error' => $obj->getError()));
				return;
			}
			elseif ($obj->setup_stage >= $this->_setupComplete)
			{
				// Record activity
				$this->project = $obj;
				$this->_postActivity(JText::_('COM_PROJECTS_REPLACED_PROJECT_PICTURE'));
			}
		}

		echo json_encode(array(
			'success'   => true
		));
	}

	/**
	 * Delete image
	 *
	 * @return     void
	 */
	public function deleteimgTask()
	{
		// Incoming
		$ajax = JRequest::getInt( 'ajax', 0 );

		$prefix = JPATH_ROOT;

		// Check if they are logged in
		if ($this->juser->get('guest'))
		{
			if ($ajax)
			{
				echo json_encode(array('error' => JText::_('User login required')));
				return;
			}
			$this->_showError();
			return;
		}

		// Incoming project ID
		if (!$this->_identifier)
		{
			$this->setError( JText::_('COM_PROJECTS_ERROR_NO_ID') );
			if ($ajax)
			{
				echo json_encode(array('error' => $this->getError()));
				return;
			}
			$this->_showError();
			return;
		}

		// Load project
		$obj = new Project( $this->database );
		$obj->loadProject($this->_identifier);

		if ($obj->alias)
		{
			$dir = $obj->alias;
		}
		else
		{
			$dir = \Hubzero\Utility\String::pad( $obj->id );
		}

		// Incoming file
		$file = JRequest::getVar( 'file', '' );
		$file = $file ? $file : $obj->picture;
		if (!$file)
		{
			$this->setError( JText::_('COM_PROJECTS_FILE_NOT_FOUND') );
			if ($ajax)
			{
				echo json_encode(array('error' => $this->getError()));
				return;
			}
			$this->_showError();
			return;
		}

		$webdir = DS . trim($this->config->get('imagepath', '/site/projects'), DS);
		$path   = $prefix . $webdir;
		$path  .= !$this->_identifier && $tempid ? DS . 'temp' : '';
		$path  .= DS . $dir;
		$tpath  = $path;
		$path  .= DS . 'images';

		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError( JText::_('COM_PROJECTS_FILE_NOT_FOUND') );
			if ($ajax)
			{
				echo json_encode(array('error' => $this->getError()));
				return;
			}
		}
		else
		{
			$ih = new ProjectsImgHandler();

			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path . DS . $file))
			{
				$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_DELETE_FILE') );
				if ($ajax)
				{
					echo json_encode(array('error' => $this->getError()));
					return;
				}
				$this->_showError();
				return;
			}

			// Delete thumbnail
			$curthumb = $ih->createThumbName($file);
			$curthumb = file_exists($path . DS . $curthumb) ? $curthumb : 'thumb.png';
			if (file_exists($path . DS . $curthumb))
			{
				if (!JFile::delete($path . DS . $curthumb))
				{
					$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_DELETE_FILE') );
					if ($ajax)
					{
						echo json_encode(array('error' => $this->getError()));
						return;
					}
					$this->_showError();
					return;
				}
			}

			// Clean up temp folder
			if (!$this->_identifier && $tempid)
			{
				jimport('joomla.filesystem.folder');
				JFolder::delete( $tpath);
			}

			// Instantiate a project, change some info and save
			if ($obj->id && !file_exists($path . DS . $file))
			{
				$obj->picture = '';
				if (!$obj->store())
				{
					$this->setError( $obj->getError() );
					if ($ajax)
					{
						echo json_encode(array('error' => $obj->getError()));
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
		$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&alias=' . $obj->alias);
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
		$media   = trim(JRequest::getVar( 'media', 'thumb' ));
		$alias 	 = trim(JRequest::getVar( 'alias', '' ));
		$source	 = NULL;
		$redirect= false;

		if (!$alias)
		{
			return false;
		}

		// Show project thumbnail
		if ($media == 'thumb')
		{
			$source = ProjectsHtml::getThumbSrc( $alias, '', $this->config );
		}
		elseif ($media)
		{
			$obj = new Project( $this->database );
			if (!$obj->loadProject($alias))
			{
				return false;
			}

			if ($media == 'master')
			{
				// Public picture
				$source = ProjectsHtml::getProjectImageSrc( $alias, $obj->picture, $this->config );
			}
			else
			{
				// Other images are non-public; in 'preview' folder
				// Check authorization
				/*
				if (!$this->_authorize())
				{
					JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
					return;
				}
				*/
				$path 	= trim($this->config->get('imagepath', '/site/projects'), DS);
				$source = $path . DS . $alias . DS . 'preview' . DS . $media;
				$redirect = true;
			}
		}

		if (is_file(JPATH_ROOT . DS . $source))
		{
			$xserver = new \Hubzero\Content\Server();
			$xserver->filename($source);
			$xserver->serve_inline(JPATH_ROOT . DS . $source);
			exit;
		}
		elseif ($redirect)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option)
			);
		}

		return;
	}
}
