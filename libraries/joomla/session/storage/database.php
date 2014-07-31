<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Session
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Database session storage handler for PHP
 *
 * @package     Joomla.Platform
 * @subpackage  Session
 * @see         http://www.php.net/manual/en/function.session-set-save-handler.php
 * @since       11.1
 */
class JSessionStorageDatabase extends JSessionStorage
{
	/**
	 * Read the data for a particular session identifier from the SessionHandler backend.
	 *
	 * @param   string  $id  The session identifier.
	 *
	 * @return  string  The session data.
	 *
	 * @since   11.1
	 */
	public function read($id)
	{
		// Get the database connection object and verify its connected.
		$db = JFactory::getDbo();
		if (!$db->connected())
		{
			return false;
		}

		try
		{
			// Get the session data from the database table.
			$query = $db->getQuery(true);
			$query->select($db->quoteName('data'))
			->from($db->quoteName('#__session'))
			->where($db->quoteName('session_id') . ' = ' . $db->quote($id));

			$db->setQuery($query);

			return (string) $db->loadResult();
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	/**
	 * Write session data to the SessionHandler backend.
	 *
	 * @param   string  $id    The session identifier.
	 * @param   string  $data  The session data.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 */
	public function write($id, $data)
	{
		global $_PROFILER;

		if (JFactory::getApplication()->getClientId() == 4 || php_sapi_name() == 'cli')
		{
			if (php_sapi_name() != 'cli')
			{
				JPROFILE ? $_PROFILER->log() : null;
			}
			
			return true; // skip session write on api and command line calls
		}
		
		// Get the database connection object and verify its connected.
		$db = JFactory::getDbo();

		if ($db->connected())
		{
			try
			{
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__session'))
				->set($db->quoteName('data') . ' = ' . $db->quote($data))
				->set($db->quoteName('time') . ' = ' . $db->quote((int) time()))
				->set($db->quoteName('ip') . ' = ' . $db->quote($_SERVER['REMOTE_ADDR']))
				->where($db->quoteName('session_id') . ' = ' . $db->quote($id));

				// Try to update the session data in the database table.
				$db->setQuery($query);

				if ($db->execute())
				{
					JPROFILE ? $_PROFILER->log() : null;
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

		JPROFILE ? $_PROFILER->log() : null;
		return false;
	}

	/**
	 * Destroy the data for a particular session identifier in the SessionHandler backend.
	 *
	 * @param   string  $id  The session identifier.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 */
	public function destroy($id)
	{
		// Get the database connection object and verify its connected.
		$db = JFactory::getDbo();
		if (!$db->connected())
		{
			return false;
		}

		try
		{
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__session'))
			->where($db->quoteName('session_id') . ' = ' . $db->quote($id));

			// Remove a session from the database.
			$db->setQuery($query);

			return (boolean) $db->execute();
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
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   11.1
	 */
	public function gc($lifetime = 1440)
	{
		// Get the database connection object and verify its connected.
		$db = JFactory::getDbo();
		if (!$db->connected())
		{
			return false;
		}

		// Determine the timestamp threshold with which to purge old sessions.
		$past = time() - $lifetime;

		try
		{
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__session'))
			->where($db->quoteName('time') . ' < ' . $db->quote((int) $past));

			// Remove expired sessions from the database.
			$db->setQuery($query);

			return (boolean) $db->execute();
		}
		catch (Exception $e)
		{
			return false;
		}
	}
}
