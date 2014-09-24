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
ini_set('display_errors', '1');
error_reporting(E_ALL | E_STRICT);

/**
 * Manage publication batch
 */
class PublicationsControllerBatchcreate extends \Hubzero\Component\AdminController
{
	/**
	 * Start page
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		// set layout
		$this->view->setLayout('display');

		$project = new Project($this->database);

		// Get filters
		$filters = array();
		$filters['sortby']  	= 'title';
		$filters['sortdir'] 	= 'DESC';
		$filters['authorized'] 	= true;
		$projects = $project->getRecords( $filters, true, 0, 1 );

		$this->view->lists['projects'] = PublicationsAdminHtml::selectProjects($projects);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Download XSD
	 *
	 * @return     void
	 */
	public function xsdTask()
	{
		$path = $this->getSchema();

		if (file_exists($path))
		{
			$xserver = new \Hubzero\Content\Server();
			$xserver->filename($path);
			$xserver->disposition('attachment');
			$xserver->acceptranges(false); // @TODO fix byte range support
			$xserver->saveas(basename($path));

			if (!$xserver->serve())
			{
				// Should only get here on error
				JError::raiseError( 404, JText::_('COM_PUBLICATIONS_SERVER_ERROR') );
			}
			else
			{
				exit;
			}
		}
		else
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('COM_PUBLICATIONS_BATCH_ERROR_NO_XSD'),
				'error'
			);
		}

		return;
	}

	/**
	 * Validate XML
	 *
	 * @return     void
	 */
	public function validateTask()
	{
		if (!isset($this->reader) || !$this->reader->isValid())
		{
			$this->setError(JText::_('COM_PUBLICATIONS_BATCH_ERROR'));
			$this->reader->close();
			return;
		}

		// Collect all errors
		while (@$this->reader->read()) { }; // empty loop
		$errors = libxml_get_errors();

		// Errors found - stop
		if (count($errors) > 0)
		{
			$this->setError(JText::_('COM_PUBLICATIONS_BATCH_ERROR_XML_VALIDATION_FAILED'));
			$output = '<pre>';
			foreach ($errors as $error)
			{
			     $output .=  $this->displayXmlError($error, $this->data);
			}
			$output .= '</pre>';
			$this->reader->close();
			return $output;
		}
	}

	/**
	 * Import, validate and parse data
	 *
	 * @param   integer $dryRun
	 * @return  void
	 */
	public function processTask( $dryRun = 0 )
	{
		// check token
		JSession::checkToken() or die( 'Invalid Token' );

		// Incoming
		$id 	= JRequest::getInt('projectid', 0);
		$file   = JRequest::getVar('file', array(), 'FILES');
		$dryRun = JRequest::getInt('dryrun', 1);

		$this->data = NULL;

		// Project ID must be supplied
		$this->project = new Project($this->database);
		if (!$id || !$this->project->load($id))
		{
			echo json_encode(array('result' => 'error', 'error' => JText::_('COM_PUBLICATIONS_BATCH_ERROR_NO_PROJECT_ID'), 'records' => NULL));
			exit();
		}

		// Check for file
		if (!is_array($file) || $file['size'] == 0 || $file['error'] != 0)
		{
			echo json_encode(array('result' => 'error', 'error' => JText::_('COM_PUBLICATIONS_BATCH_ERROR_NO_FILE'), 'records' => NULL));
			exit();
		}

		// Check for correct type
		if (!in_array($file['type'], array('application/xml', 'text/xml')))
		{
			echo json_encode(array('result' => 'error', 'error' => JText::_('COM_PUBLICATIONS_BATCH_ERROR_WRONG_FORMAT'), 'records' => NULL));
			exit();
		}

		// Get data from XML file
		if (is_uploaded_file($file['tmp_name']))
		{
			$this->data = file_get_contents($file['tmp_name']);
		}
		if (!$this->data)
		{
			echo json_encode(array('result' => 'error', 'error' => JText::_('COM_PUBLICATIONS_BATCH_ERROR_NO_DATA'), 'records' => NULL));
			exit();
		}

		// Load reader
		libxml_use_internal_errors(true);
		$this->reader   = new \XMLReader();

		// Open and validate XML against schema
		if (!$this->reader->XML($this->data, 'UTF-8', \XMLReader::VALIDATE | \XMLReader::SUBST_ENTITIES))
		{
			echo json_encode(array(
				'result' 	=> 'error',
				'error' 	=> JText::_('COM_PUBLICATIONS_BATCH_ERROR_XML_VALIDATION_FAILED'),
				'records' 	=> NULL)
			);
			exit();
		}

		// Set schema
		$schema = $this->getSchema();
		if (file_exists($schema))
		{
			$this->reader->setSchema($schema);
		}

		// Validation
		$outputData = $this->validateTask();

		// Parse data if passed validations
		if (!$this->getError())
		{
			$outputData = $this->parse($dryRun);
		}

		// Parsing errors
		if ($this->getError())
		{
			echo json_encode(array(
				'result' 	=> 'error',
				'error' 	=> $this->getError(),
				'records' 	=> $outputData,
				'dryrun'	=> $dryRun)
			);
			exit();
		}

		// return results to user
		echo json_encode(array('result' => 'success', 'error' => NULL, 'records' => $outputData, 'dryrun' => $dryRun));
		exit();
	}

	/**
	 * Parse loaded data for further processing
	 *
	 * @access public
	 * @return string
	 */
	public function parse( $dryRun = 1, $output = NULL )
	{
		// Set common props
		$this->_uid = $this->juser->get('id');

		// No errors. Let's rewind and parse
		$this->reader = new \XMLReader();
		$this->reader->XML($this->data);

		// Load classes
		$objCat = new PublicationCategory( $this->database );
		$objL = new PublicationLicense( $this->database );

		// Get base type
		$base = JRequest::getVar( 'base', 'files' );

		// Determine publication master type
		$mt 		= new PublicationMasterType( $this->database );
		$choices  	= $mt->getTypes('alias', 1);
		$mastertype = in_array($base, $choices) ? $base : 'files';

		// Get type params
		$mType = $mt->getType($mastertype);

		// Use new curation flow?
		$this->useBlocks  = $this->config->get('curation', 0);
		$curationModel 	  = NULL;

		// Get curation model for the type
		if ($this->useBlocks)
		{
			$curationModel = new PublicationsCuration($this->database, $mType->curation);
		}

		// Get defaults from manifest
		$title = $curationModel && isset($curationModel->_manifest->params->default_title)
			? $curationModel->_manifest->params->default_title : 'Untitled Draft';
		$title = $title ? $title : 'Untitled Draft';
		$cat = isset($curationModel->_manifest->params->default_category)
				? $curationModel->_manifest->params->default_category : 1;
		$this->curationModel = $curationModel;

		// Get element IDs
		$elementPrimeId 	= 1;
		$elementGalleryId 	= 2;
		$elementSupportId 	= 3;

		if ($this->curationModel)
		{
			$elements1 = $this->curationModel->getElements(1);
			$elements2 = $this->curationModel->getElements(2);
			$elements3 = $this->curationModel->getElements(3);

			$elementPrimeId 	= !empty($elements1) ? $elements1[0]->id : $elementPrimeId;
			$elementGalleryId 	= !empty($elements3) ? $elements3[0]->id : $elementGalleryId;
			$elementSupportId 	= !empty($elements2) ? $elements2[0]->id : $elementSupportId;
		}

		// Project helper
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_projects'
			. DS . 'helpers' . DS . 'helper.php');
		require_once( JPATH_ROOT . DS .'components' . DS . 'com_publications'
			. DS . 'models' . DS . 'types.php' );

		// Get project repo path
		$projectConfig 		= JComponentHelper::getParams( 'com_projects' );
		$this->projectPath 	= ProjectsHelper::getProjectPath($this->project->alias,
							$projectConfig->get('webpath'), $projectConfig->get('offroot'));

		// Git helper
		include_once( JPATH_ROOT . DS . 'components' . DS .'com_projects'
			. DS . 'helpers' . DS . 'githelper.php' );
		$this->_git = new ProjectsGitHelper(
			$projectConfig->get('gitpath', '/opt/local/bin/git'),
			$this->_uid
		);

		// Parse data
		$items = array();
		while ($this->reader->read())
		{
			if ($this->reader->name === 'publication')
			{
				$node = new SimpleXMLElement($this->reader->readOuterXML());

				// Check that category exists
				$category = isset($node->cat) ? $node->cat : 'dataset';
				$catId = $objCat->getCatId($category);

				$item['category'] 	= $category;
				$item['type']	  	= $mastertype;
				$item['errors']   	= array();
				$item['tags']   	= array();
				$item['authors']   	= array();

				// Publication properties
				$item['publication'] = new Publication( $this->database );
				$item['publication']->master_type 		= $mType->id;
				$item['publication']->category 			= $catId ? $catId : $cat;
				$item['publication']->project_id 		= $this->project->id;
				$item['publication']->created_by 		= $this->_uid;
				$item['publication']->created 			= JFactory::getDate()->toSql();
				$item['publication']->access 			= 0;

				// Version properties
				$item['version'] 		= new PublicationVersion( $this->database );
				$item['version']->title = isset($node->title) && trim($node->title)
										? trim($node->title) : $title;
				$item['version']->abstract 	= isset($node->synopsis) ? trim($node->synopsis) : '';
				$item['version']->description = isset($node->abstract) ? trim($node->abstract) : '';
				$item['version']->version_label = isset($node->version) ? trim($node->version) : '1.0';
				$item['version']->release_notes = isset($node->notes) ? trim($node->notes) : '';

				// Check license
				$license 			= isset($node->license) ? $node->license : '';
				$item['license'] 	= $objL->getLicenseByTitle($license);
				if (!$item['license'])
				{
					$item['errors'][] = JText::_('COM_PUBLICATIONS_BATCH_ITEM_ERROR_LICENSE');
				}
				else
				{
					$item['version']->license_type = $item['license']->id;
				}

				// Pick up files
				$item['files'] = array();
				if ($node->content)
				{
					$i = 1;
					foreach ($node->content->file as $file)
					{
						$this->collectFileData($file, 1, $i, $item, $elementPrimeId);
						$i++;
					}
				}

				// Supporting docs
				if ($node->supportingmaterials)
				{
					$i = 1;
					foreach ($node->supportingmaterials->file as $file)
					{
						$this->collectFileData($file, 2, $i, $item, $elementSupportId);
						$i++;
					}
				}

				// Gallery
				if ($node->gallery)
				{
					$i = 1;
					foreach ($node->gallery->file as $file)
					{
						$this->collectFileData($file, 3, $i, $item, $elementGalleryId);
						$i++;
					}
				}

				// Tags
				if ($node->tags)
				{
					foreach ($node->tags->tag as $tag)
					{
						if (trim($tag))
						{
							$item['tags'][] = $tag;
						}
					}
				}

				// Authors
				if ($node->authors)
				{
					$i = 1;
					foreach ($node->authors->author as $author)
					{
						$attributes = $author->attributes();
						$uid = $attributes['uid'];

						$this->collectAuthorData($author, $i, $uid, $item);
						$i++;
					}
				}

				// Set general process error
				if (count($item['errors']) > 0)
				{
					$this->setError(JText::_('COM_PUBLICATIONS_BATCH_ERROR_MISSING_OR_INVALID'));
				}

				$items[] = $item;
				$this->reader->next();
			}
		}

		// Show what you'll get
		if ($dryRun == 1)
		{
			$eview 	= new \Hubzero\Component\View( array(
				'name'=>'batchcreate',
				'layout' => 'dryrun'
				)
			);
			$eview->option 	= $this->_option;
			$eview->items = $items;
			$output .= $eview->loadTemplate();
		}
		elseif ($dryRun == 2)
		{
			// Get hub config
			$juri 	 = JURI::getInstance();
			$jconfig = JFactory::getConfig();
			$this->site = $jconfig->getValue('config.live_site')
				? $jconfig->getValue('config.live_site')
				: trim(preg_replace('/\/administrator/', '', $juri->base()), DS);

			// Process batch
			$out = NULL;
			$i = 0;
			foreach ($items as $item)
			{
				if ($this->processRecord($item, $out))
				{
					$i++;
				}
			}
			if ($i > 0)
			{
				$output = '<p class="success">' . JText::_('COM_PUBLICATIONS_BATCH_SUCCESS_CREATED') . ' ' .  $i . ' ' . JText::_('COM_PUBLICATIONS_BATCH_RECORDS_S') . '</p>';
			}
			if ($i != count($items))
			{
				$output = '<p class="error">' . JText::_('COM_PUBLICATIONS_BATCH_FAILED_CREATED') . ' ' .  (count($items) - $i) . ' ' . JText::_('COM_PUBLICATIONS_BATCH_RECORDS_S') . '</p>';
			}

			$output.= $out;
		}

		$this->reader->close();
		return $output;
	}

	/**
	 * Process parsed data
	 *
	 * @access public
	 * @return string
	 */
	public function processRecord($item, &$out)
	{
		// Create publication record
		if (!$item['publication']->store())
		{
			return false;
		}

		$pid = $item['publication']->id;

		// Create version record
		$item['version']->publication_id 	= $pid;
		$item['version']->version_number 	= 1;
		$item['version']->created_by 		= $this->_uid;
		$item['version']->created 			= JFactory::getDate()->toSql();
		$item['version']->secret			= strtolower(ProjectsHtml::generateCode(10, 10, 0, 1, 1));
		$item['version']->access			= 0;
		$item['version']->main				= 1;
		$item['version']->state				= 3;

		if (!$item['version']->store())
		{
			// Roll back
			$item['publication']->delete();
			return false;
		}

		$vid = $item['version']->id;

		// Build pub object
		$pub = new stdClass;
		$pub = $item['version'];
		$pub->id = $pid;
		$pub->version_id = $vid;
		$pub->_project = $this->project;

		// Build version object
		$version = new stdClass;
		$version->secret = $item['version']->secret;
		$version->id = $vid;
		$version->publication_id = $pid;

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		// Create attachments records and attach files
		foreach ($item['files'] as $fileRecord)
		{
			$this->processFileData($fileRecord, $pub, $version);
		}

		// Create author records
		foreach ($item['authors'] as $authorRecord)
		{
			$this->processAuthorData($authorRecord['author'], $pid, $vid);
		}

		// Build tags string
		if ($item['tags'])
		{
			$tags = '';
			$i = 0;
			foreach ($item['tags'] as $tag )
			{
				$i++;
				$tags .= trim($tag);
				$tags .= $i == count($item['tags']) ? '' : ',';
			}

			// Add tags
			$tagsHelper = new PublicationTags( $this->database );
			$tagsHelper->tag_object($this->_uid, $pid, $tags, 1);
		}

		// Display results
		$out .= '<p class="publication">#' . $pid . ': <a href="'
			. trim($this->site, DS) . '/publications/' . $pid
			. DS . '1" rel="external">' . $item['version']->title
			. ' v.' . $item['version']->version_label . '</a></p>';
		return true;
	}

	/**
	 * Collect file data
	 *
	 * @access public
	 * @return string
	 */
	public function collectFileData($file, $role, $ordering, &$item, $element_id = 0)
	{
		// Skip empty
		if (!trim($file->path, DS))
		{
			return;
		}

		$error = NULL;

		// Start new attachment record
		$attach = new PublicationAttachment( $this->database );
		$attach->path   	= trim($file->path, DS);
		$attach->title  	= isset($file->title) ? trim($file->title) : '';
		$attach->role		= $role;
		$attach->ordering	= $ordering;
		$attach->element_id	= $element_id;
		$attach->type		= 'file';

		// Check if file exists
		$filePath = $this->projectPath . DS . trim($file->path, DS);
		$exists = $file->path && file_exists($filePath) ? true : false;

		// Hightlight a problem
		if ($exists == false)
		{
			$error = JText::_('COM_PUBLICATIONS_BATCH_ITEM_ERROR_FILE_NOT_FOUND');
			$item['errors'][] = $error;
		}
		elseif ($role == 3 && !getimagesize($filePath))
		{
			$error = JText::_('COM_PUBLICATIONS_BATCH_ITEM_ERROR_FILE_NOT_IMAGE');
			$item['errors'][] = $error;
		}

		$type = $role == 1 ? 'primary' : 'supporting';
		$type = $role == 3 ? 'gallery' : $type;

		// Add file record
		$fileRecord = array(
			'type' => $type,
			'attachment' => $attach,
			'projectPath' => $filePath,
			'error' => $error
		);

		$item['files'][] = $fileRecord;
	}

	/**
	 * Process file data
	 *
	 * @access public
	 * @return void
	 */
	public function processFileData($fileRecord, $pub, $version)
	{
		$attachment = $fileRecord['attachment'];
		$vid = $pub->version_id;
		$pid = $pub->id;

		// Get latest Git hash
		$vcs_hash = $this->_git->gitLog($this->projectPath, $attachment->path, '', 'hash');

		// Create attachment record
		if ($this->curationModel || $fileRecord['type'] != 'gallery')
		{
			$attachment->publication_id 			= $pid;
			$attachment->publication_version_id 	= $vid;
			$attachment->vcs_hash 					= $vcs_hash;
			$attachment->created_by 				= $this->_uid;
			$attachment->created 					= JFactory::getDate()->toSql();
			$attachment->store();
		}

		// Copy files to the right location
		if ($this->curationModel)
		{
			// Get attachment type model
			$attModel = new PublicationsModelAttachments($this->database);
			$fileAttach = $attModel->loadAttach('file');

			// Get element manifest
			$elements = $this->curationModel->getElements($attachment->role);
			if (!$elements)
			{
				return false;
			}
			$element = $elements[0];

			// Set configs
			$configs  = $fileAttach->getConfigs(
				$element->manifest->params,
				$element->id,
				$pub,
				$element->block
			);

			// Check if names is already used
			$suffix = $fileAttach->checkForDuplicate(
				$configs->path . DS . $attachment->path,
				$attachment,
				$configs
			);
			// Save params if applicable
			if ($suffix)
			{
				$pa = new PublicationAttachment( $this->database );
				$pa->saveParam($attachment, 'suffix', $suffix);
			}
			// Copy file into the right spot
			$fileAttach->publishAttachment($attachment, $pub, $configs);
		}
		else
		{
			// Old-style attachments
			// Handle screenshots
			if ($fileRecord['type'] == 'gallery')
			{
				$this->createScreenshot( $fileRecord, $pub, $version );
			}
			else
			{
				// Get types helper
				if (!isset($this->_pubTypeHelper))
				{
					$this->_pubTypeHelper = new PublicationTypesHelper(
						$this->database,
						$this->project
					);
				}

				// Publish attachment
				$published = $this->_pubTypeHelper->dispatchByType(
					'file',
					'publishAttachments',
					$data = array(
						'attachments'	=> array($attachment),
						'row' 			=> $version,
						'uid'			=> $this->_uid
					)
				);
			}
		}
	}

	/**
	 * Create screenshot (old way)
	 *
	 * @return  void
	 */
	public function createScreenshot( $fileRecord, $pub, $version )
	{
		$attachment = $fileRecord['attachment'];

		// Check if image
		if (!is_file($fileRecord['projectPath']) || !getimagesize($fileRecord['projectPath']))
		{
			return false;
		}

		// Get image helper
		if (!$this->_imgHelper)
		{
			include_once( JPATH_ROOT . DS . 'components' . DS . 'com_projects'
				. DS . 'helpers' . DS . 'imghandler.php' );
			$this->_imgHelper = new ProjectsImgHandler();
		}

		// Get publications helper
		if (!isset($this->_pubHelper))
		{
			$this->_pubHelper = new PublicationHelper( $this->database );
		}

		// Get standard gallery path
		$gallery_path = $this->_pubHelper->buildPath($pub->id, $pub->version_id, $this->config->get('webpath'), 'gallery');

		$filename = basename($attachment->path);
		$hash = $attachment->vcs_hash;
		$hashed = $this->_imgHelper->createThumbName($filename, '-' . substr($hash, 0, 6));
		$thumb = $this->_imgHelper->createThumbName($filename, '-' . substr($hash, 0, 6) . '_tn', $extension = 'png');

		if (!is_dir( JPATH_ROOT . $gallery_path ))
		{
			JFolder::create( JPATH_ROOT . $gallery_path );
		}
		if (!JFile::copy($fileRecord['projectPath'], JPATH_ROOT . $gallery_path. DS . $hashed))
		{
			return false;
		}
		else
		{
			JFile::copy($fileRecord['projectPath'], JPATH_ROOT . $gallery_path. DS . $thumb);
			$this->_imgHelper->set('image', basename($thumb));
			$this->_imgHelper->set('overwrite', true);
			$this->_imgHelper->set('path', JPATH_ROOT . $gallery_path. DS);
			$this->_imgHelper->set('maxWidth', 100);
			$this->_imgHelper->set('maxHeight', 60);
			if (!$this->_imgHelper->process())
			{
				return false;
			}
			else
			{
				// Create screenshot record
				$pScreenshot = new PublicationScreenshot( $this->database );
				$pScreenshot->filename 					= $attachment->path;
				$pScreenshot->srcfile 					= $hashed;
				$pScreenshot->publication_id 			= $pub->id;
				$pScreenshot->publication_version_id 	= $pub->version_id;
				$pScreenshot->title 					= $attachment->title;
				$pScreenshot->created 					= JFactory::getDate()->toSql();
				$pScreenshot->created_by 				= $this->_uid;
				$pScreenshot->store();
				return true;
			}
		}
	}

	/**
	 * Process author data
	 *
	 * @access public
	 * @return void
	 */
	public function processAuthorData($author, $pid, $vid)
	{
		// Need to create project owner
		if (!$author->project_owner_id)
		{
			$objO = new ProjectOwner( $this->database );

			$objO->projectid 	 = $this->project->id;
			$objO->userid 		 = $author->user_id;
			$objO->status 		 = $author->user_id ? 1 : 0;
			$objO->added 		 = JFactory::getDate()->toSql();
			$objO->role 		 = 2;
			$objO->invited_email = '';
			$objO->invited_name  = $author->name;
			$objO->store();
			$author->project_owner_id = $objO->id;
		}
		$author->publication_version_id = $vid;
		$author->created_by = $this->_uid;
		$author->created 	= JFactory::getDate()->toSql();

		$author->store();
	}

	/**
	 * Collect author data
	 *
	 * @access public
	 * @return string
	 */
	public function collectAuthorData($author, $ordering, $uid, &$item)
	{
		$firstName 	= NULL;
		$lastName 	= NULL;
		$org		= NULL;
		$error 		= NULL;

		// Check that user ID exists
		if (trim($uid))
		{
			if (!intval($uid))
			{
				$error = JText::_('COM_PUBLICATIONS_BATCH_ITEM_ERROR_INVALID_USER_ID')
					. ': ' . trim($uid);
				$item['errors'][] = $error;
			}
			else
			{
				$profile = \Hubzero\User\Profile::getInstance($uid);
				if (!$profile || !$profile->get('uidNumber'))
				{
					$error = JText::_('COM_PUBLICATIONS_BATCH_ITEM_ERROR_USER_NOT_FOUND')
						. ': ' . trim($uid);
					$item['errors'][] = $error;
				}
				else
				{
					$firstName 	= $profile->get('givenName');
					$lastName 	= $profile->get('lastName');
					$org		= $profile->get('organization');
				}
			}
		}

		$pAuthor 					= new PublicationAuthor( $this->database );
		$pAuthor->user_id 			= trim($uid);
		$pAuthor->ordering 			= $ordering;
		$pAuthor->credit 			= '';
		$pAuthor->role 				= '';
		$pAuthor->status 			= 1;
		$pAuthor->organization 		= isset($author->organization) && $author->organization
									? trim($author->organization) : $org;
		$pAuthor->firstName 		= isset($author->firstname) && $author->firstname
									? trim($author->firstname) : $firstName;
		$pAuthor->lastName 			= isset($author->lastname) && $author->lastname
									? trim($author->lastname) : $lastName;
		$pAuthor->name 				= trim($pAuthor->firstName . ' ' . $pAuthor->lastName);

		// Check if project member
		$objO   = new ProjectOwner( $this->database );
		$owner  = $objO->getOwnerId( $this->project->id, $uid, $pAuthor->name );
		$pAuthor->project_owner_id 	= $owner;

		if (!$pAuthor->name)
		{
			$item['errors'][] = JText::_('COM_PUBLICATIONS_BATCH_ITEM_ERROR_AUTHOR_NAME_REQUIRED');
		}

		// Add author record
		$authorRecord = array('author' => $pAuthor, 'owner' => $owner, 'error' => $error);

		$item['authors'][] = $authorRecord;
	}

	/**
	 * Show parsing errors
	 *
	 * @access public
	 * @return string
	 */
	public function displayXmlError($error, $xml)
	{
	    $return  = "\n";

	    switch ($error->level)
		{
			case LIBXML_ERR_WARNING:
	            $return .= "Warning $error->code: ";
	            break;
			case LIBXML_ERR_ERROR:
	            $return .= "Error $error->code: ";
	            break;
			case LIBXML_ERR_FATAL:
	            $return .= "Fatal Error $error->code: ";
	            break;
	    }

	    $return .= trim($error->message);
		if ($error->line || $error->column)
		{
			$return .= "\n  Line: $error->line" .
			"\n  Column: $error->column";
		}

	    return "$return\n\n--------------------------------------------\n\n";
	}

	/**
	 * Return schema file
	 *
	 * @access public
	 * @return string
	 */
	public function getSchema()
	{
		return JPATH_COMPONENT_ADMINISTRATOR . DS . 'import' . DS . 'publications.xsd';
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return	void
	 */
	public function cancelTask()
	{
		$this->setRedirect('index.php?option=' . $this->_option . '&controller=' . $this->_controller);
	}
}
