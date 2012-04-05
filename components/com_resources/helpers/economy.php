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
defined('_JEXEC') or die( 'Restricted access' );

//----------------------------------------------------------
// Resources Economy class:
// Stores economy funtions for resources
//----------------------------------------------------------

ximport('Hubzero_Bank');

/**
 * Short description for 'ResourcesEconomy'
 * 
 * Long description (if any) ...
 */
class ResourcesEconomy extends JObject
{

	/**
	 * Description for '_db'
	 * 
	 * @var object
	 */
	var $_db = NULL;  // Database

	//-----------

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db)
	{
		$this->_db = $db;
	}

	/**
	 * Short description for 'getCons'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
	 */
	public function getCons()
	{
		// get all eligible resource contributors
		$sql = "SELECT DISTINCT aa.authorid, SUM(r.ranking) as ranking FROM jos_author_assoc AS aa "
			."\n LEFT JOIN jos_resources AS r ON r.id=aa.subid "
			."\n WHERE aa.authorid > 0 AND r.published=1 AND r.standalone=1 GROUP BY aa.authorid ";

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'distribute_points'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $con Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function distribute_points($con, $type='royalty')
	{
		if (!is_object($con)) {
			return false;
		}
		$cat = 'resource';

		$points = round($con->ranking);

		// Get qualifying users
		$juser =& JUser::getInstance( $con->authorid );

		// Reward review author
		if (is_object($juser) && $juser->get('id')) {
			$BTL = new Hubzero_Bank_Teller( $this->_db , $juser->get('id') );

			if (intval($points) > 0) {
				$msg = ($type=='royalty') ? 'Royalty payment for your resource contributions' : '';
				$BTL->deposit($points, $msg, $cat, 0);
			}
		}
	}
}

//----------------------------------------------------------
// Reviews Economy class:
// Stores economy funtions for reviews on resources
//----------------------------------------------------------

/**
 * Short description for 'class'
 * 
 * Long description (if any) ...
 */
class ReviewsEconomy extends JObject
{

	/**
	 * Description for '_db'
	 * 
	 * @var object
	 */
	var $_db = NULL;  // Database

	//-----------

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db)
	{
		$this->_db = $db;

	}

	/**
	 * Short description for 'getReviews'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     array Return description (if any) ...
	 */
	public function getReviews()
	{
		// get all eligible reviews
		$sql = "SELECT r.id, r.user_id AS author, r.resource_id as rid, "
			."\n (SELECT COUNT(*) FROM #__abuse_reports AS a WHERE a.category='review' AND a.state!=1 AND a.referenceid=r.id) AS reports,"
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='review' AND v.referenceid=r.id) AS helpful, "
			."\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='review' AND v.referenceid=r.id) AS nothelpful "
			."\n FROM #__resource_ratings AS r";
		$this->_db->setQuery( $sql );
		$result = $this->_db->loadObjectList();
		$reviews = array();
		if ($result) {
			foreach ($result as $r)
			{
				// item is not abusive, got at least 3 votes, more positive than negative
				if (!$r->reports && (($r->helpful + $r->nothelpful) >=3) && ($r->helpful > $r->nothelpful) ) {
					$reviews[] = $r;
				}
			}
		}
		return $reviews;
	}

	/**
	 * Short description for 'calculate_marketvalue'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      object $review Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function calculate_marketvalue($review, $type='royalty')
	{
		if (!is_object($review)) {
			return false;
		}

		// Get point values for actions
		$BC = new Hubzero_Bank_Config( $this->_db );
		$p_R  = $BC->get('reviewvote') ? $BC->get('reviewvote') : 2;
		//$positive_co = 2;

		$calc = 0;
		if (isset($review->helpful) && isset($review->nothelpful)) {
			$calc += ($review->helpful) * $p_R;
			//$calc += ($review->helpful) * $p_R * $positive_co;
			//$calc += ($review->nothelpful)*$p_R;
		}

		($calc) ? $calc = $calc : $calc ='0';

		return $calc;
	}

	/**
	 * Short description for 'distribute_points'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $review Parameter description (if any) ...
	 * @param      string $type Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function distribute_points($review, $type='royalty')
	{
		if (!is_object($review)) {
			return false;
		}
		$cat = 'review';

		$points = $this->calculate_marketvalue($review, $type);

		// Get qualifying users
		$juser =& JUser::getInstance( $review->author );

		// Reward review author
		if (is_object($juser)) {
			$BTL = new Hubzero_Bank_Teller( $this->_db , $juser->get('id') );

			if (intval($points) > 0) {
				$msg = ($type=='royalty') ? 'Royalty payment for posting a review on resource #'.$review->rid : 'Commission for posting a review on resource #'.$review->rid;
				$BTL->deposit($points, $msg, $cat, $review->id);
			}
		}
	}
}

