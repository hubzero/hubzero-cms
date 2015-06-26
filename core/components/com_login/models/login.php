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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Login\Models;

/**
 * Login Model
 */
class Login extends \JModelLegacy
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 */
	protected function populateState()
	{
		$credentials = array(
			'username' => \Request::getVar('username', '', 'method', 'username'),
			'password' => \Request::getVar('passwd', '', 'post', 'string', JREQUEST_ALLOWRAW)
		);
		$this->setState('credentials', $credentials);

		// check for return URL from the request first
		if ($return = \Request::getVar('return', '', 'method', 'base64'))
		{
			$return = base64_decode($return);
			if (!\JURI::isInternal($return))
			{
				$return = '';
			}
		}

		// Set the return URL if empty.
		if (empty($return))
		{
			$return = 'index.php';
		}

		$this->setState('return', $return);
	}

	/**
	 * Get the administrator login module by name (real, eg 'login' or folder, eg 'mod_login')
	 *
	 * @param   string  $name   The name of the module
	 * @param   string  $title  The title of the module, optional
	 * @return  object  The Module object
	 */
	public static function getLoginModule($name = 'mod_login', $title = null)
	{
		$result  = null;
		$modules = self::_load($name);
		$total   = count($modules);

		for ($i = 0; $i < $total; $i++)
		{
			// Match the title if we're looking for a specific instance of the module
			if (!$title || $modules[$i]->title == $title)
			{
				$result = $modules[$i];
				break;  // Found it
			}
		}

		// If we didn't find it, and the name is mod_something, create a dummy object
		if (is_null($result) && substr($name, 0, 4) == 'mod_')
		{
			$result = new \stdClass;
			$result->id        = 0;
			$result->title     = '';
			$result->module    = $name;
			$result->position  = '';
			$result->content   = '';
			$result->showtitle = 0;
			$result->control   = '';
			$result->params    = '';
			$result->user      = 0;
		}

		return $result;
	}

	/**
	 * Load login modules.
	 *
	 * Note that we load regardless of state or access level since access
	 * for public is the only thing that makes sense since users are not logged in
	 * and the module lets them log in.
	 * This is put in as a failsafe to avoid super user lock out caused by an unpublished
	 * login module or by a module set to have a viewing access level that is not Public.
	 *
	 * @param   string  $name   The name of the module
	 * @return  array
	 */
	protected static function _load($module)
	{
		static $clean;

		if (isset($clean))
		{
			return $clean;
		}

		$lang     = \Lang::getTag();
		$clientId = (int) \App::get('client')->id;

		$cache       = \App::get('cache');
		$cacheid     = 'com_modules.' . md5(serialize(array($clientId, $lang)));
		$loginmodule = array();

		if (!($clean = $cache->get($cacheid)))
		{
			$db = \App::get('db');

			$query = $db->getQuery(true);
			$query->select('m.id, m.title, m.module, m.position, m.showtitle, m.params');
			$query->from('#__modules AS m');
			$query->where('m.module =' . $db->Quote($module) .' AND m.client_id = 1');

			$query->join('LEFT', '#__extensions AS e ON e.element = m.module AND e.client_id = m.client_id');
			$query->where('e.enabled = 1');

			// Filter by language
			if (\App::isSite() && \App::get('language.filter'))
			{
				$query->where('m.language IN (' . $db->Quote($lang) . ',' . $db->Quote('*') . ')');
			}

			$query->order('m.position, m.ordering');

			// Set the query
			$db->setQuery($query);
			$modules = $db->loadObjectList();

			if ($db->getErrorNum())
			{
				\App::abort(500, \Lang::txt('JLIB_APPLICATION_ERROR_MODULE_LOAD', $db->getErrorMsg()));
				return $loginmodule;
			}

			// Return to simple indexing that matches the query order.
			$loginmodule = $modules;

			$cache->put($cacheid, $loginmodule, App::get('config')->get('cachetime', 15));
		}

		return $loginmodule;
	}
}
