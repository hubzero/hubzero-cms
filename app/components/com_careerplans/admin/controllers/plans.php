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

namespace Components\Careerplans\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Careerplans\Models\Careerplan;
use Components\Careerplans\Models\Fieldset;
use Components\Careerplans\Models\Field;
use Request;
use Notify;
use Route;
use Event;
use User;
use Lang;
use Date;
use App;

/**
 * Career plans controller class
 */
class Plans extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Display a list of entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				-1,
				'int'
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'created'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'desc'
			)
		);

		$query = Careerplan::all()
			->including(['creator', function ($creator){
				$creator->select('*');
			}]);

		$a = $query->getTableName();
		$u = User::getInstance()->getTableName();

		$query
			->select($a . '.*')
			->join($u, $u . '.id', $a . '.user_id', 'left');

		if ($filters['search'])
		{
			$query->whereLike($u . '.name', strtolower((string)$filters['search']));
		}

		if ($filters['state'] >= 0)
		{
			$query->whereEquals($a . '.state', (int)$filters['state']);
		}

		// Get records
		$rows = $query
			->order($a . '.' . $filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @return  void
	 */
	public function summaryTask()
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		// Incoming
		$id = Request::getVar('id', array(0));
		if (is_array($id) && !empty($id))
		{
			$id = $id[0];
		}

		// Load the record
		$row = Careerplan::oneOrNew($id);

		$fieldsets = $row->summary();

		// Output the HTML
		$this->view
			->set('careerplan', $row)
			->set('fieldsets', $fieldsets)
			->setLayout('summary')
			->display();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load the article
			$row = Careerplan::oneOrNew($id);
		}

		$fieldsets = Fieldset::all()
			->ordered()
			->rows();

		if (!count($fieldsets))
		{
			$fieldsets = array(Fieldset::blank());
		}

		// Output the HTML
		$this->view
			->set('careerplan', $row)
			->set('fieldsets', $fieldsets)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		//
		// Career plan
		//
		$fields = Request::getVar('careerplan', array(), 'post', 'none', 2);

		// Initiate extended database class
		$careerplan = Careerplan::oneOrNew($fields['id'])->set($fields);

		$state = $careerplan->get('state');

		// Trigger before save event
		$isNew  = $careerplan->isNew();
		$result = Event::trigger('careerplans.onCareerplanBeforeSave', array(&$careerplan, $isNew));

		if (in_array(false, $result, true))
		{
			Notify::error($careerplan->getError());
			return $this->editTask($careerplan);
		}

		// Store content
		if (!$careerplan->save())
		{
			Notify::error($careerplan->getError());
			return $this->editTask($careerplan);
		}

		//
		// Answers
		//
		$answers = Request::getVar('questions', array(), 'post', 'none', 2);

		$fields = Field::all()
			->including(['options', function ($option){
				$option
					->select('*');
			}])
			->ordered()
			->rows();

		$field_ids = $fields->fieldsByKey('id');

		// Get any previous data for this set of questions
		$prev = $careerplan->answers()
			->whereIn('field_id', $field_ids)
			->ordered()
			->rows();

		$old = Careerplan::collect($prev);
		$answers = array_merge($old, $answers);

		// Compile data
		foreach ($answers as $key => $data)
		{
			if (isset($answers[$key]) && is_array($answers[$key]))
			{
				$answers[$key] = array_filter($answers[$key]);
			}

			// If there's an 'other' value
			// set the main field's value to it and remove the 'other' entry
			if (isset($answers[$key . '_other']) && trim($answers[$key . '_other']))
			{
				if (is_array($answers[$key]))
				{
					$answers[$key][] = $answers[$key . '_other'];
				}
				else
				{
					$answers[$key] = $answers[$key . '_other'];
				}

				unset($answers[$key . '_other']);
			}
		}

		\Hubzero\Form\Form::addFieldPath(dirname(dirname(__DIR__)) . '/models/fields');
		\Hubzero\Form\Form::addRulePath(dirname(dirname(__DIR__)) . '/models/rules');

		// Validate profile data
		$form = new \Hubzero\Form\Form('application', array('control' => 'questions'));
		$form->load(Field::toXml($fields, $answers));
		$form->bind(new \Hubzero\Config\Registry($answers));

		$errors = array(
			'_missing' => array(),
			'_invalid' => array()
		);

		if (!$form->validate($answers))
		{
			foreach ($form->getErrors() as $key => $error)
			{
				// Filter out fields
				if (!empty($field_to_check) && !in_array($key, $field_to_check))
				{
					continue;
				}

				if ($error instanceof \Hubzero\Form\Exception\MissingData)
				{
					$errors['_missing'][$key] = (string)$error;
				}

				$errors['_invalid'][$key] = (string)$error;

				$this->setError((string)$error);
			}

			if ($this->getError())
			{
				Notify::error(implode('<br />', $this->getErrors()));
				return $this->editTask($careerplan);
			}
		}

		// Trigger after save event
		Event::trigger('careerplans.onCareerplanAfterSave', array(&$careerplan, $isNew));

		// Notify of success
		Notify::success(Lang::txt('COM_CAREERPLANS_ENTRY_SAVED'));

		// Redirect to main listing or go back to edit form
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($careerplan);
		}

		$this->cancelTask();
	}

	/**
	 * Delete one or more entries
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$removed = 0;

		foreach ($ids as $id)
		{
			$entry = Careerplan::oneOrFail(intval($id));

			// Delete the entry
			if (!$entry->destroy())
			{
				Notify::error($entry->getError());
				continue;
			}

			// Trigger before delete event
			Event::trigger('careerplans.onCareerplanAfterDelete', array($id));

			$removed++;
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_CAREERPLANS_ENTRIES_DELETED', $removed));
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Download one or more records
	 *
	 * @return  void
	 */
	public function exportTask()
	{
		$skip = array('password', 'params', 'usertype');
		$keys = array(
			'name' => 'Name',
			'username' => 'Username',
			'email' => 'Email'
		);

		$attribs = Field::all()
			->where('type', '!=', 'paragraph')
			->order('fieldset_id', 'asc')
			->order('ordering', 'asc')
			->rows();

		foreach ($attribs as $attrib)
		{
			if ($attrib->get('type') == 'address')
			{
				$keys[$attrib->get('id')] = $attrib->get('label') . ' Street 1';
				$keys['_' . $attrib->get('id') . '_address2']  = $attrib->get('label') . ' Street 2';
				$keys['_' . $attrib->get('id') . '_city']      = $attrib->get('label') . ' City';
				$keys['_' . $attrib->get('id') . '_postal']    = $attrib->get('label') . ' Post Code';
				$keys['_' . $attrib->get('id') . '_region']    = $attrib->get('label') . ' Region';
				$keys['_' . $attrib->get('id') . '_country']   = $attrib->get('label') . ' Country';
				$keys['_' . $attrib->get('id') . '_latitude']  = $attrib->get('label') . ' Latitude';
				$keys['_' . $attrib->get('id') . '_longitude'] = $attrib->get('label') . ' Longitude';
				continue;
			}

			$keys[$attrib->get('id')] = $attrib->get('label');
		}

		$results = Event::trigger('careerplans.onExportKeys', array($keys));
		foreach ($results as $result)
		{
			if (!is_array($result))
			{
				continue;
			}

			foreach ($result as $k => $v)
			{
				$keys[$k] = $v;
			}
		}

		// Get request vars
		$delimiter = Request::getVar('delimiter', ',');

		$path = Config::get('tmp_path') . DS . 'careerplans.csv';
		$file = fopen($path, 'w');
		fputcsv($file, $keys);

		// Get filters
		$filters = array(
			'search'       => urldecode(Request::getVar('search', '')),
			'sort'         => Request::getWord('filter_order', 'created'),
			'sort_Dir'     => Request::getWord('filter_order_Dir', 'DESC'),
			'state'        => Request::getInt('state', 1)
		);

		$query = Careerplan::all()
			->including(['creator', function ($creator){
				$creator->select('*');
			}]);

		$a = $query->getTableName();
		$u = User::getInstance()->getTableName();

		$query
			->select($u . '.name')
			->select($u . '.username')
			->select($u . '.email')
			->select($a . '.*')
			->join($u, $u . '.id', $a . '.created_by', 'left');

		if ($filters['search'])
		{
			$query->whereLike($u . 'name', strtolower((string)$filters['search']));
		}

		if ($filters['state'] >= 0)
		{
			$query->whereEquals($a . '.state', (int)$filters['state']);
		}

		// Get records
		$rows = $query
			->order($a . '.' . $filters['sort'], $filters['sort_Dir'])
			->rows();

		// Convert to array and bind to object below
		// This may seem counter-intuitive but it's for
		// performance reasons. Otherwise, all the circular
		// references eat up memery.
		$rows = $rows->toArray();

		// Gather up member information
		foreach ($rows as $row)
		{
			$careerplan = Careerplan::blank()->set($row);

			$answers = $careerplan->answers()->order('id', 'asc')->rows();

			$tmp = array(
				'name'     => $careerplan->creator->get('name'),
				'username' => $careerplan->creator->get('username'),
				'email'    => $careerplan->creator->get('email')
			);

			foreach ($keys as $key => $label)
			{
				if (in_array($key, ['name', 'username', 'email']))
				{
					continue;
				}
				if (substr($key, 0, 1) == '_')
				{
					if (!isset($tmp[$key]))
					{
						$tmp[$key] = '';
					}
					continue;
				}

				$val = null;
				foreach ($answers as $answer)
				{
					if ($answer->get('field_id') == $key)
					{
						$val = $answer->get('value');
						break;
					}
				}

				if (is_array($val))
				{
					$val = implode(';', $val);
				}
				else
				{
					if (strstr($val, '{'))
					{
						$v = json_decode((string)$val, true);

						if (!$v || json_last_error() !== JSON_ERROR_NONE)
						{
							// Nothing else to do
						}
						else
						{
							$i = 0;
							foreach ($v as $nm => $vl)
							{
								$k = '_' . $key . '_' . $nm;
								if ($i == 0)
								{
									$k = $key;
								}
								$tmp[$k] = $vl;
								$i++;
							}
							continue;
						}
					}
				}

				$tmp[$key] = $val;
			}

			$results = Event::trigger('careerplans.onExportData', array($careerplan, $tmp));
			foreach ($results as $result)
			{
				if (!is_array($result))
				{
					continue;
				}

				foreach ($result as $k => $v)
				{
					$tmp[$k] = $v;
				}
			}

			unset($application);

			fputcsv($file, $tmp);
		}

		fclose($file);

		$server = new \Hubzero\Content\Server();
		$server->filename($path);
		$server->disposition('attachment');
		$server->acceptranges(false); // @TODO fix byte range support

		if (!$server->serve())
		{
			// Should only get here on error
			App::abort(500, Lang::txt('Error serving file.'));
		}

		exit;
	}
}
