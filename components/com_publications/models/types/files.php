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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * FILES master type helper class
 */
class typeFiles extends JObject
{
	/**
	 * JDatabase
	 *
	 * @var object
	 */
	var $_database        = NULL;

	/**
	 * Project
	 *
	 * @var object
	 */
	var $_project      	  = NULL;

	/**
	 * Base alias
	 *
	 * @var string
	 */
	var $_base   		  = 'files';

	/**
	 * Attachment type
	 *
	 * @var string
	 */
	var $_attachmentType  = 'file';

	/**
	 * Selection type (single/multi)
	 *
	 * @var boolean
	 */
	var $_multiSelect 	  = true;

	/**
	 * Allow change to selection after draft is started?
	 *
	 * @var boolean
	 */
	var $_changeAllowed   = true;

	/**
	 * Allow to create a new publication with exact same content?
	 *
	 * @var boolean
	 */
	var $_allowDuplicate  = true;

	/**
	 * Unique attachment properties
	 *
	 * @var array
	 */
	var $_attProperties  = array('path', 'vcs_hash');

	/**
	 * Data
	 *
	 * @var array
	 */
	var $_data   		 = array();

	/**
	 * Serve as (default value)
	 *
	 * @var string
	 */
	var $_serveas   	= 'download';

	/**
	 * Serve as choices
	 *
	 * @var string
	 */
	var $_serveChoices  = array('download');

	/**
	 * Constructor
	 *
	 * @param      object  &$db      	 JDatabase
	 * @return     void
	 */
	public function __construct( &$db, $project = NULL, $data = array() )
	{
		$this->_database = $db;
		$this->_project  = $project;
		$this->_data 	 = $data;
	}

	/**
	 * Set
	 *
	 * @param      string 	$property
	 * @param      string 	$value
	 * @return     mixed
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get
	 *
	 * @param      string 	$property
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property]))
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Dispatch task
	 *
	 * @param      string  $task
	 * @return     void
	 */
	public function dispatch( $task = NULL )
	{
		$output 		 = NULL;

		switch ( $task )
		{
			case 'getServeAs':
				$output = $this->_getServeAs();
				break;

			case 'checkContent':
				$output = $this->_checkContent();
				break;

			case 'checkMissing':
				$output = $this->_checkMissing();
				break;

			case 'drawItem':
				$output = $this->_drawItem();
				break;

			case 'saveAttachments':
				$output = $this->_saveAttachments();
				break;

			case 'publishAttachments':
				$output = $this->_publishAttachments();
				break;

			case 'cleanupAttachments':
				$output = $this->_cleanupAttachments();
				break;

			case 'getPubTitle':
				$output = $this->_getPubTitle();

			default:
				break;
		}

		return $output;
	}

	/**
	 * Get serveas options (_showOptions function in plg_projects_publications)
	 *
	 * @return     void
	 */
	protected function _getServeAs()
	{
		$serveas = $this->_serveas;
		$choices = $this->_serveChoices;

		// Incoming data
		$selections	 	  = $this->__get('selections');
		$params			  = $this->__get('params');
		$original_serveas = $this->__get('original_serveas');

		// Formats that can be previewed via Google viewer
		$docs 	= array('pdf', 'doc', 'docx', 'xls', 'xlsx',
			'ppt', 'pptx', 'pages', 'ai',
			'psd', 'tiff', 'dxf', 'eps', 'ps', 'ttf', 'xps', 'svg'
		);

		$html5video = array('mp4','m4v','webm','ogv'); // formats for HTML5 video

		// Allow viewing files via Google Doc viewer?
		$googleView	= $params->get('googleview');

		// Determine how to serve content
		if (!empty($selections))
		{
			// Files
			if (isset($selections['files']) && !empty($selections['files']))
			{
				$count = count($selections['files']);

				// Get file mimetype
				$mt = new \Hubzero\Content\Mimetypes();
				$mimetypes = array();

				foreach($selections['files'] as $file)
				{
					$mtype = $mt->getMimeType(urldecode($file));
					$parts = explode('/', $mtype);
					$mtype = array_shift($parts);
					$mimetypes[] = strtolower($mtype);
				}

				// If one file, determine how to serve (to be extended)
				if ($count == 1)
				{
					$sf  = explode('.', basename($selections['files'][0]));
					$ext = array_pop($sf);
					$ext = strtolower($ext);

					// Some files can be viewed inline
					if (in_array('video', $mimetypes)
						|| in_array('audio', $mimetypes)
						|| in_array('image', $mimetypes))
					{
						$serveas = $original_serveas && $original_serveas != 'tardownload' ? $original_serveas : 'inlineview';

						// Offer choice
						$choices[] = 'inlineview';
					}
					elseif ($googleView && in_array(strtolower($ext), $docs))
					{
						$serveas = $original_serveas && $original_serveas != 'tardownload' ? $original_serveas : 'download';

						// Offer choice
						$choices[] = 'inlineview';
					}
					else
					{
						$serveas = 'download';
					}
				}
				else
				{
					// More than 1 file
					$serveas = 'tardownload';
				}
			}
		}

		$result = array('serveas' => $serveas, 'choices' => $choices);

		return $result;
	}

