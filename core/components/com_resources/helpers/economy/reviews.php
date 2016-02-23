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

namespace Components\Resources\Helpers\Economy;

use Hubzero\Base\Object;
use Hubzero\Bank\Teller;
use Hubzero\Bank\Config;
use User;
use Lang;

/**
 * Reviews Economy class:
 * Stores economy funtions for reviews on resources
 */
class Reviews extends Object
{
	/**
	 * Database
	 *
	 * @var  object
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
	 * Get all reviews for resources
	 *
	 * @return  array
	 */
	public function getReviews()
	{
		// get all eligible reviews
		$sql = "SELECT r.id, r.user_id AS author, r.resource_id as rid, "
			."\n (SELECT COUNT(*) FROM #__abuse_reports AS a WHERE a.category='review' AND a.state!=1 AND a.referenceid=r.id) AS reports,"
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='review' AND v.referenceid=r.id) AS helpful, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='review' AND v.referenceid=r.id) AS nothelpful "
			."\n FROM #__resource_ratings AS r";
		$this->_db->setQuery($sql);
		$result = $this->_db->loadObjectList();
		$reviews = array();
		if ($result)
		{
			foreach ($result as $r)
			{
				// item is not abusive, got at least 3 votes, more positive than negative
				if (!$r->reports && (($r->helpful + $r->nothelpful) >=3) && ($r->helpful > $r->nothelpful))
				{
					$reviews[] = $r;
				}
			}
		}
		return $reviews;
	}

	/**
	 * Calculate the market value for a review
	 *
	 * @param   object  $review  ResourcesReview
	 * @param   string  $type    Point calculation type
	 * @return  mixed   False if errors, integer on success
	 */
	public function calculate_marketvalue($review, $type='royalty')
	{
		if (!is_object($review))
		{
			return false;
		}

		// Get point values for actions
		$BC = Config::values();

		$p_R = $BC->get('reviewvote') ? $BC->get('reviewvote') : 2;

		$calc = 0;
		if (isset($review->helpful) && isset($review->nothelpful))
		{
			$calc += ($review->helpful) * $p_R;
		}

		$calc = ($calc) ? $calc : 0;

		return $calc;
	}

	/**
	 * Calculate royalties for a review and distribute
	 *
	 * @param   object   $review  ResourcesReview
	 * @param   string   $type    Point calculation type
	 * @return  boolean  False if errors, true on success
	 */
	public function distribute_points($review, $type='royalty')
	{
		if (!is_object($review))
		{
			return false;
		}
		$cat = 'review';

		$points = $this->calculate_marketvalue($review, $type);

		// Get qualifying users
		$user = User::getInstance($review->author);

		// Reward review author
		if (is_object($user))
		{
			$BTL = new Teller($user->get('id'));

			if (intval($points) > 0)
			{
				$msg = ($type=='royalty')
					 ? Lang::txt('Royalty payment for posting a review on resource #%s', $review->rid)
					 : Lang::txt('Commission for posting a review on resource #%s', $review->rid);
				$BTL->deposit($points, $msg, $cat, $review->id);
			}
		}
		return true;
	}
}

