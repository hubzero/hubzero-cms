<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_members_groups' );

//-----------

class plgMembersGroups extends JPlugin
{
	function plgMembersGroups(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'members', 'groups' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onMembersAreas( $authorized )
	{
		if (!$authorized) {
			$areas = array();
		} else {
			$areas = array(
				'groups' => JText::_('GROUPS')
			);
		}

		return $areas;
	}

	//-----------

	function onMembers( $member, $option, $authorized, $areas )
	{
		$returnhtml = true;
		$returnmeta = true;
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onMembersAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onMembersAreas( $authorized ) ) )) {
				$returnhtml = false;
			}
		}
		
		if (!$authorized) {
			$returnhtml = false;
			$returnmeta = false;
		}

		$database =& JFactory::getDBO();
		
		//$groups = $this->getGroups( $member->get('uidNumber'), 'all' );
		$applicants = $this->getGroups( $member->get('uidNumber'), 'applicants' );
		$invitees = $this->getGroups( $member->get('uidNumber'), 'invitees' );
		$members = $this->getGroups( $member->get('uidNumber'), 'members' );
		$managers = $this->getGroups( $member->get('uidNumber'), 'managers' );
		
		$groups = array_merge($applicants, $invitees);
		$managerids = array();
		foreach ($managers as $manager) 
		{
			$groups[] = $manager;
			$managerids[] = $manager->cn;
		}
		foreach ($members as $mem) 
		{
			if (!in_array($mem->cn,$managerids)) {
				$groups[] = $mem;
			}
		}
		

		// Build the final HTML
		$out = '';
		if ($returnhtml) {
			$out  = MembersHtml::hed(3,'<a name="groups"></a>'.JText::_('GROUPS')).n;
			$out .= MembersHtml::aside(
						'<ul class="sub-nav">'.
						t.'<li><a href="'.JRoute::_('index.php?option=com_groups').'">'.JText::_('ALL_GROUPS').'</a></li>'.
						t.'<li><a href="'.JRoute::_('index.php?option=com_groups&task=new').'">'.JText::_('CREATE_GROUP').'</a></li>'.
						'</ul>'.
						'<p class="help"><strong>'.JText::_('WHAT_ARE_GROUPS').'</strong><br />'.JText::_('GROUPS_EXPLANATION').'</p>'
					);
			$out .= MembersHtml::subject( $this->summary($authorized,$groups,'com_groups') );
		}

		// Build the HTML meant for the "profile" tab's metadata overview
		$metadata = '';
		if ($returnmeta) {
			$metadata  = '<p class="groups"><a href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=groups').'">'.JText::sprintf('NUMBER_GROUPS',count($groups)).'</a></p>'.n;
		}

		$arr = array(
				'html'=>$out,
				'metadata'=>$metadata
			);

		return $arr;
	}

	//-----------

	public function summary($is_manager, $groups, $option)
	{
		$html  = t.'<table id="grouplist" summary="'.JText::_('GROUPS_TBL_SUMMARY').'">'.n;
		//$html .= t.t.'<caption>'.JText::_('GROUPS_TBL_CAPTION').'</caption>'.n;
		$html .= t.t.'<thead>'.n;
		$html .= t.t.t.'<tr>'.n;
		$html .= t.t.t.t.'<th scope="col">'.JText::_('GROUPS_TBL_TH_NAME').'</th>'.n;
		$html .= t.t.t.t.'<th scope="col">'.JText::_('GROUPS_TBL_TH_STATUS').'</th>'.n;
		$html .= t.t.t.t.'<th scope="col">'.JText::_('GROUPS_TBL_TH_OPTION').'</th>'.n;
		$html .= t.t.t.'</tr>'.n;
		$html .= t.t.'</thead>'.n;
		$html .= t.t.'<tbody>'.n;
		$i = 1;
		$cls = 'even';
		if ($groups) {
			foreach ($groups as $group) 
			{
				$cls = (($cls == 'even') ? 'odd' : 'even');
				$html .= t.t.t.'<tr class="'.$cls.'">'.n;
				$html .= t.t.t.t.'<td>';
				$html .= '<a href="'. JRoute::_('index.php?option=com_groups&gid='. $group->cn).'">'. htmlentities($group->description) .'</a>';
				$html .= '</td>'.n;
				/*$html .= t.t.t.t.'<td>';
				if ($group->manager && $group->published) {
					$html .= '<span class="manager status">'.JText::_('GROUP_MANAGER').'</span>';
					$html .= '</td>'.n;
					$html .= t.t.t.t.'<td>';
					$html .= '<a href="'.JRoute::_('index.php?option=com_groups&gid='. $group->cn.'&active=members') .'">'.JText::_('MANAGE_GROUP').'</a>';
					$html .= ' <a href="'.JRoute::_('index.php?option=com_groups&gid='. $group->cn.'&task=edit') .'">'.JText::_('EDIT_GROUP').'</a>';
					$html .= ' <a href="'.JRoute::_('index.php?option=com_groups&gid='. $group->cn.'&task=delete') .'">'.JText::_('DELETE_GROUP').'</a>';
				} else {
					if (!$group['confirmed']) {
						$html .= JText::_('GROUP_PENDING');
						$html .= '</td>'.n;
						$html .= t.t.t.t.'<td>';
					} else {
						if ($group['regconfirmed']) {
							$html .= '<span class="approved">'.JText::_('GROUP_MEMBER').'</span>';
							$html .= '</td>'.n;
							$html .= t.t.t.t.'<td>';
							$html .= '<a href="'.JRoute::_('index.php?option=com_groups&gid='. $group->cn.'&task=cancel') .'">'.JText::_('CANCEL_MEMBERSHIP').'</a>';
						} else {
							$html .= '<span class="pending">'.JText::_('GROUP_MEMBER_PENDING').'</span>';
							$html .= '</td>'.n;
							$html .= t.t.t.t.'<td>';
							$html .= '<a href="'.JRoute::_('index.php?option=com_groups&gid='. $group->cn.'&task=cancel') .'">'.JText::_('CANCEL_MEMBERSHIP').'</a>';
						}
					}
				}
				$html .= '</td>'.n;*/
				$html .= t.t.t.t.'<td>';
				if ($group->manager && $group->published) {
					$html .= '<span class="manager status">'.JText::_('GROUPS_STATUS_MANAGER').'</span>';
					$opt  = '<a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->cn.a.'active=members') .'">'.JText::_('GROUPS_ACTION_MANAGE').'</a>';
					$opt .= ' <a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->cn.a.'task=edit') .'">'.JText::_('GROUPS_ACTION_EDIT').'</a>';
					$opt .= ' <a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->cn.a.'task=delete') .'">'.JText::_('GROUPS_ACTION_DELETE').'</a>';
				} else {
					if (!$group->published) {
						$html .= JText::_('GROUPS_STATUS_NEW_GROUP');
					} else {
						if ($group->registered) {
							if ($group->regconfirmed) {
								$html .= '<span class="member status">'.JText::_('GROUPS_STATUS_APPROVED').'</span>';
								$opt = '<a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->cn.a.'task=cancel') .'">'.JText::_('GROUPS_ACTION_CANCEL').'</a>';
							} else {
								$html .= '<span class="pending status">'.JText::_('GROUPS_STATUS_PENDING').'</span>';
								$opt = '<a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->cn.a.'task=cancel') .'">'.JText::_('GROUPS_ACTION_CANCEL').'</a>';
							}
						} else {
							if ($group->regconfirmed) {
								$html .= '<span class="invitee status">'.JText::_('GROUPS_STATUS_INVITED').'</span>';
								$opt  = '<a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->cn.a.'task=accept') .'">'.JText::_('GROUPS_ACTION_ACCEPT').'</a>';
								$opt .= ' <a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->cn.a.'task=cancel') .'">'.JText::_('GROUPS_ACTION_CANCEL').'</a>';
							} else {
								$html .= '<span class="status"> </span>';
								$opt = '';
							}
						}
					}
				}
				$html .= '</td>'.n;
				$html .= t.t.t.t.'<td>'.$opt.'</td>';
				$html .= t.t.t.'</tr>'.n;
				$i++;
			}
			if ($i == 1) {
				$cls = (($cls == 'even') ? 'odd' : 'even');
				$html .= t.t.t.'<tr class="'.$cls.'">'.n;
				$html .= t.t.t.t.'<td colspan="3">'.JText::_('NO_GROUP_MEMBERSHIPS').'</td>'.n;
				$html .= t.t.t.'</tr>'.n;
			}
		} else {
			$cls = (($cls == 'even') ? 'odd' : 'even');
			$html .= t.t.t.'<tr class="'.$cls.'">'.n;
			$html .= t.t.t.t.'<td colspan="3">'.JText::_('NO_GROUPS').'</td>'.n;
			$html .= t.t.t.'</tr>'.n;
		}
		$html .= t.t.'</tbody>'.n;
		$html .= t.'</table>'.n;
		
		return $html;
	}
	
	//-----------
	
	private function getGroups($uid, $type='all')
	{
		$db =& JFactory::getDBO();

		// Get all groups the user is a member of
		$query1 = "SELECT g.published, g.description, g.cn, '1' AS registered, '0' AS regconfirmed, '0' AS manager FROM #__xgroups AS g, #__xgroups_applicants AS m WHERE g.type='1' AND m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;
		$query2 = "SELECT g.published, g.description, g.cn, '1' AS registered, '1' AS regconfirmed, '0' AS manager FROM #__xgroups AS g, #__xgroups_members AS m WHERE g.type='1' AND m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;
		$query3 = "SELECT g.published, g.description, g.cn, '1' AS registered, '1' AS regconfirmed, '1' AS manager FROM #__xgroups AS g, #__xgroups_managers AS m WHERE g.type='1' AND m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;
		$query4 = "SELECT g.published, g.description, g.cn, '0' AS registered, '1' AS regconfirmed, '0' AS manager FROM #__xgroups AS g, #__xgroups_invitees AS m WHERE g.type='1' AND m.gidNumber=g.gidNumber AND m.uidNumber=".$uid;
		
		switch ($type) 
		{
			case 'all':
				$query = "( $query1 ) UNION ( $query2 ) UNION ( $query3 ) UNION ( $query4 )";
			break;
			case 'applicants':
				$query = $query1;
			break;
			case 'members':
				$query = $query2;
			break;
			case 'managers':
				$query = $query3;
			break;
			case 'invitees':
				$query = $query4;
			break;
		}
		
		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (empty($result))
			return array();

		return $result;
	}
}