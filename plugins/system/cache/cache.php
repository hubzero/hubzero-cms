<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Page Cache Plugin
 */
class plgSystemCache extends \Hubzero\Plugin\Plugin
{
	/**
	 * Constructor
	 *
	 * @var  object
	 */
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
		if (App::isAdmin() || Config::get('debug'))
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

			echo \JResponse::toString(App::get('config')->get('gzip'));

			if ($profiler = App::get('profiler'))
			{
				$profiler->mark('afterCache');
				echo implode('', $profiler->marks());
			}

			App::close();
		}
	}

	/**
	 * Save cached data
	 *
	 * @return  void
	 */
	public function onAfterRender()
	{
		if (App::isAdmin() || Config::get('debug'))
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
