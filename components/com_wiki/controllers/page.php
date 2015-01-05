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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Wiki controller class for pages
 */
class WikiControllerPage extends \Hubzero\Component\SiteController
{
	public $book = null;

	/**
	 * Constructor
	 *
	 * @param      array $config Optional configurations
	 * @return     void
	 */
	public function __construct($config=array())
	{
		$this->_base_path = JPATH_ROOT . DS . 'components' . DS . 'com_wiki';
		if (isset($config['base_path']))
		{
			$this->_base_path = $config['base_path'];
		}

		$this->_sub = false;
		if (isset($config['sub']))
		{
			$this->_sub = $config['sub'];
		}

		$this->_group = false;
		if (isset($config['group']))
		{
			$this->_group = $config['group'];
		}

		if ($this->_sub)
		{
			JRequest::setVar('task', JRequest::getWord('action'));
		}

		$this->book = new WikiModelBook(($this->_group ? $this->_group : '__site__'));

		parent::__construct($config);
	}

	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		/*if ($this->_sub || $this->_option != 'com_wiki')
		{
			$this->config = JComponentHelper::getParams('com_wiki');
		}*/

		if (!$this->book->pages('count'))
		{
			if ($result = $this->book->scribe($this->_option))
			{
				$this->setError($result);
			}

			JPROFILE ? JProfiler::getInstance('Application')->mark('afterWikiSetup') : null;
		}

		$this->page = $this->book->page();

