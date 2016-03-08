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

namespace Components\Resources\Models\Author\Role;

use Hubzero\Database\Relational;

/**
 * Resource author role type model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Type extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'author_role';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'role_id';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'role_id' => 'positive|nonzero',
		'type_id' => 'positive|nonzero'
	);

	/**
	 * Get associated role
	 *
	 * @return  object
	 */
	public function role()
	{
		return $this->belongsToOne('Components\Resources\Models\Author\Role', 'role_id');
	}

	/**
	 * Get associated type
	 *
	 * @return  object
	 */
	public function type()
	{
		return $this->belongsToOne('Components\Resources\Models\Type', 'type_id');
	}

	/**
	 * Get an entry by role and type
	 *
	 * @param   integer  $role_id
	 * @param   integer  $type_id
	 * @return  object
	 */
	public static function oneByRoleAndType($role_id, $type_id)
	{
		return self::all()
			->whereEquals('role_id', $role_id)
			->whereEquals('type_id', $type_id)
			->row();
	}
}
