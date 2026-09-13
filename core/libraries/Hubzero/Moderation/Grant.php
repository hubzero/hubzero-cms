<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation;

use Hubzero\Database\Relational;

/**
 * What one pass of the grantor did
 *
 * The economy's instrument panel. An economy nobody can see is one nobody can
 * tune, and its failure mode is silence: no credits, no moderators, and no
 * indication whether that is deliberate.
 */
class Grant extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'moderation';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'created';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created'
	);

	/**
	 * Record a pass
	 *
	 * @param   string  $itemType
	 * @param   string  $grantor
	 * @param   array   $counts
	 * @return  object
	 */
	public static function record($itemType, $grantor, array $counts)
	{
		$row = self::blank()->set(array_merge(array(
			'item_type'       => (string) $itemType,
			'grantor'         => (string) $grantor,
			'eligible'        => 0,
			'granted'         => 0,
			'credits_issued'  => 0,
			'credits_expired' => 0,
			'credits_spent'   => 0
		), $counts));

		$row->save();

		return $row;
	}
}
