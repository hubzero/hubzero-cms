<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Languages\Models;

use Hubzero\Database\Relational;

/**
 * Language model
 */
class Language extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = '';

	/**
	 * The table primary key name
	 *
	 * It defaults to 'id', but can be overwritten by a subclass.
	 *
	 * @var  string
	 **/
	protected $pk = 'lang_id';

	/**
	 * The table name, non-standard naming 
	 *
	 * @var  string
	 */
	protected $table = '#__languages';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'lang_id';

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
		'lang_code' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'ordering'
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
}
