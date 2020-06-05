<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Database\Relational;

/**
 * Metadata database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Metadata extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'file';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'key'        => 'notempty',
		'value'      => 'notempty'
	);

	/**
	 * Loads all metadata entries for a given path
	 *
	 * @param   string  $path  The path to the file being annotated
	 * @return  array
	 * @since   2.0.0
	 **/
	public static function loadAllByPath($path)
	{
		return self::whereEquals('path', $path)->rows();
	}

	/**
	 * Retrieves one row by path, returning an empty row if not found
	 *
	 * @param   string  $path  The path to the file being loaded
	 * @return  array
	 * @since   2.0.0
	 **/
	public static function oneOrNewByPathAndKey($path, $key)
	{
		$row = self::whereEquals('path', $path)->whereEquals('key', $key)->row();

		if ($row->isNew())
		{
			$row->set([
				'path' => $path,
				'key'  => $key
			]);
		}

		return $row;
	}

	/**
	 * Relocates all metadata entries for a given file to a new path
	 *
	 * @param   string  $oldPath  The current path
	 * @param   string  $newPath  The path to which we're moving
	 * @return  bool
	 * @since   2.0.0
	 **/
	public static function relocateByPath($oldPath, $newPath)
	{
		$instance = self::blank();

		return $instance->getQuery()
		                ->update($instance->getTableName())
		                ->set(['path' => $newPath])
		                ->whereEquals('path', $oldPath)
		                ->execute();
	}
}
