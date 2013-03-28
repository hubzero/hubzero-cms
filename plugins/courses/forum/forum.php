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

ximport('Hubzero_Plugin');

/**
 * Courses Plugin class for forum entries
 */
class plgCoursesForum extends Hubzero_Plugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @return     array
	 */
	public function &onCourseAreas()
	{
		$area = array(
			'name' => $this->_name,
			'title' => JText::_('PLG_COURSES_' . strtoupper($this->_name)),
			'default_access' => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => true
		);
		return $area;
	}

	/**
	 * Return data on a course view (this will be some form of HTML)
	 * 
	 * @param      object  $course      Current course
	 * @param      string  $option     Name of the component
	 * @param      string  $authorized User's authorization level
	 * @param      integer $limit      Number of records to pull
	 * @param      integer $limitstart Start of records to pull
	 * @param      string  $action     Action to perform
	 * @param      array   $access     What can be accessed
	 * @param      array   $areas      Active area(s)
	 * @return     array
	 */
	public function onCourse($config, $course, $offering, $action='', $areas=null)
	{
		$return = 'html';
		$active = $this->_name;
		$active_real = 'discussion';

		// The output array we're returning
		$arr = array(
			'html' => '',
			'name' => $active
		);

		//get this area details
		$this_area = $this->onCourseAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!in_array($this_area['name'], $areas)) 
			{
				//return $arr;
				$return = 'metadata';
			}
		}

		$this->config = $config;
		$this->course = $course;
		$this->offering = $offering;
		$this->database = JFactory::getDBO();
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'post.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'section.php');

		$this->section = new ForumSection($this->database);
		/*$sections = $this->section->getRecords(array(
			'state'    => 1, 
			'scope'    => 'course',
			'scope_id' => $offering->get('id')
		));
		if (!$sections || count($sections) < 1)*/
		if (!$this->section->loadByAlias($this->offering->get('alias'), $this->offering->get('id'), 'course'))
		//if (!$this->section->loadByObject($this->offering->get('id'), $this->offering->get('id'), 'course'))
		{
			// Create a default section
			$this->section->title    = $offering->get('title');
			$this->section->alias    = $offering->get('alias');
			$this->section->scope    = 'course';
			$this->section->scope_id = $offering->get('id');
			$this->section->state    = 1;
			if ($this->section->check())
			{
				$this->section->store();
			}
		}

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($return == 'html') 
		{
			//include 
			//require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'post.php');
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'category.php');
			//require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'section.php');
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'attachment.php');
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'pagination.php');
			require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'tags.php');
			//set course members plugin access level
			//$course_plugin_acl = $access[$active];

			//Create user object
			$this->juser = JFactory::getUser();

			//get the course members
			//$members = $course->get('members');

			//if set to nobody make sure cant access
			if (!$this->offering->access('view')) 
			{
				$arr['html'] = '<p class="info">' . JText::sprintf('COURSES_PLUGIN_REQUIRES_MEMBER', JText::_('PLG_COURSES_' . strtoupper($this->_name))) . '</p>';
				return $arr;
			}

			// Load the section
			//$section = new ForumSection($this->database);
			//$section->loadByAlias($this->offering->get('alias'), $this->offering->get('id'), 'course');

			// Get a category count
			// There should be a category for each unit
			$category = new ForumCategory($this->database);
			if (!$category->getCount(array('section_id' => $this->section->get('id'))))
			{
				if ($this->offering->units()->total() > 0)
				{
					foreach ($this->offering->units() as $unit)
					{
						$cat = new ForumCategory($this->database);
						$cat->section_id = $this->section->get('id');
						$cat->title = $unit->get('title');
						$cat->alias = $unit->get('alias');
						$cat->description = JText::sprintf('Discussions for %s', $unit->get('title'));
						$cat->state = 1;
						$cat->scope = 'course';
						$cat->scope_id = $this->offering->get('id');
						$cat->object_id = $unit->get('id');
						if ($cat->check())
						{
							$cat->store();
						}
					}
				}
			}

			//option and paging vars
			$this->option = 'com_courses';
			$this->name = 'courses';
			$this->limitstart = JRequest::getInt('limitstart', 0);
			$this->limit = JRequest::getInt('limit', 20);
			//$this->database = JFactory::getDBO();

			JRequest::setVar('section', $offering->get('alias'));

			$u = JRequest::getVar('unit', '');
			switch ($u)
			{
				case 'edit':
					$action = 'editsection';
				break;
				case 'delete':
					$action = 'deletesection';
				break;
				case 'new':
					$action = 'editcategory';
				break;
				default:
					if ($u)
					{
						JRequest::setVar('category', $u);
						$action = 'categories';
					}
				break;
			}

			$b = JRequest::getVar('group', '');
			switch ($b)
			{
				case 'edit':
					$action = 'editcategory';
				break;
				case 'delete':
					$action = 'deletecategory';
				break;
				case 'new':
					$action = 'editthread';
				break;
				default:
					if ($b)
					{
						JRequest::setVar('thread', $b);
						$action = 'threads';
					}
				break;
			}

			$c = JRequest::getVar('asset', '');
			switch ($c)
			{
				case 'edit':
					$action = 'editthread';
				break;
				case 'delete':
					$action = 'deletethread';
				break;
				default:
					if ($c)
					{
						JRequest::setVar('post', $c);
					}
				break;
			}

			$action = JRequest::getVar('action', $action, 'post');

			//push the stylesheet to the view
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('courses', $this->_name);
			Hubzero_Document::addPluginScript('courses', $this->_name);

			$pathway =& JFactory::getApplication()->getPathway();
			$pathway->addItem(
				JText::_('PLG_COURSES_' . strtoupper($this->_name)), 
				'index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=' . $this->_name
			);

			switch ($action)
			{
				case 'sections':       $arr['html'] .= $this->sections();       break;
				//case 'savesection':    $arr['html'] .= $this->savesection();    break;
				//case 'deletesection':  $arr['html'] .= $this->deletesection();  break;

				case 'categories':     $arr['html'] .= $this->categories();     break;
				case 'savecategory':   $arr['html'] .= $this->savecategory();   break;
				case 'newcategory':    $arr['html'] .= $this->editcategory();   break;
				case 'editcategory':   $arr['html'] .= $this->editcategory();   break;
				case 'deletecategory': $arr['html'] .= $this->deletecategory(); break;

				case 'threads':        $arr['html'] .= $this->threads();        break;
				case 'savethread':     $arr['html'] .= $this->savethread();     break;
				case 'editthread':     $arr['html'] .= $this->editthread();     break;
				case 'deletethread':   $arr['html'] .= $this->deletethread();   break;

				case 'download':       $arr['html'] .= $this->download();       break;
				case 'search':         $arr['html'] .= $this->search();         break;

				default: $arr['html'] .= $this->sections(); break;
			}
		}

		$tModel = new ForumPost($this->database);

		$arr['metadata']['count'] = $tModel->getCount(array(
			'scope'    => 'course',
			'scope_id' => $offering->get('id'),
			'state'    => 1,
			'parent'   => 0
		));

		// Return the output
		return $arr;
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      object $url  URL to redirect to
	 * @param      object $msg  Message to send
	 * @return     void
	 */
	public function onCourseAfterLecture($course, $unit, $lecture)
	{
		ximport('Hubzero_Document');
		Hubzero_Document::addPluginStylesheet('courses', $this->_name);
		Hubzero_Document::addPluginScript('courses', $this->_name);

		$database = JFactory::getDBO();
		$this->juser = JFactory::getUser();
		$this->offering = $course->offering();

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'category.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'section.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'attachment.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'post.php');
		//require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'pagination.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'tags.php');

		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => 'threads',
				'layout'  => 'lecture'
			)
		);

		$this->_authorize('category');
		$this->_authorize('thread');

		$view->course  = $course;
		$view->unit    = $unit;
		$view->lecture = $lecture;
		$view->option  = 'com_courses';
		$view->notifications = $this->getPluginMessage();
		$view->config = $this->params;

		$jconfig = JFactory::getConfig();

		// Incoming
		$view->filters = array();
		$view->filters['limit']    = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$view->filters['start']    = JRequest::getInt('limitstart', 0);
		$view->filters['section']  = JRequest::getVar('section', '');
		$view->filters['category'] = JRequest::getVar('category', '');
		//$view->filters['parent']   = 0; //JRequest::getInt('thread', 0);
		$view->filters['state']    = 1;
		$view->filters['scope']    = 'course';
		$view->filters['scope_id'] = $course->offering()->get('id');
		$view->filters['sticky'] = false;
		$view->no_html = JRequest::getInt('no_html', 0);

		$sort = JRequest::getVar('sort', 'newest');
		switch ($sort)
		{
			case 'oldest':
				$view->filters['sort_Dir'] = 'ASC';
			break;

			case 'newest':
			default:
				$view->filters['sort_Dir'] = 'DESC';
			break;
		}
		$view->filters['sort'] = 'c.created';
		$view->filters['object_id'] = $lecture->get('id');

		$view->post = new ForumPost($database);

		$view->total = 0;
		$view->rows = null;
		// Load the topic
		$view->post->loadByObject($lecture->get('id'), $view->filters['scope_id'], $view->filters['scope']);
		if (!$view->post->get('id'))
		{
			$view->post->set('title', $lecture->get('title'));
			$view->post->set('comment', ($lecture->get('description') ? $lecture->get('description') : $lecture->get('title')));
			$view->post->set('state', 1);
			$view->post->set('parent', 0);
			$view->post->set('anonymous', 1);
			$view->post->set('sticky', 1);
			$view->post->set('scope', $view->filters['scope']);
			$view->post->set('scope_id', $view->filters['scope_id']);
			$view->post->set('object_id', $lecture->get('id'));

			$section = new ForumSection($database);
			//if (!$section->loadByObject($course->offering()->get('id'), $this->offering->get('id'), 'course'))
			if (!$section->loadByAlias($course->offering()->get('alias'), $view->filters['scope_id'], $view->filters['scope']))
			{
				// Create a default section
				$section->title    = $course->offering()->get('title');
				$section->alias    = $course->offering()->get('alias');
				$section->scope    = 'course';
				$section->scope_id = $course->offering()->get('id');
				$section->state    = 1;
				if ($section->check())
				{
					$section->store();
				}
			}

			$category = new ForumCategory($database);
			$category->loadByObject($lecture->get('unit_id'), $section->get('id'), $view->filters['scope_id'], $view->filters['scope']);
			if (!$category->get('id'))
			{
				$category->section_id  = $section->get('id');
				$category->title       = $unit->get('title');
				$category->alias       = $unit->get('alias');
				$category->description = JText::sprintf('Discussions for %s', $unit->get('title'));
				$category->state       = 1;
				$category->scope       = 'course';
				$category->scope_id    = $course->offering()->get('id');
				$category->object_id   = $lecture->get('unit_id');
				if ($category->check())
				{
					$category->store();
				}
			}

			$view->post->set('category_id', $category->get('id'));
			if ($view->post->check())
			{
				$view->post->store();
			}
		}
		//$view->filters['parent'] = $view->post->get('id');

		// Get reply count
		$view->total = $view->post->getCount($view->filters);

		// Get replies
		$rows = $view->post->getRecords($view->filters);

		$children = array(
			0 => array()
		);

		$levellimit = ($view->filters['limit'] == 0) ? 500 : $view->filters['limit'];

		// first pass - collect children
		foreach ($rows as $v)
		{
			/*$children[0][] = $v;
			$children[$v->get('id')] = $v->children();*/
			
			//$v->set('name', '');
			$pt      = $v->parent;
			$list    = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;
		}
		// second pass - get an indent list of the items
		//$list = $this->treeRecurse(0, '', array(), $children, max(0, $levellimit-1));
		if (isset($children[$view->post->get('id')]))
		{
			$view->rows = $this->treeRecurse($children[$view->post->get('id')], $children);
		}

