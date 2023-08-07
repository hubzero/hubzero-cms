<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	public function onGroup($group, $option, $authorized, $limit, $limitstart, $action, $access, $areas=null)
	{
		$return = 'html';
		$active = $this->_name;
		$active_real = 'discussion';

		// The output array we're returning
		$arr = array(
			'html' => '',
			'metadata' => array(),
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
