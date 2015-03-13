<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Publications\Helpers;

use Hubzero\Base\Object;
use Hubzero\Bank\Teller;

include_once __DIR__ . DS . 'economy' . DS . 'reviews.php';

/**
 * Publications Economy class:
 * Stores economy functions for publication
 */
class Economy extends Object
{
	/**
	 * JDatabase
	 *
	 * @var object
	 */
	var $_db = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
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
		$juser = \JUser::getInstance($con->user_id);

		// Reward review author
		if (is_object($juser) && $juser->get('id'))
		{
			$BTL = new Teller($this->_db , $juser->get('id'));

			if (intval($points) > 0)
			{
				$msg = ($type == 'royalty') ? JText::_('Royalty payment for your publication contributions') : '';
				$BTL->deposit($points, $msg, $cat, 0);
			}
		}
		return true;
	}
}