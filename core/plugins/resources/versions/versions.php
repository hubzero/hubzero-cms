<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources Plugin class for versions
 */
class plgResourcesVersions extends \Hubzero\Plugin\Plugin
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
	 * @param   object  $model  Current model
	 * @return  array
	 */
	public function &onResourcesAreas($model)
	{
		$areas = array();

		if ($model->isTool() && $model->type->params->get('plg_versions'))
		{
			$areas['versions'] = Lang::txt('PLG_RESOURCES_VERSIONS');
		}

		return $areas;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param   object  $model   Current resource
	 * @param   string  $option  Name of the component
	 * @param   array   $areas   Active area(s)
	 * @param   string  $rtrn    Data to be returned
	 * @return  array
	 */
	public function onResources($model, $option, $areas, $rtrn='all')
	{
		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onResourcesAreas($model))
			 && !array_intersect($areas, array_keys($this->onResourcesAreas($model))))
			{
				$rtrn = 'metadata';
			}
		}

		// Display only for tools
		if (!$model->isTool())
		{
			return $arr;
		}

		if ($rtrn == 'all' || $rtrn == 'html')
		{
			$database = App::get('db');

			$tv = new \Components\Tools\Tables\Version($database);
			$rows = $tv->getVersions($model->alias);

			// Get contribtool params
			$tconfig = Component::params('com_tools');

			// Instantiate a view
			$view = $this->view('default', 'browse')
				->set('tconfig', $tconfig)
				->set('option', $option)
				->set('resource', $model)
				->set('rows', $rows);

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			$arr['metadata'] = '';
		}

		return $arr;
	}
}