	/**
	 * Get publication title for newly created draft
	 *
	 * @return     void
	 */
	protected function _getPubTitle()
	{
		// Incoming data
		$item = $this->__get('item');

		// File name is the title
		return $item;

	}

	/**
	 * Check content
	 *
	 * @return     void
	 */
	protected function _checkContent()
	{
		// Incoming data
		$attachments = $this->__get('attachments');

		if ($attachments && count($attachments) > 0)
		{
			return true;
		}

		return false;
	}

	/**
	 * Check missing content
	 *
	 * @return     void
	 */
	protected function _checkMissing()
	{
		// Incoming data
		$item  = $this->__get('item');
		$path  = $this->__get('fpath');

		if (!$item || !$path)
		{
			return false;
		}

		return is_file($path . DS . $item->path) ? false : true;
	}

	/**
	 * Cleanup publication attachments when others are picked
	 *
	 * @return     void
	 */
	protected function _cleanupAttachments()
	{
		// Incoming data
		$selections 	= $this->__get('selections');
		$vid  			= $this->__get('vid');
		$pid  			= $this->__get('pid');
		$uid  			= $this->__get('uid');
		$old  			= $this->__get('old');
		$secret  		= $this->__get('secret');

		if (empty($selections) || !isset($selections['files']))
		{
			return false;
		}

		if (!in_array(trim($old->path), $selections['files']))
		{
			$objPA = new PublicationAttachment( $this->_database );
			$objPA->deleteAttachment($vid, $old->path, $old->type);

			if ($secret)
			{
				$this->_unpublishAttachment($pid, $vid, $old->path, $secret);
			}
		}

		return true;
	}

	/**
	 * Save picked items as publication attachments
	 *
	 * @return     void
	 */
	protected function _saveAttachments()
	{
		// Incoming data
		$selections 	= $this->__get('selections');
		$option  		= $this->__get('option');
		$vid  			= $this->__get('vid');
		$pid  			= $this->__get('pid');
		$uid  			= $this->__get('uid');
		$update_hash  	= $this->__get('update_hash');
		$primary  		= $this->__get('primary');
		$added  		= $this->__get('added');
		$serveas  		= $this->__get('serveas');
		$state  		= $this->__get('state');
		$secret  		= $this->__get('secret');

		// Go through file selections
		if (isset($selections['files']) && count($selections['files']) > 0)
		{
			// Load component configs
			$config = JComponentHelper::getParams( 'com_projects' );

			// Git helper
			include_once( JPATH_ROOT . DS . 'components' . DS .'com_projects' . DS . 'helpers' . DS . 'githelper.php' );
			$git = new ProjectsGitHelper(
				$config->get('gitpath', '/opt/local/bin/git'),
				$uid
			);

			// Get project file path
			$fpath = ProjectsHelper::getProjectPath($this->_project->alias,
					$config->get('webpath'), $config->get('offroot'));

			$objPA = new PublicationAttachment( $this->_database );

			// Attach every selected file
			foreach ($selections['files'] as $file)
			{
				$path = urldecode($file);

				// Get Git hash
				$vcs_hash = $git->gitLog($fpath, urldecode($file), '', 'hash');

				$originalHash = $objPA->vcs_hash;

				if ($objPA->loadAttachment($vid, $path, $this->_attachmentType))
				{
					// Update only if no hash recorded (or update requested)
					$objPA->vcs_hash 				= $objPA->vcs_hash && $update_hash == 0 ? $objPA->vcs_hash : $vcs_hash;
					$objPA->modified_by 			= $uid;
					$objPA->modified 				= JFactory::getDate()->toSql();
				}
				else
				{
					$objPA 							= new PublicationAttachment( $this->_database );
					$objPA->publication_id 			= $pid;
					$objPA->publication_version_id 	= $vid;
					$objPA->path 					= $path;
					$objPA->type 					= $this->_attachmentType;
					$objPA->vcs_hash 				= $vcs_hash;
					$objPA->created_by 				= $uid;
					$objPA->created 				= JFactory::getDate()->toSql();
				}

				$objPA->ordering 					= $added + 1;
				$objPA->role 						= $primary;
				$objPA->title 						= $objPA->title ? $objPA->title : '';
				$objPA->params 						= $primary  == 1 && $serveas ? 'serveas=' . $serveas : $objPA->params;

				if ($objPA->store())
				{
					$added++;
					if ($secret && ($state != 1 || ($state == 1 && !$primary)))
					{
						$this->_publishAttachment($pid, $vid, $objPA->path, $originalHash, $secret);
					}
				}
			}
		}

		return $added;
	}

