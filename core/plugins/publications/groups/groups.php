<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Display groups associated with a publication
 */
class plgPublicationsGroups extends \Hubzero\Plugin\Plugin
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
	 * @param   object  $publication  Current publication
	 * @return  array
	 */
	public function &onPublicationSubAreas($publication)
	{
		$areas = array();

		if ($publication->category()->_params->get('plg_groups', 1) == 1)
		{
			$areas['groups'] = Lang::txt('PLG_PUBLICATIONS_GROUPS');
		}

		return $areas;
	}

	/**
	 * Return data on a resource sub view (this will be some form of HTML)
	 *
	 * @param   object   $publication  Current publication
	 * @param   string   $option       Name of the component
	 * @param   integer  $miniview     View style
	 * @return  array
	 */
	public function onPublicationSub($publication, $option, $miniview=0)
	{
		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (!$publication->groupOwner())
		{
			return $arr;
		}

		// Instantiate a view
		$view = $this->view('default', 'display')
			->set('option', $option)
			->set('publication', $publication)
			->set('params', $this->params)
			->set('group', $publication->groupOwner());

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
