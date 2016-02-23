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

namespace Components\Resources\Helpers;

use Hubzero\Base\Object;
use Hubzero\Bank\Teller;
use User;
use Lang;

include_once __DIR__ . DS . 'economy' . DS . 'reviews.php';

/**
 * Resources Economy class:
 * Stores economy funtions for resources
 */
class Economy extends Object
{
	/**
	 * Database
	 *
	 * @var object
	 */
	private $_db = NULL;

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
