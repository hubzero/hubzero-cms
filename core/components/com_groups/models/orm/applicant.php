<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Group applicant model
 */
class Applicant extends Relational
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
		$row = self::all()
			->whereEquals('uidNumber', $uidNumber)
			->whereEquals('gidNumber', $gidNumber)
			->row();

		if (!$row)
		{
			$row = self::blank();
		}

		return $row;
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
		$method = 'createWithNoPk'; //$this->isNew() ? 'createWithNoPk' : 'modifyWithNoPk';
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
