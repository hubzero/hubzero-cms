<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models;

use Hubzero\Database\Relational;
use App;

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

		if (strpos($classpath, 'Tables') === false)
		{
			$model = new $classpath;
		}
		else
		{
			// Accommodate the JTable class
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
