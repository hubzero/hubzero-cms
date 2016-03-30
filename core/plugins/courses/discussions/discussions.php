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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Components\Forum\Models\Manager;
use Components\Forum\Models\Section;
use Components\Forum\Models\Category;
use Components\Forum\Models\Post;
use Components\Forum\Models\Attachment;

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

		// Attempt to load the category
		$category = Category::all()
			->whereEquals('object_id', $assetgroup->get('id'))
			->whereEquals('scope', 'course')
			->whereEquals('scope_id', $unit->get('offering_id'))
			->whereEquals('state', Category::STATE_PUBLISHED)
			->row();

		// Is there a category already?
		if (!$category->get('id'))
		{
			// No category
			// Is there a parent section?
			$section = Section::all()
				->whereEquals('object_id', $unit->get('id'))
				->whereEquals('scope', 'course')
				->whereEquals('scope_id', $unit->get('offering_id'))
				->whereEquals('state', Section::STATE_PUBLISHED)
				->row();

			if (!$section->get('id'))
			{
				// No parent section
				// Create it!
				$section->set('title', $unit->get('title'));
				$section->set('alias', $unit->get('alias'));
				$section->set('state', $unit->get('state'));
				$section->set('scope', 'course');
				$section->set('scope_id', $unit->get('offering_id'));
				$section->set('object_id', $unit->get('id'));
				$section->set('ordering', $unit->get('ordering'));
				$section->save();
			}
			// Assign the section ID
			$category->set('section_id', $section->get('id'));
		}

		// Don't change "Deleted" items
		if ($category->get('state') == Category::STATE_DELETED)
		{
			return $category->get('id');
		}

		// Assign asset group data to category to keep them in sync
		$category->set('state', $assetgroup->get('state'));
		$category->set('title', $assetgroup->get('title'));

		if ($assetgroup->get('title') == '--')
		{
			$ag = ($assetgroup->assets() ? $assetgroup->assets()->fetch('first') : null);

			if ($ag)
			{
				$category->set('title', $ag->get('title'));
			}
		}

		$category->set('scope', 'course');
		$category->set('scope_id', $unit->get('offering_id'));
		$category->set('object_id', $assetgroup->get('id'));
		$category->set('alias', $assetgroup->get('alias'));
		if (!$category->get('id'))
		{
			$category->set('description', Lang::txt('Discussions for %s', $category->get('title')));
		}
		$category->set('ordering', $assetgroup->get('ordering'));
		$category->save();

		return $category->get('id');
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

		$unit = \Components\Courses\Models\Unit::getInstance($assetgroup->get('unit_id'));

		// Attempt to load an associated category
		$category = Category::all()
			->whereEquals('object_id', $assetgroup->get('id'))
			->whereEquals('scope', 'course')
			->whereEquals('scope_id', $unit->get('offering_id'))
			->whereEquals('state', Category::STATE_PUBLISHED)
			->row();

		// Was a category found?
		if ($category->get('id') && $category->get('state') != Category::STATE_DELETED)
		{
			// Mark as deleted
			// Note: State will carry through to threads under this category.
			$category->set('state', Category::STATE_DELETED);
			$category->save();
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

		$section = Section::all()
			->whereEquals('object_id', $unit->get('id'))
			->whereEquals('scope', 'course')
			->whereEquals('scope_id', $unit->get('offering_id'))
			->whereEquals('state', Section::STATE_PUBLISHED)
			->row();

		if ($section->get('id') && $section->get('state') != Section::STATE_DELETED)
		{
			$section->set('state', $unit->get('state'));
			$section->set('title', $unit->get('title'));
			$section->set('alias', $unit->get('alias'));
			$section->set('ordering', $unit->get('ordering'));
			$section->save();
		}

		return $section->get('id');
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

		$section = Section::all()
			->whereEquals('object_id', $unit->get('id'))
			->whereEquals('scope', 'course')
			->whereEquals('scope_id', $unit->get('offering_id'))
			->whereEquals('state', Section::STATE_PUBLISHED)
			->row();

		if ($section->get('id'))
		{
			// Mark as deleted
			// Note: State will carry through to categories and
			//       threads under this section.
			$section->set('state', Section::STATE_DELETED);
			$section->save();
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

			$this->sections = Section::all()
				->whereEquals('scope', 'course')
				->whereEquals('scope_id', $this->offering->get('id'))
				->whereEquals('state', Section::STATE_PUBLISHED)
				->order('ordering', 'asc') //'ordering ASC, created ASC, title'
				->rows();

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

			$this->forum = new Manager('course', $this->offering->get('id'));

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

		$posts = Post::all()
			->whereEquals('scope', 'course')
			->whereEquals('scope_id', $offering->get('id'))
			->whereIn('state', array(1, 3))
			->whereEquals('parent', 0);

		if ($this->params->get('discussions_threads', 'all') != 'all')
		{
			$posts->whereEquals('scope_sub_id', $course->offering()->section()->get('id'));
		}

		$response->set('meta_count', $posts->total());

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
				->set('message', Lang::txt('You must be enrolled to utilize the discussion feature.'));

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
		$this->course = $course;
		$this->offering = $course->offering();
		$this->unit = $unit;
		$this->base = $this->offering->link() . '&active=' . $this->_active;

		$this->_authorize('category');
		$this->_authorize('thread');

		// Incoming
		$filters = array(
			'limit'     => Request::getInt('limit', 500),
			'start'     => Request::getInt('limitstart', 0),
			'section'   => Request::getVar('section', ''),
			'category'  => Request::getVar('category', ''),
			'state'     => array(1, 3),
			'scope'     => 'course',
			'scope_id'  => $course->offering()->get('id'),
			'sticky'    => false,
			'search'    => Request::getVar('search', ''),
			'sort_Dir'  => 'DESC',
			'sort'      => 'c.created',
			'object_id' => $lecture->get('id')
		);
		if ($this->params->get('discussions_threads', 'all') != 'all')
		{
			$filters['scope_sub_id'] = $course->offering()->section()->get('id');
		}

		// Load the section
		// This should map to course Unit
		$section = Section::all()
			->whereEquals('alias', $unit->get('alias'))
			->whereEquals('scope', $filters['scope'])
			->whereEquals('scope_id', $filters['scope_id'])
			->whereEquals('state', Section::STATE_PUBLISHED)
			->row();

		if (!$section->get('id'))
		{
			// Create a default section
			$section->set('title', $unit->get('title'));
			$section->set('alias', $unit->get('alias'));
			$section->set('scope', $filters['scope']);
			$section->set('scope_id', $filters['scope_id']);
			$section->set('object_id', $unit->get('id'));
			$section->set('state', Section::STATE_PUBLISHED);
			$section->save();
		}

		// Load category
		// This should map to course asset group (lecture)
		$category = Category::all()
			->whereEquals('object_id', $lecture->get('id'))
			->whereEquals('section_id', $section->get('id'))
			->whereEquals('scope', $filters['scope'])
			->whereEquals('scope_id', $filters['scope_id'])
			->whereEquals('state', Category::STATE_PUBLISHED)
			->row();

		if (!$category->get('id'))
		{
			// Category doesn't exist yet, so create it
			$category->set('section_id', $section->get('id'));
			if ($lecture->get('title') == '--')
			{
				$category->set('title', $lecture->assets()->fetch('first')->get('title'));
			}
			else
			{
				$category->set('title', $lecture->get('title'));
			}
			$category->set('alias', $lecture->get('alias'));
			$category->set('description', Lang::txt('Discussions for %s', $unit->get('alias')));
			$category->set('state', Category::STATE_PUBLISHED);
			$category->set('scope', $filters['scope']);
			$category->set('scope_id', $filters['scope_id']);
			$category->set('object_id', $lecture->get('id'));
			$category->set('ordering', $lecture->get('ordering'));
			$category->save();
		}

		// Instantiate a blank Post
		$post = Post::blank();
		$post->set('scope', $filters['scope']);
		$post->set('scope_id', $filters['scope_id']);
		$post->set('scope_sub_id', $course->offering()->section()->get('id'));
		$post->set('category_id', $category->get('id'));
		$post->set('object_id', $lecture->get('id'));
		$post->set('parent', 0);

		// Get attachments
		//$view->attach = Attachment::blank();
		//$view->attachments = $view->attach->getAttachments($view->post->id);

		$thread = Request::getInt('thread', 0);
		$thread = $thread ?: Request::getInt('thread', 0, 'get'); // No thread? Try being more specific

		$action = strtolower(Request::getWord('action', ''));

		// Called via AJAX?
		$no_html = Request::getInt('no_html', 0);
		if ($no_html)
		{
			$data = new stdClass();
			$data->success = true;

			$data->threads = new stdClass;
			$data->threads->lastchange = '0000-00-00 00:00:00';
			$data->threads->lastid     = 0;
			$data->threads->total      = 0;
			$data->threads->posts      = null;
			$data->threads->html       = null;

			$data->thread  = new stdClass;
			$data->thread->lastchange = '0000-00-00 00:00:00';
			$data->thread->lastid     = 0;
			$data->thread->posts      = null;
			$data->thread->total      = 0;
			$data->thread->html       = null;

			if ($thread)
			{
				$post = Post::oneOrNew($thread);
			}

			if (!$action && $thread)
			{
				$action = 'both';
			}

			switch ($action)
			{
				case 'posts':
					$filters['parent']   = $post->get('id');
					$filters['start_at'] = Request::getVar('start_at', '');

					$data->thread = $this->_posts($post, $filters);
				break;

				case 'delete':
					if ($pid = Request::getInt('post', 0))
					{
						$this->deletethread($pid, false);
					}
					$data->thread = $this->_thread($post, $filters);
				break;

				case 'thread':
					$data->thread = $this->_thread($post, $filters);
				break;

				case 'search':
					$filters['search'] = Request::getVar('search', '');

					$data->threads = $this->_threadsSearch($post, $filters);
				break;

				case 'sticky':
					$post->set('sticky', Request::getInt('sticky', 0));
					$post->save();
				break;

				case 'both':
				default:
					$filters['start_at'] = Request::getVar('start_at', '');

					$data->thread = $this->_thread($post, $filters);

					$filters['start_at'] = Request::getVar('threads_start', '');

					$data->threads = $this->_threads($post, $filters);
				break;

				case 'threads':
				default:
					$filters['parent']   = $post->get('id');
					$filters['start_at'] = Request::getVar('threads_start', '');

					$data->threads = $this->_threads($post, $filters);
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
				$filters['search'] = Request::getVar('search', '');
				$data = $this->_threadsSearch($post, $filters);
				$threads = $data->posts;
			break;

			default:
				if ($action == 'delete')
				{
					if ($pid = Request::getInt('post', 0))
					{
						$this->deletethread($pid, false);
					}
				}

				$filters['parent'] = 0;

				$threads = $forum->posts($filters)->rows();
			break;
		}

		$data = null;

		if ($thread)
		{
			$post = Post::oneOrNew($thread);
			$data = $this->_thread($post, $filters);
		}

		$view = $this->view('lecture', 'threads')
			->set('course', $course)
			->set('unit', $unit)
			->set('lecture', $lecture)
			->set('option', 'com_courses')
			->set('config', $this->params)
			->set('filters', $filters)
			->set('post', $post)
			->set('data', $data)
			->set('thread', $thread)
			->set('threads', $threads)
			->setErrors($this->getErrors());

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
	 * @param   object  $post
	 * @param   array   $filters  Filters to apply
	 * @return  void
	 */
	protected function _thread($post, $filters=array())
	{
		$thread = new stdClass;
		$thread->lastchange = '0000-00-00 00:00:00';
		$thread->lastid     = $post->get('id');
		$thread->posts      = null;
		$thread->total      = 0;
		$thread->html       = null;

		$comments = null;

		$rows = $post->thread()
			->whereIn('state', $filters['state'])
			->whereIn('access', $filters['access'])
			->order('lft', 'asc')
			->rows();

		if ($rows->count())
		{
			$thread->total = $rows->count();

			foreach ($rows as $v)
			{
				if ($v->get('created') > $thread->lastchange)
				{
					$thread->lastchange = $v->get('created');
				}
			}

			$comments = $post->toTree($rows);
		}

		$view = $this->view('list', 'threads')
			->set('parent', $post->get('parent'))
			->set('thread', $post->get('id'))
			->set('option', Request::getCmd('option'))
			->set('config', $this->params)
			->set('depth', 0)
			->set('cls', 'odd')
			->set('base', $this->base . '&thread=' . $post->get('id') . ($filters['search'] ? '&action=search&search=' . $filters['search'] : ''))
			->set('comments', $comments)
			->set('course', $this->course)
			->set('search', $filters['search'])
			->set('post', $post)
			->set('thread', $post->get('thread'))
			->set('unit', '')
			->set('lecture', '');

		if ($this->_active == 'outline')
		{
			$view->set('unit', $this->unit->get('alias'));
			$view->set('lecture', $this->lecture->get('alias'));
		}

		$view->attach     = Attachment::blank();

		$thread->html = $view->loadTemplate();

		return $thread;
	}

	/**
	 * Get a filtered list of threads
	 *
	 * @param   object  $post     \Components\Forum\Models\Post
	 * @param   array   $filters  Filters to apply
	 * @return  void
	 */
	protected function _threadsSearch($post, $filters=array())
	{
		$threads = new stdClass;
		$threads->lastchange = '0000-00-00 00:00:00';
		$threads->lastid     = 0;
		$threads->total      = 0;
		$threads->posts      = null;
		$threads->html       = null;

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

			$cview = $this->view('_threads', 'threads')
				->set('category', 'categorysearch')
				->set('option', $this->option)
				->set('threads', (isset($filters['id']) && count($filters['id']) > 0) ? $post->find($filters) : null)
				->set('config', $this->params)
				->set('cls', 'odd')
				->set('search', $srch) // Pass the search term along so it can be highlighted in text
				->set('base', $this->base)
				->set('unit', '')
				->set('lecture', '')
				->set('course', $this->course)
				->set('instructors', $this->_instructors());

			if ($this->_active == 'outline')
			{
				$cview->set('unit', $this->unit->get('alias'));
				$cview->set('lecture', $this->lecture->get('alias'));
			}

			$threads->posts = $cview->get('threads');
			$threads->total = $threads->posts->count();
			$threads->html  = $cview->loadTemplate();
		}

		return $threads;
	}

	/**
	 * Get a filtered list of threads
	 *
	 * @param   object  $post     \Components\Forum\Models\Post
	 * @param   array   $filters  Filters to apply
	 * @return  void
	 */
	protected function _threads($post, $filters=array())
	{
		$threads = new stdClass;
		$threads->lastchange = '0000-00-00 00:00:00';
		$threads->lastid     = 0;
		$threads->posts      = null;
		$threads->html       = null;
		$threads->total      = 0;

		$results = Post::all()
			->whereEquals('parent', 0)
			->order('created', 'asc') // Needs to be reverse order that items are prepended with AJAX
			->rows();

		if ($results->count())
		{
			foreach ($results as $key => $row)
			{
				$threads->lastid = $row->get('id') > $threads->lastid
								 ? $row->get('id')
								 : $threads->lastid;
				$threads->lastchange = ($row->get('created') > $threads->lastchange)
									 ? $row->get('created')
									 : $threads->lastchange;

				$cview = $this->view('_thread', 'threads')
					->set('option', $this->option)
					->set('thread', $row)
					->set('unit', '')
					->set('lecture', '')
					->set('cls', 'odd')
					->set('base', $this->base)
					->set('search', '')
					->set('course', $this->course)
					->set('instructors', $this->_instructors());

				if ($this->_active == 'outline')
				{
					$cview->set('unit', $this->unit->get('alias'));
					$cview->set('lecture', $this->lecture->get('alias'));
				}

				$results[$key]->mine = ($row->get('created_by') == User::get('id')) ? true : false;
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
	 * @param   object  $post     \Components\Forum\Models\Post
	 * @param   array   $filters  Filters to apply
	 * @return  void
	 */
	protected function _posts($post, $filters=array())
	{
		$thread = new stdClass;
		$thread->lastchange = '0000-00-00 00:00:00';
		$thread->lastid     = 0;
		$thread->posts      = null;
		$thread->html       = null;
		$thread->total      = 0;

		$results = $post->getTree($post->id, $filters);

		if ($results->count())
		{
			$results = $post->toTree($results);

			foreach ($results as $key => $row)
			{
				$thread->lastchange = ($row->get('created') > $thread->lastchange)
									? $row->get('created')
									: $thread->lastchange;

				$results[$key]->replies = null;

				$cview = $this->view('comment', 'threads')
					->set('option', $this->option)
					->set('comment', $row)
					->set('post', $post)
					->set('config', $this->params)
					->set('depth', Request::getInt('depth', 1, 'post'))
					->set('cls', 'odd')
					->set('base', $this->base)
					->set('attach', Attachment::blank())
					->set('course', $this->course)
					->set('search', '')
					->set('unit', '')
					->set('lecture', '');

				if ($this->_active == 'outline')
				{
					$cview->set('unit', $this->unit->get('alias'));
					$cview->set('lecture', $this->lecture->get('alias'));
				}

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
		// Incoming
		$filters = array(
			'authorized' => 1,
			'scope'      => 'course',
			'scope_id'   => $this->offering->get('id'),
			'search'     => Request::getVar('search', ''),
			'section_id' => 0,
			'state'      => 1,
			'limit'      => Request::getInt('limit', 500),
			'start'      => Request::getInt('limitstart', 0)
		);
		if ($this->params->get('discussions_threads', 'all') != 'all')
		{
			$filters['scope_sub_id'] = $this->offering->section()->get('id');
		}

		$no_html = Request::getInt('no_html', 0);
		$thread  = Request::getInt('thread', 0);
		$thread  = $thread ?: Request::getInt('thread', 0, 'get');
		$action  = strtolower(Request::getWord('action', ''));

		//get authorization
		$this->_authorize('section');
		$this->_authorize('category');
		$this->_authorize('thread');

		if ($no_html)
		{
			$filters['sticky'] = false;
			$filters['sort_Dir'] = 'DESC';
			$filters['sort'] = 'c.created';
			//$filters['object_id'] = 0;

			$post = Post::blank();

			$data = new stdClass();
			$data->success = true;

			$data->threads = new stdClass;
			$data->threads->lastchange = '0000-00-00 00:00:00';
			$data->threads->lastid     = 0;
			$data->threads->total      = 0;
			$data->threads->posts      = null;
			$data->threads->html       = null;

			$data->thread  = new stdClass;
			$data->thread->lastchange = '0000-00-00 00:00:00';
			$data->thread->lastid     = 0;
			$data->thread->posts      = null;
			$data->thread->total      = 0;
			$data->thread->html       = null;

			if ($thread)
			{
				$post = Post::oneOrNew($thread);
			}

			if (!$action && $thread)
			{
				$action = 'both';
			}

			switch ($action)
			{
				case 'posts':
					$filters['parent']   = $post->get('id');
					$filters['start_at'] = Request::getVar('start_at', '');

					$data->thread = $this->_posts($post, $filters);
				break;

				case 'delete':
					if ($pid = Request::getInt('post', 0))
					{
						$this->deletethread($pid, false);
					}
					$data->thread = $this->_thread($post, $filters);
				break;

				case 'thread':
					$data->thread = $this->_thread($post, $filters);
				break;

				case 'search':
					$filters['search'] = Request::getVar('search', '');

					$data->threads = $this->_threadsSearch($post, $filters);
				break;

				case 'sticky':
					$post->set('sticky', Request::getInt('sticky', 0));
					$post->save();
				break;

				case 'both':
				default:
					$filters['start_at'] = Request::getVar('start_at', '');

					$data->thread = $this->_thread($post, $filters);

					$filters['start_at'] = Request::getVar('threads_start', '');

					$data->threads = $this->_threads($post, $filters);
				break;

				case 'threads':
				default:
					$filters['parent']   = $post->get('id');
					$filters['start_at'] = Request::getVar('threads_start', '');

					$data->threads = $this->_threads($post, $filters);
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

		$filters['state'] = 1;

		// Get Sections
		if (!isset($this->sections))
		{
			$sects = Section::all()
				->whereEquals('state', $filters['state'])
				->whereEquals('scope', $filters['scope'])
				->whereEquals('scope_id', $filters['scope_id'])
				->order('ordering', 'asc')
				->rows();
		}
		else
		{
			$sects = $this->sections;
		}

		$stats = new stdClass;
		$stats->categories = 0;
		$stats->threads    = 0;
		$stats->posts      = 0;

		// Collect all categories
		$categories = array();
		$results = Category::all()
			->whereEquals('scope', $filters['scope'])
			->whereEquals('scope_id', $filters['scope_id'])
			->whereEquals('state', Category::STATE_PUBLISHED)
			->order('ordering', 'asc')
			->rows();

		if ($results->count())
		{
			foreach ($results as $category)
			{
				if (!isset($categories[$category->get('section_id')]))
				{
					$categories[$category->get('section_id')] = array();
				}
				$categories[$category->get('section_id')][] = $category;
			}
		}

		// Loop through all sections and distribute categories
		$sections = array();
		foreach ($sects as $key => $section)
		{
			$section->set('threads', 0);
			$section->set('categories', isset($categories[$section->get('id')]) ? $categories[$section->get('id')] : array());

			if ((!$section->get('categories') || !count($section->get('categories')))
			 && $section->get('object_id'))
			{
				$section->set('categories', array());
			}

			$stats->categories += count($section->get('categories'));

			foreach ($section->get('categories') as $c)
			{
				$entries = $c->posts()
					->whereEquals('parent', 0)
					->whereEquals('state', $filters['state'])
					->whereEquals('scope', $filters['scope'])
					->whereEquals('scope_id', $filters['scope_id']);
				if ($filters['scope_sub_id'])
				{
					$entries->whereEquals('scope_sub_id', $filters['scope_sub_id']);
				}
				$threads = $entries->total();

				$entries = $c->posts()
					->whereEquals('state', $filters['state'])
					->whereEquals('scope', $filters['scope'])
					->whereEquals('scope_id', $filters['scope_id']);
				if ($filters['scope_sub_id'])
				{
					$entries->whereEquals('scope_sub_id', $filters['scope_sub_id']);
				}
				$posts = $entries->total();

				$c->set('threads', $threads);
				$c->set('posts', $posts);

				$section->set('threads', $section->get('threads') + $threads);

				$stats->threads += $threads;
				$stats->posts   += $posts;
			}

			$sections[] = $section;
		}

		$filters['state'] = array(1, 3);

		$post = Post::blank()
			->set('scope', $filters['scope'])
			->set('scope_id', $filters['scope_id'])
			->set('scope_sub_id', $this->offering->section()->get('id'));

		$data = null;
		if ($thread)
		{
			$post = Post::oneOrNew($thread);
			$data = $this->_thread($post, $filters);
		}

		$view = $this->view('display', 'panel')
			->set('option', $this->option)
			->set('name', 'courses')
			->set('course', $this->course)
			->set('offering', $this->offering)
			->set('config', $this->params)
			->set('sections', $sections)
			->set('filters', $filters)
			->set('thread', $thread)
			->set('stats', $stats)
			->set('data', $data)
			->setErrors($this->getErrors());

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
		// Incoming
		$filters = array(
			'authorized'   => 1,
			'scope'        => 'course',
			'scope_id'     => $offering->get('id'),
			'scope_sub_id' => $offering->section()->get('id'),
			'search'       => Request::getVar('search', ''),
			'section_id'   => 0,
			'state'        => Section::STATE_PUBLISHED,
			'limit'        => Request::getInt('limit', 500),
			'start'        => Request::getInt('limitstart', 0)
		);

		$post = Post::blank();
		$post->set('scope', $filters['scope']);
		$post->set('scope_id', $filters['scope_id']);
		$post->set('scope_sub_id', $filters['scope_sub_id']);

		$sects = Section::all()
			->whereEquals('scope', $filters['scope'])
			->whereEquals('scope_id', $filters['scope_id'])
			->whereEquals('state', Section::STATE_PUBLISHED)
			->ordered()
			->rows();

		$stats = new stdClass;
		$stats->categories = 0;
		$stats->threads    = 0;
		$stats->posts      = 0;

		$sections = array();
		foreach ($sects as $section)
		{
			$categories = $section->categories()
				->whereEquals('state', $filters['state'])
				->limit($filters['limit'])
				->rows();

			$section->set('threads', 0);
			$section->set('categories', $categories);

			$stats->categories += $categories->count();

			if ($categories->count())
			{
				foreach ($categories as $category)
				{
					$threads = $category->threads()
						->whereEquals('scope_sub_id', $filters['scope_sub_id'])
						->whereEquals('state', $filters['state'])
						->total();

					$posts = $category->posts()
						->whereEquals('scope_sub_id', $filters['scope_sub_id'])
						->whereEquals('state', $filters['state'])
						->total();

					$section->set('threads', $section->get('threads') + $threads);

					$category->set('threads', $threads);
					$category->set('posts', $posts);

					$stats->threads += $threads;
					$stats->posts   += $posts;
				}
			}

			$sections[] = $section;
		}

		$view = $this->view('dashboard', 'threads')
			->set('option', 'com_courses')
			->set('name', 'courses')
			->set('course', $course)
			->set('offering', $offering)
			->set('config', $course->config())
			->set('sections', $sections)
			->set('no_html', Request::getInt('no_html', 0))
			->set('thread', Request::getInt('thread', 0))
			->set('filters', $filters)
			->set('stats', $stats)
			->set('data', null)
			->setErrors($this->getErrors());

		return $view->loadTemplate();
	}

	/**
	 * Show sections in this forum
	 *
	 * @return  string
	 */
	public function sections()
	{
		if (!$this->course->access('manage', 'offering'))
		{
			return $this->panel();
		}

		// Get authorization
		$this->_authorize('section');
		$this->_authorize('category');

		// Filters
		$filters = array(
			'scope'    => $this->forum->get('scope'),
			'scope_id' => $this->forum->get('scope_id'),
			'state'    => Section::STATE_PUBLISHED,
			'search'   => Request::getVar('q', ''),
			'access'   => User::getAuthorisedViewLevels()
		);

		$edit = Request::getVar('section', '');

		// Get Sections
		$sections = $this->forum->sections($filters);

		// Output view
		$view = $this->view('display', 'sections')
			->set('filters', $filters)
			->set('config', $this->params)
			->set('option', $this->option)
			->set('course', $this->course)
			->set('offering', $this->offering)
			->set('forum', $this->forum)
			->set('sections', $sections)
			->set('edit', $edit)
			->setErrors($this->getErrors());

		return $view->loadTemplate();
	}

	/**
	 * Saves a section and redirects to main page afterward
	 *
	 * @return  void
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
		$section = Section::oneOrNew($fields['id'])->set($fields);

		// Check for alias duplicates
		if (!$section->isUnique())
		{
			App::redirect(
				Route::url($this->base . '&unit=manage'),
				Lang::txt('COM_FORUM_ERROR_SECTION_ALREADY_EXISTS'),
				'error'
			);
		}

		// Store new content
		if (!$section->save())
		{
			Notify::error($section->getError());
		}

		// Log activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($fields['id'] ? 'updated' : 'created'),
				'scope'       => 'forum.section',
				'scope_id'    => $section->get('id'),
				'description' => Lang::txt('PLG_COURSES_FORUM_ACTIVITY_SECTION_' . ($fields['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . Route::url($this->base) . '">' . $section->get('title') . '</a>'),
				'details'     => array(
					'title' => $section->get('title'),
					'url'   => Route::url($this->base)
				)
			],
			'recipients' => array(
				['course', $this->offering->get('id')],
				['forum.' . $this->forum->get('scope'), $this->forum->get('scope_id')],
				['forum.section', $section->get('id')],
				['user', $section->get('created_by')]
			)
		]);

		// Set the redirect
		App::redirect(
			Route::url($this->base . '&unit=manage')
		);
	}

	/**
	 * Deletes a section and redirects to main page afterwards
	 *
	 * @return  void
	 */
	public function deletesection()
	{
		if (!$this->course->access('manage', 'offering'))
		{
			return $this->panel();
		}

		// Load the section
		$section = Section::all()
			->whereEquals('alias', Request::getVar('section'))
			->whereEquals('scope', $this->forum->get('scope'))
			->whereEquals('scope_id', $this->forum->get('scope_id'))
			->row();

		// Make the sure the section exist
		if (!$section->get('id'))
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

		// Set the section to "deleted"
		$section->set('state', $section::STATE_DELETED);

		if (!$section->save())
		{
			Notify::error($section->getError());
		}
		else
		{
			Notify::success(Lang::txt('PLG_COURSES_DISCUSSIONS_SECTION_DELETED'));
		}

		// Log activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'forum.section',
				'scope_id'    => $section->get('id'),
				'description' => Lang::txt('PLG_COURSES_FORUM_ACTIVITY_SECTION_DELETED', '<a href="' . Route::url($this->base) . '">' . $section->get('title') . '</a>'),
				'details'     => array(
					'title' => $section->get('title'),
					'url'   => Route::url($this->base)
				)
			],
			'recipients' => array(
				['course', $this->offering->get('id')],
				['forum.' . $this->forum->get('scope'), $this->forum->get('scope_id')],
				['forum.section', $section->get('id')],
				['user', $section->get('created_by')]
			)
		]);

		// Redirect to main listing
		App::redirect(
			Route::url($this->base . '&unit=manage')
		);
	}

	/**
	 * Display a list of threads for a category
	 *
	 * @return  string
	 */
	public function categories()
	{
		if (!$this->course->access('manage', 'offering'))
		{
			return $this->panel();
		}

		/// Incoming
		$filters = array(
			'section'    => Request::getVar('section', ''),
			'category'   => Request::getCmd('category', ''),
			'search'     => Request::getVar('q', ''),
			'scope'      => $this->forum->get('scope'),
			'scope_id'   => $this->forum->get('scope_id'),
			'state'      => Category::STATE_PUBLISHED,
			'parent'     => 0,
			'access'     => User::getAuthorisedViewLevels()
		);

		$filters['sortby'] = Request::getWord('sortby', 'activity');
		switch ($filters['sortby'])
		{
			case 'title':
				$filters['sort'] = 'sticky` DESC, `title';
				$filters['sort_Dir'] = strtoupper(Request::getVar('sortdir', 'ASC'));
			break;

			case 'replies':
				$filters['sort'] = 'sticky` DESC, `rgt';
				$filters['sort_Dir'] = strtoupper(Request::getVar('sortdir', 'DESC'));
			break;

			case 'created':
				$filters['sort'] = 'sticky` DESC, `created';
				$filters['sort_Dir'] = strtoupper(Request::getVar('sortdir', 'DESC'));
			break;

			case 'activity':
			default:
				$filters['sort'] = 'sticky` DESC, `activity';
				$filters['sort_Dir'] = strtoupper(Request::getVar('sortdir', 'DESC'));
			break;
		}

		// Section
		$section = Section::all()
			->whereEquals('alias', $filters['section'])
			->whereEquals('scope', $this->forum->get('scope'))
			->whereEquals('scope_id', $this->forum->get('scope_id'))
			->row();
		if (!$section->get('id'))
		{
			App::abort(404, Lang::txt('COM_FORUM_SECTION_NOT_FOUND'));
		}

		// Get the category
		$category = Category::all()
			->whereEquals('alias', $filters['category'])
			->whereEquals('scope', $this->forum->get('scope'))
			->whereEquals('scope_id', $this->forum->get('scope_id'))
			->row();
		if (!$category->get('id'))
		{
			App::abort(404, Lang::txt('COM_FORUM_CATEGORY_NOT_FOUND'));
		}

		// Get authorization
		$this->_authorize('category');
		$this->_authorize('thread');

		$threads = $category->threads()
			->select("*, (CASE WHEN last_activity != '0000-00-00 00:00:00' THEN last_activity ELSE created END)", 'activity')
			->whereEquals('state', $filters['state'])
			->whereIn('access', $filters['access'])
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated()
			->rows();

		return $this->view('display', 'categories')
			->set('option', $this->option)
			->set('course', $this->course)
			->set('offering', $this->offering)
			->set('config', $this->params)
			->set('forum', $this->forum)
			->set('section', $section)
			->set('category', $category)
			->set('threads', $threads)
			->set('filters', $filters)
			->setErrors($this->getErrors())
			->loadTemplate();
	}

	/**
	 * Show a form for editing a category
	 *
	 * @param   object  $category
	 * @return  string
	 */
	public function editcategory($category=null)
	{
		if (!$this->course->access('manage', 'offering'))
		{
			return $this->panel();
		}

		if (User::isGuest())
		{
			$return = Route::url($this->base . '&unit=manage');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		// Get the section
		$section = Section::all()
			->whereEquals('alias', Request::getVar('section', ''))
			->whereEquals('scope', $this->forum->get('scope'))
			->whereEquals('scope_id', $this->forum->get('scope_id'))
			->row();

		// Incoming
		if (!is_object($category))
		{
			$category = Category::all()
				->whereEquals('alias', Request::getVar('category', ''))
				->whereEquals('scope', $this->forum->get('scope'))
				->whereEquals('scope_id', $this->forum->get('scope_id'))
				->row();
		}

		$this->_authorize('category', $category->get('id'));

		if ($category->isNew())
		{
			$category->set('created_by', User::get('id'));
			$category->set('section_id', $section->get('id'));
		}
		elseif ($category->get('created_by') != User::get('id') && !$this->params->get('access-create-category'))
		{
			App::redirect(
				Route::url($this->base . '&unit=manage')
			);
			return;
		}

		return $this->view('edit', 'categories')
			->set('option', $this->option)
			->set('course', $this->course)
			->set('offering', $this->offering)
			->set('config', $this->params)
			->set('forum', $this->forum)
			->set('section', $section)
			->set('category', $category)
			->setErrors($this->getErrors())
			->loadTemplate();
	}

	/**
	 * Save a category
	 *
	 * @return  void
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

		// Instantiate a category
		$category = Category::oneOrNew($fields['id'])->set($fields);

		// Double-check that the user is authorized
		$this->_authorize('category', $category->get('id'));

		if (!$this->params->get('access-edit-category'))
		{
			// Set the redirect
			App::redirect(
				Route::url($this->base . '&unit=manage')
			);
		}

		$category->set('closed', (isset($fields['closed']) && $fields['closed']) ? 1 : 0);

		// Forge an alias from the title
		if ($category->get('alias') == '')
		{
			$alias = $category->automaticAlias(array('title' => $category->get('title')));
			$category->set('alias', $alias);
		}

		// Check for alias duplicates within section?
		if (!$category->isUnique())
		{
			$category->set('alias', ''); //reset alias
			$category->set('section_id', (int) $category->get('section_id'));
			Request::setVar('section_id', $category->get('section_id'));

			Notify::error(Lang::txt('PLG_COURSES_FORUM_ERROR_CATEGORY_ALREADY_EXISTS'), 'courses_forum');
			return $this->editcategory($category);
		}

		// Store new content
		if (!$category->save())
		{
			Notify::error($category->getError(), 'courses_forum');
			return $this->editcategory($category);
		}

		// Log activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($fields['id'] ? 'updated' : 'created'),
				'scope'       => 'forum.category',
				'scope_id'    => $category->get('id'),
				'description' => Lang::txt('PLG_COURSES_FORUM_ACTIVITY_CATEGORY_' . ($fields['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . Route::url($this->base) . '">' . $category->get('title') . '</a>'),
				'details'     => array(
					'title' => $category->get('title'),
					'url'   => Route::url($this->base)
				)
			],
			'recipients' => array(
				['course', $this->offering->get('id')],
				['forum.' . $this->forum->get('scope'), $this->forum->get('scope_id')],
				['forum.section', $category->get('section_id')],
				['user', $category->get('created_by')]
			)
		]);

		// Set the redirect
		App::redirect(
			Route::url($this->base . '&unit=manage')
		);
	}

	/**
	 * Delete a category
	 *
	 * @return  void
	 */
	public function deletecategory()
	{
		if (!$this->course->access('manage', 'offering'))
		{
			return $this->panel();
		}

		// Load the category
		$category = Category::all()
			->whereEquals('alias', Request::getVar('category', ''))
			->whereEquals('scope', $this->forum->get('scope'))
			->whereEquals('scope_id', $this->forum->get('scope_id'))
			->row();

		// Incoming
		if (!$category->get('id'))
		{
			App::redirect(
				Route::url($this->base . '&unit=manage'),
				Lang::txt('PLG_COURSES_DISCUSSIONS_MISSING_ID'),
				'error'
			);
			return;
		}

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

		// Set the category to "deleted"
		$category->set('state', $category::STATE_DELETED);

		if (!$category->save())
		{
			App::redirect(
				Route::url($this->base . '&unit=manage'),
				$category->getError(),
				'error'
			);
			return;
		}

		// Log activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'deleted',
				'scope'       => 'forum.category',
				'scope_id'    => $category->get('id'),
				'description' => Lang::txt('PLG_COURSES_DISCUSSIONS_ACTIVITY_CATEGORY_DELETED', '<a href="' . Route::url($this->base) . '">' . $category->get('title') . '</a>'),
				'details'     => array(
					'title' => $category->get('title'),
					'url'   => Route::url($this->base)
				)
			],
			'recipients' => array(
				['course', $this->offering->get('id')],
				['forum.' . $this->forum->get('scope'), $this->forum->get('scope_id')],
				['forum.section', $category->get('section_id')],
				['user', $category->get('created_by')]
			)
		]);

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

		// Incoming
		$filters = array(
			'limit'    => Request::getInt('limit', 25),
			'start'    => Request::getInt('limitstart', 0),
			'section'  => Request::getVar('section', $this->offering->get('alias')),
			'category' => Request::getCmd('category', ''),
			'thread'   => Request::getInt('thread', 0),
			'scope'    => $this->forum->get('scope'),
			'scope_id' => $this->forum->get('scope_id'),
			'state'    => Post::STATE_PUBLISHED,
			'access'   => User::getAuthorisedViewLevels()
		);

		// Section
		$section = Section::all()
			->whereEquals('alias', $filters['section'])
			->whereEquals('scope', $this->forum->get('scope'))
			->whereEquals('scope_id', $this->forum->get('scope_id'))
			->row();
		if (!$section->get('id'))
		{
			App::abort(404, Lang::txt('PLG_COURSES_DISCUSSIONS_ERROR_SECTION_NOT_FOUND'));
		}

		// Category
		$category = Category::all()
			->whereEquals('alias', $filters['category'])
			->whereEquals('scope', $this->forum->get('scope'))
			->whereEquals('scope_id', $this->forum->get('scope_id'))
			->row();
		if (!$category->get('id'))
		{
			App::abort(404, Lang::txt('PLG_COURSES_DISCUSSIONS_ERROR_CATEGORY_NOT_FOUND'));
		}

		$filters['category_id'] = $category->get('id');

		// Load the topic
		$thread = Post::oneOrFail($filters['thread']);
		$filters['object_id'] = $thread->get('object_id');

		// Make sure thread belongs to this group
		if ($thread->get('scope_id') != $this->forum->get('scope_id')
		 || $thread->get('scope') != $this->forum->get('scope'))
		{
			App::abort(404, Lang::txt('PLG_COURSES_DISCUSSIONS_ERROR_THREAD_NOT_FOUND'));
			return;
		}

		// Redirect if the thread is soft-deleted
		if ($thread->get('state') == $thread::STATE_DELETED)
		{
			App::redirect(
				Route::url($this->base),
				Lang::txt('PLG_COURSES_DISCUSSIONS_ERROR_THREAD_NOT_FOUND'),
				'error'
			);
			return;
		}

		$filters['state'] = array(1, 3);

		// Get authorization
		$this->_authorize('category', $category->get('id'));
		$this->_authorize('thread', $thread->get('id'));
		$this->_authorize('post');

		// If the access is anything beyond public,
		// make sure they're logged in.
		if (User::isGuest() && !in_array($thread->get('access'), User::getAuthorisedViewLevels()))
		{
			$return = Route::url($this->base, false, true);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return))
			);
			return;
		}

		$unit = $this->offering->unit($filters['category']);
		$lecture = $unit->assetgroup($filters['object_id']);

		$view = $this->view('display', 'threads')
			->set('option', $this->option)
			->set('course', $this->course)
			->set('offering', $this->offering)
			->set('unit', $unit)
			->set('lecture', $lecture)
			->set('config', $this->params)
			->set('forum', $this->forum)
			->set('section', $section)
			->set('category', $category)
			->set('thread', $thread)
			->set('filters', $filters)
			->setErrors($this->getErrors());

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
		$id       = Request::getInt('post', 0);
		$category = Request::getVar('category', '');
		$section  = Request::getVar('section', '');

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

		// Get the category
		$category = Category::all()
			->whereEquals('alias', $category)
			->whereEquals('scope', $this->forum->get('scope'))
			->whereEquals('scope_id', $this->forum->get('scope_id'))
			->row();
		if (!$category->get('id'))
		{
			App::abort(404, Lang::txt('PLG_GROUPS_FORUM_ERROR_CATEGORY_NOT_FOUND'));
		}

		// Incoming
		if (!is_object($post))
		{
			$post = Post::oneOrNew($id);
		}

		// Get authorization
		$this->_authorize('thread', $id);

		if ($post->isNew())
		{
			$post->set('scope', $this->forum->get('scope'));
			$post->set('created_by', User::get('id'));
		}
		elseif ($post->get('created_by') != User::get('id') && !$this->params->get('access-edit-thread'))
		{
			App::redirect(Route::url($this->base . '&unit=manage&b=' . $section . '&c=' . $category));
		}

		return $this->view('edit', 'threads')
			->set('option', $this->option)
			->set('course', $this->course)
			->set('offering', $this->offering)
			->set('config', $this->params)
			->set('forum', $this->forum)
			->set('section', $section)
			->set('category', $category)
			->set('post', $post)
			->set('name', $this->_name)
			->setErrors($this->getErrors())
			->loadTemplate();
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

		// Bind data
		$post = Post::oneOrNew($fields['id']);

		// Double comment?
		$double = Post::all()
			->whereEquals('object_id', $post->get('object_id'))
			->whereEquals('scope', $post->get('scope'))
			->whereEquals('scope_id', $post->get('scope_id'))
			->whereEquals('created_by', $post->get('created_by'))
			->whereEquals('comment', $post->get('comment'))
			->row();

		if ($double->get('id'))
		{
			$post->set($double->toArray());
		}

		$post->set($fields);

		// Load the category
		$category = Category::oneOrFail($post->get('category_id'));

		if (!$post->get('object_id') && $category->get('object_id'))
		{
			$post->set('object_id', $category->get('object_id'));
		}

		// Store new content
		if (!$post->save())
		{
			Notify::error($post->getError());
			return $this->editthread($post);
		}

		// Upload file
		if (!$this->upload($post->get('thread', $post->get('id')), $post->get('id')))
		{
			Notify::error($this->getError());
			return $this->editthread($post);
		}

		// Save tags
		$post->tag(Request::getVar('tags', '', 'post'), User::get('id'));

		$thread = $post->get('thread');

		// Being called through AJAX?
		if ($no_html)
		{
			// Set the thread
			Request::setVar('thread', $thread);
			// Is this a new post in a thread or new thread entirely?
			if (!$post->get('parent'))
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
				$unit = $this->course->offering()->unit($category->get('alias'));

				$lecture = new \Components\Courses\Models\Assetgroup($post->get('object_id'));
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

		// Load the post
		$post = Post::oneOrFail($id);

		// Make the sure the category exist
		if (!$post->get('id'))
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

		// Trash the post
		// Note: this will carry through to all replies
		//       and attachments
		$post->set('state', $post::STATE_DELETED);

		if (!$post->save())
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
	 * @param   integer  $thread_id  Directory to upload files to
	 * @param   integer  $post_id    Post ID
	 * @return  boolean
	 */
	public function upload($thread_id, $post_id)
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return false;
		}

		if (!$listdir)
		{
			$this->setError(Lang::txt('PLG_COURSES_DISCUSSIONS_NO_UPLOAD_DIRECTORY'));
			return false;
		}

		// Instantiate an attachment record
		$attachment = Attachment::oneOrNew(Request::getInt('attachment', 0));
		$attachment->set('description', trim(Request::getVar('description', '')));
		$attachment->set('parent', $thread_id);
		$attachment->set('post_id', $post_id);
		if ($attachment->isNew())
		{
			$attachment->set('state', Attachment::STATE_PUBLISHED);
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file || !isset($file['name']) || !$file['name'])
		{
			if ($attachment->get('id'))
			{
				// Only updating the description
				if (!$attachment->save())
				{
					$this->setError($attachment->getError());
					return false;
				}
			}
			return true;
		}

		// Upload file
		if (!$attachment->upload($file['name'], $file['tmp_name']))
		{
			$this->setError($attachment->getError());
		}

		// Save entry
		if (!$attachment->save())
		{
			$this->setError($attachment->getError());
		}

		return true;
	}

	/**
	 * Serves up files only after passing access checks
	 *
	 * @return  void
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
		if (!$post_id)
		{
			$attach = Attachment::oneByThread($thread_id, $file);
		}
		else
		{
			$attach = Attachment::oneByPost($post_id);
		}

		if (!$attach->get('filename'))
		{
			App::abort(404, Lang::txt('PLG_COURSES_FORUM_FILE_NOT_FOUND'));
		}

		// Get the parent ticket the file is attached to
		$post = $attach->post();

		if (!$post->get('id') || $post->get('state') == $post::STATE_DELETED)
		{
			App::abort(404, Lang::txt('PLG_COURSES_FORUM_POST_NOT_FOUND'));
		}

		// Load ACL
		$this->_authorize('thread', $post->get('thread'));

		// Ensure the user is authorized to view this file
		if (!$this->course->access('view'))
		{
			App::abort(403, Lang::txt('PLG_COURSES_DISCUSSIONS_NOT_AUTH_FILE'));
		}

		// Get the configured upload path
		$filename = $attach->path();

		// Ensure the file exist
		if (!file_exists($filename))
		{
			App::abort(404, Lang::txt('PLG_COURSES_FILE_NOT_FOUND') . ' ' . substr($filename, strlen(PATH_ROOT)));
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

		exit;
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
	 * Reorder a section
	 *
	 * @param   integer  $dir  Direction
	 * @return  void
	 */
	public function reorder($dir=1)
	{
		if (!$this->course->access('manage', 'offering'))
		{
			return $this->panel();
		}

		// Get the section
		$section = Section::all()
			->whereEquals('alias', Request::getVar('section', ''))
			->whereEquals('scope', 'course')
			->whereEquals('scope_id', $this->offering->get('id'))
			->row();

		// Move the section
		if (!$section->move($dir))
		{
			Notify::error($section->getError());
		}

		// Record the activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => 'reordered',
				'scope'       => 'forum.section',
				'scope_id'    => $section->get('id'),
				'description' => Lang::txt('PLG_COURSES_FORUM_ACTIVITY_SECTION_REORDERED', '<a href="' . Route::url($this->base) . '">' . $section->get('title') . '</a>'),
				'details'     => array(
					'title' => $section->get('title'),
					'url'   => Route::url($this->base)
				)
			],
			'recipients' => array(
				['course', $this->offering->get('id')],
				['forum.course', $this->offering->get('id')],
				['forum.section', $section->get('id')]
			)
		]);

		// Redirect to main lsiting
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

		$sections = array();
		foreach ($course->offerings() as $offering)
		{
			if (!$offering->exists())
			{
				continue;
			}

			$sec = Section::all()
				->whereEquals('scope', 'course')
				->whereEquals('scope_id', $offering->get('id'))
				->rows();

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
				$categories = $section->categories()->rows();

				if ($categories->count())
				{
					// Build a list of category IDs
					foreach ($categories as $category)
					{
						$log .= 'forum.section.' . $section->get('id') . '.category.' . $category->get('id') . '.post' . "\n";
						$log .= 'forum.section.' . $section->get('id') . '.category.' . $category->get('id') . "\n";
					}
				}

				$log .= 'forum.section.' . $section->get('id') . ' ' . "\n";

				// Set the section to "deleted"
				// Set all the categories to "deleted"
				// Set all the threads/posts in all the categories to "deleted"
				$section->set('state', $section::STATE_DELETED);

				if (!$section->save())
				{
					$this->setError($sModel->getError());
					return '';
				}
			}
		}
		else
		{
			$log .= Lang::txt('PLG_COURSES_DISCUSSIONS_NO_RESULTS')."\n";
		}

		return $log;
	}
}
