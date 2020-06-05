<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Helpers;

use Hubzero\Base\Obj;
use Hubzero\Bank\Teller;
use User;
use Lang;

include_once __DIR__ . DS . 'economy' . DS . 'reviews.php';

/**
 * Resources Economy class:
 * Stores economy funtions for resources
 */
class Economy extends Obj
{
	/**
	 * Database
	 *
	 * @var object
	 */
	private $_db = null;

	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		$this->_db = $db;
	}

	/**
	 * Get all contributors of all resources
	 *
	 * @return  array
	 */
	public function getCons()
	{
		// get all eligible resource contributors
		$sql = "SELECT DISTINCT aa.authorid, SUM(r.ranking) as ranking FROM #__author_assoc AS aa "
			. " LEFT JOIN #__resources AS r ON r.id=aa.subid "
			. " WHERE aa.authorid > 0 AND r.published=1 AND r.standalone=1 GROUP BY aa.authorid ";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Calculate royalties for a contributor and distribute
	 *
	 * @param   object   $con   Resources Contributor
	 * @param   string   $type  Point calculation type
	 * @return  boolean  False if errors, true on success
	 */
	public function distribute_points($con, $type='royalty')
	{
		if (!is_object($con))
		{
			return false;
		}
		$cat = 'resource';

		$points = round($con->ranking);

		// Get qualifying users
		$user = User::getInstance($con->authorid);

		// Reward review author
		if (is_object($user) && $user->get('id'))
		{
			$BTL = new Teller($user->get('id'));

			if (intval($points) > 0)
			{
				$msg = ($type == 'royalty') ? Lang::txt('Royalty payment for your resource contributions') : '';
				$BTL->deposit($points, $msg, $cat, 0);
			}
		}
		return true;
	}
}
