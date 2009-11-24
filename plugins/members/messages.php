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
JPlugin::loadLanguage( 'plg_members_messages' );

//-----------

class plgMembersMessages extends JPlugin
{
	public function plgMembersMessages(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'members', 'messages' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onMembersAreas( $authorized ) 
	{
		if (!$authorized) {
			$areas = array();
		} else {
			$areas = array(
				'messages' => JText::_('MESSAGES')
			);
		}
		return $areas;
	}

	//-----------

	public function onMembers( $member, $option, $authorized, $areas )
	{
		$returnhtml = true;
		
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
		
		// Is the user logged in?
		if (!$authorized) {
			return $arr;
		}
		
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onMembersAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onMembersAreas( $authorized ) ) )) {
				$returnhtml = false;
			}
		}
		
		// Get our database object
		$database =& JFactory::getDBO();
		
		// Load some needed libraries
		ximport('xmessage');
		
		// Are we returning HTML?
		if ($returnhtml) {
			$task = JRequest::getVar('action','');
			if (!$task) {
				$task = JRequest::getVar('inaction','');
			}
			
			$mid = JRequest::getInt('msg',0);
			if ($mid) {
				$task = 'view';
			}
			
			switch ($task) 
			{
				case 'sendtoarchive': $arr['html'] = $this->sendtoarchive($database, $option, $member); break;
				case 'sendtotrash':   $arr['html'] = $this->sendtotrash($database, $option, $member);   break;
				case 'sendtoinbox':   $arr['html'] = $this->sendtoinbox($database, $option, $member);   break;
				case 'markasread':    $arr['html'] = $this->markasread($database, $option, $member);    break;
				case 'savesettings':  $arr['html'] = $this->savesettings($database, $option, $member);  break;
				case 'emptytrash':    $arr['html'] = $this->emptytrash($database, $option, $member);    break;
				case 'delete':        $arr['html'] = $this->delete($database, $option, $member);        break;
				
				case 'view':          $arr['html'] = $this->view($database, $option, $member, $mid);    break;
				case 'settings':      $arr['html'] = $this->settings($database, $option, $member);      break;
				case 'archive':       $arr['html'] = $this->archive($database, $option, $member);       break;
				case 'trash':         $arr['html'] = $this->trash($database, $option, $member);         break;
				case 'inbox': 
				default: $arr['html'] = $this->inbox($database, $option, $member); break;
			}
		} else {
			$arr['metadata'] = '<p class="messages"><a href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages').'">'.JText::_('MESSAGES').'</a></p>'.n;
		}

		// Return data
		return $arr;
	}
	
	//-----------
	
	private function subMenu($option, $member, $task='inbox', $counts=array()) 
	{
		//$html  = '<div id="sub-sub-menu">'.n;
		$html  = t.'<ul>'.n;
		$html .= t.t.'<li';    
		if ($task == 'inbox') {
			$html .= ' class="active"';
		}
		$html .= '><a class="box" href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages&task=inbox').'"><span>'.JText::_('MESSAGES_INBOX');
		if (isset($counts['inbox'])) {
			$html .= ' ('.$counts['inbox'].')';
		}
		$html .= '</span></a></li>'.n;
		$html .= t.t.'<li';  
	    if ($task == 'archive') {
			$html .= ' class="active"';
		}
		$html .= '><a class="archive" href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages&task=archive').'"><span>'.JText::_('MESSAGES_ARCHIVE');
		if (isset($counts['archive'])) {
			$html .= ' ('.$counts['archive'].')';
		}
		$html .= '</span></a></li>'.n;
		$html .= t.t.'<li';  
	    if ($task == 'trash' || $task == 'emptytrash') {
			$html .= ' class="active"';
		}
		$html .= '><a class="trash" href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages&task=trash').'"><span>'.JText::_('MESSAGES_TRASH');
		if (isset($counts['trash'])) {
			$html .= ' ('.$counts['trash'].')';
		}
		$html .= '</span></a></li>'.n;
		$html .= t.t.'<li';  
	    if ($task == 'settings' || $task == 'savesettings') {
			$html .= ' class="active"';
		}
		$html .= '><a class="config" href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages&task=settings').'"><span>'.JText::_('MESSAGES_SETTINGS').'</span></a></li>'.n;
		$html .= t.'</ul>'.n;
		//$html .= '</div>'.n;

	    return $html;
	}
	
	//-----------
	
	public function inbox($database, $option, $member) 
	{
		// Push some scripts to the template
		$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.DS.'plugins'.DS.'members'.DS.'messages.js')) {
			$document->addScript('plugins'.DS.'members'.DS.'messages.js');
		}
		
		// Filters for returning results
		$filters = array();
		$filters['limit'] = JRequest::getInt('limit', 25);
		$filters['start'] = JRequest::getInt('limitstart', 0);
		$filters['state'] = 0;
		$filter = JRequest::getVar('filter', '');
		$filters['filter'] = ($filter) ? 'com_'.$filter : '';
		
		$recipient = new XMessageRecipient( $database );
		
		$total = $recipient->getMessagesCount( $member->get('uidNumber'), $filters );
		
		$rows = $recipient->getMessages( $member->get('uidNumber'), $filters );

		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		$xmc = new XMessageComponent( $database );
		$components = $xmc->getComponents();

		$cls = 'even';
		
		$sbjt  = '<form action="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages').'" method="post" id="hubForm" class="full">'.n;
		$sbjt .= t.'<fieldset id="filters">'.n;
		$sbjt .= t.t.'<input type="hidden" name="inaction" value="inaction" />'.n;
		$sbjt .= t.t.'From: <select class="option" name="filter">'.n;
		$sbjt .= t.t.t.'<option value="">'.JText::_('All').'</option>'.n;
		if ($components) {
			foreach ($components as $component) 
			{
				$component = substr($component, 4);
				$sbjt .= t.t.t.'<option value="'.$component.'"';
				$sbjt .= ($component == $filter) ? ' selected="selected"' : '';
				$sbjt .= '>'.$component.'</option>'.n;
			}
		}
		$sbjt .= t.t.'</select> '.n;
		$sbjt .= t.t.'<input class="option" type="submit" value="Filter" />'.n;
		$sbjt .= t.'</fieldset>'.n;
		$sbjt .= t.'<fieldset id="actions">'.n;
		$sbjt .= t.t.'<select class="option" name="action">'.n;
		$sbjt .= t.t.t.'<option value="">'.JText::_('MSG_WITH_SELECTED').'</option>'.n;
		$sbjt .= t.t.t.'<option value="markasread">'.JText::_('MSG_MARK_AS_READ').'</option>'.n;
		$sbjt .= t.t.t.'<option value="sendtoarchive">'.JText::_('MSG_SEND_TO_ARCHIVE').'</option>'.n;
		$sbjt .= t.t.t.'<option value="sendtotrash">'.JText::_('MSG_SEND_TO_TRASH').'</option>'.n;
		$sbjt .= t.t.'</select> '.n;
		$sbjt .= t.t.'<input class="option" type="submit" value="'.JText::_('MSG_APPLY').'" />'.n;
		$sbjt .= t.'</fieldset>'.n;
		$sbjt .= t.'<table class="data" summary="'.JText::_('TBL_SUMMARY_OVERVIEW').'">'.n;
		$sbjt .= t.t.'<thead>'.n;
		$sbjt .= t.t.t.'<tr>'.n;
		$sbjt .= t.t.t.t.'<th scope="col"><input type="checkbox" name="msgall" id="msgall" value="all"  onclick="HUB.MembersMsg.checkAll(this, \'chkbox\');" /></th>'.n;
		$sbjt .= t.t.t.t.'<th scope="col"> </th>'.n;
		$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('Subject').'</th>'.n;
		$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('From').'</th>'.n;
		$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('Date Received').'</th>'.n;
		//$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('Expires').'</th>'.n;
		$sbjt .= t.t.t.t.'<th scope="col"> </th>'.n;
		$sbjt .= t.t.t.'</tr>'.n;
		$sbjt .= t.t.'</thead>'.n;
		$sbjt .= t.t.'<tfoot>'.n;
		$sbjt .= t.t.t.'<tr>'.n;
		$sbjt .= t.t.t.t.'<td colspan="6">'.n;
		$pagenavhtml = $pageNav->getListFooter();
		$pagenavhtml = str_replace('members/?','members/'.$member->get('uidNumber').'/messages/inbox/?',$pagenavhtml);
		$pagenavhtml = str_replace('action=inbox','',$pagenavhtml);
		$pagenavhtml = str_replace('&amp;&amp;','&amp;',$pagenavhtml);
		$pagenavhtml = str_replace('?&amp;','?',$pagenavhtml);
		$sbjt .= t.t.t.t.t.$pagenavhtml;
		$sbjt .= t.t.t.t.'</td>'.n;
		$sbjt .= t.t.t.'</tr>'.n;
		$sbjt .= t.t.'</tfoot>'.n;
		$sbjt .= t.t.'<tbody>'.n;
		if ($rows) {
			foreach ($rows as $row) 
			{
				if ($row->whenseen != '' && $row->whenseen != '0000-00-00 00:00:00') {
					$status = '<span class="read status"></span>';
					$lnkcls = '';
				} else {
					$status = '<span class="unread status">*</span>';
					$lnkcls = 'class="unread" ';
				}
				if (substr($row->component,0,4) == 'com_') {
					$row->component = substr($row->component,4);
				}

				if ($row->component == 'support') {
					$fg = explode(' ',$row->subject);
					$fh = array_pop($fg);
					$row->subject = implode(' ',$fg);
				}
				
				$url = JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages'.a.'msg='.$row->id);
				
				$cls = (($cls == 'even') ? 'odd' : 'even');
				if ($row->actionid) {
					$xma = new XMessageAction( $database );
					$xma->load( $row->actionid );
					if ($xma) {
						$url = JRoute::_(stripslashes($xma->description));
					}
				}
				$sbjt .= t.t.t.'<tr class="'.$cls;
				if ($row->actionid) {
					$sbjt .= ' actionitem';
				}
				$sbjt .= '">'.n;
				if ($row->actionid && ($row->whenseen == '' || $row->whenseen == '0000-00-00 00:00:00')) {
					$sbjt .= t.t.t.t.'<td class="check"> </td>'.n;
				} else {
					$sbjt .= t.t.t.t.'<td class="check"><input class="chkbox" type="checkbox" name="mid[]" id="msg'.$row->id.'" value="'.$row->id.'" /></td>'.n;
				}
				$sbjt .= t.t.t.t.'<td class="sttus">'.$status.'</td>'.n;
				$sbjt .= t.t.t.t.'<td><a '.$lnkcls.'href="'.$url.'">'.stripslashes($row->subject).'</a></td>'.n;
				$sbjt .= t.t.t.t.'<td>'.$row->component.'</td>'.n;
				$sbjt .= t.t.t.t.'<td>'.JHTML::_('date', $row->created, '%d %b, %Y %I:%M %p').'</td>'.n;
				//$sbjt .= t.t.t.t.'<td>'.JHTML::_('date', $row->expires, '%d %b, %Y').'</td>'.n;
				if ($row->actionid && ($row->whenseen == '' || $row->whenseen == '0000-00-00 00:00:00')) {
					$sbjt .= t.t.t.t.'<td> </td>'.n;
				} else {
					$sbjt .= t.t.t.t.'<td><a class="trash" href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages'.a.'mid[]='.$row->id.a.'action=sendtotrash').'" title="'.JText::_('MESSAGES_TRASH').'">'.JText::_('MESSAGES_TRASH').'</a></td>'.n;
				}
				$sbjt .= t.t.t.'</tr>'.n;
			}
		} else {
			$cls = (($cls == 'even') ? 'odd' : 'even');
			$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
			$sbjt .= t.t.t.t.'<td colspan="6">'.JText::_('No messages found').'</td>'.n;
			$sbjt .= t.t.t.'</tr>'.n;
		}
		$sbjt .= t.t.'</tbody>'.n;
		$sbjt .= t.'</table>'.n;
		$sbjt .= '</form>'.n;
		
		$html  = MembersHtml::hed(3,'<a name="messages"></a>'.JText::_('MESSAGES')).n;
		$html .= '<div class="withleft">'.n;
		$html .= MembersHtml::aside( $this->subMenu($option, $member, 'inbox') );
		$html .= MembersHtml::subject($sbjt);
		$html .= '</div>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function archive($database, $option, $member) 
	{
		// Push some scripts to the template
		$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.DS.'plugins'.DS.'members'.DS.'messages.js')) {
			$document->addScript('plugins'.DS.'members'.DS.'messages.js');
		}
		
		// Filters for returning results
		$filters = array();
		$filters['limit'] = JRequest::getInt('limit', 10);
		$filters['start'] = JRequest::getInt('limitstart', 0);
		$filters['state'] = 1;
		$filter = JRequest::getVar('filter', '');
		$filters['filter'] = ($filter) ? 'com_'.$filter : '';
		
		$recipient = new XMessageRecipient( $database );
		
		$total = $recipient->getMessagesCount( $member->get('uidNumber'), $filters );
		
		$rows = $recipient->getMessages( $member->get('uidNumber'), $filters );

		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		$xmc = new XMessageComponent( $database );
		$components = $xmc->getComponents();

		$cls = 'even';
		
		$sbjt  = '<form action="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages'.a.'task=archive').'" method="post" id="hubForm" class="full">'.n;
		$sbjt .= t.'<fieldset id="filters">'.n;
		$sbjt .= t.t.'<input type="hidden" name="inaction" value="archive" />'.n;
		$sbjt .= t.t.'From: <select class="option" name="filter">'.n;
		$sbjt .= t.t.t.'<option value="">'.JText::_('All').'</option>'.n;
		if ($components) {
			foreach ($components as $component) 
			{
				$component = substr($component, 4);
				$sbjt .= t.t.t.'<option value="'.$component.'"';
				$sbjt .= ($component == $filter) ? ' selected="selected"' : '';
				$sbjt .= '>'.$component.'</option>'.n;
			}
		}
		$sbjt .= t.t.'</select> '.n;
		$sbjt .= t.t.'<input class="option" type="submit" value="Filter" />'.n;
		$sbjt .= t.'</fieldset>'.n;
		$sbjt .= t.'<fieldset id="actions">'.n;
		$sbjt .= t.t.'<select class="option" name="action">'.n;
		$sbjt .= t.t.t.'<option value="">'.JText::_('MSG_WITH_SELECTED').'</option>'.n;
		$sbjt .= t.t.t.'<option value="sendtoinbox">'.JText::_('MSG_SEND_TO_INBOX').'</option>'.n;
		$sbjt .= t.t.t.'<option value="sendtotrash">'.JText::_('MSG_SEND_TO_TRASH').'</option>'.n;
		$sbjt .= t.t.'</select> '.n;
		$sbjt .= t.t.'<input class="option" type="submit" value="'.JText::_('MSG_APPLY').'" />'.n;
		$sbjt .= t.'</fieldset>'.n;
		$sbjt .= t.'<table class="data" summary="'.JText::_('TBL_SUMMARY_OVERVIEW').'">'.n;
		$sbjt .= t.t.'<thead>'.n;
		$sbjt .= t.t.t.'<tr>'.n;
		$sbjt .= t.t.t.t.'<th scope="col"><input type="checkbox" name="msgall" id="msgall" value="all"  onclick="HUB.MembersMsg.checkAll(this, \'chkbox\');" /></th>'.n;
		$sbjt .= t.t.t.t.'<th scope="col"> </th>'.n;
		$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('Subject').'</th>'.n;
		$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('Date Received').'</th>'.n;
		//$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('Expires').'</th>'.n;
		$sbjt .= t.t.t.t.'<th scope="col"> </th>'.n;
		$sbjt .= t.t.t.'</tr>'.n;
		$sbjt .= t.t.'</thead>'.n;
		$sbjt .= t.t.'<tfoot>'.n;
		$sbjt .= t.t.t.'<tr>'.n;
		$sbjt .= t.t.t.t.'<td colspan="5">'.n;
		$pagenavhtml = $pageNav->getListFooter();
		$pagenavhtml = str_replace('members/?','members/'.$member->get('uidNumber').'/messages/archive/?',$pagenavhtml);
		$pagenavhtml = str_replace('action=archive','',$pagenavhtml);
		$pagenavhtml = str_replace('&amp;&amp;','&amp;',$pagenavhtml);
		$pagenavhtml = str_replace('?&amp;','?',$pagenavhtml);
		$sbjt .= t.t.t.t.t.$pagenavhtml;
		$sbjt .= t.t.t.t.'</td>'.n;
		$sbjt .= t.t.t.'</tr>'.n;
		$sbjt .= t.t.'</tfoot>'.n;
		$sbjt .= t.t.'<tbody>'.n;
		if ($rows) {
			foreach ($rows as $row) 
			{
				if ($row->whenseen != '' && $row->whenseen != '0000-00-00 00:00:00') {
					$status = '<span class="read status"></span>';
					$lnkcls = '';
				} else {
					$status = '<span class="unread status">*</span>';
					$lnkcls = 'class="unread" ';
				}
				
				if (substr($row->component,0,4) == 'com_') {
					$row->component = substr($row->component,4);
				}

				if ($row->component == 'support') {
					$fg = explode(' ',$row->subject);
					$fh = array_pop($fg);
					$row->subject = implode(' ',$fg);
				}
				
				$cls = (($cls == 'even') ? 'odd' : 'even');
				$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
				$sbjt .= t.t.t.t.'<td class="check"><input class="chkbox" type="checkbox" name="mid[]" id="msg'.$row->id.'" value="'.$row->id.'" /></td>'.n;
				$sbjt .= t.t.t.t.'<td class="sttus">'.$status.'</td>'.n;
				$sbjt .= t.t.t.t.'<td><a '.$lnkcls.'href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages'.a.'msg='.$row->id).'">'.stripslashes($row->subject).'</a></td>'.n;
				$sbjt .= t.t.t.t.'<td>'.JHTML::_('date', $row->created, '%d %b, %Y').'</td>'.n;
				//$sbjt .= t.t.t.t.'<td>'.JHTML::_('date', $row->expires, '%d %b, %Y').'</td>'.n;
				$sbjt .= t.t.t.t.'<td><a class="trash" href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages'.a.'mid[]='.$row->id.a.'action=sendtotrash').'" title="'.JText::_('MESSAGES_TRASH').'">'.JText::_('MESSAGES_TRASH').'</a></td>'.n;
				$sbjt .= t.t.t.'</tr>'.n;
			}
		} else {
			$cls = (($cls == 'even') ? 'odd' : 'even');
			$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
			$sbjt .= t.t.t.t.'<td colspan="4">'.JText::_('No messages found').'</td>'.n;
			$sbjt .= t.t.t.'</tr>'.n;
		}
		$sbjt .= t.t.'</tbody>'.n;
		$sbjt .= t.'</table>'.n;
		$sbjt .= '</form>'.n;
		
		$html  = MembersHtml::hed(3,'<a name="messages"></a>'.JText::_('MESSAGES')).n;
		$html .= '<div class="withleft">'.n;
		$html .= MembersHtml::aside( $this->subMenu($option, $member, 'archive') );
		$html .= MembersHtml::subject($sbjt);
		$html .= '</div>'.n;
		
		return $html;
	}

	//-----------
	
	public function trash($database, $option, $member) 
	{
		// Push some scripts to the template
		$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.DS.'plugins'.DS.'members'.DS.'messages.js')) {
			$document->addScript('plugins'.DS.'members'.DS.'messages.js');
		}
		
		// Filters for returning results
		$filters = array();
		$filters['limit'] = JRequest::getInt('limit', 10);
		$filters['start'] = JRequest::getInt('limitstart', 0);
		$filters['state'] = 2;
		$filter = JRequest::getVar('filter', '');
		$filters['filter'] = ($filter) ? 'com_'.$filter : '';
		
		$recipient = new XMessageRecipient( $database );
		
		$total = $recipient->getMessagesCount( $member->get('uidNumber'), $filters );
		
		$rows = $recipient->getMessages( $member->get('uidNumber'), $filters );

		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		$xmc = new XMessageComponent( $database );
		$components = $xmc->getComponents();

		$cls = 'even';
		
		$sbjt  = '<form action="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages'.a.'task=trash').'" method="post" id="hubForm" class="full">'.n;
		$sbjt .= t.'<fieldset id="filters">'.n;
		$sbjt .= t.t.'<input type="hidden" name="inaction" value="trash" />'.n;
		$sbjt .= t.t.'From: <select class="option" name="filter">'.n;
		$sbjt .= t.t.t.'<option value="">'.JText::_('All').'</option>'.n;
		if ($components) {
			foreach ($components as $component) 
			{
				$component = substr($component, 4);
				$sbjt .= t.t.t.'<option value="'.$component.'"';
				$sbjt .= ($component == $filter) ? ' selected="selected"' : '';
				$sbjt .= '>'.$component.'</option>'.n;
			}
		}
		$sbjt .= t.t.'</select> '.n;
		$sbjt .= t.t.'<input class="option" type="submit" value="Filter" />'.n;
		$sbjt .= t.'</fieldset>'.n;
		$sbjt .= t.'<fieldset id="actions">'.n;
		$sbjt .= t.t.t.t.t.'<select class="option" name="action">'.n;
		$sbjt .= t.t.t.t.t.t.'<option value="">'.JText::_('MSG_WITH_SELECTED').'</option>'.n;
		$sbjt .= t.t.t.t.t.t.'<option value="sendtoinbox">'.JText::_('MSG_SEND_TO_INBOX').'</option>'.n;
		$sbjt .= t.t.t.t.t.t.'<option value="sendtoarchive">'.JText::_('MSG_SEND_TO_ARCHIVE').'</option>'.n;
		$sbjt .= t.t.t.t.t.t.'<option value="delete">'.JText::_('MSG_DELETE').'</option>'.n;
		$sbjt .= t.t.t.t.t.'</select> '.n;
		$sbjt .= t.t.t.t.t.'<input class="option" type="submit" value="'.JText::_('MSG_APPLY').'" />'.n;
		$sbjt .= t.'</fieldset>'.n;
		$sbjt .= t.'<table class="data" summary="'.JText::_('TBL_SUMMARY_OVERVIEW').'">'.n;
		$sbjt .= t.t.'<thead>'.n;
		$sbjt .= t.t.t.'<tr>'.n;
		$sbjt .= t.t.t.t.'<th scope="col"><input type="checkbox" name="msgall" id="msgall" value="all"  onclick="HUB.MembersMsg.checkAll(this, \'chkbox\');" /></th>'.n;
		$sbjt .= t.t.t.t.'<th scope="col"> </th>'.n;
		$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('Subject').'</th>'.n;
		$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('Date Received').'</th>'.n;
		//$sbjt .= t.t.t.t.'<th scope="col">'.JText::_('Expires').'</th>'.n;
		$sbjt .= t.t.t.'</tr>'.n;
		$sbjt .= t.t.'</thead>'.n;
		$sbjt .= t.t.'<tfoot>'.n;
		$sbjt .= t.t.t.'<tr>'.n;
		$sbjt .= t.t.t.t.'<td colspan="4">'.n;
		$pagenavhtml = $pageNav->getListFooter();
		$pagenavhtml = str_replace('members/?','members/'.$member->get('uidNumber').'/messages/trash/?',$pagenavhtml);
		$pagenavhtml = str_replace('action=trash','',$pagenavhtml);
		$pagenavhtml = str_replace('&amp;&amp;','&amp;',$pagenavhtml);
		$pagenavhtml = str_replace('?&amp;','?',$pagenavhtml);
		$sbjt .= t.t.t.t.t.$pagenavhtml;
		$sbjt .= t.t.t.t.'</td>'.n;
		$sbjt .= t.t.t.'</tr>'.n;
		$sbjt .= t.t.'</tfoot>'.n;
		$sbjt .= t.t.'<tbody>'.n;
		if ($rows) {
			foreach ($rows as $row) 
			{
				if ($row->whenseen != '' && $row->whenseen != '0000-00-00 00:00:00') {
					$status = '<span class="read status"></span>';
					$lnkcls = '';
				} else {
					$status = '<span class="unread status">*</span>';
					$lnkcls = 'class="unread" ';
				}
				
				if (substr($row->component,0,4) == 'com_') {
					$row->component = substr($row->component,4);
				}

				if ($row->component == 'support') {
					$fg = explode(' ',$row->subject);
					$fh = array_pop($fg);
					$row->subject = implode(' ',$fg);
				}
				
				$cls = (($cls == 'even') ? 'odd' : 'even');
				$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
				$sbjt .= t.t.t.t.'<td class="check"><input class="chkbox" type="checkbox" name="mid[]" id="msg'.$row->id.'" value="'.$row->id.'" /></td>'.n;
				$sbjt .= t.t.t.t.'<td class="sttus">'.$status.'</td>'.n;
				$sbjt .= t.t.t.t.'<td><a '.$lnkcls.'href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages'.a.'msg='.$row->id).'">'.stripslashes($row->subject).'</a></td>'.n;
				$sbjt .= t.t.t.t.'<td>'.JHTML::_('date', $row->created, '%d %b, %Y').'</td>'.n;
				//$sbjt .= t.t.t.t.'<td>'.JHTML::_('date', $row->expires, '%d %b, %Y').'</td>'.n;
				$sbjt .= t.t.t.'</tr>'.n;
			}
		} else {
			$cls = (($cls == 'even') ? 'odd' : 'even');
			$sbjt .= t.t.t.'<tr class="'.$cls.'">'.n;
			$sbjt .= t.t.t.t.'<td colspan="4">'.JText::_('No messages found').'</td>'.n;
			$sbjt .= t.t.t.'</tr>'.n;
		}
		$sbjt .= t.t.'</tbody>'.n;
		$sbjt .= t.'</table>'.n;
		$sbjt .= '</form>'.n;
		
		$html  = MembersHtml::hed(3,'<a name="messages"></a>'.JText::_('MESSAGES')).n;
		$html .= '<div class="withleft">'.n;
		$html .= MembersHtml::aside( $this->subMenu($option, $member, 'trash') );
		$html .= MembersHtml::subject($sbjt);
		$html .= '</div>'.n;
		
		return $html;
	}

	//-----------
	
	public function sendtoarchive($database, $option, $member) 
	{
		$mids = JRequest::getVar('mid',array(0));
		if (count($mids) > 0) {
			foreach ($mids as $mid) 
			{
				$recipient = new XMessageRecipient( $database );
				$recipient->mid = $mid;
				$recipient->uid = $member->get('uidNumber');
				$recipient->loadRecord();
				$recipient->state = 1;
				if (!$recipient->store()) {
					$this->setError( $recipient->getError() );
				}
			}
		}
		
		$html = '';
		if ($this->getError()) {
			$html .= MembersHtml::error( $this->getError() );
		}
		$html .= $this->inbox($database, $option, $member);
		
		return $html;
	}

	//-----------
	
	public function sendtoinbox($database, $option, $member) 
	{
		$mids = JRequest::getVar('mid',array(0));
		if (count($mids) > 0) {
			foreach ($mids as $mid) 
			{
				$recipient = new XMessageRecipient( $database );
				$recipient->mid = $mid;
				$recipient->uid = $member->get('uidNumber');
				$recipient->loadRecord();
				$recipient->state = 0;
				if (!$recipient->store()) {
					$this->setError( $recipient->getError() );
				}
			}
		}
		
		$html = '';
		if ($this->getError()) {
			$html .= MembersHtml::error( $this->getError() );
		}
		$html .= $this->inbox($database, $option, $member);
		
		return $html;
	}

	//-----------
	
	public function sendtotrash($database, $option, $member) 
	{
		$mids = JRequest::getVar('mid',array(0));
		if (count($mids) > 0) {
			foreach ($mids as $mid) 
			{
				$recipient = new XMessageRecipient( $database );
				$recipient->mid = $mid;
				$recipient->uid = $member->get('uidNumber');
				$recipient->loadRecord();
				
				$xseen = new XMessageSeen( $database );
				$xseen->mid = $mid;
				$xseen->uid = $member->get('uidNumber');
				$xseen->loadRecord();
				if ($xseen->whenseen == '' || $xseen->whenseen == '0000-00-00 00:00:00' || $xseen->whenseen == NULL) {
					$xseen->whenseen = date( 'Y-m-d H:i:s', time() );
					$xseen->store( true );
				}
				
				$recipient->state = 2;
				$recipient->expires = date( 'Y-m-d H:i:s', time()+(10*60*60*60) );
				if (!$recipient->store()) {
					$this->setError( $recipient->getError() );
				}
			}
		}
		
		$html = '';
		if ($this->getError()) {
			$html .= MembersHtml::error( $this->getError() );
		}
		$html .= $this->inbox($database, $option, $member);
		
		return $html;
	}
	
	//-----------
	
	public function emptytrash($database, $option, $member) 
	{
		$recipient = new XMessageRecipient( $database );
		$recipient->uid = $member->get('uidNumber');
		if (!$recipient->deleteTrash()) {
			$this->setError( $recipient->getError() );
		}
		
		$html = '';
		if ($this->getError()) {
			$html .= MembersHtml::error( $this->getError() );
		}
		$html .= $this->trash($database, $option, $member);
		
		return $html;
	}
	
	//-----------
	
	public function delete($database, $option, $member) 
	{
		$mids = JRequest::getVar('mid',array(0));
		if (count($mids) > 0) {
			foreach ($mids as $mid) 
			{
				$recipient = new XMessageRecipient( $database );
				$recipient->mid = $mid;
				$recipient->uid = $member->get('uidNumber');
				$recipient->loadRecord();
				if (!$recipient->delete()) {
					$this->setError( $recipient->getError() );
				}
			}
		}
		
		$html = '';
		if ($this->getError()) {
			$html .= MembersHtml::error( $this->getError() );
		}
		$html .= $this->trash($database, $option, $member);
		
		return $html;
	}
	
	//-----------
	
	public function markasread($database, $option, $member) 
	{
		$ids = JRequest::getVar('mid',array(0));
		if (count($ids) > 0) {
			foreach ($ids as $mid) 
			{
				$xseen = new XMessageSeen( $database );
				$xseen->mid = $mid;
				$xseen->uid = $member->get('uidNumber');
				$xseen->loadRecord();
				if ($xseen->whenseen == '' || $xseen->whenseen == '0000-00-00 00:00:00' || $xseen->whenseen == NULL) {
					$xseen->whenseen = date( 'Y-m-d H:i:s', time() );
					$xseen->store( true );
				}
			}
		}
		
		$html = '';
		if ($this->getError()) {
			$html .= MembersHtml::error( $this->getError() );
		}
		$html .= $this->inbox($database, $option, $member);
		
		return $html;
	}
	
	//-----------
	
	public function view($database, $option, $member, $mid) 
	{
		$xmessage = new XMessage( $database );
		$xmessage->load( $mid );
		$xmessage->message = stripslashes($xmessage->message);
		
		$xmr = new XMessageRecipient( $database );
		$xmr->loadRecord( $mid, $member->get('uidNumber') );

		$xmessage->message = str_replace("\n","\n ",$xmessage->message);
		$UrlPtrn  = "[^=\"\'](https?:|mailto:|ftp:|gopher:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";
		$xmessage->message = preg_replace_callback("/$UrlPtrn/", array('plgMembersMessages','autolink'), $xmessage->message);
		
		$xmessage->message = nl2br($xmessage->message);
		$xmessage->message = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$xmessage->message);
		
		$xseen = new XMessageSeen( $database );
		$xseen->mid = $mid;
		$xseen->uid = $member->get('uidNumber');
		$xseen->loadRecord();
		if ($xseen->whenseen == '' || $xseen->whenseen == '0000-00-00 00:00:00' || $xseen->whenseen == NULL) {
			$xseen->whenseen = date( 'Y-m-d H:i:s', time() );
			$xseen->store( true );
		}
		
		$sbjt  = '<form action="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages').'" method="post" id="hubForm" class="full">'.n;
		if ($xmr->state != 2) {
			$sbjt .= t.'<fieldset id="filters">'.n;
			$sbjt .= '<a class="trash" href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages'.a.'mid[]='.$xmessage->id.a.'action=sendtotrash').'" title="'.JText::_('MESSAGES_TRASH').'">'.JText::_('MESSAGES_TRASH').'</a>';
			$sbjt .= t.'</fieldset>'.n;
		}
		$sbjt .= t.'<fieldset id="actions">'.n;
		$sbjt .= t.t.'<select class="option" name="action">'.n;
		$sbjt .= t.t.t.'<option value="">'.JText::_('MSG_WITH_SELECTED').'</option>'.n;
		switch ($xmr->state) 
		{
			case 2:
				$sbjt .= t.t.t.'<option value="sendtoinbox">'.JText::_('MSG_SEND_TO_INBOX').'</option>'.n;
				$sbjt .= t.t.t.'<option value="sendtoarchive">'.JText::_('MSG_SEND_TO_ARCHIVE').'</option>'.n;
			break;
			case 1:
				$sbjt .= t.t.t.'<option value="sendtoinbox">'.JText::_('MSG_SEND_TO_INBOX').'</option>'.n;
				$sbjt .= t.t.t.'<option value="sendtotrash">'.JText::_('MSG_SEND_TO_TRASH').'</option>'.n;
			break;
			case 0:
			default:
				$sbjt .= t.t.t.'<option value="sendtoarchive">'.JText::_('MSG_SEND_TO_ARCHIVE').'</option>'.n;
				$sbjt .= t.t.t.'<option value="sendtotrash">'.JText::_('MSG_SEND_TO_TRASH').'</option>'.n;
			break;
		}
		$sbjt .= t.t.'</select> '.n;
		$sbjt .= t.t.'<input class="option" type="submit" value="'.JText::_('MSG_APPLY').'" />'.n;
		$sbjt .= t.t.'<input type="hidden" name="mid[]" id="msg'.$xmessage->id.'" value="'.$xmessage->id.'" />'.n;
		$sbjt .= t.'</fieldset>'.n;
		$sbjt .= t.'<table class="profile" summary="'.JText::_('TBL_SUMMARY_OVERVIEW').'">'.n;
		$sbjt .= t.t.'<tbody>'.n;
		$sbjt .= t.t.t.'<tr>'.n;
		$sbjt .= t.t.t.t.'<th>'.JText::_('Received').'</th>'.n;
		$sbjt .= t.t.t.t.'<td>'.JHTML::_('date', $xmessage->created, '%d %b, %Y').'</td>'.n;
		$sbjt .= t.t.t.'</tr>'.n;
		$sbjt .= t.t.t.'<tr>'.n;
		$sbjt .= t.t.t.t.'<th>'.JText::_('Subject').'</th>'.n;
		$sbjt .= t.t.t.t.'<td>'.stripslashes($xmessage->subject).'</td>'.n;
		$sbjt .= t.t.t.'</tr>'.n;
		$sbjt .= t.t.t.'<tr>'.n;
		$sbjt .= t.t.t.t.'<th>'.JText::_('Message').'</th>'.n;
		$sbjt .= t.t.t.t.'<td>'.$xmessage->message.'</td>'.n;
		$sbjt .= t.t.t.'</tr>'.n;
		$sbjt .= t.t.'</tbody>'.n;
		$sbjt .= t.'</table>'.n;
		$sbjt .= '</form>'.n;

		switch ($xmr->state) 
		{
			case 2: $task = 'trash'; break;
			case 1: $task = 'archive'; break;
			case 0:
			default: $task = 'inbox'; break;
		}
		
		$html  = MembersHtml::hed(3,'<a name="messages"></a>'.JText::_('MESSAGES')).n;
		$html .= '<div class="withleft">'.n;
		$html .= MembersHtml::aside( $this->subMenu($option, $member, $task) );
		$html .= MembersHtml::subject($sbjt);
		$html .= '</div>'.n;
		
		return $html;
	}
	
	//-----------

	public function autolink($matches) 
	{
		$href = $matches[0];

		if (substr($href, 0, 1) == '!') {
			return substr($href, 1);
		}
		
		$href = str_replace('"','',$href);
		$href = str_replace("'",'',$href);
		$href = str_replace('&#8221','',$href);
		
		$h = array('h','m','f','g','n');
		if (!in_array(substr($href,0,1), $h)) {
			$href = substr($href, 1);
		}
		$name = trim($href);
		if (substr($name, 0, 7) == 'mailto:') {
			$name = substr($name, 7, strlen($name));
			$name = Eventshtml::obfuscate($name);
			//$href = Eventshtml::obfuscate($href);
			$href = 'mailto:'.$name;
		}
		$l = sprintf(
			' <a class="ext-link" href="%s" rel="external">%s</a>',$href,$name
		);
		return $l;
	}
	
	//-----------

	public function selectMethod($notimethods, $name, $values=array(), $ids=array())
	{
		$out = '';
		$i = 0;
		foreach ($notimethods as $notimethod) 
		{
			$out .= t.t.t.t.t.'<td>'.n;
			$out .= t.t.t.t.t.t.'<input type="checkbox" name="settings['.$name.'][]" class="opt-'.$notimethod.'" value="'.$notimethod.'"';
			$out .= (in_array($notimethod, $values))
						  ? ' checked="checked"'
						  : '';
			$out .= ' />'.n;
			$out .= t.t.t.t.t.t.'<input type="hidden" name="ids['.$name.']['.$notimethod.']" value="';
			if (isset($ids[$notimethod])) {
				$out .= $ids[$notimethod];
			} else {
				$out .= '0';
			}
			$out .= '" />'.n;
			$out .= t.t.t.t.t.'</td>'.n;
			$i++;
		}
		return $out;
	}
	
	//-----------
	
	public function settings($database, $option, $member) 
	{
		// Push some scripts to the template
		$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.DS.'plugins'.DS.'members'.DS.'messages.js')) {
			$document->addScript('plugins'.DS.'members'.DS.'messages.js');
		}
		
		$xmc = new XMessageComponent( $database );
		$components = $xmc->getRecords();
		
		$html  = MembersHtml::hed(3,'<a name="messages"></a>'.JText::_('MESSAGES')).n;
		$html .= '<div class="withleft">'.n;
		$html .= MembersHtml::aside( $this->subMenu($option, $member, 'settings') );
		$html .= t.'<div class="subject">'.n;
		if (!$components) {
			$html .= MembersHtml::error( JText::_('No components configured for XMessages.') );
			$html .= t.'</div>'.n;
			$html .= '</div>'.n;
			return $html;
		}
		
		$settings = array();
		foreach ($components as $component) 
		{
			$settings[$component->action] = array();
		}
		
		// Load plugins
		JPluginHelper::importPlugin( 'xmessage' );
		$dispatcher =& JDispatcher::getInstance();
		
		// Fetch message methods
		$notimethods = $dispatcher->trigger( 'onMessageMethods', array() );
		
		// A var for storing the default notification method
		$default_method = null;
		
		// Instantiate our notify object
		$notify = new XMessageNotify( $database );
		
		// Get the user's selected methods
		$methods = $notify->getRecords( $member->get('uidNumber') );
		if ($methods) {
			foreach ($methods as $method) 
			{
				$settings[$method->type]['methods'][] = $method->method;
				$settings[$method->type]['ids'][$method->method] = $method->id;
			}
		} else {
			$default_method = $this->_params->get('default_method');
		}

		// Fill in any settings that weren't set.
		foreach ($settings as $key=>$val)
		{
			if (count($val) <= 0) {
				// If the user has never changed their settings, set up the defaults
				if ($default_method !== null) {
					$settings[$key]['methods'][] = 'internal';
					$settings[$key]['methods'][] = $default_method;
					$settings[$key]['ids']['internal'] = 0;
					$settings[$key]['ids'][$default_method] = 0;
				} else {
					$settings[$key]['methods'] = array();
					$settings[$key]['ids'] = array();
				}
			}
		}
		
		$html .= '<form action="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=messages').'" method="post" id="hubForm" class="full">'.n;
		$html .= t.'<input type="hidden" name="action" value="savesettings" />'.n;
		
		/*$html .= t.'<div class="aside">'.n;
		$html .= t.t.'<p>'.JText::_('MSG_EXPLANATION').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<div class="subject">'.n;*/
		
		$html .= t.t.'<table class="settings" summary="User notification methods">'.n;
		$html .= t.t.t.'<thead>'.n;
		$html .= t.t.t.t.'<tr>'.n;
		$html .= t.t.t.t.t.'<th scope="col">Message sent when:</th>'.n;
		foreach ($notimethods as $notimethod) 
		{
			$html .= t.t.t.t.t.'<th scope="col"><input type="checkbox" name="override['.$notimethod.']" value="all" onclick="HUB.MembersMsg.checkAll(this, \'opt-'.$notimethod.'\');" /> '.JText::_('MSG_'.strtoupper($notimethod)).'</th>'.n;
		}
		$html .= t.t.t.t.'</tr>'.n;
		$html .= t.t.t.'</thead>'.n;
		$html .= t.t.t.'<tfoot>'.n;
		$html .= t.t.t.t.'<tr>'.n;
		$html .= t.t.t.t.t.'<td colspan="'.(count($notimethods) + 1).'">'.n;
		$html .= t.t.t.t.t.t.'<input type="submit" value="'.JText::_('MSG_SAVE_SETTINGS').'" />'.n;
		$html .= t.t.t.t.t.'</td>'.n;
		$html .= t.t.t.t.'</tr>'.n;
		$html .= t.t.t.'</tfoot>'.n;
		$html .= t.t.t.'<tbody>'.n;
		
		$cls = 'even';
		
		$sheader = '';
		foreach ($components as $component) 
		{
			if ($component->name != $sheader) {
				$sheader = $component->name;
				
				$html .= t.t.t.t.'<tr class="section-header">'.n;
				$html .= t.t.t.t.t.'<th scope="col">'.$component->name.'</th>'.n;
				foreach ($notimethods as $notimethod) 
				{
					$html .= t.t.t.t.t.'<th scope="col"><span class="'.$notimethod.' iconed">'.JText::_('MSG_'.strtoupper($notimethod)).'</span></th>'.n;
				}
				$html .= t.t.t.t.'</tr>'.n;
			}
			$cls = (($cls == 'even') ? 'odd' : 'even');
			$html .= t.t.t.t.'<tr class="'.$cls.'">'.n;
			$html .= t.t.t.t.t.'<th scope="col">'.$component->title.'</th>'.n;
			$html .= $this->selectMethod($notimethods, $component->action, $settings[$component->action]['methods'], $settings[$component->action]['ids']).n;
			$html .= t.t.t.t.'</tr>'.n;
		}

		$html .= t.t.t.'</tbody>'.n;
		$html .= t.t.'</table>'.n;
		$html .= '</form>'.n;
		$html .= t.'</div>'.n;
		$html .= '</div>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function savesettings($database, $option, $member) 
	{
		// Incoming
		//$override = JRequest::getInt('override',0);
		$settings = JRequest::getVar('settings',array());
		$ids = JRequest::getVar('ids',array());
		
		// Ensure we have data to work with
		if ($settings && count($settings) > 0) {
			// Loop through each setting
			foreach ($settings as $key=>$value) 
			{
				foreach ($value as $v) 
				{
					if ($v) {
						// Instantiate a Notify object and set its values
						$notify = new XMessageNotify( $database );
						$notify->uid = $member->get('uidNumber');
						$notify->method = $v;
						$notify->type = $key;
						$notify->priority = 1;
						// Do we have an ID for this setting?
						// Determines if the store() method is going to INSERT or UPDATE
						if ($ids[$key][$v] > 0) {
							$notify->id = $ids[$key][$v];
							$ids[$key][$v] = -1;
							//echo 'updated: '.$key.':'.$v.'<br />';
						//} else {
							//echo 'created: '.$key.':'.$v.'<br />';
						}
						// Save
						if (!$notify->store()) {
							$this->setError( JText::_('Unable to create XMessageNotify entry for:').' '.$notify->method );
						}
					}
				}
			}

			$notify = new XMessageNotify( $database );
			foreach ($ids as $key=>$value) 
			{
				foreach ($value as $k=>$v) 
				{
					if ($v > 0) {
						$notify->delete( $v );
						//echo 'deleted: '.$v.'<br />';
					}
				}
			}
			
			// If they previously had everything turned off, we need to remove that entry saying so
			$records = $notify->getRecords( $member->get('uidNumber'), 'all' );
			if ($records) {
				foreach ($records as $record) 
				{
					$notify->delete( $record->id );
				}
			}
		} else {
			// This creates a single entry to let the system know that the user has explicitly chosen "none" for all options
			// It ensures we can know the difference between someone who has never changed their settings (thus, no database entries) 
			// and someone who purposely wants everything turned off.
			$notify = new XMessageNotify( $database );
			$notify->uid = $member->get('uidNumber');
			
			$records = $notify->getRecords( $member->get('uidNumber'), 'all' );
			if (!$records) {
				$notify->clearAll();
				$notify->method = 'none';
				$notify->type = 'all';
				$notify->priority = 1;
				if (!$notify->store()) {
					$this->setError( JText::_('Unable to create XMessageNotify entry for:').' '.$notify->method );
				}
			}
		}
		
		$html = '';
		if ($this->getError()) {
			$html .= MembersHtml::error( $this->getError() );
		}
		$html .= $this->settings($database, $option, $member);
		
		// Push through to the settings view
		return $html;
	}
}