	/**
	 * Publish content attachment
	 *
	 * @param      integer  	$pid
	 * @param      integer  	$vid
	 * @param      string  		$fpath
	 * @param      string  		$hash
	 * @param      string  		$secret
	 *
	 * @return     boolean or error
	 */
	protected function _publishAttachment ($pid, $vid, $fpath, $hash, $secret)
	{
		// Get publications helper
		$helper = new PublicationHelper($this->_database, $vid, $pid);

		// Get projects helper
		$projectsHelper = new ProjectsHelper( $this->_database );

		// Load component configs
		$pubconfig = JComponentHelper::getParams( 'com_publications' );
		$config = JComponentHelper::getParams( 'com_projects' );

		// Remove files that got unselected from a finalized draft
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		// Build publication path
		$base_path 	= $pubconfig->get('webpath');
		$devpath 	= $helper->buildDevPath($this->_project->alias);
		$newpath 	= $helper->buildPath($pid, $vid, $base_path, $secret, 1);
		$gitpath 	= $config->get('gitpath', '/opt/local/bin/git');

		// Create new version path
		if (!is_dir( $newpath ))
		{
			if (!JFolder::create( $newpath ))
			{
				$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_UNABLE_TO_CREATE_PATH') );
				return '<p class="error">' . $this->getError() . '</p>';
			}
		}

		// Get latest commit hash
		$latest = $projectsHelper->showGitInfo($gitpath, $devpath, $hash, $fpath, 2);

		// If parent dir does not exist, we must create it
		if (!file_exists(dirname($newpath. DS .$fpath)))
		{
			JFolder::create(dirname($newpath. DS .$fpath));
		}

		// Copy file if there (will be at latest revision)
		if (is_file($devpath. DS .$fpath))
		{
			if (!is_file($newpath. DS .$fpath) || $hash != $latest['hash'])
			{
				JFile::copy($devpath. DS .$fpath, $newpath. DS .$fpath);
			}
		}

		return true;
	}

	/**
	 * Unpublish content attachment
	 *
	 * @param      integer  	$pid
	 * @param      integer  	$vid
	 * @param      string  		$fpath
	 * @param      string  		$secret
	 *
	 * @return     boolean or error
	 */
	protected function _unpublishAttachment($pid, $vid, $fpath, $secret)
	{
		// Get publications helper
		$helper = new PublicationHelper($this->_database, $vid, $pid);

		// Remove files that got unselected from a finalized draft
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		// Load component configs
		$pubconfig = JComponentHelper::getParams( 'com_publications' );
		$config = JComponentHelper::getParams( 'com_projects' );

		// Build publication path
		$base_path  = $pubconfig->get('webpath');
		$newpath 	= $helper->buildPath($pid, $vid, $base_path, $secret, 1);

		if (is_dir( $newpath ))
		{
			$file =  $newpath. DS .$fpath;
			if (is_file($file))
			{
				JFile::delete($file);
			}
		}

		return true;
	}

