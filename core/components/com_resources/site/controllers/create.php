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

namespace Components\Resources\Site\Controllers;

use Components\Resources\Models\Orm\Resource;
use Components\Resources\Models\License;
use Components\Resources\Models\Type;
use Components\Resources\Models\Elements;
use Components\Resources\Helpers\Tags;
use Components\Resources\Helpers\Html;
use Hubzero\Component\SiteController;
use Hubzero\Utility\String;
use Hubzero\User\Profile;
use Pathway;
use Request;
use Route;
use Event;
use Lang;
use User;
use Date;
use App;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'orm' . DS . 'resource.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'license.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'type.php');

/**
 * Resources controller for creating a resource
 */
class Create extends SiteController
{
	/**
	 * Container for steps
	 *
	 * @var array
	 */
	public $steps = array('Type', 'Compose', 'Attach', 'Authors', 'Tags', 'Review');

	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('discard', 'delete');
		$this->registerTask('remove', 'delete');
		$this->registerTask('start', 'draft');

		// Get the task at hand
		$task = Request::getVar('task', '');
		$this->step = Request::getInt('step', 0);
		if ($this->step && !$task)
		{
			Request::setVar('task', 'draft');
		}

		if (User::isGuest())
		{
			Request::setVar('task', 'login');
		}

		$row = Resource::oneOrNew(Request::getInt('id', 0));

		// Build the title
		$this->_buildTitle($row);

		// Build the pathway
		$this->_buildPathway($row);

