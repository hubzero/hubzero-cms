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
	public function &onProjectAreas($alias = NULL)
	{
		$area = array(
			'name'    => $this->_name,
			'alias'   => null,
			'title'   => Lang::txt('PLG_PROJECTS_INFO'),
			'submenu' => NULL,
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
	public function onProject($model, $action = '', $areas = NULL)
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
}