<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Test\Stubs;

use Hubzero\Database\Exception\ConnectionFailedException;

/**
 * Stands in for the database driver in the test container.
 *
 * There is no database during a unit run, but two callers still reach for one.
 * Component::path() asks for a query and treats a connection failure as its cue
 * to fall back to the on-disk path, so every query attempt throws. Date::toSql()
 * only wants the driver's datetime format, so that answer is real.
 */
class DatabaseStub
{
	/**
	 * Datetime format used when rendering a Date to SQL
	 *
	 * @return  string
	 */
	public function getDateFormat()
	{
		return 'Y-m-d H:i:s';
	}

	/**
	 * Any attempt to actually query fails as an unreachable connection
	 *
	 * @param   string  $name
	 * @param   array   $arguments
	 * @return  void
	 * @throws  ConnectionFailedException
	 */
	public function __call($name, $arguments)
	{
		throw new ConnectionFailedException('No database connection in tests');
	}
}

/**
 * Stands in for the translator in the test container.
 *
 * Returns the key untranslated, which is what the tests that exercise language
 * keys assert against.
 */
class TranslatorStub
{
	/**
	 * Return the key itself rather than a translation
	 *
	 * @param   string  $key
	 * @return  string
	 */
	public function txt($key = '')
	{
		return $key;
	}

	/**
	 * Same for every other translator call
	 *
	 * @param   string  $name
	 * @param   array   $arguments
	 * @return  string
	 */
	public function __call($name, $arguments)
	{
		return isset($arguments[0]) ? $arguments[0] : '';
	}
}
