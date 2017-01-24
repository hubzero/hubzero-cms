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

/**
 * Members Plugin class for courses
 */
class plgGroupsCourses extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Loads the plugin language file
	 *
	 * @param   string   $extension  The extension for which a language file should be loaded
	 * @param   string   $basePath   The basepath to use
	 * @return  boolean  True, if the file has successfully loaded.
	 */
	public function loadLanguage($extension = '', $basePath = PATH_APP)
	{
		if (empty($extension))
		{
			$extension = 'plg_' . $this->_type . '_' . $this->_name;
		}

		$group = Hubzero\User\Group::getInstance(Request::getCmd('cn'));
		if ($group && $group->isSuperGroup())
		{
			$basePath = PATH_APP . DS . 'site' . DS . 'groups' . DS . $group->get('gidNumber');
		}

		$lang = App::get('language');
		return $lang->load(strtolower($extension), $basePath, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_APP . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true)
			|| $lang->load(strtolower($extension), PATH_CORE . DS . 'plugins' . DS . $this->_type . DS . $this->_name, null, false, true);
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @return  array
	 */
	public function &onGroupAreas()
	{
		$area = array(
			'name'             => $this->_name,
			'title'            => Lang::txt('PLG_GROUPS_COURSES'),
			'default_access'   => $this->params->get('plugin_access', 'anyone'),
			'display_menu_tab' => $this->params->get('display_tab', 1),
			'icon'             => 'f09c'
		);
		return $area;
	}

	/**
	 * Return data on a group view (this will be some form of HTML)
	 *
	 * @param   object   $group       Current group
	 * @param   string   $option      Name of the component
	 * @param   string   $authorized  User's authorization level
	 * @param   integer  $limit       Number of records to pull
	 * @param   integer  $limitstart  Start of records to pull
	 * @param   string   $action      Action to perform
	 * @param   array    $access      What can be accessed
	 * @param   array    $areas       Active area(s)
	 * @return  array
	 */
	public function onGroup($group, $option, $authorized, $limit=0, $limitstart=0, $action='', $access, $areas=null)
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
		$this_area = $this->onGroupAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas) && $limit)
		{
			if (!in_array($this_area['name'], $areas))
			{
				$return = 'metadata';
			}
		}

		require_once Component::path('com_courses') . DS . 'models' . DS . 'courses.php';
		$model = Components\Courses\Models\Courses::getInstance();

		$filters = array(
			'group'    => $group->get('cn'),
			'group_id' => $group->get('gidNumber'),
			'count'    => true
		);

		$arr['metadata']['count'] = $model->courses($filters);

		// Build the HTML
		if ($return == 'html')
		{
			$filters['count'] = false;
			$filters['limit'] = Request::getState(
				$option . '.plugin.courses.limit',
				'limit',
				Config::get('list_limit'),
				'int'
			);
			$filters['start'] = Request::getState(
				$option . '.plugin.courses.limitstart',
				'limitstart',
				0,
				'int'
			);
			$filters['sortby'] = Request::getState(
				$option . '.plugin.courses.sortby',
				'sortby',
				''
			);
			$filters['search'] = Request::getState(
				$option . '.plugin.courses.search',
				'search',
				''
			);
			$filters['index'] = '';
			$filters['tag'] = '';

			if (!in_array($filters['sortby'], array('alias', 'title', 'popularity')))
			{
				$filters['sortby'] = 'title';
			}
			switch ($filters['sortby'])
			{
				case 'popularity':
					$filters['sort']  = 'students';
					$filters['sort_Dir'] = 'DESC';
				break;
				case 'title':
				case 'alias':
				default:
					$filters['sort']  = $filters['sortby'];
					$filters['sort_Dir'] = 'ASC';
				break;
			}

			$view = $this->view('default', 'display')
				->set('filters', $filters)
				->set('option', $option)
				->set('group', $group)
				->set('total', $arr['metadata']['count'])
				->set('results', $model->courses($filters));

			$arr['html'] = $view->loadTemplate();
		}

		return $arr;
	}
}
