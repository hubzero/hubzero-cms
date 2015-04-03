<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Citations\Site\Controllers;

use Components\Citations\Tables\Citation;
use Components\Citations\Tables\Type;
use Components\Citations\Tables\Tags;
use Hubzero\Component\SiteController;
use Exception;

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

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
		if ($this->juser->get('guest'))
		{
			$this->setRedirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url('index.php?option=' . $this->_option . '&task=import', false, true))),
				Lang::txt('COM_CITATIONS_NOT_LOGGEDIN'),
				'warning'
			);
			return;
		}

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
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option)
			);
			return;
		}

		//are we only allowing admins?
		$isAdmin = $this->juser->authorize($this->_option, 'import');
		if ($importParam == 2 && !$isAdmin)
		{
			$this->setRedirect(
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
		$this->_citationCleanup();

		// Instantiate a new view
		$this->view->title = Lang::txt(strtoupper($this->_option)) . ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_controller));

		//import the plugins
		\JPluginHelper::importPlugin('citation');
		$dispatcher = \JDispatcher::getInstance();

		//call the plugins
		$this->view->accepted_files = $dispatcher->trigger('onImportAcceptedFiles' , array());

		//get any messages
		$this->view->messages = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();

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
		// get file
		$file = Request::getVar('citations_file', null, 'files', 'array');

		// make sure we have a file
		if (!$file['name'])
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&task=import'),
				Lang::txt('COM_CITATIONS_IMPORT_MISSING_FILE'),
				'error'
			);
			return;
		}

		// make sure file is under 4MB
		if ($file['size'] > 4000000)
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&task=import'),
				Lang::txt('COM_CITATIONS_IMPORT_FILE_TOO_BIG'),
				'error'
			);
			return;
		}

		// make sure we dont have any file errors
		if ($file['error'] > 0)
		{
			throw new Exception(Lang::txt('COM_CITATIONS_IMPORT_UPLOAD_FAILURE'), 500);
		}

		// load citation import plugins
		\JPluginHelper::importPlugin('citation');
		$dispatcher = \JDispatcher::getInstance();

		// call the plugins
		$citations = $dispatcher->trigger('onImport' , array($file));
		$citations = array_values(array_filter($citations));

		// did we get citations from the citation plugins
		if (!$citations)
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&task=import'),
				Lang::txt('COM_CITATIONS_IMPORT_PROCESS_FAILURE'),
				'error'
			);
			return;
		}

		// get the session object
		$session = \JFactory::getSession();
		$sessionid = $session->getId();

		// write the citation data to files
		$p1 = $this->getTmpPath() . DS . 'citations_require_attention_' . $sessionid . '.txt';
		$p2 = $this->getTmpPath() . DS . 'citations_require_no_attention_' . $sessionid . '.txt';
		$file1 = \JFile::write($p1, serialize($citations[0]['attention']));
		$file2 = \JFile::write($p2, serialize($citations[0]['no_attention']));

		//get group ID
		$group = Request::getVar('group');

		if (isset($group) && $group != '')
		{
			// review imported citations
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&task=import_review&group=' . $group)
			);
		}
		else
		{
			// review imported citations
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&task=import_review')
			);
		}

		return;
	}

	/**
	 * Review an entry
	 *
	 * @return  void
	 */
	public function reviewTask()
	{
		// get the session object
		$session = \JFactory::getSession();
		$sessionid = $session->getId();

		// get the citations
		$p1 = $this->getTmpPath() . DS . 'citations_require_attention_' . $sessionid . '.txt';
		$p2 = $this->getTmpPath() . DS . 'citations_require_no_attention_' . $sessionid . '.txt';
		$citations_require_attention    = null;
		$citations_require_no_attention = null;
		if (file_exists($p1))
		{
			$citations_require_attention    = unserialize(\JFile::read($p1));
		}
		if (file_exists($p2))
		{
			$citations_require_no_attention = unserialize(\JFile::read($p2));
		}

		$group = Request::getVar('group');

		if (isset($group) && $group != '')
		{
			$this->view->group = $group;

			// make sure we have some citations
			if (!$citations_require_attention && !$citations_require_no_attention)
			{
				$this->setRedirect(
					Route::url('index.php?option=' . $this->_option . '&task=import&group=' . $group),
					Lang::txt('COM_CITATIONS_IMPORT_MISSING_FILE_CONTINUE'),
					'error'
				);
				return;
			}
		}
		else
		{
			// make sure we have some citations
			if (!$citations_require_attention && !$citations_require_no_attention)
			{
				$this->setRedirect(
					Route::url('index.php?option=' . $this->_option . '&task=import'),
					Lang::txt('COM_CITATIONS_IMPORT_MISSING_FILE_CONTINUE'),
					'error'
				);
				return;
			}
		}

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// include tag handler
		//require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'helpers' . DS . 'handler.php');

		// Instantiate a new view
		$this->view->title = Lang::txt(strtoupper($this->_option)) . ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		$this->view->citations_require_attention    = $citations_require_attention;
		$this->view->citations_require_no_attention = $citations_require_no_attention;

		// get any messages
		$this->view->messages = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();

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
		// get the session object
		$session = \JFactory::getSession();
		$sessionid = $session->getId();

		// read in contents of citations file
		$p1 = $this->getTmpPath() . DS . 'citations_require_attention_' . $sessionid . '.txt';
		$p2 = $this->getTmpPath() . DS . 'citations_require_no_attention_' . $sessionid . '.txt';
		$cites_require_attention    = unserialize(\JFile::read($p1));
		$cites_require_no_attention = unserialize(\JFile::read($p2));

		// action for citations needing attention
		$citations_action_attention = Request::getVar('citation_action_attention', array());

		// action for citations needing no attention
		$citations_action_no_attention = Request::getVar('citation_action_no_attention', array());

		// check to make sure we have citations
		if (!$cites_require_attention && !$cites_require_no_attention)
		{
			$this->setRedirect(
				Route::url('index.php?option=' . $this->_option . '&task=import'),
				Lang::txt('COM_CITATIONS_IMPORT_MISSING_FILE_CONTINUE'),
				'error'
			);
			return;
		}

		// vars
		$citations_saved     = array();
		$citations_not_saved = array();
		$citations_error     = array();
		$now = \JFactory::getDate()->toSql();
		$user = $this->juser->get('id');
		$allow_tags   = $this->config->get('citation_allow_tags', 'no');
		$allow_badges = $this->config->get('citation_allow_badges', 'no');

		// loop through each citation that required attention from user
		if ($cites_require_attention)
		{
			foreach ($cites_require_attention as $k => $cra)
			{
				$cc = new Citation($this->database);

				// add a couple of needed keys
				$cra['uid'] = $user;
				$cra['created'] = $now;

				// reset tags and badges
				$tags = '';
				$badges = '';

				// remove errors
				unset($cra['errors']);

				// if tags were sent over
				if (array_key_exists('tags', $cra))
				{
					$tags = $cra['tags'];
					unset($cra['tags']);
				}

				// if badges were sent over
				if (array_key_exists('badges', $cra))
				{
					$badges = $cra['badges'];
					unset($cra['badges']);
				}

				//take care fo type
				$ct = new Type($this->database);
				$types = $ct->getType();

				$type = '';
				foreach ($types as $t)
				{
					if (strtolower($t['type_title']) == strtolower($cra['type']))
					{
						$type = $t['id'];
					}
				}
				$cra['type'] = ($type) ? $type : '1';

				switch ($citations_action_attention[$k])
				{
					case 'overwrite':
						$cra['id'] = $cra['duplicate'];
					break;

					case 'both':
					break;

					case 'discard':
						$citations_not_saved[] = $cra;
						continue 2;
					break;
				}

				// remove duplicate flag
				unset($cra['duplicate']);

				//sets group if set
				$group = Request::getVar('group');
				if (isset($group) && $group != '')
				{
					$cra['gid'] = $group;
					$cra['scope'] = 'group';
				}

				// save the citation
				if (!$cc->save($cra))
				{
					$citations_error[] = $cra;
				}
				else
				{
					// tags
					if ($allow_tags == 'yes' && isset($tags))
					{
						$this->_tagCitation($user, $cc->id, $tags, '');
					}

					// badges
					if ($allow_badges == 'yes' && isset($badges))
					{
						$this->_tagCitation($user, $cc->id, $badges, 'badge');
					}

					// add the citattion to the saved
					$citations_saved[] = $cc->id;
				}
			}
		}

		//
		if ($cites_require_no_attention)
		{
			foreach ($cites_require_no_attention as $k => $crna)
			{
				// new citation object
				$cc = new Citation($this->database);

				// add a couple of needed keys
				$crna['uid'] = $user;
				$crna['created'] = $now;

				// reset tags and badges
				$tags = '';
				$badges = '';

				// remove errors
				unset($crna['errors']);

				// if tags were sent over
				if (array_key_exists('tags', $crna))
				{
					$tags = $crna['tags'];
					unset($crna['tags']);
				}

				// if badges were sent over
				if (array_key_exists('badges', $crna))
				{
					$badges = $crna['badges'];
					unset($crna['badges']);
				}

				// verify we haad this one checked to be submitted
				if ($citations_action_no_attention[$k] != 1)
				{
					$citations_not_saved[] = $crna;
					continue;
				}

				// take care fo type
				$ct = new Type($this->database);
				$types = $ct->getType();

				$type = '';
				foreach ($types as $t)
				{
					// TODO: undefined index type? I just suppressed the error b/c I'm not sure what the logic is supposed to be /SS
					if (strtolower($t['type_title']) == strtolower($crna['type']))
					{
						$type = $t['id'];
					}
				}
				$crna['type'] = ($type) ? $type : '1';

				// remove duplicate flag
				unset($crna['duplicate']);

				//sets group if set
				$group = Request::getVar('group');
				if (isset($group) && $group != '')
				{
					$crna['gid'] = $group;
					$crna['scope'] = 'group';
				}

				// save the citation
				if (!$cc->save($crna))
				{
					$citations_error[] = $crna;
				}
				else
				{
					// tags
					if ($allow_tags == 'yes' && isset($tags))
					{
						$this->_tagCitation($user, $cc->id, $tags, '');
					}

					// badges
					if ($allow_badges == 'yes' && isset($badges))
					{
						$this->_tagCitation($user, $cc->id, $badges, 'badge');
					}

					// add the citattion to the saved
					$citations_saved[] = $cc->id;
				}
			}
		}

		if (isset($group) && $group != '')
		{
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_groups' . DS . 'tables' . DS . 'group.php');
			$gob = new \GroupsGroup($this->database);
			$cn = $gob->getName($group);

			$this->setRedirect(
				Route::url('index.php?option=com_groups' . DS . $cn . DS . 'citations&action=dashboard')
			);
		}
		else
		{
			// success message a redirect
			$this->addComponentMessage(
				Lang::txt('COM_CITATIONS_IMPORT_RESULTS_SAVED', count($citations_saved)),
				'passed'
			);

			// if we have citations not getting saved
			if (count($citations_not_saved) > 0)
			{
				$this->addComponentMessage(
					Lang::txt('COM_CITATIONS_IMPORT_RESULTS_NOT_SAVED', count($citations_not_saved)),
					'warning'
				);
			}

			if (count($citations_error) > 0)
			{
				$this->addComponentMessage(
					Lang::txt('COM_CITATIONS_IMPORT_RESULTS_SAVE_ERROR', count($citations_error)),
					'error'
				);
			}

			//get the session object
			$session = \JFactory::getSession();

			//ids of sessions saved and not saved
			$session->set('citations_saved', $citations_saved);
			$session->set('citations_not_saved', $citations_not_saved);
			$session->set('citations_error', $citations_error);

			//delete the temp files that hold citation data
			\JFile::delete($p1);
			\JFile::delete($p2);

			//redirect
			$this->setRedirect(
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
		$session = \JFactory::getSession();

		// Get the citations
		$citations_saved     = $session->get('citations_saved');
		$citations_not_saved = $session->get('citations_not_saved');
		$citations_error     = $session->get('citations_error');

		// Check to make sure we have citations
		if (!$citations_saved && !$citations_not_saved)
		{
			$this->setRedirect(
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
		$this->view->messages = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();

		//display view
		$this->view->display();
	}

	/**
	 * Add tags to a citation
	 *
	 * @param   integer  $userid      User ID
	 * @param   integer  $objectid    Citation ID
	 * @param   string   $tag_string  Comma separated list of tags
	 * @param   string   $label       Label
	 * @return  void
	 */
	protected function _tagCitation($userid, $objectid, $tag_string, $label)
	{
		if ($tag_string)
		{
			$ct = new Tags($objectid);
			$ct->setTags($tag_string, $userid, 0, 1, $label);
		}
	}

	/**
	 * Delete old files
	 *
	 * @return  void
	 */
	protected function _citationCleanup()
	{
		$p = $this->getTmpPath();

		if (is_dir($p))
		{
			$tmp = \JFolder::files($p);

			if ($tmp)
			{
				foreach ($tmp as $t)
				{
					$ft = filemtime($p . DS . $t);

					if ($ft < strtotime("-1 DAY"))
					{
						\JFile::delete($p . DS . $t);
					}
				}
			}
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return  void
	 */
	protected function getTmpPath()
	{
		return PATH_APP . DS . 'tmp' . DS . 'citations';
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

		\JFactory::getDocument()->setTitle($this->_title);
	}
}
