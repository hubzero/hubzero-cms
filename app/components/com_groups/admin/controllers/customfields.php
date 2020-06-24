<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Groups\Models\Orm\Field;
use Components\Groups\Models\Orm\Option;
use Request;
use Config;
use Notify;
use Route;
use User;
use Lang;
use Date;
use App;

require_once \Component::path('com_groups') . '/models/orm/field.php';

/**
 * Applications controller class for forms
 */
class CustomFields extends AdminController
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
		$this->registerDefaultTask('edit');

		parent::execute();
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

		$fields = Field::all()
			->including(['options', function ($option){
				$option
					->select('*')
					->ordered();
			}])
			->ordered()
			->rows();

		$this->view
			->set('fields', $fields)
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

		$fs = Request::getArray('questions', array(), 'post');

		// Posted data is coming in 
		$form = json_decode(Request::getString('questions', '[]', 'post'), true);

		if (json_last_error() !== JSON_ERROR_NONE)
		{
			App::abort(500, 'JSON decode error: ' . json_last_error());
		}

		// Get the old schema
		$oldFields = Field::all()
			->including(['options', function ($option){
				$option
					->select('*')
					->ordered();
			}])
			->ordered()
			->rows();

		$defaultValues = array(
			'label' => '',
			'name' => '',
			'description' => '',
			'required' => 0,
			'readonly' => 0,
			'disabled' => 0,
			'access' => 0,
			'option_other' => '',
			'option_blank' => '',
			'min' => 0,
			'max' => 0,
			'default_value' => '',
			'placeholder' => ''
		);
		$currentFields = array_reduce($form, function($currentFields, $field) use ($defaultValues){
			$subTypes = array('address', 'facultyadvisor');
			$field['default_value'] = isset($field['value']) ? $field['value'] : '';
			$field['option_other'] = isset($field['other']) ? $field['other'] : '';
			$type = $field['type'];
			$type = isset($field['subtype']) && in_array($field['subtype'], $subTypes) ? $field['subtype'] : $type;
			switch ($type)
			{
				case 'radio-group':
					$field['type'] = 'radio';
					break;
				case 'checkbox-group':
					$field['type'] = 'checkboxes';
					break;
				case 'date':
					$field['type'] = 'calendar';
					break;
				case 'paragraph':
					$field['description'] = $field['label'];
					break;
			}
			$currentFields[] = array_merge($defaultValues, $field);
			return $currentFields;
		});
		$currentFields = isset($currentFields) ? $currentFields : array();
		$ordering = 0;
		foreach ($currentFields as $element)
		{
			$ordering++;
			$field = null;
			$fid = isset($element['id']) ? $element['id'] : 0;
			if ($fid)
			{
				$oldFields->drop($fid);
			}
			$field = Field::oneOrNew($fid);
			$field->set($element);
			$field->set('ordering', $ordering);
			if (!$field->save())
			{
				Notify::error($field->getError());
				continue;
			}

			// Does this field have any set options?
			if (isset($element['values']))
			{
				foreach ($element['values'] as $j => $opt)
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
					if (!$option)
					{
						$option = Option::blank();
					}

					$dependents = array();
					if (isset($opt['dependents']))
					{
						$dependents = explode(',', trim($opt['dependents']));
						$dependents = array_map('trim', $dependents);
						foreach ($dependents as $i => $dependent)
						{
							if (!$dependent)
							{
								unset($dependents[$i]);
							}
						}
					}
					//$option = ($option ?: Option::oneOrNew($oid));
					$option->set(array(
						'field_id'   => $field->get('id'),
						'label'      => (string) $opt['label'],
						'value'      => (isset($opt['value']) ? (string) $opt['value'] : ''),
						'checked'    => (isset($opt['selected']) ? (int) $opt['selected'] : 0),
						'ordering'   => $j + 1,
						'dependents' => json_encode($dependents)
					));

					if (!$option->save())
					{
						Notify::error($option->getError());
						continue;
					}

					$oid = $option->get('id');
					if ($oid)
					{
						// Remove found options from the list
						// Anything remaining will be deleted
						$field->options->drop($oid);
					}
				}
			}

			// Remove any options not in the incoming list
			if (!$field->options->destroyAll())
			{
				Notify::error($field->options->getError());
			}
		}

		if (!$oldFields->destroyAll())
		{
			Notify::error($oldFields->getError());
		}

		// Set success message
		Notify::success(Lang::txt('COM_GROUPS_SCHEMA_SAVED'));

		// Redirect
		$this->cancelTask();
	}
}
