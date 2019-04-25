<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Blog Plugin class for adding Open Graph metadata to the document
 */
class plgBlogOpengraph extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param   object  $model   Current model
	 * @return  void
	 */
	public function onBlogView($model)
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

		Document::addCustomTag('<meta property="og:title" content="' . $view->escape(Hubzero\Utility\Str::truncate(strip_tags($model->title), 40)) . '" />');

		$content = Hubzero\Utility\Str::truncate(strip_tags($model->content), 300);
		$content = str_replace(array("\n", "\t", "\r"), ' ', $content);
		$content = trim($content);

		Document::addCustomTag('<meta property="og:description" content="' . $view->escape($content) . '" />');

		Document::addCustomTag('<meta property="og:type" content="article" />');

		$url = Route::url($model->link());
		$url = rtrim(Request::root(), '/') . '/' . trim($url, '/');

		Document::addCustomTag('<meta property="og:url" content="' . $url . '" />');
	}
}
