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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Tables;

use Components\Wishlist\Models\Tags;
use Component;
use Lang;
use Date;

/**
 * Table class for a wish
 */
class Wish extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__wishlist_item', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->subject = trim($this->subject);
		if ($this->subject == '')
		{
			$this->setError(Lang::txt('COM_WISHLIST_ERROR_NO_SUBJECT'));
		}

		$this->wishlist = intval($this->wishlist);
		if (!$this->wishlist)
		{
			$this->setError(Lang::txt('COM_WISHLIST_ERROR_NO_LIST'));
		}

		if ($this->getError())
		{
			return false;
		}

		if (!$this->id)
		{
			$this->proposed    = Date::toSql();
			$this->proposed_by = User::get('id');
		}

		return true;
	}

	/**
	 * Get the sum total votes
	 *
	 * @param   integer  $wishid  Entry ID
	 * @param   string   $what    Field to sum
	 * @return  mixed    False if error, integer on success
	 */
	public function get_votes_sum($wishid, $what)
	{
		if ($wishid === NULL)
		{
			return false;
		}

		$sql = "SELECT SUM($what) FROM `#__wishlist_vote` WHERE wishid=" . $wishid;
		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get a record count for a list
	 *
	 * @param   string   $listid   List ID
	 * @param   array    $filters  Filters to build query on
	 * @param   integer  $admin    User is admin?
	 * @param   object   $user     current user
	 * @return  mixed    False if error, integer on success
	 */
	public function get_count($listid, $filters, $admin, $user=NULL)
	{
		if ($listid === NULL)
		{
			return false;
		}
		if (is_object($user))
		{
			$uid = $user->get('id');
		}
		else
		{
			$uid = 0;
		}

		$sql = "SELECT ws.id FROM `#__wishlist_item` AS ws ";

		if (isset($filters['tag']) && $filters['tag'])
		{
			$sql .= "\n LEFT JOIN #__tags_object AS RTA ON RTA.objectid=ws.id AND RTA.tbl='wishlist' ";
			$sql .= "\n LEFT JOIN #__tags AS TA ON RTA.tagid=TA.id ";
		}

		$sql .= " WHERE 1=1 ";
		$sql .= ($listid > 0) ? " AND ws.wishlist='" . $listid . "'" : '';

		// list  filtering
		switch ($filters['filterby'])
		{
				case 'all':    		$sql .= ' AND ws.status!=2';
									break;
				case 'granted':    	$sql .= ' AND ws.status=1';
									break;
				case 'open':    	$sql .= ' AND ws.status=0';
									break;
				case 'accepted':    $sql .= ' AND ws.accepted=1 AND (ws.status=0 OR ws.status=6)';
									break;
				case 'pending':     $sql .= ' AND ws.accepted=0 AND ws.status=0';
									break;
				case 'rejected':    $sql .= ' AND ws.status=3';
									break;
				case 'withdrawn':   $sql .= ' AND ws.status=4';
									break;
				case 'deleted':     $sql .= ' AND ws.status=2';
									break;
				case 'useraccepted':$sql .= ' AND ws.accepted=3 AND ws.status!=2';
									break;
				case 'private':    	$sql .= ' AND ws.status!=2 AND ws.private=1';
									break;
				case 'public':    	$sql .= ' AND ws.status!=2 AND ws.private=0';
									break;
				case 'mine':
					if ($uid)
					{
						$sql .= ' AND ws.assigned="' . $uid . '" AND ws.status!=2';
					}
				break;
				case 'submitter':
					if ($uid)
					{
						$sql .= ' AND ws.proposed_by=' . $uid . ' AND ws.status!=2';
					}
					break;
				case 'assigned':
					$sql .= ' AND ws.assigned NOT NULL AND ws.status!=2';
				break;
				default:
					$sql .= ' AND ws.status!=2';
				break;
		}
		if (isset($filters['search']) && $filters['search'])
		{
			$word = $this->_db->quote('%' . strtolower($filters['search']) . '%');
			$sql .= " AND ((LOWER(ws.subject) LIKE $word) OR (LOWER(ws.about) LIKE $word)) ";
		}

		// do not show private wishes
		if (!$admin)
		{
			$sql .="\n AND ws.private='0'";
		}

		if (isset($filters['tag']) && $filters['tag'])
		{
			require_once(dirname(__DIR__) . DS . 'models' . DS . 'tags.php');

			$tagging = new Tags(1, 'wishlist');

			$tags = $filters['tag'];
			if (is_string($tags))
			{
				$tags = trim($tags);
				$tags = preg_split("/(,|;)/", $tags);
			}
			foreach ($tags as $k => $tag)
			{
				$tags[$k] = $tagging->normalize($tag);
			}

			$sql .= " AND (RTA.objectid=ws.id AND (RTA.tbl='wishlist') AND (TA.tag IN ('" . implode("','", $tags) . "') OR TA.raw_tag IN ('" . implode("','", $tags) . "')))";
			$sql .= " GROUP BY ws.id ";
		}

		$this->_db->setQuery($sql);
		$result = $this->_db->loadObjectList();

		return count($result);
	}

	/**
	 * Get wishes for a list
	 *
	 * @param   integer  $listid   List ID
	 * @param   array    $filters  Filters to build query from
	 * @param   integer  $admin    Admin access?
	 * @param   object   $user     User
	 * @param   integer  $fullinfo Return fullinfo or not?
	 * @return  mixed    False if error, array on success
	 */
	public function get_wishes($listid, $filters, $admin, $user=NULL, $fullinfo = 1)
	{
		if ($listid === NULL)
		{
			return false;
		}
		if (is_object($user))
		{
			$uid = $user->get('id');
		}
		else
		{
			$uid = 0;
		}

		$filters['tag'] = isset($filters['tag']) ? $filters['tag'] : '';

		require_once(dirname(__DIR__) . DS . 'models' . DS . 'tags.php');

		$sort = 'ws.status ASC, ws.proposed DESC';
		// list  sorting
		if (isset($filters['sortby']))
		{
			switch ($filters['sortby'])
			{
					case 'date':
						$sort = 'ws.status ASC, ws.proposed DESC';
					break;
					case 'ranking':
						$sort = 'ws.status ASC, ranked, ws.ranking DESC, positive DESC, ws.proposed DESC';
					break;
					case 'feedback':
						$sort = 'positive DESC, ws.status ASC';
					break;
					case 'bonus':
						$sort = 'ws.status ASC, bonus DESC, positive DESC, ws.ranking DESC, ws.proposed DESC';
					break;
					case 'latestcomment':
						$sort = 'latestcomment DESC, ws.status ASC';
					break;
					case 'submitter':
						$sort = 'xp.name ASC';
					break;
					default:
						$sort = 'ws.accepted DESC, ws.status ASC, ws.proposed DESC';
					break;
			}
		}

		$sql = $fullinfo
				? "SELECT ws.*, v.helpful AS vote, m.importance AS myvote_imp, m.effort AS myvote_effort, xp.name AS authorname, "
				: "SELECT ws.id, ws.wishlist, ws.proposed, ws.granted, ws.granted_vid, ws.status, xp.name AS authorname ";

		if ($fullinfo)
		{
			if ($uid)
			{
				$sql .= "\n (SELECT count(*) FROM #__wishlist_vote AS wv WHERE wv.wishid=ws.id AND wv.userid=" . $uid . ") AS ranked,";
			}
			else
			{
				$sql .= "\n NULL AS ranked,";
			}
			// Get votes
			$sql .= "\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='wish' AND v.referenceid=ws.id) AS positive, ";
			$sql .= "\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='wish' AND v.referenceid=ws.id) AS negative, ";
			$sql .= "\n (SELECT COUNT(*) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id) AS num_votes, ";

			if (isset($filters['sortby']) && $filters['sortby'] == 'latestcomment')
			{
				$sql .= "\n (SELECT MAX(CC.created) FROM #__item_comments AS CC WHERE CC.item_id=ws.id AND CC.item_type='wish' GROUP BY CC.referenceid) AS latestcomment, ";
			}

			// Get xprofile info
			$sql .= "\n (SELECT xp.name FROM #__xprofiles AS xp WHERE xp.uidNumber=ws.granted_by) as grantedby, ";
			$sql .= "\n (SELECT xp.name FROM #__xprofiles AS xp WHERE xp.uidNumber=ws.assigned) as assignedto, ";

			// Get comments count
			//$sql .= "\n (SELECT count(*) FROM #__item_comments AS CC WHERE CC.item_id=ws.id AND CC.state=0 AND CC.item_type='wish') AS comments, ";
			//$sql .= "\n (SELECT count(*) FROM #__item_comments AS CC JOIN #__item_comments AS C2 ON C2.id=CC.item_id AND C2.item_type='wish' WHERE CC.state=0 AND CC.item_type='wish' AND C2.item_id=ws.id) AS commentreplies, ";
			//$sql .= "\n (SELECT count(*) FROM #__item_comments AS CC JOIN #__item_comments AS C2 ON C2.id=CC.item_id AND C2.item_type='wish' JOIN #__item_comments AS C3 ON C3.id=C2.parent AND C3.item_type='wish'  WHERE CC.state=0 AND CC.item_type='wish' AND C3.item_id=ws.id) AS replyreplies, ";
			//$sql .= "\n (SELECT comments + commentreplies + replyreplies) AS numreplies, ";
			$sql .= "\n (SELECT count(*) FROM #__item_comments AS CC WHERE CC.item_id=ws.id AND CC.state=1 AND CC.item_type='wish') AS numreplies, ";

			// Get abouse reports count
			//$sql .= "\n (SELECT count(*) FROM #__abuse_reports AS RR WHERE RR.referenceid=ws.id AND RR.state=0 AND RR.category='wish') AS reports, ";
			$sql .= "'0' AS reports, ";

			// Get averages
			$sql .= "\n (SELECT AVG(m.importance) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id) AS average_imp, ";
			$sql .= "\n (SELECT AVG(m.effort) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id AND m.effort!=6) AS average_effort, ";

			// Get bonus
			$sql .= "\n (SELECT SUM(amount) FROM #__users_transactions WHERE category='wish' AND referenceid=ws.id AND type='hold') AS bonus, ";
			$sql .= "\n (SELECT COUNT(DISTINCT uid) FROM #__users_transactions WHERE category='wish' AND referenceid=ws.id AND type='hold') AS bonusgivenby ";
		}
		$sql .= "\n FROM #__wishlist_item AS ws";
		$sql .= "\n JOIN #__xprofiles AS xp ON xp.uidNumber=ws.proposed_by ";
		if ($fullinfo)
		{
			//$sql .= "\n JOIN #__xprofiles AS xp ON xp.uidNumber=ws.proposed_by ";
			$sql .= "\n LEFT JOIN #__vote_log AS v ON v.referenceid=ws.id AND v.category='wish' AND v.voter='" . $uid . "' ";
			$sql .= "\n LEFT JOIN #__wishlist_vote AS m ON m.wishid=ws.id AND m.userid='" . $uid . "' ";
			if ($filters['tag'])
			{
				$sql .= "\n JOIN #__tags_object AS RTA ON RTA.objectid=ws.id AND RTA.tbl='wishlist' ";
				$sql .= "\n INNER JOIN #__tags AS TA ON RTA.tagid=TA.id ";
			}
		}

		$sql .= " WHERE 1=1 ";
		$sql .= ($listid > 0) ? " AND ws.wishlist='" . $listid . "'" : '';

		if (!$fullinfo && isset($filters['timelimit']))
		{
			$sql .= "\n OR (ws.status= 1 AND ws.granted > '" . $filters['timelimit'] . "') ";
		}

		if (!$fullinfo && isset($filters['versionid']))
		{
			$sql .= "\n OR (ws.granted_vid = '" . $filters['versionid'] . "') ";
		}

		if ($fullinfo && isset($filters['filterby']))
		{
			// list  filtering
			switch ($filters['filterby'])
			{
				case 'all':
					$sql .= ' AND ws.status!=2';
				break;
				case 'granted':
					$sql .= ' AND ws.status=1';
				break;
				case 'open':
					$sql .= ' AND ws.status=0';
				break;
				case 'accepted':
					$sql .= ' AND ws.accepted=1 AND ws.status=0';
				break;
				case 'pending':
					$sql .= ' AND ws.accepted=0 AND ws.status=0';
				break;
				case 'rejected':
					$sql .= ' AND ws.status=3';
				break;
				case 'withdrawn':
					$sql .= ' AND ws.status=4';
				break;
				case 'deleted':
					$sql .= ' AND ws.status=2';
				break;
				case 'useraccepted':
					$sql .= ' AND ws.accepted=3 AND ws.status!=2';
				break;
				case 'private':
					$sql .= ' AND ws.status!=2 AND ws.private=1';
				break;
				case 'public':
					$sql .= ' AND ws.status!=2 AND ws.private=0';
				break;
				case 'mine':
					if ($uid)
					{
						$sql .= ' AND ws.assigned="' . $uid . '" AND ws.status!=2';
					}
				break;
				case 'submitter':
					if ($uid)
					{
						$sql .= ' AND ws.proposed_by=' . $uid . ' AND ws.status!=2';
					}
				break;
				case 'assigned':
					$sql .= ' AND ws.assigned NOT NULL AND ws.status!=2';
				break;
				default:
					$sql .= ' AND ws.status!=2';
				break;
			}
		}

		// do not show private wishes
		if (!$admin)
		{
			$sql .= "\n AND ws.private='0'";
		}

		if ($fullinfo && isset($filters['search']) && $filters['search'])
		{
			$word = $this->_db->quote('%' . strtolower($filters['search']) . '%');
			$sql .= " AND ((LOWER(ws.subject) LIKE $word) OR (LOWER(ws.about) LIKE $word)) ";
		}
		if ($fullinfo && $filters['tag'])
		{
			$tagging = new Tags();
			$tags = $tagging->parseTags($filters['tag']);

			$sql .= " AND (RTA.objectid=ws.id AND (RTA.tbl='wishlist') AND (TA.tag IN ('" . implode("','", $tags) . "') OR TA.raw_tag IN ('" . implode("','", $tags) . "')))";
			$sql .= " GROUP BY ws.id ";
		}

		if (isset($filters['sort']))
		{
			if (!$filters['sort'])
			{
				$filters['sort'] = 'title';
			}
			if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
			{
				$filters['sort_Dir'] = 'ASC';
			}
			$sort =  $filters['sort'] . " " . $filters['sort_Dir'];
		}

		$sql .= "\n ORDER BY " . $sort;
		$sql .= (isset($filters['limit']) && $filters['limit'] > 0) ? " LIMIT " . $filters['start'] . ", " . $filters['limit'] : "";

		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Mark a record as deleted
	 *
	 * @param   integer  $wishid    Wish ID
	 * @param   integer  $withdraw  Withdraw a wish
	 * @return  boolean  False if error, True on success
	 */
	public function delete_wish($wishid, $withdraw=0)
	{
		if ($wishid === NULL)
		{
			$wishid == $this->id;
		}
		if ($wishid === NULL)
		{
			return false;
		}
		$status = $withdraw ? 4 : 2;

		$query  = "UPDATE $this->_tbl SET status='" . $status . "', ranking='0' WHERE id=" . $wishid;

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Delete a record and any associated content
	 *
	 * @param   integer  $oid  Record ID
	 * @return  boolean  True on success
	 */
	public function delete($oid=null)
	{
		$k = $this->_tbl_key;
		if ($oid)
		{
			$this->$k = intval($oid);
		}

		// Dlete attachments
		$this->_db->setQuery("SELECT * FROM `#__wish_attachments` WHERE wish=" . $this->_db->quote($this->$k));
		if (($attachments = $this->_db->loadObjectList()))
		{
			$config = Component::params('com_wishlist');

			$path = PATH_APP . DS . trim($config->get('webpath', '/site/wishes'), DS) . DS . $oid;

			foreach ($attachments as $attachment)
			{
				if (file_exists($path . DS . $attachment->filename) or !$attachment->filename)
				{
					// Attempt to delete the file
					if (!\Filesystem::delete($path . DS . $attachment->filename))
					{
						$this->setError(Lang::txt('UNABLE_TO_DELETE_FILE'));
					}
				}
			}
		}

		// Delete the plan
		$query = 'DELETE FROM `#__wishlist_implementation` WHERE wishid = ' . $this->_db->quote($this->$k);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Remove all tags
		include_once(dirname(__DIR__) . DS . 'models' . DS . 'tags.php');
		$wt = new Tags($oid);
		$wt->setTags('', User::get('id'), 1);

		// Delete the wish
		return parent::delete($oid);
	}

	/**
	 * Get a record based on some filters
	 *
	 * @param   integer  $wishid   Wish ID
	 * @param   integer  $uid      User ID
	 * @param   integer  $refid    Reference object ID
	 * @param   string   $cat      Reference object catgory
	 * @param   integer  $deleted  Record is deleted state?
	 * @return  mixed    False if error, object on success
	 */
	public function get_wish($wishid = 0, $uid = 0, $refid = 0, $cat = '', $deleted = 0)
	{
		if ($wishid === NULL)
		{
			return false;
		}

		$sql = "SELECT ws.*, v.helpful AS vote, m.importance AS myvote_imp, m.effort AS myvote_effort, xp.name AS authorname, ";
		if ($uid)
		{
			$sql .= "\n (SELECT count(*) FROM #__wishlist_vote AS wv WHERE wv.wishid=ws.id AND wv.userid=" . intval($uid) . ") AS ranked,";
		}
		$sql .= "\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='yes' AND v.category='wish' AND v.referenceid=ws.id) AS positive, ";
		$sql .= "\n (SELECT COUNT(*) FROM #__vote_log AS v WHERE v.helpful='no' AND v.category='wish' AND v.referenceid=ws.id) AS negative, ";

		// Get xprofile info
		$sql .= "\n (SELECT xp.name FROM #__xprofiles AS xp WHERE xp.uidNumber=ws.granted_by) as grantedby, ";
		$sql .= "\n (SELECT xp.name FROM #__xprofiles AS xp WHERE xp.uidNumber=ws.assigned) as assignedto, ";

		// Get comments count
		//$sql .= "\n (SELECT count(*) FROM #__item_comments AS CC WHERE CC.item_id=ws.id AND CC.state=0 AND CC.item_type='wish') AS comments, ";
		//$sql .= "\n (SELECT count(*) FROM #__item_comments AS CC JOIN #__item_comments AS C2 ON C2.id=CC.item_id AND C2.item_type='wish' WHERE CC.state=0 AND CC.item_type='wish' AND C2.item_id=ws.id) AS commentreplies, ";
		//$sql .= "\n (SELECT count(*) FROM #__item_comments AS CC JOIN #__item_comments AS C2 ON C2.id=CC.item_id AND C2.item_type='wish' JOIN #__item_comments AS C3 ON C3.id=C2.parent AND C3.item_type='wish'  WHERE CC.state=0 AND CC.item_type='wish' AND C3.item_id=ws.id) AS replyreplies, ";
		//$sql .= "\n (SELECT comments + commentreplies + replyreplies) AS numreplies, ";
		$sql .= "\n (SELECT count(*) FROM #__item_comments AS CC WHERE CC.item_id=ws.id AND CC.state=1 AND CC.item_type='wish') AS numreplies, ";

		// Get abouse reports count
		//$sql .= "\n (SELECT count(*) FROM #__abuse_reports AS RR WHERE RR.referenceid=ws.id AND RR.state=0 AND RR.category='wish') AS reports, ";
		$sql .= "'0' AS reports, ";

		$sql .= "\n (SELECT COUNT(*) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id) AS num_votes, ";
		$sql .= "\n (SELECT COUNT(*) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id AND m.effort=6) AS num_skipped_votes, "; // did anyone skip effort selection?
		$sql .= "\n (SELECT AVG(m.importance) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id) AS average_imp, ";
		$sql .= "\n (SELECT AVG(m.effort) FROM #__wishlist_vote AS m WHERE m.wishid=ws.id AND m.effort!=6) AS average_effort, ";
		$sql .= "\n (SELECT SUM(amount) FROM #__users_transactions WHERE category='wish' AND referenceid=ws.id AND type='hold') AS bonus, ";
		$sql .= "\n (SELECT COUNT(DISTINCT uid) FROM #__users_transactions WHERE category='wish' AND referenceid=ws.id AND type='hold') AS bonusgivenby ";

		$sql .= "\n FROM #__wishlist_item AS ws";
		if ($refid && $cat)
		{
			$sql .= "\n JOIN #__wishlist AS W ON W.id=ws.wishlist AND W.referenceid=" . $this->_db->quote($refid) . " AND W.category=" . $this->_db->quote($cat) . " ";
		}
		$sql .= "\n JOIN #__xprofiles AS xp ON xp.uidNumber=ws.proposed_by ";
		$sql .= "\n LEFT JOIN #__vote_log AS v ON v.referenceid=ws.id AND v.category='wish' AND v.voter=" . $this->_db->quote($uid) . " ";
		$sql .= "\n LEFT JOIN #__wishlist_vote AS m ON m.wishid=ws.id AND m.userid=" . $this->_db->quote($uid) . " ";
		$sql .= "\n WHERE ws.id=" . $this->_db->quote($wishid) . " ";
		if (!$deleted)
		{
			$sql .=" AND ws.status!=2";
		}

		$this->_db->setQuery($sql);
		$res = $this->_db->loadObjectList();
		$wish = ($res) ? $res[0] : array();

		return $wish;
	}

	/**
	 * Does the wish exist on this list?
	 *
	 * @param   integer  $wishid  Wish ID
	 * @param   integer  $listid  List ID
	 * @return  mixed    False if error, integer on success
	 */
	public function check_wish($wishid, $listid)
	{
		if ($wishid === NULL or $listid === NULL)
		{
			return false;
		}

		$query  = "SELECT id ";
		$query .= "FROM #__wishlist_item  ";
		$query .= "WHERE id = " . $this->_db->quote($wishid) . " AND wishlist=" . $this->_db->quote($listid) . " LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get an entry ID based off of some filtrs
	 *
	 * @param   string   $which    Sort records
	 * @param   integer  $id       Wish ID
	 * @param   integer  $listid   List ID
	 * @param   integer  $admin    Admin access?
	 * @param   integer  $uid      User ID.
	 * @param   array    $filters  Filters to build query from
	 * @return  mixed    False if error, integer on success
	 */
	public function getWishID($which, $id, $listid, $admin, $uid, $filters=array())
	{
		if ($which === NULL or $id === NULL or $listid === NULL)
		{
			return false;
		}

		$query  = "SELECT ws.id ";
		$query .= "FROM #__wishlist_item AS ws ";
		if (isset($filters['tag']) && $filters['tag']!='')
		{
			$query .= "\n JOIN #__tags_object AS RTA ON RTA.objectid=ws.id AND RTA.tbl='wishlist' ";
			$query .= "\n INNER JOIN #__tags AS TA ON RTA.tagid=TA.id ";
		}
		$query .= "WHERE ws.wishlist=" . $this->_db->quote($listid) . " AND ";
		$query .= ($which == 'prev')  ? "ws.id < " . $this->_db->quote($id) . " " : "ws.id > " . $this->_db->quote($id);

		if (isset($filters['filterby']))
		{
			switch ($filters['filterby'])
			{
				case 'all':
					$query .= ' AND ws.status!=2';
				break;
				case 'granted':
					$query .= ' AND ws.status=1';
				break;
				case 'open':
					$query .= ' AND ws.status=0';
				break;
				case 'accepted':
					$query .= ' AND ws.accepted=1 AND ws.status=0';
				break;
				case 'pending':
					$query .= ' AND ws.accepted=0 AND ws.status=0';
				break;
				case 'rejected':
					$query .= ' AND ws.status=3';
				break;
				case 'withdrawn':
					$query .= ' AND ws.status=4';
				break;
				case 'deleted':
					$query .= ' AND ws.status=2';
				break;
				case 'useraccepted':
					$query .= ' AND ws.accepted=3 AND ws.status!=2';
				break;
				case 'private':
					$query .= ' AND ws.status!=2 AND ws.private=1';
				break;
				case 'public':
					$query .= ' AND ws.status!=2 AND ws.private=0';
				break;
				case 'mine':
					if ($uid)
					{
						$query .= ' AND ws.assigned="' . $uid . '" AND ws.status!=2';
					}
				break;
				case 'assigned':
					$query .= ' AND ws.assigned NOT NULL AND ws.status!=2';
				break;
				default:
					$query .= ' AND ws.status!=2';
				break;
			}
		}
		else
		{
			$query .= ' AND ws.status!=2';
		}

		if (!$admin)
		{
			$query .="\n AND ws.private='0' ";
		}
		if (isset($filters['tag']) && $filters['tag']!='')
		{
			$tagging = new Tags();
			$tags = $tagging->parseTags($filters['tag']);

			$query .= " AND (RTA.objectid=ws.id AND (RTA.tbl='wishlist') AND (TA.tag IN ('" . implode("','", $tags) . "') OR TA.raw_tag IN ('" . implode("','", $tags) . "')))";
			$query .= " GROUP BY ws.id ";
		}
		$query .= ($which == 'prev') ? " ORDER BY ws.id DESC " : " ORDER BY ws.id ASC ";
		$query .= " LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get the vote count on an object
	 *
	 * @param   integer  $refid     Reference object ID
	 * @param   string   $category  Reference object category
	 * @param   integer  $uid       User ID
	 * @return  mixed    False if error, integer on success
	 */
	public function get_vote($refid, $category= 'wish', $uid)
	{
		if ($refid === NULL or $uid === NULL)
		{
			return false;
		}

		$query  = "SELECT v.helpful ";
		$query .= "FROM #__vote_log as v  ";
		$query .= "WHERE v.referenceid = " . $this->_db->quote($refid) . " AND v.category=" . $this->_db->quote($category) . " AND v.voter=" . $this->_db->quote($uid) . " LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}

