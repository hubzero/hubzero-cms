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

//-------------------------------------------------------------
// Joomla module
// "My Groups"
//    This module displays support tickets assigned to the 
//    user currently logged in.
// XGroup library REQUIRED
//-------------------------------------------------------------

class modMyGroups
{
	private $attributes = array();

	//-----------

	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
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
		$db->query();

		$result = $db->loadObjectList();

		if (empty($result))
			return array();

		return $result;
	}
	
	//-----------
	
	private function getStatus( $group ) 
	{
		if ($group->manager) {
			$status = 'manager';
		} else {
			if ($group->registered) {
				if ($group->regconfirmed) {
					$status = 'member';
				} else {
					$status = 'pending';
				}
			} else {
				if ($group->regconfirmed) {
					$status = 'invitee';
				} else {
					$status = '';
				}
			}
		}
		return $status;
	}

	//-----------
	
	public function display() 
	{
		$juser =& JFactory::getUser();
		$database =& JFactory::getDBO();
		
		// Get the module parameters
		$params =& $this->params;
		$moduleclass = $params->get( 'moduleclass' );
		$limit = intval( $params->get( 'limit' ) );
		$limit = ($limit) ? $limit : 10;

		// Get the user's groups
		$applicants = $this->getGroups( $juser->get('id'), 'applicants' );
		$invitees = $this->getGroups( $juser->get('id'), 'invitees' );
		$members = $this->getGroups( $juser->get('id'), 'members' );
		$managers = $this->getGroups( $juser->get('id'), 'managers' );

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
		
		// Push the module CSS to the template
		ximport('xdocument');
		XDocument::addModuleStyleSheet('mod_mygroups');
		
		// Build the HTML
		$html  = '<div';
		$html .= ($moduleclass) ? ' class="'.$moduleclass.'">'."\n" : '>'."\n";
		if ($groups && count($groups) > 0) {
			$html .= "\t".'<ul class="compactlist">'."\n";
			$i = 0;
			foreach ($groups as $group)
			{
				if ($group->published && $i < $limit) {
					$status = $this->getStatus( $group );

					$html .= "\t\t".'<li class="group">'."\n";
					$html .= "\t\t\t".'<a href="'.JRoute::_('index.php?option=com_groups&gid='.$group->cn).'">'.$group->description.'</a>'."\n";
					$html .= "\t\t\t".'<span><span class="'.$status.' status">'.JText::_('GROUPS_STATUS_'.strtoupper($status)).'</span></span>'."\n";
					$html .= "\t\t".'</li>';
					$i++;
				}
			}
			$html .= "\t".'</ul>'."\n";
		} else {
			$html .= "\t".'<p>'.JText::_('NO_GROUPS').'</p>'."\n";
		}
		$html .= "\t".'<ul class="module-nav">'."\n";
		$html .= "\t\t".'<li><a href="'.JRoute::_('index.php?option=com_members&id='.$juser->get('id').'&active=groups').'">'.JText::_('All My Groups').'</a></li>'."\n";
		$html .= "\t\t".'<li><a href="'.JRoute::_('index.php?option=com_groups').'">'.JText::_('ALL_GROUPS').'</a></li>'."\n";
		$html .= "\t\t".'<li><a href="'.JRoute::_('index.php?option=com_groups&task=new').'">'.JText::_('NEW_GROUP').'</a></li>'."\n";
		$html .= "\t".'</ul>'."\n";
		$html .= '</div>'."\n";
		
		// Output the HTML
		return $html;
	}
}

//-------------------------------------------------------------

$modmygroups = new modMyGroups();
$modmygroups->params = $params;

require( JModuleHelper::getLayoutPath('mod_mygroups') );
?>
