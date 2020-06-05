<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources Plugin class for adding Twitter metadata to the document
 */
class plgBlogTwitter extends \Hubzero\Plugin\Plugin
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

		$user = $this->params->get('twitter_username');

		if (!$user)
		{
			return;
		}

		$view = $this->view();

		Document::addCustomTag('<meta property="twitter:card" content="summary" />');

		Document::addCustomTag('<meta property="twitter:site" content="@' . $view->escape($user) . '" />');

		Document::addCustomTag('<meta property="twitter:title" content="' . $view->escape(Hubzero\Utility\Str::truncate(strip_tags($model->title), 40)) . '" />');

		$content = Hubzero\Utility\Str::truncate(strip_tags($model->content), 140);
		$content = str_replace(array("\n", "\t", "\r"), ' ', $content);
		$content = trim($content);

		Document::addCustomTag('<meta property="twitter:description" content="' . $view->escape($content) . '" />');

		$url = Route::url($model->link());
		$url = rtrim(Request::root(), '/') . '/' . trim($url, '/');

		Document::addCustomTag('<meta property="twitter:url" content="' . $url . '" />');
	}
}