//$view->rows = null;
		//$view->total = count($list);

		//$view->rows = array_slice($list, $view->filters['start'], $view->filters['limit']);
		/*if ($view->rows && count($view->rows) > 0)
		{
			$filters = $view->filters['parent'];
			$filters['limit'] = 0;
			foreach ($view->rows as $k => $row)
			{
				$filters['parent'] = $row->id;
				$view->rows[$k]->replies = $this->getRecords($filters);
				if ($view->rows[$k]->replies && count($view->rows[$k]->replies) > 0)
				{
					foreach ($view->rows[$k]->replies as $j => $reply)
					{
						$filters['parent'] = $reply->id;
						$view->rows[$k]->replies[$j]->replies = $this->getRecords($filters);
					}
				}
			}
		}*/
		//$view->filters['parent'] = $view->post->get('id');

		// Record the hit
		//$view->participants = $view->post->getParticipants($view->filters);

		// Get attachments
		$view->attach = new ForumAttachment($database);
		$view->attachments = $view->attach->getAttachments($view->post->id);

		// Get tags on this article
		//$view->tModel = new ForumTags($this->database);
		//$view->tags = $view->tModel->get_tag_cloud(0, 0, $view->post->id);

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination(
			($view->total - 1), // subtract one for the thread starter
			$view->filters['start'], 
			$view->filters['limit']
		);

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		if ($view->no_html == 1)
		{
			ob_clean();
			echo $view->loadTemplate();
			exit();
		}

		return $view->loadTemplate();
	}

	/**
	 * Recursive function to build tree
	 * 
	 * @param      integer $id       Parent ID
	 * @param      string  $indent   Indent text
	 * @param      array   $list     List of records
	 * @param      array   $children Container for parent/children mapping
	 * @param      integer $maxlevel Maximum levels to descend
	 * @param      integer $level    Indention level
	 * @param      integer $type     Indention type
	 * @return     void
	 */
	public function treeRecurse($children, $list, $maxlevel=9999, $level=0)
	{
		if ($level <= $maxlevel)
		{
			foreach ($children as $v => $child)
			{
				if (isset($list[$child->id]))
				{
					$children[$v]->replies = $this->treeRecurse($list[$child->id], $list, $maxlevel, $level+1);
				}
			}
		}
		return $children;
	}

	/**
	 * Set redirect and message
	 * 
	 * @param      string $url  URL to redirect to
	 * @param      string $msg  Message to send
	 * @param      string $type Message type (message, error, warning, info)
	 * @return     void
	 */
	public function setRedirect($url, $msg=null, $type='message')
	{
		if ($msg !== null)
		{
			$this->addPluginMessage($msg, $type);
		}
		$this->redirect($url);
	}
	
	/**
	 * Set permissions
	 * 
	 * @param      string  $assetType Type of asset to set permissions for (component, section, category, thread, post)
	 * @param      integer $assetId   Specific object to check permissions for
	 * @return     void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->params->set('access-view', true);
		if (!$this->juser->get('guest')) 
		{
			$this->params->set('access-view-' . $assetType, false);
			if (in_array($this->juser->get('id'), $this->offering->members()))
			{
				$this->params->set('access-view-' . $assetType, true);
			}
			if (isset($this->model) && is_object($this->model))
			{
				if (!$this->model->state)
				{
					$this->params->set('access-view-' . $assetType, false);
				}
			}

			$this->params->set('access-create-' . $assetType, false);
			$this->params->set('access-delete-' . $assetType, false);
			$this->params->set('access-edit-' . $assetType, false);
			switch ($assetType)
			{
				case 'thread':
					$this->params->set('access-create-' . $assetType, true);
					if ($this->offering->access('manage'))
					{
						$this->params->set('access-delete-' . $assetType, true);
						$this->params->set('access-edit-' . $assetType, true);
						$this->params->set('access-view-' . $assetType, true);
					}
				break;
				case 'category':
					if ($this->offering->access('manage'))
					{
						$this->params->set('access-create-' . $assetType, true);
						$this->params->set('access-delete-' . $assetType, true);
						$this->params->set('access-edit-' . $assetType, true);
						$this->params->set('access-view-' . $assetType, true);
					}
				break;
				case 'section':
					if ($this->offering->access('manage'))
					{
						$this->params->set('access-create-' . $assetType, true);
						$this->params->set('access-delete-' . $assetType, true);
						$this->params->set('access-edit-' . $assetType, true);
						$this->params->set('access-view-' . $assetType, true);
					}
				break;
				case 'component':
				default:
					if ($this->offering->access('manage'))
					{
						$this->params->set('access-create-' . $assetType, true);
						$this->params->set('access-delete-' . $assetType, true);
						$this->params->set('access-edit-' . $assetType, true);
						$this->params->set('access-view-' . $assetType, true);
					}
				break;
			}
		}
	}
	
	/**
	 * Show sections in this forum
	 * 
	 * @return     string
	 */
	public function sections()
	{
		// Instantiate a vew
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => 'sections',
				'layout'  => 'display'
			)
		);

		// Incoming
		$view->filters = array();
		$view->filters['authorized'] = 1;
		$view->filters['scope']      = 'course';
		$view->filters['scope_id']   = $this->offering->get('id');
		$view->filters['search']     = JRequest::getVar('q', '');
		$view->filters['section_id'] = 0;
		$view->filters['state']      = 1;

		$view->edit = JRequest::getVar('section', '');

		// Get Sections
		//$sModel = new ForumSection($this->database);
		$view->sections = $this->section->getRecords(array(
			'state'    => $view->filters['state'],
			'scope'    => $view->filters['scope'], 
			'scope_id' => $view->filters['scope_id']
		));

		$model = new ForumCategory($this->database);

		// Check if there are uncategorized posts
		// This should mean legacy data
		/*if (($posts = $model->getPostCount(0, $this->offering->get('id'))) || !$view->sections || !count($view->sections))
		{
			// Create a default section
			$dSection = new ForumSection($this->database);
			$dSection->title = JText::_('Default Section');
			$dSection->alias = str_replace(' ', '-', $dSection->title);
			$dSection->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($dSection->title));
			$dSection->scope = 'course';
			$dSection->scope_id = $this->offering->get('id');
			$dSection->state = 1;
			if ($dSection->check())
			{
				$dSection->store();
			}

			// Create a default category
			$dCategory = new ForumCategory($this->database);
			$dCategory->title = JText::_('Discussions');
			$dCategory->description = JText::_('Default category for all discussions in this forum.');
			$dCategory->alias = str_replace(' ', '-', $dCategory->title);
			$dCategory->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($dCategory->title));
			$dCategory->section_id = $dSection->id;
			$dCategory->scope = 'course';
			$dCategory->scope_id = $this->offering->get('id');
			$dCategory->state = 1;
			if ($dCategory->check())
			{
				$dCategory->store();
			}

			if ($posts)
			{
				// Update all the uncategorized posts to the new default
				$tModel = new ForumPost($this->database);
				$tModel->updateCategory(0, $dCategory->id, $this->offering->get('id'));
			}

			$view->sections = $sModel->getRecords(array(
				'state' => 1, 
				'scope' => 'course', 
				'scope_id' => $this->offering->get('id')
			));
		}*/

		$view->stats = new stdClass;
		$view->stats->categories = 0;
		$view->stats->threads = 0;
		$view->stats->posts = 0;

		foreach ($view->sections as $key => $section)
		{
			$view->filters['section_id'] = $section->id;

			$view->sections[$key]->categories = $model->getRecords($view->filters);
	
			$view->stats->categories += count($view->sections[$key]->categories);
			if ($view->sections[$key]->categories)
			{
				foreach ($view->sections[$key]->categories as $c)
				{
					$view->stats->threads += $c->threads;
					$view->stats->posts += $c->posts;
				}
			}
		}

		$post = new ForumPost($this->database);
		$view->lastpost = $post->getLastActivity($this->offering->get('id'), 'course');

		//get authorization
		$this->_authorize('section');
		$this->_authorize('category');
		$view->config = $this->params;
		$view->course = $this->course;
		$view->offering = $this->offering;
		$view->option = $this->option;
		$view->notifications = $this->getPluginMessage();

		// email settings data
		/*include_once(JPATH_ROOT . DS . 'plugins' . DS . 'groups' . DS . 'memberoptions' . DS . 'memberoption.class.php');
		$user = & JFactory::getUser();

		$database = & JFactory::getDBO();
		$recvEmailOption = new XGroups_MemberOption($database);
		$recvEmailOption->loadRecord($this->course->offering()->get('id'), $user->id, GROUPS_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION);

		if ($recvEmailOption->id) 
		{
			$view->recvEmailOptionID = $recvEmailOption->id;
			$view->recvEmailOptionValue = $recvEmailOption->optionvalue;
		} 
		else 
		{
			$view->recvEmailOptionID = 0;
			$view->recvEmailOptionValue = 0;
		}*/

		// Set any errors
		if ($this->getError()) 
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}

	/**
	 * Saves a section and redirects to main page afterward
	 * 
	 * @return     void
	 */
	/*public function savesection()
	{
		// Incoming posted data
		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Instantiate a new table row and bind the incoming data
		$model = new ForumSection($this->database);
		if (!$model->bind($fields))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum')
			);
			return;
		}

		// Check content
		if ($model->check()) 
		{
			// Store new content
			$model->store();
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum')
		);
	}*/

	/**
	 * Deletes a section and redirects to main page afterwards
	 * 
	 * @return     void
	 */
	/*public function deletesection()
	{
		// Is the user logged in?
		if ($this->juser->get('guest')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum'))),
				JText::_('PLG_GROUPS_FORUM_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$alias = JRequest::getVar('section', '');

		// Load the section
		$model = new ForumSection($this->database);
		$model->loadByAlias($alias, $this->offering->get('id'), 'course');

		// Make the sure the section exist
		if (!$model->id) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum'),
				JText::_('PLG_GROUPS_FORUM_MISSING_ID'),
				'error'
			);
			return;
		}

		// Check if user is authorized to delete entries
		$this->_authorize('section', $model->id);
		if (!$this->params->get('access-delete-section')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum'),
				JText::_('PLG_GROUPS_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Get all the categories in this section
		$cModel = new ForumCategory($this->database);
		$categories = $cModel->getRecords(array(
			'section_id' => $model->id,
			'scope'      => 'course', 
			'scope_id'   => $this->offering->get('id')
		));
		if ($categories)
		{
			// Build an array of category IDs
			$cats = array();
			foreach ($categories as $category)
			{
				$cats[] = $category->id;
			}

			// Set all the threads/posts in all the categories to "deleted"
			$tModel = new ForumPost($this->database);
			if (!$tModel->setStateByCategory($cats, 2))  // 0 = unpublished, 1 = published, 2 = deleted 
			{
				$this->setError($tModel->getError());
			}

			// Set all the categories to "deleted"
			if (!$cModel->setStateBySection($model->id, 2))  // 0 = unpublished, 1 = published, 2 = deleted 
			{
				$this->setError($cModel->getError());
			}
		}

		// Set the section to "deleted"
		$model->state = 2;  // 0 = unpublished, 1 = published, 2 = deleted 
		if (!$model->store()) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum'),
				$model->getError(),
				'error'
			);
			return;
		}

		// Redirect to main listing
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum'),
			JText::_('PLG_GROUPS_FORUM_SECTION_DELETED'),
			'passed'
		);
	}*/

	/**
	 * Short description for 'topics'
	 * 
	 * @return     string
	 */
	public function categories()
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => 'categories',
				'layout'  => 'display'
			)
		);

		$jconfig = JFactory::getConfig();

		// Incoming
		$view->filters = array();
		$view->filters['authorized'] = 1;
		$view->filters['limit']    = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$view->filters['start']    = JRequest::getInt('limitstart', 0);
		$view->filters['section']  = JRequest::getVar('section', '');
		$view->filters['category'] = JRequest::getVar('category', '');
		$view->filters['search']   = JRequest::getVar('q', '');
		$view->filters['scope']    = 'course';
		$view->filters['scope_id'] = $this->offering->get('id');
		$view->filters['state']    = 1;
		$view->filters['parent']   = 0;
		//$view->filters['sticky'] = false;
		$view->filters['sort_Dir'] = 'ASC';
		
		$view->section = new ForumSection($this->database);
		$view->section->loadByAlias($view->filters['section'], $this->offering->get('id'), 'course');
		$view->filters['section_id'] = $view->section->id;

		$view->category = new ForumCategory($this->database);
		$view->category->loadByAlias($view->filters['category'], $view->section->id, $this->offering->get('id'), 'course');
		$view->filters['category_id'] = $view->category->id;

		if (!$view->category->id)
		{
			$view->category->title = JText::_('Discussions');
			$view->category->alias = str_replace(' ', '-', $view->category->title);
			$view->category->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($view->category->title));
		}

		// Initiate a forum object
		$view->forum = new ForumPost($this->database);

		// Get record count
		$view->total = $view->forum->getCount($view->filters);

		// Get records
		$view->rows = $view->forum->getRecords($view->filters);

		//get authorization
		$this->_authorize('category');
		$this->_authorize('thread');

		$view->config = $this->params;
		$view->course = $this->course;
		$view->offering = $this->offering;
		$view->option = $this->option;
		$view->notifications = $this->getPluginMessage();

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination(
			$view->total, 
			$view->filters['start'], 
			$view->filters['limit']
		);

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		return $view->loadTemplate();
	}

	/**
	 * Search forum entries and display results
	 * 
	 * @return     string
	 */
	public function search()
	{
		ximport('Hubzero_Plugin_View');
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => 'categories',
				'layout'  => 'search'
			)
		);

		$jconfig = JFactory::getConfig();

		// Incoming
		$this->view->filters = array();
		$this->view->filters['authorized'] = 1;
		$this->view->filters['limit']    = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$this->view->filters['start']    = JRequest::getInt('limitstart', 0);
		$this->view->filters['search']   = JRequest::getVar('q', '');
		$this->view->filters['scope']    = 'course';
		$this->view->filters['scope_id'] = $this->offering->get('id');

		$this->view->section = new ForumSection($this->database);
		$this->view->section->title = JText::_('Posts');
		$this->view->section->alias = str_replace(' ', '-', $this->view->section->title);
		$this->view->section->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($this->view->section->title));

		// Get all sections
		$sections = $this->view->section->getRecords(array(
			'state'    => 1, 
			'scope'    => $this->view->filters['scope'],
			'scope_id' => $this->view->filters['scope_id']
		));
		$s = array();
		foreach ($sections as $section)
		{
			$s[$section->id] = $section;
		}
		$this->view->sections = $s;

		$this->view->category = new ForumCategory($this->database);
		$this->view->category->title = JText::_('Search');
		$this->view->category->alias = str_replace(' ', '-', $this->view->category->title);
		$this->view->category->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($this->view->category->title));

		// Get all categories
		$categories = $this->view->category->getRecords(array(
			'state'    => 1, 
			'scope'    => $this->view->filters['scope'],
			'scope_id' => $this->view->filters['scope_id']
		));
		$c = array();
		foreach ($categories as $category)
		{
			$c[$category->id] = $category;
		}
		$this->view->categories = $c;

		// Initiate a forum object
		$this->view->forum = new ForumPost($this->database);

		// Get record count
		$this->view->total = $this->view->forum->getCount($this->view->filters);

		// Get records
		$this->view->rows = $this->view->forum->getRecords($this->view->filters);

		//get authorization
		$this->_authorize('category');
		$this->_authorize('thread');

		$this->view->config = $this->params;
		$this->view->course = $this->course;
		$this->view->offering = $this->offering;
		$this->view->option = $this->option;

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$this->view->total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		$this->view->notifications = $this->getPluginMessage();

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		return $this->view->loadTemplate();
	}

	/**
	 * Show a form for editing a category
	 * 
	 * @return     string
	 */
	public function editcategory($model=null)
	{
		ximport('Hubzero_Plugin_View');
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => 'categories',
				'layout'  => 'edit'
			)
		);

		$category = JRequest::getVar('category', '');
		$section = JRequest::getVar('section', '');
		if ($this->juser->get('guest')) 
		{
			$return = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum');
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($return))
			);
			return;
		}

		$sModel = new ForumSection($this->database);
		$sModel->loadByAlias($section, $this->offering->get('id'), 'course');

		// Incoming
		if (is_object($model))
		{
			$this->view->model = $model;
		}
		else 
		{
			$this->view->model = new ForumCategory($this->database);
			$this->view->model->loadByAlias($category, $sModel->id, $this->offering->get('id'), 'course');
		}

		$this->_authorize('category', $this->view->model->id);

		if (!$this->view->model->id) 
		{
			$this->view->model->created_by = $this->juser->get('id');
			$this->view->model->section_id = ($this->view->model->section_id) ? $this->view->model->section_id : $sModel->id;
		}
		elseif ($this->view->model->created_by != $this->juser->get('id') && !$this->params->get('access-create-category')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum')
			);
			return;
		}

		$this->view->section = $sModel;
		/*$this->view->sections = $sModel->getRecords(array(
			'state' => 1,
			'scope_id' => $this->course->get('id'),
			'scope' => 'course'
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
		}*/

		$this->view->notifications = $this->getPluginMessage();
		$this->view->config = $this->params;
		$this->view->course = $this->course;
		$this->view->offering = $this->offering;
		$this->view->option = $this->option;

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		return $this->view->loadTemplate();
	}

	/**
	 * Save a category
	 * 
	 * @return     void
	 */
	public function savecategory()
	{
		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		$model = new ForumCategory($this->database);
		if (!$model->bind($fields))
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editcategory($model);
		}

		$this->_authorize('category', $model->id);
		if (!$this->params->get('access-edit-category'))
		{
			// Set the redirect
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum')
			);
		}
		$model->closed = (isset($fields['closed']) && $fields['closed']) ? 1 : 0;
		// Check content
		if (!$model->check()) 
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editcategory($model);
		}

		// Store new content
		if (!$model->store()) 
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editcategory($model);
		}

		// Set the redirect
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum')
		);
	}

	/**
	 * Delete a category
	 * 
	 * @return     void
	 */
	public function deletecategory()
	{
		// Is the user logged in?
		if ($this->juser->get('guest')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum'))),
				JText::_('PLG_COURSES_FORUM_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$category = JRequest::getVar('category', '');
		if (!$category) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum'),
				JText::_('PLG_COURSES_FORUM_MISSING_ID'),
				'error'
			);
			return;
		}
		
		$section = JRequest::getVar('section', '');
		$sModel = new ForumSection($this->database);
		$sModel->loadByAlias($section, $this->offering->get('id'), 'course');

		// Initiate a forum object
		$model = new ForumCategory($this->database);
		$model->loadByAlias($category, $sModel->id, $this->offering->get('id'), 'course');

		// Check if user is authorized to delete entries
		$this->_authorize('category', $model->id);
		if (!$this->params->get('access-delete-category')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum'),
				JText::_('PLG_COURSES_FORUM_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Set all the threads/posts in all the categories to "deleted"
		$tModel = new ForumPost($this->database);
		if (!$tModel->setStateByCategory($model->id, 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
		{
			$this->setError($tModel->getError());
		}

		// Set the category to "deleted"
		$model->state = 2;  /* 0 = unpublished, 1 = published, 2 = deleted */
		if (!$model->store()) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum'),
				$model->getError(),
				'error'
			);
			return;
		}

		// Redirect to main listing
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum'),
			JText::_('PLG_COURSES_FORUM_CATEGORY_DELETED'),
			'passed'
		);
	}

	/**
	 * Show a thread
	 * 
	 * @return     string
	 */
	public function threads()
	{
		ximport('Hubzero_Plugin_View');
		$view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => 'threads',
				'layout'  => 'display'
			)
		);

		$jconfig = JFactory::getConfig();

		// Incoming
		$view->filters = array();
		$view->filters['limit']    = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$view->filters['start']    = JRequest::getInt('limitstart', 0);
		$view->filters['section']  = $this->offering->get('alias'); //JRequest::getVar('section', '');
		$view->filters['category'] = JRequest::getVar('category', '');
		//$view->filters['parent']   = JRequest::getInt('thread', 0);
		$view->filters['state']    = 1;
		$view->filters['scope']    = 'course';
		$view->filters['scope_id'] = $this->offering->get('id');

		$thread   = JRequest::getInt('thread', 0);

		
		//$view->filters['object_id'] = $lecture->get('id');

		/*if ($view->filters['parent'] == 0) 
		{
			return $this->categories();
		}*/

		$view->section = new ForumSection($this->database);
		$view->section->loadByAlias($view->filters['section'], $this->offering->get('id'), 'course');
		$view->filters['section_id'] = $view->section->id;

		$view->category = new ForumCategory($this->database);
		$view->category->loadByAlias($view->filters['category'], $view->section->id, $this->offering->get('id'), 'course');
		$view->filters['category_id'] = $view->category->id;

		if (!$view->category->id)
		{
			$view->category->title = JText::_('Discussions');
			$view->category->alias = 'discussions';
		}

		// Initiate a forum object
		$view->post = new ForumPost($this->database);

		// Load the topic
		$view->post->load($thread);
		//$view->post->loadByObject($lecture->get('id'), $view->filters['scope_id'], $view->filters['scope']);
		$view->filters['object_id'] = $view->post->object_id;

		$view->unit = $this->offering->unit($view->filters['category']);
		$view->lecture = $view->unit->assetgroup($view->filters['object_id']);

		// Get reply count
		$view->total = $view->post->getCount($view->filters);

		// Get replies
		//$view->rows = $view->post->getRecords($view->filters);
		// Get replies
		//$view->filters['parent'] = 0;
		$rows = $view->post->getRecords($view->filters);

		$children = array(
			0 => array()
		);

		$levellimit = ($view->filters['limit'] == 0) ? 500 : $view->filters['limit'];

		// first pass - collect children
		foreach ($rows as $v)
		{
			/*$children[0][] = $v;
			$children[$v->get('id')] = $v->children();*/
			
			//$v->set('name', '');
			$pt      = $v->parent;
			$list    = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;
		}
		
		// second pass - get an indent list of the items
		//$list = $this->treeRecurse(0, '', array(), $children, max(0, $levellimit-1));
		$view->rows = array();
		if (isset($children[$view->post->get('id')]))
		{
			$view->rows = $this->treeRecurse($children[$view->post->get('id')], $children);
		}
		/*if (!$view->rows)
		{
			$view->rows = array();
		}*/
		if (isset($children[0]) && !$children[0][0]->object_id)
		{
			array_unshift($view->rows, $children[0][0]);
		}
		

		$view->filters['parent']   = $view->post->id;

		// Record the hit
		$view->participants = $view->post->getParticipants($view->filters);
		
		// Get attachments
		$view->attach = new ForumAttachment($this->database);
		$view->attachments = $view->attach->getAttachments($view->post->id);
		
		// Get tags on this article
		$view->tModel = new ForumTags($this->database);
		$view->tags = $view->tModel->get_tag_cloud(0, 0, $view->post->id);

		// Get authorization
		$this->_authorize('category', $view->category->id);
		$this->_authorize('thread', $view->post->id);

		$view->config = $this->params;
		$view->course = $this->course;
		$view->offering = $this->offering;
		$view->option = $this->option;
		$view->notifications = $this->getPluginMessage();

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination(
			$view->total, 
			$view->filters['start'], 
			$view->filters['limit']
		);

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		return $view->loadTemplate();
	}

	/**
	 * Show a form for editing a post
	 * 
	 * @return     string
	 */
	public function editthread($post=null)
	{
		ximport('Hubzero_Plugin_View');
		$this->view = new Hubzero_Plugin_View(
			array(
				'folder'  => 'courses',
				'element' => $this->_name,
				'name'    => 'threads',
				'layout'  => 'edit'
			)
		);

		$id = JRequest::getInt('thread', 0);
		$category = JRequest::getVar('category', '');
		$sectionAlias = JRequest::getVar('section', '');

		if ($this->juser->get('guest')) 
		{
			$return = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum&unit=' . $section . '&b=' . $category . '&c=new');
			if ($id)
			{
				$return = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum&unit=' . $section . '&b=' . $category . '&c=' . $id . '/edit');
			}
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($return))
			);
			return;
		}

		$this->view->category = new ForumCategory($this->database);
		$this->view->category->loadByAlias($category);

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

		// Get authorization
		$this->_authorize('thread', $id);

		if (!$id) 
		{
			$this->view->post->created_by = $this->juser->get('id');
		}
		elseif ($this->view->post->created_by != $this->juser->get('id') && !$this->params->get('access-edit-thread')) 
		{
			$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum&unit=' . $section . '&b=' . $category));
			return;
		}

		$sModel = new ForumSection($this->database);
		$this->view->sections = $sModel->getRecords(array(
			'state'    => 1, 
			'scope'    => 'course',
			'scope_id' => $this->offering->get('id')
		));

		if (!$this->view->sections || count($this->view->sections) <= 0)
		{
			$this->view->sections = array();

			$default = new stdClass;
			$default->id = 0;
			$default->title = JText::_('Categories');
			$default->alias = str_replace(' ', '-', $default->title);
			$default->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($default->title));
			$this->view->sections[] = $default;
		}

		$cModel = new ForumCategory($this->database);
		foreach ($this->view->sections as $key => $section)
		{
			$this->view->sections[$key]->categories = $cModel->getRecords(array(
				'section_id' => $section->id,
				'scope'      => 'course',
				'scope_id'   => $this->offering->get('id'),
				'state'      => 1
			));
		}

		// Get tags on this article
		$this->view->tModel = new ForumTags($this->database);
		$this->view->tags = $this->view->tModel->get_tag_string($this->view->post->id, 0, 0, $this->view->post->created_by);

		$this->view->option = $this->option;
		$this->view->config = $this->params;
		$this->view->course = $this->course;
		$this->view->offering = $this->offering;
		$this->view->section = $sectionAlias;
		$this->view->notifications = $this->getPluginMessage();

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		return $this->view->loadTemplate();
	}
	
	/**
	 * Saves posted data for a new/edited forum thread post
	 * 
	 * @return     void
	 */
	public function savethread()
	{
		if ($this->juser->get('guest')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum')))
			);
			return;
		}

		// Incoming
		$section = JRequest::getVar('section', '');

		$no_html = JRequest::getInt('no_html', 0);

		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		$this->_authorize('thread', intval($fields['id']));
		$asset = 'thread';
		/*if ($fields['parent'])
		{
			$asset = 'post';
		}*/
		if (($fields['id'] && !$this->params->get('access-edit-thread')) 
		 || (!$fields['id'] && !$this->params->get('access-create-thread')))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum'),
				JText::_('You are not authorized to perform this action.'),
				'warning'
			);
			return;
		}

		if ($fields['id'])
		{
			$old = new ForumPost($this->database);
			$old->load(intval($fields['id']));
		}

		// Bind data
		/* @var $model ForumPost */
		$model = new ForumPost($this->database);
		if (!$model->bind($fields)) 
		{
			/*if ($no_html)
			{
				ob_clean();
				echo $model->getError();
				exit;
			}*/
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editthread($model);
		}

		// Check content
		if (!$model->check()) 
		{
			/*if ($no_html)
			{
				ob_clean();
				echo $model->getError();
				exit;
			}*/
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editthread($model);
		}

		// Store new content
		if (!$model->store()) 
		{
			/*if ($no_html)
			{
				ob_clean();
				echo $model->getError();
				exit;
			}*/
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editthread($model);
		}

		$parent = ($model->parent) ? $model->parent : $model->id;

		$this->upload($parent, $model->id);

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

		// Determine post save message 
		// Also, get subject of post for outgoing email, either the title of parent post (for replies), or title of current post (for new threads)
		if (!$fields['id'])
		{
			if (!$fields['parent'])
			{
				$message = JText::_('PLG_COURSES_FORUM_THREAD_STARTED');
				$posttitle = $model->title;
			}
			else 
			{
				$message = JText::_('PLG_COURSES_FORUM_POST_ADDED');
				
				/* @var $parentForumPost ForumPost */
				$parentForumPost = new ForumPost($this->database);
				$parentForumPost->load(intval($fields['parent']));
				$posttitle = $parentForumPost->title;
			}
		}
		else 
		{
			$message = ($model->modified_by) ? JText::_('PLG_COURSES_FORUM_POST_EDITED') : JText::_('PLG_COURSES_FORUM_POST_ADDED');
		}

		// Determine route
		if ($model->parent) 
		{
			$thread = $model->parent;
		} 
		else 
		{
			$thread = $model->id;
		}

		// Build outgoing email message
		$juser =& JFactory::getUser();

		$params =& JComponentHelper::getParams('com_courses');

		// Email the course and insert email tokens to allow them to respond to course posts via email
		if ($params->get('email_comment_processing'))
		{
			$prependtext = "~!~!~!~!~!~!~!~!~!~!\r\n";
			$prependtext .= "You can reply to this message, but be sure to include your reply text above this area.\r\n\r\n" ;
			$prependtext .= $juser->name . " (". $juser->username . ") wrote:";
			$forum_message = $prependtext . "\r\n\r\n" . $model->comment;

			// Translate the message wiki formatting to html
			/*
			ximport('Hubzero_Wiki_Parser');

			$p =& Hubzero_Wiki_Parser::getInstance();

			$wikiconfig = array(
				'option'   => $this->option,
				'scope'    => 'course' . DS . 'forum',
				'pagename' => 'course',
				'pageid'   => $this->course->get('id'),
				'filepath' => '',
				'domain'   => ''
			);

			$forum_message = $p->parse("\n".stripslashes($forum_message), $wikiconfig);		
			*/

			ximport('Hubzero_Emailtoken');
			// Figure out who should be notified about this comment (all course members for now)
			$userIDsToEmail = array();

			foreach ($this->members as $mbr)
			{
				//Look up user info 
				$user = new JUser();

				if ($user->load($mbr))
				{
					include_once(JPATH_ROOT . DS . 'plugins' . DS . 'courses' . DS . 'memberoptions' . DS . 'memberoption.class.php');

					// Find the user's course settings, do they want to get email (0 or 1)?
					$courseMemberOption = new Courses_MemberOption($this->database);
					$courseMemberOption->loadRecord(
						$this->course->get('id'), 
						$user->id, 
						COURSES_MEMBEROPTION_TYPE_DISCUSSION_NOTIFICIATION
					);

					$sendEmail = 0;
					if ($courseMemberOption->id)
					{
						$sendEmail = $courseMemberOption->optionvalue;
					}

					if ($sendEmail)
					{
						$userIDsToEmail[] = $user->id;
					}
				}
			}

			JPluginHelper::importPlugin('xmessage');
			$dispatcher =& JDispatcher::getInstance();

			// Email each course member separately, each needs a user specific token
			foreach ($userIDsToEmail as $userID)
			{
				ximport('Hubzero_Emailtoken');
				$encryptor = new Hubzero_Email_Token();
				$jconfig =& JFactory::getConfig();

				// Construct User specific Email ThreadToken
				// Version, type, userid, xforumid
				$token = $encryptor->buildEmailToken(1, 2, $userID, $parent);

				$subject = ' - ' . $this->course->get('alias') . ' - ' . $posttitle;

				$from = array();
				$from['name']  = $jconfig->getValue('config.sitename') . ' ';
				$from['email'] = $jconfig->getValue('config.mailfrom');
				$from['replytoemail'] = 'hgm-' . $token;

				if (!$dispatcher->trigger('onSendMessage', array('course_message', $subject, $forum_message, $from, array($userID), $this->option, null, '', $this->course->get('id')))) 
				{
					$this->setError(JText::_('COM_COURSES_ERROR_EMAIL_MEMBERS_FAILED'));
				}
			}
		}

		if ($no_html == 1)
		{
			$unit = $this->course->offering()->unit($category->alias);
			return $this->onCourseAfterLecture($this->course, $unit, $unit->assetgroup($model->object_id));
		}

		$rtrn = base64_decode(JRequest::getVar('return', '', 'post'));
		if (!$rtrn)
		{
			$rtrn = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias') . '&active=forum&unit=' . $category->alias . '&b=' . $thread . '#c' . $model->id);
		}

		// Set the redirect
		$this->setRedirect(
			$rtrn,
			$message,
			'passed'
		);
	}

	/**
	 * Remove a thread
	 * 
	 * @return     void
	 */
	public function deletethread()
	{
		$section = JRequest::getVar('section', '');
		$category = JRequest::getVar('category', '');

		// Is the user logged in?
		if ($this->juser->get('guest')) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum&unit=' . $section . '&b=' . $category),
				JText::_('PLG_COURSES_FORUM_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$id = JRequest::getInt('thread', 0);

		// Initiate a forum object
		$model = new ForumPost($this->database);
		$model->load($id);

		// Make the sure the category exist
		if (!$model->id) 
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum&unit=' . $section . '&b=' . $category),
				JText::_('PLG_COURSES_FORUM_MISSING_ID'),
				'error'
			);
			return;
		}

		// Check if user is authorized to delete entries
		$this->_authorize('thread', $id);
		if (!$this->params->get('access-delete-thread'))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum&unit=' . $section . '&b=' . $category),
				JText::_('PLG_COURSES_FORUM_NOT_AUTHORIZED'),
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
				JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum&unit=' . $section . '&b=' . $category),
				$forum->getError(),
				'error'
			);
			return;
		}

		// Redirect to main listing
		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum&unit=' . $section . '&b=' . $category),
			JText::_('PLG_COURSES_FORUM_THREAD_DELETED'),
			'passed'
		);
	}

	/**
	 * Uploads a file to a given directory and returns an attachment string
	 * that is appended to report/comment bodies
	 * 
	 * @param      string $listdir Directory to upload files to
	 * @return     string A string that gets appended to messages
	 */
	public function upload($listdir, $post_id)
	{
		// Check if they are logged in
		if ($this->juser->get('guest')) 
		{
			return;
		}

		if (!$listdir) 
		{
			$this->setError(JText::_('PLG_COURSES_FORUM_NO_UPLOAD_DIRECTORY'));
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
		$path = JPATH_ROOT . DS . trim($this->params->get('filepath', '/site/forum'), DS) . DS . $listdir;
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
				$this->setError(JText::_('PLG_COURSES_FORUM_UNABLE_TO_CREATE_UPLOAD_PATH'));
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
			$this->setError(JText::_('PLG_COURSES_FORUM_ERROR_UPLOADING'));
			return;
		} 
		else 
		{
			// File was uploaded
			// Create database entry
			$row = new ForumAttachment($this->database);
			$row->bind(array(
				'id'          => 0,
				'parent'      => $listdir,
				'post_id'     => $post_id,
				'filename'    => $file['name'],
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
	 * Serves up files only after passing access checks
	 *
	 * @return	void
	 */
	public function download()
	{
		// Incoming
		$section = JRequest::getVar('section', '');
		$category = JRequest::getVar('category', '');
		$thread = JRequest::getInt('thread', 0);
		$post = JRequest::getInt('post', 0);
		$file = JRequest::getVar('file', '');

		// Check logged in status
		if ($this->juser->get('guest')) 
		{
			$return = JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . '&active=forum&unit=' . $section . '&b=' . $category . '&c=' . $thread . '/' . $post . '/' . $file);
			$this->setRedirect(
				JRoute::_('index.php?option=com_login&return=' . base64_encode($return))
			);
			return;
		}

		// Ensure we have a database object
		if (!$this->database) 
		{
			JError::raiseError(500, JText::_('PLG_COURSES_FORUM_DATABASE_NOT_FOUND'));
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
			JError::raiseError(404, JText::_('PLG_COURSES_FORUM_FILE_NOT_FOUND'));
			return;
		}
		$file = $attach->filename;

		// Get the parent ticket the file is attached to
		$this->model = new ForumPost($this->database);
		$this->model->load($attach->post_id);

		if (!$this->model->id) 
		{
			JError::raiseError(404, JText::_('PLG_COURSES_FORUM_POST_NOT_FOUND'));
			return;
		}

		// Load ACL
		$this->_authorize('thread', $this->model->id);

		// Ensure the user is authorized to view this file
		if (!$this->params->get('access-view-thread')) 
		{
			JError::raiseError(403, JText::_('PLG_COURSES_FORUM_NOT_AUTH_FILE'));
			return;
		}

		// Ensure we have a path
		if (empty($file)) 
		{
			JError::raiseError(404, JText::_('PLG_COURSES_FORUM_FILE_NOT_FOUND'));
			return;
		}
		if (preg_match("/^\s*http[s]{0,1}:/i", $file)) 
		{
			JError::raiseError(404, JText::_('PLG_COURSES_FORUM_BAD_FILE_PATH'));
			return;
		}
		if (preg_match("/^\s*[\/]{0,1}index.php\?/i", $file)) 
		{
			JError::raiseError(404, JText::_('PLG_COURSES_FORUM_BAD_FILE_PATH'));
			return;
		}
		// Disallow windows drive letter
		if (preg_match("/^\s*[.]:/", $file)) 
		{
			JError::raiseError(404, JText::_('PLG_COURSES_FORUM_BAD_FILE_PATH'));
			return;
		}
		// Disallow \
		if (strpos('\\', $file)) 
		{
			JError::raiseError(404, JText::_('PLG_COURSES_FORUM_BAD_FILE_PATH'));
			return;
		}
		// Disallow ..
		if (strpos('..', $file)) 
		{
			JError::raiseError(404, JText::_('PLG_COURSES_FORUM_BAD_FILE_PATH'));
			return;
		}

		// Get the configured upload path
		$basePath  = DS . trim($this->params->get('filepath', '/site/forum'), DS) . DS  . $attach->parent . DS . $attach->post_id;

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
			JError::raiseError(404, JText::_('PLG_COURSES_FORUM_FILE_NOT_FOUND'));
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
			JError::raiseError(404, JText::_('PLG_COURSES_FORUM_SERVER_ERROR'));
		} 
		else 
		{
			exit;
		}
		return;
	}

	/**
	 * Remove all items associated with the gorup being deleted
	 * 
	 * @param      object $course Course being deleted
	 * @return     string Log of items removed
	 */
	public function onCourseDelete($course)
	{
		$log = JText::_('PLG_COURSES_FORUM') . ': ';

		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'post.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'category.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'section.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'attachment.php');

		$this->database = JFactory::getDBO();

		$sModel = new ForumSection($this->database);
		$sections = $sModel->getRecords(array(
			'scope'    => 'course',
			'scope_id' => $course->offering()->get('id')
		));

		// Do we have any IDs?
		if (count($sections) > 0) 
		{
			// Loop through each ID
			foreach ($sections as $section) 
			{
				// Get the categories in this section
				$cModel = new ForumCategory($this->database);
				$categories = $cModel->getRecords(array(
					'section_id' => $section->id,
					'scope'      => 'course',
					'scope_id'   => $course->offering()->get('id')
				));

				if ($categories)
				{
					// Build an array of category IDs
					$cats = array();
					foreach ($categories as $category)
					{
						$cats[] = $category->id;
					}

					// Set all the threads/posts in all the categories to "deleted"
					$tModel = new ForumPost($this->database);
					if (!$tModel->setStateByCategory($cats, 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
					{
						$this->setError($tModel->getError());
					}
					$log .= 'forum.section.' . $section->id . '.category.' . $category->id . '.post' . "\n";

					// Set all the categories to "deleted"
					if (!$cModel->setStateBySection($model->id, 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
					{
						$this->setError($cModel->getError());
					}
					$log .= 'forum.section.' . $section->id . '.category.' . $category->id . "\n";
				}

				// Set the section to "deleted"
				$sModel->load($section->id);
				$sModel->state = 2;  /* 0 = unpublished, 1 = published, 2 = deleted */
				if (!$sModel->store()) 
				{
					$this->setError($sModel->getError());
					return '';
				}
				$log .= 'forum.section.' . $section->id . ' ' . "\n";
			}
		}
		else 
		{
			$log .= JText::_('PLG_COURSES_FORUM_NO_RESULTS')."\n";
		}

		return $log;
	}

	/**
	 * Get a count of all items that will be deleted with this gorup
	 * 
	 * @param      object $course Course to be deleted
	 * @return     void
	 */
	public function onCourseDeleteCount($course)
	{
	}
}
