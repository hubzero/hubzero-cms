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

/**
 * Tool SessionClassGroup model
 *
 * @uses \Hubzero\Database\Relational
 */
class SessionClassGroup extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'tool_session_class';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__tool_session_class_groups';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = array(
		'class_id' => 'positive|nonzero',
		'group_id' => 'positive|nonzero'
	);

	/**
	 * Get relationship to sessionclass
	 *
	 * @return  object
	 */
	public function sessionclass()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\SessionClass', 'class_id', 'id');
	}

	/**
	 * Get relationship to groups
	 *
	 * @return  object
	 */
	public function group()
	{
		return $this->oneToOne('Hubzero\User\Group', 'group_id', 'gidNumber');
	}

	/**
	 * Remove records by class_id
	 *
	 * @param   integer  $class_id
	 * @return  bool
	 */
	public static function destroyByClass($class_id)
	{
		$records = self::all()
			->whereEquals('class_id', $class_id)
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

	/**
	 * Remove records by group_id
	 *
	 * @param   integer  $group_id
	 * @return  bool
	 */
	public static function destroyByGroup($group_id)
	{
		$records = self::all()
			->whereEquals('group_id', $group_id)
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