		if (in_array($this->page->get('namespace'), array('image', 'file')))
		{
			$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=media&scope=' . $this->page->get('scope') . '&pagename=' . $this->page->get('pagename') . '&task=download'
			);
			return;
		}

		parent::execute();
	}

	/**
	 * Display a page
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->book      = $this->book;
		$this->view->page      = $this->page;
		$this->view->config    = $this->config;
		$this->view->base_path = $this->_base_path;
		$this->view->sub       = $this->_sub;

		// Prep the pagename for display
		$this->view->title = $this->page->get('title'); //getTitle();

		// Set the page's <title> tag
		$document = JFactory::getDocument();
		if ($this->_sub)
		{
			$document->setTitle($document->getTitle() . ': ' . $this->view->title);
		}
		else
		{
			$document->setTitle(($this->_sub ? JText::_('COM_GROUPS') . ': ' : '') . JText::_('COM_WIKI') . ': ' . $this->view->title);
		}

		// Set the pathway
		$pathway = JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
		}

		// Is this a special page?
		if ($this->page->get('namespace') == 'special')
		{
			// Set the layout
			$this->view->setLayout('special');
			$this->view->layout = $this->page->denamespaced(); //get('pagename')
			$this->view->page->set('scope', JRequest::getVar('scope', ''));
			$this->view->page->set('group_cn', $this->_group);
			$this->view->message = $this->_message;

			// Ensure the special page exists
			if (!in_array(strtolower($this->view->layout), $this->book->special()))
			{
				//JError::raiseWarning(404, JText::_('COM_WIKI_WARNING_NOT_FOUND'));
				$this->setRedirect(
					JRoute::_('index.php?option=' . $this->_option . '&scope=' . $this->view->page->get('scope'))
				);
				return;
			}

			if ($this->getError())
			{
				foreach ($this->getErrors() as $error)
				{
					$this->view->setError($error);
				}
			}
			$this->view->display();
			return;
		}

		// Does a page exist for the given pagename?
		if (!$this->page->exists() || $this->page->isDeleted())
		{
			// No! Ask if they want to create a new page
			$this->view->setLayout('doesnotexist');
			if ($this->_group)
			{
				$this->page->set('group_cn', $this->_group);
				$this->page->set('scope', $this->_group . '/wiki');
			}

			if ($this->getError())
			{
				foreach ($this->getErrors() as $error)
				{
					$this->view->setError($error);
				}
			}
			$this->view->display();
			return;
		}

		if ($this->page->get('group_cn') && !$this->_group)
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_groups&scope=' . $this->page->get('scope') . '&pagename=' . $this->page->get('pagename'))
			);
			return;
		}

		// Check if the page is group restricted and the user is authorized
		if (!$this->page->access('view', 'page'))
		{
			JError::raiseWarning(403, JText::_('COM_WIKI_WARNING_NOT_AUTH'));
			return;
		}

		if ($this->page->get('scope') && !$this->page->get('group_cn'))
		{
			$bits = explode('/', $this->page->get('scope'));
			$s = array();
			foreach ($bits as $bit)
			{
				$bit = trim($bit);
				if ($bit != '/' && $bit != '')
				{
					$p = WikiModelPage::getInstance($bit, implode('/', $s));
					if ($p->exists())
					{
						$pathway->addItem(
							$p->get('title'),
							$p->link()
						);
					}
					$s[] = $bit;
				}
			}
		}
		/*if ($this->page->get('group_cn'))
		{
			$pathway->addItem(
				JText::_('Wiki'),
				$this->page->link('base')
			);
		}*/
		$pathway->addItem(
			$this->view->title,
			$this->page->link()
		);

		// Retrieve a specific version if given
		$this->view->version  = JRequest::getInt('version', 0);
		$this->view->revision = $this->page->revision($this->view->version);

		if (!$this->view->revision->exists())
		{
			$this->view->setLayout('nosuchrevision');

			if ($this->getError())
			{
				foreach ($this->getErrors() as $error)
				{
					$this->view->setError($error);
				}
			}
			$this->view->display();
			return;
		}

		if (JRequest::getVar('format', '') == 'raw')
		{
			JRequest::setVar('no_html', 1);

			echo nl2br($this->view->revision->get('pagetext'));
			return;
		}

		// Load the wiki parser
		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => $this->page->get('scope'),
			'pagename' => $this->page->get('pagename'),
			'pageid'   => $this->page->get('id'),
			'filepath' => '',
			'domain'   => $this->page->get('group_cn')
		);

		$p = WikiHelperParser::getInstance();

		// Parse the text
		if (intval($this->book->config('cache', 1)))
		{
			// Caching
			// Default time is 15 minutes
			$cache = JFactory::getCache('callback');
			$cache->setCaching(1);
			$cache->setLifeTime(intval($this->book->config('cache_time', 15)));

			$this->view->revision->set('pagehtml', $cache->call(
				array($p, 'parse'),
				$this->view->revision->get('pagetext'), $wikiconfig, true, true
			));
		}
		else
		{
			$this->view->revision->set('pagehtml', $p->parse($this->view->revision->get('pagetext'), $wikiconfig, true, true));
		}

		JPROFILE ? JProfiler::getInstance('Application')->mark('afterWikiParse') : null;

		// Handle display events
		JPluginHelper::importPlugin('wiki');
		$dispatcher = JDispatcher::getInstance();

		$this->page->event = new stdClass();

		$results = $dispatcher->trigger('onAfterDisplayTitle', array($this->page, &$this->view->revision, $this->config));
		$this->page->event->afterDisplayTitle = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onBeforeDisplayContent', array(&$this->page, &$this->view->revision, $this->config));
		$this->page->event->beforeDisplayContent = trim(implode("\n", $results));

		$results = $dispatcher->trigger('onAfterDisplayContent', array(&$this->page, &$this->view->revision, $this->config));
		$this->page->event->afterDisplayContent = trim(implode("\n", $results));

		$this->view->message = $this->_message;

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	/**
	 * Show a form for creating an entry
	 *
	 * @return     void
	 */
	public function newTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @return     void
	 */
	public function editTask()
	{
		// Check if they are logged in
		if ($this->juser->get('guest'))
		{
			$url = JRequest::getVar('REQUEST_URI', '', 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($url))
			);
			return;
		}

		// Check if the page is locked and the user is authorized
		if ($this->page->get('state') == 1 && !$this->page->access('manage'))
		{
			$this->setRedirect(
				JRoute::_($this->page->link()),
				JText::_('COM_WIKI_WARNING_NOT_AUTH_EDITOR'),
				'warning'
			);
			return;
		}

		// Check if the page is group restricted and the user is authorized
		if (!$this->page->access('edit') && !$this->page->access('modify'))
		{
			$this->setRedirect(
				JRoute::_($this->page->link()),
				JText::_('COM_WIKI_WARNING_NOT_AUTH_EDITOR'),
				'warning'
			);
			return;
		}

		$this->view->setLayout('edit');

		// Load the page
		$ischild = false;
		if ($this->page->get('id') && $this->_task == 'new')
		{
			$this->page->set('id', 0);
			$ischild = true;
		}

		// Get the most recent version for editing
		if (!is_object($this->revision))
		{
			$this->revision = $this->page->revision('current'); //getCurrentRevision();
			$this->revision->set('created_by', $this->juser->get('id'));
			$this->revision->set('summary', '');
		}

		// If an existing page, pull its tags for editing
		if (!$this->page->exists())
		{
			$this->page->set('access', 0);
			$this->page->set('created_by', $this->juser->get('id'));

			if ($this->_group)
			{
				$this->page->set('group_cn', $this->_group);
				$this->page->set('scope', $this->_group . '/' . $this->_sub);
			}

			if ($ischild && $this->page->get('pagename'))
			{
				$this->revision->set('pagetext', '');
				$this->page->set('scope', $this->page->get('scope') . ($this->page->get('scope') ? '/' . $this->page->get('pagename') : $this->page->get('pagename')));
				$this->page->set('pagename', '');
				$this->page->set('title', JText::_('COM_WIKI_NEW_PAGE'));
			}
		}

		$this->view->tags = trim(JRequest::getVar('tags', $this->page->tags('string'), 'post'));
		$this->view->authors = trim(JRequest::getVar('authors', $this->page->authors('string'), 'post'));

		// Prep the pagename for display
		// e.g. "MainPage" becomes "Main Page"
		$this->view->title = (trim($this->page->get('title')) ? $this->page->get('title') : JText::_('COM_WIKI_NEW_PAGE'));

		// Set the page's <title> tag
		$document = JFactory::getDocument();
		if ($this->_sub)
		{
			$document->setTitle($document->getTitle() . ': ' . $this->view->title);
		}
		else
		{
			$document->setTitle(JText::_(strtoupper($this->_option)) . ': ' . $this->view->title . ': ' . JText::_(strtoupper($this->_option . '_' . $this->_task)));
		}

		// Set the pathway
		$pathway = JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller
			);
		}
		if (!$this->_sub)
		{
			$pathway->addItem(
				$this->view->title,
				$this->page->link()
			);
			$pathway->addItem(
				JText::_(strtoupper($this->_option . '_' . $this->_task)),
				$this->page->link() . '&task=' . $this->_task
			);
		}

		$this->view->preview = NULL;

		// Are we previewing?
		if ($this->preview)
		{
			// Yes - get the preview so we can parse it and display
			$this->view->preview = $this->preview;

			// Parse the HTML
			$wikiconfig = array(
				'option'   => $this->_option,
				'scope'    => $this->page->get('scope'),
				'pagename' => $this->page->get('pagename'),
				'pageid'   => $this->page->get('id'),
				'filepath' => '',
				'domain'   => $this->_group
			);

			$p = WikiHelperParser::getInstance();

			$this->revision->set('pagehtml', $p->parse($this->revision->get('pagetext'), $wikiconfig, true, true));
		}

		$this->view->sub       = $this->_sub;
		$this->view->base_path = $this->_base_path;
		$this->view->message   = $this->_message;
		$this->view->page      = $this->page;
		$this->view->book      = $this->book;
		//$this->view->config    = $this->config;
		$this->view->revision  = $this->revision;

		// Pull a tree of pages in this wiki
		$items = $this->book->pages('list', array(
			'group'  => $this->_group,
			'sortby' => 'pagename ASC, scope ASC',
			'state'  => array(0, 1)
		));
		$tree = array();
		if ($items)
		{
			foreach ($items as $k => $branch)
			{
				// Since these will be parent pages, we need to add the item's pagename to the scope
				$branch->set('scope', ($branch->get('scope') ? $branch->get('scope') . '/' . $branch->get('pagename') : $branch->get('pagename')));
				$branch->set('scopeName', $branch->get('scope'));
				// Strip the group name from the beginning of the scope for display.
				if ($this->_group)
				{
					$branch->set('scopeName', substr($branch->get('scope'), strlen($this->_group . '/wiki/')));
				}
				// Push the item to the tree
				$tree[$branch->get('scope')] = $branch;
			}
			ksort($tree);
		}
		$this->view->tree = $tree; //$items;

		$this->view->tplate = trim(JRequest::getVar('tplate', ''));

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}

	/**
	 * Save a wiki page
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Check if they are logged in
		if ($this->juser->get('guest'))
		{
			$url = JRequest::getVar('REQUEST_URI', '', 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($url))
			);
			return;
		}

		// Incoming revision
		$rev = JRequest::getVar('revision', array(), 'post', 'none', 2);
		//$rev['pageid'] = (isset($rev['pageid'])) ? intval($rev['pageid']) : 0;

		$this->revision = $this->page->revision('current');
		$this->revision->set('version', $this->revision->get('version') + 1);
		if (!$this->revision->bind($rev))
		{
			$this->setError($this->revision->getError());
			$this->editTask();
			return;
		}
		$this->revision->set('id', 0);

		// Incoming page
		$page = JRequest::getVar('page', array(), 'post', 'none', 2);

		$this->page = new WikiModelPage(intval($rev['pageid']));
		if (!$this->page->bind($page))
		{
			$this->setError($this->page->getError());
			$this->editTask();
			return;
		}
		$this->page->set('pagename', trim(JRequest::getVar('pagename', '', 'post')));
		$this->page->set('scope', trim(JRequest::getVar('scope', '', 'post')));

		// Get parameters
		$paramClass = 'JRegistry';
		$bindMethod = 'loadArray';

		$params = new $paramClass($this->page->get('params', ''));
		$params->$bindMethod(JRequest::getVar('params', array(), 'post'));

		$this->page->set('params', $params->toString());

		// Get the previous version to compare against
		if (!$rev['pageid'])
		{
			// New page - save it to the database
			$this->page->set('created_by', $this->juser->get('id'));

			$old = new WikiModelRevision(0);
		}
		else
		{
			// Get the revision before changes
			$old = $this->page->revision('current');
		}

		// Was the preview button pushed?
		$this->preview = trim(JRequest::getVar('preview', ''));
		if ($this->preview)
		{
			// Set the component task
			if (!$rev['pageid'])
			{
				JRequest::setVar('task', 'new');
				$this->_task = 'new';
			}
			else
			{
				JRequest::setVar('task', 'edit');
				$this->_task = 'edit';
			}

			// Push on through to the edit form
			$this->editTask();
			return;
		}

		// Check content
		// First, make sure the pagetext isn't empty
		if ($this->revision->get('pagetext') == '')
		{
			$this->setError(JText::_('COM_WIKI_ERROR_MISSING_PAGETEXT'));
			$this->editTask();
			return;
		}

		// Store new content
		if (!$this->page->store(true))
		{
			$this->setError($this->page->getError());
			$this->editTask();
			return;
		}

		// Get allowed authors
		if (!$this->page->updateAuthors(JRequest::getVar('authors', '', 'post')))
		{
			$this->setError($this->page->getError());
			$this->editTask();
			return;
		}

		// Get the upload path
		$path = DS . trim($this->book->config('filepath', '/site/wiki'), DS);

		// Rename the temporary upload directory if it exist
		$lid = JRequest::getInt('lid', 0, 'post');
		if ($lid != $this->page->get('id'))
		{
			if (is_dir(JPATH_ROOT . $path . DS . $lid))
			{
				jimport('joomla.filesystem.folder');
				if (!JFolder::move(JPATH_ROOT . $path . DS . $lid, JPATH_ROOT . $path . DS . $this->page->get('id')))
				{
					$this->setError(JFolder::move(JPATH_ROOT . $path . DS . $lid, JPATH_ROOT . $path . DS . $this->page->get('id')));
				}
				$wpa = new WikiTableAttachment($this->database);
				$wpa->setPageID($lid, $this->page->get('id'));
			}
		}

		$this->revision->set('pageid',   $this->page->get('id'));
		$this->revision->set('pagename', $this->page->get('pagename'));
		$this->revision->set('scope',    $this->page->get('scope'));
		$this->revision->set('group_cn', $this->page->get('group_cn'));
		$this->revision->set('version',  $this->revision->get('version') + 1);

		if ($this->page->param('mode', 'wiki') == 'knol')
		{
			// Set revisions to NOT approved
			$this->revision->set('approved', 0);
			// If an author or the original page creator, set to approved
			if ($this->page->get('created_by') == $this->juser->get('id')
			 || $this->page->isAuthor($this->juser->get('id')))
			{
				$this->revision->set('approved', 1);
			}
		}
		else
		{
			// Wiki mode, approve revision
			$this->revision->set('approved', 1);
		}

		// Compare against previous revision
		// We don't want to create a whole new revision if just the tags were changed
		if (rtrim($old->get('pagetext')) != rtrim($this->revision->get('pagetext')))
		{
			// Transform the wikitext to HTML
			$this->revision->set('pagehtml', '');
			$this->revision->set('pagehtml', $this->revision->content('parsed'));

			// Parse attachments
			/*$a = new WikiTableAttachment($this->database);
			$a->pageid = $this->page->id;
			$a->path = $path;

			$this->revision->pagehtml = $a->parse($this->revision->pagehtml);*/
			if ($this->page->access('manage') || $this->page->access('edit'))
			{
				$this->revision->set('approved', 1);
			}

			// Store content
			if (!$this->revision->store(true))
			{
				$this->setError(JText::_('COM_WIKI_ERROR_SAVING_REVISION'));
				$this->editTask();
				return;
			}

			$this->page->set('version_id', $this->revision->get('id'));
			$this->page->set('modified', $this->revision->get('created'));
		}
		else
		{
			$this->page->set('modified', \JFactory::getDate()->toSql());
		}

		if (!$this->page->store(true))
		{
			// This really shouldn't happen.
			$this->setError(JText::_('COM_WIKI_ERROR_SAVING_PAGE'));
			$this->editTask();
			return;
		}

		// Process tags
		$this->page->tag(JRequest::getVar('tags', ''));

		// Redirect
		$this->setRedirect(
			JRoute::_($this->page->link())
		);
	}

	/**
	 * Delete a page
	 *
	 * @return     void
	 */
	public function deleteTask()
	{
		// Check if they are logged in
		if ($this->juser->get('guest'))
		{
			$url = JRequest::getVar('REQUEST_URI', '', 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($url))
			);
			return;
		}

		if (!is_object($this->page))
		{
			$this->setRedirect(
				JRoute::_($this->page->link('base')),
				JText::_('COM_WIKI_ERROR_PAGE_NOT_FOUND'),
				'error'
			);
			return;
		}

		// Make sure they're authorized to delete
		if (!$this->page->access('delete'))
		{
			$this->setRedirect(
				JRoute::_($this->page->link('base')),
				JText::_('COM_WIKI_ERROR_NOTAUTH'),
				'error'
			);
			return;
		}

		$confirmed = JRequest::getInt('confirm', 0, 'post');

		switch ($confirmed)
		{
			case 1:
				// Check for request forgeries
				JRequest::checkToken() or jexit('Invalid Token');

				$this->page->set('state', 2);
				if (!$this->page->store(false, 'page_removed'))
				{
					$this->setError(JText::_('COM_WIKI_UNABLE_TO_DELETE'));
				}

				$cache = JFactory::getCache('callback');
				$cache->clean('callback');
			break;

			default:
				$this->view->page      = $this->page;
				$this->view->config    = $this->config;
				$this->view->base_path = $this->_base_path;
				$this->view->sub       = $this->_sub;

				// Prep the pagename for display
				// e.g. "MainPage" becomes "Main Page"
				$this->view->title = $this->page->get('title');

				// Set the page's <title> tag
				$document = JFactory::getDocument();
				$document->setTitle(JText::_(strtoupper($this->_option)) . ': ' . $this->view->title . ': ' . JText::_(strtoupper($this->_option . '_' . $this->_task)));

				// Set the pathway
				$pathway = JFactory::getApplication()->getPathway();
				if (count($pathway->getPathWay()) <= 0)
				{
					$pathway->addItem(
						JText::_(strtoupper($this->_option)),
						'index.php?option=' . $this->_option . '&controller=' . $this->_controller
					);
				}
				$pathway->addItem(
					$this->view->title,
					$this->page->link()
				);
				$pathway->addItem(
					JText::_(strtoupper($this->_option . '_' . $this->_task)),
					$this->page->link('delete')
				);

				$this->view->message = $this->_message;

				if ($this->getError())
				{
					foreach ($this->getErrors() as $error)
					{
						$this->view->setError($error);
					}
				}

				$this->view->display();
				return;
			break;
		}

		$this->setRedirect(
			JRoute::_($this->page->link('base'))
		);
	}

	/**
	 * Show a form to rename a page
	 *
	 * @return     void
	 */
	public function renameTask()
	{
		// Check if they are logged in
		if ($this->juser->get('guest'))
		{
			$url = JRequest::getVar('REQUEST_URI', '', 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($url))
			);
			return;
		}

		// Make sure they're authorized to delete
		if (!$this->page->access('edit'))
		{
			$this->setRedirect(
				JRoute::_($this->page->link('base')),
				JText::_('COM_WIKI_ERROR_NOTAUTH'),
				'error'
			);
			return;
		}

		// Set the layout
		// This is done in case we fell through from saverenameTask()
		$this->view->setLayout('rename');

		$this->view->page      = $this->page;
		$this->view->config    = $this->config;
		$this->view->base_path = $this->_base_path;
		$this->view->sub       = $this->_sub;

		// Prep the pagename for display
		// e.g. "MainPage" becomes "Main Page"
		$this->view->title = $this->page->get('title');

		// Set the page's <title> tag
		$document = JFactory::getDocument();
		$document->setTitle(JText::_(strtoupper($this->_name)) . ': ' . $this->view->title . ': ' . JText::_('RENAME'));

		// Set the pathway
		$pathway = JFactory::getApplication()->getPathway();
		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_name)),
				'index.php?option=' . $this->_option
			);
		}
		$pathway->addItem(
			$this->view->title,
			$this->page->link()
		);
		$pathway->addItem(
			JText::_(strtoupper('COM_WIKI_RENAME')),
			$this->page->link('rename')
		);

		$this->view->message = $this->_message;

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output HTML
		$this->view->display();
	}

	/**
	 * Save the new page name
	 *
	 * @return     void
	 */
	public function saverenameTask()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Check if they are logged in
		if ($this->juser->get('guest'))
		{
			$url = JRequest::getVar('REQUEST_URI', '', 'server');
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($url))
			);
			return;
		}

		// Incoming
		$oldpagename = trim(JRequest::getVar('oldpagename', '', 'post'));
		$newpagename = trim(JRequest::getVar('newpagename', '', 'post'));
		$scope = trim(JRequest::getVar('scope', '', 'post'));

		// Load the page
		$this->page = new WikiModelPage($oldpagename, $scope);

		// Attempt to rename
		if (!$this->page->rename($newpagename))
		{
			$this->setError($this->page->getError());
			$this->renameTask();
			return;
		}

		// Redirect to the newly named page
		$this->setRedirect(
			JRoute::_($this->page->link())
		);
	}

	/**
	 * Output the contents of a wiki page as a PDF
	 *
	 * Based on work submitted by Steven Maus <steveng4235@gmail.com> (2014)
	 *
	 * @return     void
	 */
	public function pdfTask()
	{
		// Does a page exist for the given pagename?
		if (!$this->page->exists() || $this->page->isDeleted())
		{
			// No! Ask if they want to create a new page
			$this->view->setLayout('doesnotexist');
			if ($this->_group)
			{
				$this->page->set('group_cn', $this->_group);
				$this->page->set('scope', $this->_group . '/wiki');
			}

			if ($this->getError())
			{
				foreach ($this->getErrors() as $error)
				{
					$this->view->setError($error);
				}
			}
			$this->view->display();
			return;
		}

		// Retrieve a specific version if given
		$this->view->revision = $this->page->revision(JRequest::getInt('version', 0));
		if (!$this->view->revision->exists())
		{
			$this->view->setLayout('nosuchrevision');

			if ($this->getError())
			{
				foreach ($this->getErrors() as $error)
				{
					$this->view->setError($error);
				}
			}
			$this->view->display();
			return;
		}

		JRequest::setVar('format', 'pdf');

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(10);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// Set font
		$pdf->SetFont('dejavusans', '', 11, '', true);

		//$current = $page->getCurrentRevision();
		//$pageTitle = $page->getTitle();
		//$pageAuthor = $page->authors();
		$pdf->setAuthor  = $this->page->creator('name');
		$pdf->setCreator = JFactory::getConfig()->get('sitename');

		$pdf->setDocModificationTimeStamp($this->page->modified());
		$pdf->setHeaderData(NULL, 0, strtoupper($this->page->get('itle')), NULL, array(84, 94, 124), array(146, 152, 169));
		$pdf->setFooterData(array(255, 255, 255), array(255, 255, 255));

		$pdf->AddPage();

		// Set the view page content to current revision html
		$this->view->page = $this->page;

		// Load the wiki parser
		$wikiconfig = array(
			'option'   => $this->_option,
			'scope'    => $this->page->get('scope'),
			'pagename' => $this->page->get('pagename'),
			'pageid'   => $this->page->get('id'),
			'filepath' => '',
			'domain'   => $this->page->get('group_cn')
		);

		$p = WikiHelperParser::getInstance();

		// Parse the text
		$this->view->revision->set('pagehtml', $p->parse($this->view->revision->get('pagetext'), $wikiconfig, true, true));

		$pdf->writeHTML($this->view->loadTemplate(), true, false, true, false, '');

		header("Content-type: application/octet-stream");

		// Close and output PDF document
		// Force the download of the PDF
		$pdf->Output($this->page->get('pagename') . '.pdf', 'D');
		exit();
	}
}

