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
 * Projects controller class
 */
class ProjectsControllerMedia extends \Hubzero\Component\SiteController
{
	/**
	 * Upload project image
	 *
	 * @return     void
	 */
	public function uploadTask()
	{
		// How many steps in setup process?
		$setup_complete = $this->config->get('confirm_step', 0) ? 3 : 2;
		$prefix = JPATH_ROOT;

		// Check if they are logged in
		if ($this->juser->get('guest'))
		{
			return false;
		}

		// Incoming project ID
		$id 	= JRequest::getInt( 'id', 0 );
		$tempid = JRequest::getInt( 'tempid', 0 );
		if (!$id && !$tempid)
		{
			$this->setError( JText::_('COM_PROJECTS_ERROR_NO_ID') );
			$this->imgTask( $id, $tempid );
			return;
		}

		// Check authorization - extra check
		if ($id)
		{
			$authorized = $this->_authorize($id);
			if (!$authorized)
			{
				JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
				return;
			}
		}

		// Incoming file
		$file = JRequest::getVar( 'upload', '', 'files', 'array' );
		if (!$file['name'])
		{
			$this->setError( JText::_('COM_PROJECTS_NO_FILE') );
			$this->imgTask( $id, $tempid );
			return;
		}

		// Build upload path
		$useid = $id ? $id : $tempid;

		// Use if or alias?
		if ($id)
		{
			$obj = new Project( $this->database );
			$dir = $obj->getAlias( $id );
		}
		else
		{
			$dir = \Hubzero\Utility\String::pad( $useid );
		}
		$webdir = DS . trim($this->config->get('imagepath', '/site/projects'), DS);
		$path  = $prefix . $webdir;
		$path .= !$id && $tempid ? DS . 'temp' : '';
		$path .= DS . $dir . DS . 'images';

		if (!is_dir( $path ))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path ))
			{
				$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_CREATE_UPLOAD_PATH') );
				$this->imgTask( $id, $tempid );
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ','_',$file['name']);

		// Do we have an old file we're replacing?
		$curfile = JRequest::getVar( 'currentfile', '' );

		// Check it's an image in allowed format
		$ext = explode('.', $file['name']);
		$ext = end($ext);

		if (!in_array($ext, array('jpg', 'gif', 'png')))
		{
			$this->setError( JText::_('Format unsupported. Please upload a .jpg, .gif or .png') );
			$this->imgTask( $id, $tempid, $curfile );
			return false;
		}

		// Delete older file with same name
		if (file_exists($path . DS . $file['name']))
		{
			JFile::delete($path . DS . $file['name']);
		}

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError( JText::_('COM_PROJECTS_ERROR_UPLOADING') );
			$file = $curfile;
		}
		else
		{
			if (ProjectsHelper::virusCheck($path . DS . $file['name']))
			{
				$this->setError(JText::_('Virus detected, refusing to upload'));
				$this->imgTask( $id, $tempid );
				return;
			}

			$ih = new ProjectsImgHandler();

			// Resize the image if necessary
			$ih->set('image',$file['name']);
			$ih->set('path',$path.DS);
			$ih->set('maxWidth', 186);
			$ih->set('maxHeight', 186);
			if (!$ih->process())
			{
				JFile::delete($path . DS . $file['name']);
				$this->setError( $ih->getError() );
			}

			// Delete previous thumb
			if (file_exists($path . DS . 'thumb.png'))
			{
				JFile::delete($path . DS . 'thumb.png');
			}

			// Create a thumbnail image
			$ih->set('maxWidth', 50);
			$ih->set('maxHeight', 50);
			$ih->set('cropratio', '1:1');
			$ih->set('outputName', 'thumb.png');
			if (!$ih->process())
			{
				JFile::delete($path . DS . $file['name']);
				$this->setError( $ih->getError() );
			}

			$file = $file['name'];

			// Instantiate a project, change some info and save
			if (!$this->getError() && $id)
			{
				$obj = new Project( $this->database );
				$obj->loadProject($id);
				$obj->picture = $file;
				if (!$obj->store())
				{
					$this->setError( $obj->getError() );
				}
				elseif ($obj->setup_stage >= $setup_complete)
				{
					// Record activity
					$objAA = new ProjectActivity( $this->database );
					$aid = $objAA->recordActivity( $id, $this->juser->get('id'),
						JText::_('COM_PROJECTS_REPLACED_PROJECT_PICTURE'), $id, '',
						'', 'project', 0 );
				}
			}

			// Remove old images
			if (!$this->getError() && $curfile != '' && $curfile != $file )
			{
				if (file_exists($path . DS . $curfile))
				{
					JFile::delete($path . DS . $curfile);
				}
				$curthumb = $ih->createThumbName($curfile);
				if (file_exists($path . DS . $curthumb))
				{
					JFile::delete($path . DS . $curthumb);
				}
			}
		}

		if ($this->getError())
		{
			$this->imgTask( $id, $tempid, $curfile );
			return;
		}

		// Push through to the image view
		$this->imgTask( $id, $tempid );
	}

	/**
	 * Delete image
	 *
	 * @return     void
	 */
	public function deleteimgTask()
	{
		$prefix = JPATH_ROOT;

		// Incoming
		$this->_id 			= JRequest::getInt( 'id', 0 );
		$this->_alias   	= JRequest::getVar( 'alias', '' );
		$this->_identifier  = $this->_id ? $this->_id : $this->_alias;

		// Check if they are logged in
		if ($this->juser->get('guest'))
		{
			return false;
		}

		// Incoming project ID
		$tempid = JRequest::getInt( 'tempid', 0 );
		if (!$this->_identifier && !$tempid)
		{
			$this->setError( JText::_('COM_PROJECTS_ERROR_NO_ID') );
			$this->imgTask( $this->_identifier, $tempid );
			return;
		}

		// Load project
		$obj = new Project( $this->database );
		$obj->loadProject($this->_identifier);

		// Build the file path
		$useid = $obj->id ? $obj->id : $tempid;

		if ($obj->alias)
		{
			$dir = $obj->alias;
		}
		else
		{
			$dir = \Hubzero\Utility\String::pad( $useid );
		}

		// Incoming file
		$file = JRequest::getVar( 'file', '' );
		$file = $file ? $file : $obj->picture;
		if (!$file)
		{
			$this->setError( JText::_('COM_PROJECTS_FILE_NOT_FOUND') );
			$this->imgTask( $this->_identifier, $tempid );
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
		}
		else
		{
			$ih = new ProjectsImgHandler();

			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path . DS . $file))
			{
				$this->setError( JText::_('COM_PROJECTS_UNABLE_TO_DELETE_FILE') );
				$this->imgTask( $this->_identifier, $tempid, $file );
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
					$this->imgTask( $this->_identifier, $tempid );
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
				}
			}
		}

		// Push through to the image view
		$this->imgTask( $obj->id, $tempid );
	}

	/**
	 * Display project image and upload form
	 *
	 * @param  string $file
	 * @param  int $id
	 * @param  int $tempid
	 * @return void
	 */
	public function imgTask( $id = 0, $tempid = 0, $file = '' )
	{
		// Incoming
		$this->_id 			= JRequest::getInt( 'id', 0 );
		$this->_alias   	= JRequest::getVar( 'alias', '' );
		$this->_identifier  = $this->_id ? $this->_id : $this->_alias;
		$this->_identifier	= $id ? $id : $this->_identifier;
		$tempid 			= $tempid ? $tempid : JRequest::getInt( 'tempid', 0, 'get' );

		$useid 				= $this->_identifier ? $this->_identifier : $tempid;
		$prefix 			= JPATH_ROOT;

		// Load project
		$obj = new Project( $this->database );
		$obj->loadProject($this->_identifier);

		if ($obj->alias)
		{
			$dir = $obj->alias;
		}
		else
		{
			$dir = \Hubzero\Utility\String::pad( $useid );
		}

		// Build the file path
		$webdir = DS . trim($this->config->get('imagepath', '/site/projects'), DS);
		$path  = $webdir;
		$path .= !$this->_identifier && $tempid ? DS . 'temp' : '';
		$path .= DS . $dir . DS . 'images';

		$file = $file ? $file : $obj->picture;

		// set the needed layout
		$this->view->setLayout('img');

		// Output HTML
		$this->view->option 			= $this->_option;
		$this->view->webpath 			= $webdir;
		$this->view->default_picture 	= $this->config->get('defaultpic');
		$this->view->path 				= $path;
		$this->view->file 				= $file;

		$ih = new ProjectsImgHandler();
		$this->view->thumb 			= !file_exists($prefix . $path . DS . 'thumb.png')
									&& file_exists($prefix . $path . DS . $file)
									? $ih->createThumbName($file) : 'thumb.png';

		$this->view->file_path 		= $prefix . $path;
		$this->view->id 			= $obj->id;
		$this->view->tempid 		= $tempid;
		if ($this->getError())
		{
			$this->view->setError( $this->getError() );
		}
		$this->view->display();
	}

	/**
	 * Authorize users
	 *
	 * @param  int $projectid
	 * @param  int $check_site_admin
	 * @return void
	 */
	protected function _authorize( $projectid = 0, $check_site_admin = 0 )
	{
		// Check login
		if ($this->juser->get('guest'))
		{
			return false;
		}

		// Check whether user belongs to the project
		if ($projectid != 0)
		{
			$pOwner = new ProjectOwner( $this->database );
			if ($result = $pOwner->isOwner($this->juser->get('id'), $projectid))
			{
				return $result;
			}
		}

		// Check if they're a site admin (from Joomla)
		if ($check_site_admin)
		{
			if ($this->juser->get('id') && $this->juser->authorize($this->_option, 'manage'))
			{
				return 'admin';
			}
		}

		return false;
	}
}
