<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources Plugin class for adding Open Graph metadata to the document
 */
class plgResourcesOpengraph extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param   object  $model   Current model
	 * @param   string  $option  Name of the component
	 * @param   array   $areas   Active area(s)
	 * @param   string  $rtrn    Data to be returned
	 * @return  void
	 */
	public function onResources($model, $option, $areas, $rtrn='all')
	{
		if (!App::isSite())
		{
			return;
		}

		if (Request::getWord('tmpl') || Request::getWord('format') || Request::getInt('no_html'))
		{
			return;
		}

		$view = $this->view();

		Document::addCustomTag('<meta property="og:title" content="' . $view->escape($model->title) . '" />');

		Document::addCustomTag('<meta property="og:description" content="' . $view->escape($model->introtext) . '" />');

		Document::addCustomTag('<meta property="og:type" content="article" />');

		$url = Route::url($model->link());
		$url = rtrim(Request::root(), '/') . '/' . trim($url, '/');

		Document::addCustomTag('<meta property="og:url" content="' . $url . '" />');
	}
}
