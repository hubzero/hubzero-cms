<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Models;

use Hubzero\Database\Relational;
use Date;
use User;

/**
 * Newsletter model for secondary story
 */
class Secondary extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'newsletter_secondary';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__newsletter_secondary_story';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'order';

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
		'story' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'order'
	);

	/**
	 * Defines a belongs to one relationship between newsletter and story
	 *
	 * @return  object
	 */
	public function newsletter()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Newsletter', 'nid');
	}

	/**
	 * Generates automatic order field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticOrder($data)
	{
		if (!isset($data['order']))
		{
			$last = self::all()
				->whereEquals('nid', (isset($data['nid']) ? $data['nid'] : 0))
				->order('order', 'desc')
				->row();

			$data['order'] = $last->order + 1;
		}

		return $data['order'];
	}

	/**
	 * Get Highest Story Order
	 *
	 * @param   integer  $newsletterId  Newsletter Id
	 * @return 	integer
	 */
	public function _getCurrentHighestOrder($newsletterId)
	{
		$last = self::all()
			->whereEquals('nid', $newsletterId)
			->whereEquals('deleted', 0)
			->order('order', 'desc')
			->row();

		return $last->get('order', 0);
	}
}
