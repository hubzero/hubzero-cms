<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	public static function handleError($error)
	{
		$renderer = new \Hubzero\Error\Renderer\Page(
			App::get('document'),
			App::get('template.loader'),
			App::get('config')->get('debug')
		);

		// Make sure the error is a 404 and we are not in the administrator.
		if (!App::isSite() || $error->getCode() != 404)
		{
			// Render the error page.
			return $renderer->render($error);
		}

		// Get the full current URI.
		$uri = Hubzero\Utility\Uri::getInstance();
		$current = $uri->toString(array('scheme', 'host', 'port', 'path', 'query', 'fragment'));

		// Attempt to ignore idiots.
		if ((strpos($current, 'mosConfig_') !== false)
		 || (strpos($current, '=http://') !== false))
		{
			// Render the error page.
			return $renderer->render($error);
		}

		if (file_exists(Component::path('com_redirect') . DS . 'models' . DS . 'link.php'))
		{
			include_once Component::path('com_redirect') . DS . 'models' . DS . 'link.php';

			$current = rtrim($current);

			// See if the current url exists in the database as a redirect.
			$link = Components\Redirect\Models\Link::all()
				->whereEquals('old_url', $current)
				->row();

			// If no published redirect was found try with the server-relative URL
			if (!$link->get('id') || !$link->isPublished())
			{
				$currRel = $uri->toString(array('path', 'query', 'fragment'));
				$currRel = '/' . trim($currRel, '/');

				$link = Components\Redirect\Models\Link::all()
					->whereEquals('old_url', $currRel)
					->orWhereEquals('old_url', ltrim($currRel, '/'))
					->row();
			}

			// If a redirect exists and is published, permanently redirect.
			if ($link->get('id') && $link->isPublished())
			{
				$redirect = new Hubzero\Http\RedirectResponse($link->new_url, $link->get('status_code', 301));
				$redirect->setRequest(App::get('request'));
				$redirect->send();

				App::close();
			}

			$referer = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];

			$row = Components\Redirect\Models\Link::all()
				->whereEquals('old_url', substr($current, 0, 255))
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
				$row->set('hits', intval($row->get('hits', 0)) + 1);
			}

			try
			{
				$row->save();
			}
			catch (Exception $e)
			{
				// Do nothing for now.
				// @TODO  Log this?
			}
		}

		// Render the error page.
		$renderer->render($error);
	}
}
