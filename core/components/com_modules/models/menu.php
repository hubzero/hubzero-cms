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
