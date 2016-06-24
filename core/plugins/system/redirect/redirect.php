<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Plugin class for redirect handling.
 *
 * @package		Joomla.Plugin
 * @subpackage	System.redirect
 */
class plgSystemRedirect extends \Hubzero\Plugin\Plugin
{
	/**
	 * Object Constructor.
	 *
	 * @param	object	The object to observe -- event dispatcher.
	 * @param	object	The configuration object for the plugin.
	 * @return	void
	 * @since	1.0
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
		$renderer = new \Hubzero\Error\Renderer\Page(
			App::get('document'),
			App::get('template')->template,
			App::get('config')->get('debug')
		);

		// Make sure the error is a 404 and we are not in the administrator.
		if (!App::isSite() || $error->getCode() != 404)
		{
			// Render the error page.
			$renderer->render($error);
		}

		// Get the full current URI.
		$uri = Hubzero\Utility\Uri::getInstance();
		$current = $uri->toString(array('scheme', 'host', 'port', 'path', 'query', 'fragment'));

		// Attempt to ignore idiots.
		if ((strpos($current, 'mosConfig_') !== false) || (strpos($current, '=http://') !== false))
		{
			// Render the error page.
			$renderer->render($error);
		}

		// See if the current url exists in the database as a redirect.
		$db = App::get('db');
		$db->setQuery(
			'SELECT ' . $db->quoteName('new_url') . ', ' . $db->quoteName('published').
			' FROM ' . $db->quoteName('#__redirect_links') .
			' WHERE ' . $db->quoteName('old_url') . ' = ' . $db->quote($current),
			0, 1
		);
		$link = $db->loadObject();

		// If no published redirect was found try with the server-relative URL
		if (!$link || $link->published != 1)
		{
			$currRel = $uri->toString(array('path', 'query', 'fragment'));
			$db->setQuery(
				'SELECT ' . $db->quoteName('new_url') . ', ' . $db->quoteName('published') .
				' FROM ' . $db->quoteName('#__redirect_links') .
				' WHERE ' . $db->quoteName('old_url') . ' = ' . $db->quote($currRel),
				0, 1
			);
			$link = $db->loadObject();
		}

		// If a redirect exists and is published, permanently redirect.
		if ($link && $link->published == 1)
		{
			App::redirect($link->new_url, null, null, true, false);
			return;
		}

		$referer = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];

		$db->setQuery('SELECT id FROM ' . $db->quoteName('#__redirect_links') . '  WHERE ' . $db->quoteName('old_url') . ' = ' . $db->quote($current));
		$res = $db->loadResult();
		if (!$res)
		{
			// If not, add the new url to the database.
			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__redirect_links'), false);
			$columns = array(
				$db->quoteName('old_url'),
				$db->quoteName('new_url'),
				$db->quoteName('referer'),
				$db->quoteName('comment'),
				$db->quoteName('hits'),
				$db->quoteName('published'),
				$db->quoteName('created_date')
			);
			$query->columns($columns);
			$query->values(
				$db->Quote($current) . ', ' . $db->Quote('') .
				' ,' . $db->Quote($referer) . ', ' . $db->Quote('') . ',1,0, ' .
				$db->Quote(Date::toSql())
			);

			$db->setQuery($query);
			$db->query();
		}
		else
		{
			// Existing error url, increase hit counter
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__redirect_links'));
			$query->set($db->quoteName('hits') . ' = ' . $db->quoteName('hits') . ' + 1');
			$query->where('id = ' . (int)$res);
			$db->setQuery((string)$query);
			$db->query();
		}

		// Render the error page.
		$renderer->render($error);
	}
}
