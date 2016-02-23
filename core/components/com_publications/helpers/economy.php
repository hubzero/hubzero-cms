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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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