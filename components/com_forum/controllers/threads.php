<?php
/**
 * @package		HUBzero                                  CMS
 * @author		Shawn                                     Rice <zooley@purdue.edu>
 * @copyright	Copyright                               2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * 
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Forum controller class for threads
 */
class ForumControllerThreads extends Hubzero_Controller
{
	/**
	 * Execute a task
	 *
	 * @return	void
	 */
	public function execute()
	{
		$this->model = new ForumModel('site', 0);

		$this->registerTask('latest', 'feed');
		$this->registerTask('latest', 'feed.rss');
		$this->registerTask('latest', 'latest.rss');

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	protected function _buildPathway()
	{
		$pathway = JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if (isset($this->view->section))
		{
			$pathway->addItem(
				stripslashes($this->view->section->get('title')), 
				'index.php?option=' . $this->_option . '&section=' . $this->view->section->get('alias')
			);
		}
		if (isset($this->view->category))
		{
			$pathway->addItem(
				stripslashes($this->view->category->get('title')), 
				'index.php?option=' . $this->_option . '&section=' . $this->view->section->get('alias') . '&category=' . $this->view->category->get('alias')
			);
		}
		if (isset($this->view->thread) && $this->view->thread->exists())
		{
			$pathway->addItem(
				'#' . $this->view->thread->get('id') . ' - ' . stripslashes($this->view->thread->get('title')), 
				'index.php?option=' . $this->_option . '&section=' . $this->view->section->get('alias') . '&category=' . $this->view->category->get('alias') . '&thread=' . $this->view->thread->get('id')
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return	void
	 */
	protected function _buildTitle()
	{
		$this->_title = JText::_(strtoupper($this->_option));
		if (isset($this->view->section))
		{
			$this->_title .= ': ' . stripslashes($this->view->section->get('title'));
		}
		if (isset($this->view->category))
		{
			$this->_title .= ': ' . stripslashes($this->view->category->get('title'));
		}
		if (isset($this->view->thread) && $this->view->thread->exists())
		{
			$this->_title .= ': #' . $this->view->thread->get('id') . ' - ' . stripslashes($this->view->thread->get('title'));
		}
		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Display a thread
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->title = JText::_('Discussion Forum');

		// Incoming
		$this->view->filters = array(
			'limit'    => JRequest::getInt('limit', 25),
			'start'    => JRequest::getInt('limitstart', 0),
			'section'  => JRequest::getVar('section', ''),
			'category' => JRequest::getCmd('category', ''),
			'parent'   => JRequest::getInt('thread', 0),
			'state'    => 1
		);

		$this->view->section  = $this->model->section($this->view->filters['section'], $this->model->get('scope'), $this->model->get('scope_id'));
		if (!$this->view->section->exists())
		{
			JError::raiseError(404, JText::_('Section not found.'));
			return;
		}

		$this->view->category = $this->view->section->category($this->view->filters['category']);
		if (!$this->view->category->exists())
		{
			JError::raiseError(404, JText::_('Category not found.'));
			return;
		}

		$this->view->filters['category_id'] = $this->view->category->get('id');

		// Load the topic
		$this->view->thread = $this->view->category->thread($this->view->filters['parent']);

		// Check logged in status
		if ($this->view->thread->get('access') > 0 && $this->juser->get('guest')) 
		{
			$return = base64_encode(JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section=' . $this->view->filters['section'] . '&category=' . $this->view->filters['category'] . '&thread=' . $this->view->filters['parent']), false, true);
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . $return)
			);
			return;
		}

		// Get authorization
		$this->_authorize('category', $this->view->category->get('id'));
		$this->_authorize('thread', $this->view->thread->get('id'));
		$this->_authorize('post');

		$this->view->config = $this->config;
		$this->view->model  = $this->model;

		$this->view->notifications = $this->getComponentMessage();

		// Push CSS to the template
		$this->_getStyles();

		// Push scripts to the template
		$this->_getScripts('assets/js/' . $this->_name);

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Set any errors
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
	 * Show a form for creating a new entry
	 * 
	 * @return     void
	 */
	public function latestTask()
	{
		include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'document' . DS . 'feed' . DS . 'feed.php');
		ximport('Hubzero_Group');
		ximport('Hubzero_View_Helper_Html');

		$app =& JFactory::getApplication();

		// Set the mime encoding for the document
		$jdoc =& JFactory::getDocument();
		$jdoc->setMimeEncoding('application/rss+xml');

		// Start a new feed object
		$doc = new JDocumentFeed;
		$doc->link = JRoute::_('index.php?option=' . $this->_option);

		// Get configuration
		$jconfig = JFactory::getConfig();

		// Paging variables
		$start = JRequest::getInt('limitstart', 0);
		$limit = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));

		// Build some basic RSS document information
		$doc->title  = $jconfig->getValue('config.sitename') . ' - ' . JText::_('COM_FORUM_RSS_TITLE');
		$doc->description = JText::sprintf('COM_FORUM_RSS_DESCRIPTION', $jconfig->getValue('config.sitename'));
		$doc->copyright   = JText::sprintf('COM_FORUM_RSS_COPYRIGHT', date("Y"), $jconfig->getValue('config.sitename'));
		$doc->category    = JText::_('COM_FORUM_RSS_CATEGORY');

		// get all forum posts on site forum
		$this->database->setQuery("SELECT f.* FROM #__forum_posts f WHERE f.scope_id='0' AND scope='site' AND f.state='1'");
		$site_forum = $this->database->loadAssocList();

		// get any group posts
		$this->database->setQuery("SELECT f.* FROM #__forum_posts f WHERE f.scope_id<>'0' AND scope='group' AND f.state='1'");
		$group_forum = $this->database->loadAssocList();

		// make sure that the group for each forum post has the right privacy setting
		foreach ($group_forum as $k => $gf) 
		{
			$group = Hubzero_Group::getInstance($gf['scope_id']);
			if (is_object($group)) 
			{
				ximport("Hubzero_Group_Helper");
				$forum_access = Hubzero_Group_Helper::getPluginAccess($group, 'forum');

				if ($forum_access == 'nobody' 
				 || ($forum_access == 'registered' && $this->juser->get('guest')) 
				 || ($forum_access == 'members' && !in_array($this->juser->get('id'), $group->get('members')))) 
				{
					unset($group_forum[$k]);
				}
			} 
			else 
			{
				unset($group_forum[$k]);
			}
		}

		//based on param decide what to include
		switch ($this->config->get('forum', 'both')) 
		{
			case 'site':  $rows = $site_forum;  break;
			case 'group': $rows = $group_forum; break;
			case 'both':  
			default:
				$rows = array_merge($site_forum, $group_forum);
			break;
		}

		$categories = array();
		$ids = array();
		foreach ($rows as $post)
		{
			$ids[] = $post['category_id'];
		}
		$this->database->setQuery("SELECT c.id, c.alias, s.alias as section FROM #__forum_categories c LEFT JOIN #__forum_sections as s ON s.id=c.section_id WHERE c.id IN (" . implode(',', $ids) . ") AND c.state='1'");
		$cats = $this->database->loadObjectList();
		if ($cats)
		{
			foreach ($cats as $category)
			{
				$categories[$category->id] = $category;
			}
		}

		//function to sort by created date
		function sortbydate($a, $b)
		{
			$d1 = date("Y-m-d H:i:s", strtotime($a['created']));
			$d2 = date("Y-m-d H:i:s", strtotime($b['created']));
			
			return ($d1 > $d2) ? -1 : 1;
		}

		//sort using function above - date desc
		usort($rows, 'sortbydate');

		// Start outputing results if any found
		if (count($rows) > 0) 
		{
			foreach ($rows as $row)
			{
				// Prepare the title
				$title = strip_tags(stripslashes($row['title']));
				$title = html_entity_decode($title);

				// Get URL
				if ($row['scope_id'] == 0) 
				{
					$link = 'index.php?option=com_forum&section=' . $categories[$row['category_id']]->section . '&category=' . $categories[$row['category_id']]->alias . '&thread=' . ($row['parent'] ? $row['parent'] : $row['id']);
				} 
				else 
				{
					$group = Hubzero_Group::getInstance($row['scope_id']);
					$link = 'index.php?option=com_groups&gid=' . $group->get('cn') . '&active=forum&scope=' .  $categories[$row['category_id']]->section . '/' . $categories[$row['category_id']]->alias . '/' . ($row['parent'] ? $row['parent'] : $row['id']);
				}
				$link = JRoute::_($link);
				$link = DS . ltrim($link, DS);

				// Get description
				$description = stripslashes($row['comment']);
				$description = Hubzero_View_Helper_Html::shortenText($description, 300, 0, 0);

				// Get author
				$juser =& JUser::getInstance($row['created_by']);
				$author = stripslashes($juser->get('name'));

				// Get date
				@$date = ($row->created ? date('r', strtotime($row->created)) : '');

				// Load individual item creator class
				$item = new JFeedItem();
				$item->title       = $title;
				$item->link        = $link;
				$item->description = $description;
				$item->date        = $date;
				$item->category    = ($row['scope_id'] == 0) ? JText::_('Site-Wide Forum') : stripslashes($group->get('description'));
				$item->author      = $author;

				// Loads item info into rss array
				$doc->addItem($item);
			}
		}

		// Output the feed
		echo $doc->render();
	}

	/**
	 * Show a form for creating a new entry
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
	public function editTask($post=null)
	{
		$this->view->setLayout('edit');

		$id       = JRequest::getInt('thread', 0);
		$category = JRequest::getCmd('category', '');
		$section  = JRequest::getVar('section', '');

		if ($this->juser->get('guest')) 
		{
			$return = JRoute::_('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category . '&task=new');
			if ($id)
			{
				$return = JRoute::_('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category . '&thread=' . $id . '&task=edit');
			}
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($return)).
					JText::_('Please login to create or edit posts.'),
					'warning'
			);
			return;
		}

		$this->view->section  = $this->model->section($section, $this->model->get('scope'), $this->model->get('scope_id'));
		if (!$this->view->section->exists())
		{
			JError::raiseError(404, JText::_('Section not found.'));
			return;
		}

		$this->view->category = $this->view->section->category($category);
		if (!$this->view->category->exists())
		{
			JError::raiseError(404, JText::_('Category not found.'));
			return;
		}

		// Incoming
		if (is_object($post))
		{
			$this->view->post = $post;
		}
		else 
		{
			$this->view->post = new ForumModelThread($id);
		}

		$this->_authorize('thread', $id);

		if (!$id) 
		{
			$this->view->post->set('created_by', $this->juser->get('id'));
		}
		elseif ($this->view->post->get('created_by') != $this->juser->get('id') && !$this->config->get('access-edit-thread')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category),
				JText::_('You are not authorized to edit this thread.'),
				'warning'
			);
			return;
		}

		// Push CSS to the template
		$this->_getStyles();

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		$this->view->config = $this->config;
		$this->view->model  = $this->model;

		$this->view->notifications = $this->getComponentMessage();

		// Set any errors
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
	 * Save an entry
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		if ($this->juser->get('guest')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_('index.php?option=' . $this->_option)))
			);
			return;
		}

		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Incoming
		$section = JRequest::getVar('section', '');

		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		$assetType = 'thread';
		if ($fields['parent'])
		{
			$assetType = 'post';
		}

		$this->_authorize($assetType, intval($fields['id']));
		if (!$this->config->get('access-edit-' . $assetType) && !$this->config->get('access-create-' . $assetType))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_('index.php?option=' . $this->_option)))
			);
			return;
		}

		$fields['sticky'] = (isset($fields['sticky'])) ? $fields['sticky'] : 0;
		$fields['closed'] = (isset($fields['closed'])) ? $fields['closed'] : 0;

		if ($fields['id'])
		{
			$old = new ForumModelPost(intval($fields['id']));
		}

		// Bind data
		$model = new ForumModelPost($fields['id']);
		if ($model->get('parent'))
		{
			$thread = new ForumModelThread($fields['thread']);
			if (!$thread->exists() || $thread->get('closed'))
			{
				$this->addComponentMessage(JText::_('This thread is closed and not accepting new posts.'), 'error');
				$this->editTask($model);
				return;
			}
		}
		if (!$model->bind($fields)) 
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}

		// Store new content
		if (!$model->store(true)) 
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}

		$parent = $model->get('thread', $model->get('id'));

		// Upload files
		$this->uploadTask($parent, $model->get('id'));

		// Save tags
		$model->tag(JRequest::getVar('tags', '', 'post'), $this->juser->get('id'));

		// Determine message
		if (!$fields['id'])
		{
			if (!$fields['parent']) 
			{
				$message = JText::_('COM_FORUM_THREAD_STARTED');
			}
			else 
			{
				$message = JText::_('COM_FORUM_POST_ADDED');
			}
		}
		else 
		{
			$message = ($model->modified_by) ? JText::_('COM_FORUM_POST_EDITED') : JText::_('COM_FORUM_POST_ADDED');
		}

		$category = new ForumModelCategory($model->get('category_id'));

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category->get('alias') . '&thread=' . $parent . '#c' . $model->get('id')),
			$message,
			'message'
		);
	}

	/**
	 * Delete an entry
	 * 
	 * @return     void
	 */
	public function deleteTask()
	{
		$section  = JRequest::getVar('section', '');
		$category = JRequest::getVar('category', '');

		// Is the user logged in?
		if ($this->juser->get('guest')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category),
				JText::_('COM_FORUM_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$id = JRequest::getInt('thread', 0);

		// Load the post
		$model = new ForumPost($this->database);
		$model->load($id);

		// Make the sure the category exist
		if (!$model->id) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category),
				JText::_('COM_FORUM_MISSING_ID'),
				'error'
			);
			return;
		}

