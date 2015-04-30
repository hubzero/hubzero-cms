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

namespace Hubzero\Session\Storage;

use Hubzero\Session\Store;
use Exception;

/**
 * Database session storage handler
 */
class Database extends Store
{
	/**
	 * Constructor
	 *
	 * @param   array  $options  Optional parameters.
	 * @return  void
	 */
	public function __construct($options = array())
	{
		if (!isset($options['database']) || !($options['database'] instanceof \JDatabase))
		{
			$options['database'] = \JFactory::getDBO();
		}

		$this->connection = $options['database'];

		parent::__construct($options);
	}

	/**
	 * Read the data for a particular session identifier from the SessionHandler backend.
	 *
	 * @param   string  $id  The session identifier.
	 * @return  string  The session data.
	 */
	public function read($session_id)
	{
		// Get the database connection object and verify its connected.
		if (!$this->connection->connected())
		{
			return false;
		}

		try
		{
			// Get the session data from the database table.
			$query = $this->connection->getQuery(true);
			$query->select($this->connection->quoteName('data'))
				->from($this->connection->quoteName('#__session'))
				->where($this->connection->quoteName('session_id') . ' = ' . $this->connection->quote($session_id));

			$this->connection->setQuery($query);

			return (string) $this->connection->loadResult();
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	/**
	 * Write session data to the SessionHandler backend.
	 *
	 * @param   string   $session_id    The session identifier.
	 * @param   string   $session_data  The session data.
	 * @return  boolean  True on success, false otherwise.
	 */
	public function write($session_id, $session_data)
	{
		//global $_PROFILER;

		/*if (JFactory::getApplication()->getClientId() == 4 || php_sapi_name() == 'cli')
		{
			if (php_sapi_name() != 'cli')
			{
				JPROFILE ? $_PROFILER->log() : null;
			}
			
			return true; // skip session write on api and command line calls
		}*/

		// Get the database connection object and verify its connected.
		if ($this->connection->connected())
		{
			try
			{
				$query = $this->connection->getQuery(true);
				$query->update($this->connection->quoteName('#__session'))
					->set($this->connection->quoteName('data') . ' = ' . $this->connection->quote($session_data))
					->set($this->connection->quoteName('time') . ' = ' . $this->connection->quote((int) time()))
					->set($this->connection->quoteName('ip') . ' = ' . $this->connection->quote($_SERVER['REMOTE_ADDR']))
					->where($this->connection->quoteName('session_id') . ' = ' . $this->connection->quote($session_id));

				// Try to update the session data in the database table.
				$this->connection->setQuery($query);

				if ($this->connection->execute())
				{
					//JPROFILE ? $_PROFILER->log() : null;
					return true;
				}

				/* Since $db->execute did not throw an exception, so the query was successful.
				Either the data changed, or the data was identical.
				In either case we are done.
				*/
			}
			catch (Exception $e)
			{
			}
		}

		//JPROFILE ? $_PROFILER->log() : null;
		return false;
	}

	/**
	 * Destroy the data for a particular session identifier in the SessionHandler backend.
	 *
	 * @param   string   $id  The session identifier.
	 * @return  boolean  True on success, false otherwise.
	 */
	public function destroy($session_id)
	{
		// Get the database connection object and verify its connected.
		if (!$this->connection->connected())
		{
			return false;
		}

		try
		{
			$query = $this->connection->getQuery(true);
			$query->delete($this->connection->quoteName('#__session'))
				->where($this->connection->quoteName('session_id') . ' = ' . $this->connection->quote($session_id));

			// Remove a session from the database.
			$this->connection->setQuery($query);

			return (boolean) $this->connection->execute();
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	/**
	 * Garbage collect stale sessions from the SessionHandler backend.
	 *
	 * @param   integer  $lifetime  The maximum age of a session.
	 * @return  boolean  True on success, false otherwise.
	 */
	public function gc($lifetime = 1440)
	{
		// Get the database connection object and verify its connected.
		if (!$this->connection->connected())
		{
			return false;
		}

		// Determine the timestamp threshold with which to purge old sessions.
		$past = time() - $lifetime;

		try
		{
			$query = $this->connection->getQuery(true);
			$query->delete($this->connection->quoteName('#__session'))
				->where($this->connection->quoteName('time') . ' < ' . $this->connection->quote((int) $past));

			// Remove expired sessions from the database.
			$this->connection->setQuery($query);

			return (boolean) $this->connection->execute();
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	/**
	 * Get single session data as an object
	 * 
	 * @param   integer  $session_id  Session Id 
	 * @return  object
	 */
	public function session($session_id)
	{
		$query = $this->connection->getQuery(true);
		$query->select('*')
				->from('#__session')
				->group('userid, client_id')
				->order('time DESC');

		$this->connection->setQuery($query);
		return $this->connection->loadObject();
	}

	/**
	 * Get list of all sessions
	 * 
	 * @param   array  $filters
	 * @return  array
	 */
	public function all($filters = array())
	{
		$max = '';
		if (isset($filters['distinct']) && $filters['distinct'] == 1)
		{
			$max = "MAX(time) as time,";
		}

		$query = $this->connection->getQuery(true);
		$query->select('session_id, client_id, guest, time, ' . $max . ' data, userid, username, ip')
				->from('#__session');

		if (isset($filters['guest']))
		{
			$query->where('guest=' . $this->connection->quote($filters['guest']));
		}

		if (isset($filters['client']))
		{
			if (!is_array($filters['client']))
			{
				$filters['client'] = array($filters['client']);
			}

			$query->where('client_id IN ('. implode(',', $filters['client']) . ')');
		}

		if (isset($filters['distinct']) && $filters['distinct'] == 1)
		{
			$query->group('userid, client_id');
		}

		$query->order('time DESC');

		$this->connection->setQuery($query);
		return $this->connection->loadObjectList();
	}
}
