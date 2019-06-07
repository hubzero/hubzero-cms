<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Orm;

use Hubzero\Database\Relational;
use Date;
use Lang;

/**
 * Projects Stat model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Stat extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'project';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'processed';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'stats' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'processed'
	);

	/**
	 * Generates automatic priority field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticProcessed($data)
	{
		if (!isset($data['processed']) || !$data['processed'] || $data['processed'] == '0000-00-00 00:00:00')
		{
			$data['processed'] = null;
		}

		return $data['processed'];
	}
}
