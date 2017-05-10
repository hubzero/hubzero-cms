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

namespace Components\Resources\Models;

use Hubzero\Database\Relational;
use Date;
use User;

include_once __DIR__ . DS . 'audience' . DS. 'level.php';

/**
 * Resource audience model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Audience extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'resource_taxonomy';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__resource_taxonomy_audience';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

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
		'rid' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'added',
		'added_by'
	);

	/**
	 * Generates automatic added field value
	 *
	 * @param   array   $data  The data being saved
	 * @return  string
	 * @since   2.0.0
	 **/
	public function automaticAdded($data)
	{
		return (isset($data['added']) && $data['added'] ? $data['added'] : Date::toSql());
	}

	/**
	 * Generates automatic added by field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  int
	 * @since   2.0.0
	 **/
	public function automaticAddedBy($data)
	{
		return (isset($data['added_by']) && $data['added_by'] ? (int)$data['added_by'] : (int)User::get('id'));
	}

	/**
	 * Return a formatted timestamp for created date
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		if ($as)
		{
			return Date::of($this->get('created'))->toLocal($as);
		}

		return $this->get('created');
	}

	/**
	 * Defines a belongs to one relationship between audience and user
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'added_by');
	}

	/**
	 * Defines a belongs to one relationship between resource and audience
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function resource()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Orm\\Resource', 'rid');
	}

	/**
	 * Defines a belongs to one relationship to level 0
	 *
	 * @return  object  \Hubzero\Database\Relationship\OneToOne
	 */
	public function level0()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Audience\\Level', 'id', 'level0');
	}

	/**
	 * Defines a belongs to one relationship to level 1
	 *
	 * @return  object  \Hubzero\Database\Relationship\OneToOne
	 */
	public function level1()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Audience\\Level', 'id', 'level1');
	}

	/**
	 * Defines a belongs to one relationship to level 2
	 *
	 * @return  object  \Hubzero\Database\Relationship\OneToOne
	 */
	public function level2()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Audience\\Level', 'id', 'level2');
	}

	/**
	 * Defines a belongs to one relationship to level 3
	 *
	 * @return  object  \Hubzero\Database\Relationship\OneToOne
	 */
	public function level3()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Audience\\Level', 'id', 'level3');
	}

	/**
	 * Defines a belongs to one relationship to level 4
	 *
	 * @return  object  \Hubzero\Database\Relationship\OneToOne
	 */
	public function level4()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Audience\\Level', 'id', 'level4');
	}

	/**
	 * Defines a belongs to one relationship to level 5
	 *
	 * @return  object  \Hubzero\Database\Relationship\OneToOne
	 */
	public function level5()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Audience\\Level', 'id', 'level5');
	}
}
