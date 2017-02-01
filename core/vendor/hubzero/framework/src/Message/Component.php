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
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Message;

use Hubzero\Database\Relational;
use Lang;

/**
 * Model class for message component list
 * These are action items that are message-able
 */
class Component extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'xmessage';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__xmessage_component';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'component';

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
		'component' => 'notempty',
		'action'    => 'notempty'
	);

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('component', function($data)
		{
			self::$connection->setQuery("SELECT element FROM `#__extensions` AS e WHERE e.type = 'component' ORDER BY e.name ASC");
			$extensions = self::$connection->loadColumn();
			if (!in_array($data['component'], $extensions))
			{
				return Lang::txt('Component does not exist.');
			}
			return false;
		});
	}

	/**
	 * Defines a belongs to one relationship between newsletter and story
	 *
	 * @return  object
	 */
	public function getRecords($filters = array())
	{
		$entries = self::all();

		$c = $entries->getTableName();
		$e = '#__extensions';

		$entries
			->select($c . '.*,' . $e . '.name')
			->join($e, $e . '.element', $c . '.component', 'inner')
			->whereEquals($e . '.type', 'component');

		if (isset($filters['component']) && $filters['component'])
		{
			$entries->whereEquals($e . '.element', $filters['component']);
		}

		return $entries
			->ordered($c . '.component', 'asc')
			->rows();
	}

	/**
	 * Get all records
	 *
	 * @return  array
	 */
	public function getComponents()
	{
		return self::all()
			->order('component', 'asc')
			->group('component')
			->rows();
	}
}
