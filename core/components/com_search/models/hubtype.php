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
 * @since     2.1.1
 */

namespace Components\Search\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Hubzero\Base\Object;

/**
 * Database model for search hub types
 *
 * @uses  \Hubzero\Database\Relational
 */
class HubType extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'search';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 **/
	public $orderBy = 'id';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = array(
		//'type'  => 'notempty',
		//'title' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 **/
	public $initiate = array(
		'created_by',
		'created'
	);

	/**
	 * Get structure for a type
	 *
	 * @return  mixed  Iterable
	 */
	public function structure()
	{
		require_once PATH_ROOT . DS . $this->get('file_path');

		$classpath = $this->get('class_path');

		if (strpos($classpath, 'Tables') === FALSE)
		{
			$model = new $classpath;
		}
		else
		{
			// Accomodate the JTable class
			$database = App::get('db');
			$model = new $classpath($database);
		}
		if (get_parent_class($model) == 'Hubzero\Database\Relational')
		{
			$modelStructure = $model->getStructure()->getTableColumns($model->getTableName());
		}
		elseif (get_parent_class($model) == 'Hubzero\Base\Model')
		{
			$modelStructure = $model->toArray();
		}
		elseif (isset($database))
		{
			$modelStructure = $model->getFields();
		}

		return $modelStructure;
	}
}
