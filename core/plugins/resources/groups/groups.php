<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Display groups associated with a resource
 */
class plgResourcesGroups extends \Hubzero\Plugin\Plugin
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
	 * @param   object  $resource  Current resource
	 * @return  array
	 */
	public function &onResourcesSubAreas($resource)
	{
		$areas = array(
			'groups' => Lang::txt('PLG_RESOURCES_GROUPS')
		);
		return $areas;
	}

	/**
	 * Return data on a resource sub view (this will be some form of HTML)
	 *
	 * @param   object   $resource  Current resource
	 * @param   string   $option    Name of the component
	 * @param   integer  $miniview  View style
	 * @return  array
	 */
	public function onResourcesSub($resource, $option, $miniview=0)
	{
		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		if (!$resource->get('group_owner') || substr($resource->get('group_owner'), 0, strlen('app-')) == 'app-')
		{
			return $arr;
		}

		$group = \Hubzero\User\Group::getInstance($resource->get('group_owner'));

		if (!$group || !$group->get('gidNumber'))
		{
			return $arr;
		}

		// Pass the view some info
		$view = $this->view('default', 'display')
			->set('option', $option)
			->set('resource', $resource)
			->set('params', $this->params)
			->set('group', $group);

		if ($miniview)
		{
			$view->setLayout('mini');
		}

		// Return the output
		$arr['html'] = $view
			->setErrors($this->getErrors())
			->loadTemplate();

		return $arr;
	}
}
