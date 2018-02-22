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

namespace Components\Publications\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Publication license model
 */
class License extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'publication';

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
		'title' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'name',
		'title'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'ordering',
		'icon'
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
				->order('ordering', 'desc')
				->row();

			$data['ordering'] = $last->ordering + 1;
		}

		return $data['ordering'];
	}

	/**
	 * Generates automatic icon field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticIcon($data)
	{
		if (!isset($data['icon']) || !$data['icon'])
		{
			$data['icon'] = '/core/components/com_publications/site/assets/img/logos/license.gif';
		}

		return $data['icon'];
	}

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticTitle($data)
	{
		$title = strip_tags($data['title']);
		$title = trim($title);

		if (strlen($title) > 70)
		{
			$title  = substr($title . ' ', 0, 70);
			$title  = substr($title, 0, strrpos($title, ' '));
			$title .= '...';
		}

		return $title;
	}

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticName($data)
	{
		$alias = (isset($data['name']) && $data['name'] ? $data['name'] : $data['title']);
		$alias = strip_tags($alias);
		$alias = trim($alias);
		if (strlen($alias) > 70)
		{
			$alias = substr($alias . ' ', 0, 70);
			$alias = substr($alias, 0, strrpos($alias, ' '));
		}
		$alias = str_replace(' ', '-', $alias);

		return preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));
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
		$query = self::all();

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
	 * Is this the main entry?
	 *
	 * @return  boolean
	 */
	public function isMain()
	{
		return ($this->get('main') == 1);
	}

	/**
	 * Is this license being used?
	 *
	 * @return  boolean
	 */
	public function isUsed()
	{
		require_once __DIR__ . DS . 'version.php';

		$total = Version::all()
			->whereEquals('license_type', $this->get('id'))
			->total();

		return ($total > 0);
	}

	/**
	 * Set this record to be the main record
	 * Unsets all other records.
	 *
	 * @return  boolean
	 */
	public function setMain()
	{
		if ($this->get('main') == 1)
		{
			return true;
		}

		$db = \App::get('db');
		$query = $db->getQuery()
			->update($this->getTableName())
			->set(array('main' => 1))
			->where('id', '=', $this->get('id'))
			->toString();

		if (!$db->execute($query))
		{
			return false;
		}

		$query = $db->getQuery()
			->update($this->getTableName())
			->set(array('main' => 0))
			->where('id', '!=', $this->get('id'))
			->toString();

		return $db->execute($query);
	}

	/**
	 * Get default license
	 *
	 * @return  object
	 */
	public static function main()
	{
		return self::all()
			->whereEquals('main', 1)
			->row();
	}

	/**
	 * Get license by name
	 *
	 * @param   string  $name
	 * @return  object
	 */
	public static function oneByName($name)
	{
		return self::all()
			->whereEquals('name', $name)
			->row();
	}

	/**
	 * Get license by title
	 *
	 * @param   string  $title
	 * @return  object
	 */
	public static function oneByTitle($title)
	{
		return self::all()
			->whereLike('title', $title)
			->row();
	}
}
