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

if (!defined("n")) {
	define("t","\t");
	define("n","\n");
	define("r","\r");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class GroupsHtml 
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

	public function aside($txt, $id='')
	{
		return GroupsHtml::div($txt, 'aside', $id);
	}
	
	//-----------
	
	public function subject($txt, $id='')
	{
		return GroupsHtml::div($txt, 'subject', $id);
	}

	//-----------

	public function shortenText($text, $chars=300, $p=1) 
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' &#8230;';
		}
		
		if ($text == '') {
			$text = '&#8230;';
		}
		
		if ($p) {
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}

	//-----------

	public function encode_html($str, $quotes=1)
	{
		$str = stripslashes($str);
		$a = array(
			'&' => '&#38;',
			'<' => '&#60;',
			'>' => '&#62;',
		);
		if ($quotes) $a = $a + array(
			"'" => '&#39;',
			'"' => '&#34;',
		);

		return strtr($str, $a);
	}

	//-------------------------------------------------------------
	//  Group description page helpers
	//-------------------------------------------------------------

	public function metadata($sections, $cats, $option, $id)
	{
		$k = 0;
		
		$html  = '';
		foreach ($sections as $section)
		{
			if (isset($section['metadata']) && $section['metadata'] != '') {
				$name = key($cats[$k]);
				
				$html .= '<div class="dashboard">'.n;
				$html .= '<h4 class="dash-header">'.JText::_(strtoupper($cats[$k][$name]).'_DASHBOARD').' <small>'.$section['metadata'].'</small></h4>'.n;
				$html .= $section['dashboard'];
				$html .= '</div>'.n;
			}
			$k++;
		}
		$html .= GroupsHtml::div('', 'clear');
		
		return $html; //GroupsHtml::div($html, 'metadata');
	}
	
	//-----------

	public function sections( $sections, $cats, $active='about', $h, $c ) 
	{
		$html = '';
		
		if (!$sections) {
			return $html;
		}
		
		$k = 0;
		foreach ($sections as $section) 
		{
			if ($section['html'] != '') {
				$cls  = ($c) ? $c.' ' : '';
				if (key($cats[$k]) != $active) {
					$cls .= ($h) ? $h.' ' : '';
				}
				$html .= GroupsHtml::div( $section['html'], $cls.'section', key($cats[$k]).'-section' );
			}
			$k++;
		}
		
		return $html;
	}
	
	//-----------
	
	public function tabs( $option, $id, $cats, $active='profile' ) 
	{
		$html  = '<div id="sub-menu">'.n;
		$html .= t.'<ul>'.n;
		$i = 1;
		foreach ($cats as $cat)
		{
			$name = key($cat);
			if ($name != '') {
				/*if (strtolower($name) == $active) {
					$app =& JFactory::getApplication();
					$pathway =& $app->getPathway();
					$pathway->addItem($cat[$name],'index.php?option='.$option.a.'gid='.$id.a.'active='.$name);
				}*/
				$html .= t.t.'<li id="sm-'.$i.'"';
				$html .= (strtolower($name) == $active) ? ' class="active"' : '';
				$html .= '><a class="tab" rel="'.$name.'" href="'.JRoute::_('index.php?option='.$option.a.'gid='.$id.a.'active='.$name).'"><span>'.$cat[$name].'</span></a></li>'.n;
				$i++;
			}
		}
		$html .= t.'</ul>'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</div><!-- / #sub-menu -->'.n;
		
		return $html;
	}
	
	//-----------
	
	public function overview( $sections, $cats, $option, $group, $uidNumber, $authorized, $ismember, $public_desc, $private_desc, $error ) 
	{
		if ($authorized != 'admin' && $authorized !='manager' && $authorized != 'member') {
			$registered = false;
		} else {
			$registered = true;
		}
		
		$isApplicant = $group->isApplicant($uidNumber);

		// Get the group tags
		$database =& JFactory::getDBO();
		$gt = new GroupsTags( $database );
		$tags = $gt->get_tag_cloud(0,0,$group->get('gidNumber'));
		if (!$tags) {
			$tags = JText::_('None');
		}
		
		// Get the managers
		$managers = $group->get('managers');
		$m = array();
		if ($managers) {
			foreach ($managers as $manager) 
			{
				$person =& JUser::getInstance($manager);
				$m[] = '<a href="'.JRoute::_('index.php?option=com_members&id='.$manager).'" rel="member">'.stripslashes($person->get('name')).'</a>';
			}
		}
		$m = implode(', ', $m);
		
		// Determine the join policy
		switch ($group->get('join_policy')) 
		{
			case 3: $policy = JText::_('Closed');      break;
			case 2: $policy = JText::_('Invite Only'); break;
			case 1: $policy = JText::_('Restricted');  break;
			case 0:
			default: $policy = JText::_('Open'); break;
		}
		
		// Determine the privacy
		switch ($group->get('privacy')) 
		{
			case 1: $privacy = JText::_('Protected'); break;
			case 4: $privacy = JText::_('Private');   break;
			case 0:
			default: $privacy = JText::_('Public'); break;
		}
		
		// Get the group creation date
		$gl = new XGroupLog( $database );
		$gl->getLog( $group->get('gidNumber'), 'first' );
		
		if ($isApplicant) {
			$cls = 'pending';
		} else {
			$cls = $ismember;
		}
		
		$aside = '';
		
		switch ($group->get('join_policy')) 
		{
			case 3:
				if ($isApplicant || $ismember) {
					if ($ismember == 'invitee') {
						$aside .= '<p id="primary-document"><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=accept').'">'.JText::_('GROUPS_ACCEPT_INVITE').'</a></p>'.n;
						//$aside .= '<p><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=cancel') .'">'.JText::_('GROUPS_ACTION_CANCEL_MEMBERSHIP').'</a></p>'.n;
					} else {
						$aside .= '<p id="group-status" class="'.$cls.'"><span>You are a </span>'.$cls.'</p>'.n;
						if ($ismember == 'manager' && count($group->get('managers')) == 1) {
							$aside .= '';
						} else {
							$aside .= '<p id="group-cancel" class="'.$cls.'"><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=cancel') .'">'.JText::_('GROUPS_ACTION_CANCEL_MEMBERSHIP').'</a></p>'.n;
						}
					}
				}
			break;
			
			case 2:
				if ($ismember == 'invitee') {
					$aside .= '<p id="primary-document"><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=accept').'">'.JText::_('GROUPS_ACCEPT_INVITE').'</a></p>'.n;
					//$aside .= '<p><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=cancel') .'">'.JText::_('GROUPS_ACTION_CANCEL_MEMBERSHIP').'</a></p>'.n;
				} else {
					if ($isApplicant || $ismember == 'manager' || $ismember == 'member') {
						$aside .= '<p id="group-status" class="'.$cls.'"><span>You are a </span>'.$cls.'</p>'.n;
						if ($ismember == 'manager' && count($group->get('managers')) == 1) {
							$aside .= '';
						} else {
							$aside .= '<p id="group-cancel" class="'.$cls.'"><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=cancel') .'">'.JText::_('GROUPS_ACTION_CANCEL_MEMBERSHIP').'</a></p>'.n;
						}
					}
				}
			break;
			
			case 1:
				if ($isApplicant || $ismember) {
					if ($ismember == 'invitee') {
						$aside .= '<p id="primary-document"><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=accept').'">'.JText::_('GROUPS_ACCEPT_INVITE').'</a></p>'.n;
						//$aside .= '<p><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=cancel') .'">'.JText::_('GROUPS_ACTION_CANCEL_MEMBERSHIP').'</a></p>'.n;
					} else {
						$aside .= '<p id="group-status" class="'.$cls.'"><span>You are a </span>'.$cls.'</p>'.n;
						if ($ismember == 'manager' && count($group->get('managers')) == 1) {
							$aside .= ''.n;
						} else {
							$aside .= '<p id="group-cancel" class="'.$cls.'"><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=cancel') .'">'.JText::_('GROUPS_ACTION_CANCEL_MEMBERSHIP').'</a></p>'.n;
						}
					}
				} else {
					$aside .= '<p id="primary-document"><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=join').'">'.JText::_('GROUPS_REQUEST_MEMBERSHIP_TO_GROUP').'</a></p>'.n;
				}
			break;
			
			case 0:
			default:
				if ($isApplicant || $ismember) {
					if ($ismember == 'invitee') {
						$aside .= '<p id="primary-document"><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=accept').'">'.JText::_('GROUPS_ACCEPT_INVITE').'</a></p>'.n;
					} else {
						$aside .= '<p id="group-status" class="'.$cls.'"><span>You are a </span>'.$cls.'</p>'.n;
						if ($ismember == 'manager' && count($group->get('managers')) == 1) {
							$aside .= ''.n;
						} else {
							$aside .= '<p id="group-cancel" class="'.$cls.'"><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=cancel') .'">'.JText::_('GROUPS_ACTION_CANCEL_MEMBERSHIP').'</a></p>'.n;
						}
					}
				} else {
					$aside .= '<p id="primary-document"><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=join').'">'.JText::_('GROUPS_JOIN_GROUP').'</a></p>'.n;
				}
			break;
		}
		
		$aside .= '<div class="metadata">';
		$aside .= '<table><tbody><tr><th>Managers:</th><td>'.$m.'</td></tr>';
		$aside .= '<tr><th>Members:</th><td>'.count($group->get('members')).'</td></tr>';
		$aside .= '<tr><th>Access:</th><td>'.$privacy.'</td></tr>';
		$aside .= '<tr><th>Join Policy:</th><td>'.$policy.'</td></tr>';
		$aside .= '<tr><th>Created:</th><td>'.JHTML::_('date', $gl->timestamp, '%d %b. %Y').'</td></tr>';
		$aside .= '<tr><th>Tags:</th><td>'.$tags.'</td></tr></tbody></table>';
		$aside .= '</div>';
		
		if ($authorized == 'admin' || $authorized == 'manager') {
			$aside .= '<div class="admin-options">'.n;
			$aside .= t.'<p class="edit"><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=edit').'">'.JText::_('GROUPS_EDIT_GROUP').'</a></p>';
			$aside .= t.'<p class="delete"><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=delete').'">'.JText::_('GROUPS_DELETE_GROUP').'</a></p>';
			$aside .= t.'<p class="invite"><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=invite').'">'.JText::_('GROUPS_INVITE_USERS').'</a></p>'.n;
			$aside .= '</div>'.n;
		}
		
		$text = '';
		if ($error) {
			$text .= GroupsHtml::error( $error );
		}
		
		if ($authorized == 'admin' 
		 || $authorized == 'manager' 
		 || $authorized == 'member') {
			if ($private_desc) {
				$text .= '<div class="dashboard" id="private-text">'.n;
				$text .= '<h4 class="dash-header">'.JText::_('GROUPS_PRIVATE_TEXT').'</h4>'.n;
				$text .= $private_desc;
				$text .= '</div>'.n;
			} else if ($public_desc) {
				$text .= '<div class="dashboard">'.n;
				$text .= '<h4 class="dash-header">'.JText::_('GROUPS_ABOUT').'</h4>'.n;
				$text .= $public_desc;
				$text .= '</div>'.n;
			}
		} else {
			if ($public_desc) {
				$text .= '<div class="dashboard">'.n;
				$text .= '<h4 class="dash-header">'.JText::_('GROUPS_ABOUT').'</h4>'.n;
				$text .= $public_desc;
				$text .= '</div>'.n;
			}
		}
		
		//if ($registered) {
			$text .= GroupsHtml::metadata( $sections, $cats, $option, $group->get('cn') );
		//}
		
		$html  = GroupsHtml::hed(3,'<a name="overview"></a>'.JText::_('GROUPS_OVERVIEW')).n;
		$html .= GroupsHtml::aside(
					$aside
				);
		$html .= GroupsHtml::subject( $text );
		
		return $html;
	}
	
	//-----------
	
	public function view( $group, $authorized, $option, $cats, $sections, $tab ) 
	{
		$html  = GroupsHtml::div( GroupsHtml::hed(2,$group->get('description')), '', 'content-header' ).n;
		$html .= '<div id="content-header-extra">'.n;
		$html .= t.'<ul id="useroptions">'.n;
		$html .= t.t.'<li class="last"><a class="group" href="'.JRoute::_('index.php?option='.$option.a.'task=browse').'">'.JText::_('GROUPS_ALL_GROUPS').'</a></li>'.n;
		$html .= t.'</ul>'.n;
		$html .= '</div><!-- / #content-header-extra -->'.n;
		$html .= GroupsHtml::tabs( $option, $group->get('cn'), $cats, $tab );
		$html .= GroupsHtml::sections( $sections, $cats, $tab, 'hide', 'main' );
		
		return $html;
	}

	//-----------
	
	public function join($option, $title, $group)
	{
		$msg = $group->get('restrict_msg');
		
		$html  = GroupsHtml::div( GroupsHtml::hed( 2, $title ), '', 'content-header' ).n;
		$html .= '<div id="content-header-extra">'.n;
		$html .= t.'<ul id="useroptions">';
		$html .= t.t.'<li class="last"><a class="group" href="'.JRoute::_('index.php?option='.$option).'">'.JText::_('GROUPS_ALL_GROUPS').'</a></li>';
		$html .= t.'</ul>';
		$html .= '</div><!-- / #content-header-extra -->'.n;
		$html .= '<div class="main section">'.n;
		$html .= '<form action="index.php" method="post" id="hubForm">'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p class="info">'.JText::_('GROUPS_JOIN_EXPLANATION').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;		
		$html .= t.t.'<h3>'.JText::_('GROUPS_JOIN_HEADER').'</h3>'.n;
		if ($msg) {
			$html .= GroupsHtml::warning( JText::_('NOTE').': '.htmlentities(stripslashes($msg)) );
		}
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('GROUPS_JOIN_REASON').':'.n;
		$html .= t.t.t.'<textarea name="reason" id="reason" rows="10" cols="50"></textarea>'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<input type="hidden" name="gid" value="'.$group->get('cn').'" />'.n;
		$html .= t.t.'<input type="hidden" name="task" value="confirm" />'.n;
		$html .= t.t.'<input type="hidden" name="option" value="'.$option.'" />'.n;
		$html .= t.'</fieldset><div class="clear"></div>'.n;
		$html .= t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
		$html .= '</form>'.n;
		$html .= '</div><!-- / .main section -->'.n;
		
		return $html;
	}
	
	//-------------------------------------------------------------
	//  Other views
	//-------------------------------------------------------------

	public function invite($option, $title, $group, $msg, $return='view', $err='')
	{
		$html  = GroupsHtml::div( GroupsHtml::hed( 2, $title ), '', 'content-header' ).n;
		$html .= '<div id="content-header-extra">'.n;
		$html .= t.'<ul id="useroptions">'.n;
		$html .= t.t.'<li class="last"><a class="group" href="'.JRoute::_('index.php?option='.$option).'">'.JText::_('GROUPS_ALL_GROUPS').'</a></li>'.n;
		$html .= t.'</ul>'.n;
		$html .= '</div><!-- / #content-header-extra -->'.n;
		
		$html .= ($err) ? GroupsHtml::error( $err ) : '';
		
		$html .= '<div class="main section">'.n;
		$html .= '<form action="index.php" method="post" id="hubForm">'.n;
		$html .= t.'<div class="explaination">'.n;
		//$html .= t.t.'<p>'.JText::_('Group deletion is permanent.').'</p>'.n;
		$html .= t.t.'<div class="admin-options">'.n;
		$html .= t.t.t.'<p><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=view').'">'.JText::_('GROUPS_VIEW_GROUP').'</a></p>'.n;
		$html .= t.t.t.'<p><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=edit').'">'.JText::_('GROUPS_EDIT_GROUP').'</a></p>'.n;
		$html .= t.t.t.'<p><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=delete').'">'.JText::_('GROUPS_DELETE_GROUP').'</a></p>'.n;
		$html .= t.t.'</div>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.GroupsHtml::hed( 3, JText::_('GROUPS_INVITE_HEADER') );
		$html .= t.t.'<p>'.JText::sprintf('GROUPS_INVITE_EXPLANATION',$group->get('description')).'</p>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('GROUPS_INVITE_LOGINS').':'.n;
		$html .= t.t.t.'<input type="text" name="logins" size="35" value="" />'.n;
		$html .= t.t.t.'<span class="hint">Enter logins or e-mails separated by commas</span>'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('GROUPS_INVITE_MESSAGE').':'.n;
		$html .= t.t.t.'<textarea name="msg" id="msg" rows="12" cols="50">'.htmlentities(stripslashes($msg)).'</textarea>'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.'</fieldset><div class="clear"></div>'.n;
		$html .= t.'<input type="hidden" name="gid" value="'.$group->get('cn').'" />'.n;
		$html .= t.'<input type="hidden" name="task" value="invite" />'.n;
		$html .= t.'<input type="hidden" name="process" value="1" />'.n;
		$html .= t.'<input type="hidden" name="option" value="'.$option.'" />'.n;
		$html .= t.'<input type="hidden" name="return" value="'.$return.'" />'.n;
		
		$html .= t.'<p class="submit"><input type="submit" value="'.JText::_('INVITE').'" /></p>'.n;
		$html .= '</form>'.n;
		$html .= '</div><!-- / .main section -->'.n;
		
		return $html;
	}
	
	//-----------

	public function delete($option, $title, $gid, $group, $log, $msg='', $err='')
	{
		$warn  = t.JText::sprintf('GROUPS_DELETE_WARNING',$group->get('description')).n;
		$warn .= t.'<br /><br />'.$log.n;

		$html  = GroupsHtml::div( GroupsHtml::hed( 2, $title ), '', 'content-header' ).n;
		$html .= '<div id="content-header-extra">'.n;
		$html .= t.'<ul id="useroptions">'.n;
		$html .= t.t.'<li class="last"><a class="group" href="'.JRoute::_('index.php?option='.$option).'">'.JText::_('GROUPS_ALL_GROUPS').'</a></li>'.n;
		$html .= t.'</ul>'.n;
		$html .= '</div><!-- / #content-header-extra -->'.n;
		
		$html .= ($err) ? GroupsHtml::error( $err ) : '';
		
		$html .= '<form action="index.php" method="post" id="hubForm">'.n;
		$html .= t.'<div class="explaination">'.n;
		//$html .= t.t.'<p>'.JText::_('Group deletion is permanent.').'</p>'.n;
		$html .= t.t.'<div class="admin-options">'.n;
		$html .= t.t.t.'<p><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$gid.a.'task=view').'">'.JText::_('GROUPS_VIEW_GROUP').'</a></p>'.n;
		$html .= t.t.t.'<p><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$gid.a.'task=edit').'">'.JText::_('GROUPS_EDIT_GROUP').'</a></p>'.n;
		$html .= t.t.t.'<p><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$gid.a.'task=invite').'">'.JText::_('GROUPS_INVITE_USERS').'</a></p>'.n;
		$html .= t.t.'</div>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.GroupsHtml::hed( 3, JText::_('GROUPS_DELETE_HEADER') );
		
		$html .= GroupsHtml::warning( $warn ).n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('GROUPS_DELETE_MESSAGE').':'.n;
		$html .= t.t.t.'<textarea name="msg" id="msg" rows="12" cols="50">'.htmlentities($msg).'</textarea>'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'<input type="checkbox" class="option" name="confirmdel" value="1" /> '.n;
		$html .= t.t.t.JText::_('GROUPS_DELETE_CONFIRM').n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.'</fieldset><div class="clear"></div>'.n;
		
		$html .= t.'<input type="hidden" name="gid" value="'.$gid.'" />'.n;
		$html .= t.'<input type="hidden" name="task" value="delete" />'.n;
		$html .= t.'<input type="hidden" name="process" value="1" />'.n;
		$html .= t.'<input type="hidden" name="option" value="'.$option.'" />'.n;
		
		$html .= t.'<p class="submit"><input type="submit" value="'.JText::_('DELETE').'" /></p>'.n;
		$html .= '</form>'.n;
		
		return $html;
	}
	
	
	//-----------
	
	public function browse( $option, $groups, $authorized, $title, $pageNav, $filters, $err='' )
	{
		$maxtextlen = 42;
		$row = 1;
		$juser =& JFactory::getUser();
		
		$html  = GroupsHtml::div( GroupsHtml::hed( 2, $title ), '', 'content-header' ).n;
		$html .= '<div id="content-header-extra">'.n;
		$html .= t.'<ul id="useroptions">'.n;
		$html .= t.t.'<li class="last"><a class="group" href="'.JRoute::_('index.php?option='.$option.a.'task=new').'">'.JText::_('GROUPS_CREATE_GROUP').'</a></li>'.n;
		$html .= t.'</ul>'.n;
		$html .= '</div><!-- / #content-header-extra -->'.n;
		
		if ($err) {
			$html .= GroupsHtml::error( $err );
		}
		
		$html .= '<form action="'.JRoute::_('index.php?option='.$option.a.'task=browse').'" method="get">';
		$html .= '<div class="main section">'.n;
		$html .= t.'<div class="aside">'.n;
		$html .= t.t.'<fieldset>'.n;
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.t.JText::_('SORT_BY').':'.n;
		$html .= t.t.t.t.'<select name="sortby">'.n;
		$html .= t.t.t.t.t.'<option value="description ASC"';
		if ($filters['sortby'] == 'description ASC') {
			$html .= ' selected="selected"';
		}
		$html .= '>'.JText::_('Title').'</option>'.n;
		$html .= t.t.t.t.t.'<option value="cn ASC"';
		if ($filters['sortby'] == 'cn ASC') {
			$html .= ' selected="selected"';
		}
		$html .= '>'.JText::_('Alias').'</option>'.n;
		$html .= t.t.t.t.'</select>'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.t.JText::_('GROUPS_SEARCH').n;
		$html .= t.t.t.t.'<input type="text" name="search" value="'. $filters['search'] .'" />'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
		//$html .= t.t.'<input type="hidden" name="option" value="'.$option.'" />'.n;
		$html .= t.t.'</fieldset>'.n;
		//$html .= t.t.'<p>'.JText::_('GROUPS_EXPLANATION').'</p>'.n;
		//$html .= t.t.'<h4>'.JText::_('GROUPS_HOW_TO_JOIN').'</h4>'.n;
		//$html .= t.t.'<p>'.JText::_('GROUPS_HOW_TO_JOIN_EXPLANATION').'</p>'.n;
		$html .= t.'</div>'.n;
		
		$html .= t.'<div class="subject">'.n;
		
		$qs = array();
		foreach ($filters as $f=>$v) 
		{
			$qs[] = ($v != '' && $f != 'index' && $f != 'authorized' && $f != 'type' && $f != 'fields') ? $f.'='.$v : '';
		}
		$qs[] = 'limitstart=0';
		$qs = implode(a,$qs);
		
		$letters = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

		$html .= '<p id="letter-index">'.n;
		$url  = 'index.php?option='.$option.'&task=browse';
		$url .= ($qs) ? '&'.$qs : '';
		$html .= t.'<a href="'.JRoute::_($url).'"';
		if ($filters['index'] == '') {
			$html .= ' class="active-index"';
		}
		$html .= '>'.JText::_('ALL').'</a> '.n;
		foreach ($letters as $letter)
		{
			$url  = 'index.php?option='.$option.'&task=browse&index='.strtolower($letter);
			$url .= ($qs) ? a.$qs : '';
			
			$html .= t.'<a href="'.JRoute::_($url).'"';
			if ($filters['index'] == strtolower($letter)) {
				$html .= ' class="active-index"';
			}
			$html .= '>'.$letter.'</a> '.n;
		}
		$html .= '</p>'.n;
		
		$html .= t.t.'<table id="grouplist" summary="'.JText::_('GROUPS_BROWSE_TBL_SUMMARY').'">'.n;
		$html .= t.t.t.'<thead>'.n;
		$html .= t.t.t.t.'<tr>'.n;
		$html .= t.t.t.t.t.'<th>'.JText::_('Title').'</th>'.n;
		//$html .= t.t.t.t.t.'<th>'.JText::_('Alias').'</th>'.n;
		$html .= t.t.t.t.t.'<th>'.JText::_('Join Policy').'</th>'.n;
		if ($authorized) {
			$html .= t.t.t.t.t.'<th>'.JText::_('Status').'</th>'.n;
			$html .= t.t.t.t.t.'<th>'.JText::_('Options').'</th>'.n;
		} else {
			$html .= t.t.t.t.t.'<th> </th>'.n;
			$html .= t.t.t.t.t.'<th> </th>'.n;
		}
		$html .= t.t.t.t.'</tr>'.n;
		$html .= t.t.t.'</thead>'.n;
		$html .= t.t.t.'<tbody>'.n;
		if ($groups) {
			foreach ($groups as $group) 
			{
				$cls = ($row%2) ? ' class="odd"' : ' class="even"';

				// Only display if the group is registered
				if (isset($group->published) && $group->published) {
					// Determine the join policy
					switch ($group->join_policy) 
					{
						case 3: $policy = '<span class="closed join-policy">'.JText::_('Closed').'</span>';      break;
						case 2: $policy = '<span class="inviteonly join-policy">'.JText::_('Invite Only').'</span>'; break;
						case 1: $policy = '<span class="restricted join-policy">'.JText::_('Restricted').'</span>';  break;
						case 0:
						default: $policy = '<span class="open join-policy">'.JText::_('Open').'</span>'; break;
					}
					
					$html .= t.t.t.t.'<tr'.$cls.'>'.n;
					$html .= t.t.t.t.t.'<td><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->cn) .'"';
					if (trim($group->public_desc) != '') {
						//$html .= '<br /><span class="description">'.GroupsHtml::shortenText(stripslashes($group->public_desc),100,0).'</span>';
						$html .= ' class="tooltips" title="Description :: '.GroupsHtml::shortenText(stripslashes($group->public_desc),100,0).'"';
					}
					$html .= '>'. htmlentities($group->description) .'</a></td>'.n;
					//$html .= t.t.t.t.t.'<td>'.$group->cn.'</td>'.n;
					$html .= t.t.t.t.t.'<td>'.$policy.'</td>'.n;
					if ($authorized) {
						$html .= t.t.t.t.t.'<td>';
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
										$opt = '<a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->cn.a.'task=cancel'.a.'return=browse') .'">'.JText::_('GROUPS_ACTION_CANCEL').'</a>';
									} else {
										$html .= '<span class="pending status">'.JText::_('GROUPS_STATUS_PENDING').'</span>';
										$opt = '<a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->cn.a.'task=cancel'.a.'return=browse') .'">'.JText::_('GROUPS_ACTION_CANCEL').'</a>';
									}
								} else {
									if ($group->regconfirmed) {
										$html .= '<span class="invitee status">'.JText::_('GROUPS_STATUS_INVITED').'</span>';
										$opt  = '<a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->cn.a.'task=accept'.a.'return=browse') .'">'.JText::_('GROUPS_ACTION_ACCEPT').'</a>';
										$opt .= ' <a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->cn.a.'task=cancel'.a.'return=browse') .'">'.JText::_('GROUPS_ACTION_CANCEL').'</a>';
									} else {
										$html .= '<span class="status"> </span>';
										$opt = '';
									}
								}
							}
						}
						$html .= '</td>'.n;
						$html .= t.t.t.t.t.'<td>'.$opt.'</td>'.n;
					} else {
						$html .= t.t.t.t.t.'<td>&nbsp;</td>'.n;
						$html .= t.t.t.t.t.'<td>&nbsp;</td>'.n;
					}
					$html .= t.t.t.t.'</tr>'.n;

					$row++;
				} else {
					if (isset($group->cn) && $group->cn) {
						$html .= t.t.t.t.'<tr'.$cls.'>'.n;
						$html .= t.t.t.t.t.'<td><a href="'.JRoute::_('index.php?option='.$option.a.'gid='. $group->cn) .'">'. htmlentities($group->description) .'</a></td>'.n;
						$html .= t.t.t.t.t.'<td>&nbsp;</td>'.n;
						if ($authorized) {
							$html .= t.t.t.t.t.'<td>&nbsp;</td>'.n;
							$html .= t.t.t.t.t.'<td>&nbsp;</td>'.n;
						}
						$html .= t.t.t.t.'</tr>'.n;
						$row++;
					}
				}
			}
		}
		if ($row == 1) {
			$html .= t.t.t.t.'<tr class="odd">'.n;
			$html .= t.t.t.t.t.'<td colspan="4">'.JText::_('NONE').'</td>'.n;
			$html .= t.t.t.t.'</tr>'.n;
		}
		$html .= t.t.t.'</tbody>'.n;
		$html .= t.t.'</table>'.n;
		//$html .= t.t.$pageNav->getListFooter();
		$pn = $pageNav->getListFooter();
		$pn = str_replace('/?','/browse/?',$pn);
		$html .= $pn;
		$html .= t.'</div><!-- / .subject -->'.n;
		$html .= '</div><!-- / .main section --><div class="clear"></div>'.n;
		$html .= '</form>'.n;
		
		return $html;
	}

	//-----------
	
	public function edit( $option, $group, $task, $title, $tags, $errors=array() ) 
	{
		$html  = GroupsHtml::div( GroupsHtml::hed( 2, $title ), '', 'content-header' ).n;
		$html .= '<div id="content-header-extra">'.n;
		$html .= t.'<ul id="useroptions">'.n;
		/*if ($task != 'new') {
			$html .= t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=view').'">View group page</a></li>'.n;
			$html .= t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'task=delete').'">Delete group</a></li>'.n;
			$html .= t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'gid='.$group->get('cn').a.'active=manage').'">Manage Members</a></li>'.n;
		}*/
		$html .= t.t.'<li class="last"><a class="group" href="'.JRoute::_('index.php?option='.$option).'">'.JText::_('GROUPS_ALL_GROUPS').'</a></li>'.n;
		$html .= t.'</ul>'.n;
		$html .= '</div><!-- / #content-header-extra -->'.n;
		
		$html .= '<div class="main section">'.n;
		$html .= '<form action="index.php" method="post" id="hubForm">'.n;

		if ($task != 'new' && !$group->get('published')) {
			$html .= t.t.GroupsHtml::warning( JText::_('GROUPS_STATUS_NEW_GROUP') ).n;
		}
		
		if (!empty($errors)) {
			$html .= GroupsHtml::error( implode(n,$errors) );
		}

		$html .= t.'<div class="explaination">'.n;
		//$html .= t.t.'<p>'.JText::_('GROUPS_ACCESS_EXPLANATION').'</p>'.n;
		/*$html .= t.t.'<dl><dt>'.JText::_('GROUPS_ACCESS_PUBLIC').'</dt>'.n;
		$html .= t.t.'<dd>'.JText::_('GROUPS_ACCESS_PUBLIC_EXPLANATION').'</dd>'.n;
		$html .= t.t.'<dt>'.JText::_('GROUPS_ACCESS_PROTECTED').'</dt>'.n;
		$html .= t.t.'<dd>'.JText::_('GROUPS_ACCESS_PROTECTED_EXPLANATION').'</p>'.n;
		$html .= t.t.'<dt>'.JText::_('GROUPS_ACCESS_PRIVATE').'</dt>'.n;
		$html .= t.t.'<dd>'.JText::_('GROUPS_ACCESS_PRIVATE_EXPLANATION').'</dd></dl>'.n;*/
		//$html .= t.t.'<p>'.JText::_('GROUPS_EDIT_DESCRIPTION_EXPLANATION').'</p>';
		$html .= t.t.'<p>'.JText::_('Upload files or images to use in your description:').'</p>';
		if ($group->get('gidNumber')) {
			$lid = $group->get('gidNumber');
		} else {
			$lid = time().rand(0,10000);
		}
		$html .= t.t.'<iframe width="100%" height="370" name="filer" id="filer" src="index.php?option='.$option.a.'no_html=1'.a.'task=media'.a.'listdir='.$lid.'"></iframe>'.n;
		$html .= t.'</div>'.n;
		
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<h3>'.JText::_('GROUPS_EDIT_DETAILS').'</h3>'.n;
		if ($task != 'new') {
			$html .= t.t.t.'<input name="cn" type="hidden" value="'. $group->get('cn') .'" />'.n;
		} else {
			$html .= t.t.'<label';
			$html .= ($task == 'save' && !$group->get('cn')) 
			   	? ' class="fieldWithErrors"' 
			   	: '';
			$html .= '>'.n;
			$html .= t.t.t.JText::_('GROUPS_ID').': <span class="required">'.JText::_('GROUPS_REQUIRED').'</span>'.n;
			$html .= t.t.t.'<input name="cn" type="text" size="35" value="'. $group->get('cn') .'" /> <span class="hint">'.JText::_('GROUPS_ID_HINT').'</span>'.n;
			$html .= t.t.'</label>'.n;
		}
		$html .= ($task == 'save' && !$group->get('cn')) 
			   ? GroupsHtml::error( JText::_('GROUPS_ERROR_PROVIDE_ID') )
			   : '';
		$html .= ($task == 'save' && $group->get('cn') && !XGroupHelper::valid_cn($group->get('cn'))) 
			   ? GroupsHtml::error( JText::_('GROUPS_ERROR_INVALID_ID') )
			   : '';

		$html .= t.t.'<label';
		$html .= ($task == 'save' && !$group->get('description')) 
			   ? ' class="fieldWithErrors"' 
			   : '';
		$html .= '>'.n;
		$html .= t.t.t.JText::_('GROUPS_TITLE').': <span class="required">'.JText::_('GROUPS_REQUIRED').'</span>'.n;
		$html .= t.t.t.'<input type="text" name="description" size="35" value="'. htmlentities(stripslashes($group->get('description'))) .'" />'.n;
		$html .= t.t.'</label>'.n;
		$html .= ($task == 'save' && !$group->get('description')) 
			   ? GroupsHtml::error( JText::_('GROUPS_ERROR_PROVIDE_TITLE') )
			   : '';
		
		JPluginHelper::importPlugin( 'tageditor' );
		$dispatcher =& JDispatcher::getInstance();
		$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tags','actags','',$tags,'')) );

		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('GROUPS_FIELD_TAGS').': <span class="optional">'.JText::_('GROUPS_OPTIONAL').'</span>'.n;
		if (count($tf) > 0) {
			$html .= $tf[0];
		} else {
			$html .= t.t.t.'<input type="text" name="tags" value="'. $tags .'" />'.n;
		}
		$html .= t.t.t.'<span class="hint">'.JText::_('GROUPS_FIELD_TAGS_HINT').'</span>'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('GROUPS_EDIT_PUBLIC_TEXT').': <span class="optional">'.JText::_('GROUPS_OPTIONAL').'</span>'.n;
		$html .= t.t.t.'<textarea name="public_desc" rows="15" cols="50">'. htmlentities(stripslashes($group->get('public_desc'))) .'</textarea>'.n;
		$html .= t.t.t.'<span class="hint"><a href="'.JRoute::_('index.php?option=com_topics&scope=&pagename=Help:WikiFormatting').'">Wiki formatting</a> is allowed.</span>'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('GROUPS_EDIT_PRIVATE_TEXT').': <span class="optional">'.JText::_('GROUPS_OPTIONAL').'</span>'.n;
		$html .= t.t.t.'<textarea name="private_desc" rows="15" cols="50">'. htmlentities(stripslashes($group->get('private_desc'))) .'</textarea>'.n;
		$html .= t.t.t.'<span class="hint"><a href="'.JRoute::_('index.php?option=com_topics&scope=&pagename=Help:WikiFormatting').'">Wiki formatting</a> is allowed.</span>'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.'</fieldset><div class="clear"></div>'.n;
		
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('GROUPS_EDIT_CREDENTIALS_EXPLANATION').'</p>';
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<h3>'.JText::_('GROUPS_EDIT_MEMBERSHIP').'</h3>'.n;
		/*$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('GROUPS_EDIT_CREDENTIALS').': <span class="optional">'.JText::_('GROUPS_OPTIONAL').'</span>'.n;
		$html .= t.t.t.'<textarea name="restrict_msg" rows="10" cols="50">'. htmlentities(stripslashes($group->get('restrict_msg'))) .'</textarea>'.n;
		$html .= t.t.'</label>'.n;*/
		$html .= t.t.'<fieldset>'.n;
		$html .= t.t.t.'<legend>'.JText::_('Who can join?').' <span class="required">'.JText::_('GROUPS_REQUIRED').'</span></legend>'.n;
		$html .= t.t.t.'<label><input type="radio" class="option" name="join_policy" value="0"';
		if ($group->get('join_policy') == 0) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> <strong>'.JText::_('Anyone').'</strong> <span class="indent">Membership requests are automatically accepted (no pending status).</span></label>'.n;
		$html .= t.t.t.'<label><input type="radio" class="option" name="join_policy" value="1"';
		if ($group->get('join_policy') == 1) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> <strong>'.JText::_('Restricted').'</strong> <span class="indent">Membership requests are pending and must be approved/denied by a manager.</span></label>'.n;
		$html .= t.t.'<label class="indent">'.n;
		$html .= t.t.t.JText::_('GROUPS_EDIT_CREDENTIALS').': <span class="optional">'.JText::_('GROUPS_OPTIONAL').'</span>'.n;
		$html .= t.t.t.'<textarea name="restrict_msg" rows="5" cols="50">'. htmlentities(stripslashes($group->get('restrict_msg'))) .'</textarea>'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.t.t.'<label><input type="radio" class="option" name="join_policy" value="2"';
		if ($group->get('join_policy') == 2) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> <strong>'.JText::_('Invite Only').'</strong> <span class="indent">Membership can only be gained through an invite.</span></label>'.n;
		$html .= t.t.t.'<label><input type="radio" class="option" name="join_policy" value="3"';
		if ($group->get('join_policy') == 3) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> <strong>'.JText::_('Closed').'</strong> <span class="indent">Membership cannot be requested.</span></label>'.n;
		$html .= t.t.'</fieldset>'.n;
		$html .= t.'</fieldset>'.n;
		$html .= t.'<div class="clear"></div>'.n;

		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('GROUPS_ACCESS_EXPLANATION').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<h3>'.JText::_('Access Settings').'</h3>'.n;
		$html .= t.t.'<fieldset>'.n;
		$html .= t.t.t.'<legend>'.JText::_('GROUPS_PRIVACY').' <span class="required">'.JText::_('GROUPS_REQUIRED').'</span></legend>'.n;
		//$html .= t.t.t.'<p>'.JText::_('GROUPS_PRIVACY_HINT').'</p>'.n;
		$html .= t.t.t.'<label><input type="radio" class="option" name="privacy" value="0"';
		if ($group->get('privacy') == 0) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> <strong>'.JText::_('GROUPS_ACCESS_PUBLIC').'</strong> <span class="indent">Group is discoverable (searches, etc.) and viewable by anyone.</span></label>'.n;
		$html .= t.t.t.'<label><input type="radio" class="option" name="privacy" value="1"';
		if ($group->get('privacy') == 1) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> <strong>'.JText::_('GROUPS_ACCESS_PROTECTED').'</strong> <span class="indent">Group is discoverable (searches, etc.) and viewable by registered users only.</span></label>'.n;
		$html .= t.t.t.'<label><input type="radio" class="option" name="privacy" value="4"';
		if ($group->get('privacy') == 4) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> <strong>'.JText::_('GROUPS_ACCESS_PRIVATE').'</strong> <span class="indent">Group is not discoverable (searches, etc.) and viewable only by members.</span></label>'.n;
		$html .= t.t.'</fieldset>'.n;
		
		$html .= t.t.'<fieldset>'.n;
		$html .= t.t.t.'<legend>'.JText::_('Content Privacy').' <span class="required">'.JText::_('GROUPS_REQUIRED').'</span></legend>'.n;
		$html .= t.t.t.'<p>'.JText::_('GROUPS_PRIVACY_HINT').'</p>'.n;
		$html .= t.t.t.'<label><input type="radio" class="option" name="access" value="0"';
		if ($group->get('access') == 0) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> <strong>'.JText::_('GROUPS_ACCESS_PUBLIC').'</strong> <span class="indent">'.JText::_('GROUPS_ACCESS_PUBLIC_EXPLANATION').'</span></label>'.n;
		$html .= t.t.t.'<label><input type="radio" class="option" name="access" value="3"';
		if ($group->get('access') == 3) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> <strong>'.JText::_('GROUPS_ACCESS_PROTECTED').'</strong> <span class="indent">'.JText::_('GROUPS_ACCESS_PROTECTED_EXPLANATION').'</span></label>'.n;
		$html .= t.t.t.'<label><input type="radio" class="option" name="access" value="4"';
		if ($group->get('access') == 4) {
			$html .= ' checked="checked"';
		}
		$html .= ' /> <strong>'.JText::_('GROUPS_ACCESS_PRIVATE').'</strong> <span class="indent">'.JText::_('GROUPS_ACCESS_PRIVATE_EXPLANATION').'</span></label>'.n;
		$html .= t.t.'</fieldset>'.n;
		$html .= t.'</fieldset>'.n;
		$html .= t.'<div class="clear"></div>'.n;

		$html .= t.'<input type="hidden" name="lid" value="'. $lid .'" />'.n;
		$html .= t.'<input type="hidden" name="gidNumber" value="'. $group->get('gidNumber') .'" />'.n;
		$html .= t.'<input type="hidden" name="option" value="'.$option.'" />'.n;
		$html .= t.'<input type="hidden" name="task" value="save" />'.n;
		$html .= t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
		$html .= '</form>'.n;
		$html .= '</div><!-- / .main section -->'.n;
		
		return $html;
	}
	
	//-------------------------------------------------------------
	// Media manager functions
	//-------------------------------------------------------------
	
	public function attachTop( $option, $name ) 
	{
		// Get the app
		$app =& JFactory::getApplication();
		
		$html  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.n;
		$html .= '<html xmlns="http://www.w3.org/1999/xhtml">'.n;
		$html .= '<head>'.n;
		$html .= t.'<title>'.JText::_('GROUPS_FILE_MANAGER').'</title>'.n;
		$html .= t.'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.n;
		$html .= t.'<link rel="stylesheet" type="text/css" media="screen" href="/templates/'. $app->getTemplate() .'/css/main.css" />'.n;
		//$html .= t.'<link rel="stylesheet" type="text/css" media="screen" href="components/'.$option.'/'.$name.'.css" />'.n;
		if (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$option.DS.'groups.css')) {
			$html .= t.'<link rel="stylesheet" href="'.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$option.DS.'groups.css" type="text/css" />'.n;
		} else {
			$html .= t.'<link rel="stylesheet" href="'.DS.'components'.DS.$option.DS.'groups.css" type="text/css" />'.n;
		}
		$html .= '</head>'.n;
		$html .= '<body id="attachments">'.n;
		return $html;
	}
	
	//-----------
	
	public function media( &$config, $listdir, $option, $name, $error='' ) 
	{

		$html  = GroupsHtml::attachTop( $option, $name );
		$html .= GroupsHtml::hed(1, JText::_('GROUPS_FILE_MANAGER') ).n;
		$html .= '<form action="index.php" id="adminForm" method="post" enctype="multipart/form-data">'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<div id="themanager" class="manager">'.n;
		$html .= t.t.t.'<iframe src="index.php?option='. $option .a.'no_html=1'.a.'task=listfiles'.a.'listdir='. $listdir .'" name="imgManager" id="imgManager" width="98%" height="180"></iframe>'.n;
		$html .= t.t.'</div>'.n;
		$html .= t.'</fieldset>'.n;
		
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<p><input type="file" name="upload" id="upload" /></p>'.n;
		$html .= t.t.'<p><input type="submit" value="'.JText::_('UPLOAD').'" /></p>'.n;

		$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$html .= t.t.'<input type="hidden" name="listdir" id="listdir" value="'. $listdir .'" />'.n;
		$html .= t.t.'<input type="hidden" name="task" value="upload" />'.n;
		$html .= t.t.'<input type="hidden" name="no_html" value="1" />'.n;
		$html .= t.'</fieldset>'.n;
		$html .= '</form>'.n;
		if ($error) {
			$html .= GroupsHtml::error($error);
		}
		$html .= GroupsHtml::attachBottom();
		return $html;
	}
	
	//-----------
	
	public function attachBottom() 
	{
		$html  = '</body>'.n;
		$html .= '</html>'.n;
		return $html;
	}

	//-----------

	public function dir_name($dir)
	{
		$lastSlash = intval(strrpos($dir, DS));
		if ($lastSlash == strlen($dir)-1) {
			return substr($dir, 0, $lastSlash);
		} else {
			return dirname($dir);
		}
	}

	//-----------
	
	public function draw_no_results()
	{
		return '<p>'.JText::_('NO_FILES_FOUND').'</p>'.n;
	}

	//-----------

	public function draw_no_dir( $dir ) 
	{
		return GroupsHtml::error( JText::sprintf('ERROR_MISSING_DIRECTORY', $dir) ).n;
	}

	//-----------

	public function draw_table_header() 
	{
		$html  = t.t.'<form action="index.php" method="post" id="filelist">'.n;
		$html .= t.t.'<table>'.n;
		return $html;
	}

	//-----------

	public function draw_table_footer() 
	{
		$html  = t.t.'</table>'.n;
		$html .= t.t.'</form>'.n;
		return $html;
	}

	//-----------

	public function show_dir( $path, $dir, $listdir, $option) 
	{
		$num_files = WikiHtml::num_files( JPATH_ROOT.$path );

		if ($listdir == '/') {
			$listdir = '';
		}
		
		$html  = ' <tr>'.n;
		$html .= '  <td><img src="/components/'. $option .'/images/icons/folder.gif" alt="'. $dir .'" width="16" height="16" /></td>'.n;
		$html .= '  <td width="100%">'. $dir .'</td>'.n;
	    $html .= '  <td><a href="index.php?option='.$option.a.'task=deletefolder'.a.'folder='.$path.a.'listdir='.$listdir.a.'no_html=1" target="filer" onclick="return deleteFolder(\''. $dir .'\', '. $num_files .');" title="'.JText::_('DELETE').'"><img src="/components/'. $option .'/images/icons/trash.gif" width="15" height="15" alt="'.JText::_('DELETE').'" /></a></td>'.n;
		$html .= ' </tr>'.n;
		
		return $html;
	}

	//-----------

	public function show_doc($option, $doc, $listdir, $icon) 
	{
		$html  = ' <tr>'.n;
		$html .= '  <td><img src="'. $icon .'" alt="'. $doc .'" width="16" height="16" /></td>'.n;
		$html .= '  <td width="100%">'. $doc .'</td>'.n;
		$html .= '  <td><a href="index.php?option='.$option.a.'task=deletefile'.a.'file='.$doc.a.'listdir='.$listdir.a.'no_html=1" target="filer" onclick="return deleteImage(\''. $doc .'\');" title="'.JText::_('DELETE').'"><img src="/components/'. $option .'/images/icons/trash.gif" width="15" height="15" alt="'.JText::_('DELETE').'" /></a></td>'.n;
		$html .= ' </tr>'.n;
		
		return $html;
	}

	//-----------

	public function parse_size($size)
	{
		if ($size < 1024) {
			return $size.' bytes';
		} else if ($size >= 1024 && $size < 1024*1024) {
			return sprintf('%01.2f',$size/1024.0).' <abbr title="kilobytes">Kb</abbr>';
		} else {
			return sprintf('%01.2f',$size/(1024.0*1024)).' <abbr title="megabytes">Mb</abbr>';
		}
	}

	//-----------

	public function num_files($dir)
	{
		$total = 0;

		if (is_dir($dir)) {
			$d = @dir($dir);

			while (false !== ($entry = $d->read()))
			{
				if (substr($entry,0,1) != '.') {
					$total++;
				}
			}
			$d->close();
		}
		return $total;
	}
	
	//-----------
	
	public function imageStyle($listdir)
	{
		?>
		<script type="text/javascript">
		function updateDir()
		{
			var allPaths = window.top.document.forms[0].dirPath.options;
			for (i=0; i<allPaths.length; i++)
			{
				allPaths.item(i).selected = false;
				if ((allPaths.item(i).value)== '<?php if (strlen($listdir)>0) { echo $listdir ;} else { echo '/';}  ?>') {
					allPaths.item(i).selected = true;
				}
			}
		}

		function deleteImage(file)
		{
			if (confirm("Delete file \""+file+"\"?")) {
				return true;
			}

			return false;
		}
		
		function deleteFolder(folder, numFiles)
		{
			if (numFiles > 0) {
				alert('There are '+numFiles+' files/folders in "'+folder+'".\n\nPlease delete all files/folder in "'+folder+'" first.');
				return false;
			}
	
			if (confirm('Delete folder "'+folder+'"?')) {
				return true;
			}
	
			return false;
		}
		</script>
		<?php
	}
}
?>
