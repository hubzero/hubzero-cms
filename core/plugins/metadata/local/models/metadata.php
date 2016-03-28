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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     Class available since release 2.0.0
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
