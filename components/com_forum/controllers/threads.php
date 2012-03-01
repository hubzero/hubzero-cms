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
 * Short description for 'ForumController'
 * 
 * Long description (if any) ...
 */
class ForumControllerThreads extends Hubzero_Controller
{
	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	protected function _buildPathway()
	{
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();

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
				stripslashes($this->view->section->title), 
				'index.php?option=' . $this->_option . '&section=' . $this->view->section->alias
			);
		}
		if (isset($this->view->category))
		{
			$pathway->addItem(
				stripslashes($this->view->category->title), 
				'index.php?option=' . $this->_option . '&section=' . $this->view->section->alias . '&category=' . $this->view->category->alias
			);
		}
		if (isset($this->view->post) && $this->view->post->id)
		{
			$pathway->addItem(
				'#' . $this->view->post->id . ' - ' . stripslashes($this->view->post->title), 
				'index.php?option=' . $this->_option . '&section=' . $this->view->section->alias . '&category=' . $this->view->category->alias . '&thread=' . $this->view->post->id
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
			$this->_title .= ': ' . stripslashes($this->view->section->title);
		}
		if (isset($this->view->category))
		{
			$this->_title .= ': ' . stripslashes($this->view->category->title);
		}
		if (isset($this->view->post) && $this->view->post->id)
		{
			$this->_title .= ': #' . $this->view->post->id . ' - ' . stripslashes($this->view->post->title);
		}
		$document =& JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Short description for 'topic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function displayTask()
	{
		$this->view->title = JText::_('Discussion Forum');

		// Incoming
		$this->view->filters = array();
		//$filters['authorized'] = $this->authorized;
		$this->view->filters['limit']  = JRequest::getInt('limit', 25);
		$this->view->filters['start']  = JRequest::getInt('limitstart', 0);
		$this->view->filters['section'] = JRequest::getVar('section', '');
		$this->view->filters['category'] = JRequest::getVar('category', '');
		$this->view->filters['parent'] = JRequest::getInt('thread', 0);
		$this->view->filters['state']  = 1;

		/*if ($this->view->filters['parent'] == 0) 
		{
			$this->view->_name = 'threads';
			$this->threadsTask();
			return;
		}*/
		
		$this->view->section = new ForumSection($this->database);
		$this->view->section->loadByAlias($this->view->filters['section'], 0);

		$this->view->category = new ForumCategory($this->database);
		$this->view->category->loadByAlias($this->view->filters['category'], $this->view->section->id, 0);
		$this->view->filters['category_id'] = $this->view->category->id;

		// Initiate a forum object
		$this->view->post = new ForumPost($this->database);

		// Load the topic
		$this->view->post->load($this->view->filters['parent']);

		// Check logged in status
		if ($this->view->post->access > 0 && $this->juser->get('guest')) 
		{
			$return = base64_encode(JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&section=' . $this->view->filters['section'] . '&category=' . $this->view->filters['category'] . '&thread=' . $this->view->filters['parent']));
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . $return)
			);
			return;
		}

		// Get reply count
		$this->view->total = $this->view->post->getCount($this->view->filters);

		// Get replies
		$this->view->rows = $this->view->post->getRecords($this->view->filters);

		// Record the hit
		//$this->view->forum->hit();
		$this->view->participants = $this->view->post->getParticipants($this->view->filters);
		
		// Get attachments
		$this->view->attach = new ForumAttachment($this->database);
		$this->view->attachments = $this->view->attach->getAttachments($this->view->post->id);
		
		// Get tags on this article
		$this->view->tModel = new ForumTags($this->database);
		$this->view->tags = $this->view->tModel->get_tag_cloud(0, 0, $this->view->post->id);

		// Get authorization
		$this->_authorize('category', $this->view->category->id);
		$this->_authorize('thread', $this->view->post->id);
		$this->_authorize('post');

		$this->view->config = $this->config;
		$this->view->notifications = $this->getComponentMessage();

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		// Push CSS to the template
		$this->_getStyles();
		
		// Push scripts to the template
		$this->_getScripts();
	
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}
		
		$this->view->display();
	}

	/**
	 * Short description for 'addTopic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function newTask()
	{
		$this->view->setLayout('edit');
		$this->editTask();
	}

	/**
	 * Short description for 'editTopic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function editTask($post=null)
	{
		$id = JRequest::getInt('thread', 0);
		$category = JRequest::getVar('category', '');
		$section = JRequest::getVar('section', '');
		
		if ($this->juser->get('guest')) 
		{
			$return = JRoute::_('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category . '&task=new');
			if ($id)
			{
				$return = JRoute::_('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category . '&thread=' . $id . '&task=edit');
			}
			$this->setRedirect(JRoute::_('index.php?option=com_login&return=' . base64_encode($return)));
			return;
		}
		
		$this->view->section = new ForumSection($this->database);
		$this->view->section->loadByAlias($section, 0);

		$this->view->category = new ForumCategory($this->database);
		$this->view->category->loadByAlias($category, $this->view->section->id, 0);

		// Incoming
		if (is_object($post))
		{
			$this->view->post = $post;
		}
		else 
		{
			$this->view->post = new ForumPost($this->database);
			$this->view->post->load($id);
		}
		
		$this->_authorize('thread', $id);
		
		if (!$id) 
		{
			$this->view->post->created_by = $this->juser->get('id');
		}
		elseif ($this->view->post->created_by != $this->juser->get('id') && $this->view->authorized != 'admin') 
		{
			$this->setRedirect(JRoute::_('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category));
			return;
		}
		
		$this->view->sections = $this->view->section->getRecords(array(
			'state' => 1,
			'group' => 0
		));
		if (!$this->view->sections || count($this->view->sections) <= 0)
		{
			$this->view->sections = array();
			
			$default = new ForumSection($this->database);
			$default->id = 0;
			$default->title = JText::_('Categories');
			$default->alias = str_replace(' ', '-', $default->title);
			$default->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($default->title));
			$this->view->sections[] = $default;
		}
		
		$cModel = new ForumCategory($this->database);
		foreach ($this->view->sections as $key => $sect)
		{
			$this->view->sections[$key]->categories = $cModel->getRecords(array(
				'section_id' => $sect->id,
				'group'      => 0
			));
		}

		// Get tags on this article
		$this->view->tModel = new ForumTags($this->database);
		$this->view->tags = $this->view->tModel->get_tag_string($this->view->post->id, 0, 0, $this->view->post->created_by);

		$this->view->authorized = $this->_authorize();

		// Push CSS to the template
		$this->_getStyles();
		
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();

		$this->view->config = $this->config;
		$this->view->notifications = $this->getComponentMessage();

		// Set any errors
		if ($this->getError()) 
		{
			$this->view->setError($this->getError());
		}
		
		$this->view->display();
	}

	/**
	 * Short description for 'saveTopic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
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

		if ($fields['id'])
		{
			$old = new ForumPost($this->database);
			$old->load(intval($fields['id']));
		}

		// Bind data
		$model = new ForumPost($this->database);
		if (!$model->bind($fields)) 
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($model);
			return;
		}
		
		// Check content
		if (!$model->check()) 
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->view->setLayout('edit');
			$this->editTask($model);
			return;
		}

		// Store new content
		if (!$model->store()) 
		{
			$this->addComponentMessage($model->getError(), 'error');
			$this->editTask($model);
			return;
		}
		
		$parent = ($model->parent) ? $model->parent : $model->id;
		
		$this->uploadTask($parent, $model->id);
		
		if ($fields['id'])
		{
			if ($old->category_id != $fields['category_id'])
			{
				$model->updateReplies(array('category_id' => $fields['category_id']), $model->id);
			}
		}
		
		$category = new ForumCategory($this->database);
		$category->load(intval($model->category_id));
		
		$tags = JRequest::getVar('tags', '', 'post');
		$tagger = new ForumTags($this->database);
		$tagger->tag_object($this->juser->get('id'), $model->id, $tags, 1);
		
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
		
		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->_option . '&section=' . $section . '&category=' . $category->alias . '&thread=' . $parent . '#c' . $model->id),
			$message,
			'message'
		);
	}

	/**
	 * Short description for 'deleteTopic'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	public function deleteTask()
	{
		$section = JRequest::getVar('section', '');
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

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name']) 
		{
			return;
		}

		// Incoming
		$description = trim(JRequest::getVar('description', ''));

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
			$row = new ForumAttachment($this->database);
			$row->bind(array(
				'id' => 0,
				'parent' => $listdir,
				'post_id' => $post_id,
				'filename' => $file['name'],
				'description' => $description
			));
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
	 * Short description for '_authorize'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     string Return description (if any) ...
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