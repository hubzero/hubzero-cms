<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Helpers\Economy;

use Hubzero\Base\Obj;
use Hubzero\Bank\Teller;
use Hubzero\Bank\Config;
use User;
use Lang;

/**
 * Reviews Economy class:
 * Stores economy funtions for reviews on resources
 */
class Reviews extends Obj
{
	/**
	 * Database
	 *
	 * @var  object
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
	 * Get all reviews for resources
	 *
	 * @return  array
	 */
	public function getReviews()
	{
		// get all eligible reviews
		$sql = "SELECT r.id, r.user_id AS author, r.resource_id as rid, "
			."\n (SELECT COUNT(*) FROM #__abuse_reports AS a WHERE a.category='review' AND a.state!=1 AND a.referenceid=r.id) AS reports,"
			."\n (SELECT COUNT(*) FROM #__item_votes AS v WHERE v.vote='1' AND v.item_type='review' AND v.item_id=r.id) AS helpful, "
			."\n (SELECT COUNT(*) FROM #__item_votes AS v WHERE v.vote='-1' AND v.item_type='review' AND v.item_id=r.id) AS nothelpful "
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
