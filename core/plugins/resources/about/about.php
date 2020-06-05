<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Tags\Models\Tag;
use Components\Tags\Models\Objct;

/**
 * Resources Plugin class for about tab
 */
class plgResourcesAbout extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param      object  $model  Current model
	 * @return     array
	 */
	public function &onResourcesAreas($model)
	{
		$areas = array();

		if ($model->type->params->get('plg_about', 0)
			&& $model->access('view'))
		{
			$areas['about'] = Lang::txt('PLG_RESOURCES_ABOUT');
		}

		return $areas;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param      object  $model   Current model
	 * @param      string  $option  Name of the component
	 * @param      array   $areas   Active area(s)
	 * @param      string  $rtrn    Data to be returned
	 * @return     array
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

		$ar = $this->onResourcesAreas($model);
		if (empty($ar))
		{
			$rtrn = '';
		}

		if ($rtrn == 'all' || $rtrn == 'html')
		{
			$query = Tag::all();

			$t = $query->getTableName();
			$o = Objct::blank()->getTableName();

			$query->select($t . '.*')
				->join($o, $t . '.id', $o . '.tagid')
				->where($o . '.label', '!=', 'badge')
				->whereEquals($o . '.tbl', 'resources')
				->whereEquals($o . '.objectid', $model->get('id'));

			if (!$model->access('edit'))
			{
				$query->whereNotIn($t . '.admin', [1]);
			}

			$tags = $query->rows();

			// Instantiate a view
			$view = $this->view('default', 'index');
			$view->option   = $option;
			$view->model    = $model;
			$view->database = App::get('db');
			$view->plugin   = $this->params;
			$view->tags     = $tags;

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		return $arr;
	}
}
