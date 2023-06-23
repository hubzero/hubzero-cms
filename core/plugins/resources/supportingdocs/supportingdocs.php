<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources Plugin class for supporting documentss
 */
class plgResourcesSupportingDocs extends \Hubzero\Plugin\Plugin
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
	 * @param   object   $model  Current model
	 * @param   integer  $archive
	 * @return  array
	 */
	public function &onResourcesAreas($model, $archive = 0)
	{
		$areas = array();

		if ($model->isTool())
		{
			$children = $model->children()
				->whereEquals('published', Components\Resources\Models\Entry::STATE_PUBLISHED)
				->order('ordering', 'asc')
				->rows();
		}
		else
		{
			$children = $model->children()
				->whereEquals('published', Components\Resources\Models\Entry::STATE_PUBLISHED)
				->whereEquals('standalone', 0)
				->order('ordering', 'asc')
				->rows();
		}

		if ( count($children) < 2)
		{
			return $areas;
		}

		if (!$archive && $model->type->params->get('plg_' . $this->_name)
			&& $model->access('view-all'))
		{
			$areas['supportingdocs'] = Lang::txt('PLG_RESOURCES_SUPPORTINGDOCS');
		}

		return $areas;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param   object  $resource  Current resource
	 * @param   string  $option    Name of the component
	 * @param   array   $areas     Active area(s)
	 * @param   string  $rtrn      Data to be returned
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
				// Do nothing
				return $arr;
			}
		}

		// Instantiate a view
		$view = $this->view('default', 'browse')
			->set('option', $option)
			->set('model', $model)
			->set('live_site', rtrim(Request::base(), '/'));

		// Return the output
		$arr['html'] = $view->loadTemplate();

		return $arr;
	}
}