		// Check if user is authorized to delete entries
		$this->_authorize('thread', $id);
		if (!$this->config->get('access-delete-thread'))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category),
				JText::_('COM_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Update replies if this is a parent (thread starter)
		if (!$model->parent)
		{
			if (!$model->updateReplies(array('state' => 2), $model->id))  /* 0 = unpublished, 1 = published, 2 = deleted */
			{
				$this->setError($model->getError());
			}
		}

		// Delete the topic itself
		$model->state = 2;  /* 0 = unpublished, 1 = published, 2 = deleted */
		if (!$model->store()) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category),
				$model->getError(),
				'error'
			);
			return;
		}

		// Redirect to main listing
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category),
			JText::_('COM_FORUM_THREAD_DELETED'),
			'message'
		);
	}
	
	/**
	 * Serves up files only after passing access checks
	 *
	 * @return	void
	 */
	public function downloadTask()
	{
		// Incoming
		$section = JRequest::getVar('section', '');
		$category = JRequest::getVar('category', '');
		$thread = JRequest::getInt('thread', 0);
		$post = JRequest::getInt('post', 0);
		$file = JRequest::getVar('file', '');

		// Ensure we have a database object
		if (!$this->database) 
		{
			JError::raiseError(500, JText::_('COM_FORUM_DATABASE_NOT_FOUND'));
			return;
		}

		// Instantiate an attachment object
		$attach = new ForumAttachment($this->database);
		if (!$post)
		{
			$attach->loadByThread($thread, $file);
		}
		else 
		{
			$attach->loadByPost($post);
		}
		
		if (!$attach->filename) 
		{
			JError::raiseError(404, JText::_('COM_FORUM_FILE_NOT_FOUND'));
			return;
		}
		$file = $attach->filename;

		// Get the parent ticket the file is attached to
		$row = new ForumPost($this->database);
		$row->load($attach->post_id);

		if (!$row->id) 
		{
			JError::raiseError(404, JText::_('COM_FORUM_POST_NOT_FOUND'));
			return;
		}

		// Check logged in status
		if ($row->access > 0 && $this->juser->get('guest')) 
		{
			$return = base64_encode(JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section=' . $section . '&category=' . $category . '&thread=' . $thread . '&post=' . $post . '&file=' . $file));
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . $return)
			);
			return;
		}

		// Load ACL
		$this->_authorize('thread', $row->id);

		// Ensure the user is authorized to view this file
		if (!$this->config->get('access-view-thread')) 
		{
			JError::raiseError(403, JText::_('COM_FORUM_NOT_AUTH_FILE'));
			return;
		}
		
		// Ensure we have a path
		if (empty($file)) 
		{
			JError::raiseError(404, JText::_('COM_FORUM_FILE_NOT_FOUND'));
			return;
		}
		if (preg_match("/^\s*http[s]{0,1}:/i", $file)) 
		{
			JError::raiseError(404, JText::_('COM_FORUM_BAD_FILE_PATH'));
			return;
		}
		if (preg_match("/^\s*[\/]{0,1}index.php\?/i", $file)) 
		{
			JError::raiseError(404, JText::_('COM_FORUM_BAD_FILE_PATH'));
			return;
		}
		// Disallow windows drive letter
		if (preg_match("/^\s*[.]:/", $file)) 
		{
			JError::raiseError(404, JText::_('COM_FORUM_BAD_FILE_PATH'));
			return;
		}
		// Disallow \
		if (strpos('\\', $file)) 
		{
			JError::raiseError(404, JText::_('COM_FORUM_BAD_FILE_PATH'));
			return;
		}
		// Disallow ..
		if (strpos('..', $file)) 
		{
			JError::raiseError(404, JText::_('COM_FORUM_BAD_FILE_PATH'));
			return;
		}

		// Get the configured upload path
		$basePath  = DS . trim($this->config->get('webpath', '/site/forum'), DS) . DS  . $attach->parent . DS . $attach->post_id;

		// Does the path start with a slash?
		if (substr($file, 0, 1) != DS) 
		{
			$file = DS . $file;
			// Does the beginning of the $attachment->filename match the config path?
			if (substr($file, 0, strlen($basePath)) == $basePath) 
			{
				// Yes - this means the full path got saved at some point
			} 
			else 
			{
				// No - append it
				$file = $basePath . $file;
			}
		}

		// Add JPATH_ROOT
		$filename = JPATH_ROOT . $file;

		// Ensure the file exist
		if (!file_exists($filename)) 
		{
			JError::raiseError(404, JText::_('COM_FORUM_FILE_NOT_FOUND') . ' ' . $filename);
			return;
		}

		// Get some needed libraries
		ximport('Hubzero_Content_Server');

		// Initiate a new content server and serve up the file
		$xserver = new Hubzero_Content_Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve()) 
		{
			// Should only get here on error
			JError::raiseError(404, JText::_('COM_FORUM_SERVER_ERROR'));
		} 
		else 
		{
			exit;
		}
		return;
	}

	/**
	 * Uploads a file to a given directory and returns an attachment string
	 * that is appended to report/comment bodies
	 * 
	 * @param      string $listdir Directory to upload files to
	 * @return     string A string that gets appended to messages
	 */
	public function uploadTask($listdir, $post_id)
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			return;
		}

		if (!$listdir) 
		{
			$this->setError(JText::_('COM_FORUM_NO_UPLOAD_DIRECTORY'));
			return;
		}

		$row = new ForumAttachment($this->database);
		$row->load(JRequest::getInt('attachment', 0));
		$row->description = trim(JRequest::getVar('description', ''));
		$row->post_id = $post_id;
		$row->parent = $listdir;

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name']) 
		{
			if ($row->id)
			{
				if (!$row->check()) 
				{
					$this->setError($row->getError());
				}
				if (!$row->store()) 
				{
					$this->setError($row->getError());
				}
			}
			return;
		}

		// Construct our file path
		$path = JPATH_ROOT . DS . trim($this->config->get('webpath', '/site/forum'), DS) . DS . $listdir;
		if ($post_id)
		{
			$path .= DS . $post_id;
		}

		// Build the path if it doesn't exist
		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				$this->setError(JText::_('COM_FORUM_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		$ext = strtolower(JFile::getExt($file['name']));

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name'])) 
		{
			$this->setError(JText::_('COM_FORUM_ERROR_UPLOADING'));
			return;
		} 
		else 
		{
			// File was uploaded
			// Create database entry
			$row->filename = $file['name'];
			
			if (!$row->check()) 
			{
				$this->setError($row->getError());
			}
			if (!$row->store()) 
			{
				$this->setError($row->getError());
			}
		}
	}

	/**
	 * Set access permissions for a user
	 * 
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if (!$this->juser->get('guest')) 
		{
			if (version_compare(JVERSION, '1.6', 'ge'))
			{
				$asset  = $this->_option;
				if ($assetId)
				{
					$asset .= ($assetType != 'component') ? '.' . $assetType : '';
					$asset .= ($assetId) ? '.' . $assetId : '';
				}

				$at = '';
				if ($assetType != 'component')
				{
					$at .= '.' . $assetType;
				}

				// Admin
				$this->config->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $asset));
				$this->config->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $asset));
				// Permissions
				$this->config->set('access-create-' . $assetType, $this->juser->authorise('core.create' . $at, $asset));
				$this->config->set('access-delete-' . $assetType, $this->juser->authorise('core.delete' . $at, $asset));
				$this->config->set('access-edit-' . $assetType, $this->juser->authorise('core.edit' . $at, $asset));
				$this->config->set('access-edit-state-' . $assetType, $this->juser->authorise('core.edit.state' . $at, $asset));
				$this->config->set('access-edit-own-' . $assetType, $this->juser->authorise('core.edit.own' . $at, $asset));
			}
			else 
			{
				if ($assetType == 'post' || $assetType == 'thread')
				{
					$this->config->set('access-create-' . $assetType, true);
					$this->config->set('access-edit-' . $assetType, true);
					$this->config->set('access-delete-' . $assetType, true);
				}
				if ($this->juser->authorize($this->_option, 'manage'))
				{
					$this->config->set('access-manage-' . $assetType, true);
					$this->config->set('access-admin-' . $assetType, true);
					$this->config->set('access-create-' . $assetType, true);
					$this->config->set('access-delete-' . $assetType, true);
					$this->config->set('access-edit-' . $assetType, true);
				}
			}
		}
	}
}