<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

/**
 * Plugin class for redirect handling.
 */
class plgSystemRedirect extends \Hubzero\Plugin\Plugin
{
	/**
	 * Object Constructor.
	 *
	 * @param   object  $subject  The object to observe -- event dispatcher.
	 * @param   object  $config   The configuration object for the plugin.
	 * @return  void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		if (App::isSite())
		{
			// Set the error handler for E_ERROR to be the class handleError method.
			set_exception_handler(array('plgSystemRedirect', 'handleError'));
		}
	}

	/**
	 * Method to handle an error condition.
	 *
	 * @param   Exception  &$error  The Exception object to be handled.
	 * @return  void
	 */
	public static function handleError(&$error)
	{
		include_once(PATH_CORE . DS . 'components' . DS . 'com_redirect' . DS . 'models' . DS . 'link.php');

		$renderer = new \Hubzero\Error\Renderer\Page(
			App::get('document'),
			App::get('template.loader'),
			App::get('config')->get('debug')
		);

		// Make sure the error is a 404 and we are not in the administrator.
		if (!App::isAdmin() and ($error->getCode() == 404))
		{
			// Render the error page.
			$renderer->render($error);
			return;
		}

		// Get the full current URI.
		$uri = \Hubzero\Utility\Uri::getInstance();
		$current = $uri->toString(array('scheme', 'host', 'port', 'path', 'query', 'fragment'));

		// Attempt to ignore idiots.
		if ((strpos($current, 'mosConfig_') !== false)
		 || (strpos($current, '=http://') !== false))
		{
			// Render the error page.
			$renderer->render($error);
			return;
		}

		// See if the current url exists in the database as a redirect.
		$link = \Components\Redirect\Models\Link::all()
				->whereEquals('old_url', $current)
				->row();

		// If no published redirect was found try with the server-relative URL
		if (!$link->id || $link->published != 1)
		{
			$currRel = $uri->toString(array('path', 'query', 'fragment'));

			$link = \Components\Redirect\Models\Link::all()
				->whereEquals('old_url', $currRel)
				->row();
		}

		// If a redirect exists and is published, permanently redirect.
		if ($link->id && $link->published == 1)
		{
			App::redirect($link->new_url, null, null, true, false);
		}
		else
		{
			$referer = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];

			$row = \Components\Redirect\Models\Link::all()
				->whereEquals('old_url', $current)
				->row();

			if (!$row->get('id'))
			{
				$row->set([
					'old_url'   => $current,
					'new_url'   => '',
					'referer'   => $referer,
					'comment'   => '',
					'hits'      => 1,
					'published' => 0,
					'created_date' => Date::toSql()
				]);
			}
			else
			{
				$row->set('hits', intval($row->get('hits')) + 1);
			}

			$row->save();

			// Render the error page.
			$renderer->render($error);
		}
	}
}
