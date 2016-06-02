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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

require_once(PATH_CORE . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'manager.php');

/**
 * Courses Plugin class for forum entries
 */
class plgCoursesDiscussions extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return  array
	 */
	public function onCourseAreas()
	{
		$area = array(
			'name' => $this->_name,
			'title' => Lang::txt('PLG_COURSES_' . strtoupper($this->_name)),
			'default_access' => $this->params->get('plugin_access', 'members'),
			'display_menu_tab' => true,
			'icon' => 'f086'
		);
		return $area;
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return  array
	 */
	public function onSectionEdit()
	{
		return $this->onCourseAreas();
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return  array
	 */
	public function onAssetgroupEdit()
	{
		return $this->onCourseAreas();
	}

	/**
	 * Update any category associated with the assetgroup
	 *
	 * @param   object  $model  \Components\Courses\Models\Assetgroup
	 * @return  mixed
	 */
	public function onAssetgroupSave($assetgroup)
	{
		if (!$assetgroup->exists())
		{
			return;
		}

		if (!$assetgroup->params('discussions_category'))
		{
			return;
		}

		require_once(PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'unit.php');

		// Load the parent unit
		$unit = \Components\Courses\Models\Unit::getInstance($assetgroup->get('unit_id'));
		$db = App::get('db');

		// Attempt to load the category
		$category = new \Components\Forum\Tables\Category($db);
		$category->loadByObject($assetgroup->get('id'), null, $unit->get('offering_id'), 'course');

		// Is there a category already?
		if (!$category->id)
		{
			// No category
			// Is there a parent section?
			$section = new \Components\Forum\Tables\Section($db);
			$section->loadByObject($unit->get('id'), $unit->get('offering_id'), 'course');
			if (!$section->id)
			{
				// No parent section
				// Create it!
				$section->title     = $unit->get('title');
				$section->alias     = $unit->get('alias');
				$section->state     = $unit->get('state');
				$section->scope     = 'course';
				$section->scope_id  = $unit->get('offering_id');
				$section->object_id = $unit->get('id');
				$section->ordering  = $unit->get('ordering');
				if ($section->check())
				{
					$section->store();
				}
			}
			// Assign the section ID
			$category->section_id = $section->id;
		}

		// Don't change "Deleted" items
		if ($category->state == 2)
		{
			return $category->id;
		}

		// Assign asset group data to category to keep them in sync
		$category->state = $assetgroup->get('state');
		if ($assetgroup->get('title') == '--')
		{
			$ag = ($assetgroup->assets() ? $assetgroup->assets()->fetch('first') : null);
			if ($ag)
			{
				$category->title = $ag->get('title');
			}
		}
		else
		{
			$category->title = $assetgroup->get('title');
		}
		$category->scope     = 'course';
		$category->scope_id  = $unit->get('offering_id');
		$category->object_id = $assetgroup->get('id');
		$category->title     = ($category->title ? $category->title : $assetgroup->get('title'));
		$category->alias     = $assetgroup->get('alias');
		if (!$category->id)
		{
			$category->description = Lang::txt('Discussions for %s', $category->title);
		}
		$category->ordering  = $assetgroup->get('ordering');
		if (!$category->check())
		{
			$category->store();
		}


		return $category->id;
	}

	/**
	 * Actions to perform after deleting an assetgroup
	 *
	 * @param   object  $model  \Components\Courses\Models\Assetgroup
	 * @return  void
	 */
	public function onAssetgroupDelete($assetgroup)
	{
		if (!$assetgroup->exists())
		{
			return;
		}

		require_once(PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'unit.php');

		$db   = App::get('db');
		$unit = \Components\Courses\Models\Unit::getInstance($assetgroup->get('unit_id'));

		// Attempt to load an associated category
		$category = new \Components\Forum\Tables\Category($db);
		$category->loadByObject($assetgroup->get('id'), null, $unit->get('offering_id'), 'course');

		// Was a category found?
		if ($category->id && $category->state != 2)
		{
			// Mark as deleted
			$category->state = 2;
			if ($category->check())
			{
				$category->store();
			}

			// Mark all threads in category as deleted
			$thread = new \Components\Forum\Tables\Post($db);
			$thread->setStateByCategory($category->get('id'), 2);
		}

		// Bit of recursion here for nested asset groups
		if ($assetgroup->children(null, true)->total() > 0)
		{
			foreach ($assetgroup->children() as $child)
			{
				$this->onAssetgroupDelete($child);
			}
		}
	}

	/**
	 * Update any section associated with the unit
	 *
	 * @param   object  $model  \Components\Courses\Models\Unit
	 * @return  mixed
	 */
	public function onUnitSave($unit)
	{
		if (!$unit->exists())
		{
			return;
		}

		$db      = App::get('db');
		$section = new \Components\Forum\Tables\Section($db);
		$section->loadByObject($unit->get('id'), $unit->get('offering_id'), 'course');
		if ($section->id && $section->state != 2)
		{
			$section->state    = $unit->get('state');
			$section->title    = $unit->get('title');
			$section->alias    = $unit->get('alias');
			$section->ordering = $unit->get('ordering');
			if ($section->check())
			{
				$section->store();
			}
		}
		return $section->id;
	}

	/**
	 * Actions to perform after deleting a unit
	 *
	 * @param   object  $model  \Components\Courses\Models\Unit
	 * @return  void
	 */
	public function onUnitDelete($unit)
	{
		if (!$unit->exists())
		{
			return;
		}

		$db      = App::get('db');
		$section = new \Components\Forum\Tables\Section($db);
		$section->loadByAlias($unit->get('alias'), $unit->get('offering_id'), 'course');
		if ($section->id)
		{
			$section->state = 2;
			if ($section->check())
			{
				$section->store();
			}

			$categories = $section->getRecords(array('section_id' => $section->id));
			if ($categories)
			{
				$ids = array();
				foreach ($categories as $category)
				{
					$ids[] = $category->id;
					$cat   = new ForumTableCategory($db);
					$cat->load($category->id);
					$cat->setStateBySection($section->id, 2);
				}

				$thread = new \Components\Forum\Tables\Post($db);
				$thread->setStateByCategory($ids, 2);
			}
		}
	}

	/**
	 * Return data on a course view (this will be some form of HTML)
	 *
	 * @param   object   $course    Current course
	 * @param   object   $offering  Name of the component
	 * @param   boolean  $describe  Return plugin description only?
	 * @return  object
	 */
	public function onCourse($course, $offering, $describe=false)
	{
		$response = with(new \Hubzero\Base\Object)
			->set('name', $this->_name)
			->set('title', Lang::txt('PLG_COURSES_' . strtoupper($this->_name)))
			->set('description', JText::_('PLG_COURSES_' . strtoupper($this->_name) . '_BLURB'))
			->set('default_access', $this->params->get('plugin_access', 'members'))
			->set('display_menu_tab', true)
			->set('icon', 'f086');

		if ($describe)
		{
			return $response;
		}

		if (!($active = Request::getVar('active')))
		{
			Request::setVar('active', ($active = $this->_name));
		}

		$this->config   = $course->config();
		$this->course   = $course;
		$this->offering = $offering;
		$this->database = App::get('db');

		$this->params->merge(new \Hubzero\Config\Registry($offering->section()->get('params')));

		// Determine if we need to return any HTML (meaning this is the active plugin)
		if ($response->get('name') == $active)
		{
			$this->_active = $this->_name;

			$this->section = new \Components\Forum\Tables\Section($this->database);
			$this->sections = $this->section->getRecords(array(
				'state'    => 1,
				'scope'    => 'course',
				'scope_id' => $this->offering->get('id'),
				'sort_Dir' => 'DESC',
				'sort'     => 'ordering ASC, created ASC, title'
			));

			//option and paging vars
			$this->option     = 'com_courses';
			$this->name       = 'courses';
			$this->limitstart = Request::getInt('limitstart', 0);
			$this->limit      = Request::getInt('limit', 500);

			$action = '';

			$u = strtolower(Request::getWord('unit', ''));
			if ($u == 'manage')
			{
				$action = 'sections';

				$b = Request::getVar('group', '');
				if ($b)
				{
					Request::setVar('section', $b);
				}

				$c = Request::getVar('asset', '');
				switch ($c)
				{
					case 'orderdown':
						$action = 'orderdown';
					break;
					case 'orderup':
						$action = 'orderup';
					break;
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
						if ($c)
						{
							Request::setVar('category', $c);
							$action = 'editcategory';
						}
						$d = Request::getVar('d', '');
						switch ($d)
						{
							case 'edit':
								$action = 'editcategory';
							break;
							case 'delete':
								$action = 'deletecategory';
							break;
							default:
								//$d = Request::setVar('thread', $c);
								//$action = 'threads';
							break;
						}
					break;
				}
			}

			if (Request::getVar('file', ''))
			{
				$action = 'download';
			}

			$action = Request::getVar('action', $action, 'post');
			if (!$action)
			{
				$action = Request::getVar('action', $action, 'get');
			}
			if ($action == 'edit' && Request::getInt('post', 0))
			{
				$action = 'editthread';
			}

			//push the stylesheet to the view
			$this->css();

			$this->base = $this->offering->link() . '&active=' . $this->_name;

			Pathway::append(
				Lang::txt('PLG_COURSES_' . strtoupper($this->_name)),
				$this->base
			);

			switch ($action)
			{
				case 'sections':       $response->set('html', $this->sections());       break;
				case 'newsection':     $response->set('html', $this->sections());       break;
				case 'editsection':    $response->set('html', $this->sections());       break;
				case 'savesection':    $response->set('html', $this->savesection());    break;
				case 'deletesection':  $response->set('html', $this->deletesection());  break;

				case 'categories':     $response->set('html', $this->categories());     break;
				case 'savecategory':   $response->set('html', $this->savecategory());   break;
				case 'newcategory':    $response->set('html', $this->editcategory());   break;
				case 'editcategory':   $response->set('html', $this->editcategory());   break;
				case 'deletecategory': $response->set('html', $this->deletecategory()); break;

				case 'threads':        $response->set('html', $this->threads());        break;
				case 'savethread':     $response->set('html', $this->savethread());     break;
				case 'editthread':     $response->set('html', $this->editthread());     break;
				case 'deletethread':   $response->set('html', $this->deletethread());   break;

				case 'orderup':        $response->set('html', $this->orderup());        break;
				case 'orderdown':      $response->set('html', $this->orderdown());      break;

				case 'download':       $response->set('html', $this->download());       break;
				case 'search':         $response->set('html', $this->panel());          break;

				default: $response->set('html', $this->panel()); break;
			}
		}

		$tModel = new \Components\Forum\Tables\Post($this->database);

		$response->set('meta_count', $tModel->getCount(array(
			'scope'    => 'course',
			'scope_id' => $offering->get('id'),
			'state'    => array(1, 3),
			'parent'   => 0,
			'scope_sub_id' => ($this->params->get('discussions_threads', 'all') != 'all' ? $course->offering()->section()->get('id') : null)
		)));

		// Return the output
		return $response;
	}

	/**
	 * Set redirect and message
	 *
	 * @param   object  $url  URL to redirect to
	 * @param   object  $msg  Message to send
	 * @return  void
	 */
	public function onCourseAfterLecture($course, $unit, $lecture)
	{
		if (!$course->offering()->section()->access('view'))
		{
			$view = new \Hubzero\Plugin\View(array(
				'folder'  => 'courses',
				'element' => 'outline',
				'name'    => 'shared',
				'layout'  => '_not_enrolled'
			));

			$view->set('course', $course)
			     ->set('option', 'com_courses')
			     ->set('message', 'You must be enrolled to utilize the discussion feature.');

			return $view->loadTemplate();
		}

		// Are discussions turned on?
		if (!$lecture->params('discussions_category'))
		{
			return '';
		}

		$this->params->merge(new \Hubzero\Config\Registry($course->offering()->section()->get('params')));

		$this->_active = 'outline';

		$this->database = App::get('db');
		$this->offering = $course->offering();

		$this->base = $this->offering->link() . '&active=' . $this->_active;

		$this->_authorize('category');
		$this->_authorize('thread');

		$view = $this->view('lecture', 'threads');
		$view->course  = $this->course = $course;
		$view->unit    = $this->unit = $unit;
		$view->lecture = $this->lecture = $lecture;
		$view->option  = $this->option = 'com_courses';
		$view->config  = $this->params;

		// Incoming
		$view->filters = array();
		$view->filters['limit']    = Request::getInt('limit', 500);
		$view->filters['start']    = Request::getInt('limitstart', 0);
		$view->filters['section']  = Request::getVar('section', '');
		$view->filters['category'] = Request::getVar('category', '');
		$view->filters['state']    = 1;
		$view->filters['scope']    = 'course';
		$view->filters['scope_id'] = $course->offering()->get('id');
		if ($this->params->get('discussions_threads', 'all') != 'all')
		{
			$view->filters['scope_sub_id'] = $course->offering()->section()->get('id');
		}
		$view->filters['sticky'] = false;
		//$view->filters['start_id'] = Request::getInt('start_id', 0);
		$view->filters['search']   = Request::getVar('search', '');

		$view->no_html = Request::getInt('no_html', 0);

		$view->filters['sort_Dir'] = 'DESC';
		$view->filters['sort'] = 'c.created';
		$view->filters['object_id'] = $lecture->get('id');

		$view->post  = new \Components\Forum\Tables\Post($this->database);
		$view->total = 0;
		$view->rows  = null;

		// Load the section
		$section = new \Components\Forum\Tables\Section($this->database);
		if (!$section->loadByAlias($unit->get('alias'), $view->filters['scope_id'], $view->filters['scope']))
		{
			// Create a default section
			$section->title     = $unit->get('title');
			$section->alias     = $unit->get('alias');
			$section->scope     = $view->filters['scope'];
			$section->scope_id  = $view->filters['scope_id'];
			$section->object_id = $unit->get('id');
			$section->state     = 1;
			if ($section->check())
			{
				$section->store();
			}
		}

		$category = new \Components\Forum\Tables\Category($this->database);
		$category->loadByObject($lecture->get('id'), $section->get('id'), $view->filters['scope_id'], $view->filters['scope']);
		if (!$category->get('id'))
		{
			$category->section_id  = $section->get('id');
			if ($lecture->get('title') == '--')
			{
				$category->title  = $lecture->assets()->fetch('first')->get('title');
			}
			else
			{
				$category->title   = $lecture->get('title');
			}
			$category->alias       = $lecture->get('alias');
			$category->description = Lang::txt('Discussions for %s', $unit->get('alias'));
			$category->state       = 1;
			$category->scope       = $view->filters['scope'];
			$category->scope_id    = $view->filters['scope_id'];
			$category->object_id   = $lecture->get('id');
			$category->ordering    = $lecture->get('ordering');
			if ($category->check())
			{
				$category->store();
			}
		}

		$view->post->scope        = $view->filters['scope'];
		$view->post->scope_id     = $view->filters['scope_id'];
		$view->post->scope_sub_id = $course->offering()->section()->get('id');
		$view->post->category_id  = $category->get('id');
		$view->post->object_id    = $lecture->get('id');
		$view->post->parent       = 0;

		// Get attachments
		$view->attach = new \Components\Forum\Tables\Attachment($this->database);
		$view->attachments = $view->attach->getAttachments($view->post->id);

		$view->filters['state'] = array(1, 3);

		$view->thread = Request::getInt('thread', 0);
		// No thread?
		if (!$view->thread)
		{
			// Try being more specific
			$view->thread = Request::getInt('thread', 0, 'get');
		}
		$action = strtolower(Request::getWord('action', ''));

		if ($view->no_html == 1)
		{
			$data = new stdClass();
			$data->success = true;

			$data->threads = new stdClass;
			$data->threads->lastchange = '0000-00-00 00:00:00';
			$data->threads->lastid = 0;
			$data->threads->total = 0;
			$data->threads->posts = null;
			$data->threads->html = null;

			$data->thread  = new stdClass;
			$data->thread->lastchange = '0000-00-00 00:00:00';
			$data->thread->lastid = 0;
			$data->thread->posts = null;
			$data->thread->total = 0;
			$data->thread->html = null;

			if ($view->thread)
			{
				$view->post->load($view->thread);
			}

			if (!$action && $view->thread)
			{
				$action = 'both';
			}

			switch ($action)
			{
				case 'posts':
					$view->filters['parent'] = $view->post->id;
					$view->filters['start_at'] = Request::getVar('start_at', '');

					$data->thread = $this->_posts($view->post, $view->filters);
				break;

				case 'delete':
					if ($pid = Request::getInt('post', 0))
					{
						$this->deletethread($pid, false);
					}
					$data->thread = $this->_thread($view->post, $view->filters);
				break;

				case 'thread':
					$data->thread = $this->_thread($view->post, $view->filters);
				break;

				case 'search':
					$view->filters['search'] = Request::getVar('search', '');

					$data->threads = $this->_threadsSearch($view->post, $view->filters);
				break;

				case 'sticky':
					$view->post->sticky = Request::getInt('sticky', 0);
					$view->post->store();
				break;

				case 'both':
				default:
					$view->filters['start_at'] = Request::getVar('start_at', '');

					$data->thread = $this->_thread($view->post, $view->filters);

					$view->filters['start_at'] = Request::getVar('threads_start', '');

					$data->threads = $this->_threads($view->post, $view->filters);
				break;

				case 'threads':
				default:
					$view->filters['parent'] = $view->post->id;
					$view->filters['start_at'] = Request::getVar('threads_start', '');

					$data->threads = $this->_threads($view->post, $view->filters);
				break;
			}

			if ($this->getError())
			{
				$data->success = false;
				$data->errors = $this->getErrors();
			}

			ob_clean();
			header('Content-type: text/plain');
			echo json_encode($data);
			exit();
		}

		switch ($action)
		{
			case 'search':
				$view->filters['search'] = Request::getVar('search', '');
				$data = $this->_threadsSearch($view->post, $view->filters);
				$view->threads = $data->posts;
			break;

			default:
				if ($action == 'delete')
				{
					if ($pid = Request::getInt('post', 0))
					{
						$this->deletethread($pid, false);
					}
				}

				$view->filters['parent'] = 0;

				$view->threads = $view->post->find($view->filters);
			break;
		}

		$view->data = null;
		if ($view->thread)
		{
			$view->post->load($view->thread);
			$view->data = $this->_thread($view->post, $view->filters);
		}

		$view->notifications = $this->getPluginMessage();

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Get a list of instructors for this course
	 *
	 * @return  void
	 */
	protected function _instructors()
	{
		if (!isset($this->_instructors) || !is_array($this->_instructors))
		{
			$this->_instructors = array();

			$inst = $this->course->instructors();
			if (count($inst) > 0)
			{
				foreach ($inst as $i)
				{
					$this->_instructors[] = $i->get('user_id');
				}
			}
		}

		return $this->_instructors;
	}

	/**
	 * Get an entire thread
	 *
	 * @param   object  $post     \Components\Forum\Tables\Post
	 * @param   array   $filters  Filters to apply
	 * @return  void
	 */
	protected function _thread($post, $filters=array())
	{
		$thread = new stdClass;
		$thread->lastchange = '0000-00-00 00:00:00';
		$thread->lastid = $post->id;
		$thread->posts = null;
		$thread->total = 0;
		$thread->html = null;

		$view = $this->view('list', 'threads');
		$view->comments = null;

		if ($rows = $post->getTree($post->id)) //getTree
		{
			$thread->total = count($rows);

			$children = array(
				0 => array()
			);

			$levellimit = ($filters['limit'] == 0) ? 500 : $filters['limit'];

			foreach ($rows as $v)
			{
				$pt      = $v->parent;
				$list    = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;

				$thread->lastchange = ($v->created > $thread->lastchange) ? $v->created : $thread->lastchange;
				//$lastid     = ($v->id > $lastid)          ? $v->id      : $lastid;
			}
			$total = count($rows);

			if (!isset($children[$post->get('parent')]))
			{
				$children[$post->get('parent')] = array();
			}

			$view->comments = $this->treeRecurse($children[$post->get('parent')], $children);
		}

		$view->parent = $post->parent;
		$view->thread = $post->id;
		$view->option = $this->option;
		$view->config      = $this->params;
		$view->depth      = 0;
		$view->cls        = 'odd';
		$view->base       = $this->base . '&thread=' . $post->id . ($filters['search'] ? '&action=search&search=' . $filters['search'] : '');

		$view->unit       = '';
		$view->lecture    = '';
		if ($this->_active == 'outline')
		{
			$view->unit       = $this->unit->get('alias');
			$view->lecture    = $this->lecture->get('alias');
		}

		$view->attach     = new \Components\Forum\Tables\Attachment($this->database);
		$view->course     = $this->course;
		$view->search     = $filters['search'];
		$view->post       = $post;
		$view->thread     = (!$post->parent ? $post->id : $post->parent);

		$thread->html = $view->loadTemplate();

		return $thread;
	}

	/**
	 * Get a filtered list of threads
	 *
	 * @param   object  $post     \Components\Forum\Tables\Post
	 * @param   array   $filters  Filters to apply
	 * @return  void
	 */
	protected function _threadsSearch($post, $filters=array())
	{
		$threads = new stdClass;
		$threads->lastchange = '0000-00-00 00:00:00';
		$threads->lastid = 0;
		$threads->total = 0;
		$threads->posts = null;
		$threads->html = null;

		// If we have a search term
		if (isset($filters['search']) && $filters['search'])
		{
			// Find all posts with that terms
			$ids = array();

			if ($results = $post->find($filters))
			{
				foreach ($results as $result)
				{
					$ids[] = $result->thread;
				}
			}
			// A collection of thread IDs
			$filters['id'] = $ids;

			$srch = $filters['search'];

			// Set the search filter to null
			// This needs to be done so thread starters aren't filtered from the list of threads
			// containing matching search terms.
			$filters['search'] = null;
			$filters['parent'] = $post->get('id');

			$cview = $this->view('_threads', 'threads');
			$cview->category    = 'categorysearch';
			$cview->option      = $this->option;
			$cview->threads     = (isset($filters['id']) && count($filters['id']) > 0) ? $post->find($filters) : null;
			$cview->config      = $this->params;
			$cview->cls         = 'odd';
			$cview->search      = $srch; // Pass the search term along so it can be highlighted in text
			$cview->base        = $this->base;
			$cview->unit        = '';
			$cview->lecture     = '';
			if ($this->_active == 'outline')
			{
				$cview->unit    = $this->unit->get('alias');
				$cview->lecture = $this->lecture->get('alias');
			}

			$cview->course      = $this->course;
			$cview->instructors = $this->_instructors();

			$threads->posts = $cview->threads;
			$threads->total = count($cview->threads);
			$threads->html = $cview->loadTemplate();
		}

		return $threads;
	}

	/**
	 * Get a filtered list of threads
	 *
	 * @param   object  $post     \Components\Forum\Tables\Post
	 * @param   array   $filters  Filters to apply
	 * @return  void
	 */
	protected function _threads($post, $filters=array())
	{
		$threads = new stdClass;
		$threads->lastchange = '0000-00-00 00:00:00';
		$threads->lastid = 0;
		$threads->posts  = null;
		$threads->html   = null;
		$threads->total  = 0;

		$filters['parent'] = 0;
		$filters['sort'] = 'created';
		$filters['sort_Dir'] = 'ASC'; // Needs to be reverse order that items are prepended with AJAX

		if ($results = $post->find($filters))
		{
			foreach ($results as $key => $row)
			{
				$threads->lastid = $row->id > $threads->lastid
								 ? $row->id
								 : $threads->lastid;
				$threads->lastchange = ($row->created > $threads->lastchange)
									 ? $row->created
									 : $threads->lastchange;

				$cview = $this->view('_thread', 'threads');
				$cview->option      = $this->option;
				$cview->thread      = $row;
				$cview->unit        = '';
				$cview->lecture     = '';
				if ($this->_active == 'outline')
				{
					$cview->unit    = $this->unit->get('alias');
					$cview->lecture = $this->lecture->get('alias');
				}
				$cview->cls         = 'odd';
				$cview->base        = $this->base;
				$cview->search      = '';
				$cview->course      = $this->course;
				$cview->instructors = $this->_instructors();

				$results[$key]->mine = ($row->created_by == User::get('id')) ? true : false;
				$results[$key]->html = $cview->loadTemplate();
			}
			$threads->total = count($results);
			$threads->posts = $results;
		}

		return $threads;
	}

	/**
	 * Get a filtered list of posts for a thread
	 *
	 * @param   object  $post     \Components\Forum\Tables\Post
	 * @param   array   $filters  Filters to apply
	 * @return  void
	 */
	protected function _posts($post, $filters=array())
	{
		$thread = new stdClass;
		$thread->lastchange = '0000-00-00 00:00:00';
		$thread->lastid = 0;
		$thread->posts  = null;
		$thread->html   = null;
		$thread->total  = 0;

		if ($results = $post->getTree($post->id, $filters))
		{
			foreach ($results as $key => $row)
			{
				$thread->lastchange = ($row->created > $thread->lastchange)
									? $row->created
									: $thread->lastchange;

				$results[$key]->replies = null;

				$cview = $this->view('comment', 'threads');
				$cview->option     = $this->option;
				$cview->comment    = $row;
				$cview->post       = $post;

				$cview->unit       = '';
				$cview->lecture    = '';
				if ($this->_active == 'outline')
				{
					$cview->unit       = $this->unit->get('alias');
					$cview->lecture    = $this->lecture->get('alias');
				}

				$cview->config     = $this->params;
				$cview->depth      = Request::getInt('depth', 1, 'post');
				$cview->cls        = 'odd';
				$cview->base       = $this->base;
				$cview->attach     = new \Components\Forum\Tables\Attachment($this->database);
				$cview->course     = $this->course;
				$cview->search     = '';

				$results[$key]->html = $cview->loadTemplate();
			}
			$thread->total = count($results);
			$thread->posts = $results;
		}

		return $thread;
	}

	/**
	 * Recursive function to build tree
	 *
	 * @param   integer  $id        Parent ID
	 * @param   string   $indent    Indent text
	 * @param   array    $list      List of records
	 * @param   array    $children  Container for parent/children mapping
	 * @param   integer  $maxlevel  Maximum levels to descend
	 * @param   integer  $level     Indention level
	 * @param   integer  $type      Indention type
	 * @return  void
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
	 * Set permissions
	 *
	 * @param   string   $assetType  Type of asset to set permissions for (component, section, category, thread, post)
	 * @param   integer  $assetId    Specific object to check permissions for
	 * @return  void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->params->set('access-view', true);
		if (!User::isGuest())
		{
			$this->offering->members();

			$this->params->set('access-view-' . $assetType, true);

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
	 * @return  string
	 */
	public function panel()
	{
		// Instantiate a vew
		$view = $this->view('display', 'panel');

		// Incoming
		$view->filters = array();
		$view->filters['authorized'] = 1;
		$view->filters['scope']      = 'course';
		$view->filters['scope_id']   = $this->offering->get('id');
		if ($this->params->get('discussions_threads', 'all') != 'all')
		{
			$view->filters['scope_sub_id'] = $this->offering->section()->get('id');
		}
		$view->filters['search']     = Request::getVar('search', '');
		$view->filters['section_id'] = 0;
		$view->filters['state']      = 1;
		$view->filters['limit']      = Request::getInt('limit', 500);
		$view->filters['start']      = Request::getInt('limitstart', 0);

		$view->no_html = Request::getInt('no_html', 0);
		$view->thread  = Request::getInt('thread', 0);
		// No thread?
		if (!$view->thread)
		{
			// Try being more specific
			$view->thread = Request::getInt('thread', 0, 'get');
		}
		$action = strtolower(Request::getWord('action', ''));

		//get authorization
		$this->_authorize('section');
		$this->_authorize('category');
		$this->_authorize('thread');

		$view->filters['state'] = array(1, 3);

		if ($view->no_html == 1)
		{
			$view->filters['sticky'] = false;
			$view->filters['sort_Dir'] = 'DESC';
			$view->filters['sort'] = 'c.created';
			//$view->filters['object_id'] = 0;

			$view->post = new \Components\Forum\Tables\Post($this->database);

			$data = new stdClass();
			$data->success = true;

			$data->threads = new stdClass;
			$data->threads->lastchange = '0000-00-00 00:00:00';
			$data->threads->lastid = 0;
			$data->threads->total = 0;
			$data->threads->posts = null;
			$data->threads->html = null;

			$data->thread  = new stdClass;
			$data->thread->lastchange = '0000-00-00 00:00:00';
			$data->thread->lastid = 0;
			$data->thread->posts = null;
			$data->thread->total = 0;
			$data->thread->html = null;

			if ($view->thread)
			{
				$view->post->load($view->thread);
			}

			if (!$action && $view->thread)
			{
				$action = 'both';
			}

			switch ($action)
			{
				case 'posts':
					$view->filters['parent'] = $view->post->id;
					$view->filters['start_at'] = Request::getVar('start_at', '');

					$data->thread = $this->_posts($view->post, $view->filters);
				break;

				case 'delete':
					if ($pid = Request::getInt('post', 0))
					{
						$this->deletethread($pid, false);
					}
					$data->thread = $this->_thread($view->post, $view->filters);
				break;

				case 'thread':
					$data->thread = $this->_thread($view->post, $view->filters);
				break;

				case 'search':
					$view->filters['search'] = Request::getVar('search', '');

					$data->threads = $this->_threadsSearch($view->post, $view->filters);
				break;

				case 'sticky':
					$view->post->sticky = Request::getInt('sticky', 0);
					$view->post->store();
				break;

				case 'both':
				default:
					$view->filters['start_at'] = Request::getVar('start_at', '');

					$data->thread = $this->_thread($view->post, $view->filters);

					$view->filters['start_at'] = Request::getVar('threads_start', '');

					$data->threads = $this->_threads($view->post, $view->filters);
				break;

				case 'threads':
				default:
					$view->filters['parent'] = $view->post->id;
					$view->filters['start_at'] = Request::getVar('threads_start', '');

					$data->threads = $this->_threads($view->post, $view->filters);
				break;
			}

			if ($this->getError())
			{
				$data->success = false;
				$data->errors = $this->getErrors();
			}

			ob_clean();
			header('Content-type: text/plain');
			echo json_encode($data);
			exit();
		}

		$view->filters['state'] = 1;

		// Get Sections
		if (!isset($this->sections))
		{
			$view->sections = $this->section->getRecords(array(
				'state'    => $view->filters['state'],
				'scope'    => $view->filters['scope'],
				'scope_id' => $view->filters['scope_id'],
				'sort_Dir' => 'DESC',
				'sort'     => 'ordering ASC, created ASC, title'
			));
		}
		else
		{
			$view->sections = $this->sections;
		}

		$model = new \Components\Forum\Tables\Category($this->database);

		$view->stats = new stdClass;
		$view->stats->categories = 0;
		$view->stats->threads = 0;
		$view->stats->posts = 0;

		// Collect all categories
		$view->filters['section_id'] = -1;
		$categories = array();
		$view->filters['sort_Dir'] = 'DESC';
		$view->filters['sort']     = 'ordering ASC, created ASC, title';
		$results = $model->getRecords($view->filters);
		if ($results)
		{
			foreach ($results as $category)
			{
				if (!isset($categories[$category->section_id]))
				{
					$categories[$category->section_id] = array();
				}
				$categories[$category->section_id][] = $category;
			}
		}

		// Loop through all sections and distribute categories
		foreach ($view->sections as $key => $section)
		{
			$view->filters['section_id'] = $section->id;

			$view->sections[$key]->threads = 0;
			$view->sections[$key]->categories = isset($categories[$section->id]) ? $categories[$section->id] : array();

			if ((!$view->sections[$key]->categories || !count($view->sections[$key]->categories))
			 && $view->sections[$key]->object_id)
			{
				$view->sections[$key]->categories = array();
			}

			$view->stats->categories += count($view->sections[$key]->categories);
			if ($view->sections[$key]->categories)
			{
				foreach ($view->sections[$key]->categories as $c)
				{
					$view->sections[$key]->threads += $c->threads;
					$view->stats->threads += $c->threads;
					$view->stats->posts += $c->posts;
				}
			}
		}

		$view->filters['state'] = array(1, 3);

		$view->post = new \Components\Forum\Tables\Post($this->database);
		$view->post->scope    = $view->filters['scope'];
		$view->post->scope_id = $view->filters['scope_id'];
		$view->post->scope_sub_id = $this->offering->section()->get('id');

		$view->config = $this->params;
		$view->course = $this->course;
		$view->offering = $this->offering;
		$view->option = $this->option;
		$view->notifications = $this->getPluginMessage();

		$view->data = null;
		if ($view->thread)
		{
			$view->post->load($view->thread);
			$view->data = $this->_thread($view->post, $view->filters);
		}

		// Set any errors
		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}

	/**
	 * Display content for dashboard
	 *
	 * @param   object  $course    \Components\Courses\Models\Course
	 * @param   object  $offering  \Components\Courses\Models\Offering
	 * @return  string
	 */
	public function onCourseDashboard($course, $offering)
	{
		//$this->config = $config;
		$this->course   = $course;
		$this->offering = $offering;
		$this->database = App::get('db');

		$this->option = 'com_courses';
		$this->name = 'courses';
		$this->limitstart = Request::getInt('limitstart', 0);
		$this->limit = Request::getInt('limit', 500);

		// Instantiate a vew
		$view = $this->view('dashboard', 'threads');

		// Incoming
		$view->filters = array();
		$view->filters['authorized']   = 1;
		$view->filters['scope']        = 'course';
		$view->filters['scope_id']     = $this->offering->get('id');
		$view->filters['scope_sub_id'] = $this->offering->section()->get('id');
		$view->filters['search']       = Request::getVar('search', '');
		$view->filters['section_id']   = 0;
		$view->filters['state']        = 1;
		$view->filters['limit']        = Request::getInt('limit', 500);
		$view->filters['start']        = Request::getInt('limitstart', 0);

		$view->course = $this->course;
		$view->offering = $this->offering;
		$view->option = $this->option;
		$view->config = $this->course->config();
		$view->no_html = Request::getInt('no_html', 0);
		$view->thread = Request::getInt('thread', 0);
		$view->notifications = $this->getPluginMessage();

		$view->post = new \Components\Forum\Tables\Post($this->database);
		$view->post->scope    = $view->filters['scope'];
		$view->post->scope_id = $view->filters['scope_id'];
		$view->post->scope_sub_id = $view->filters['scope_sub_id'];

		$this->section = new \Components\Forum\Tables\Section($this->database);
		$view->sections = $this->section->getRecords(array(
			'state'    => $view->filters['state'],
			'scope'    => $view->filters['scope'],
			'scope_id' => $view->filters['scope_id']
		));

		$model = new \Components\Forum\Tables\Category($this->database);

		$view->stats = new stdClass;
		$view->stats->categories = 0;
		$view->stats->threads = 0;
		$view->stats->posts = 0;

		foreach ($view->sections as $key => $section)
		{
			$view->filters['section_id'] = $section->id;

			$view->sections[$key]->threads = 0;
			$view->sections[$key]->categories = $model->getRecords($view->filters);

			$view->stats->categories += count($view->sections[$key]->categories);
			if ($view->sections[$key]->categories)
			{
				foreach ($view->sections[$key]->categories as $c)
				{
					$view->sections[$key]->threads += $c->threads;
					$view->stats->threads += $c->threads;
					$view->stats->posts += $c->posts;
				}
			}
		}

		$view->data = null;

		// Set any errors
		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}

	/**
	 * Show sections in this forum
	 *
	 * @return     string
	 */
	public function sections()
	{
		if (!$this->course->access('manage', 'offering'))
		{
			return $this->panel();
		}

		// Instantiate a vew
		$view = $this->view('display', 'sections');

		// Incoming
		$view->filters = array();
		$view->filters['authorized'] = 1;
		$view->filters['scope']      = 'course';
		$view->filters['scope_id']   = $this->offering->get('id');
		$view->filters['search']     = Request::getVar('q', '');
		$view->filters['section_id'] = 0;
		$view->filters['state']      = 1;

		$view->edit = Request::getVar('section', '');

		// Get Sections
		$view->sections = $this->section->getRecords(array(
			'state'    => $view->filters['state'],
			'scope'    => $view->filters['scope'],
			'scope_id' => $view->filters['scope_id'],
			'sort'     => 'ordering',
			'sort_Dir' => 'ASC'
		));

		$model = new \Components\Forum\Tables\Category($this->database);

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

		$post = new \Components\Forum\Tables\Post($this->database);
		$view->lastpost = $post->getLastActivity($this->offering->get('id'), 'course');

		//get authorization
		$this->_authorize('section');
		$this->_authorize('category');
		$view->config = $this->params;
		$view->course = $this->course;
		$view->offering = $this->offering;
		$view->option = $this->option;
		$view->notifications = $this->getPluginMessage();

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
	public function savesection()
	{
		if (!$this->course->access('manage', 'offering'))
		{
			return $this->panel();
		}

		// Incoming posted data
		$fields = Request::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		// Instantiate a new table row and bind the incoming data
		$model = new \Components\Forum\Tables\Section($this->database);
		if (!$model->bind($fields))
		{
			App::redirect(
				Route::url($this->base . '&unit=manage')
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
		App::redirect(
			Route::url($this->base . '&unit=manage')
		);
	}

	/**
	 * Deletes a section and redirects to main page afterwards
	 *
	 * @return     void
	 */
	public function deletesection()
	{
		if (!$this->course->access('manage', 'offering'))
		{
			return $this->panel();
		}

		// Incoming
		$alias = Request::getVar('section', '');

		// Load the section
		$model = new \Components\Forum\Tables\Section($this->database);
		$model->loadByAlias($alias, $this->offering->get('id'), 'course');

		// Make the sure the section exist
		if (!$model->id)
		{
			App::redirect(
				Route::url($this->base . '&unit=manage'),
				Lang::txt('PLG_COURSES_DISCUSSIONS_MISSING_ID'),
				'error'
			);
			return;
		}

		// Check if user is authorized to delete entries
		$this->_authorize('section', $model->id);
		if (!$this->params->get('access-delete-section'))
		{
			App::redirect(
				Route::url($this->base . '&unit=manage'),
				Lang::txt('PLG_COURSES_DISCUSSIONS_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Get all the categories in this section
		$cModel = new \Components\Forum\Tables\Category($this->database);
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
			$tModel = new \Components\Forum\Tables\Post($this->database);
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
			App::redirect(
				Route::url($this->base . '&unit=manage'),
				$model->getError(),
				'error'
			);
			return;
		}

		// Redirect to main listing
		App::redirect(
			Route::url($this->base . '&unit=manage'),
			Lang::txt('PLG_COURSES_DISCUSSIONS_SECTION_DELETED'),
			'passed'
		);
	}

	/**
	 * Short description for 'topics'
	 *
	 * @return     string
	 */
	public function categories()
	{
		if (!$this->course->access('manage', 'offering'))
		{
			return $this->panel();
		}

		$view = $this->view('display', 'categories');

		// Incoming
		$view->filters = array();
		$view->filters['authorized'] = 1;
		$view->filters['limit']    = Request::getInt('limit', Config::get('list_limit'));
		$view->filters['start']    = Request::getInt('limitstart', 0);
		$view->filters['section']  = Request::getVar('section', '');
		$view->filters['category'] = Request::getVar('category', '');
		$view->filters['search']   = Request::getVar('q', '');
		$view->filters['scope']    = 'course';
		$view->filters['scope_id'] = $this->offering->get('id');
		$view->filters['state']    = 1;
		$view->filters['parent']   = 0;
		$view->filters['sort_Dir'] = 'ASC';

		$view->section = new \Components\Forum\Tables\Section($this->database);
		$view->section->loadByAlias($view->filters['section'], $this->offering->get('id'), 'course');
		$view->filters['section_id'] = $view->section->id;

		$view->category = new \Components\Forum\Tables\Category($this->database);
		$view->category->loadByAlias($view->filters['category'], $view->section->id, $this->offering->get('id'), 'course');
		$view->filters['category_id'] = $view->category->id;

		if (!$view->category->id)
		{
			$view->category->title = Lang::txt('Discussions');
			$view->category->alias = str_replace(' ', '-', $view->category->title);
			$view->category->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($view->category->title));
		}

		// Initiate a forum object
		$view->forum = new \Components\Forum\Tables\Post($this->database);

		// Get record count
		$view->total = $view->forum->getCount($view->filters);

		// Get records
		$view->rows = $view->forum->getRecords($view->filters);
		if ($view->rows)
		{
			foreach ($view->rows as $i => $row)
			{
				$view->rows[$i] = new \Components\Forum\Models\Post($row);
			}
		}

		//get authorization
		$this->_authorize('category');
		$this->_authorize('thread');

		$view->config = $this->params;
		$view->course = $this->course;
		$view->offering = $this->offering;
		$view->option = $this->option;
		$view->notifications = $this->getPluginMessage();

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Show a form for editing a category
	 *
	 * @param   object  $model
	 * @return  string
	 */
	public function editcategory($model=null)
	{
		if (!$this->course->access('manage', 'offering'))
		{
			return $this->panel();
		}

		$this->view = $this->view('edit', 'categories');

		$category = Request::getVar('category', '');
		$section  = Request::getVar('section', '');
		if (User::isGuest())
		{
			$return = Route::url($this->base . '&unit=manage');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		$sModel = new \Components\Forum\Tables\Section($this->database);
		$sModel->loadByAlias($section, $this->offering->get('id'), 'course');

		// Incoming
		if (is_object($model))
		{
			$this->view->model = $model;
		}
		else
		{
			$this->view->model = new \Components\Forum\Tables\Category($this->database);
			$this->view->model->loadByAlias($category, $sModel->id, $this->offering->get('id'), 'course');
		}

		$this->_authorize('category', $this->view->model->id);

		if (!$this->view->model->id)
		{
			$this->view->model->created_by = User::get('id');
			$this->view->model->section_id = ($this->view->model->section_id) ? $this->view->model->section_id : $sModel->id;
		}
		elseif ($this->view->model->created_by != User::get('id') && !$this->params->get('access-create-category'))
		{
			App::redirect(
				Route::url($this->base . '&unit=manage')
			);
			return;
		}

		$this->view->section = $sModel;
		$this->view->sections = $sModel->getRecords(array(
			'state'    => 1,
			'scope_id' => $this->offering->get('id'),
			'scope'    => 'course'
		));
		if (!$this->view->sections || count($this->view->sections) <= 0)
		{
			$this->view->sections = array();

			$default = new \Components\Forum\Tables\Section($this->database);
			$default->id = 0;
			$default->title = Lang::txt('Categories');
			$default->alias = str_replace(' ', '-', $default->title);
			$default->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($default->title));
			$this->view->sections[] = $default;
		}

		$this->view->notifications = $this->getPluginMessage();
		$this->view->config = $this->params;
		$this->view->course = $this->course;
		$this->view->offering = $this->offering;
		$this->view->option = $this->option;

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
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
		// Check for request forgeries
		Request::checkToken();

		if (!$this->course->access('manage', 'offering'))
		{
			return $this->panel();
		}

		$fields = Request::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		$model = new \Components\Forum\Tables\Category($this->database);
		if (!$model->bind($fields))
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editcategory($model);
		}

		$this->_authorize('category', $model->id);
		if (!$this->params->get('access-edit-category'))
		{
			// Set the redirect
			App::redirect(
				Route::url($this->base . '&unit=manage')
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
		App::redirect(
			Route::url($this->base . '&unit=manage')
		);
	}

	/**
	 * Delete a category
	 *
	 * @return     void
	 */
	public function deletecategory()
	{
		if (!$this->course->access('manage', 'offering'))
		{
			return $this->panel();
		}

		// Incoming
		$category = Request::getVar('category', '');
		if (!$category)
		{
			App::redirect(
				Route::url($this->base . '&unit=manage'),
				Lang::txt('PLG_COURSES_DISCUSSIONS_MISSING_ID'),
				'error'
			);
			return;
		}

		$section = Request::getVar('section', '');
		$sModel = new \Components\Forum\Tables\Section($this->database);
		$sModel->loadByAlias($section, $this->offering->get('id'), 'course');

		// Initiate a forum object
		$model = new \Components\Forum\Tables\Category($this->database);
		$model->loadByAlias($category, $sModel->id, $this->offering->get('id'), 'course');

		// Check if user is authorized to delete entries
		$this->_authorize('category', $model->id);
		if (!$this->params->get('access-delete-category'))
		{
			App::redirect(
				Route::url($this->base . '&unit=manage'),
				Lang::txt('PLG_COURSES_DISCUSSIONS_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Set all the threads/posts in all the categories to "deleted"
		$tModel = new \Components\Forum\Tables\Post($this->database);
		if (!$tModel->setStateByCategory($model->id, 2))  // 0 = unpublished, 1 = published, 2 = deleted
		{
			$this->setError($tModel->getError());
		}

		// Set the category to "deleted"
		$model->state = 2;  // 0 = unpublished, 1 = published, 2 = deleted
		if (!$model->store())
		{
			App::redirect(
				Route::url($this->base . '&unit=manage'),
				$model->getError(),
				'error'
			);
			return;
		}

		// Redirect to main listing
		App::redirect(
			Route::url($this->base . '&unit=manage'),
			Lang::txt('PLG_COURSES_DISCUSSIONS_CATEGORY_DELETED'),
			'passed'
		);
	}

	/**
	 * Show a thread
	 *
	 * @return  string
	 */
	public function threads()
	{
		if (!$this->course->access('manage', 'offering'))
		{
			return $this->panel();
		}

		$view = $this->view('display', 'threads');

		// Incoming
		$view->filters = array();
		$view->filters['limit']    = null;
		$view->filters['section']  = $this->offering->get('alias');
		$view->filters['category'] = Request::getVar('category', '');
		$view->filters['state']    = 1;
		$view->filters['scope']    = 'course';
		$view->filters['scope_id'] = $this->offering->get('id');
		$view->filters['sort_Dir'] = 'ASC';
		$view->filters['sort']     = 'c.created';

		$thread   = Request::getInt('thread', 0);

		$view->section = new \Components\Forum\Tables\Section($this->database);
		$view->section->loadByAlias($view->filters['section'], $this->offering->get('id'), 'course');
		$view->filters['section_id'] = $view->section->id;

		$view->category = new \Components\Forum\Tables\Category($this->database);
		$view->category->loadByAlias($view->filters['category'], $view->section->id, $this->offering->get('id'), 'course');
		$view->filters['category_id'] = $view->category->id;

		if (!$view->category->id)
		{
			$view->category->title = Lang::txt('Discussions');
			$view->category->alias = 'discussions';
		}

		// Initiate a forum object
		$view->post = new \Components\Forum\Tables\Post($this->database);

		// Load the topic
		$view->post->load($thread);
		$view->filters['object_id'] = $view->post->object_id;

		$view->unit = $this->offering->unit($view->filters['category']);
		$view->lecture = $view->unit->assetgroup($view->filters['object_id']);

		// Get reply count
		$view->total = $view->post->getCount($view->filters);

		$rows = $view->post->getRecords($view->filters);
		if ($rows)
		{
			foreach ($rows as $i => $row)
			{
				$rows[$i] = new \Components\Forum\Models\Post($row);
			}
		}

		$view->filters['limit'] = Request::getInt('limit', Config::get('list_limit'));
		$view->filters['start'] = Request::getInt('limitstart', 0);

		$children = array(
			0 => array()
		);

		$levellimit = ($view->filters['limit'] == 0) ? 500 : $view->filters['limit'];

		foreach ($rows as $v)
		{
			$pt      = $v->get('parent');
			$list    = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;
		}

		// Get replies
		$list = $this->_treeRecurse(0, '', array(), $children, max(0, $levellimit-1));

		$view->rows = array_slice($list, $view->filters['start'], $view->filters['limit']);

		$view->filters['parent']   = $view->post->id;

		// Record the hit
		$view->participants = $view->post->getParticipants($view->filters);

		// Get attachments
		$view->attach = new \Components\Forum\Tables\Attachment($this->database);
		$view->attachments = $view->attach->getAttachments($view->post->id);

		// Get tags on this article
		$view->tModel = new \Components\Forum\Models\Tags($view->post->id);
		$view->tags = $view->tModel->tags('cloud');

		// Get authorization
		$this->_authorize('category', $view->category->id);
		$this->_authorize('thread', $view->post->id);

		$view->config = $this->params;
		$view->course = $this->course;
		$view->offering = $this->offering;
		$view->option = $this->option;
		$view->notifications = $this->getPluginMessage();

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Recursive function to build tree
	 *
	 * @param   integer  $id        Parent ID
	 * @param   string   $indent    Indent text
	 * @param   array    $list      List of records
	 * @param   array    $children  Container for parent/children mapping
	 * @param   integer  $maxlevel  Maximum levels to descend
	 * @param   integer  $level     Indention level
	 * @param   integer  $type      Indention type
	 * @return  void
	 */
	public function _treeRecurse($id, $indent, $list, $children, $maxlevel=9999, $level=0, $type=1)
	{
		if (@$children[$id] && $level <= $maxlevel)
		{
			foreach ($children[$id] as $v)
			{
				$id = $v->get('id');

				$pre    = ' treenode';
				$spacer = ' indent' . $level;

				if ($v->get('parent') == 0)
				{
					$txt = '';
				}
				else
				{
					$txt = $pre;
				}
				$pt = $v->get('parent');

				$list[$id] = $v;
				$list[$id]->set('treename', "$indent$txt");
				$list[$id]->set('children', count(@$children[$id]));

				$list = $this->_treeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level+1, $type);
			}
		}
		return $list;
	}

	/**
	 * Show a form for editing a post
	 *
	 * @param   object  $post
	 * @return  string
	 */
	public function editthread($post=null)
	{
		$this->view = $this->view('edit', 'threads');
		$this->view->name = $this->_name;

		$id = Request::getInt('post', 0);
		$category = Request::getVar('category', '');
		$sectionAlias = Request::getVar('section', '');

		if (User::isGuest())
		{
			$return = Route::url($this->offering->link() . '&active=' . $this->_name . '&unit=' . $section . '&b=' . $category . '&c=new');
			if ($id)
			{
				$return = Route::url($this->offering->link() . '&active=' . $this->_name . '&unit=' . $section . '&b=' . $category . '&c=' . $id . '/edit');
			}
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		$this->view->category = new \Components\Forum\Tables\Category($this->database);
		$this->view->category->loadByAlias($category);

		// Incoming
		if (is_object($post))
		{
			$this->view->post = $post;
		}
		else
		{
			$this->view->post = new \Components\Forum\Tables\Post($this->database);
			$this->view->post->load($id);
		}

		// Get authorization
		$this->_authorize('thread', $id);

		if (!$id)
		{
			$this->view->post->created_by = User::get('id');
		}
		elseif ($this->view->post->created_by != User::get('id') && !$this->params->get('access-edit-thread'))
		{
			App::redirect(
				Route::url($this->base . '&unit=manage&b=' . $section . '&c=' . $category)
			);
			return;
		}

		$sModel = new \Components\Forum\Tables\Section($this->database);
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
			$default->title = Lang::txt('Categories');
			$default->alias = str_replace(' ', '-', $default->title);
			$default->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($default->title));
			$this->view->sections[] = $default;
		}

		$cModel = new \Components\Forum\Tables\Category($this->database);
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
		$this->view->tModel = new \Components\Forum\Models\Tags($this->view->post->id);
		$this->view->tags = $this->view->tModel->tags('string');

		$this->view->option = $this->option;
		$this->view->config = $this->params;
		$this->view->course = $this->course;
		$this->view->offering = $this->offering;
		$this->view->section = $sectionAlias;
		$this->view->notifications = $this->getPluginMessage();

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		return $this->view->loadTemplate();
	}

	/**
	 * Saves posted data for a new/edited forum thread post
	 *
	 * @return  void
	 */
	public function savethread()
	{
		// Check for request forgeries
		Request::checkToken();

		// Must be logged in
		if (User::isGuest())
		{
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->base, false, true)))
			);
			return;
		}

		// Incoming
		$section = Request::getVar('section', '');
		$no_html = Request::getInt('no_html', 0);
		$fields  = Request::getVar('fields', array(), 'post', 'none', 2);
		$fields  = array_map('trim', $fields);

		// Check permissions
		$this->_authorize('thread', intval($fields['id']));
		$asset = 'thread';

		if (($fields['id'] && !$this->params->get('access-edit-thread'))
		 || (!$fields['id'] && !$this->params->get('access-create-thread')))
		{
			App::redirect(
				Route::url($this->base),
				Lang::txt('You are not authorized to perform this action.'),
				'warning'
			);
			return;
		}

		if ($fields['id'])
		{
			$old = new \Components\Forum\Tables\Post($this->database);
			$old->load(intval($fields['id']));
		}

		// Bind data
		$model = new \Components\Forum\Tables\Post($this->database);
		if (!$model->bind($fields))
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editthread($model);
		}

		// Double comment?
		$query  = "SELECT * FROM `#__forum_posts` WHERE object_id=" . $this->database->Quote($model->object_id);
		$query .= " AND scope_id=" . $this->database->Quote($model->scope_id) . " AND scope=" . $this->database->Quote($model->scope);
		$query .= " AND comment=" . $this->database->Quote($model->comment) . " AND created_by=" . $this->database->Quote($model->created_by);
		$query .= " LIMIT 1";

		$this->database->setQuery($query);
		if ($result = $this->database->loadAssoc())
		{
			$model->bind($result);
		}

		// Load the category
		$category = new \Components\Forum\Tables\Category($this->database);
		$category->load(intval($model->category_id));
		if (!$model->object_id && $category->object_id)
		{
			$model->object_id = $category->object_id;
		}

		// Check content
		if (!$model->check())
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editthread($model);
		}

		// Store new content
		if (!$model->store())
		{
			$this->addPluginMessage($model->getError(), 'error');
			return $this->editthread($model);
		}

		// Determine parent ID
		$parent = ($model->parent) ? $model->parent : $model->id;

		// Get the thread ID
		if (!$model->thread && !$model->parent)
		{
			$model->thread = $model->id;
		}

		// Upload file
		$this->upload($model->thread, $model->id);

		// Update category ID if it was changed
		if ($fields['id'])
		{
			if ($old->category_id != $fields['category_id'])
			{
				$model->updateReplies(array('category_id' => $fields['category_id']), $model->id);
			}
		}

		// Save tags
		$tags = Request::getVar('tags', '', 'post');
		$tagger = new \Components\Forum\Models\Tags($model->id);
		$tagger->setTags($tags, User::get('id'), 1);

		// Being called through AJAX?
		if ($no_html)
		{
			// Set the thread
			Request::setVar('thread', $model->thread);
			// Is this a new post in a thread or new thread entirely?
			if (!$model->parent)
			{
				// New thread
				// Update the thread list and get the contents of the thread
				Request::setVar('action', 'both');
			}
			else
			{
				// Get a list of new posts in the thread
				Request::setVar('action', 'posts');
			}

			// If we have a lecture set, push through to the lecture view
			if (Request::getVar('group', ''))
			{
				$unit = $this->course->offering()->unit($category->alias);

				$lecture = new \Components\Courses\Models\Assetgroup($model->object_id);
				return $this->onCourseAfterLecture($this->course, $unit, $lecture);
			}
			else
			{
				// Display main panel
				return $this->panel();
			}
		}

		$rtrn = base64_decode(Request::getVar('return', '', 'post'));
		if (!$rtrn)
		{
			$rtrn = Route::url($this->base . '&thread=' . $thread);
		}

		// Set the redirect
		App::redirect(
			$rtrn,
			$message,
			'passed'
		);
	}

	/**
	 * Remove a thread
	 *
	 * @param   integer  $id
	 * @param   boolean  $redirect
	 * @return  void
	 */
	public function deletethread($id=0, $redirect=true)
	{
		$section  = Request::getVar('section', '');
		$category = Request::getVar('category', '');

		// Is the user logged in?
		if (User::isGuest())
		{
			App::redirect(
				Route::url($this->base),
				Lang::txt('PLG_COURSES_DISCUSSIONS_LOGIN_NOTICE'),
				'warning'
			);
			return;
		}

		// Incoming
		$id = ($id) ? $id : Request::getInt('thread', 0);

		// Initiate a forum object
		$model = new \Components\Forum\Tables\Post($this->database);
		$model->load($id);

		// Make the sure the category exist
		if (!$model->id)
		{
			App::redirect(
				Route::url($this->base),
				Lang::txt('PLG_COURSES_DISCUSSIONS_MISSING_ID'),
				'error'
			);
			return;
		}

		// Check if user is authorized to delete entries
		$this->_authorize('thread', $id);
		if (!$this->params->get('access-delete-thread'))
		{
			App::redirect(
				Route::url($this->base),
				Lang::txt('PLG_COURSES_DISCUSSIONS_NOT_AUTHORIZED'),
				'warning'
			);
			return;
		}

		// Update replies if this is a parent (thread starter)
		//if (!$model->parent)
		//{
			if (!$model->updateReplies(array('state' => 2), $model->id))  // 0 = unpublished, 1 = published, 2 = deleted
			{
				$this->setError($model->getError());
			}
		//}

		// Delete the topic itself
		$model->state = 2;  // 0 = unpublished, 1 = published, 2 = deleted
		if (!$model->store())
		{
			App::redirect(
				Route::url($this->base),
				$forum->getError(),
				'error'
			);
			return;
		}

		// Redirect to main listing
		if ($redirect)
		{
			App::redirect(
				Route::url($this->base),
				Lang::txt('PLG_COURSES_DISCUSSIONS_THREAD_DELETED'),
				'passed'
			);
		}
	}

	/**
	 * Uploads a file to a given directory and returns an attachment string
	 * that is appended to report/comment bodies
	 *
	 * @param   string   $listdir  Directory to upload files to
	 * @param   integer  $post_id  ID of the post
	 * @return  string   A string that gets appended to messages
	 */
	public function upload($listdir, $post_id)
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return;
		}

		if (!$listdir)
		{
			$this->setError(Lang::txt('PLG_COURSES_DISCUSSIONS_NO_UPLOAD_DIRECTORY'));
			return;
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			return;
		}

		// Incoming
		$description = trim(Request::getVar('description', ''));

		// Construct our file path
		$path = PATH_APP . DS . trim($this->params->get('filepath', '/site/forum'), DS) . DS . $listdir;
		if ($post_id)
		{
			$path .= DS . $post_id;
		}

		// Build the path if it doesn't exist
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('PLG_COURSES_DISCUSSIONS_UNABLE_TO_CREATE_UPLOAD_PATH'));
				return;
			}
		}

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);
		$ext = strtolower(Filesystem::extension($file['name']));

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('PLG_COURSES_DISCUSSIONS_ERROR_UPLOADING'));
			return;
		}
		else
		{
			// File was uploaded
			// Create database entry
			$row = new \Components\Forum\Tables\Attachment($this->database);
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
		$thread = Request::getInt('group', 0);
		$post   = Request::getInt('asset', 0);
		$file   = Request::getVar('file', '');

		// Check logged in status
		if (User::isGuest())
		{
			$return = Route::url($this->offering->link() . '&active=' . $this->_name . '&unit=download&b=' . $thread . '&file=' . $file);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// Ensure we have a database object
		if (!$this->database)
		{
			App::abort(500, Lang::txt('PLG_COURSES_DISCUSSIONS_DATABASE_NOT_FOUND'));
			return;
		}

		// Instantiate an attachment object
		$attach = new \Components\Forum\Tables\Attachment($this->database);
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
			App::abort(404, Lang::txt('PLG_COURSES_DISCUSSIONS_FILE_NOT_FOUND'));
			return;
		}
		$file = $attach->filename;

		// Get the parent ticket the file is attached to
		$this->model = new \Components\Forum\Tables\Post($this->database);
		$this->model->load($attach->post_id);

		if (!$this->model->id)
		{
			App::abort(404, Lang::txt('PLG_COURSES_DISCUSSIONS_POST_NOT_FOUND'));
			return;
		}

		// Load ACL
		$this->_authorize('thread', $this->model->id);

		// Ensure the user is authorized to view this file
		if (!$this->course->access('view'))
		{
			App::abort(403, Lang::txt('PLG_COURSES_DISCUSSIONS_NOT_AUTH_FILE'));
			return;
		}

		// Ensure we have a path
		if (empty($file))
		{
			App::abort(404, Lang::txt('PLG_COURSES_DISCUSSIONS_FILE_NOT_FOUND'));
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

		// Add PATH_CORE
		$filename = PATH_APP . $file;

		// Ensure the file exist
		if (!file_exists($filename))
		{
			App::abort(404, Lang::txt('PLG_COURSES_DISCUSSIONS_FILE_NOT_FOUND'));
			return;
		}

		// Initiate a new content server and serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			// Should only get here on error
			App::abort(404, Lang::txt('PLG_COURSES_DISCUSSIONS_SERVER_ERROR'));
		}
		else
		{
			exit;
		}
		return;
	}

	/**
	 * Reorder a record up
	 *
	 * @return  void
	 */
	public function orderup()
	{
		return $this->reorder(-1);
	}

	/**
	 * Reorder a record up
	 *
	 * @return  void
	 */
	public function orderdown()
	{
		return $this->reorder(1);
	}

	/**
	 * Reorder a plugin
	 *
	 * @param   integer  $access  Access level to set
	 * @return  void
	 */
	public function reorder($inc=1)
	{
		if (!$this->course->access('manage', 'offering'))
		{
			return $this->panel();
		}

		$alias = Request::getVar('section', '');

		$row = new \Components\Forum\Tables\Section($this->database);

		if ($row->loadByAlias($alias, $this->offering->get('id'), 'course'))
		{
			$row->move($inc, 'scope=' . $this->database->Quote($row->scope) . ' AND scope_id=' . $this->database->Quote($row->scope_id));
			$row->reorder('scope=' . $this->database->Quote($row->scope) . ' AND scope_id=' . $this->database->Quote($row->scope_id));
		}

		App::redirect(
			Route::url($this->base . '&unit=manage')
		);
	}

	/**
	 * Remove all items associated with the gorup being deleted
	 *
	 * @param   object  $course  Course being deleted
	 * @return  string  Log of items removed
	 */
	public function onCourseDelete($course)
	{
		if (!$course->exists())
		{
			return '';
		}

		$log = Lang::txt('PLG_COURSES_FORUM') . ': ';

		$this->database = App::get('db');

		$sModel = new \Components\Forum\Tables\Section($this->database);
		$sections = array();
		foreach ($course->offerings() as $offering)
		{
			if (!$offering->exists())
			{
				continue;
			}
			$sec = $sModel->getRecords(array(
				'scope'    => 'course',
				'scope_id' => $offering->get('id')
			));
			foreach ($sec as $s)
			{
				$sections[] = $s;
			}
		}

		// Do we have any IDs?
		if (count($sections) > 0)
		{
			// Loop through each ID
			foreach ($sections as $section)
			{
				// Get the categories in this section
				$cModel = new \Components\Forum\Tables\Category($this->database);
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
					$tModel = new \Components\Forum\Tables\Post($this->database);
					if (!$tModel->setStateByCategory($cats, 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
					{
						$this->setError($tModel->getError());
					}
					$log .= 'forum.section.' . $section->id . '.category.' . $category->id . '.post' . "\n";

					// Set all the categories to "deleted"
					if (!$cModel->setStateBySection($section->id, 2))  /* 0 = unpublished, 1 = published, 2 = deleted */
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
			$log .= Lang::txt('PLG_COURSES_DISCUSSIONS_NO_RESULTS')."\n";
		}

		return $log;
	}
}
