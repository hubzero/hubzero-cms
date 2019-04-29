<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications Plugin class for recommendations
 */
class plgPublicationsRecommendations extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return data on a publication sub view (this will be some form of HTML)
	 *
	 * @param   object   $publication  Current publication
	 * @param   string   $option       Name of the component
	 * @param   integer  $miniview     View style
	 * @return  array
	 */
	public function onPublicationSub($publication, $option, $miniview=0)
	{
		$arr = array(
			'html'    =>'',
			'metadata'=>'',
			'name'    =>'recommendations'
		);

		// Check if our area is in the array of areas we want to return results for
		if (!$publication->category()->_params->get('plg_recommendations', 1))
		{
			return $arr;
		}

		// Get some needed libraries
		include_once __DIR__ . DS . 'models' . DS . 'recommendation.php';

		// Get recommendations
		$r = Plugins\Publications\Recommendations\Models\Recommendation::find(
			$publication->id,
			$this->params->get('threshold', '0.21')
		);

		$results = $r->limit($this->params->get('display_limit', 10))->rows();

		// Pass the view some info
		$view = $this->view('default', 'browse')
			->set('option', $option)
			->set('publication', $publication)
			->set('results', $results)
			->setErrors($this->getErrors());

		if ($miniview)
		{
			$view->setLayout('mini');
		}

		// Return the output
		$arr['html'] = $view->loadTemplate();

		return $arr;
	}
}
