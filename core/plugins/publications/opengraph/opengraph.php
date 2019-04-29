<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications Plugin class for adding Open Graph metadata to the document
 */
class plgPublicationsOpengraph extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param   object   $publication  Current publication
	 * @param   string   $option       Name of the component
	 * @param   array    $areas        Active area(s)
	 * @param   string   $rtrn         Data to be returned
	 * @param   string   $version      Version name
	 * @param   boolean  $extended     Whether or not to show panel
	 * @return  array
	 */
	public function onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)
	{
		if (!App::isSite()
		 || Request::getWord('format') == 'raw'
		 || Request::getInt('no_html'))
		{
			return;
		}

		$view = $this->view();

		Document::addCustomTag('<meta property="og:title" content="' . $view->escape($publication->title) . '" />');

		Document::addCustomTag('<meta property="og:description" content="' . $view->escape($publication->abstract) . '" />');

		Document::addCustomTag('<meta property="og:type" content="article" />');

		$url = Route::url($publication->link());
		$url = rtrim(Request::root(), '/') . '/' . trim($url, '/');

		Document::addCustomTag('<meta property="og:url" content="' . $url . '" />');
	}
}
