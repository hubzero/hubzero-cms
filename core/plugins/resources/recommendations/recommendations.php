<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources Plugin class for recommendations
 */
class plgResourcesRecommendations extends \Hubzero\Plugin\Plugin
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
			'recommendations' => Lang::txt('PLG_RESOURCES_RECOMMENDATIONS')
		);
		return $areas;
	}

	/**
	 * Return data on a resource sub view (this will be some form of HTML)
	 *
	 * @param   object   $resource   Current resource
	 * @param   string   $option     Name of the component
	 * @param   integer  $miniview   View style
	 * @return  array
	 */
	public function onResourcesSub($resource, $option, $miniview=0)
	{
		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		// Get some needed libraries
		include_once __DIR__ . DS . 'models' . DS . 'recommendation.php';

		// Get recommendations
		$r = Plugins\Resources\Recommendations\Models\Recommendation::find(
			$resource->id,
			$this->params->get('threshold', '0.21')
		);

		$results = $r->limit($this->params->get('display_limit', 10))->rows();

		// Pass the view some info
		$view = $this->view('default', 'browse');

		if ($miniview)
		{
			$view->setLayout('mini');
		}

		$view->set('option', $option);
		$view->set('resource', $resource);
		$view->set('results', $results);
		$view->setErrors($this->getErrors());

		// Return the output
		$arr['html'] = $view->loadTemplate();

		return $arr;
	}
}
