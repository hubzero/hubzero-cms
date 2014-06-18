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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Publications Economy class:
 * Stores economy functions for publication
 */
class PublicationsEconomy extends JObject
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
		$juser = JUser::getInstance($con->user_id);

		// Reward review author
		if (is_object($juser) && $juser->get('id'))
		{
			$BTL = new \Hubzero\Bank\Teller($this->_db , $juser->get('id'));

			if (intval($points) > 0)
			{
				$msg = ($type == 'royalty') ? JText::_('Royalty payment for your publication contributions') : '';
				$BTL->deposit($points, $msg, $cat, 0);
			}
		}
		return true;
	}
}

/**
 * Reviews Economy class:
 * Stores economy funtions for reviews on resources
 */
class ReviewsEconomy extends JObject
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
	 * Get all reviews for resources
	 *
	 * @return     array
	 */
	public function getReviews()
	{
		// get all eligible reviews
		$sql = "SELECT r.id, r.created_by AS author, r.publication_id as pid, "
			."\n (SELECT COUNT(*) FROM #__abuse_reports AS a WHERE a.category='review' AND a.state!=1 AND a.referenceid=r.id) AS reports,"
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='review' AND v.referenceid=r.id) AS helpful, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='review' AND v.referenceid=r.id) AS nothelpful "
			."\n FROM #__publication_ratings AS r";
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
	 * @param      object $review ResourcesReview
	 * @param      string $type   Point calculation type
	 * @return     mixed False if errors, integer on success
	 */
	public function calculate_marketvalue($review, $type='royalty')
	{
		if (!is_object($review))
		{
			return false;
		}

		// Get point values for actions
		$BC = new \Hubzero\Bank\Config($this->_db);

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
	 * @param      object $review ResourcesReview
	 * @param      string $type   Point calculation type
	 * @return     boolean False if errors, true on success
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
		$juser = JUser::getInstance($review->author);

		// Reward review author
		if (is_object($juser))
		{
			$BTL = new \Hubzero\Bank\Teller($this->_db , $juser->get('id'));

			if (intval($points) > 0)
			{
				$msg = ($type=='royalty')
					 ? JText::sprintf('Royalty payment for posting a review on publication #%s', $review->pid)
					 : JText::sprintf('Commission for posting a review on publication #%s', $review->pid);
				$BTL->deposit($points, $msg, $cat, $review->id);
			}
		}
		return true;
	}
}

