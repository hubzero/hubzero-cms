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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Site\Controllers;

use Components\Citations\Tables\Citation;
use Components\Citations\Tables\Type;
use Components\Citations\Tables\Tags;
use Components\Citations\Models\Importer;
use Hubzero\Component\SiteController;
use Exception;
use Filesystem;
use Pathway;
use Request;
use Notify;
use Config;
use Route;
use Event;
use User;
use Date;
use Lang;
use App;

include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'importer.php');

/**
 * Citations controller class for importing citation entries
 */
class Import extends SiteController
{
	/**
	 * Redirect to login form
	 *
	 * @return  void
	 */
	public function execute()
	{
		if (User::isGuest())
		{
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url('index.php?option=' . $this->_option . '&task=import', false, true))),
				Lang::txt('COM_CITATIONS_NOT_LOGGEDIN'),
				'warning'
			);
			return;
		}

		$this->importer = new Importer(
			App::get('db'),
			App::get('filesystem'),
			App::get('config')->get('tmp_path') . DS . 'citations',
			App::get('session')->getId()
		);

		$this->registerTask('import_upload', 'upload');
		$this->registerTask('import_review', 'review');
		$this->registerTask('import_save', 'save');
		$this->registerTask('import_saved', 'saved');

		parent::execute();
	}

	/**
	 * Display a form for importing citations
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$gid = Request::getVar('group');
		if (isset($gid) && $gid != '')
		{
			$this->view->gid = $gid;
		}

		//are we allowing importing
		$importParam = $this->config->get('citation_bulk_import', 1);

		//if importing is turned off go to intro page
		if (!$importParam)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option)
			);
			return;
		}

		//are we only allowing admins?
		$isAdmin = User::authorise('core.manage', $this->_option);
		if ($importParam == 2 && !$isAdmin)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option),
				Lang::txt('COM_CITATIONS_CITATION_NOT_AUTH'),
				'warning'
			);
			return;
		}

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		//citation temp file cleanup
		//$this->_citationCleanup();
		$this->importer->cleanup();

		// Instantiate a new view
		$this->view->title = Lang::txt(strtoupper($this->_option)) . ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_controller));

		//call the plugins
		$this->view->accepted_files = Event::trigger('citation.onImportAcceptedFiles' , array());

		//get any messages
		$this->view->messages = Notify::messages('citations');

		//display view
		$this->view->display();
	}

	/**
	 * Upload a file
	 *
	 * @return  void
	 */
	public function uploadTask()
	{
		Request::checkToken();

		// get file
		$file = Request::file('citations_file');

		// make sure we have a file
		$filename = $file->getClientOriginalName();
		if ($filename == '')
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=import'),
				Lang::txt('COM_CITATIONS_IMPORT_MISSING_FILE'),
				'error'
			);
			return;
		}

		// make sure file is under 4MB
		if ($file->getSize() > 4000000)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=import'),
				Lang::txt('COM_CITATIONS_IMPORT_FILE_TOO_BIG'),
				'error'
			);
			return;
		}

		// make sure we dont have any file errors
		if ($file->getError() > 0)
		{
			throw new Exception(Lang::txt('COM_CITATIONS_IMPORT_UPLOAD_FAILURE'), 500);
		}

		// call the plugins
		$citations = Event::trigger('citation.onImport' , array($file));
		$citations = array_values(array_filter($citations));

		// did we get citations from the citation plugins
		if (!$citations)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=import'),
				Lang::txt('COM_CITATIONS_IMPORT_PROCESS_FAILURE'),
				'error'
			);
			return;
		}

		if (!isset($citations[0]['attention']))
		{
			$citations[0]['attention'] = '';
		}

		if (!isset($citations[0]['no_attention']))
		{
			$citations[0]['no_attention'] = '';

		}
		if (!$this->importer->writeRequiresAttention($citations[0]['attention']))
		{
			Notify::error(Lang::txt('Unable to write temporary file.'));
		}

		if (!$this->importer->writeRequiresNoAttention($citations[0]['no_attention']))
		{
			Notify::error(Lang::txt('Unable to write temporary file.'));
		}

		//get group ID
		$group = Request::getVar('group');

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&task=import_review' . ($group ? '&group=' . $group : ''))
		);
	}

	/**
	 * Review an entry
	 *
	 * @return  void
	 */
	public function reviewTask()
	{
		$citations_require_attention    = $this->importer->readRequiresAttention();
		$citations_require_no_attention = $this->importer->readRequiresNoAttention();

		$group = Request::getVar('group');

		// make sure we have some citations
		if (!$citations_require_attention && !$citations_require_no_attention)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=import' . ($group ? '&group=' . $group : '')),
				Lang::txt('COM_CITATIONS_IMPORT_MISSING_FILE_CONTINUE'),
				'error'
			);
			return;
		}

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Instantiate a new view
		$this->view->title = Lang::txt(strtoupper($this->_option)) . ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		$this->view->citations_require_attention    = $citations_require_attention;
		$this->view->citations_require_no_attention = $citations_require_no_attention;

		// get any messages
		$this->view->messages = Notify::messages('citations');

		// display view
		$this->view->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		Request::checkToken();

		$cites_require_attention    = $this->importer->readRequiresAttention();
		$cites_require_no_attention = $this->importer->readRequiresNoAttention();

		// action for citations needing attention
		$citations_action_attention = Request::getVar('citation_action_attention', array());

		// action for citations needing no attention
		$citations_action_no_attention = Request::getVar('citation_action_no_attention', array());

		// check to make sure we have citations
		if (!$cites_require_attention && !$cites_require_no_attention)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=import'),
				Lang::txt('COM_CITATIONS_IMPORT_MISSING_FILE_CONTINUE'),
				'error'
			);
			return;
		}

		// vars
		$allow_tags   = $this->config->get('citation_allow_tags', 'no');
		$allow_badges = $this->config->get('citation_allow_badges', 'no');

		$this->importer->set('user', User::get('id'));
		if ($group = Request::getVar('group'))
		{
			$this->importer->set('scope', 'group');
			$this->importer->set('scope_id', $group);
		}
		$this->importer->setTags($allow_tags == 'yes');
		$this->importer->setBadges($allow_badges == 'yes');

		// Process
		$results = $this->importer->process(
			$citations_action_attention,
			$citations_action_no_attention
		);

		if (isset($group) && $group != '')
		{
			require_once(PATH_CORE . DS . 'components' . DS . 'com_groups' . DS . 'tables' . DS . 'group.php');
			$gob = new \Components\Groups\Tables\Group($this->database);
			$cn = $gob->getName($group);

			App::redirect(
				Route::url('index.php?option=com_groups&cn=' . $cn . '&active=citations&action=dashboard')
			);
		}
		else
		{
			// success message a redirect
			Notify::success(
				Lang::txt('COM_CITATIONS_IMPORT_RESULTS_SAVED', count($results['saved'])),
				'citations'
			);

			// if we have citations not getting saved
			if (count($results['not_saved']) > 0)
			{
				Notify::warning(
					Lang::txt('COM_CITATIONS_IMPORT_RESULTS_NOT_SAVED', count($results['not_saved'])),
					'citations'
				);
			}

			if (count($results['error']) > 0)
			{
				Notify::error(
					Lang::txt('COM_CITATIONS_IMPORT_RESULTS_SAVE_ERROR', count($results['error'])),
					'citations'
				);
			}

			//get the session object
			$session = App::get('session');

			//ids of sessions saved and not saved
			$session->set('citations_saved', $results['saved']);
			$session->set('citations_not_saved', $results['not_saved']);
			$session->set('citations_error', $results['error']);

			//delete the temp files that hold citation data
			$this->importer->cleanup(true);

			//redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=import_saved')
			);
		}

		return;
	}

	/**
	 * Show the results of the import
	 *
	 * @return  void
	 */
	public function savedTask()
	{
		// Get the session object
		$session = App::get('session');

		// Get the citations
		$citations_saved     = $session->get('citations_saved');
		$citations_not_saved = $session->get('citations_not_saved');
		$citations_error     = $session->get('citations_error');

		// Check to make sure we have citations
		if (!$citations_saved && !$citations_not_saved)
		{
			App::redirect(
				Route::url('index.php?option=com_citations&task=import'),
				Lang::txt('COM_CITATIONS_IMPORT_MISSING_FILE_CONTINUE'),
				'error'
			);
			return;
		}

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Filters for gettiung just previously uploaded
		$filters = array(
			'start'  => 0,
			'search' => ''
		);

		// Instantiate a new view
		$this->view->title     = Lang::txt(strtoupper($this->_option)) . ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		$this->view->config    = $this->config;
		$this->view->database  = $this->database;
		$this->view->filters   = $filters;
		$this->view->citations = array();

		foreach ($citations_saved as $cs)
		{
			$cc = new Citation($this->database);
			$cc->load($cs);
			$this->view->citations[] = $cc;
		}

		$this->view->openurl['link'] = '';
		$this->view->openurl['text'] = '';
		$this->view->openurl['icon'] = '';

		//take care fo type
		$ct = new Type($this->database);
		$this->view->types = $ct->getType();

		//get any messages
		$this->view->messages = Notify::messages('citations');

		//display view
		$this->view->display();
	}

	/**
	 * Return the citation format
	 *
	 * @return  void
	 */
	public function getformatTask()
	{
		echo 'format' . Request::getVar('format', 'apa');
	}

	/**
	 * Method to set the document path
	 *
	 * @return  void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return  void
	 */
	protected function _buildTitle()
	{
		$this->_title = Lang::txt(strtoupper($this->_option));
		if ($this->_task)
		{
			$this->_title .= ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}

		App::get('document')->setTitle($this->_title);
	}
}
