<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Joomla! Page Cache Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.cache
 */
class plgSystemCache extends JPlugin
{
	private $_cache = null;

	/**
	 * Constructor
	 *
	 * @param   object  $subject  The object to observe
	 * @param   array   $config   An array that holds the plugin configuration
	 * @return  void
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		// Set the language in the class
		$options = array(
			'defaultgroup' => 'page',
			'browsercache' => $this->params->get('browsercache', false),
			'caching'      => false,
		);

		$this->_cache = \JCache::getInstance('page', $options);
	}

	/**
	 * Converting the site URL to fit to the HTTP request
	 *
	 * @return  void
	 */
	public function onAfterInitialise()
	{
		if (App::isAdmin() || JDEBUG)
		{
			return;
		}

		if (Notify::any())
		{
			return;
		}

		if (User::isGuest() && Request::method() == 'GET')
		{
			$this->_cache->setCaching(true);
		}

		$data  = $this->_cache->get();

		if ($data !== false)
		{
			\JResponse::setBody($data);

			echo \JResponse::toString(App::config('gzip'));

			if ($profiler = App::get('profiler'))
			{
				$profiler->mark('afterCache');
				echo implode('', $profiler->marks());
			}

			$app->close();
		}
	}

	/**
	 * Save cached data
	 *
	 * @return  void
	 */
	public function onAfterRender()
	{
		if (App::isAdmin() || JDEBUG)
		{
			return;
		}

		if (Notify::any())
		{
			return;
		}

		if (User::isGuest())
		{
			// We need to check again here, because auto-login plugins
			// have not been fired before the first aid check
			$this->_cache->store();
		}
	}
}
