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
use Hubzero\Utility\String;
use Hubzero\Base\Object;

/**
 * Resource license model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Author extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'author';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__author_assoc';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'ordering';

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
		'subtable' => 'notempty',
		'subid'    => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'ordering',
		'subtable'
	);

	/**
	 * Generates automatic ordering field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticOrdering($data)
	{
		if (!isset($data['ordering']))
		{
			$last = self::all()
				->select('ordering')
				->whereEquals('subid', (int)$data['subid'])
				->whereEquals('subtable', 'resources')
				->order('ordering', 'desc')
				->row();

			$data['ordering'] = $last->ordering + 1;
		}

		return $data['ordering'];
	}

	/**
	 * Generates automatic subtable field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticSubtable($data)
	{
		if (!isset($data['subtable']))
		{
			$data['subtable'] = 'resources';
		}

		return $data['subtable'];
	}

	/**
	 * Get profile for author ID
	 *
	 * @return  object
	 */
	public function profile()
	{
		return $this->belongsToOne('Hubzero\User\User', 'authorid');
	}

	/**
	 * Populate record with profile info
	 *
	 * @return  boolean
	 */
	public function populateFromProfile()
	{
		$profile = $this->profile;

		if (!$profile->get('id'))
		{
			return false;
		}

		if (!$profile->get('name'))
		{
			$profile->set('name', $profile->get('givenName') . ' ' . $profile->get('surname'));
		}

		$this->set('name', $profile->get('name'));
		$this->set('organization', $profile->get('organization'));

		return true;
	}

	/**
	 * Method to move a row in the ordering sequence of a group of rows defined by an SQL WHERE clause.
	 * Negative numbers move the row up in the sequence and positive numbers move it down.
	 *
	 * @param   integer  $delta  The direction and magnitude to move the row in the ordering sequence.
	 * @param   string   $where  WHERE clause to use for limiting the selection of rows to compact the ordering values.
	 * @return  bool     True on success.
	 */
	public function move($delta, $where = '')
	{
		// If the change is none, do nothing.
		if (empty($delta))
		{
			return true;
		}

		// Select the primary key and ordering values from the table.
		$query = self::all()
			->whereEquals('subid', $this->get('subid'))
			->whereEquals('subtable', $this->get('subtable'));

		// If the movement delta is negative move the row up.
		if ($delta < 0)
		{
			$query->where('ordering', '<', (int) $this->get('ordering'));
			$query->order('ordering', 'desc');
		}
		// If the movement delta is positive move the row down.
		elseif ($delta > 0)
		{
			$query->where('ordering', '>', (int) $this->get('ordering'));
			$query->order('ordering', 'asc');
		}

		// Add the custom WHERE clause if set.
		if ($where)
		{
			$query->whereRaw($where);
		}

		// Select the first row with the criteria.
		$row = $query->ordered()->row();

		// If a row is found, move the item.
		if ($row->get('id'))
		{

			$prev = $this->get('ordering');

			// Update the ordering field for this instance to the row's ordering value.
			$this->set('ordering', (int) $row->get('ordering'));

			// Check for a database error.
			if (!$this->save())
			{
				return false;
			}

			// Update the ordering field for the row to this instance's ordering value.
			$row->set('ordering', (int) $prev);

			// Check for a database error.
			if (!$row->save())
			{
				$this->addError($row->getError());
				return false;
			}
		}
		else
		{
			// Update the ordering field for this instance.
			$this->set('ordering', (int) $this->get('ordering'));

			// Check for a database error.
			if (!$this->save())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Get the user ID for a name
	 *
	 * @param   string   $name  Name to look up
	 * @return  integer
	 */
	public function getUserId($name)
	{
		$row = self::all()
			->where('authorid', '<', 0)
			->whereEquals('name', $name)
			->row();

		$uid = $row->get('authorid');

		// If not account if found, use a negative timestamp
		if ($uid == null)
		{
			return -(time());
		}
		else
		{
			return $uid;
		}
	}

	/**
	 * Get a record by its relationship
	 *
	 * @param   integer  $resource_id
	 * @param   integer  $user_id
	 * @return  object
	 */
	public static function oneByRelationship($resource_id, $user_id)
	{
		$row = self::all()
			->whereEquals('subid', $resource_id)
			->whereEquals('authorid', $user_id)
			->whereEquals('subtable', 'resources')
			->order('ordering', 'asc')
			->row();

		return $row;
	}

	/**
	 * Get a record by its relationship and author name
	 *
	 * @param   integer  $resource_id
	 * @param   string   $name
	 * @return  object
	 */
	public static function oneByName($resource_id, $name)
	{
		$row = self::all()
			->whereEquals('subid', $resource_id)
			->where('authorid', '<', 0)
			->whereEquals('subtable', 'resources')
			->whereEquals('name', $name)
			->order('ordering', 'asc')
			->row();

		return $row;
	}
}
