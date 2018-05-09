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

namespace Components\Groups\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Group member model
 */
class Member extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'xgroups';

	/**
	 * The table primary key name
	 *
	 * It defaults to 'id', but can be overwritten by a subclass.
	 *
	 * @var  string
	 **/
	protected $pk = 'gidNumber';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'gidNumber';

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
		'gidNumber' => 'positive|nonzero',
		'uidNumber' => 'positive|nonzero'
	);

	/**
	 * Defines a belongs to one relationship between user and entry
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'uidNumber');
	}

	/**
	 * Defines a belongs to one relationship between group and entry
	 *
	 * @return  object
	 */
	public function group()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Group', 'gidNumber');
	}

	/**
	 * Defines a belongs to one relationship between group and entry
	 *
	 * @param   integer  $gidNumber
	 * @param   integer  $uidNumber
	 * @return  object
	 */
	public static function oneByGroupAndUser($gidNumber, $uidNumber)
	{
		return self::all()
			->whereEquals('uidNumber', $uidNumber)
			->whereEquals('gidNumber', $gidNumber)
			->row();
	}

	/**
	 * Saves the current model to the database
	 *
	 * @return  bool
	 **/
	public function save()
	{
		// Validate
		if (!$this->validate())
		{
			return false;
		}

		// See if we're creating or updating
		$method = $this->isNew() ? 'createWithNoPk' : 'modifyWithNoPk';
		$result = $this->$method($this->getAttributes());

		$result = ($result === false ? false : true);

		// If creating, result is our new id, so set that back on the model
		if ($this->isNew())
		{
			\Event::trigger($this->getTableName() . '_new', ['model' => $this]);
		}

		\Event::trigger('system.onContentSave', array($this->getTableName(), $this));

		return $result;
	}

	/**
	 * Inserts a new row into the database
	 *
	 * @return  bool
	 * @since   2.0.0
	 **/
	protected function createWithNoPk()
	{
		return $this->getQuery()->push($this->getTableName(), $this->getAttributes());
	}

	/**
	 * Updates an existing item in the database
	 *
	 * @return  bool
	 **/
	protected function modifyWithNoPk()
	{
		$query = $this->getQuery()->update($this->getTableName())
			->set($this->getAttributes());

		foreach ($this->getAttributes() as $key => $val)
		{
			$query->whereEquals($key, $val);
		}

		// Return the result of the query
		return $query->execute();
	}

	/**
	 * Deletes the existing/current model
	 *
	 * @return  bool
	 **/
	public function destroy()
	{
		$query = $this->getQuery()->delete($this->getTableName());

		foreach ($this->getAttributes() as $key => $val)
		{
			$query->whereEquals($key, $val);
		}

		// Return the result of the query
		return $query->execute();
	}
}
