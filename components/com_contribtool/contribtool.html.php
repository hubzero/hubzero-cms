<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

if (!defined('n')) {
	define('t',"\t");
	define('n',"\n");
	define('r',"\r");
	define('br','<br />');
	define('sp','&#160;');
	define('a','&amp;');
}



class ContribtoolHtml 
{

	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------

	public function passed( $msg, $tag='p' )
	{
		return '<'.$tag.' class="passed">'.$msg.'</'.$tag.'>'.n;
	}


	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	//-----------
	
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}

	//-----------

	public function div($txt, $cls='', $id='')
	{
		$html  = '<div';
		$html .= ($cls) ? ' class="'.$cls.'"' : '';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= '>'.n;
		$html .= $txt.n;
		$html .= '</div><!-- / ';
		if ($id) {
			$html .= '#'.$id;
		}
		if ($cls) {
			$html .= '.'.$cls;
		}
		$html .= ' -->'.n;
		return $html;
	}
	//-----------

	public function mkt($stime)
	{
		if ($stime && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $stime, $regs )) {
			$stime = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		return $stime;
	}
	
	//-----------
	
	public function timeAgoo($timestamp)
	{
		// Store the current time
		$current_time = time();
		
		// Determine the difference, between the time now and the timestamp
		$difference = $current_time - $timestamp;
		
		// Set the periods of time
		$periods = array(JText::_('SECOND'), JText::_('MINUTE'), JText::_('HOUR'), JText::_('DAY'), JText::_('WEEK'), JText::_('MONTH'), JText::_('YEAR'), JText::_('DECADE'));
		
		// Set the number of seconds per period
		$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);
		
		// Determine which period we should use, based on the number of seconds lapsed.
		// If the difference divided by the seconds is more than 1, we use that. Eg 1 year / 1 decade = 0.1, so we move on
		// Go from decades backwards to seconds
		for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $difference / $lengths[$val]) <= 1); $val--);
		
		// Ensure the script has found a match
		if ($val < 0) $val = 0;
		
		// Determine the minor value, to recurse through
		$new_time = $current_time - ($difference % $lengths[$val]);
		
		// Set the current value to be floored
		$number = floor($number);

		// If required create a plural
		if ($number != 1) 
		$periods = array(JText::_('SECONDS'), JText::_('MINUTES'), JText::_('HOURS'), JText::_('DAYS'), JText::_('WEEKS'), JText::_('MONTHS'), JText::_('YEARS'), JText::_('DECADES'));
		
		
		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);
		
		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0)){
			$text .= ContribtoolHtml::timeAgoo($new_time);
		}
		
		return $text;
	}
	
	//-----------
	
	public function timeAgo($timestamp) 
	{
		$timestamp = ContribtoolHtml::mkt($timestamp);
		$text = ContribtoolHtml::timeAgoo($timestamp);
		
		$parts = explode(' ',$text);

		$text  = $parts[0].' '.$parts[1];

		return $text;
	}
	
	//-----------
	public function formSelect($name, $idname, $array, $value, $class='', $jscall='')
	{
		$out  = '<select name="'.$name.'" id="'.$idname.'"';
		$out .= ($class) ? ' class="'.$class.'"' : ''."";
		$out .= ($jscall) ? ' onChange="'.$jscall.'">'."\n" : '>'."\n";
		foreach ($array as $avalue => $alabel) 
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="'.$avalue.'"'.$selected.'>'.$alabel.'</option>'."\n";
		}
		$out .= '</select>'."\n";
		return $out;
	}
	
	//-----------
	public function primaryButton($class, $href, $msg, $xtra='', $title='', $action='')
	{
		$title = str_replace('"','&quot;',$title);
		$html  = t.'<span id="test-document"><a class="'.$class.'" style="padding:0.1em 1em 0 1em;"  href="'.$href.'" title="'.$title.'" '.$action.'>'.$msg.'</a>';
		$html .= $xtra;
		$html .= '</span>'.n;
		return $html;
	}
			
	//-----------
		
	public function getNumofTools($status, $toolnum='') 
	{	
		// get hub parameters
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		
		$toolnum = ($status['state']!=9) ? JText::_('THIS_TOOL').'  ': '';
		if(!$status['published'] && ContribtoolHtml::toolActive($status['state']) ) {
			$toolnum .= JText::_('IS_ONE_OF').' '.$status['ntoolsdev'].' '.strtolower(JText::_('TOOLS')). ' '
			.strtolower(JText::_('UNDER_DEVELOPMENT')).' '.JText::_('ON').' '.$xhub->getCfg('hubShortName');
		}
		else if($status['published'] && ContribtoolHtml::toolActive($status['state'])) {
			$toolnum .= JText::_('IS_ONE_OF').' '.$status['ntools_published'].' '.strtolower(JText::_('TOOLS')). ' '
			.strtolower(JText::_('PUBLISHED')).' '.JText::_('ON').' '.$xhub->getCfg('hubShortName');
		}
		else if($status['state']==8) {
			$toolnum .= JText::_('WAS_ONCE_PUBLISHED').' '.JText::_('ON').' '.$xhub->getCfg('hubShortName').' '.JText::_('NOW_RETIRED');
		}
			
		return $toolnum;
	}
	
	//-----------
		
	public function toolActive($stateNum) {
		if($stateNum==8 or $stateNum==9) {
			return false;
		}
		else {
			return true;
		}
	
	}
	//-----------
		
	public function toolWIP($stateNum) {
		if($stateNum==2 or $stateNum==9 or $stateNum==1) {
			return false;
		}
		else {
			return true;
		}
	
	}
	
	//-----------
		
	public function toolEstablished($stateNum) {
		if($stateNum==1 or $stateNum==9) {
			return false;
		}
		else {
			return true;
		}
	
	}
	//-----------
		
	public function getStatusClass($statusNum, &$statusClass) {
		
		switch($statusNum) 
		{
			case 7: 	$statusClass = '_closed';		break;
			case 8: 	$statusClass = '_abandoned';	break;
			case 9: 	$statusClass = '_abandoned';	break;
			default: 	$statusClass = '';							
		}
		
		return $statusClass;
	}
	//-----------
		
	public function getStatusName($statusNum, &$statusName) {
		
		switch($statusNum) 
		{
			case 1: 	$statusName = JText::_('REGISTERED');     	break;
			case 2: 	$statusName = JText::_('CREATED'); 			break;
			case 3: 	$statusName = JText::_('UPLOADED'); 		break;
			case 4: 	$statusName = JText::_('INSTALLED'); 		break;
			case 5: 	$statusName = JText::_('UPDATED');			break;
			case 6: 	$statusName = JText::_('APPROVED'); 		break;
			case 7: 	$statusName = JText::_('PUBLISHED');		break;
			case 8: 	$statusName = JText::_('RETIRED');			break;
			case 9: 	$statusName = JText::_('ABANDONED');		break;							
		}
		
		return $statusName;
	}
	//-----------
	
	public function getStatusNum($statusName, $statusNum=1)
	{
		$statusName= strtolower($statusName);
		switch($statusName)
		{
			case 'registered': 	$statusNum = 1;   	break;
			case 'created': 	$statusNum = 2;   	break;
			case 'uploaded': 	$statusNum = 3;   	break;
			case 'installed': 	$statusNum = 4;   	break;
			case 'updated': 	$statusNum = 5;   	break;
			case 'approved': 	$statusNum = 6;   	break;
			case 'published': 	$statusNum = 7;  	break;
			case 'retired': 	$statusNum = 8;   	break;
			case 'abandoned': 	$statusNum = 9;   	break;

		}
		return $statusNum;
	}
	//-----------
	
	public function getPriority ($int, $priority='')
	{
		switch($int)
		{
			case 1: 	$priority = 'critical';     break;
			case 2: 	$priority = 'high'; 		break;
			case 3: 	$priority = 'normal'; 		break;
			case 4: 	$priority = 'low'; 			break;
			case 5: 	$priority = 'lowest'; 		break;
			default: 	$priority = 'normal';		break;
		
		}
		return $priority;
	}

	//-----------
		
	public function getDevTeam($members, $obj = 1, $team='') {
		
		if(count($members)>0) {
			foreach($members as $member) {
				$uid = ($obj) ? $member->uidNumber  : $member ;			
				$juser =& JUser::getInstance ( $uid );
				if (is_object($juser)) {
							$login = $juser->get('username');
				} else {
					$login = JText::_('UNKNOWN'); 
				}	
				$team .= ($member != end($members)) ? $login. ', ' : $login;
			}
		}
		else {
			$team .= JText::_('N/A'); 
		}
		
		return $team;
	}
	
	//-----------
		
	public function getGroups($groups, $obj = 1, $list='') {
		if(count($groups)>0) {
			foreach($groups as $group) {
				$cn = ($obj) ? $group->cn : $group;
				$list .= ($group != end($groups)) ? $cn. ', ' : $cn;
			}
		}
			
		return $list;
	}
	
	//-----------
		
	public function getToolAccess($access, $groups, $toolaccess='') 
	{		
		switch($access) 
		{
			case '@GROUP': 	
							if(count($groups)>0) {
							$toolaccess = JText::_('RESTRICTED').' '.JText::_('TO').' '.JText::_('GROUP_OR_GROUPS').' ';
								foreach($groups as $group) {
									$toolaccess .= ($group != end($groups)) ? $group->cn. ', ' : $group->cn;
								}
							}
							else { $toolaccess = JText::_('RESTRICTED').' '.JText::_('TO').' '.JText::_('UNSPECIFIED').' '.JText::_('GROUP_OR_GROUPS'); }
							
							break;
							
			case '@US': 	$toolaccess = JText::_('TOOLACCESS_US'); 			break;			
			case '@PU': 	$toolaccess = JText::_('TOOLACCESS_PU'); 			break;
			case '@D1': 	$toolaccess = JText::_('TOOLACCESS_D1'); 			break;				
			default: 		$toolaccess = JText::_('ACCESS_OPEN'); 				break;			
		}
		
		return $toolaccess;
	}
	//-----------
	
	public function getCodeAccess($access, $codeaccess = '') 
	{	
		switch($access) 
		{
			case '@OPEN': 	$codeaccess = JText::_('OPEN_SOURCE');				break;
			case '@DEV': 	$codeaccess = JText::_('CLOSED_SOURCE');			break;							
			default: 		$codeaccess = JText::_('UNSPECIFIED');				break;				
		}
		
		return $codeaccess;
	}
	
	//-----------
	
	public function getWikiAccess($access, $wikiaccess = '') 
	{		
		switch($access) 
		{
			case '@OPEN': 	$wikiaccess = JText::_('ACCESS_OPEN'); 				break;
			case '@DEV': 	$wikiaccess = JText::_('ACCESS_RESTRICTED'); 		break;							
			default: 		$wikiaccess = JText::_('UNSPECIFIED');				break;				
		}
		
		return $wikiaccess;
	}
	//------------
	
	public function writeWhatNext ($status, $config, $option, $title, $par='', $step2='', $step4='', $step5addon='') 
	{			
		// get configs
		$xhub 			=& XFactory::getHub();
		$hubShortName 	= $xhub->getCfg('hubShortName');
		$hubShortURL 	= $xhub->getCfg('hubShortURL');
		$hubLongURL 	= $xhub->getCfg('hubLongURL');
		
		// get tool access text
		$toolaccess = ContribtoolHtml::getToolAccess($status['exec'], $status['membergroups']);
		
		// get configurations/ defaults
		$developer_site = isset($config->parameters['developer_site']) ? $config->parameters['developer_site'] : 'nanoFORGE';
		$developer_url 	= isset($config->parameters['developer_url']) ? $config->parameters['developer_url'] : 'https://developer.nanohub.org';
		$project_path 	= isset($config->parameters['project_path']) ? $config->parameters['project_path'] : '/projects/app-';
		$dev_suffix 	= isset($config->parameters['dev_suffix']) ? $config->parameters['dev_suffix'] : '_dev';
		
		// set common paths
		$statuspath =  JRoute::_('index.php?option='.$option.a.'task=status'.a.'toolid='.$status['toolid']);
		$testpath = 'index.php?option=com_tools'.a.'task=invoke&app='.$status['toolname'].a.'version=dev';
		
		$step3 = ($status['resource_modified'] == '1') 
					? '<li class="complete"> '.JText::_('TODO_MAKE_RES_PAGE').'. 
					<a href="/resources/'.$status['resourceid'].'/?rev=dev">'.JText::_('PREVIEW').'</a> | 
					<a href="index.php?option=com_contribtool&amp;task=start&amp;step=1&amp;rid='.$status['resourceid'].'">'.JText::_('TODO_EDIT_PAGE').'...</a></li>' 
					: '<li class="todo"> '.JText::_('TODO_MAKE_RES_PAGE').'. 
					<a href="index.php?option=com_contribtool&amp;task=start&amp;step=1&amp;rid='.$status['resourceid'].'">'.JText::_('TODO_CREATE_PAGE').'...</a></li>';
			
		switch ($status['state']) {
       
	    //  registered
        case 1:
			$par = '		<p>'.JText::_('TEAM_WILL_CREATE').' <a href="'.$developer_url.'">'.$developer_site.'</a>, '.JText::_('WHATSNEXT_REGISTERED_INSTRUCTIONS').'. '.JText::_('WHATSNEXT_IT_HAS_BEEN').' '.ContribtoolHtml::timeAgo($status['changed']).' '.JText::_('WHATSNEXT_SINCE_YOUR_REQUEST').'. '.JText::_('WHATSNEXT_YOU_WILL_RECEIVE_RESPONSE').' 24 '.JText::_('HOURS').'</p>';
			$step2 = '		<li class="incomplete"> '.JText::_('WHATSNEXT_UPLOAD_CODE').' '.$developer_site.'</li>';
			$step4 = '		<li class="incomplete"> '.JText::_('WHATSNEXT_TEST_AND_APPROVE').'</li>';
		break;
			
		//  created
        case 2:
			$par  = '		<p>'.ucfirst(JText::_('THE')).' '.$xhub->getCfg('hubShortName').'  '.JText::_('WHATSNEXT_AREA_CREATED').' <a href="'.$developer_url.'">'.$developer_site.'</a> '.JText::_('SITE').': <br />';
			$par .= '		<a href="'.$developer_url.$project_path.$status['toolname'].'">'.$developer_url.$project_path.$status['toolname'].'</a></p>'.n;
			$par .= '		<p>'.JText::_('WHATSNEXT_FOLLOW_STEPS').':</p>'.n;
			$par .= '		<ul>'.n;
			$par .= '			<li><a href="http://rappture.org/wiki/FAQ_UpDownloadSrc">'.JText::_('LEARN_MORE').'</a> '.JText::_('WHATSNEXT_ABOUT_UPLOADING').'</li>'.n;
			$par .= '			<li>'.JText::_('LEARN_MORE').' '.strtolower(JText::_('ABOUT')).' '.JText::_('THE').' <a href="http://rappture.org">Rappture toolkit</a>.</li>'.n;
			$par .= '			<li>'.JText::_('WHATSNEXT_WHEN_READY').', <a href="'.$developer_url.$project_path.$status['toolname'].'/wiki/GettingStarted">'.JText::_('WHATSNEXT_FOLLOW_THESE_INSTRUCTIONS').'</a> '.JText::_('WHATSNEXT_TO_ACCESS_CODE').'.</li>'.n;
			$par .= '		</ul>'.n;
			$par .= '		<h2>'.JText::_('WHATSNEXT_WE_ARE_WAITING').'</h2>'.n;
			$par .= '		<p>'.JText::_('WHATSNEXT_CREATED_LET_US_KNOW').':</p>'.n;
			$par .= '		<ul>'.n;
			$par .= '			<li class="todo"><span id="Uploaded"><a href="javascript:void(0);" class="flip" >'.JText::_('WHATSNEXT_CREATED_CODE_UPLOADED').'</a></span></li>'.n;
			$par .= '		</ul>'.n;
			$step2 = '		<li class="incomplete"> '.JText::_('WHATSNEXT_UPLOAD').' <span id="Uploaded_"><a href="javascript:void(0);'.$statuspath.'" class="flip" >'.JText::_('WHATSNEXT_DONE').'</a></span></li>';
			$step4 = '		<li class="incomplete"> '.JText::_('WHATSNEXT_TEST_AND_APPROVE').'</li>';
		break; 
			
		//  uploaded
        case 3:
			$par = '<p>'.ucfirst(JText::_('THE')).' '.$xhub->getCfg('hubShortName').' '.JText::_('WHATSNEXT_UPLOADED_TEAM_NEEDS').' '.$xhub->getCfg('hubShortName').' '.JText::_('WHATSNEXT_UPLOADED_SO_YOU_CAN_TEST').'. '.JText::_('WHATSNEXT_IT_HAS_BEEN').' '.ContribtoolHtml::timeAgo($status['changed']).' '.JText::_('WHATSNEXT_SINCE_LAST_STATUS_CHANGE').'. '.JText::_('WHATSNEXT_YOU_WILL_RECEIVE_RESPONSE').' 3 '.JText::_('DAYS').'.</p>';
			$step2 = '		<li class="complete"> '.JText::_('WHATSNEXT_UPLOAD_CODE').' '.$developer_site.'</li>';
			$step4 = '		<li class="incomplete"> '.JText::_('WHATSNEXT_TEST_AND_APPROVE').'</li>';			
		break;
			
		//  installed
        case 4:
			$par = '		<p>'.JText::_('WHATSNEXT_INSTALLED_CODE_READY').' '.$xhub->getCfg('hubShortURL').'. '.JText::_('WHATSNEXT_INSTALLED_PLS_TEST').':</p>'.n;
			$par .= '		<ul ><li class="todo"><span id="primary-document" >'.JText::_('WHATSNEXT_INSTALLED_TEST').': <a class="launchtool" style="padding:0.4em 0.2em 0.1em 1.5em;margin-top:1em;"  href="'.$testpath.'" title="">'.JText::_('LAUNCH_TOOL').'</a></span></li>'.n;
			$par .= '		<li class="todo">'.n;
			$par .= ($status['resource_modified']) ? '<a href="index.php?option=com_contribtool&amp;task=preview&amp;rid='.$status['resourceid'].'">'.JText::_('TODO_REVIEW_RES_PAGE').'</a>' : '<a href="index.php?option=com_contribtool&amp;task=start&amp;step=1&amp;rid='.$status['resourceid'].'">'.JText::_('TODO_CREATE_PAGE').'</a>';
			$par .= '		</li></ul>'.n;
			$par .= ($status['resource_modified']) ? '': '<p class="warning">'.JText::_('PLEASE').' <a href="index.php?option=com_contribtool&amp;task=start&amp;step=1&amp;rid='.$status['resourceid'].'">'.strtolower(JText::_('CREATE')).'</a> '.JText::_('WHATSNEXT_PAGE_DESC').'.</p>'.n;
			$par .= '		<h2>'.JText::_('WHATSNEXT_WE_ARE_WAITING').'</h2>'.n;
			$par .= '		<p>'.JText::_('WHATSNEXT_INSTALLED_CLICK_AFTER_TESTING').':</p>'.n;
			$par .= '		<ul>'.n;
			$par .= ($status['resource_modified']) ? '<li class="todo"><span id="Approved"><a href="javascript:void(0);" class="flip" >'.JText::_('WHATSNEXT_INSTALLED_TOOL_WORKS').'</a></span></li>':'<li class="todo_disabled">'.JText::_('WHATSNEXT_INSTALLED_TOOL_WORKS').'</li>' ;
			$par .= '		</ul>'.n;
			$par .= '		<p>'.JText::_('WHATSNEXT_INSTALLED_NEED_CHANGES').':</p>'.n;
			$par .= '		<ul>'.n;
			$par .= '		<li class="todo"><span id="Updated"><a href="javascript:void(0);" class="flip" >'.JText::_('WHATSNEXT_CODE_FIXED_PLS_INSTALL').'.</a></span></li>'.n;
			$par .= '		</ul>'.n;
			$step2 = '		<li class="complete"> '.JText::_('WHATSNEXT_UPLOAD_CODE').' '.$developer_site.'</li>' ;
			$step4 = '		<li class="todo"> '.JText::_('WHATSNEXT_TEST_AND_APPROVE').'. ';
			$step4.= ($status['resource_modified'] == '1') ?'<span id="Approved_"><a href="javascript:void(0);" class="flip" >'.JText::_('WHATSNEXT_I_APPROVE').'</a></span> ' : '<span class="disabled">'.JText::_('WHATSNEXT_I_APPROVE').'</span>';
			$step4.= ' | <span id="Updated_"><a href="javascript:void(0);" class="flip" >'.JText::_('WHATSNEXT_CHANGES_MADE').'</a></span></li>'.n;
		break;
			 
		//  updated
        case 5:
			$par = '<p>'.ucfirst(JText::_('THE')).' '.$xhub->getCfg('hubShortName').' '.JText::_('WHATSNEXT_UPLOADED_TEAM_NEEDS').' '.$xhub->getCfg('hubShortName').' '.JText::_('WHATSNEXT_UPLOADED_SO_YOU_CAN_TEST').'. '.JText::_('WHATSNEXT_IT_HAS_BEEN').' '.ContribtoolHtml::timeAgo($status['changed']).' '.JText::_('WHATSNEXT_SINCE_LAST_STATUS_CHANGE').'. '.JText::_('WHATSNEXT_YOU_WILL_RECEIVE_RESPONSE').' 3 '.JText::_('DAYS').'.</p>';
			$step2 = '		<li class="complete"> '.JText::_('WHATSNEXT_UPLOAD_CODE').' '.$developer_site.'</li>' ;
			$step4 = '		<li class="incomplete"> '.JText::_('WHATSNEXT_TEST_AND_APPROVE').'</li>';		
		break;
			 
		//  approved
        case 6:
			$par = '		<p>'.ucfirst(JText::_('THE')).' '.$xhub->getCfg('hubShortName').' '.JText::_('WHATSNEXT_APPROVED_TEAM_WILL_FINALIZE').' '.JText::_('WHATSNEXT_IT_HAS_BEEN').' '.ContribtoolHtml::timeAgo($status['changed']).' '.JText::_('WHATSNEXT_APPROVED_SINCE').'  '.JText::_('WHATSNEXT_APPROVED_WHAT_WILL_HAPPEN').' '.$toolaccess.'.</p>';
			$par .= '		<p> '.JText::_('WHATSNEXT_APPROVED_PLS_CLICK').' '.$xhub->getCfg('hubShortName').': <br />'.n;
			$par .= '		<a href="'.JRoute::_('index.php?option=com_resources&alias='.$status['toolname']).'" ><'.$xhub->getCfg('hubLongURL').'/tools/'.$status['toolname'].'</a></p>';
			$step2 = '		<li class="complete"> '.JText::_('WHATSNEXT_UPLOAD_CODE').' '.$developer_site.'</li>' ;
			$step4 = '		<li class="complete"> '.JText::_('WHATSNEXT_TEST_AND_APPROVE').'</li>';
			$step5addon = '<br /><span id="Updated"><a href="javascript:void(0);" class="flip" >'.JText::_('WHATSNEXT_WAIT').'</a></span>';	
		break;
			 
		//  published
        case 7:
			$par = '		<p>'.JText::_('WHATSNEXT_PUBLISHED_MSG').': <br />'.n;
			$par .= '		<a href="'.JRoute::_('index.php?option=com_resources&alias='.$status['toolname']).'" >'.$xhub->getCfg('hubLongURL').'/tools/'.$status['toolname'].'</a></p>'.n;
			$par .= '		<h3>'.JText::_('WHATSNEXT_YOUR_OPTIONS').':</h3>'.n;
			$par .= '		<ul class="youroptions">'.n;
			$par .= '		<li> '.JText::_('WHATSNEXT_CHANGES_MADE').' <span id="Updated"><a href="javascript:void(0);" class="flip" >'.JText::_('WHATSNEXT_PUBLISHED_PLS_INSTALL').'</a></span></li>'.n;
			$par .= '		</ul>'.n;
		break;
			
		//  retired
        case 8:
			$par = '		<p>'.JText::_('WHATSNEXT_RETIRED_FROM').' '.$xhub->getCfg('hubShortURL').'. '.JText::_('CONTACT').' '.$xhub->getCfg('hubShortName').' '.JText::_('CONTACT_SUPPORT_TO_REPUBLISH').' .</p>	';
			$par .= '		<h3>'.JText::_('WHATSNEXT_YOUR_OPTIONS').':</h3>'.n;
			$par .= '		<ul class="youroptions">'.n;
			$par .= '		<li> '.JText::_('WHATSNEXT_RETIRED_WANT_REPUBLISH').'. <span id="Updated"><a href="javascript:void(0);" class="flip" >'.JText::_('WHATSNEXT_RETIRED_PLS_REPUBLISH').'</a></span></li>'.n;
			$par .= '		</ul>'.n;
		break;
			
		//  abandoned
        case 9:
			$par = '		<p> '.JText::_('WHATSNEXT_ABANDONED_MSG').' '.$xhub->getCfg('hubShortName').' '.JText::_('WHATSNEXT_ABANDONED_CONTACT').'.</p>	';
		break;			
		} 		 
			 $html = $par.n;
			 
			 if($step2) {
			 $html .= '		<h4>'.JText::_('WHATSNEXT_REMAINING_STEPS').':</h4>'.n; 
			 $html .= '		<ul>'.n;
			 $html .= ' 		<li class="complete">'.JText::_('WHATSNEXT_REGISTER').' '.$xhub->getCfg('hubShortName').'</li>'.n;
			 $html .= $step2.n;
			 $html .= $step3.n;
			 $html .= $step4.n;
			 $html .= ' 		<li class="incomplete">  '.JText::_('WHATSNEXT_PUBLISH').' '.$xhub->getCfg('hubShortURL');
			 $html .= ($step5addon) ? $step5addon: '';
			 $html .= '			</li>'.n; 
			 $html .= '		</ul>'.n; 
			 }
			 
			 $html .= '<p style="margin-top:5em;border-top:1px solid #ccc;"> '.JText::_('WHATSNEXT_CONFUSED').' '.JText::_('VIEW').' <a href="contribute/tools">'.JText::_('RESOURCES').'</a> '.JText::_('EXPLAINING_CONTRIBUTION').'.</p>'.n;
			 $html .= '		</div>'.n;
			 $html .= '	  </div>'.n;
			 $html .= '<div class="clear"></div>'.n;
			 return $html;			
	}
		
	//-----------
	
	public function writeApproval($active_stage) 
	{
	
		//$stages = array(JText::_('CONTRIBTOOL_STEP_CONFIRM_VERSION'),JText::_('CONTRIBTOOL_STEP_CONFIRM_LICENSE'), JText::_('CONTRIBTOOL_STEP_APPEND_NOTES'), JText::_('CONTRIBTOOL_STEP_CONFIRM_APPROVE'));
		$stages = array(JText::_('CONTRIBTOOL_STEP_CONFIRM_VERSION'),JText::_('CONTRIBTOOL_STEP_CONFIRM_LICENSE'), JText::_('CONTRIBTOOL_STEP_CONFIRM_APPROVE'));
		$key = array_keys($stages, $active_stage);
	
		$html = "\t\t".'<div class="clear"></div>'."\n";
		$html .= "\t\t".'<ol id="steps">'."\n";
		$html .= "\t\t".' <li>'.JText::_('CONTRIBTOOL_APPROVE_PUBLICATION').':</li>'."\n";
		
		for ($i=0, $n=count( $stages ); $i < $n; $i++) 
			{
				$html .= "\t\t".' <li';
				
				if(strtolower($active_stage) == strtolower($stages[$i])) { 
					$html .= ' class="active"';
					
				} 
				else if (count($key) == 0 or $i > $key[0]) {				
					$html .= ' class="future"';
				}
	
				$html .= '>';
				$html .= $stages[$i];
				$html .= '</li>'."\n";
			}
		$html .= "\t\t".'</ol>'."\n";
		$html .= "\t\t".'<div class="clear"></div>'."\n";		
			
		echo $html;
	}
	//-----------
	
	public function writeResourceEditStage($stage, $version, $option, $rid, $published, $vnum) 
	{
		$stages = array(JText::_('CONTRIBTOOL_STEP_DESCRIPTION'),JText::_('CONTRIBTOOL_STEP_CONTRIBUTORS'),
		JText::_('CONTRIBTOOL_STEP_ATTACHMENTS'),JText::_('CONTRIBTOOL_STEP_TAGS'), JText::_('CONTRIBTOOL_STEP_FINALIZE'));
		$key = $stage-1;
		
		
		$html = "\t\t".'<div class="clear"></div>'."\n";
		$html .= "\t\t".'<ol id="steps" style="border-bottom:1px solid #ccc;margin-bottom:0;padding-bottom:0.7em;">'."\n";
		$html .= "\t\t".' <li>'.JText::_('CONTRIBTOOL_EDIT_PAGE_FOR').' ';
		if($version=='dev') {
		$html .= JText::_('CONTRIBTOOL_TIP_NEXT_TOOL_RELEASE');
		}
		else {
		$html .= JText::_('CONTRIBTOOL_TIP_CURRENT_VERSION');
		}
		$html .= ':</li>'.n;
		
		for ($i=0, $n=count( $stages ); $i < $n; $i++) 
			{
				$html .= "\t\t".' <li';
				
				if($i==$key) { 
					$html .= ' class="active"';
					
				} 
		
				$html .= '>';
				if($version=='dev' && $i!=$key && ($i+1)!= count( $stages )){
				$html .='<a href="'.JRoute::_('index.php?option=com_contribtool&amp;task=start&amp;step='.($i+1).'&amp;rid='.$rid).'">'.$stages[$i].'</a>';
				}
				else if($version=='current' && $i!=$key && ($i+1)!= count( $stages ) && ($i==0 or $i==3 or $i==2)){
				$html .='<a href="'.JRoute::_('index.php?option=com_contribtool&amp;task=start&amp;step='.($i+1).'&amp;rid='.$rid).'?editversion=current">'.$stages[$i].'</a>';
				}
				else {
				$html .= $stages[$i];
				}
				$html .= '</li>'."\n";
			}
	
		$html .= "\t\t".'</ol>'."\n";
		$html .= "\t\t".'<p class="';
		if($version=='dev') { 
			if($vnum) {
				$html .= 'devversion">'.ucfirst(JText::_('VERSION')).' '.$vnum;
			}
			else {
				$html .= 'devversion">'.ucfirst(JText::_('Next version'));
			}
			$html .= ' - '.JText::_('not published yet (changes take effect later)');
		}
		else if($version=='current' ) {
		$html .= 'currentversion">'.ucfirst(JText::_('VERSION')).' '.$vnum.' - '.JText::_('published now (changes take effect immediately)');
		}
		$html .= ($version=='dev' && $published) ? ' <span style="margin-left:2em;"><a href="'.JRoute::_('index.php?option=com_contribtool&amp;task=start&amp;step='.$stage.'&amp;rid='.$rid).'?editversion=current">'.JText::_('change current published version instead').'</a></span>' : ''; 
		$html .= ($version=='current' && $published) ? ' <span style="margin-left:2em;"><a href="'.JRoute::_('index.php?option=com_contribtool&amp;task=start&amp;step='.$stage.'&amp;rid='.$rid).'">'.JText::_('change upcoming version instead').'</a></span>' : '' ;
		$html .='</p>'."\n";
		
		$html .= "\t\t".'<div class="clear"></div>'."\n";		
			
		echo $html;
	}
	
	//-----------
	
	public function writeStates($active_state, $statuspath )
	{
	
		$states = array(JText::_('REGISTERED'),JText::_('CREATED'),JText::_('UPLOADED'),JText::_('INSTALLED'),JText::_('APPROVED'),JText::_('PUBLISHED')); // regular state list
	
		if($active_state == JText::_('RETIRED')) {
			$states[] = JText::_('RETIRED');
		}
		
		if($active_state == JText::_('UPDATED')) {
			$states[2] = JText::_('UPDATED');
		}
	
		$key = array_keys($states, $active_state);
			
		$html = "\t\t".'<div class="clear"></div>'."\n";
		$html .= "\t\t".'<ol id="steps">'."\n";
		$html .= "\t\t".' <li class="steps_hed">'.JText::_('STATUS').':</li>'."\n";
		
		for ($i=0, $n=count( $states ); $i < $n; $i++) 
		{
			$html .= "\t\t".' <li';
				
			if(strtolower($active_state) == strtolower($states[$i])) { 
				$html .= ' class="active"';
					
			} 
			/*if ($i< $key[0]) {				
				$html .= ' class="future"';
			}*/
			
			else if (count($key) == 0 or $i > $key[0]) {				
				$html .= ' class="future"';
			}
	
			$html .= '>';
			if(strtolower($active_state) == strtolower($states[$i])) {
				$html .= $states[$i];
			} 
			else {
				$html .= $states[$i];
			}
			$html .= '</li>'."\n";
		}
			
		$html .= "\t\t".'</ol>'."\n";
		$html .= "\t\t".'<div class="clear"></div>'."\n";		
			
		echo $html;
	}
	//-----------

	public function selectAccess($as, $value)
	{
		$html  = '<select name="access">';
		for ($i=0, $n=count( $as ); $i < $n; $i++)
		{
			if ($as[$i] != 'Registered' && $as[$i] != 'Special') {
				$html .= '<option value="'.$i.'"';
				if ($value == $i) {
					$html .= ' selected="selected"';
				}
				$html .= '>'.JText::_('ACCESS_'.strtoupper($as[$i])) .'</option>';
			}
		}
		$html .= '</select>';
		return $html;
	}
	//-----------
	
	public function selectGroup($groups, $value)
	{
		$html  = '<select name="group_owner">'.n;
		$html .= t.'<option value="">'.JText::_('SELECT_GROUP').'</option>'.n;
		foreach ($groups as $group)
		{
			$html .= t.'<option value="'.$group->cn.'"';
			if ($value == $group->cn) {
				$html .= ' selected="selected"';
			}
			$html .= '>'.$group->description .'</option>'.n;
		}
		$html .= '</select>'.n;
		return $html;
	}

	//-----------------------------------------------------
	// Tool registration/edit form
	//-----------------------------------------------------
	public function writeToolForm($option, $title, $admin, $juser, $defaults, $error, $id, $task, $config, $editversion='dev') 
	{	
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		
		$exec_pu = isset($config->parameters['exec_pu']) ? $config->parameters['exec_pu'] : 1;
		
		$execChoices[''] = JText::_('SELECT_TOP');
        $execChoices['@OPEN'] =  ucfirst(JText::_('TOOLACCESS_OPEN'));
        $execChoices['@US'] = ucfirst(JText::_('TOOLACCESS_US'));
		$execChoices['@D1'] = ucfirst(JText::_('TOOLACCESS_D1'));
		if($exec_pu) { $execChoices['@PU'] = ucfirst(JText::_('TOOLACCESS_PU')); }
		$execChoices['@GROUP'] = ucfirst(JText::_('RESTRICTED')).' '.JText::_('TO').' '.JText::_('GROUP_OR_GROUPS');
		
		$codeChoices[''] = JText::_('SELECT_TOP');
        $codeChoices['@OPEN'] = ucfirst(JText::_('OPEN_SOURCE')). ' ('.JText::_('OPEN_SOURCE_TIPS').')';
        $codeChoices['@DEV'] = ucfirst(JText::_('ACCESS_RESTRICTED'));
		
		$wikiChoices[''] = JText::_('SELECT_TOP');
        $wikiChoices['@OPEN'] = ucfirst(JText::_('ACCESS_OPEN'));
        $wikiChoices['@DEV'] = ucfirst(JText::_('ACCESS_RESTRICTED'));
		
?>
	 <div id="content-header">
			<h2><?php echo $title; ?></h2>
	 </div><!-- / #content-header -->
	  <div id="content-header-extra">
			<ul id="useroptions">
             <?php if($id) { ?>
           		<li><a href="<?php echo JRoute::_('index.php?option='.$option.a.'task=status'.a.'toolid='.$id); ?>"><?php echo JText::_('TOOL_STATUS'); ?></a></li>
            <?php }?>
            	<li class="last"><a href="<?php echo JRoute::_('index.php?option='.$option.'&task=pipeline'); ?>"><?php echo JText::_('CONTRIBTOOL_ALL_TOOLS'); ?></a></li>
			</ul>
	  </div><!-- / #content-header-extra -->
      <?php 
	  	if (count($error)>0) {
		  $err =  JText::_('ERR_FORM');
		  foreach ($error as $e) {
			$err.= (trim($e)!='') ? '<br />- '.$e : '';
		  }
	  	  echo ContribtoolHtml::error($err); } ?>
      <div class="section">
		<div class="aside expanded">
        <?php if(!$id) { ?>
        	<h3><?php echo JText::_('SIDE_HOW_CONTRIBUTE'); ?></h3>
			<p><?php echo JText::_('SIDE_EASY_PROCESS').' '.JText::_('VIEW').' <a href="contribute/tools">'.JText::_('RESOURCES').'</a> '.JText::_('EXPLAINING_CONTRIBUTION').'.'; ?></p>
       		<h3><?php echo JText::_('SIDE_WHAT_TOOLNAME'); ?></h3>
			<p><?php echo JText::_('SIDE_TIPS_TOOLNAME'); ?></p>
        <?php } else { ?>
       	 	<p><?php echo JText::_('SIDE_EDIT_TOOL'); ?></p>
        <?php } ?>            
            <h3><?php echo JText::_('SIDE_WHAT_TOOLACCESS'); ?></h3>
			<p><?php echo JText::_('SIDE_TIPS_TOOLACCESS'); ?></p>
            <h3><?php echo JText::_('SIDE_WHAT_CODEACCESS'); ?></h3>
			<?php echo JText::_('SIDE_TIPS_CODEACCESS'); ?>
            <h3><?php echo JText::_('SIDE_WHAT_WIKIACCESS'); ?></h3>
			<p><?php echo JText::_('SIDE_TIPS_WIKIACCESS'); ?></p>
		</div><!-- / .aside -->
        <div class="subject contracted">
			<form action="index.php" method="post" id="hubForm" enctype="multipart/form-data">
				<fieldset>
                <legend><?php echo JText::_('LEGEND_ABOUT'); ?>:</legend>
					<input type="hidden" name="id" value="<?php echo $id; ?>" />
					<input type="hidden" name="option" value="<?php echo $option; ?>" />
					<input type="hidden" name="task" value="<?php echo ($id) ? 'save' : 'register'; ?>" />
                    <input type="hidden" name="editversion" value="<?php echo $editversion; ?>" />
                    <label><?php echo JText::_('TOOLNAME'); ?>: 
				   		<?php if($id) { echo '<input type="hidden" name="tool[toolname]" id="t_toolname" value="'.$defaults['toolname'].'" />
						<strong>'.$defaults['toolname'].' ('; echo ($editversion=="current") ? JText::_('CURRENT_VERSION') : JText::_('DEV_VERSION'); echo ')</strong>';
						if(isset($defaults['published']) && $defaults['published']) { echo ' <a href="'.JRoute::_('index.php?option='.$option.'&amp;task=versions&amp;toolid='.$id).'">'.JText::_('ALL_VERSIONS').'</a>'; }  } 
						else { ?> <span class="required"><?php echo JText::_('REQUIRED'); ?></span>
					<input type="text" name="tool[toolname]" id="t_toolname" maxlength = "15" value="<?php echo $defaults['toolname']; ?>" />
					<p class="hint"><?php echo JText::_('HINT_TOOLNAME'); ?></p>
                    <?php }  ?>
				</label> 
				<label><?php echo JText::_('TITLE') ?>: <span class="required"><?php echo JText::_('REQUIRED'); ?></span>
					<input type="text" name="tool[title]" id="t_title" maxlength = "127" value="<?php echo $defaults['title']; ?>" />
					<p class="hint"><?php echo JText::_('HINT_TITLE'); ?></p>
				</label> 
                   <label><?php echo JText::_('VERSION') ?>: 
                   <?php if($editversion=='current') { echo '<input type="hidden" name="tool[version]" id="t_version" value="'.$defaults['version'].'" />
						<strong>'.$defaults['version'].'</strong>
						<p class="hint">'.JText::_('HINT_VERSION_PUBLISHED').'</p>'; }
						else { ?>
				   	<input type="text" name="tool[version]" id="t_version" maxlength = "15" value="<?php echo $defaults['version']; ?>" />
                    <p class="hint"><?php echo JText::_('HINT_VERSION'); ?></p>
                    <?php }  ?>
				   </label> 
                  <label><?php echo JText::_('AT_A_GLANCE') ?>: <span class="required"><?php echo JText::_('REQUIRED'); ?></span>
				    <input type="text" name="tool[description]" id="t_description" maxlength = "256" value="<?php echo stripslashes($defaults['description']); ?>" />
                    <p class="hint"><?php echo JText::_('HINT_DESCRIPTION'); ?></p>
				   </label> 
                   <?php if($id && isset($defaults['resourceid'])) { ?>
                   <label><?php echo JText::_('DESCRIPTION'); ?>: 
				   		<a href="/resources/<?php echo $defaults['resourceid']; ?>/?rev=dev"><?php echo JText::_('PREVIEW') ?></a>  | <a href="<?php echo JRoute::_('index.php?option='.$option.a.'task=start'.a.'rid='.$defaults['resourceid']); ?>"><?php echo JText::_('TODO_EDIT_PAGE') ?>...</a>
				   </label> 
                  <?php } ?>
                   <label><?php echo ($id) ? JText::_('APPLICATION_SCREEN_SIZE'): JText::_('SUGGESTED_SCREEN_SIZE')  ?>: </label>
				   		<?php echo JText::_('MARKER_WIDTH'); ?> <input type="text" class="sameline" name="tool[vncGeometryX]" id="vncGeometryX" size="4" maxlength="4" value="<?php echo $defaults['vncGeometryX']; ?>" /> x
                        <?php echo JText::_('MARKER_HEIGHT'); ?> <input type="text"class="sameline"  name="tool[vncGeometryY]" id="vncGeometryY" size="4" maxlength="4" value="<?php echo $defaults['vncGeometryY']; ?>" />
                        <p class="hint"><?php echo JText::_('HINT_VNC'); ?></p>
                    <label><?php echo JText::_('TOOL_ACCESS'); ?>: <span class="required"><?php echo JText::_('REQUIRED'); ?></span>
				   		<?php echo ContribtoolHtml::formSelect('tool[exec]', 't_exec', $execChoices, $defaults['exec'], 'groupchoices'); ?>
				   </label> 
                   <div id="groupname" <?php echo ($defaults['exec']=='@GROUP') ? 'style="display:block"': 'style="display:none"'; ?>>
                   	 <input type="text" name="tool[membergroups]" id="t_groups" value="<?php echo ContribToolHtml::getGroups($defaults['membergroups'], $id); ?>" />
                     <p class="hint"><?php echo JText::_('HINT_GROUPS'); ?></p>                 
                   </div> 
                    <label><?php echo JText::_('CODE_ACCESS'); ?>: <span class="required"><?php echo JText::_('REQUIRED'); ?></span>
				   		<?php echo ContribtoolHtml::formSelect('tool[code]', 't_code', $codeChoices, $defaults['code']); ?>
				   </label> 
                    <label><?php echo JText::_('WIKI_ACCESS'); ?>: <span class="required"><?php echo JText::_('REQUIRED'); ?></span>
				   		<?php echo ContribtoolHtml::formSelect('tool[wiki]', 't_wiki', $wikiChoices, $defaults['wiki']); ?>
				   </label>
                    <label><?php echo JText::_('DEVELOPMENT_TEAM'); ?>: <span class="required"><?php echo JText::_('REQUIRED'); ?></span>
				   		 <input type="text" name="tool[developers]" id="t_team" value="<?php echo ContribToolHtml::getDevTeam($defaults['developers'], $id);  ?>" />
                        <p class="hint"><?php echo $hubShortName.' '.JText::_('HINT_TEAM'); ?> </p>
				   </label>               
                   <p class="submit"><input type="submit" value="<?php echo (!$id) ? JText::_('REGISTER_TOOL') : JText::_('SAVE_CHANGES'); ?>" />
                    <?php if($id) { echo ' &nbsp;&nbsp;<a href="'.JRoute::_('index.php?option=com_contribtool&amp;task=status&amp;toolid='.$id).'" title="'.JText::_('HINT_CANCEL').'">'.JText::_('CANCEL').'</a>'; }?>
                    </p>
        		</fieldset>
        	</form>
        </div>
	</div> 
    <div class="clear"></div>       
<?php	
	}	

	//-----------------------------------------------------
	// Version Approval Steps
	//-----------------------------------------------------

	public function writeFinalizeVersion($status, $admin, $error, $option, $title) 
	{
		/*
			$status['toolid']
			$status['version']
			$status['toolname']
			$status['membergroups']
			$status['license']
			$status['description']
			$status['exec']
			$status['code']
			$status['wiki']
			$status['vncGeometry']
			$status['developers']
			$status['authors']
		*/

		$editpath = JRoute::_('index.php?option='.$option.a.'task=edit'.a.'toolid='.$status['toolid']);
		// get tool access text
		$toolaccess = ContribtoolHtml::getToolAccess($status['exec'], $status['membergroups']);
		// get source code access text
		$codeaccess = ContribtoolHtml::getCodeAccess($status['code']);		
		// get wiki access text
		$wikiaccess = ContribtoolHtml::getWikiAccess($status['wiki']);
		
	 	?>
          <div id="content-header">
			<h2><?php echo $title; ?></h2>
          </div><!-- / #content-header -->
          <div id="content-header-extra">
              <ul id="useroptions">
                   <li><a href="<?php echo JRoute::_('index.php?option='.$option.a.'task=status'.a.'toolid='.$status['toolid']); ?>"><?php echo JText::_('TOOL_STATUS'); ?></a></li>
                   <li class="last"><a href="<?php echo JRoute::_('index.php?option='.$option.'&task=create'); ?>" class="add"><?php echo JText::_('CONTRIBTOOL_NEW_TOOL'); ?></a></li>
             </ul>
          </div><!-- / #content-header-extra -->
         <div class="main section">   
        <?php
		
		// write header
		ContribtoolHtml::writeApproval('Approve');
		if($error) echo '<p class="error">'.$error.'</p>'; ?>
        <h4><?php echo JText::_('CONTRIBTOOL_FINAL_REVIEW'); ?>:</h4>         
       <form action="index.php" method="post" id="versionForm" name="versionForm">
                       <fieldset  class="versionfield">
                       <div class="two columns first">
                            <input type="hidden" name="option" value="<?php echo $option ?>" />   
                            <input type="hidden" name="task" value="finalizeversion" />
                            <input type="hidden" name="newstate" value="<?php echo ContribtoolHtml::getStatusNum('Approved') ?>" />
                            <input type="hidden" name="id" value="<?php echo $status['toolid'] ?>" />
                            <div>
                             <h4>Tool Information <a class="edit button"   href="<?php echo $editpath ?>" title="Edit this version information">Edit</a></h4> 		 
                                    <p><span class="heading"><?php echo JText::_('TITLE'); ?>: </span><span class="desc"><?php echo $status['title']; ?></span></p>
                                    <p><span class="heading"><?php echo JText::_('VERSION'); ?>: </span><span class="desc"><?php echo $status['version']; ?></span><span class="actionlink">[<a href="index.php?option=<?php echo $option ?>&amp;task=versions&amp;toolid=<?php echo $status['toolid'] ?>&amp;action=confirm">edit</a>]</span></p>
                                    <p><span class="heading"><?php echo JText::_('DESCRIPTION'); ?>: </span><span class="desc"><?php echo $status['description']; ?></span></p>
                                    <p><span class="heading"><?php echo JText::_('TOOL_ACCESS'); ?>: </span><span class="desc"> <?php echo $toolaccess; ?></span></p>
                                    <p><span class="heading"><?php echo JText::_('SOURCE_CODE'); ?>: </span><span class="desc"> <?php echo $codeaccess; ?></span></p>
                                    <p><span class="heading"><?php echo JText::_('WIKI_ACCESS'); ?>: </span><span class="desc"> <?php echo $wikiaccess; ?></span></p>
                                    <p><span class="heading"><?php echo JText::_('SCREEN_SIZE'); ?>: </span><span class="desc"> <?php echo $status['vncGeometry']; ?></span></p>
                                    <p><span class="heading"><?php echo JText::_('DEVELOPERS'); ?>: </span><span class="desc"> <?php echo ContribToolHtml::getDevTeam($status['developers']); ?></span></p>
                                    <p><span class="heading"><?php echo JText::_('AUTHORS'); ?>: </span><span class="desc"> <?php echo ContribToolHtml::getDevTeam($status['authors']); ?></span></p>
                                    <p><a href="/tools/<?php echo $status['toolname'].'?rev=dev'; ?>"><?php echo JText::_('PREVIEW_RES_PAGE'); ?></a></p>
                            </div>
                        </div>
                        <div class="two columns second">
                          <h4><?php echo JText::_('TOOL_LICENSE'); ?> <span class="actionlink">[
                          <a href="index.php?option=<?php echo $option ?>&amp;task=license&amp;toolid=<?php echo $status['toolid'] ?>&amp;action=confirm"><?php echo JText::_('EDIT'); ?></a>]</span></h4>
                          <pre class="licensetxt"><?php echo stripslashes($status['license']); ?></pre>           
                        </div>
                        <div class="moveon"><input type="submit"  value="<?php echo JText::_('APPROVE_THIS_TOOL'); ?>" /></div>  
                       </fieldset>
        </form>  
		<div class="clear"></div>
        </div>
    <?php		
	}
	
	//------------

	public function writeNotesArea($notes, $option, $type='', $edititem = 0, $addnew = 1) 
	{
	
		$out ='';
		$i = 0;	
		if(count($notes) > 0 ) {
			$out .= '<ul class="features">'.n;
			for ($i=0, $n=count( $notes ); $i < $n; $i++) {
				$note = $notes[$i];
				$out .= ' <li>'.n;
				$out .= '  <span><span>'.JText::_('EDIT').'</span></span>'.n;
				$out .= $note->note;
				$out .= ' </li>'.n;
			}
			$out .= '</ul>'.n;
		}
		
		if ($addnew) {
			$out .= ContribtoolHtml::addNoteArea($i, $option, $type);
		}
        
		return $out;   
	
	}
	
	//------------

	public function addNoteArea($i, $option, $type = 'item') 
	{	
		$out  = ''; 
	 	$out .= '<label>'.n;
		$out .= ' <span class="selectgroup editnote">'.n;
		$out .= '   <textarea name="'.$type.'[]" id="'.$type.$i.'"  rows="6" cols="35"></textarea>'.n;
        $out .= '   <span class="extras"><span></span></span>'.n;
        $out .= ' </span>'.n;
		$out .= '</label>'.n;
		
		return $out;
	
	}
	
	//------------

	public function writeToolLicense($licenses, $status, $admin, $error, $option, $action, $license_choice, $code, $action, $title) 
	{
		$open 					= ($code == '@OPEN') ? 1 : 0 ;
		$codeaccess 			= ($code == '@OPEN') ? 'open' : 'closed';
		$newstate   			= ($action == 'confirm') ? 'Approved' :  $status['state'];
		$statuspath 			= JRoute::_('index.php?option='.$option.a.'task=status'.a.'toolid='.$status['toolid']);
		$submitlabel 			= ($action == 'edit') ? 'Save' : 'Use this license';
		$instruction 			= ($action == 'edit') ? 'Specify license for next tool release:' : 'Please confirm your license for this tool release:';
		$codeChoices['@OPEN'] 	= 'open source (anyone can access code)';
		$codeChoices['@DEV'] 	= 'closed code';	
		$choices 				= ContribtoolHtml::formSelect('t_code', 't_code',  $codeChoices, $code,'shifted','');
		$licenseChoices 		= array();
		$licenseChoices['c1'] 	= 'Load a standard license';
		if($licenses) {
			foreach ($licenses as $l) {
				if($l->name != 'default') $licenseChoices[$l->name] = $l->title;
			}
		}
		?>
          <div id="content-header">
			<h2><?php echo $title; ?></h2>
          </div><!-- / #content-header -->
          <div id="content-header-extra">
              <ul id="useroptions">
                   <li><a href="<?php echo JRoute::_('index.php?option='.$option.a.'task=status'.a.'toolid='.$status['toolid']); ?>"><?php echo JText::_('TOOL_STATUS'); ?></a></li>
                   <li class="last"><a href="<?php echo JRoute::_('index.php?option='.$option.'&task=create'); ?>" class="add"><?php echo JText::_('CONTRIBTOOL_NEW_TOOL'); ?></a></li>
             </ul>
          </div><!-- / #content-header-extra -->
         <div class="main section">   
        <?php
		
		$templates = ContribtoolHtml::formSelect('templates', 'templates',  $licenseChoices, $license_choice['template'],'shifted','');
		if($action == 'confirm') {
				ContribtoolHtml::writeApproval('Confirm license');
		}
		$license = ($status['license'] && !$open) ? $status['license'] : '' ;
		?>
		<div class="two columns first">
	   <?php  if($error) echo '<p class="error">'.$error.'</p>'; ?>
		<h4><?php echo $instruction ?></h4>
		<form action="index.php" method="post" id="versionForm" name="versionForm">
					   <fieldset class="versionfield">
						   <label><?php echo JText::_('CODE_ACCESS'); ?>: </label>
							<?php echo $choices ?>
							<div id="lic_cl"><?php echo JText::_('LICENSE'); ?>:</div>
							 <div class="licinput" >
								<textarea name="license" cols="50" rows="15" id="license"><?php echo stripslashes($license_choice['text']); ?></textarea>
									 <?php if($licenses) {
										foreach ($licenses as $l) {
											echo '<input type="hidden" name="'.$l->name.'" id="'.$l->name.'" value="'.stripslashes(htmlentities($l->text)).'" /> '.n; } 
									 } ?>
							<input type="hidden" name="option" value="<?php echo $option ?>" />   
							<input type="hidden" name="task" value="savelicense" />
							<input type="hidden" name="curcode" id="curcode" value="<?php echo $open ?>" />
							<input type="hidden" name="newstate" value="<?php echo $newstate ?>" />
							<input type="hidden" name="action" value="<?php echo $action ?>" />
							<input type="hidden" name="id" value="<?php echo $status['toolid'] ?>" />
							<input type="hidden" name="toolname" value="<?php echo $status['toolname'] ?>" />
							</div>  
							<div id="lic" >
							<label><?php echo JText::_('LICENSE_TEMPLATE'); ?>: </label>
							<?php echo $templates ?>
							</div>     
							<div id="legendnotes"><p><?php echo JText::_('LICENSE_TEMPLATE_TIP'); ?>:
							<br />[<?php echo JText::_('YEAR'); ?>]<br />[<?php echo JText::_('OWNER'); ?>]<br />[<?php echo JText::_('ORGANIZATION'); ?>]<br />[<?php echo strtoupper(JText::_('ONE_LINE_DESCRIPTION')); ?>]<br />[<?php echo JText::_('URL'); ?>]</p>
							<label><input type="checkbox" name="authorize" value="1" /> <?php echo JText::_('LICENSE_CERTIFY').' <strong>'.JText::_('OPEN_SOURCE').'</strong> '.JText::_('LICENSE_UNDER_SPECIFIED'); ?></label></div> 
							<div class="moveon"><input type="submit"  value="<?php echo $submitlabel ?>" /></div>                             
					   </fieldset>                              				    
		</form>    	
		</div>
			<div class="two columns second">
            	<h3><?php echo JText::_('CONTRIBTOOL_LICENSE_WHAT_OPTIONS'); ?></h3>
				<p class="opensource"><?php echo '<strong>'.ucfirst(JText::_('OPEN_SOURCE')).'</strong><br />'.JText::_('CONTRIBTOOL_LICENSE_IF_YOU_CHOOSE').' <a href="http://www.opensource.org/" rel="external" title="Open Source Initiative">'.strtolower(JText::_('OPEN_SOURCE')).'</a>, '.JText::_('CONTRIBTOOL_LICENSE_OPEN_TXT'); ?></p>
				<p class="error"><?php echo JText::_('CONTRIBTOOL_LICENSE_ATTENTION'); ?> </p>	
				<p class="closedsource"> <strong><?php echo ucfirst(JText::_('CLOSED_SOURCE')); ?>  </strong><br /><?php echo JText::_('CONTRIBTOOL_LICENSE_CLOSED_TXT'); ?> </p>           
			</div>
			<div class="clear"></div>
            </div>
		<?php
	}
	
	//------------

	public function writeToolVersions($tools, $status, $admin, $error, $option, $action, $title) 
	{
		/*
			$status['toolid']
			$status['published']
			$status['version']
			$status['state']
			$status['toolname']
			$status['membergroups']
			$status['resourceid']
			$status['currentversion']
			$tools[]->codeaccess
			$tools[]->toolaccess
			$tools[]->wikiaccess
			$tools[]->doi
			$tools[]->state
			$tools[]->version
			$tools[]->released
			$tools[]->revision
			$tools[]->title
			$tools[]->description
			$tools[]->authors
		*/

		$juser = &JFactory::getUser();
	?>
    <div id="content-header">
			<h2><?php echo $title; ?></h2>
    </div><!-- / #content-header -->
    <div id="content-header-extra">
			<ul id="useroptions">
            	<li><a href="<?php echo JRoute::_('index.php?option='.$option.a.'task=status'.a.'toolid='.$status['toolid']); ?>"><?php echo JText::_('TOOL_STATUS'); ?></a></li>
				<li class="last"><a href="<?php echo JRoute::_('index.php?option='.$option.'&task=create'); ?>" class="add"><?php echo JText::_('CONTRIBTOOL_NEW_TOOL'); ?></a></li>
			</ul>
		</div><!-- / #content-header-extra -->
     <div class="main section">   
     <?php 
		($status['published'] != 1 && !$status['version']) ?  $hint = '1.0' :$hint = '' ; // if tool is under dev and no version was specified before
		$statuspath = JRoute::_('index.php?option='.$option.a.'task=status'.a.'toolid='.$status['toolid']);
		
		$newstate = ($action == 'edit') ? $status['state']: ContribtoolHtml::getStatusNum('Approved') ;
		$submitlabel = ($action == 'edit') ? JText::_('SAVE') : JText::_('USE_THIS_VERSION');
		if($action == 'confirm') {
		ContribtoolHtml::writeApproval(JText::_('CONFIRM_VERSION'));
		}
	
		$xhub =& XFactory::getHub();
		
		$rconfig =& JComponentHelper::getParams( 'com_resources' );
		$hubDOIpath = $rconfig->get('doi');
		
		
		
	?> 
        <div class="two columns first">	
		<?php 
		if ($error) { echo ContribtoolHtml::error( $error ); }  
		if($action != 'dev' && $status['state']!=ContribtoolHtml::getStatusNum('Published')) {
			if ($action == 'confirm' or $action == 'edit') { 
				
			?>
			<h4><?php echo JText::_('VERSION_PLS_CONFIRM'); ?> <?php echo($action == 'edit') ? JText::_('NEXT'): JText::_('THIS'); ?> <?php echo JText::_('TOOL_RELEASE'); ?>:</h4>
		<?php }
			else if($action == 'new' && $status['toolname']) { // new version is required ?>
			<h4><?php echo JText::_('CONTRIBTOOL_ENTER_UNIQUE_VERSION'); ?>:</h4>
        <?php
			}
		?>
		<form action="index.php" method="post" id="versionForm">
				   <fieldset class="versionfield">
                       <label for="newversion"><?php echo ucfirst(JText::_('VERSION')); ?>: </label>
                        <input type="text" name="newversion" id="newversion" value="<?php echo $status['version']; ?>" size="20" maxlength = "15" />	
                        <input type="hidden" name="option" value="<?php echo $option ?>" />   
                        <input type="hidden" name="task" value="saveversion" />
                        <input type="hidden" name="newstate" value="<?php echo $newstate ?>" />
                        <input type="hidden" name="action" value="<?php echo $action ?>" />
                        <input type="hidden" name="id" value="<?php echo $status['toolid'] ?>" />
                        <input type="hidden" name="toolname" value="<?php echo $status['toolname'] ?>" />
                        <input type="submit" value="<?php echo $submitlabel ?>" />
				   </fieldset>
		</form>			
		<?php
		}
		?>
		<h3><?php echo JText::_('CONTRIBTOOL_EXISTING_VERSIONS'); ?>:</h3>
		<?php
		if($tools && $status['toolname']) { // show versions
		?>
			<table id="tktlist">
			 <thead>
			  <tr>
			   <th><?php echo ucfirst(JText::_('VERSION')); ?></th>
			   <th><?php echo ucfirst(JText::_('RELEASED')); ?></th>
			   <th><?php echo ucfirst(JText::_('SUBVERSION')); ?></th>
			   <th><?php echo ucfirst(JText::_('PUBLISHED')); ?></th>
               <th></th>
			  </tr>
			 </thead>
			 <tbody>             
	<?php
			$i=0;				
			foreach ($tools as $t) {
		
			// get tool access text
			$toolaccess = ContribtoolHtml::getToolAccess($t->toolaccess, $status['membergroups']);
			// get source code access text
			$codeaccess = ContribtoolHtml::getCodeAccess($t->codeaccess);		
			// get wiki access text
			$wikiaccess = ContribtoolHtml::getWikiAccess($t->wikiaccess);
			
			$handle = ($t->doi) ? $hubDOIpath.'r'.$status['resourceid'].'.'.$t->doi : '' ;
			
			$t->version = ($t->state==3 && $t->version==$status['currentversion']) ? JText::_('NO_LABEL') : $t->version; 
	?>
			  <tr id="displays_<?php echo $i; ?>">
			   <td><span class="showcontrols"><a href="javascript:void(0);" class="expand" style="border:none;" id="exp_<?php echo $i; ?>">&nbsp;&nbsp;</a></span> <?php echo ($t->version) ? $t->version : JText::_('NA'); ?></td>
			   <td><?php if($t->state!=3) { echo $t->released ? JHTML::_('date', $t->released, '%d %b. %Y') : 'N/A'; } else { echo '<span class="yes">'.JText::_('UNDER_DEVELOPMENT').'</span>'; } ?></td>
			   <td><?php if($t->state!=3 or ($t->state==3 && $t->revision != $status['currentrevision'])) { echo $t->revision; } else { echo '-'; } ?></td>
			   <td><span class="<?php echo ($t->state=='1' ? 'toolpublished' : 'toolunpublished'); ?>"></span></td> 
                <td><?php if ($t->state=='1' && $admin) { echo '<span class="actionlink"><a href="'.JRoute::_('index.php?option='.$option.a.'task=edit'.a.'toolid='.$status['toolid']).'?editversion=current">'.JText::_('EDIT') .'</a></span>'; } else if ($t->state==3) { echo '<span class="actionlink"><a href="'.JRoute::_('index.php?option='.$option.a.'task=edit'.a.'toolid='.$status['toolid']).'?editversion=dev">'.JText::_('EDIT') .'</a></span>'; } ?></td>	             			
			  </tr>
              <tr id="configure_<?php echo $i; ?>" class="config hide">
               <td id="conftdone_<?php echo $i; ?>"></td>
			   <td colspan="4" id="conftdtwo_<?php echo $i; ?>">
                <div id="confdiv_<?php echo $i; ?>" class="vmanage"> 
                 <p><span class="heading"><?php echo ucfirst(JText::_('TITLE')); ?>: </span><span class="desc"><?php echo $t->title; ?></span></p>
                 <p><span class="heading"><?php echo ucfirst(JText::_('DESCRIPTION')); ?>: </span><span class="desc"><?php echo $t->description; ?></span></p>
                 <p><span class="heading"><?php echo ucfirst(JText::_('AUTHORS')); ?>: </span><span class="desc"> <?php echo ContribToolHtml::getDevTeam($t->authors); ?></span></p>
                 <p><span class="heading"><?php echo ucfirst(JText::_('TOOL_ACCESS')); ?>: </span><span class="desc"> <?php echo $toolaccess; ?></span></p>
                 <p><span class="heading"><?php echo ucfirst(JText::_('CODE_ACCESS')); ?>: </span><span class="desc"> <?php echo $codeaccess; ?></span></p>
                 <?php if ($handle) { echo ' <p><span class="heading">'.JText::_('DOI').': </span><span class="desc"><a href="http://hdl.handle.net/'.$handle.'">'.$handle.'</a></span></p>'; } ?>
                </div>
               </td>		
			  </tr>
			  <?php 	$i++;
			  } // end foreach
		   ?>
		 </tbody>
			</table>		
		<?php
		}
		else { // no versions found
			echo (JText::_('CONTRIBTOOL_NO_VERSIONS').' '.$status['toolname']. '. '.ucfirst(JText::_('GO_BACK_TO')).' <a href="'.$statuspath.'">'.strtolower(JText::_('TOOL_STATUS')).'</a>.');
		}
		?>
		</div>
		<div class="two columns second">
			<h3><?php echo JText::_('CONTRIBTOOL_VERSION_WHY_NEED_NUMBER'); ?></h3>
			<p><?php echo JText::_('CONTRIBTOOL_VERSION_WHY_NEED_NUMBER_ANSWER'); ?></p>
			<h3><?php echo JText::_('CONTRIBTOOL_VERSION_HOW_DECIDE'); ?></h3>
			<p><?php echo JText::_('CONTRIBTOOL_VERSION_HOW_DECIDE_ANSWER_ONE'); ?></p>			
			<p><?php echo JText::_('CONTRIBTOOL_VERSION_HOW_DECIDE_ANSWER_ONE'); ?></p>		
			<p><?php echo JText::_('CONTRIBTOOL_VERSION_HOW_DECIDE_ANSWER_THREE'); ?></p>
		</div>
		<div class="clear"></div>
</div>
		<?php
	
	}	
	//-----------------------------------------------------
	// Status
	//-----------------------------------------------------
    
    public function writeToolStatus($status, $user, $admin, $error, $option, $msg, $title, $config) 
	{	
		// get configurations/ defaults
		$developer_site = isset($config->parameters['developer_site']) ? $config->parameters['developer_site'] : 'nanoFORGE';
		$developer_url 	= isset($config->parameters['developer_url']) ? $config->parameters['developer_url'] : 'https://developer.nanohub.org';
		$project_path 	= isset($config->parameters['project_path']) ? $config->parameters['project_path'] : '/projects/app-';
		$dev_suffix 	= isset($config->parameters['dev_suffix']) ? $config->parameters['dev_suffix'] : '_dev';
		
		// get status name
		ContribtoolHtml::getStatusName($status['state'], $state);
		ContribtoolHtml::getStatusClass($status['state'], $statusClass);
		
		// format tool title
		$tooltitle = $status['title'];
        if ($status['version']) { $tooltitle = $tooltitle.' v.'.$status['version']; }

		// write breadcrumbs
		//$bc = '<a href="index.php?option='.$option.'">'.JText::_('CONTRIBTOOL_ALL_TOOLS').' ('.$status['ntools'].')</a> &gt; '.$status['toolname'];
		//echo ContribtoolHtml::div( $bc, '', 'breadcrumbs' );
		
		// write title
		$title .= ' - <span class="state_hed">'.$state.'</span>';
		echo ContribtoolHtml::div( ContribtoolHtml::hed( 2, $title ), '', 'content-header' );
		
		// display error
		if ($error) { echo ContribtoolHtml::error( $error ); }  else { 
			
		// set common paths
		$statuspath =  JRoute::_('index.php?option='.$option.a.'task=status'.a.'toolid='.$status['toolid']);
		$editpath 	=  JRoute::_('index.php?option='.$option.a.'task=edit'.a.'toolid='.$status['toolid']);
		$cancelpath =  JRoute::_('index.php?option='.$option.a.'task=cancel'.a.'toolid='.$status['toolid']);
		$licensepath = JRoute::_('index.php?option='.$option.a.'task=license'.a.'toolid='.$status['toolid']);
		$ticketpath = JRoute::_('index.php?option=com_support'.a.'task=ticket'.a.'id='.$status['ticketid']);
		$testpath = 'index.php?option=com_tools'.a.'task=invoke&app='.$status['toolname'].a.'version=dev';
		
		// get configs
		$xhub 			=& XFactory::getHub();
		$hubShortName 	= $xhub->getCfg('hubShortName');
		$hubShortURL 	= $xhub->getCfg('hubShortURL');
		$hubLongURL 	= $xhub->getCfg('hubLongURL');
		
		
		// get tool access text
		$toolaccess = ContribtoolHtml::getToolAccess($status['exec'], $status['membergroups']);
		// get source code access text
		$codeaccess = ContribtoolHtml::getCodeAccess($status['code']);		
		// get wiki access text
		$wikiaccess = ContribtoolHtml::getWikiAccess($status['wiki']);	
		?> 
		
	
		<div id="content-header-extra">
			<ul id="useroptions">
            	<li><a href="<?php echo JRoute::_('index.php?option='.$option.'&task=pipeline'); ?>"><?php echo JText::_('CONTRIBTOOL_ALL_TOOLS'); ?></a></li>
				<li class="last"><a href="<?php echo JRoute::_('index.php?option='.$option.'&task=create'); ?>" class="add"><?php echo JText::_('CONTRIBTOOL_NEW_TOOL'); ?></a></li>
			</ul>
		</div><!-- / #content-header-extra -->
		<div class="main section">  
		<?php
			if(ContribtoolHtml::toolActive($status['state'])) {
				ContribtoolHtml::writeStates($state, $statuspath);
			}
		?>
       <div class="toolinfo_note"> 
        <?php if($msg) { echo '<p class="passed">'.$msg.'</p>'; } ?>
        <?php if(ContribtoolHtml::getNumofTools($status)) { echo '<p>'.ContribtoolHtml::getNumofTools($status).'.</p>'; }?>
       </div>

            <div class="two columns first">
        	<div class="toolinfo<?php echo $statusClass; ?>"> 
     		<table id="toolstatus">
					<tbody>
						<tr>
							<th colspan="2" class="toolinfo_hed"><?php 
							echo JText::_('TOOL_INFO'); 
							if(ContribtoolHtml::toolActive($status['state'])) { 
							echo ' <a class="edit button" href="'.$editpath.'" title="'.JText::_('EDIT_TIPS').'">'.JText::_('EDIT'); ?></a><?php } ?></th>
						</tr>
                        <tr>
				   			<th><?php echo JText::_('TITLE'); ?></th>
				   			<td><?php echo stripslashes($status['title']).' ('.$status['toolname'].' - '.strtolower(JText::_('ID')).' #'.$status['toolid'].')'; ?></td>
				  		</tr>
				 		<tr>
				   			<th><?php echo JText::_('VERSION'); ?></th>
				   			<td><?php echo ($status['version']) ? JText::_('THIS_VERSION').' '.$status['version']: JText::_('THIS_VERSION').': '.JText::_('NO_LABEL');
								if(!$status['published'] or ($status['version']!=$status['currentversion'] && ContribtoolHtml::toolActive($status['state']))) { echo ' ('.JText::_('UNDER_DEVELOPMENT').')';  }
								if($status['published']) { echo ' [<a href="'.JRoute::_('index.php?option='.$option.'&amp;task=versions&amp;toolid='.$status['toolid']).'">'
								.strtolower(JText::_('ALL_VERSIONS')).'</a>]'; }  ?>
                         	</td>
				  		</tr>
				 		<tr>
				   			<th><?php echo JText::_('AT_A_GLANCE'); ?></th>
				   			<td><?php echo stripslashes($status['description']); ?></td>
				  		</tr>
                        <tr>
				   			<th><?php echo JText::_('DESCRIPTION'); ?></th>
				   			<td>
                            	<a href="/resources/<?php echo $status['resourceid']; ?>/?rev=dev"><?php echo JText::_('PREVIEW'); ?></a> | 
                   				<a href="index.php?option=com_contribtool&amp;task=start&amp;step=1&amp;rid=<?php echo $status['resourceid']; ?>"><?php echo JText::_('EDIT_THIS_PAGE'); ?></a>
                            </td>
				  		</tr>
                        <tr>
				   			<th><?php echo JText::_('VNC_GEOMETRY'); ?></th>
				   			<td><?php echo $status['vncGeometryX'].'x'.$status['vncGeometryY'];?></td>
				  		</tr>
                        <tr>
				   			<th><?php echo JText::_('TOOL_EXEC'); ?></th>
				   			<td><?php echo $toolaccess; ?></td>
				  		</tr>
                        <tr>
				   			<th><?php echo JText::_('SOURCE_CODE'); ?></th>
				   			<td><?php echo $codeaccess; ?>
                            <?php if( ContribtoolHtml::toolActive($status['state']) && ContribtoolHtml::toolWIP($status['state'])) { ?>
                   					[<a href="<?php echo $licensepath ?>"><?php echo JText::_('CHANGE_LICENSE'); ?></a>]
                   			<?php } ?>
                   			</td>
				  		</tr>
                        <tr>
				   			<th><?php echo JText::_('PROJECT_AREA'); ?></th>
				   			<td><?php echo $wikiaccess; ?></td>
				  		</tr>
                        <tr>
				   			<th><?php echo JText::_('DEVELOPMENT_TEAM'); ?></th>
				   			<td><?php echo ContribToolHtml::getDevTeam($status['developers']); ?></td>
				  		</tr>
                        <tr>
							<th colspan="2" class="toolinfo_hed"><?php echo JText::_('DEVELOPER_TOOLS');?></th>
						</tr>
                         <tr>
							<th colspan="2">
                            <!-- / tool admin icons -->
							<ul class="adminactions">
                                <li class="history"><a href="<?php echo $ticketpath; ?>" title="<?php echo JText::_('HISTORY_TIPS');?>">History</a></li>
                                <?php if ($status['state'] != 'Registered') { // hide for tools in registered status ?>
                                <li class="wiki"><a href="<?php echo $developer_url.$project_path.$status['toolname']; ?>/wiki" title="<?php echo JText::_('WIKI_TIPS');?>">Wiki</a></li>
                                <li class="sourcecode"><a href="<?php echo $developer_url.$project_path.$status['toolname']; ?>/browser" title="<?php echo JText::_('SOURCE_TIPS');?>">
								<?php echo JText::_('SOURCE');?></a></li>
                                <li class="timeline"><a href="<?php echo $developer_url.$project_path.$status['toolname']; ?>/timeline" title="<?php echo JText::_('TIMELINE_TIPS');?>">
								<?php echo JText::_('TIMELINE');?></a></li>
                                <?php }  else { ?>
                                <li class="wiki"><span class="disabled"><?php echo JText::_('WIKI');?></span></li>
                                <li class="sourcecode"><span class="disabled"><?php echo JText::_('SOURCE_CODE');?></span></li>
                                <li class="timeline"><span class="disabled"><?php echo JText::_('TIMELINE');?></span></li>
                                 <?php } ?>
                                <li class="message"><a href="javascript:void(0);" title="<?php echo JText::_('SEND_MESSAGE').' '.JText::_('TO');?> <?php echo ($admin) ? strtolower(JText::_('DEVELOPMENT_TEAM')) : JText::_('SITE_ADMIN'); ?>" class="showmsg"><?php echo JText::_('MESSAGE');?></a></li>
                                <?php if($status['published']!=1 && ContribtoolHtml::toolActive($status['state'])) {  // show cancel option only for tools under development ?>
                                <li class="canceltool"><a href="javascript:void(0);" title="<?php echo JText::_('CANCEL_TIPS');?>" class="showcancel"><?php echo JText::_('CANCEL');?></a></li>
                                <?php } ?>                   
                        	</ul>
                            <div id="ctCancel">
                                <p class="error">
                                <span class="cancel_warning"><?php echo JText::_('CANCEL_WARNING');?> </span> 
                                <a href="<?php echo $cancelpath; ?>"><?php echo JText::_('CANCEL_YES');?></a> 
                                <span class="boundary">|</span> <a href="javascript:void(0);" class="hidecancel"><?php echo JText::_('CANCEL_NO');?></a>                             
                                </p>
                            </div>
                            <div id="ctComment" >
                                <span class="closebox"><a href="javascript:void(0);" class="hidemsg">x</a></span>		
                                <h4><?php echo JText::_('SEND_MESSAGE').' '.JText::_('TO');?> <?php echo ($admin) ? strtolower(JText::_('DEVELOPMENT_TEAM')) : strtolower(JText::_('SITE_ADMIN')); ?>:</h4>					
                                <form action="index.php" method="post" id="commentForm">					
            			<?php if($admin) { ?>
                                  <fieldset><label><input type="checkbox" name="access" value="1" /> <?php echo JText::_('COMMENT_PRIVACY_TIPS'); ?></label></fieldset>				  
            			<?php } ?>					 
                                    <fieldset><textarea name="comment" style="width:300px;height:100px;" cols="50" rows="5"></textarea></fieldset>					 	 
                                    <fieldset>
                                    <input type="hidden" name="option" value="<?php echo $option ?>" />
                                    <input type="hidden" name="task" value="message" />
                                    <input type="hidden" name="id" value="<?php echo $status['toolid']?>" />
                                    <input type="hidden" name="toolname" value="<?php echo $status['toolname']?>" />	
                                    <input type="submit" value="<?php echo JText::_('SEND_MESSAGE'); ?>" /> 
                                    </fieldset>               		
                                </form>
                            </div>
                           
                            </th>
						</tr>                     
                            <?php if ($admin) { ?>
         		 		<tr>
							<th colspan="2" class="toolinfo_hed"><?php echo JText::_('ADMIN_CONTROLS');?></th>
						</tr>
                          <tr>
							<th colspan="2">
                            <!-- / admin controls -->
                                 <form action="index2.php" method="post" id="adminCalls">                                                
                                <ul class="adminactions">
                                    <li id="createtool"><a href="javascript:void(0);" class="admincall" title="<?php echo JText::_('COMMAND_ADD_REPO_TIPS');?>">
										<?php echo JText::_('COMMAND_ADD_REPO');?></a></li>
                                    <li id="installtool"><a href="javascript:void(0);"  class="admincall" title="<?php echo JText::_('COMMAND_INSTALL_TIPS');?>">
										<?php echo JText::_('COMMAND_INSTALL');?></a></li>
                                    <li id="publishtool"><a href="javascript:void(0);"  class="admincall" title="<?php echo JText::_('COMMAND_PUBLISH_TIPS');?>">
										<?php echo JText::_('COMMAND_PUBLISH');?></a></li>                       
                                    <li id="retiretool"><a href="javascript:void(0);"  class="admincall" title="<?php echo JText::_('COMMAND_RETIRE_TIPS');?>">
										<?php echo JText::_('COMMAND_RETIRE');?></a></li>        
                                </ul>
                                <div id="ctSending">
                                </div>
                                <div id="ctSuccess">
                                </div>
                                 <fieldset>	
                                    <input type="hidden" name="option" value="<?php echo $option ?>" />
                                    <input type="hidden" name="task" value="" />
                                    <input type="hidden" name="id" value="<?php echo $status['toolid']?>" />
                                    <input type="hidden" name="toolname" value="<?php echo $status['toolname']?>" />
                                    <input type="hidden" name="no_html" value="1" />
                                 </fieldset>	
                                </form>                      
                                	</th>
                              </tr>                      
                              <tr>
                                  <th>
								  	<span class="admin_label"><?php echo JText::_('FLIP_STATUS');?>:</span>
                                  	<span class="admin_label"><?php echo JText::_('PRIORITY');?>:</span>
                                    <span class="admin_label"><?php echo JText::_('MESSAGE_TO_DEV_TEAM').' <br />('.JText::_('OPTIONAL').')';?></span>
                                  </th>
                                  <td>
                                   <form action="index.php" method="post" id="adminForm">         
                                     <fieldset class="admin_label">                         				   		   
                                        <select name="newstate">
                                         <option value="1"<? if($status['state'] == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('REGISTERED');?></option>
                                         <option value="2"<? if($status['state'] == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('CREATED');?></option>
                                         <option value="3"<? if($status['state'] == 3) { echo ' selected="selected"'; } ?>><?php echo JText::_('UPLOADED');?></option>
                                         <option value="4"<? if($status['state'] == 4) { echo ' selected="selected"'; } ?>><?php echo JText::_('INSTALLED');?></option>
                                         <option value="5"<? if($status['state'] == 5) { echo ' selected="selected"'; } ?>><?php echo JText::_('UPDATED');?></option>
                                         <option value="6"<? if($status['state'] == 6) { echo ' selected="selected"'; } ?>><?php echo JText::_('APPROVED');?></option>
                                         <option value="7"<? if($status['state'] == 7) { echo ' selected="selected"'; } ?>><?php echo JText::_('PUBLISHED');?></option>
                    <?php if($status['published']==1) { // admin can retire only tools that have a published flag on ?>
                                         <option value="8"<? if($status['state'] == 8) { echo ' selected="selected"'; } ?>><?php echo JText::_('RETIRED');?></option>
                    <?php } ?>
                                        </select>
                                      </fieldset>				                     				   
                                     <fieldset class="admin_label">
                                        <select name="priority">
                                         <option value="3"<? if($status['priority'] == 3) { echo ' selected="selected"'; } ?>><?php echo JText::_('NORMAL');?></option>
                                         <option value="2"<? if($status['priority'] == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('HIGH');?></option>
                                         <option value="1"<? if($status['priority'] == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('CRITICAL');?></option>
                                         <option value="4"<? if($status['priority'] == 4) { echo ' selected="selected"'; } ?>><?php echo JText::_('LOW');?></option>
                                         <option value="5"<? if($status['priority'] == 5) { echo ' selected="selected"'; } ?>><?php echo JText::_('LOWEST');?></option>
                                        </select>		   
                                        <input type="hidden" name="option" value="<?php echo $option ?>" />
                                        <input type="hidden" name="task" value="update" />
                                        <input type="hidden" name="id" value="<?php echo $status['toolid']?>" />
                                        <input type="hidden" name="toolname" value="<?php echo $status['toolname']?>" /> 
                                         </fieldset>             		   
                                     
                                         <fieldset class="admin_label">
                                        <textarea name="comment" id="comment" cols="40" rows="5"></textarea>
                                        <input type="submit" class="submitform" value="<?php echo JText::_('APPLY_CHANGE');?>" />
                                         </fieldset>
                                        </form>   		   
                                       </td>
                                   </tr>                                   	                              
                <?php } ?>            	
                   
                    </tbody>
            </table>                  	
                
            </div>
		</div><!-- / .twocolumn left -->
        <div class="two columns second">
			<div id="whatsnext">
                <h2 class="nextaction"><?php echo JText::_('WHAT_NEXT');?></h2>
                <form action="index.php" method="post" id="statusForm">
                	<fieldset>		   
                        <input type="hidden" name="option" value="<?php echo $option ?>" />
                        <input type="hidden" name="task" value="update" />
                        <input type="hidden" name="id" value="<?php echo $status['toolid']?>" />
                        <input type="hidden" name="toolname" value="<?php echo $status['toolname']?>" />	
                        <input type="hidden" name="newstate" id="newstate" value="" />
                	</fieldset>	
                </form>
                <?php echo ContribtoolHtml::writeWhatNext ($status, $config, $option, $tooltitle); ?>
            </div>
        </div>
        <div class="clear"></div>
        </div>
        <?php 			   	
	  	} //end if no error
    }
	
	//-----------------------------------------------------
	// Pipeline
	//-----------------------------------------------------

	public function summary (&$tools, $option, $filters, $admin, &$pageNav, $total, $title, $config) 
	{	
		// get configurations/ defaults
		$developer_site = isset($config->parameters['developer_site']) ? $config->parameters['developer_site'] : 'nanoFORGE';
		$developer_url 	= isset($config->parameters['developer_url']) ? $config->parameters['developer_url'] : 'https://developer.nanohub.org';
		$project_path 	= isset($config->parameters['project_path']) ? $config->parameters['project_path'] : '/projects/app-';
		$dev_suffix 	= isset($config->parameters['dev_suffix']) ? $config->parameters['dev_suffix'] : '_dev';
	?>
		<div id="content-header">
			<h2><?php echo $title; ?></h2>
		</div><!-- / #content-header -->
		<div id="content-header-extra">
			<ul id="useroptions">
				<li class="last"><a href="<?php echo JRoute::_('index.php?option='.$option.'&task=create'); ?>" class="add"><?php echo JText::_('CONTRIBTOOL_NEW_TOOL'); ?></a></li>
			</ul>
		</div><!-- / #content-header-extra -->

		<div class="main section">
 			<p><?php if(!$admin) { echo  (JText::_('CONTRIBTOOL_CHECK_STATUS'). ' ( '.$total.' )'); } 
			else { echo (JText::_('CONTRIBTOOL_LOGGED_AS_ADMIN').' '.JText::_('CONTRIBTOOL_ALL_SUBMISSIONS').' ('.$total.')');  } ?>.</p> 
			<form action="index.php" method="get" name="adminForm">
					<fieldset class="filters">
						<label>
							<?php echo JText::_('FIND_TOOL'); ?>:
							<input type="text" name="search" id="search" value="<?php echo ($filters['search'] == '') ? htmlentities($filters['search']) : ''; ?>" />
						</label>
			
						<label>
							<?php echo JText::_('FILTER_BY'); ?>:
							<select name="filterby">
								<option value="all"<?php if ($filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>><?php echo JText::_('CONTRIBTOOL_FILTER_ALL'); ?></option>
								<option value="mine"<?php if ($filters['filterby'] == 'mine') { echo ' selected="selected"'; } ?>><?php echo JText::_('CONTRIBTOOL_FILTER_MINE'); ?></option>
								<option value="published"<?php if ($filters['filterby'] == 'published') { echo ' selected="selected"'; } ?>><?php echo JText::_('CONTRIBTOOL_FILTER_PUBLISHED'); ?></option>
								<?php if ($admin) { ?>
								<option value="dev"<?php if ($filters['filterby'] == 'dev') { echo ' selected="selected"'; } ?>><?php echo JText::_('CONTRIBTOOL_FILTER_ALL_DEV'); ?></option>
								<?php } ?>
							</select>
						</label>
                        
                        <label>
							<?php echo JText::_('SORT_BY'); ?>:
							<select name="sortby">
								<?php if($admin) { ?>	
                                <option value="f.state, f.priority, f.toolname"<? if($filters['sortby'] == 'f.state, f.priority, f.toolname') { echo ' selected="selected"'; } ?>><?php echo JText::_('CONTRIBTOOL_SORTBY_STATUS'); ?></option>
                <?php } else { ?>
                                <option value="f.state, f.registered"<? if($filters['sortby']  == 'f.state, f.registered') { echo ' selected="selected"'; } ?>><?php echo JText::_('CONTRIBTOOL_SORTBY_STATUS'); ?></option>
                <?php } ?>
                                <option value="f.registered"<? if($filters['sortby']  == 'f.registered') { echo ' selected="selected"'; } ?>><?php echo JText::_('CONTRIBTOOL_SORTBY_REG'); ?></option>
                                <option value="f.toolname"<? if($filters['sortby']  == 'f.toolname') { echo ' selected="selected"'; } ?>><?php echo JText::_('CONTRIBTOOL_SORTBY_NAME'); ?></option>
                <?php if($admin) { ?>			
                                <option value="f.priority"<? if($filters['sortby']  == 'f.priority') { echo ' selected="selected"'; } ?>><?php echo JText::_('PRIORITY'); ?></option>
                                <option value="f.state_changed DESC"<? if($filters['sortby'] == 'f.state_changed DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('LAST_STATUS_CHANGE'); ?></option>
                <?php } ?>
							</select>
						</label>

						<input type="submit" value="<?php echo JText::_('GO'); ?>" />
                        <input type="hidden" name="option" value="<?php echo $option ?>" />
						<input type="hidden" name="task" value="pipeline" />
					
					</fieldset>
                	
					<table id="tktlist">
						<thead>
							<tr>
                            	<th scope="col"><?php echo JText::_('ID'); ?></th>
								<th scope="col"><?php echo JText::_('TOOL'); ?></th>
								<th scope="col"><?php echo JText::_('STATUS'); ?></th>
								<th scope="col"><?php echo JText::_('LAST_STATUS_CHANGE'); ?></th>
                                <th scope="col"><?php echo JText::_('REGISTERED'); ?></th>
								<th scope="col"><?php echo JText::_('LINKS'); ?></th>								
							</tr>
						</thead>
<?php if (count($tools) > $filters['limit']) { ?>
						<tfoot>
							<tr>
								<td colspan="7"><?php echo $pageNav->getListFooter(); ?></td>
							</tr>
						</tfoot>
<?php } ?>
						<tbody>
                        <?php
		$k = 0;
			
		for ($i=0, $n=count( $tools ); $i < $n; $i++) 
		{
			$row = &$tools[$i];
			
			$ticketpath = JRoute::_('index.php?option=com_support'.a.'task=ticket'.a.'id='.$row->ticketid);
			
			$lastchange = ($row->state_changed!='0000-00-00 00:00:00') ? ContribtoolHtml::timeAgo($row->state_changed) : ContribtoolHtml::timeAgo($row->registered);
			$title = ($row->version) ? $row->title.' v'.$row->version : $row->title;
			ContribtoolHtml::getStatusName($row->state, $status);
					
?>
							<tr  class="<?php echo strtolower($status) ; if(!$admin) { echo ('_user'); }?>">
								<td><?php echo $row->id; ?></td>
								<td> <a href="<?php echo 'index.php?option='.$option.a.'task=status'.a.'toolid='.$row->id; ?>" title="<?php echo $row->title; ?>"><?php echo  ($title.' ('.$row->toolname.')'); ?></a></td>
								<td style="white-space: nowrap;"><a href="<?php echo 'index.php?option='.$option.a.'task=status'.a.'toolid='.$row->id; ?>" title="<?php echo $row->title; ?>"><?php echo ucfirst($status); ?></a></td>
								<td style="white-space: nowrap;"><?php echo $lastchange.' '.JText::_('AGO'); ?></td>
                                <td style="white-space: nowrap;"><?php echo JHTML::_('date', $row->registered, '%d %b, %Y'); ?></td>
								<td style="white-space: nowrap;" <?php if (!ContribtoolHtml::toolEstablished($row->state)) { echo ' class="disabled_links" ';} ?>>
                                	<?php if (!ContribtoolHtml::toolActive($row->state)) { echo '<span>'.JText::_('RESOURCE').'</span>';} else { ?>
                                    <a href="<?php echo '/tools/'.$row->toolname; ?>" ><?php echo JText::_('RESOURCE'); ?></a><?php } ?>
                                    |
                                    <a href="<?php echo JRoute::_('index.php?option=com_support'.a.'task=ticket'.a.'id='.$row->ticketid) ?>" ><?php echo strtolower(JText::_('HISTORY')); ?></a>
                                    |
									<?php if ($status=='Abandoned') { echo '<span>'.strtolower(JText::_('PROJECT')).'</span>';} else { ?>
                                    <a href="<?php echo $developer_url.$project_path.($row->toolname); ?>/wiki" rel="external" ><?php echo strtolower(JText::_('PROJECT')); ?></a><?php } ?>
                                </td>
								
							</tr>
<?php
			$k = 1 - $k;
		}
?>
                        </tbody>
					</table>
				
			
			</form>
            <?php
			echo ($total <= 0) ? '<p>'.JText::_('NO_CONTRIBUTIONS').'. <a href="'.JRoute::_('index.php?option='.$option.'&task=create').'" >'.JText::_('Contribute a new tool').'</a>.</p>' : '';
			?>
		</div><!-- /.main section -->	

<?php 
	
	
	}

	//-------------------------------------------------------------
	// Resource page editing
	//-------------------------------------------------------------

	public function writeResourceEditForm ($rid, $toolid, $status, $row, $version, $allnbtags, $step, $option, $admin, $tags, $tagfa, $fat, $authors, $title, $groups) 
	{		
		$nextstep = $step+1;
		$task = ($nextstep==5) ? 'preview' : 'start';
	      
		echo ContribtoolHtml::div( ContribtoolHtml::hed( 2, $title ), '', 'content-header' );
		?>
           <div id="content-header-extra">
			<ul id="useroptions">
            	<li><a href="<?php echo JRoute::_('index.php?option='.$option.a.'task=status'.a.'toolid='.$toolid); ?>"><?php echo JText::_('TOOL_STATUS'); ?></a></li>
				<li class="last"><a href="<?php echo JRoute::_('index.php?option='.$option.'&task=create'); ?>" class="add"><?php echo JText::_('CONTRIBTOOL_NEW_TOOL'); ?></a></li>
			</ul>
		</div><!-- / #content-header-extra -->
        <?php	

		$dev = ($version=='dev') ? 1: 0;
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
			
		if($version=='dev') {
			$v = ($status['version'] && $status['version']!= $status['currentversion']) ? $status['version'] : '';
		}
		else {
			$v = $status['version'];
		}
		
		ContribtoolHtml::writeResourceEditStage($step, $version, $option, $rid, $status['published'], $v);
	 
		?>
<div class="main section noborder">
            <form action="index.php" method="post" id="hubForm" >
			<div style="float:left; width:70%;padding:1em 0 1em 0;">
            		 <?php if($step!=1) { ?><span style="float:left;width:100px;"><input type="button" value=" &lt; <?php echo ucfirst(JText::_('PREVIOUS')); ?> " class="returntoedit" /></span><?php } ?>
			         <span style="float:right;width:120px;"><input type="submit" value="<?php echo ucfirst(JText::_('Save & Go Next')); ?> &gt;" /></span>
			</div>
			
            <div class="clear"></div>
            <?php switch ($step) {
            //  registered
            case 1: ?>
            
			<div class="explaination"> 
            	<p class="help"><?php echo $dev ? JText::_('SIDE_EDIT_PAGE') : JText::_('SIDE_EDIT_PAGE_CURRENT'); ?></p>	      
				<p><?php echo JText::_('COMPOSE_ABSTRACT_HINT'); ?></p>
			</div>       
			<fieldset>
				<h3><?php echo JText::_('COMPOSE_ABOUT'); ?></h3>
				<label>
                <?php echo JText::_('COMPOSE_TITLE'); ?>: <span class="required"><?php echo JText::_('REQUIRED'); ?></span>
                <?php if ($dev) { ?>					
					<input type="text" name="title" maxlength="127" value="<?php echo stripslashes($status['title']); ?>" />
                <?php  } else { ?>
                	<input type="text" name="rtitle" maxlength="127" value="<?php echo stripslashes($status['title']); ?>" disabled="disabled" />
                    <input type="hidden" name="title" maxlength="127" value="<?php echo stripslashes($status['title']); ?>" />
                    <p class="warning"> <?php   echo JText::_('TITLE_CANT_CHANGE'); ?></p>
                <?php  } ?>
				</label>
				<label>
					<?php echo JText::_('COMPOSE_AT_A_GLANCE'); ?>: <span class="required"><?php echo JText::_('REQUIRED'); ?></span>
					<input type="text" name="description"  maxlength="256" value="<?php echo stripslashes($status['description']); ?>" />
				</label>
				<label>
					<?php echo JText::_('COMPOSE_ABSTRACT'); ?>:
					<textarea name="fulltext" cols="50" rows="20"><?php echo stripslashes($status['fulltext']); ?></textarea>
                    <span class="hint"><a href="/topics/Help:WikiFormatting"><?php echo JText::_('WIKI_FORMATTING'); ?></a> <?php echo JText::_('COMPOSE_TIP_ALLOWED'); ?>.</span>
				</label>
			</fieldset><div class="clear"></div>

			<div class="explaination">
				<p><?php echo JText::_('COMPOSE_CUSTOM_FIELDS_EXPLANATION'); ?></p>
			</div>
			<fieldset>
				<h3><?php echo JText::_('COMPOSE_DETAILS'); ?></h3>
<?php 
foreach ($allnbtags as $tagname => $tagcontent) 
if($tagname!='screenshots' and $tagname!='bio') {
{ 
	$tagcontent = preg_replace('/<br\\s*?\/??>/i', "", $tagcontent);
?>
				<label>
					<?php echo JText::_(strtoupper($tagname)); ?>:
					<textarea name="<?php echo 'nbtag['.$tagname.']'; ?>" cols="50" rows="6"><?php echo stripslashes($tagcontent); ?></textarea>
				</label>
<?php 
}
} 
?>
				<?php break; 
				case 2: 
					// authors
					ContribtoolHtml::stepAuthors( $rid, $version, $option);
					 break; 
				case 3:
					// attachments
				 	ContribtoolHtml::stepAttach( $rid, $option, $version, $status['published']);
				 	break; 
				case 4: 
					// tags
					ContribtoolHtml::stepTags( $rid, $tags, $tagfa, $fat, $option, $status['published'], $version );
					break;
				}
				 ?> 
                 </fieldset>          
                 <fieldset style="display:none;"> 
                    <input type="hidden" name="toolid" value="<?php echo $toolid; ?>" />
                    <input type="hidden" name="rid" value="<?php echo $rid; ?>" />
                    <input type="hidden" name="option" value="<?php echo $option; ?>" />
                    <input type="hidden" name="task" value="<?php echo $task; ?>" />
                    <input type="hidden" name="step" value="<?php echo $nextstep; ?>" />
                    <input type="hidden" name="editversion" value="<?php echo $version; ?>" />
                    <input type="hidden" name="toolname" value="<?php echo $status['toolname']; ?>" />
                   
				</fieldset>
            <div class="clear"></div>
 			<div style="float:left; width:70%;padding:1em 0 1em 0;">
            		 <?php if($step!=1) { ?><span style="float:left;width:100px;"><input type="button" value=" &lt; <?php echo ucfirst(JText::_('PREVIOUS')); ?> " class="returntoedit" /></span><?php } ?>
			           <span style="float:right;width:120px;"><input type="submit" value="<?php echo ucfirst(JText::_('Save & Go Next')); ?> &gt;" /></span>
			</div>
			
		</form>
 </div>       
        <?php	
	}
	//-----------
	
	public function stepAttach( $rid, $option, $version, $published=0)
	{
		$allowupload = ($version=='current' or !$published) ? 1 : 0;
		?>
			<div class="explaination">
				<h4><?php echo JText::_('ATTACH_WHAT_ARE_ATTACHMENTS'); ?></h4>
				<p><?php echo JText::_('ATTACH_EXPLANATION'); ?></p>

			</div>
			<fieldset>
				<h3><?php echo JText::_('ATTACH_ATTACHMENTS'); ?></h3>
				<iframe width="100%" height="200" frameborder="0" name="attaches" id="attaches" src="index.php?option=<?php echo $option; ?>&amp;task=attach&amp;rid=<?php echo $rid; ?>&amp;no_html=1&amp;type=7&amp;allowupload=<?php echo $allowupload; ?>"></iframe>				
			</fieldset><div class="clear"></div>
            <div class="explaination">
				<h4><?php echo JText::_('ATTACH_WHAT_ARE_SCREENSHOTS'); ?></h4>
				<p><?php echo JText::_('ATTACH_SCREENSHOTS_EXPLANATION'); ?></p>

			</div>
			<fieldset>
				<h3><?php echo JText::_('ATTACH_SCREENSHOTS'); ?></h3>
				<iframe width="100%" height="400" frameborder="0" name="screens" id="screens" src="index.php?option=<?php echo $option; ?>&amp;task=screenshots&amp;rid=<?php echo $rid; ?>&amp;no_html=1&amp;version=<?php echo $version; ?>"></iframe>				
			</fieldset><div class="clear"></div>
		<?php
	}

	//-----------

	public function stepAuthors($rid, $version, $option)
	{
		?>
			<div class="explaination">
				<h4><?php echo JText::_('AUTHORS_NO_LOGIN'); ?></h4>
				<p><?php echo JText::_('AUTHORS_NO_LOGIN_EXPLANATION'); ?></p>
			</div>
			<fieldset>
				<h3><?php echo JText::_('AUTHORS_AUTHORS'); ?></h3>

				<iframe width="100%" height="400" frameborder="0" name="authors" id="authors" src="index2.php?option=<?php echo $option; ?>&amp;task=authors&amp;rid=<?php echo $rid; ?>&amp;no_html=1&amp;version=<?php echo $version ?>"></iframe>
            
			</fieldset><div class="clear"></div>
		<?php
	}

	//-----------

	public function stepTags( $rid, $tags, $tagfa, $fat, $option, $published=0, $version )
	{
		
		$showwarning = ($version=='current' or !$published) ? 0 : 1;
		
		?>
			<div class="explaination">
				<h4><?php echo JText::_('TAGS_WHAT_ARE_TAGS'); ?></h4>
				<p><?php echo JText::_('TAGS_EXPLANATION'); ?></p>
			</div>
			<fieldset>
				<h3><?php echo JText::_('TAGS_ADD'); ?></h3>
<?php if (!empty($fat)) { ?>				
				<fieldset>
					<legend><?php echo JText::_('TAGS_SELECT_FOCUS_AREA'); ?>:</legend>
					<?php
					foreach ($fat as $key => $value) 
					{
						echo '<label><input class="option" type="radio" name="tagfa" value="' . $value . '"';
						if ($tagfa == $value) {
							echo ' checked="checked "';
						}
						echo ' /> '.$key.'</label>'.n;
					}
					?>
				</fieldset>
<?php } ?>				
				<label>
					<?php echo JText::_('TAGS_ASSIGNED'); ?>:
					<?php
					JPluginHelper::importPlugin( 'tageditor' );
					$dispatcher =& JDispatcher::getInstance();
					
					$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tags','actags','',$tags,'')) );
					
					if (count($tf) > 0) {
						echo $tf[0];
					} else {
						echo t.t.t.'<textarea name="tags" id="tags-men" rows="6" cols="35">'. $tags .'</textarea>'.n;
					}
					?>
				</label>
				<p><?php echo JText::_('TAGS_NEW_EXPLANATION'); ?></p>
			</fieldset><div class="clear"></div>
		<?php
	}

	//-----------
	
	public function writeResourcePreview ( &$database, $option, $task, $rid, $toolid, $resource, $config, $usersgroups, $version, $title ) 
	{
		
		$juser =& JFactory::getUser();
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		$hubShortURL = $xhub->getCfg('hubShortURL');
		$license = '/legal/license';

		$html = '';
		
		?>
        
         <div id="content-header">
			<h2><?php echo $title; ?></h2>
		</div><!-- / #content-header -->
           <div id="content-header-extra">
			<ul id="useroptions">
            	<li><a href="<?php echo JRoute::_('index.php?option='.$option.a.'task=status'.a.'toolid='.$toolid); ?>"><?php echo JText::_('TOOL_STATUS'); ?></a></li>
				<li class="last"><a href="<?php echo JRoute::_('index.php?option='.$option.'&task=create'); ?>" class="add"><?php echo JText::_('CONTRIBTOOL_NEW_TOOL'); ?></a></li>
			</ul>
		</div><!-- / #content-header-extra -->
        <?php
		
		// Get parameters
		$rparams =& new JParameter( $resource->params );
		$params = $config;
		$params->merge( $rparams );

		// Get attributes
		$attribs =& new JParameter( $resource->attribs );
		
		// Get the resource's children
		$helper = new ResourcesHelper( $rid, $database );
		
		ContribtoolHtml::writeResourceEditStage(5, $version, $option, $rid, 0, $resource->version);
		?>
         <form action="index.php" method="post" id="hubForm" >
          		<fieldset style="display:none;"> 
                    <input type="hidden" name="toolid" value="<?php echo $toolid; ?>" />
                    <input type="hidden" name="rid" value="<?php echo $rid; ?>" />
                    <input type="hidden" name="option" value="<?php echo $option; ?>" />
                    <input type="hidden" name="task" value="status" />
                    <input type="hidden" name="msg" value="<?php echo JText::_('NOTICE_RES_UPDATED'); ?>" />
                    <input type="hidden" name="step" value="6" />
                    <input type="hidden" name="editversion" value="<?php echo $version; ?>" />
                    <input type="hidden" name="toolname" value="<?php echo $resource->alias; ?>" />
				</fieldset>
     
         	<div style="float:left; width:70%;padding:1em 0 1em 0;">
            		 <span style="float:left;width:100px;"><input type="button" value=" &lt; <?php echo ucfirst(JText::_('PREVIOUS')); ?> " class="returntoedit" /></span>
			         <span style="float:right;width:100px;"><input type="submit" value="<?php echo ucfirst(JText::_('CONTRIBTOOL_STEP_FINALIZE')); ?> &gt;" /></span>
			</div>
            <div class="clear"></div>
         </form>
        <?php
	
		$cats = array();
		$sections = array();
		
		ximport('resourcestats');
		
		$body = ResourcesHtml::about( $database, 0, $usersgroups, $resource, $helper, $config, array(), null, null, null, null, $params, $attribs, $option, 0 );
		
		$cat = array();
		$cat['about'] = JText::_('ABOUT');
		array_unshift($cats, $cat);
		array_unshift($sections, array('html'=>$body,'metadata'=>''));
		
		
		//$html  = '<h1 id="preview-header">'.JText::_('REVIEW_PREVIEW').'</h1>'.n;
		//$html .= '<div id="preview-pane">'.n;
		//$html .= ResourcesHtml::title( 'com_resources', $resource, $params, false );
		//$html .= ResourcesHtml::tabs( 'com_resources', $rid, $cats, 'about' );
		$html .= ResourcesHtml::sections( $sections, $cats, 'about', 'hide', 'main' );
		//$html .= '</div><!-- / #preview-pane -->'.n;
		
		echo $html;
	}
	//-------------------------------------------------------------
	// Other views
	//-------------------------------------------------------------
	
	public function ss_pop( $option, $rid, $wpath, $upath, $file, $error, $version, $vid, $shot=array()) 
	{
	$size = getimagesize($upath.DS.$file);
	$w = ($size[0] > 600) ? $size[0]/1.4444444 : $size[0];
	$h = ($w != $size[0]) ? $size[1]/1.4444444 : $size[1];
		
	$title = (count($shot)>0 && isset($shot[0]->title)) ? $shot[0]->title : ''; 
	
?>
	<div class="ss_pop">
		<div><img src="<?php echo $wpath.DS.$file; ?>" width="<?php echo $w; ?>" height="<?php echo $h; ?>"  /></div>
        <form action="index.php" name="hubForm" id="ss-pop-form" method="post" enctype="multipart/form-data">
        		<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="no_html" value="1" />
                <input type="hidden" name="version" value="<?php echo $version; ?>" />
				<input type="hidden" name="pid" id="pid" value="<?php echo $rid; ?>" />
				<input type="hidden" name="path" id="path" value="<?php echo $upath; ?>" />
                <input type="hidden" name="filename" id="filename" value="<?php echo $file; ?>" />
                <input type="hidden" name="vid" id="vid" value="<?php echo $vid; ?>" />
				<input type="hidden" name="task" value="savess" />
                <fieldset class="uploading">
                <label class="ss_title"><?php echo JText::_('SS_TITLE').':'; ?>
                    <input type="text" name="title"  size="127" maxlength="127" value="<?php echo $title; ?>" class="input_restricted" />
                </label>
					<input type="submit" id="ss_pop_save" value="<?php echo strtolower(JText::_('SAVE')); ?>" />
                </fieldset>
        </form>
     </div>
        
<?php
			
	}
	
	//--------------
	
	public function screenshots ( $option, $rid, $upath, $wpath, $cparams, $error, $version, $shots, $published=0)
	{
		$versionlabel = ($version == 'current') ? JText::_('CURRENTLY_PUBLISHED') : JText::_('DEVELOPMENT');
		if ($error) {
			echo ContribtoolHtml::error($error);
		}
		?>
       
        <form action="index.php" name="hubForm" id="screenshots-form" method="post" enctype="multipart/form-data">
        
         <h3><?php echo JText::_('EXISTING_SS'); ?> 
		 <?php if ($published) { ?> (<?php echo $version=='dev' ? JText::_('DEVELOPMENT').' '.strtolower(JText::_('VERSION')) : JText::_('CURRENTLY_PUBLISHED').' '.strtolower(JText::_('VERSION'));  ?>) <?php  } ?> </h3> 
      
		<?php
		$d = @dir($upath);
	
		$images = array();
		$tns = array();
		$all = array();
		$ordering = array();
		$html = '';
		
		// pick images from the upload directory
		if ($d) {
			while (false !== ($entry = $d->read())) 
			{			
				$img_file = $entry; 
				if (is_file($upath.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "bmp|gif|jpg|png|swf", $img_file )) {
						$images[] = $img_file;
					}
					if (eregi( "-tn", $img_file )) {
						$tns[] = $img_file;
					}
					$images = array_diff($images, $tns);
				}
							
			}
										
			$d->close();
		}
		
		// get rid of images without thumbnails
		if($images) {
			foreach($images as $key => $value) {
				$tn = ResourcesHtml::thumbnail($value);
				if(!is_file($upath.DS.$tn)) {
				unset($images[$key]);
				}
			}
			$images = array_values($images);
		}
		
		// Get screenshot titles and ordering
		$b = 0;
		if($images) {
			foreach($images as $ima) {
				$new = array();
				$new['img'] = $ima;
				$new['type'] = explode('.',$new['img']);
						
				// get title and ordering info from the database, if available
				if(count($shots) > 0) {
					foreach ($shots as $si) {				
						if($si->filename == $ima) {
							$new['title'] = stripslashes($si->title);
							$new['title'] = preg_replace( '/"((.)*?)"/i', "&#147;\\1&#148;", $new['title'] );
							$new['ordering'] = $si->ordering;
						}	
					}
				}
			
				$ordering[] = isset($new['ordering']) ? $new['ordering'] : $b;
				$b++;
				$all[]=$new;
			} 
		}
			
		// Order images
		if(count($shots) > 0)  {
			// sort by ordering
			array_multisort($ordering, $all);
		}
		else {
			// sort by name
			sort ($all);
		}
		$images = $all;
		

		// Display screenshots
		$els = '';
		$k = 0;
		$g = 0;
		for ($i=0, $n=count( $images ); $i < $n; $i++) 
		{
			$tn = ResourcesHtml::thumbnail($images[$i]['img']);
			
			if (is_file($upath.DS.$tn)) {
			
				if (strtolower(end($images[$i]['type'])) == 'swf') {
					$g++;
					$title = (isset($images[$i]['title']) && $images[$i]['title']!='' ) ? $images[$i]['title'] : JText::_('DEMO').' #'.$g;
					$els .= ' <li><a class="popup" rel="external" href="'.$wpath.DS.$images[$i]['img'].'" title="'.$title.'">';
					$els .= '<img src="'.$wpath.DS.$tn.'" alt="'.$title.'" id="ss_'.$i.'" /></a></li>'.n;
				} else {
					$k++;
					$title = (isset($images[$i]['title']) && $images[$i]['title']!='' ) ? $images[$i]['title']: JText::_('SCREENSHOT').' #'.$k;
					$els .= '<li>';
					$els .= '<span class="dev_ss"><a href="index2.php?option='.$option.'&amp;task=editss&amp;pid='.$rid.'&amp;filename='.$images[$i]['img'].'&amp;version='.$version.'&amp;no_html=1" class="edit_ss popup" rel="external">&nbsp;</a><a href="index2.php?option='.$option.'&amp;task=deletess&amp;pid='.$rid.'&amp;filename='.$images[$i]['img'].'&amp;version='.$version.'&amp;no_html=1" class="delete_ss">&nbsp;</a></span>';
					$els .= '<a class="popup"  href="index2.php?option='.$option.'&amp;task=editss&amp;pid='.$rid.'&amp;filename='.$images[$i]['img'].'&amp;version='.$version.'&amp;no_html=1" title="'.$title.'">';
					$els .= '<img src="'.$wpath.DS.$tn.'" alt="'.$title.'" id="ss_'.$i.'" /></a></li>'.n;
				}
				// add re-ordering option
				if($i != ($n-1)) {
					$els .= '<li style="width:20px;top:40px;">';
					$els .= '<a href="index2.php?option='.$option.'&amp;task=orderss&amp;pid='.$rid.'&amp;fl='.$images[$i+1]['img'].'&amp;fr='.$images[$i]['img'].'&amp;ol='.($i+1).'&amp;or='.$i.'&amp;version='.$version.'&amp;no_html=1"><img src="components'.DS.$option.DS.'images/reorder.gif"  /></a>';
					$els .= '</li>'.n;
				}
			}
		}
		
		if ($els) {
			$html .= '<div class="upload_ss">'.n;
			$html .= '<ul class="screenshots">'.n;
			$html .= $els;
			$html .= '</ul>'.n;
			$html .= '<div class="clear"></div></div>'.n;
		} else {
			// No images available
			$html .= '<p class="upload_ss">'.JText::_('UPLOAD_NO_SS').'</p>'.n;
		}
		echo $html;
		?>
        <div class="clear"></div>

        <h3><?php echo JText::_('UPLOAD_NEW_SS'); ?></h3>
		
			<fieldset class="uploading">
				<label>
					<input type="file" class="option" name="upload" />
                </label>
                <label class="ss_title"><?php echo JText::_('SS_TITLE').':'; ?>
                    <input type="text" name="title"  size="127" maxlength="127" value="" class="input_restricted" />
                    <input type="submit" class="upload" value="<?php echo strtolower(JText::_('UPLOAD')); ?>" />
                </label>
					
	
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="no_html" value="1" />
                <input type="hidden" name="changing_version" value="0" />
                <input type="hidden" name="version" id="version" value="<?php echo $version; ?>" />
				<input type="hidden" name="pid" id="pid" value="<?php echo $rid; ?>" />
				<input type="hidden" name="path" id="path" value="<?php echo $upath; ?>" />
				<input type="hidden" name="task" value="uploadss" />
			</fieldset>
		</form>
        <?php if ($published && $version=='dev') { ?>
         
          <form action="index.php" name="copySSForm"  method="post" enctype="multipart/form-data">
          <fieldset style="border-top:1px solid #ccc;padding-top:1em;">
          <h3><?php echo JText::_('Copy Screenshots'); ?></h3>
              <input type="hidden" name="option" value="<?php echo $option; ?>" />
              <input type="hidden" name="version" value="<?php echo $version; ?>" />
              <input type="hidden" name="task" value="copyss" />
              <input type="hidden" name="rid" value="<?php echo $rid; ?>" />
              <input type="hidden" name="no_html" value="1" />
          	<label>
					<?php 
					$v = $version=='dev' ? 'current' : 'development';
					echo JText::_('From').' '.$v.' '.strtolower(JText::_('VERSION')); ?>
                     <input type="submit" class="upload" value="<?php echo strtolower(JText::_('COPY')); ?>" />
                </label>
          </fieldset>
          </form>
         
         <?php } ?>

		<?php
		
		
		
	}
	
	//--------------
	
 	public function attachments( $option, $id, $path, $children, $config, $error='', $allowupload=1 ) 
	{
		
		$out = '';
		if($allowupload) {
		?>
		<form action="index.php" name="hubForm" id="attachments-form" method="post" enctype="multipart/form-data">
			<fieldset>
				<label>
					<input type="file" class="option" name="upload" />
					<input type="submit" class="option" value="<?php echo strtolower(JText::_('UPLOAD')); ?>" />
				</label>

				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="no_html" value="1" />
				<input type="hidden" name="pid" id="pid" value="<?php echo $id; ?>" />
				<input type="hidden" name="path" id="path" value="<?php echo $path; ?>" />
				<input type="hidden" name="task" value="saveattach" />
			</fieldset>
		</form>
		<?php
		}
		else {
		$out .= t.t.'<p class="warning">'.JText::_('SUPPORTING_DOCS_ONLY_CURRENT').' '.JText::_('PLEASE').' <a href="'.JRoute::_('index.php?option=com_contribtool&amp;task=start&amp;step=3&amp;rid='.$id).'?editversion=current" target="_top">'.strtolower(JText::_('EDIT_CURRENT_VERSION')).'</a>, '.JText::_('IF_YOU_NEED_CHANGES').'</p>'.n;
		}
		
		
		if ($error) {
			$out .= ContribtoolHtml::error($error);
		}
		
		// loop through children and build list
		if ($children) {
			$base = $config->get('uploadpath');
			
			$k = 0;
			$i = 0;
			$files = array(13,15,26,33,35,38);
			$n = count( $children );
			
			if($allowupload) {
			$out .= '<p>'.JText::_('ATTACH_EDIT_TITLE_EXPLANATION').'</p>'.n;
			}
			$out .= '<table class="list">'.n;
			
			foreach ($children as $child) 
			{
				$k++;
			
				// figure ou the URL to the file
				switch ($child->type) 
				{
					case 12:
						if ($child->path) {
							// internal link, not a resource
							$url = $child->path; 
						} else {
							// internal link but a resource
							$url = '/index.php?option=com_resources'.a.'id='. $child->id;
						}
						break;
					default: 
						$url = $child->path;
						/*if (substr($url, 0, 1) == '/') {
							$url = substr($url, 1, strlen($url)-1);
						}*/
						break;
				}

				// figure out the file type so we can give it the appropriate CSS class
				$type = '';
				$liclass = '';
				$file_name_arr = explode('.',$url);
	    		$type = end($file_name_arr);
				$type = (strlen($type) > 3) ? substr($type, 0, 3): $type;
				if ($child->type == 12) {
					$liclass = ' class="ftitle html';
				} else {
					$type = ($type) ? $type : 'html';
					$liclass = ' class="ftitle '.$type;
				}
			
				$out .= ' <tr>'.n;
				$out .= '  <td width="100%">';
				if($allowupload) {
				$out .= '<span'.$liclass.' item:name id:'.$child->id.'">'.$child->title.'</span><br />'.ContribtoolHtml::getFileAttribs( $url, $base );
				}
				else {
				$out .= '<span>'.$child->title.'</span>'.n;
				}
				$out .='</td>'.n;
				if($allowupload) {
				$out .= '  <td class="d">'.ContribtoolHtml::orderUpIcon( $i, $id, $child->id, 'a' ).'</td>'.n;
				$out .= '  <td class="u">'.ContribtoolHtml::orderDownIcon( $i, $n, $id, $child->id, 'a' ).'</td>'.n;
				$out .= '  <td class="t"><a href="index.php?option='.$this->_option.a.'task=deleteattach'.a.'no_html=1'.a.'id='.$child->id.a.'pid='.$id.'"><img src="/components/com_contribute/images/trash.gif" alt="'.JText::_('DELETE').'" /></a></td>'.n;
				}
				$out .= ' </tr>'.n;

				$i++;
			}
			$out .= '</table>'.n;
		} else {
			$out .= '<p>'.JText::_('ATTACH_NONE_FOUND').'</p>'.n;
		}
		echo $out;
	}

	//-----------
	
 	public function contributors( $id, $rows, $contributors, $option, $error='', $version='dev' ) 
	{
		
		$out = '';
		if($version=='dev') {
		?>
		<form action="index.php" id="authors-form" method="post" enctype="multipart/form-data">
			<fieldset>
				<label>
					<?php
					if ($error) {
						echo ContribtoolHtml::error($error);
					}
					
					$html  = '<select name="authid" id="authid">'.n;
					$html .= ' <option value="">'.JText::_('AUTHORS_SELECT').'</option>'.n;
					foreach ($rows as $row) 
					{
						$html .= t.'<option value="'.$row->uidNumber.'">'.$row->surname.', '.$row->givenName;
						$html .= ($row->middleName) ? ' '.$row->middleName : '';
						$html .= '</option>'.n;
					}
					$html .= '</select>'.n;
					echo $html;
					?> 
					<?php echo JText::_('OR'); ?>
				</label>
				
				<label>
					<input type="text" name="new_authors" value="" />
					<?php echo JText::_('AUTHORS_ENTER_LOGINS'); ?>
				</label>
				
				<p class="submit">
					<input type="submit" value="<?php echo JText::_('ADD'); ?>" />
				</p>

				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="no_html" value="1" />
				<input type="hidden" name="pid" id="pid" value="<?php echo $id; ?>" />
				<input type="hidden" name="task" value="saveauthor" />
			</fieldset>
		</form>
        
		<?php
		}
		else {
		$out .= t.t.'<p class="warning">'.JText::_('AUTHORS_CANT_CHANGE').'</p>'.n;
		}
		
		
		// Do we have any contributors associated with this resource?
		if ($contributors) {
			$i = 0;
			$n = count( $contributors );
	
			// loop through contributors and build HTML list
			$out .= '<table class="list">'.n;
			$out .= ' <tbody>'.n;
			foreach ($contributors as $contributor) 
			{
				$out .= ' <tr>'.n;
				// build name
				$out .= '  <td width="100%">'. stripslashes($contributor->firstname) .' ';
				if ($contributor->middlename != NULL) {
					$out .= stripslashes($contributor->middlename) .' ';
				}
				$out .= stripslashes($contributor->lastname);
				$out .= ' <span class="caption">('.$contributor->org.')</span></td>'.n;
				// build order-up/down icons
				if($version=='dev') {
				$out .= '  <td class="u">'.ContribtoolHtml::orderUpIcon( $i, $id, $contributor->id, 'c' ).'</td>'.n;
				$out .= '  <td class="d">'.ContribtoolHtml::orderDownIcon( $i, $n, $id, $contributor->id, 'c' ).'</td>'.n;
				// build trash icon
				$out .= '  <td class="t"><a href="index.php?option='.$option.a.'task=removeauthor'.a.'no_html=1'.a.'id='.$contributor->id.a.'pid='.$id.'"><img src="/components/com_contribute/images/trash.gif" alt="'.JText::_('DELETE').'" /></a></td>'.n;
				}
				$out .= ' </tr>'.n;

				$i++;
			}
			$out .= ' </tbody>'.n;
			$out .= '</table>'.n;
		} else {
			$out .= '<p>'.JText::_('AUTHORS_NONE_FOUND').'</p>'.n;
		}
		echo $out;
	}

	//------------

	public function parseTag($text, $tag)
	{
		preg_match("#<nb:".$tag.">(.*?)</nb:".$tag.">#s", $text, $matches);
		if (count($matches) > 0) {
			$match = $matches[0];
			$match = str_replace('<nb:'.$tag.'>','',$match);
			$match = str_replace('</nb:'.$tag.'>','',$match);
		} else {
			$match = '';
		}
		return $match;
	}

	//------------

	public function txt_unpee($pee)
	{
		$pee = str_replace("\t", '', $pee);
		$pee = str_replace('</p><p>', '', $pee);
		$pee = str_replace('<p>', '', $pee);
		$pee = str_replace('</p>', "\n", $pee);
		$pee = str_replace('<br />', '', $pee);
		$pee = trim($pee);
		return $pee; 
	}
	
	//-------------------------------------------------------------
	// Media manager functions
	//-------------------------------------------------------------
	
	public function pageTop( $option, $app, $title) 
	{
		?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php echo $title; ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<link rel="stylesheet" type="text/css" media="screen" href="/templates/<?php echo $app->getTemplate(); ?>/css/main.css" />
	<?php
		if (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$option.DS.'contribute.css')) {
			echo '<link rel="stylesheet" type="text/css" media="screen" href="'.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$option.DS.'contribute.css" />'.n;
		} else {
			echo '<link rel="stylesheet" type="text/css" media="screen" href="'.DS.'components'.DS.$option.DS.'contribute.css" />'.n;
		}
	?> 
    <link rel="stylesheet" href="/templates/<?php echo $app->getTemplate(); ?>/html/com_resources/resources.css" type="text/css" />
       <link rel="stylesheet" href="/templates/<?php echo $app->getTemplate(); ?>/html/com_contribtool/contribtool.css" type="text/css" />

	<script type="text/javascript" src="/media/system/js/mootools-uncompressed.js"></script>
  	<script type="text/javascript" src="/includes/js/joomla.javascript.js"></script>    
	<script type="text/javascript" src="/components/<?php echo $option; ?>/contribute.js"></script>
    <script type="text/javascript" src="/components/com_contribtool/contribtool.js"></script>
    <script type="text/javascript" src="/templates/<?php echo $app->getTemplate(); ?>/js/globals.js"></script>
 </head>
 <body id="small-page">
 		<?php
	}
	
	//-----------
	
	public function pageBottom() 
	{
		$html  = ' </body>'.n;
		$html .= '</html>'.n;
		echo $html;
	}
	
	//-----------
	
	public function orderUpIcon( $i, $pid, $cid, $for='' ) 
	{
		if ($i > 0 || ($i+0 > 0)) {
		    return '<a href="index.php?option=com_contribtool'.a.'no_html=1'.a.'pid='.$pid.a.'id='.$cid.a.'task=orderup'.$for.'" class="order up" title="'.JText::_('MOVE_UP').'"><span>'.JText::_('MOVE_UP').'</span></a>';
  		} else {
  		    return '&nbsp;';
		}
	}
	
	//-----------

	public function orderDownIcon( $i, $n, $pid, $cid, $for='' ) 
	{
		if ($i < $n-1 || $i+0 < $n-1) {
			return '<a href="index.php?option=com_contribtool'.a.'no_html=1'.a.'pid='.$pid.a.'id='.$cid.a.'task=orderdown'.$for.'" class="order down" title="'.JText::_('MOVE_DOWN').'"><span>'.JText::_('MOVE_DOWN').'</span></a>';
  		} else {
  		    return '&nbsp;';
		}
	}
	
	//-----------

	public function niceidformat($someid) 
	{
		while (strlen($someid) < 5) 
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}
	
	//-----------
	
	public function getFileAttribs( $path, $base_path='' )
	{
		// Return nothing if no path provided
		if (!$path) {
			return '';
		}
		
		if ($base_path) {
			// Strip any trailing slash
			if (substr($base_path, -1) == DS) { 
				$base_path = substr($base_path, 0, strlen($base_path) - 1);
			}
			// Ensure a starting slash
			if (substr($base_path, 0, 1) != DS) { 
				$base_path = DS.$base_path;
			}
		}
		
		// Ensure a starting slash
		if (substr($path, 0, 1) != DS) { 
			$path = DS.$path;
		}
		if (substr($path, 0, strlen($base_path)) == $base_path) {
			// Do nothing
		} else {
			$path = $base_path.$path;
		}
		$path = JPATH_ROOT.$path;

		//$file_name_arr = explode('.',$path);
	    //$type = end($file_name_arr);
		//$type = strtoupper($type);
		$file_name_arr = explode(DS,$path);
	    $type = end($file_name_arr);
	
		$fs = '';
		
		// Get the file size if the file exist
		if (file_exists( $path )) {
			$fs = filesize( $path );
		}
		
		$html  = '<span class="caption">('.$type;
		if ($fs) {
			switch ($type)
			{
				case 'HTM':
				case 'HTML':
				case 'PHP':
				case 'ASF':
				case 'SWF': $fs = ''; break;
				default: 
					$fs = ContribtoolHtml::formatsize($fs); 
					break;
			}
		
			$html .= ($fs) ? ', '.$fs : '';
		}
		$html .= ')</span>';
		
		return $html;
	}
	
	//-----------

	public function formatsize($file_size) 
	{
		if ($file_size >= 1073741824) {
			$file_size = round($file_size / 1073741824 * 100) / 100 . ' <abbr title="gigabytes">Gb</abbr>';
		} elseif ($file_size >= 1048576) {
			$file_size = round($file_size / 1048576 * 100) / 100 . ' <abbr title="megabytes">Mb</abbr>';
		} elseif ($file_size >= 1024) {
			$file_size = round($file_size / 1024 * 100) / 100 . ' <abbr title="kilobytes">Kb</abbr>';
		} else {
			$file_size = $file_size . ' <abbr title="bytes">b</abbr>';
		}
		return $file_size;
	}
	//-----------
	
	
}

?>
