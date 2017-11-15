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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */
namespace Components\Tools\Models\Orm;

use Hubzero\Database\Relational;
use Date;

/**
 * Tool preferences model
 *
 * @uses \Hubzero\Database\Relational
 */
class Recent extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'tool';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__recent_tools';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = array(
		'uid'  => 'positive|nonzero',
		'tool' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created'
	);

	/**
	 * Get relationship to user
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->oneToOne('Hubzero\User\User', 'id', 'uid');
	}

	/**
	 * Get relationship to tool
	 *
	 * @return  object
	 */
	public function tool()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Tool', 'toolname', 'tool');
	}

	/**
	 * Record usage of a tool by a user
	 *
	 * @param   integer  $uid
	 * @param   string   $tool
	 * @return  object
	 */
	public static function hit($uid, $tool)
	{
		// Check if any recent tools are the same as the one just launched
		$records = self::all()
			->whereEquals('uid', $uid)
			->order('created', 'desc')
			->row();

		foreach ($records as $record)
		{
			if ($record->get('tool') == $tool)
			{
				$record->set('created', Date::of('now')->toSql());

				if (!$record->save())
				{
					return false;
				}

				return true;
			}
		}

		// Check if we've reached 5 recent tools or not
		if ($records->count() < 5)
		{
			// Still under 5, so insert a new record
			$recent = self::blank()->set(array(
				'uid'  => $uid,
				'tool' => $tool
			));

			if (!$recent->save())
			{
				return false;
			}

			return true;
		}

		// We reached the limit, so update the oldest entry effectively replacing it
		$recent = $records->last();
		$record->set('tool', $tool);
		$record->set('created', Date::of('now')->toSql());

		if (!$recent->save())
		{
			return false;
		}

		return true;
	}

	/**
	 * Remove records by user ID
	 *
	 * @param   integer  $uid
	 * @return  bool
	 */
	public static function destroyByUser($uid)
	{
		$records = self::all()
			->whereEquals('uid', $uid)
			->rows();

		foreach ($records as $record)
		{
			if (!$record->destroy())
			{
				return false;
			}
		}

		return true;
	}
}
