<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Projects Info plugin
 */
class plgProjectsInfo extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param   string  $alias
	 * @return  array   Plugin name and title
	 */
	public function &onProjectAreas($alias = null)
	{
		$area = array(
			'name'    => $this->_name,
			'alias'   => null,
			'title'   => Lang::txt('PLG_PROJECTS_INFO'),
			'submenu' => null,
			'show'    => true,
			'icon'    => 'f05a'
		);
		return $area;
	}

	/**
	 * Event call to return data for a specific project
	 *
	 * @param   object  $model   Project model
	 * @param   string  $action  Plugin task
	 * @param   string  $areas   Plugins to return data
	 * @return  array   Return array of html
	 */
	public function onProject($model, $action = '', $areas = null)
	{
		$returnhtml = true;

		$arr = array(
			'html'     =>'',
			'metadata' =>''
		);

		// Get this area details
		$this->_area = $this->onProjectAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (empty($this->_area) || !in_array($this->_area['name'], $areas))
			{
				return;
			}
		}
		// Check that project exists
		if (!$model->exists())
		{
			return $arr;
		}

		// Check authorization
		if (!$model->access('member'))
		{
			return $arr;
		}

		// Are we returning HTML?
		if ($returnhtml)
		{
			$fields = Components\Projects\Models\Orm\Description\Field::all()
				->order('ordering', 'ASC')
				->rows();

			$projectDescription = Components\Projects\Models\Orm\Description::all()
				->where('project_id', '=', $model->get('id'))
				->rows();

			$info = array();
			foreach ($fields as $field)
			{
				foreach ($projectDescription as $description)
				{
					if ($description->description_key == $field->name)
					{
						$f = new stdClass;
						$f->label = $field->label;
						$f->value = $description->description_value;
						array_push($info, $f);
					}
				}
			}

			// Set vars
			$view = $this->view('default', 'view')
				->set('option', 'com_projects')
				->set('info', $info)
				->set('model', $model);

			$arr['html'] = $view->loadTemplate();
		}

		// Return data
		return $arr;
	}

	/**
	 * Event call to get content for public project page
	 *
	 * @param   object  $model
	 * @return  string
	 */
	public function onProjectPublicList($model)
	{
		if (!$model->exists() || !$model->isPublic())
		{
			return;
		}

		$fields = Components\Projects\Models\Orm\Description\Field::all()
			->order('ordering', 'ASC')
			->rows();

		$projectDescription = Components\Projects\Models\Orm\Description::all()
			->where('project_id', '=', $model->get('id'))
			->rows();

		$info = array();
		foreach ($fields as $field)
		{
			foreach ($projectDescription as $description)
			{
				if ($description->description_key == $field->name)
				{
					$f = new stdClass;
					$f->label = $field->label;
					$f->value = $description->description_value;
					array_push($info, $f);
				}
			}
		}

		// Set vars
		$view = $this->view('public', 'view')
			->set('option', 'com_projects')
			->set('info', $info)
			->set('model', $model);

		return $view->loadTemplate();
	}
}
