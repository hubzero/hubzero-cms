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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'WishlistOwner'
 * 
 * Long description (if any) ...
 */
class WishlistOwner extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var integer
	 */
	var $id       = NULL;  // @var int(11) Primary key


	/**
	 * Description for 'wishlist'
	 * 
	 * @var unknown
	 */
	var $wishlist = NULL;  // @var int(11)


	/**
	 * Description for 'userid'
	 * 
	 * @var unknown
	 */
	var $userid	  = NULL;  // @var int(11)

	//-----------


	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__wishlist_owners', 'id', $db );
	}

	/**
	 * Short description for 'delete_owner'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $listid Parameter description (if any) ...
	 * @param      string $uid Parameter description (if any) ...
	 * @param      unknown $admingroup Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function delete_owner($listid, $uid, $admingroup)
	{
		if ($listid === NULL or $uid === NULL) {
			return false;
		}

		$nativeowners = $this->get_owners($listid, $admingroup, 1);

		$quser =& JUser::getInstance( $uid );

		// cannot delete "native" owner (e.g. resource contributor)
		if (is_object($quser) && !in_array($quser->get('id'), $nativeowners, true)) {
			$query = "DELETE FROM $this->_tbl WHERE wishlist='". $listid."' AND userid='".$uid."'";
			$this->_db->setQuery( $query );
			$this->_db->query();
		}
	}

	/**
	 * Short description for 'save_owners'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $listid Parameter description (if any) ...
	 * @param      unknown $admingroup Parameter description (if any) ...
	 * @param      array $newowners Parameter description (if any) ...
	 * @param      integer $type Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function save_owners($listid, $admingroup, $newowners = array(), $type = 0)
	{
		if ($listid === NULL) {
			return false;
		}

		$owners = $this->get_owners($listid, $admingroup);

		if (count($newowners) > 0)  {
			foreach ($newowners as $no)
			{
				$quser =& JUser::getInstance( $no );
				if (is_object($quser) && !in_array($quser->get('id'), $owners['individuals'], true) && !in_array($quser->get('id'), $owners['advisory'], true)) {
					$this->id = 0;
					$this->userid = $quser->get('id');
					$this->wishlist = $listid;
					$this->type = $type;

					if (!$this->store()) {
						$this->setError( JText::_('Failed to add a user.') );
						return false;
					} else {
						// send email to added user
						$xhub =& Hubzero_Factory::getHub();
						$jconfig =& JFactory::getConfig();
						$admin_email = $jconfig->getValue('config.mailfrom');

						$kind = $type==2 ? JText::_('member of Advisory Committee') : JText::_('list administrator');
						$subject = JText::_('Wish List').', '.JText::_('You have been added as a').' '.$kind.' '.JText::_('FOR').' '.JText::_('Wish List').' #'.$listid;

						$from = array();
						$from['name']  = $jconfig->getValue('config.sitename').' '.JText::_('Wish List');
						$from['email'] = $jconfig->getValue('config.mailfrom');

						$message  = $subject.'. ';
						$message .= "\r\n\r\n";
						$message .= '----------------------------'."\r\n";
						$url = $xhub->getCfg('hubLongURL').JRoute::_('index.php?option=com_wishlist&id='.$listid);
					    $message .= JText::_('Please go to').' '.$url.' '.JText::_('to view the wish list and rank new wishes.');

						JPluginHelper::importPlugin( 'xmessage' );
						$dispatcher =& JDispatcher::getInstance();
						if (!$dispatcher->trigger( 'onSendMessage', array( 'wishlist_new_owner', $subject, $message, $from, array($quser->get('id')), 'com_wishlist'))) {
							$this->setError( JText::_('Failed to message new wish list owner.') );
						}
					}
				}
			}
		}
	}

	/**
	 * Short description for 'get_owners'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $listid Parameter description (if any) ...
	 * @param      unknown $admingroup Parameter description (if any) ...
	 * @param      mixed $wishlist Parameter description (if any) ...
	 * @param      integer $native Parameter description (if any) ...
	 * @param      mixed $wishid Parameter description (if any) ...
	 * @param      array $owners Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function get_owners($listid, $admingroup, $wishlist='', $native=0, $wishid=0, $owners = array())
	{
		ximport('Hubzero_Group');

		if ($listid === NULL) {
			return false;
		}

		$obj = new Wishlist( $this->_db );
		$objG = new WishlistOwnerGroup( $this->_db );
		if (!$wishlist) {
			$wishlist = $obj->get_wishlist($listid);
		}

		// if private user list, add the user
		if ($wishlist->category == 'user') {
			$owners[] = $wishlist->referenceid;
		}

		// if resource, get contributors
		if ($wishlist->category=='resource' &&  $wishlist->resource->type!='7') {
			$cons = $obj->getCons($wishlist->referenceid);
			if ($cons) {
				foreach ($cons as $con)
				{
					$owners[] = $con->id;
				}
			}
		}

		// get groups		
		$groups = $objG->get_owner_groups($listid, $admingroup, $wishlist, $native);
		if ($groups) {
			foreach ($groups as $g)
			{
				// Load the group
				$group = Hubzero_Group::getInstance( $g );
				$members = $group->get('members');
				$managers = $group->get('managers');
				$members = array_merge($members, $managers);
				if ($members) {
					foreach ($members as $member)
					{
						$owners[] = $member;
					}
				}
			}
		}

		// get individuals
		if (!$native) {
			$sql = "SELECT o.userid"
				. "\n FROM #__wishlist_owners AS o "
				. "\n WHERE o.wishlist='".$listid."' AND o.type!=2";

			$this->_db->setQuery( $sql );
			$results =  $this->_db->loadObjectList();
			if ($results) {
				foreach ($results as $result)
				{
					$owners[] = $result->userid;
				}
			}
		}

		$owners = array_unique($owners);
		sort($owners);

		// are we also including advisory committee?
		$wconfig =& JComponentHelper::getParams( 'com_wishlist' );
		$allow_advisory = $wconfig->get('allow_advisory');
		$advisory = array();

		if ($allow_advisory) {
			$sql = "SELECT DISTINCT o.userid"
					. "\n FROM #__wishlist_owners AS o "
					. "\n WHERE o.wishlist='".$listid."' AND o.type=2";

			$this->_db->setQuery( $sql );
			$results =  $this->_db->loadObjectList();
			if ($results) {
				foreach ($results as $result)
				{
					$advisory[] = $result->userid;
				}
			}
		}

		// find out those who voted - for distribution of points
		if ($wishid) {
			$activeowners = array();
			$query  = "SELECT v.userid ";
			$query .= "FROM #__wishlist_vote AS v LEFT JOIN #__wishlist_item AS i ON v.wishid = i.id ";
			$query .= "WHERE i.wishlist = '".$listid."' AND v.wishid='".$wishid."' AND (v.userid IN (";
			$tquery = '';
			foreach ($owners as $o)
			{
				$tquery .= "'".$o."',";
			}
			$tquery = substr($tquery,0,strlen($tquery) - 1);
			$query .= $tquery.")) ";

			$this->_db->setQuery( $query );
			$result = $this->_db->loadObjectList();
			if ($result) {
				foreach ($result as $r)
				{
					$activeowners[] = $r->userid;
				}

				$owners = $activeowners;
			}
		}

		$collect = array();
		$collect['individuals'] = $owners;
		$collect['groups'] = $groups;
		$collect['advisory'] = $advisory;

		return $collect;
	}
}

