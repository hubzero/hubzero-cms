<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
