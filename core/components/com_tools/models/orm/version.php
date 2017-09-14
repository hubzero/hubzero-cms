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
 */
namespace Components\Tools\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Tool version model
 *
 * @uses \Hubzero\Database\Relational
 */
class Version extends Relational
{
	/**
	 * Status constants
	 */
	const STATUS_DEV = 3;
	const STATUS_CURRENT = 1;

	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'tool';

	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $table = '#__tool_version';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'toolname' => 'notempty',
	);

	/**
	 * Get a list of versions
	 *
	 * @return  object
	 */
	public function tool()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Tool', 'toolname', 'toolname');
	}

	/**
	 * Retrieves one row loaded by toolname and revision fields
	 *
	 * @param   string  $toolname
	 * @param   string  $revision
	 * @return  mixed
	 */
	public static function oneByToolnameAndRevision($toolname, $revision)
	{
		$query = self::blank()
			->whereEquals('toolname', $toolname);

		if ($revision == 'current')
		{
			$query->whereEquals('state', self::STATUS_CURRENT);
		}
		else if ($revision == 'dev')
		{
			$query->whereEquals('state', self::STATUS_DEV);
		}
		else
		{
			$query->whereEquals('revision', $revision);
		}

		return $query->row();
	}

	/**
	 * Retrieves one row loaded by instance field
	 *
	 * @param   string  $instance
	 * @return  mixed
	 */
	public static function oneByInstance($instance)
	{
		return self::blank()
			->whereEquals('instance', $instance)
			->row();
	}

	/**
	 * Is the tool version the development version?
	 *
	 * @return  boolean
	 */
	public function isDev()
	{
		return ($this->get('state') == self::STATUS_DEV);
	}

	/**
	 * Is the tool version the current active version?
	 *
	 * @return  boolean
	 */
	public function isCurrent()
	{
		return ($this->get('state') == self::STATUS_CURRENT);
	}
}
