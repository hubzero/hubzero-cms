<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources Plugin class for citations
 */
class plgResourcesCitations extends \Hubzero\Plugin\Plugin
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
	public function &onResourcesAreas($model)
	{
		$areas = array();

		if ($model->type->params->get('plg_citations')
			&& $model->access('view-all'))
		{
			$areas['citations'] = Lang::txt('PLG_RESOURCES_CITATIONS');
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
				$rtrn = 'metadata';
			}
		}
		if (!$model->type->params->get('plg_citations'))
		{
			return $arr;
		}

		// Get a needed library
		include_once \Component::path('com_citations') . DS . 'models' . DS . 'citation.php';

		$cc = \Components\Citations\Models\Citation::all();

		$a = \Components\Citations\Models\Association::blank()->getTableName();
		$c = $cc->getTableName();

		$citations = $cc
			->join($a, $a . '.cid', $c . '.id', 'inner')
			->whereEquals($c . '.published', 1)
			->whereEquals($a . '.tbl', 'resource')
			->whereEquals($a . '.oid', $model->id)
			->order($c . '.affiliated', 'asc')
			->order($c . '.year', 'desc')
			->rows();

		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html')
		{
			// Instantiate a view
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'browse'
				)
			);

			// Pass the view some info
			$view->option    = $option;
			$view->resource  = $model;
			$view->citations = $citations;
			$view->citationFormat = $this->params->get('format', 'APA');
			if ($this->getError())
			{
				$view->setError($this->getError());
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		// Are we returning metadata?
		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'metadata'
				)
			);
			$view->url = Route::url($model->link() . '&active=citations');
			$view->citations = $citations;

			$arr['metadata'] = $view->loadTemplate();
		}

		// Return results
		return $arr;
	}
}