	/**
	 * Draw selected item html
	 *
	 * @return     void
	 */
	protected function _drawItem()
	{
		// Incoming data
		$att   		= $this->__get('att');
		$item   	= $this->__get('item');
		$path  		= $this->__get('path');
		$canedit  	= $this->__get('canedit');
		$move  		= $this->__get('move');
		$option  	= $this->__get('option');
		$url  		= $this->__get('url');
		$vid  		= $this->__get('vid');
		$pid  		= $this->__get('pid');
		$role  		= $this->__get('role');

		$item		= $att->id ? $att->path : $item;
		$file 		= str_replace($path . DS, '', $item);
		$ext 		= explode('.', $file);
		$ext 		= end($ext);
		$ext 		= strtolower($ext);
		$revision 	= NULL;

		// Get file revision
		if ($this->_project && $this->_project->provisioned != 1)
		{
			// Get projects helper
			$projectsHelper = new ProjectsHelper( $this->_database );

			// Load component configs
			$config = JComponentHelper::getParams( 'com_projects' );

			// Git path
			$gitpath  = $config->get('gitpath', '/opt/local/bin/git');
			$revision = $projectsHelper->showGitInfo($gitpath, $path, $att->vcs_hash, $file);
		}

		$html = '<img src="' . ProjectsHtml::getFileIcon($ext) . '" alt="' . $ext . '" /> ' . ProjectsHtml::shortenFileName($file, 50);
		if($canedit && $pid) {
		$html .= '<span class="c-edit"><a href="' . $url . '?vid=' . $vid
		. '&item=file::'.urlencode($file) . '&move=' . $move . '&action=edititem&role=' . $role . '" class="showinbox">' . ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_EDIT')) . '</a></span>';
		}
		$html .= '<span class="c-iteminfo">';
		if ($att->id)
		{
			$html .= $att->title ? '"' . $att->title . '"' : JText::_('PLG_PROJECTS_PUBLICATIONS_NO_DESCRIPTION');
			$html .= $revision ? ' &middot; ' . $revision : '';
		}
		else
		{
			$html .= JText::_('PLG_PROJECTS_PUBLICATIONS_NO_DESCRIPTION');
		}
		$html .= '</span>';
		return $html;
	}

	/**
	 * Publish attachments (draft submission step)
	 *
	 * @return     void
	 */
	protected function _publishAttachments()
	{
		// Incoming data
		$attachments= $this->__get('attachments');
		$row  		= $this->__get('row');
		$uid  		= $this->__get('uid');

		$published = 0;

		// Get helpers
		$helper = new PublicationHelper($this->_database, $row->id, $row->publication_id);
		$projectsHelper = new ProjectsHelper( $this->_database );

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		// Load component configs
		$pubconfig = JComponentHelper::getParams( 'com_publications' );
		$config = JComponentHelper::getParams( 'com_projects' );

		// Build publication paths
		$base_path 	= $pubconfig->get('webpath');
		$devpath 	= $helper->buildDevPath($this->_project->alias);
		$newpath 	= $helper->buildPath($row->publication_id, $row->id, $base_path, $row->secret, 1);
		$gitpath 	= $config->get('gitpath', '/opt/local/bin/git');

		// Create new version path
		if (!is_dir( $newpath ))
		{
			if (!JFolder::create( $newpath ))
			{
				$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_UNABLE_TO_CREATE_PATH') );
				return '<p class="error">' . $this->getError() . '</p>';
			}
		}

		foreach ($attachments as $att)
		{
			if ($att->type != $this->_attachmentType)
			{
				continue;
			}

			// Get latest commit hash
			$latest = $projectsHelper->showGitInfo($gitpath, $devpath, $att->vcs_hash, $att->path, 2);

			// If parent dir does not exist, we must create it
			if (!file_exists(dirname($newpath. DS .$att->path)))
			{
				JFolder::create(dirname($newpath. DS .$att->path));
			}

			// Copy file if there (will be at latest revision)
			if (is_file($devpath. DS .$att->path))
			{
				JFile::copy($devpath. DS .$att->path, $newpath. DS .$att->path);
			}
			else
			{
				// Check out revision when file was added
				chdir($devpath);
				exec($gitpath.' checkout '.$att->vcs_hash.' '.escapeshellarg($att->path).' 2>&1', $out);
				if (file_exists($devpath. DS . $att->path))
				{
					JFile::copy($devpath. DS . $att->path, $newpath. DS .$att->path);
				}
				// Check back latest revision
				exec($gitpath.' checkout '.$latest['hash'].' '.escapeshellarg($att->path).' 2>&1', $out);
			}

			// Copy succeeded?
			if (is_file($newpath. DS . $att->path))
			{
				$objAtt = new PublicationAttachment( $this->_database );
				$objAtt->load($att->id);
				$objAtt->vcs_hash 	  = $latest['hash'];
				$objAtt->modified_by  = $uid;
				$objAtt->modified 	  = JFactory::getDate()->toSql();
				$objAtt->content_hash = hash_file('sha256', $newpath. DS .$att->path);

				// Create hash file
				$hfile =  $newpath . DS .$att->path . '.hash';
				if (!is_file($hfile))
				{
					$handle = fopen($hfile, 'w');
					fwrite($handle, $objAtt->content_hash);
					fclose($handle);
					chmod($hfile, 0644);
				}
				$objAtt->store();
				$published++;
			}
		}

		return $published;
	}
}