		parent::execute();
	}

	/**
	 * Method to set the document path
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function _buildPathway($row)
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($row->id && $row->published == 1)
		{
			Pathway::append(
				Lang::txt('COM_CONTRIBUTE_EDIT'),
				'index.php?option=' . $this->_option . '&task=new'
			);
		}
		else
		{
			Pathway::append(
				Lang::txt('COM_CONTRIBUTE_NEW'),
				'index.php?option=' . $this->_option . '&task=new'
			);
		}
		if ($this->_task)
		{
			Pathway::append(
				Lang::txt('COM_CONTRIBUTE' . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
		if ($this->step)
		{
			Pathway::append(
				Lang::txt('COM_CONTRIBUTE_STEP_NUMBER', $this->step) . ': ' . Lang::txt('COM_CONTRIBUTE_STEP_' . strtoupper($this->steps[$this->step])),
				'index.php?option=' . $this->_option . '&task=' . $this->_task . '&step=' . $this->step
			);
		}
	}

	/**
	 * Method to build and set the document title
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function _buildTitle($row)
	{
		$this->_title = Lang::txt(strtoupper($this->_option)) . ': ';
		if ($row->id && $row->published == 1)
		{
			$this->_title .= Lang::txt('COM_CONTRIBUTE_EDIT');
		}
		else
		{
			$this->_title .= Lang::txt('COM_CONTRIBUTE_NEW');
		}
		if ($this->_task)
		{
			$this->_title .= ': ' . Lang::txt('COM_CONTRIBUTE' . '_' . strtoupper($this->_task));
		}
		if ($this->step)
		{
			$this->_title .= ': ' . Lang::txt('COM_CONTRIBUTE_STEP_NUMBER', $this->step) . ': ' . Lang::txt('COM_CONTRIBUTE_STEP_' . strtoupper($this->steps[$this->step]));
		}

		App::get('document')->setTitle($this->_title);
	}

	/**
	 * Redirect to the login page with the return set
	 *
	 * @return  void
	 */
	public function loginTask()
	{
		$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_controller), 'server');
		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn))
		);
		return;
	}

	/**
	 * Component landing page
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$this->view
			->set('title', $this->_title)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Check how many steps have been completed for a resource
	 *
	 * @param   integer  $id  Resource ID
	 * @return  void
	 */
	protected function _checkProgress($id)
	{
		$steps = $this->steps;
		$laststep = (count($steps) - 1);
		$stepchecks = array();

		$progress['submitted'] = 0;
		for ($i=1, $n=count($steps); $i < $n; $i++)
		{
			$check = 'step_' . $steps[$i] . '_check';
			$stepchecks[$steps[$i]] = $this->$check($id);

			if ($stepchecks[$steps[$i]])
			{
				$progress[$steps[$i]] = 1;
				if ($i == $laststep)
				{
					$progress['submitted'] = 1;
				}
			}
			else
			{
				$progress[$steps[$i]] = 0;
			}
		}
		$this->progress = $progress;
	}

	/**
	 * Call the current step
	 *
	 * @return  void
	 */
	public function draftTask()
	{
		// Determine the current step
		$steps = $this->steps;
		$step  = $this->step;
		if ($step > count($steps))
		{
			$step = count($steps);
		}

		// Determine the previous step
		$pre = ($step > 0) ? $step - 1 : 0;

		// Build name for methods
		$preprocess = 'step_' . strtolower($steps[$pre]) . '_process';
		$activestep = 'step_' . strtolower($steps[$step]);

		if (!method_exists($this, $activestep))
		{
			App::abort(404, Lang::txt('Unknown step.'));
		}

		// Set the layout to the current step
		$this->setView('steps', strtolower($steps[$step]));

		// assign some commonly used vars
		$this->view->config   = $this->config;
		$this->view->database = $this->database;
		$this->view->title    = $this->_title;
		$this->view->step     = $this->step;
		$this->view->steps    = $this->steps;

		// Is it a POST and the step field was set?
		// If so, it means we're at least past step 1
		if (isset($_POST['step']))
		{
			if (!method_exists($this, $preprocess))
			{
				App::abort(404, Lang::txt('Unknown step.'));
			}

			// Perform any preprocessing
			$this->$preprocess();
		}

		// Any errors?
		if (!$this->getError())
		{
			// Check the progress
			$this->_checkProgress(Request::getInt('id', 0));

			$this->view->progress = $this->progress;

			// Call current step
			$this->$activestep();
		}
	}

	/**
	 * Display a list of contributable resource types and let the user pick
	 *
	 * @return  void
	 */
	public function step_type()
	{
		$step = $this->step;
		$step++;

		// Get available resource types
		$types = Type::getMajorTypes();

		$this->view
			->set('group', Request::getVar('group', ''))
			->set('step', $step)
			->set('types', $types)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Display a form for composing the title, abstract, etc.
	 *
	 * @param   object  $row  Resource
	 * @return  void
	 */
	public function step_compose($row=null)
	{
		$group = Request::getVar('group', '');
		$type = Request::getInt('type', '');

		if ($type == '7')
		{
			App::redirect(Route::url('index.php?option=com_tools&task=create'), '', 'message', true);
		}

		$this->view->next_step = $this->step + 1;

		// Incoming
		$id = Request::getInt('id', 0);

		if (!is_object($row))
		{
			// Instantiate a new resource object
			$row = Resource::oneOrNew($id);

			if (!$id)
			{
				// Load the type and set the state
				$row->set('type', $type);
				$row->set('published', 2);
				$row->set('group_owner', $group);

				// generate a random number for file uploader
				$session = App::get('session');
				if (!$session->get('resources_temp_id'))
				{
					$row->set('id', '9999' . rand(1000,10000));
					$session->set('resources_temp_id', $row->get('id'));
				}
				else
				{
					$row->set('id', $session->get('resources_temp_id'));
				}
			}
		}

		// Output HTML
		$this->view
			->set('row', $row)
			->set('id', $id)
			->set('progress', $this->progress)
			->set('task', 'draft')
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Show form for adding attachments to a resource
	 *
	 * @param   boolean  $check
	 * @return  void
	 */
	public function step_attach($check = FALSE)
	{
		if ($this->view->getName() != 'steps')
		{
			$this->setView('steps', 'attachments');
		}

		if (!isset($this->view->database))
		{
			if ($check == TRUE)
			{
				foreach ($this->steps as $step => $name)
				{
					if ($name == 'Attach')
					{
						$this->step = $step;
					}
				}
			}
			else
			{
				$this->step = $this->step;
			}

			$this->view->config   = $this->config;
			$this->view->database = $this->database;
			$this->view->title    = $this->_title;
			$this->view->step     = $this->step;
			$this->view->steps    = $this->steps;
			$this->view->progress = $this->progress;
		}

		// Incoming
		$id = Request::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			App::abort(404, Lang::txt('COM_CONTRIBUTE_NO_ID'));
		}

		// Load the resource
		$row = Resource::oneOrFail($id);

		// Output HTML
		$this->view
			->set('row', $row)
			->set('id', $id)
			->set('next_step', $this->step + 1)
			->set('task', 'draft')
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Show form for adding authors to a resource
	 *
	 * @return  void
	 */
	public function step_authors()
	{
		// Incoming
		$id = Request::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			App::abort(404, Lang::txt('COM_CONTRIBUTE_NO_ID'));
		}

		// Load the resource
		$row = Resource::oneOrFail($id);

		// Get groups
		$profile = \Hubzero\User\Profile::getInstance(User::get('id'));
		$groups = $profile->getGroups('members');

		$this->_checkProgress($id);

		// Output HTML
		$this->view
			->set('row', $row)
			->set('id', $id)
			->set('groups', $groups)
			->set('next_step', $this->step + 1)
			->set('task', 'draft')
			->set('progress', $this->progress)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Recursive method for loading hierarchical focus areas (tags)
	 *
	 * @param   integer  $id            Resource type ID
	 * @param   array    $labels        Tags
	 * @param   integer  $parent_id     Tag ID
	 * @param   string   $parent_label  Tag
	 * @return  void
	 */
	private function _loadFocusAreas($type, $labels = null, $parent_id = NULL, $parent_label = NULL)
	{
		if (is_null($labels))
		{
			$this->database->setQuery(
				'SELECT DISTINCT tag
				FROM #__focus_area_resource_type_rel fr
				INNER JOIN #__focus_areas f ON f.id = fr.focus_area_id
				INNER JOIN #__tags t ON t.id = f.tag_id
				WHERE fr.resource_type_id = ' . $type
			);
			if (!($labels = $this->database->loadColumn()))
			{
				return array();
			}
			$labels = '\'' . implode('\', \'', array_map(array($this->database, 'escape'), $labels)) . '\'';
		}

		$this->database->setQuery(
			$parent_id
				// get tags labeled focus area and parented by the tag identified by $parent_id
				? 'SELECT DISTINCT t.raw_tag AS label, t2.id, t2.tag, t2.raw_tag, t2.description
					FROM #__tags t
					INNER JOIN #__tags_object to1 ON to1.tbl = \'tags\' AND to1.tagid = t.id AND to1.label = \'label\'
					INNER JOIN #__tags_object to2 ON to2.tbl = \'tags\' AND to2.label = \'parent\' AND to2.objectid = to1.objectid
						AND to2.tagid = ' . $parent_id . '
					INNER JOIN #__tags t2 ON t2.id = to1.objectid
					WHERE t.raw_tag = ' . $this->database->quote($parent_label) . '
					ORDER BY CASE WHEN t2.raw_tag LIKE \'other%\' THEN 1 ELSE 0 END, t2.raw_tag'
				// get tags that are labeled focus areas that are not also a parent of another tag labeled as a focus area
				: 'SELECT DISTINCT t.raw_tag AS label, t2.id, t2.tag, t2.raw_tag, t2.description
					FROM #__tags t
					LEFT JOIN #__tags_object to1 ON to1.tagid = t.id AND to1.label = \'label\' AND to1.tbl = \'tags\'
					INNER JOIN #__tags t2 ON t2.id = to1.objectid
					WHERE t.tag IN (' . $labels . ') AND (
						SELECT COUNT(*)
						FROM #__tags_object to2
						INNER JOIN #__tags_object to3 ON to3.tbl = \'tags\' AND to3.label = \'label\' AND to3.objectid = to2.tagid
						INNER JOIN #__tags t3 ON t3.id = to3.tagid AND t3.tag IN (' . $labels . ')
						WHERE to2.tbl = \'tags\' AND to2.label = \'parent\' AND to2.objectid = t2.id
						LIMIT 1
					) = 0
					ORDER BY t.tag, CASE WHEN t2.raw_tag LIKE \'other%\' THEN 1 ELSE 0 END, t2.raw_tag'
		);
		$fas = $this->database->loadAssocList('raw_tag');
		foreach ($fas as &$fa)
		{
			$fa['children'] = $this->_loadFocusAreas($type, $labels, $fa['id'], $fa['label']);
		}
		return $fas;
	}

	/**
	 * Show form for adding tags to an entry
	 *
	 * @param   array  $existing
	 * @return  void
	 */
	public function step_tags($existing = array())
	{
		if ($this->view->getName() != 'steps')
		{
			$this->setView('steps', 'tags');
		}

		// Incoming
		$id = Request::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			App::abort(404, Lang::txt('COM_CONTRIBUTE_NO_ID'));
		}

		// Check the progress
		$this->_checkProgress($id);

		// Load the resource
		$row = Resource::oneOrFail($id);

		// Get focus areas
		$this->database->setQuery('SELECT type FROM `#__resources` WHERE id = ' . $this->database->quote($id));
		$fas = $this->_loadFocusAreas($this->database->loadResult());
		$focusareas = array();
		foreach ($fas as $tag => $fa)
		{
			if (!isset($focusareas[$fa['label']]))
			{
				$focusareas[$fa['label']] = array();
			}
			$focusareas[$fa['label']][$tag] = $fa;
		}

		// Get all the tags on this resource
		$tagcloud = new Tags($id);
		$tags_men = $tagcloud->tags();

		$mytagarray = array();
		foreach ($tags_men as $tag_men)
		{
			$mytagarray[] = $tag_men->get('raw_tag');
		}
		$tags = implode(', ', $mytagarray);

		if (!$tags)
		{
			$tags = Request::getVar('tags', '');
		}

		if ($err = Request::getInt('err', 0))
		{
			$this->setError(Lang::txt('Please select one of the focus areas.'));
		}

		// Output HTML
		$this->view
			->set('row', $row)
			->set('id', $id)
			->set('tags', $tags)
			->set('fas', $focusareas)
			->set('next_step', $this->step + 1)
			->set('task', 'draft')
			->set('progress', $this->progress)
			->set('existing', $existing)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Show final review form for setting license and agreeing to terms of submission
	 *
	 * @return  void
	 */
	public function step_review()
	{
		if ($this->view->getName() != 'steps')
		{
			$this->setView('steps', 'review');
		}
		if (!isset($this->view->database))
		{
			$this->view->config   = $this->config;
			$this->view->database = $this->database;
			$this->view->title    = $this->_title;
			$this->view->step     = $this->step;
			$this->view->steps    = $this->steps;
			$this->view->progress = $this->progress;
		}

		// Incoming
		$id = Request::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			App::abort(404, Lang::txt('COM_CONTRIBUTE_NO_ID'));
		}

		// Load resource info
		$row = Resource::oneOrFail($id);

		$usersgroups = array();
		if (!User::isGuest())
		{
			$xgroups = \Hubzero\User\Helper::getGroups(User::get('id'), 'all');
			// Get the groups the user has access to
			$usersgroups = $this->_getUsersGroups($xgroups);
		}

		// Output HTML
		$licenses = License::all()
			->whereEquals('name', 'custom' . $id)
			->orWhere('name', 'NOT LIKE', 'custom%')
			->ordered()
			->rows();

		$this->view
			->set('row', $row)
			->set('id', $id)
			->set('licenses', $licenses)
			->set('usersgroups', $usersgroups)
			->set('next_step', $this->step + 1)
			->set('task', 'submit')
			->set('progress', $this->progress)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Generate an array of just group aliases
	 *
	 * @param   array  $groups  Array of group objects
	 * @return  array
	 */
	private function _getUsersGroups($groups)
	{
		$arr = array();
		if (!empty($groups))
		{
			foreach ($groups as $group)
			{
				if ($group->regconfirmed)
				{
					$arr[] = $group->cn;
				}
			}
		}
		return $arr;
	}

	/**
	 * Process the type step
	 *
	 * @return  void
	 */
	public function step_type_process()
	{
		// do nothing
	}

	/**
	 * Process the compose step
	 *
	 * @return  void
	 */
	public function step_compose_process()
	{
		// Initiate extended database class
		$fields = Request::getVar('fields', array(), 'post');

		$row = Resource::oneOrNew($fields['id'])->set($fields);

		$isNew = $row->id < 1 || substr($row->id, 0, 4) == '9999';

		$row->created    = ($row->created)    ? $row->created    : Date::toSql();
		$row->created_by = ($row->created_by) ? $row->created_by : User::get('id');

		// Set status to "composing"
		if ($isNew)
		{
			$row->published = 2;
		}
		else
		{
			$row->published = ($row->published ?: 2);
		}
		$row->publish_up   = ($row->publish_up   && $row->publish_up   != '0000-00-00 00:00:00' ? $row->publish_up : Date::toSql());
		$row->publish_down = ($row->publish_down && $row->publish_down != '0000-00-00 00:00:00' ? $row->publish_down : '0000-00-00 00:00:00');
		$row->modified     = Date::toSql();
		$row->modified_by  = User::get('id');
		$row->access       = ($row->access ?: 0);

		$row->fulltxt   = trim(preg_replace('/\\\/', "%5C", $row->fulltxt));
		$row->introtext = String::truncate(strip_tags($row->fulltxt), 500);

		// Get custom areas, add wrapper tags, and compile into fulltxt
		$type = Type::oneOrFail($row->type);

		include_once(PATH_CORE . DS . 'components' . DS . 'com_resources' . DS . 'models' . DS . 'elements.php');
		$elements = new Elements(array(), $type->customFields);
		$schema = $elements->getSchema();

		$fields = array();
		if (is_object($schema))
		{
			foreach ($schema->fields as $field)
			{
				$fields[$field->name] = $field;
			}
		}

		$nbtag = Request::getVar('nbtag', array(), 'post');
		$found = array();
		foreach ($nbtag as $tagname => $tagcontent)
		{
			$f = '';

			$row->fulltxt .= "\n" . '<nb:' . $tagname . '>';
			if (is_array($tagcontent))
			{
				$c = count($tagcontent);
				$num = 0;
				foreach ($tagcontent as $key => $val)
				{
					if (trim($val))
					{
						$num++;
					}
					$row->fulltxt .= '<' . $key . '>' . trim($val) . '</' . $key . '>';
				}
				if ($c == $num)
				{
					$f = 'found';
				}
			}
			else
			{
				$f = trim($tagcontent);
				if ($f)
				{
					$row->fulltxt .= trim($tagcontent);
				}
			}
			$row->fulltxt .= '</nb:' . $tagname . '>' . "\n";

			if (!$f && isset($fields[$tagname]) && $fields[$tagname]->required)
			{
				$this->setError(Lang::txt('COM_CONTRIBUTE_REQUIRED_FIELD_CHECK', $fields[$tagname]->label));
			}

			$found[] = $tagname;
		}

		foreach ($fields as $field)
		{
			if (!in_array($field->name, $found) && $field->required)
			{
				$found[] = $field->name;
				$this->setError(Lang::txt('COM_CONTRIBUTE_REQUIRED_FIELD_CHECK', $field->label));
			}
		}

		$row->title = preg_replace('/\s+/', ' ', $row->title);
		$row->title = $this->_txtClean($row->title);

		// Strip any scripting there may be
		if (trim($row->fulltxt))
		{
			$row->fulltxt    = \Components\Resources\Helpers\Html::stripStyles($row->fulltxt);
			$row->fulltxt    = $this->_txtClean($row->fulltxt);
			$row->footertext = $this->_txtClean($row->footertext);
		}

		// Fall back to step if any errors found
		if ($this->getError())
		{
			$this->step--;
			$this->view->step = $this->step;
			$this->view->setLayout('compose');
			return $this->step_compose($row);
		}

		// reset id
		if ($isNew)
		{
			$row->id = null;
		}

		// Store new content
		if (!$row->save())
		{
			$this->setError(Lang::txt('Error: Failed to store changes.'));
			$this->step--;
			$this->view->step = $this->step;
			$this->view->setLayout('compose');
			return $this->step_compose($row);
		}

		// build path to temp upload folder and future permanent folder
		$session = App::get('session');
		$created = Date::format('Y-m-d 00:00:00');
		$oldPath = $row->basepath() . Html::build_path($created, $session->get('resources_temp_id') ,'');
		$newPath = $row->filespace();

		// if we have a temp dir, move it to permanent location
		if (is_dir($oldPath))
		{
			\Filesystem::move($oldPath, $newPath);

			$old = DS . $session->get('resources_temp_id') . DS;
			$new = DS . $row->id . DS;

			// update all images in abstract
			$row->introtext = str_replace($old, $new, $row->introtext);
			$row->fulltxt   = str_replace($old, $new, $row->fulltxt);
			$row->store();

			// clear temp id
			$session->clear('resources_temp_id');
		}

		// Is it a new resource?
		if ($isNew)
		{
			// Automatically attach this user as the first author
			Request::setVar('pid', $row->id);
			Request::setVar('id', $row->id);
			Request::setVar('authid', User::get('id'));

			include_once(__DIR__ . DS . 'authors.php');
			$authors = new Authors();
			$authors->saveTask(0);
		}

		// Log activity
		$recipients = array(
			['resource', $row->get('id')],
			['user', $row->get('created_by')]
		);
		foreach ($row->authors()->where('authorid', '>', 0)->rows() as $author)
		{
			$recipients[] = ['user', $author->get('authorid')];
		}

		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => ($isNew ? 'updated' : 'created'),
				'scope'       => 'resource',
				'scope_id'    => $row->get('id'),
				'description' => Lang::txt('COM_RESOURCES_ACTIVITY_ENTRY_' . ($fields['id'] ? 'UPDATED' : 'CREATED'), '<a href="' . Route::url('index.php?option=com_resources&id=' . $row->get('id')) . '">' . $row->get('title') . '</a>'),
				'details'     => array(
					'title' => $row->get('title'),
					'url'   => Route::url('index.php?option=com_resources&id=' . $row->get('id'))
				)
			],
			'recipients' => $recipients
		]);
	}

	/**
	 * Process the attach step
	 *
	 * @return  void
	 */
	public function step_attach_process()
	{
		// do nothing
	}

	/**
	 * Process the authors step
	 *
	 * @return  void
	 */
	public function step_authors_process()
	{
		// Incoming
		$id = Request::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			return;
		}

		// Load the resource
		$row = Resource::oneOrFail($id);

		// Set the group and access level
		$row->set('group_owner', Request::getVar('group_owner', ''));
		$row->set('access', Request::getInt('access', 0));

		if ($row->get('access') > 2 && !$row->get('group_owner'))
		{
			$this->setError(Lang::txt('Please select a group to restrict access to.'));
			$this->step--;
			$this->view->set('step', $this->step);
			$this->view->setLayout('authors');
			return $this->step_authors();
		}

		// Store new content
		if (!$row->save())
		{
			$this->setError(Lang::txt('Error: Failed to store changes.'));
			$this->step--;
			$this->view->set('step', $this->step);
			$this->view->setLayout('authors');
			return $this->step_authors();
		}
	}

	/**
	 * Process the tags step
	 *
	 * @return  void
	 */
	public function step_tags_process()
	{
		$id = Request::getInt('id', 0);

		$this->database->setQuery(
			'SELECT 1 FROM #__author_assoc WHERE authorid = ' . User::get('id') . ' AND subtable = \'resources\' AND subid = ' . $id . '
			UNION
			SELECT 1 FROM #__resources WHERE id = ' . $id . ' AND (created_by = ' . User::get('id') . ' OR modified_by = ' . User::get('id') . ')
			UNION
			SELECT 1 FROM #__users u
			INNER JOIN #__user_usergroup_map cagam ON cagam.user_id = u.id
			INNER JOIN #__usergroups caag ON caag.id = cagam.group_id AND (caag.title = \'Super Administrator\' OR caag.title = \'Super Users\' OR caag.title = \'Administrator\')
			WHERE u.id = ' . User::get('id')
		);

		if (!$this->database->loadResult())
		{
			App::abort(403, Lang::txt('Forbidden'));
			return;
		}

		$tags = preg_split('/,\s*/', $_POST['tags']);
		$push = array();
		$map  = array();

		$this->database->setQuery(
			'SELECT fa.tag_id, t.raw_tag, fa.mandatory_depth AS minimum_depth, 0 AS actual_depth
			FROM #__focus_areas fa
			INNER JOIN #__tags t ON t.id = fa.tag_id
			INNER JOIN #__focus_area_resource_type_rel rtr ON rtr.focus_area_id = fa.id
			INNER JOIN #__resource_types rt ON rt.id = rtr.resource_type_id
			INNER JOIN #__resources r ON r.type = rt.id AND r.id = ' . $id . '
			WHERE fa.mandatory_depth IS NOT NULL AND fa.mandatory_depth > 0'
		);
		$fas = $this->database->loadAssocList('raw_tag');
		foreach ($_POST as $k => $vs)
		{
			if (!preg_match('/^tagfa/', $k))
			{
				continue;
			}
			if (!is_array($vs))
			{
				$vs = array($vs);
			}
			foreach ($vs as $v)
			{
				$norm_tag = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($v));
				if (isset($map[$norm_tag]))
				{
					continue;
				}
				$this->database->setQuery(
					'SELECT t2.raw_tag AS fa, t2.id AS label_id, t.id
					FROM #__tags t
					INNER JOIN #__tags_object to1 ON to1.tbl = \'tags\' AND to1.label = \'label\' AND to1.objectid = t.id
					INNER JOIN #__tags t2 ON t2.id = to1.tagid
					INNER JOIN #__focus_areas fa ON fa.tag_id = to1.tagid
					WHERE t.tag = ' . $this->database->quote($norm_tag)
				);
				if (($row = $this->database->loadAssoc()))
				{
					$push[] = array($v, $norm_tag, $row['fa'], $row['id'], $row['label_id']);
					$map[$norm_tag] = true;
				}
			}
		}

		$filtered = array();
		// only accept focus areas with parents if their parent is also checked
		foreach ($push as $idx => $tag)
		{
			$this->database->setQuery(
				'SELECT t.tag, t.id
				FROM #__tags_object to1
				INNER JOIN #__tags t ON t.id = to1.tagid
				INNER JOIN #__tags_object to2 ON to2.tagid = ' . $tag[4] . ' AND to2.tbl = \'tags\' AND to2.objectid = to1.tagid
				WHERE to1.objectid = ' . $tag[3] . ' AND to1.tbl = \'tags\' AND to1.label = \'parent\''
			);
			$any_match = false;
			$parent = array();
			$possible_parents = $this->database->loadAssocList();
			foreach ($possible_parents as $par)
			{
				if (isset($map[$par['tag']]))
				{
					$parent[] = $par;
					$any_match = true;
				}
			}
			if (!$possible_parents || $any_match)
			{
				$filtered[] = $tag;
				$parent_id = array();
				foreach ($parent as $par)
				{
					$parent_id[] = $par['id'];
				}
				if (isset($fas[$tag[2]]) && $fas[$tag[2]]['actual_depth'] < $fas[$tag[2]]['minimum_depth'])
				{
					// count depth if necessary to determine whether focus area constraints are satisified
					for ($depth = $parent ? 2 : 1; $parent_id && $fas[$tag[2]]['actual_depth'] < $fas[$tag[2]]['minimum_depth'] && $depth < $fas[$tag[2]]['minimum_depth']; ++$depth)
					{
						$this->database->setQuery(
							'SELECT t.id
							FROM #__tags_object to1
							INNER JOIN #__tags t ON t.id = to1.tagid
							INNER JOIN #__tags_object to2 ON to2.tagid = ' . $tag[4] . ' AND to2.tbl = \'tags\' AND to2.objectid = to1.tagid
							WHERE to1.objectid IN (' . implode(',', $parent_id) . ') AND to1.tbl = \'tags\' AND to1.label = \'parent\''
						);
						$parent_id = $this->database->loadColumn();
					}
					$fas[$tag[2]]['actual_depth'] = max($depth, $fas[$tag[2]]['actual_depth']);
				}
			}
			else
			{
				unset($map[$tag[1]]);
			}
		}
		$push = $filtered;

		foreach ($tags as $tag)
		{
			$norm_tag = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($tag));

			if (!$norm_tag || isset($map[$norm_tag]))
			{
				continue;
			}
			$push[] = array($tag, $norm_tag, null);
			$map[$norm_tag] = true;
		}
		foreach ($push as $idx => $tag)
		{
			$this->database->setQuery("SELECT raw_tag FROM `#__tags` WHERE tag = " . $this->database->quote($tag[1]));
			if (($raw_tag = $this->database->loadResult()))
			{
				$push[$idx][0] = $raw_tag;
			}
		}

		foreach ($fas as $lbl => $fa)
		{
			if ($fa['actual_depth'] < $fa['minimum_depth'])
			{
				$this->setError(
					$fa['minimum_depth'] == 1
						? 'Please ensure you have made a ' . $lbl . ' selection'
						: 'Please make selections for "' . $lbl . '" to a depth of at least ' . $fa['minimum_depth']
				);
				--$this->step;
				$this->view->step = $this->step;
				$this->view->setLayout('tags');
				return $this->step_tags($push);
			}
		}

		$tags = array();
		foreach ($push as $tag)
		{
			$tags[] = $tag[0];
		}
		$tags = implode(', ', $tags);

		$rt = new Tags($id);
		$this->database->setQuery('DELETE FROM `#__tags_object` WHERE tbl = \'resources\' AND objectid = ' . $id);
		$this->database->execute();
		foreach ($push as $tag)
		{
			$rt->add($tag[0], User::get('id'), 0, 1, ($tag[2] ? $tag[2] : ''));
		}
	}

	/**
	 * Final submission
	 *
	 * @return  void
	 */
	public function submitTask()
	{
		// Incoming
		$id = Request::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			App::abort(404, Lang::txt('COM_CONTRIBUTE_NO_ID'));
		}

		// Load resource info
		$resource = Resource::oneOrFail($id);

		// Set a flag for if the resource was already published or not
		$published = 0;
		if ($resource->get('published') != 2)
		{
			$published = 1;
		}

		// Check if a newly submitted resource was authorized to be published
		$authorized = Request::getInt('authorization', 0);
		if (!$authorized && !$published)
		{
			$this->setError(Lang::txt('COM_CONTRIBUTE_CONTRIBUTION_NOT_AUTHORIZED'));
			$this->_checkProgress($id);
			return $this->step_review();
		}

		// Is this a newly submitted resource?
		if (!$published)
		{
			$activity = 'submitted';

			// 0 = unpublished, 1 = published, 2 = composing, 3 = pending (submitted), 4 = deleted
			// Are submissions auto-approved?
			if ($this->config->get('autoapprove') == 1)
			{
				//checks if autoapproved content has children (configurable in options on backend)
				if ($this->config->get('autoapprove_content_check') == 1)
				{
					if ($resource->children()->total() < 1)
					{
						$this->setError(Lang::txt('COM_CONTRIBUTE_NO_CONTENT'));
						return $this->step_review();
					}
				}

				// Set status to published
				$resource->set('published', 1);
				$resource->set('publish_up', Date::toSql());

				$activity = 'published';
			}
			else
			{
				$apu = $this->config->get('autoapproved_users');
				$apu = explode(',', $apu);
				$apu = array_map('trim', $apu);

				if (in_array(User::get('username'), $apu))
				{
					// Set status to published
					$resource->set('published', 1);
					$resource->set('publish_up', Date::toSql());
				}
				else
				{
					// Set status to pending review (submitted)
					$resource->set('published', 3);
				}
			}

			// Get the resource's contributors
			$authors = $resource->authors()->rows();

			if ($authors->count() <= 0)
			{
				$this->setError(Lang::txt('COM_CONTRIBUTE_CONTRIBUTION_HAS_NO_AUTHORS'));
				$this->_checkProgress($id);
				return $this->step_review();
			}

			// Get any set emails that should be notified of ticket submission
			$defs = explode(',', $this->config->get('email_when_submitted', '{config.mailfrom}'));

			if (!empty($defs))
			{
				$message = new \Hubzero\Mail\Message();
				$message->setSubject(Config::get('sitename') . ' ' . Lang::txt('COM_RESOURCES_EMAIL_SUBJECT_NEW_SUBMISSION', $resource->id));
				$message->addFrom(
					Config::get('mailfrom'),
					Config::get('sitename') . ' ' . Lang::txt(strtoupper($this->_option))
				);

				// Plain text email
				$eview = new \Hubzero\Mail\View(array(
					'name'   => 'emails',
					'layout' => 'submitted_plain'
				));
				$eview->option     = $this->_option;
				$eview->controller = $this->_controller;
				$eview->resource   = $resource;
				$eview->delimiter  = '';

				$plain = $eview->loadTemplate();
				$plain = str_replace("\n", "\r\n", $plain);

				$message->addPart($plain, 'text/plain');

				// HTML email
				$eview->setLayout('submitted_html');

				$html = $eview->loadTemplate();
				$html = str_replace("\n", "\r\n", $html);

				$message->addPart($html, 'text/html');

				// Loop through the addresses
				foreach ($defs as $def)
				{
					$def = trim($def);

					// Check if the address should come from config
					if ($def == '{config.mailfrom}')
					{
						$def = Config::get('mailfrom');
					}

					// Check for a valid address
					if (\Hubzero\Utility\Validate::email($def))
					{
						// Send e-mail
						$message->setTo(array($def));
						$message->send();
					}
				}
			}

			// Log activity
			$recipients = array(
				['resource', $resource->get('id')],
				['user', $resource->get('created_by')]
			);
			foreach ($authors as $author)
			{
				if ($author->get('authorid') > 0)
				{
					$recipients[] = ['user', $author->get('authorid')];
				}
			}

			Event::trigger('system.logActivity', [
				'activity' => [
					'action'      => $activity,
					'scope'       => 'resource',
					'scope_id'    => $resource->get('title'),
					'description' => Lang::txt('COM_RESOURCES_ACTIVITY_ENTRY_' . strtoupper($activity), '<a href="' . Route::url($resource->link()) . '">' . $resource->get('title') . '</a>'),
					'details'     => array(
						'title' => $resource->get('title'),
						'url'   => Route::url($resource->link())
					)
				],
				'recipients' => $recipients
			]);
		}

		// Is this resource licensed under Creative Commons?
		if ($this->config->get('cc_license'))
		{
			$license = Request::getVar('license', '');

			if ($license == 'custom')
			{
				$license .= $resource->get('id');

				$licenseText = Request::getVar('license-text', '');

				if ($licenseText == '[ENTER LICENSE HERE]')
				{
					$this->setError(Lang::txt('Please enter a license.'));
					$this->_checkProgress($id);
					return $this->step_review();
				}

				$rl = License::oneOrNew($license);
				$rl->set('name', $license);
				$rl->set('text', $licenseText);
				$rl->set('info', $resource->get('id'));
				$rl->save();
			}

			// set license
			$params = new \Hubzero\Config\Registry($resource->get('params'));
			$params->set('license', $license);

			$resource->set('params', $params->toString());
		}

		// Save the resource
		$resource->save();

		// If a previously published resource, redirect to the resource page
		if ($published == 1)
		{
			App::redirect(
				Route::url($resource->link())
			);
			return;
		}

		// Output HTML
		$this->setView($this->_controller, 'thanks');

		$this->view
			->set('title', $this->_title)
			->set('config', $this->config)
			->set('resource', $resource)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Show a confirmation form for deleting a contribution
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Incoming
		$id = Request::getInt('id', 0);

		// Ensure we have an ID to work with
		if (!$id)
		{
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&task=new')
			);
			return;
		}

		// Load the resource
		$resource = Resource::oneOrNew($id);

		// Incoming step
		$step = Request::getVar('step', 1);

		// Perform step
		switch ($step)
		{
			case 1:
				// Check progress
				$this->_checkProgress($id);

				// Output HTML
				$this->view
					->set('title', $this->_title)
					->set('step', 'discard')
					->set('steps', $this->steps)
					->set('row', $resource)
					->set('id', $id)
					->set('progress', $this->progress)
					->setErrors($this->getErrors())
					->display();
			break;

			case 2:
				// Incoming confirmation flag
				$confirm = Request::getVar('confirm', '', 'post');

				// Did they confirm the deletion?
				if ($confirm != 'confirmed')
				{
					$this->setError(Lang::txt('Please confirm.'));

					// Check progress
					$this->_checkProgress($id);

					// Output HTML
					$this->view
						->set('title', $this->_title)
						->set('step', 'discard')
						->set('steps', $this->steps)
						->set('row', $resource)
						->set('id', $id)
						->set('progress', $this->progress)
						->setErrors($this->getErrors())
						->display();
					return;
				}

				// Check if the resource was "published"
				if ($resource->get('published') == 1)
				{
					// It was, so we can only mark it as "deleted"
					$resource->set('published', 4);

					if (!$resource->save())
					{
						App::abort(500, $resource->getError());
					}
				}
				else
				{
					// It wasn't. Attempt to delete the resource
					if (!$resource->destroy())
					{
						App::abort(500, $resource->getError());
					}
				}

				// Log activity
				$recipients = array(
					['resource', $resource->get('id')],
					['user', $resource->get('created_by')]
				);
				foreach ($resource->authors()->where('authorid', '>', 0)->rows() as $author)
				{
					$recipients[] = ['user', $author->get('authorid')];
				}

				Event::trigger('system.logActivity', [
					'activity' => [
						'action'      => 'deleted',
						'scope'       => 'resource',
						'scope_id'    => $resource->get('id'),
						'description' => Lang::txt('COM_RESOURCES_ACTIVITY_ENTRY_' . strtoupper($activity), '<a href="' . Route::url($resource->link()) . '">' . $resource->get('title') . '</a>'),
						'details'     => array(
							'title' => $resource->get('title'),
							'url'   => Route::url($resource->link())
						)
					],
					'recipients' => $recipients
				]);

				// Redirect to the start page
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&task=new')
				);
			break;
		}
	}

	/**
	 * Retract a submission
	 *
	 * @return  void
	 */
	public function retractTask()
	{
		// Incoming
		$id = Request::getInt('id', 0);

		// Ensure we have an ID to work with
		if ($id)
		{
			// Load the resource
			$resource = Resource::oneOrFail($id);

			// Check if it's in pending status
			if ($resource->get('published') == 3)
			{
				// Set it back to "draft" status
				$resource->set('published', 2);

				// Save changes
				$resource->save();
			}
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&task=new')
		);
	}

	/**
	 * Check if the type step is completed
	 *
	 * @param   integer  $id  Resource ID
	 * @return  void
	 */
	public function step_type_check($id)
	{
		// do nothing
	}

	/**
	 * Check if the compose step is completed
	 *
	 * @param   integer  $id  Resource ID
	 * @return  integer  # > 1 = step completed, 0 = not completed
	 */
	public function step_compose_check($id)
	{
		return $id;
	}

	/**
	 * Check if the attach step is completed
	 *
	 * @param   integer  $id  Resource ID
	 * @return  integer  # > 1 = step completed, 0 = not completed
	 */
	public function step_attach_check($id)
	{
		$total = 0;

		if ($id)
		{
			$resource = Resource::oneOrNew($id);
			$total = $resource->children()->total();
		}

		return $total;
	}

	/**
	 * Check if the authors step is completed
	 *
	 * @param   integer  $id  Resource ID
	 * @return  integer  # > 1 = step completed, 0 = not completed
	 */
	public function step_authors_check($id)
	{
		$contributors = 0;

		if ($id)
		{
			$resource = Resource::oneOrNew($id);
			$total = $resource->authors()->total();
		}

		return $contributors;
	}

	/**
	 * Check if the tags step is completed
	 *
	 * @param   integer  $id  Resource ID
	 * @return  integer  1 = step completed, 0 = not completed
	 */
	public function step_tags_check($id)
	{
		$rt = new Tags($id);
		$tags = $rt->tags()->count();

		if ($tags > 0)
		{
			return 1;
		}

		return 0;
	}

	/**
	 * Check if the review step is completed
	 *
	 * @param   integer  $id  Resource ID
	 * @return  integer  1 = step completed, 0 = not completed
	 */
	public function step_review_check($id)
	{
		$resource = Resource::oneOrNew($id);

		if ($resource->get('published') == 1)
		{
			return 1;
		}

		return 0;
	}

	/**
	 * Convert Microsoft characters and strip disallowed content
	 * This includes script tags, HTML comments, xhubtags, and style tags
	 *
	 * @param   string  &$text  Text to clean
	 * @return  string
	 */
	private function _txtClean(&$text)
	{
		// Handle special characters copied from MS Word
		$text = str_replace('“','"', $text);
		$text = str_replace('”','"', $text);
		$text = str_replace("’","'", $text);
		$text = str_replace("‘","'", $text);

		//$text = preg_replace('/{kl_php}(.*?){\/kl_php}/s', '', $text);
		//$text = preg_replace('/{.+?}/', '', $text);
		$text = preg_replace("'<style[^>]*>.*?</style>'si", '', $text);
		$text = preg_replace("'<script[^>]*>.*?</script>'si", '', $text);
		$text = preg_replace('/<!--.+?-->/', '', $text);

		return $text;
	}
}
