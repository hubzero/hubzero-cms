<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Modules\Models;

use Hubzero\Database\Relational;

/**
 * Module extension model
 */
class Menu extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'modules';

	/**
	 * The table name, non-standard naming 
	 *
	 * @var  string
	 */
	protected $table = '#__modules_menu';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'menuid';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

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
			//$this->set($this->getPrimaryKey(), $result);
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
		// Add any automatic fields
		//$this->parseAutomatics('initiate');

		return $this->getQuery()->push($this->getTableName(), $this->getAttributes());
	}

	/**
	 * Updates an existing item in the database
	 *
	 * @return  bool
	 **/
	protected function modifyWithNoPk()
	{
		// Add any automatic fields
		//$this->parseAutomatics('renew');

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

	/**
	 * Remove all records for a module
	 *
	 * @param   integer  $moduleid
	 * @return  bool
	 */
	public static function destroyForModule($moduleid)
	{
		$rows = self::all()
			->whereEquals('moduleid', (int)$moduleid)
			->rows();

		foreach ($rows as $row)
		{
			if (!$row->destroy())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Remove all records for a menu
	 *
	 * @param   integer  $menuid
	 * @return  bool
	 */
	public static function destroyForMenu($menuid)
	{
		$rows = self::all()
			->whereEquals('menuid', (int)$menuid)
			->rows();

		foreach ($rows as $row)
		{
			if (!$row->destroy())
			{
				return false;
			}
		}

		return true;
	}
}
