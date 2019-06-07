<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Helpers;

use Hubzero\Base\Obj;
use Hubzero\Bank\Teller;

include_once __DIR__ . DS . 'economy' . DS . 'reviews.php';

/**
 * Publications Economy class:
 * Stores economy functions for publication
 */
class Economy extends Obj
{
	/**
	 * Database
	 *
	 * @var object
	 */
	var $_db = null;

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
	 * @return     array
	 */
	public function getCons()
	{
		// get all eligible resource contributors
		$sql = "SELECT DISTINCT aa.user_id, SUM(r.ranking) as ranking FROM #__publication_authors AS aa "
			. " JOIN #__publication_versions AS v ON v.id=aa.publication_version_id "
			. " JOIN #__publications AS r ON r.id=v.publication_id "
			. " WHERE aa.user_id > 0 AND v.state=1 GROUP BY r.id, aa.user_id ";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Calculate royalties for a contributor and distribute
	 *
	 * @param      object $con  PublicationsContributor
	 * @param      string $type Point calculation type
	 * @return     boolean False if errors, true on success
	 */
	public function distribute_points($con, $type='royalty')
	{
		if (!is_object($con))
		{
			return false;
		}
		$cat = 'publication';

		$points = round($con->ranking);

		// Get qualifying users
		$user = User::getInstance($con->user_id);

		// Reward review author
		if (is_object($user) && $user->get('id'))
		{
			$BTL = new Teller($user->get('id'));

			if (intval($points) > 0)
			{
				$msg = ($type == 'royalty') ? Lang::txt('Royalty payment for your publication contributions') : '';
				$BTL->deposit($points, $msg, $cat, 0);
			}
		}
		return true;
	}
}
