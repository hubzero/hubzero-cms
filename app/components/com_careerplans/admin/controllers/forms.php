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
use Components\Careerplans\Models\Fieldset;
use Components\Careerplans\Models\Field;
use Components\Careerplans\Models\Option;
use Request;
use Config;
use Notify;
use Route;
use User;
use Lang;
use Date;
use App;

/**
 * Applications controller class for forms
 */
class Forms extends AdminController
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
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * Display a list of blog comments
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		/*$filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			// Paging
			'limit' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			),
			'start' => Request::getState(
				$this->_option . '.' . $this->_controller . '.limitstart',
				'limitstart',
				0,
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
				'ASC'
			)
		);

		$comments = Fieldset::all();

		if ($filters['search'])
		{
			$comments->whereLike('label', strtolower((string)$filters['search']));
		}

		$rows = $comments
			->ordered('filter_order', 'filter_order_Dir')
			->rows();

		// Output the HTML
		$this->view
			->set('filters', $filters)
			->set('rows', $rows)
			->display();*/
		$this->editTask();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		//Request::setVar('hidemainmenu', 1);

		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$fieldsets = Fieldset::all()
			->including(['fields', function ($field){
				$field
					->select('*')
					->including(['options', function ($option){
						$option
							->select('*')
							->ordered();
					}])
					->ordered();
			}])
			->ordered()
			->rows();

		if (!count($fieldsets))
		{
			$fieldset = Fieldset::blank()->set(array(
				'label' => Lang::txt('Page 1'),
				'name'  => 'page1'
			));

			$fieldsets = array($fieldset);
		}

		$this->view
			->set('fieldsets', $fieldsets)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save changes to an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.manage', $this->_option)
		 && !User::authorise('core.admin', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$oldsets = Fieldset::all()->ordered()->rows();

		// Incoming data
		$fieldsets = array();
		$fs = Request::getVar('fieldset', array(), 'post', 'none', 2);
		foreach ($fs as $i => $f)
		{
			if (!isset($f['title']) || !$f['title'])
			{
				//$f['title'] = Lang::txt('COM_APPLICATIONS_PAGE_NUM', ($i + 1));
				continue;
			}

			$fieldset = Fieldset::oneByOrdering($i + 1);
			$fieldset->set('label', $f['title']);
			$fieldset->set('ordering', $i + 1);
			$fieldset->save();

			$fieldsets[] = $fieldset;
		}

		// Posted data is coming in 
		$form = json_decode(Request::getVar('questions', '[]', 'post', 'none', 2));
		if (json_last_error() !== JSON_ERROR_NONE)
		{
			App::abort(500, 'JSON decode error: ' . json_last_error());
		}

		// Get the old schema
		$fields = Field::all()
			->including(['options', function ($option){
				$option
					->select('*')
					->ordered();
			}])
			->ordered()
			->rows();

		// Collect old fields
		$oldFields = array();
		foreach ($fields as $oldField)
		{
			$oldFields[$oldField->get('id')] = $oldField;
		}

		foreach ($form as $k => $fields)
		{
			/*$fieldset = Fieldset::oneByOrdering($k + 1);
			if (!$fieldset || !$fieldset->get('id'))
			{
				$fieldset = Fieldset::blank();
			}
			$fieldset->set('ordering', $k + 1);
			$fieldset->save();*/
			if (!isset($fieldsets[$k]))
			{
				$fieldset = Fieldset::blank();
				$fieldset->set('title', Lang::txt('Page %s', ($k + 1)));
				$fieldset->set('ordering', $k + 1);
				$fieldset->save();

				$fieldsets[$k] = $fieldset;
			}

			foreach ($fields as $i => $element)
			{
				$field = null;

				$fid = (isset($element->id) ? $element->id : 0);

				if ($fid && isset($oldFields[$fid]))
				{
					$field = $oldFields[$fid];

					// Remove found fields from the list
					// Anything remaining will be deleted
					unset($oldFields[$fid]);
				}

				$field = ($field ?: Field::oneOrNew($fid));
				$field->set(array(
					'fieldset_id'   => $fieldsets[$k]->get('id'),
					'type'          => (string) $element->type,
					'label'         => (string) $element->label,
					'name'          => (isset($element->name) ? (string) $element->name : ''),
					'description'   => (isset($element->description) ? (string) $element->description : ''),
					'required'      => (isset($element->required) ? (int) $element->required : 0),
					'readonly'      => (isset($element->readonly) ? (int) $element->readonly : 0),
					'disabled'      => (isset($element->disabled) ? (int) $element->disabled : 0),
					'ordering'      => ($i + 1),
					'access'        => (isset($element->access) ? (int) $element->access : 0),
					'option_other'  => (isset($element->other) ? (int) $element->other : ''),
					'option_blank'  => (isset($element->blank) ? (int) $element->blankn : ''),
					'min'           => (isset($element->min) ? (int) $element->min : 0),
					'max'           => (isset($element->max) ? (int) $element->max : 0),
					'default_value' => (isset($element->value) ? (string) $element->value : ''),
					'placeholder'   => (isset($element->placeholder) ? (string) $element->placeholder : '')
				));

				if ($field->get('type') == 'radio-group')
				{
					$field->set('type', 'radio');
				}
				if ($field->get('type') == 'checkbox-group')
				{
					$field->set('type', 'checkboxes');
				}
				if ($field->get('type') == 'date')
				{
					$field->set('type', 'calendar');
				}
				if (isset($element->subtype) && $element->subtype == 'address')
				{
					$field->set('type', 'address');
				}
				if (isset($element->subtype) && $element->subtype == 'facultyadvisor')
				{
					$field->set('type', 'facultyadvisor');
				}
				if ($field->get('type') == 'paragraph')
				{
					$field->set('description', $field->get('label'));
				}

				if (!$field->save())
				{
					Notify::error($field->getError());
					continue;
				}

				// Collect old options
				$oldOptions = array();
				foreach ($field->options as $oldOption)
				{
					$oldOptions[$oldOption->get('id')] = $oldOption;
				}

				// Does this field have any set options?
				if (isset($element->values))
				{
					foreach ($element->values as $j => $opt)
					{
						$option = null;

						$oid = (isset($opt->id) ? $opt->id : 0);

						if ($oid)
						{
							$option = Option::oneOrNew($oid);
						}
						elseif (isset($opt->value) && $opt->value)
						{
							$option = Option::oneByValue($opt->value, $field->get('id'));
						}
						else
						{
							$option = Option::oneByOrdering($j + 1, $field->get('id'));
						}
						$oid = $option->get('id');

						if ($oid && isset($oldOptions[$oid]))
						{
							$option = $oldOptions[$oid];

							// Remove found options from the list
							// Anything remaining will be deleted
							unset($oldOptions[$oid]);
						}

						$dependents = array();
						if (isset($opt->dependents))
						{
							$dependents = explode(',', trim($opt->dependents));
							$dependents = array_map('trim', $dependents);
							foreach ($dependents as $j => $dependent)
							{
								if (!$dependent)
								{
									unset($dependents[$j]);
								}
							}
						}

						//$option = ($option ?: Option::oneOrNew($oid));
						$option->set(array(
							'field_id'   => $field->get('id'),
							'label'      => (string) $opt->label,
							'value'      => (isset($opt->value)   ? (string) $opt->value : ''),
							'checked'    => (isset($opt->checked) ? (int) $opt->checked : 0),
							'ordering'   => ($j + 1),
							'dependents' => json_encode($dependents)
						));

						if (!$option->save())
						{
							Notify::error($option->getError());
							continue;
						}
					}
				}

				// Remove any options not in the incoming list
				foreach ($oldOptions as $option)
				{
					if (!$option->destroy())
					{
						Notify::error($option->getError());
						continue;
					}
				}
			}
		}

		// Remove any fields not in the incoming list
		foreach ($oldFields as $field)
		{
			if (!$field->destroy())
			{
				Notify::error($field->getError());
				continue;
			}
		}

		// Were pages created that have no fields?
		if (count($fieldsets) > count($form))
		{
			$k++;
			for ($k; $k < count($form); $k++)
			{
				//Notify::warning('would delete fieldset: ' . $fieldsets[$k]->get('id'));
				$fieldsets[$k]->destroy();
			}
		}

		if (count($fieldsets) < count($oldsets))
		{
			$last = end($fieldsets);
			foreach ($oldsets as $os)
			{
				if ($os->get('ordering') > $last->get('ordering'))
				{
					$os->destroy();
				}
			}
		}

		// Set success message
		Notify::success(Lang::txt('COM_CAREERPLANS_SCHEMA_SAVED'));

		// Drop through to edit form?
		/*if ($this->getTask() == 'apply')
		{
			return $this->editTask();
		}*/

		// Redirect
		$this->cancelTask();
	}
}
