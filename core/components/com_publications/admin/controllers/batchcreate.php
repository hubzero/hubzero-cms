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

namespace Components\Publications\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Publications\Tables;
use stdClass;
use Exception;
use Request;
use Route;
use Date;
use Lang;
use User;
use App;

/**
 * Manage publication batch
 */
class Batchcreate extends AdminController
{
	/**
	 * Start page
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$project = new \Components\Projects\Tables\Project($this->database);

		// Get filters
		$filters = array(
			'sortby'     => 'title',
			'sortdir'    => 'DESC',
			'authorized' => true
		);
		$this->view->projects  = $project->getRecords($filters, true, 0, 1);

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view
			->setLayout('display')
			->display();
	}

	/**
	 * Download XSD
	 *
	 * @return  void
	 */
	public function xsdTask()
	{
		$path = $this->getSchema();

		if (file_exists($path))
		{
			$server = new \Hubzero\Content\Server();
			$server->filename($path);
			$server->disposition('attachment');
			$server->acceptranges(true);
			$server->saveas(basename($path));

			if (!$server->serve())
			{
				// Should only get here on error
				throw new Exception(Lang::txt('COM_PUBLICATIONS_SERVER_ERROR'), 404);
			}
			else
			{
				exit;
			}
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_PUBLICATIONS_BATCH_ERROR_NO_XSD'),
			'error'
		);
	}

	/**
	 * Validate XML
	 *
	 * @return  void
	 */
	public function validateTask()
	{
		if (!isset($this->reader) || !$this->reader->isValid())
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_BATCH_ERROR'));
			$this->reader->close();
			return;
		}

		// Collect all errors
		while (@$this->reader->read()) { }; // empty loop
		$errors = libxml_get_errors();

		// Errors found - stop
		if (count($errors) > 0)
		{
			$this->setError(Lang::txt('COM_PUBLICATIONS_BATCH_ERROR_XML_VALIDATION_FAILED'));
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
	 * @param   integer  $dryRun
	 * @return  void
	 */
	public function processTask($dryRun = 0)
	{
		// check token
		Request::checkToken();

		// Incoming
		$id     = Request::getInt('projectid', 0);
		$file   = Request::getVar('file', array(), 'FILES');
		$dryRun = Request::getInt('dryrun', 1);

		$this->data = NULL;

		// Project ID must be supplied
		$this->project = new \Components\Projects\Models\Project($id);
		if (!$this->project->exists())
		{
			echo json_encode(array(
				'result'  => 'error',
				'error'   => Lang::txt('COM_PUBLICATIONS_BATCH_ERROR_NO_PROJECT_ID'),
				'records' => NULL
			));
			exit();
		}

		// Check for file
		if (!is_array($file) || $file['size'] == 0 || $file['error'] != 0)
		{
			echo json_encode(array(
				'result'  => 'error',
				'error'   => Lang::txt('COM_PUBLICATIONS_BATCH_ERROR_NO_FILE'),
				'records' => NULL
			));
			exit();
		}

		// Check for correct type
		if (!in_array($file['type'], array('application/xml', 'text/xml')))
		{
			echo json_encode(array(
				'result'  => 'error',
				'error'   => Lang::txt('COM_PUBLICATIONS_BATCH_ERROR_WRONG_FORMAT'),
				'records' => NULL
			));
			exit();
		}

		// Get data from XML file
		if (is_uploaded_file($file['tmp_name']))
		{
			$this->data = file_get_contents($file['tmp_name']);
		}
		if (!$this->data)
		{
			echo json_encode(array(
				'result'  => 'error',
				'error'   => Lang::txt('COM_PUBLICATIONS_BATCH_ERROR_NO_DATA'),
				'records' => NULL
			));
			exit();
		}

		// Load reader
		libxml_use_internal_errors(true);
		$this->reader = new \XMLReader();

		// Open and validate XML against schema
		if (!$this->reader->XML($this->data, 'UTF-8', \XMLReader::VALIDATE | \XMLReader::SUBST_ENTITIES))
		{
			echo json_encode(array(
				'result'  => 'error',
				'error'   => Lang::txt('COM_PUBLICATIONS_BATCH_ERROR_XML_VALIDATION_FAILED'),
				'records' => NULL
			));
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
				'result'  => 'error',
				'error'   => $this->getError(),
				'records' => $outputData,
				'dryrun'  => $dryRun
			));
			exit();
		}

		// return results to user
		echo json_encode(array(
			'result'  => 'success',
			'error'   => NULL,
			'records' => $outputData,
			'dryrun'  => $dryRun
		));
		exit();
	}

	/**
	 * Parse loaded data for further processing
	 *
	 * @param   integer  $dryRun
	 * @param   string   $output
	 * @return  string
	 */
	public function parse($dryRun = 1, $output = NULL)
	{
		// Set common props
		$this->_uid = User::get('id');

		// No errors. Let's rewind and parse
		$this->reader = new \XMLReader();
		$this->reader->XML($this->data);

		// Load classes
		$objCat = new Tables\Category($this->database);
		$objL   = new Tables\License($this->database);

		// Get base type
		$base = Request::getVar('base', 'files');

		// Determine publication master type
		$mt = new Tables\MasterType($this->database);
		$choices    = $mt->getTypes('alias', 1);
		$mastertype = in_array($base, $choices) ? $base : 'files';

		// Get type params
		$mType = $mt->getType($mastertype);

		// Get curation model for the type
		$curationModel = new \Components\Publications\Models\Curation($mType->curation);

		// Get defaults from manifest
		$title = $curationModel && isset($curationModel->_manifest->params->default_title)
			? $curationModel->_manifest->params->default_title : 'Untitled Draft';
		$title = $title ? $title : 'Untitled Draft';
		$cat = isset($curationModel->_manifest->params->default_category)
				? $curationModel->_manifest->params->default_category : 1;
		$this->curationModel = $curationModel;

		// Get element IDs
		$elementPrimeId   = 1;
		$elementGalleryId = 2;
		$elementSupportId = 3;

		if ($this->curationModel)
		{
			$elements1 = $this->curationModel->getElements(1);
			$elements2 = $this->curationModel->getElements(2);
			$elements3 = $this->curationModel->getElements(3);

			$elementPrimeId   = !empty($elements1) ? $elements1[0]->id : $elementPrimeId;
			$elementGalleryId = !empty($elements3) ? $elements3[0]->id : $elementGalleryId;
			$elementSupportId = !empty($elements2) ? $elements2[0]->id : $elementSupportId;
		}

		// Get project repo path
		$this->projectPath = $this->project->repo()->get('path');

		// Parse data
		$items = array();
		while ($this->reader->read())
		{
			if ($this->reader->name === 'publication')
			{
				$node = new \SimpleXMLElement($this->reader->readOuterXML());

				// Check that category exists
				$category = isset($node->cat) ? $node->cat : 'dataset';
				$catId = $objCat->getCatId($category);

				$item['category'] = $category;
				$item['type']     = $mastertype;
				$item['errors']   = array();
				$item['tags']     = array();
				$item['authors']  = array();

				// Publication properties
				$item['publication'] = new Tables\Publication($this->database);
				$item['publication']->master_type = $mType->id;
				$item['publication']->category    = $catId ? $catId : $cat;
				$item['publication']->project_id  = $this->project->get('id');
				$item['publication']->created_by  = $this->_uid;
				$item['publication']->created     = Date::toSql();
				$item['publication']->access      = 0;

				// Version properties
				$item['version'] = new Tables\Version($this->database);
				$item['version']->title         = isset($node->title) && trim($node->title) ? trim($node->title) : $title;
				$item['version']->abstract      = isset($node->synopsis) ? trim($node->synopsis) : '';
				$item['version']->description   = isset($node->abstract) ? trim($node->abstract) : '';
				$item['version']->version_label = isset($node->version) ? trim($node->version) : '1.0';
				$item['version']->release_notes = isset($node->notes) ? trim($node->notes) : '';

				// Check license
				$license = isset($node->license) ? $node->license : '';
				$item['license'] = $objL->getLicenseByTitle($license);
				if (!$item['license'])
				{
					$item['errors'][] = Lang::txt('COM_PUBLICATIONS_BATCH_ITEM_ERROR_LICENSE');
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
					$this->setError(Lang::txt('COM_PUBLICATIONS_BATCH_ERROR_MISSING_OR_INVALID'));
				}

				$items[] = $item;
				$this->reader->next();
			}
		}

		// Show what you'll get
		if ($dryRun == 1)
		{
			$eview = new \Hubzero\Component\View(array(
				'name'   =>'batchcreate',
				'layout' => 'dryrun'
			));
			$eview->option = $this->_option;
			$eview->items  = $items;
			$output       .= $eview->loadTemplate();
		}
		elseif ($dryRun == 2)
		{
			// Get hub config
			$this->site = trim(Request::base(), DS);

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
				$output = '<p class="success">' . Lang::txt('COM_PUBLICATIONS_BATCH_SUCCESS_CREATED') . ' ' .  $i . ' ' . Lang::txt('COM_PUBLICATIONS_BATCH_RECORDS_S') . '</p>';
			}
			if ($i != count($items))
			{
				$output = '<p class="error">' . Lang::txt('COM_PUBLICATIONS_BATCH_FAILED_CREATED') . ' ' .  (count($items) - $i) . ' ' . Lang::txt('COM_PUBLICATIONS_BATCH_RECORDS_S') . '</p>';
			}

			$output .= $out;
		}

		$this->reader->close();

		return $output;
	}

	/**
	 * Process parsed data
	 *
	 * @param   object  $item
	 * @param   object  $out
	 * @return  boolean
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
		$item['version']->publication_id = $pid;
		$item['version']->version_number = 1;
		$item['version']->created_by     = $this->_uid;
		$item['version']->created        = Date::toSql();
		$item['version']->secret         = strtolower(\Components\Projects\Helpers\Html::generateCode(10, 10, 0, 1, 1));
		$item['version']->access         = 0;
		$item['version']->main           = 1;
		$item['version']->state          = 3;

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

		// Build version object
		$version = new stdClass;
		$version->secret = $item['version']->secret;
		$version->id = $vid;
		$version->publication_id = $pid;

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
			foreach ($item['tags'] as $tag)
			{
				$i++;
				$tags .= trim($tag);
				$tags .= $i == count($item['tags']) ? '' : ',';
			}

			// Add tags
			$tagsHelper = new \Components\Publications\Helpers\Tags($this->database);
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
	 * @param   object   $file
	 * @param   string   $role
	 * @param   integer  $ordering
	 * @param   object   $item
	 * @param   integer  $element_id
	 * @return  void
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
		$attach = new Tables\Attachment($this->database);
		$attach->path       = trim($file->path, DS);
		$attach->title      = isset($file->title) ? trim($file->title) : '';
		$attach->role       = $role;
		$attach->ordering   = $ordering;
		$attach->element_id = $element_id;
		$attach->type       = 'file';

		// Check if file exists
		$filePath = $this->projectPath . DS . trim($file->path, DS);
		$exists = $file->path && file_exists($filePath) ? true : false;

		// Hightlight a problem
		if ($exists == false)
		{
			$error = Lang::txt('COM_PUBLICATIONS_BATCH_ITEM_ERROR_FILE_NOT_FOUND');
			$item['errors'][] = $error;
		}
		elseif ($role == 3 && !getimagesize($filePath))
		{
			$error = Lang::txt('COM_PUBLICATIONS_BATCH_ITEM_ERROR_FILE_NOT_IMAGE');
			$item['errors'][] = $error;
		}

		$type = $role == 1 ? 'primary' : 'supporting';
		$type = $role == 3 ? 'gallery' : $type;

		// Add file record
		$fileRecord = array(
			'type'        => $type,
			'attachment'  => $attach,
			'projectPath' => $filePath,
			'error'       => $error
		);

		$item['files'][] = $fileRecord;
	}

	/**
	 * Process file data
	 *
	 * @param   array   $fileRecord
	 * @param   object  $pub
	 * @param   object  $version
	 * @return  void
	 */
	public function processFileData($fileRecord, $pub, $version)
	{
		$attachment = $fileRecord['attachment'];
		$vid = $pub->version_id;
		$pid = $pub->id;

		// Get latest Git hash
		$vcs_hash = $this->_git->gitLog($attachment->path, '', 'hash');

		// Create attachment record
		if ($this->curationModel || $fileRecord['type'] != 'gallery')
		{
			$attachment->publication_id         = $pid;
			$attachment->publication_version_id = $vid;
			$attachment->vcs_hash               = $vcs_hash;
			$attachment->created_by             = $this->_uid;
			$attachment->created                = Date::toSql();
			$attachment->store();
		}

		// Copy files to the right location
		if ($this->curationModel)
		{
			// Get attachment type model
			$attModel = new Models\Attachments($this->database);
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
				$pa = new \Components\Publications\Tables\Attachment($this->database);
				$pa->saveParam($attachment, 'suffix', $suffix);
			}

			// Copy file into the right spot
			$fileAttach->publishAttachment($attachment, $pub, $configs);
		}
	}

	/**
	 * Process author data
	 *
	 * @param   object   $author
	 * @param   integer  $pid
	 * @param   integer  $vid
	 * @return  void
	 */
	public function processAuthorData($author, $pid, $vid)
	{
		// Need to create project owner
		if (!$author->project_owner_id)
		{
			$objO = new Tables\Owner($this->database);

			$objO->projectid     = $this->project->get('id');
			$objO->userid        = $author->user_id;
			$objO->status        = $author->user_id ? 1 : 0;
			$objO->added         = Date::toSql();
			$objO->role          = 2;
			$objO->invited_email = '';
			$objO->invited_name  = $author->name;
			$objO->store();
			$author->project_owner_id = $objO->id;
		}

		$author->publication_version_id = $vid;
		$author->created_by = $this->_uid;
		$author->created    = Date::toSql();

		$author->store();
	}

	/**
	 * Collect author data
	 *
	 * @param   object   $author
	 * @param   integer  $ordering
	 * @param   integer  $uid
	 * @param   object   $item
	 * @return  string
	 */
	public function collectAuthorData($author, $ordering, $uid, &$item)
	{
		$firstName = NULL;
		$lastName  = NULL;
		$org       = NULL;
		$error     = NULL;

		// Check that user ID exists
		if (trim($uid))
		{
			if (!intval($uid))
			{
				$error = Lang::txt('COM_PUBLICATIONS_BATCH_ITEM_ERROR_INVALID_USER_ID') . ': ' . trim($uid);
				$item['errors'][] = $error;
			}
			else
			{
				$profile = \Hubzero\User\Profile::getInstance($uid);
				if (!$profile || !$profile->get('uidNumber'))
				{
					$error = Lang::txt('COM_PUBLICATIONS_BATCH_ITEM_ERROR_USER_NOT_FOUND') . ': ' . trim($uid);
					$item['errors'][] = $error;
				}
				else
				{
					$firstName = $profile->get('givenName');
					$lastName  = $profile->get('lastName');
					$org       = $profile->get('organization');
				}
			}
		}

		$pAuthor = new Tables\Author($this->database);
		$pAuthor->user_id      = trim($uid);
		$pAuthor->ordering     = $ordering;
		$pAuthor->credit       = '';
		$pAuthor->role         = '';
		$pAuthor->status       = 1;
		$pAuthor->organization = isset($author->organization) && $author->organization
								? trim($author->organization) : $org;
		$pAuthor->firstName    = isset($author->firstname) && $author->firstname
								? trim($author->firstname) : $firstName;
		$pAuthor->lastName     = isset($author->lastname) && $author->lastname
								? trim($author->lastname) : $lastName;
		$pAuthor->name         = trim($pAuthor->firstName . ' ' . $pAuthor->lastName);

		// Check if project member
		$objO   = $this->project->table('Owner');
		$owner  = $objO->getOwnerId($this->project->get('id'), $uid, $pAuthor->name);
		$pAuthor->project_owner_id = $owner;

		if (!$pAuthor->name)
		{
			$item['errors'][] = Lang::txt('COM_PUBLICATIONS_BATCH_ITEM_ERROR_AUTHOR_NAME_REQUIRED');
		}

		// Add author record
		$authorRecord = array('author' => $pAuthor, 'owner' => $owner, 'error' => $error);

		$item['authors'][] = $authorRecord;
	}

	/**
	 * Show parsing errors
	 *
	 * @param   object  $error
	 * @param   string  $xml
	 * @return  string
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
	 * @return  string
	 */
	public function getSchema()
	{
		return dirname(__DIR__) . DS . 'import' . DS . 'publications.xsd';
	}
}
