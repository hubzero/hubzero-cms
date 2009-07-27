<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
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
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class WhoisHtml
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
	
	public function hed( $level, $txt )
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}

	//-----------

	public function div($txt, $cls='', $id='')
	{
		$html  = '<div';
		$html .= ($cls) ? ' class="'.$cls.'"' : '';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= '>';
		$html .= ($txt != '') ? n.$txt.n : '';
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

	public function form( $option )
	{
	    $xhub =& XFactory::getHub();
		$hubName = $xhub->getCfg('hubShortName');

		$html  = WhoisHtml::div( WhoisHtml::hed(2,JText::_('Lookup User(s)')), 'full', 'content-header').n;
		$html .= '<div class="main section">'.n;

		$html .= '<form name="whoFrom" id="hubForm" action="'. JRoute::_('index.php?option='.$option) .'" method="post" enctype="multipart/form-data">'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= WhoisHtml::div( WhoisHtml::admin_links(), 'info');
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.t.WhoisHtml::hed(3,'Lookup').n;
		$html .= '<p>Enter the search parameters for the ' . $hubName . ' user(s) you wish to lookup.'.n;
		$html .= 'Searches are made against Common Name (cn), E-mail (mail), Login (uid), '.n;
		$html .= 'User ID (uidNumber), Email Confirmed (emailConfirmed), and Proxy UID Number '.n;
		$html .= '(proxyUidNumber) fields, as well as the logical search Proxy Confirmed '.n;
		$html .= '(proxyConfirmed) in the LDAP directory.  You may specify the field to search '.n;
		$html .= 'or allow the field to be chosen based on your input.  Wildcard characters '.n;
		$html .= '\'*\' and \'?\' may be used.  The operators \'&lt=\' and \'&gt=\' are '.n;
		$html .= 'replaced by \'-=\' and \'+=\', respectively, to avoid HTML problems.</p>'.n;
		$html .= '<p>Search is limited to first 100 results.</p>'.n;
		
		$html .= t.t.'<label>'.n;
		//$html .= t.t.t.'Lookup'.n;
		$html .= t.t.t.'<input type="text" name="query" size="30" />'.n;
		$html .= t.t.t.'<span class="hint">[ (cn|mail|uid|uidNumber|emailConfirmed|proxyConfirmed|proxyUidNumber) (=|-=|+=|!=) ] value [,...]</span>'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<input type="hidden" name="task" value="view" />'.n;
		$html .= t.'</fieldset>'.n;
		$html .= t.'<p class="submit"><input type="submit" value="Submit" /></p>'.n;
		$html .= '</form>'.n;
		
		//$html .= '</div>'.n;

		return $html;
	}
	
	//-----------

	public function altrow($row) 
	{
	    if ($row % 2 == 0) {
	        return('class="odd"');
	    } else {
	        return('class="even"');
	    }   
	}       

	//-----------

	public function list_matches($summaries, $option)
	{
		$row = 1;

		$html .= '<table summary="'.JText::_('WHOIS_TABLE_SUMMARY').'">'.n;
		$html .= t.'<thead>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th colspan="3">'.JText::_('WHOIS_MATCHES').'</th>'.n;
		$html .= t.t.'</tr>'.n;
		$html .= t.'</thead>'.n;
		$html .= t.'<tbody>'.n;
		for ($i = 0; $i < count($summaries); $i++) 
		{
			$html .= t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.'<td><a href="'.JRoute::_('index.php?option='.$option.a.'task=view'.a.'username='. $summaries[$i]['uid']).'">';
			$html .= $summaries[$i]['uid'] .'</a> (' . $summaries[$i]['uidNumber'] .') </td>'.n;
			$html .= t.t.t.'<td>'. $summaries[$i]['cn'] .'</td>'.n;
			$html .= t.t.t.'<td>'. $summaries[$i]['mail'] .'</td>'.n;
			$html .= t.t.'</tr>'.n;
		}
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;

		return $html;
	}
	
	//-----------

	public function admin_links()
	{
		$html  = WhoisHtml::hed(4,JText::_('WHOIS_ADMIN_OPTIONS')).n;
		$html .= '<p><a href="/hub/registration/proxycreate">'.JText::_('PROXY_CREATE_USER').'</a></p>';
		return $html;
	}
	
	//-----------
	
	public function viewaccount_markup($uid, $showdetails, $showgroups, $showcourses) 
	{
		$xhub =& XFactory::getHub();
		$hubName = $xhub->getCfg('hubShortName');

		$xuser =& XFactory::getUser();
		$juser =& JFactory::getUser();

		$row   = 1;
		$admin = false;
		$html  = false;

		$admin = $juser->authorize('com_members', 'manage');
		$self = ($xuser->get('login') == $uid);

		if (!$self && !$admin) {
			$html  = "<h2>Access Denied</h2>\n";
			$html .= "<p>You are not allowed to access information about other users.</p>\n";
			return $html;
		}
		if (!$admin) {
			$showdetails = false;
		}

		if (!$self) {
			$xuser =& XUser::getInstance($uid);
		}

		if ($juser->get('guest')) {
			$html  = "<h2>Invalid Login</h2>\n";
			$html .= "<p>To access account information, you must provide a valid login.</p>\n";
			return $html;
		}

		if (($xuser->get('email_confirmed') != 1) && ($xuser->get('email_confirmed') != 3)) {
			if ($self)
				$html .= "\t<p>Your account will not be activated until you confirm receipt of email at the address listed below.</p>\n";
			else
				$html .= "\t<p>This account will not be activated until they confirm receipt of email at the address listed below.</p>\n";
		}
		$html .= "<br />\n";

		$html .= t.'<table summary="Account Details">'.n;
		$html .= t.t.'<caption>Account Details</caption>'.n;
		/*$html .= "\t".' <tfoot>'.n;
		$html .= t.t.t.'<tr>'.n;
		$html .= t.t.t.t.'<th colspan="2" class="aRight"><a href="'. JRoute::_($url['user_edit']);
		if($self) {
			$html .= '">Edit My Information';
		} else {
			$html .= '?username='. $xuser->get('login') .'">Edit Information';
		}
		$html .= '</a></th>'.n;
		$html .= t.t.t.'</tr>'.n;
		$html .= t.t.t.'<tr>'.n;
		$html .= t.t.t.t.'<th colspan="2" class="aRight"><a href="'. JRoute::_($url['user_limit']);
		if($self) {
			$html .= '">Request Additional Resources';
		} else {
			$html .= '?username='. $xuser->get('login') .'">Increase Resources';
		}
		$html .= '</a></th>'.n;
		$html .= t.t.t.'</tr>'.n;
		if($admin) {
			$html .= t.t.t.'<tr>'.n;
			$html .= t.t.t.t.'<th colspan="2" class="aRight"><a href="'. JRoute::_($url['user_delete']);
			if($self) {
				$html .= '">Delete My Account';
			} else {
				$html .= '?username='. $xuser->get('login') .'">Delete Account';
			}
			$html .= '</a></th>'.n;
			$html .= t.t.t.'</tr>'.n;
		}
		$html .= "\t".' </tfoot>'.n;*/
		$html .= t.t.'<tbody>'.n;
		$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
		$html .= t.t.t.t.'<th>Full Name:</th>'.n;
		$html .= t.t.t.t.'<td>'. htmlentities($xuser->get('name')) .'</td>'.n;
		$html .= t.t.t.'</tr>'.n;

		$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
		$html .= t.t.t.t.'<th>Login:</th>'.n;
		if ($showdetails) {
		    $html .= t.t.t.t.'<td>'. htmlentities($xuser->get('login')) .'&nbsp;&nbsp;('. htmlentities($xuser->get('uid')) .')</td>'.n;
		} else {
		    $html .= t.t.t.t.'<td>'. htmlentities($xuser->get('login')) .'</td>'.n;
		}
		$html .= t.t.t.'</tr>'.n;

		if ($self && ( ($xuser->get('email_confirmed') == 1) || ($xuser->get('email_confirmed') == 3))) {
			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.t.'<th>Password:</th>'.n;
			$html .= t.t.t.t.'<td><a href="'. JRoute::_('index.php?option=com_members&id='.$xuser->get('uid').'task=changepassword') .'">Change Password</a></td>'.n;
			$html .= t.t.t.'</tr>'.n;
		} elseif ($admin) {
			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.t.'<th>Password:</th>'.n;
			$html .= t.t.t.t.'<td><a href="'. JRoute::_('index.php?option=com_hub&task=lostpassword') .'">Reset Password</a></td>'.n;
			$html .= t.t.t.'</tr>'.n;
		}

		$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
		$html .= t.t.t.t.'<th>Organization or School:</th>'.n;
		$html .= t.t.t.t.'<td>'. htmlentities($xuser->get('org')) .'</td>'.n;
		$html .= t.t.t.'</tr>'.n;

	 	$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
		$html .= t.t.t.t.'<th>Employment Status:</th>'.n;
		$html .= t.t.t.t.'<td>';
		if (!$showdetails) {
			$html .= 'n/a';
		} else {
			switch ($xuser->get('orgtype'))
			{
				case '':
					$html .= 'n/a';
					break;
				case 'university':
					$html .= 'University / College Student or Staff';
					break;
				case 'precollege':
					$html .= 'K-12 (Pre-College) Student or Staff';
					break;
				case 'nationallab':
					$html .= 'National Laboratory';
					break;
				case 'industry':
					$html .= 'Industry / Private Company';
					break;
				case 'government':
					$html .= 'Government Agency';
					break;
				case 'military':
					$html .= 'Military';
					break;
				case 'unemployed':
					$html .= 'Retired / Unemployed';
					break;
				default:
					$html .= htmlentities(WhoisHtml::propercase($xuser->get('orgtype')));
					break;
			}
		}
		$html .= '</td>'.n;
		$html .= t.t.t.'</tr>'.n;

		$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'.>'.n;
		$html .= t.t.t.t.'<th>E-mail:</th>'.n;
		$html .= t.t.t.t.'<td>'. htmlentities($xuser->get('email'));
		if ($showdetails) {
			if ($xuser->get('email_confirmed') == 1) {
				$html .= '<br />(confirmed)';
			} elseif ($xuser->get('email_confirmed') == 2) {
				$html .= '<br />(grandfathered account)';
			} elseif ($xuser->get('email_confirmed') == 3) {
				$html .= '<br />(domain supplied email)';
			} elseif ($xuser->get('email_confirmed') < 0) {
				if ($xuser->get('email')) {
					$html .= '<br /><span style="color: red;">(awaiting confirmation)</span>';
					$html .= '<br />[code: ' . -$xuser->get('email_confirmed') . ']';
				} else {
					$html .= '<br /><span style="color: red;">(no email address on file)</span>';
				}
			} else {
				$html .= '<br /><span style="color: red;">(unknown confirmation status)</span>';
			}
		} else {
			if ( ($xuser->get('email_confirmed') != 1) && ($xuser->get('email_confirmed') != 3) ){
				$html .= '<br /><span style="color: red;">(awaiting confirmation)</span>';
			}
		}
		$html .= '</td>'.n;
		$html .= t.t.t.'</tr>'.n;

		if ($xuser->get('mailPreferenceOption') != 0) {
			$cmmsg = 'Yes, I wish to receive newsletters and other updates by e-mail.';
		} else {
			$cmmsg = 'No, I do not wish to receive newsletters and other updates by e-mail.';
		}
		$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
		$html .= t.t.t.t.'<th>Contact Me:</th>'.n;
		$html .= t.t.t.t.'<td>'.$cmmsg.'</td>'.n;
		$html .= t.t.t.'</tr>'.n;

		if ($showdetails) {
			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.t.'<th>'.$hubName.' Administrator:</th>'.n;
			if ($admin) {
				$html .= t.t.t.t.'<td>Yes</td>'.n;
			} else {
				$html .= t.t.t.t.'<td>No</td>'.n;
			}
			$html .= t.t.t.'</tr>'.n;

			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.t.'<th>Host Access:</th>'.n;
			$html .= t.t.t.t.'<td>';
			$hosts = $xuser->get('hosts');
			$count = (!empty($hosts)) ? count($hosts) : 0;
			for($i = 0;$i < $count; $i++) {
				$html .= htmlentities($hosts[$i]) .' ';
			}
			$html .= '</td>'.n;
			$html .= t.t.t.'</tr>'.n;

			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.t.'<th>Jobs Allowed:</th>'.n;
			$html .= t.t.t.t.'<td>'. htmlentities($xuser->get('jobs_allowed')) .'</td>'.n;
			$html .= t.t.t.'</tr>'.n;

			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.t.'<th>Citizenship:</th>'.n;
			$html .= t.t.t.t.'<td>'. htmlentities($xuser->get('countryorigin')) .'</td>'.n;
			$html .= t.t.t.'</tr>'.n;

			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.t.'<th>Residence:</th>'.n;
			$html .= t.t.t.t.'<td>'. htmlentities($xuser->get('countryresident')) .'</td>'.n;
			$html .= t.t.t.'</tr>'.n;

			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.t.'<th>Sex:</th>'.n;
			$html .= t.t.t.t.'<td>'. WhoisHtml::propercase_singleresponse($xuser->get('sex')) .'</td>'.n;
			$html .= t.t.t.'</tr>'.n;

			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.t.'<th>Racial Background:</th>'.n;
			$html .= t.t.t.t.'<td>'. WhoisHtml::propercase_multiresponse($xuser->get('race')) .'</td>'.n;
			$html .= t.t.t.'</tr>'.n;

			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.t.'<th>Hispanic Heritage:</th>'.n;
			$html .= t.t.t.t.'<td>'. WhoisHtml::propercase_multiresponse($xuser->get('hispanic')) .'</td>'.n;
			$html .= t.t.t.'</tr>'.n;

			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.t.'<th>Disability:</th>'.n;
			$html .= t.t.t.t.'<td>'. WhoisHtml::propercase_multiresponse($xuser->get('disability')) .'</td>'.n;
			$html .= t.t.t.'</tr>'.n;

			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.t.'<th>Telephone:</th>'.n;
			$html .= t.t.t.t.'<td>'. htmlentities($xuser->get('phone')) .'</td>'.n;
			$html .= t.t.t.'</tr>'.n;

			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) . '>'.n;
			$html .= t.t.t.t.'<th>Home Directory:</th>'.n;
			$html .= t.t.t.t.'<td>'. htmlentities($xuser->get('home')) .'</td>'.n;
			$html .= t.t.t.'</tr>'.n;
		}

		$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
		$html .= t.t.t.t.'<th>Show Materials for:</th>'.n;
		$html .= t.t.t.t.'<td>';
		$role = $xuser->get('role');
		for ($i = 0; $i < count($role); $i++) 
		{
			switch ($role[$i])
			{
				case 'student':    $html .= 'Student';    break;
				case 'educator':   $html .= 'Educator';   break;
				case 'researcher': $html .= 'Researcher'; break;
				case 'developer':  $html .= 'Developer';  break;
			}

			if ($i < count($role)) {
				$html .= '<br />';
			} elseif (count($xuser->get('edulevel'))) {
				$html .= '<br />';
			}
		}
		$edulevel = $xuser->get('edulevel');
		for ($i = 0; $i < count($edulevel); $i++) 
		{
			switch ($edulevel[$i])
			{
				case 'k12':           $html .= 'Pre-college (K-12)';      break;
				case 'undergraduate': $html .= 'Undergraduate';           break;
				case 'graduate':      $html .= 'Graduate / Professional'; break;
			}

			if ($i < count($edulevel)) {
				$html .= '<br />';
			}
		}
		$html .= '</td>'.n;
		$html .= t.t.t.'</tr>'.n;

		if ($showdetails) {
			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.t.'<th>Reason for Account:</th>'.n;
			$html .= t.t.t.t.'<td>'. htmlentities($xuser->get('reason')) .'</td>'.n;
			$html .= t.t.t.'</tr>'.n;

			$reg_host = $xuser->get('reg_host');
			$reg_ip   = $xuser->get('reg_ip');
			$reg_host = (empty($reg_host)) ? "n/a" : $reg_host;
			$reg_ip   = (empty($reg_ip)) ? "n/a" : $reg_ip;

			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.t.'<th>Created from Host:<br />(IP Address)</th>'.n;
			$html .= t.t.t.t.'<td>'. htmlentities($reg_host) .'<br />('. htmlentities($reg_ip) .')</td>'.n;
			$html .= t.t.t.'</tr>'.n;

			if ($admin) {
				$proxy = $xuser->get('proxy_uid');
				if ($proxy) {
					$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
					$html .= t.t.t.t.'<th>Proxy Created By:</th>'.n;
					$html .= t.t.t.t.'<td>';
					$proxyuser =& XUser::getInstance($proxy);
					if (!empty($proxyuser)) {
						$html .= $proxyuser->get('name') . ' (<a href="'.JRoute::_('index.php?option=com_whois&task=view&username=' . $proxyuser->get('login')) . '">' . $proxyuser->get('login') . '</a>)';
					} else {
						$html .= 'Unknown&nbsp;&nbsp;(' . $proxy . ')';
					}
					$html .= '</td>'.n;
					$html .= t.t.t.'</tr>'.n;
				}
			}

			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.t.'<th>Created On:</th>'.n;
			$html .= t.t.t.t.'<td>';
			if ($xuser->get('reg_date')) {
				$html .= str_replace('  ', '&nbsp;&nbsp;', date("F j, Y  g:ia", WhoisHtml::date2epoch($xuser->get('reg_date'))));
			} else {
				$html .= 'n/a';
			}
			$html .= '</td>'.n;
			$html .= t.t.t.'</tr>'.n;

			$html .= t.t.t.'<tr '. WhoisHtml::altrow($row++) .'>'.n;
			$html .= t.t.t.t.'<th>Last Modified On:</th>'.n;
			$html .= t.t.t.t.'<td>';
			if ($xuser->get('mod_date')) {
				$html .= str_replace('  ', '&nbsp;&nbsp;', date("F j, Y  g:ia", WhoisHtml::date2epoch($xuser->get('mod_date'))));
			} else {
				$html .= 'n/a';
			}
			$html .= '</td>'.n;
			$html .= t.t.t.'</tr>'.n;
		}

		$html .= "\t".' </tbody>'.n;
		$html .= "\t".'</table>'.n;

		if ( ($xuser->get('email_confirmed') == 1) || ($xuser->get('email_confirmed') == 3) ) {
			if ($showgroups) {
				ximport('xuserhelper');

				$applicants = XUserHelper::getGroups( $xuser->get('uid'), 'applicants' );
				$applicants = (is_array($applicants)) ? $applicants : array();
				$invitees = XUserHelper::getGroups( $xuser->get('uid'), 'invitees' );
				$invitees = (is_array($invitees)) ? $invitees : array();
				$members = XUserHelper::getGroups( $xuser->get('uid'), 'members' );
				$members = (is_array($members)) ? $members : array();
				$managers = XUserHelper::getGroups( $xuser->get('uid'), 'managers' );
				$managers = (is_array($managers)) ? $managers : array();
				
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
				
				$html .= t.'<table id="grouplist" summary="'.JText::_('GROUPS_TBL_SUMMARY').'">'.n;
				$html .= t.t.'<caption>'.JText::_('GROUPS_TBL_CAPTION').'</caption>'.n;
				$html .= t.t.'<thead>'.n;
				$html .= t.t.t.'<tr>'.n;
				$html .= t.t.t.t.'<th scope="col">'.JText::_('GROUPS_TBL_TH_NAME').'</th>'.n;
				$html .= t.t.t.t.'<th scope="col">'.JText::_('GROUPS_TBL_TH_STATUS').'</th>'.n;
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
						$html .= t.t.t.t.'<td>';
						if ($group->manager && $group->published) {
							$html .= '<span class="manager status">'.JText::_('GROUPS_STATUS_MANAGER').'</span>';
							$opt  = '<a href="'.JRoute::_('index.php?option=com_groups'.a.'gid='.$group->cn.a.'active=members') .'">'.JText::_('GROUPS_ACTION_MANAGE').'</a>';
							$opt .= ' <a href="'.JRoute::_('index.php?option=com_groups'.a.'gid='.$group->cn.a.'task=edit') .'">'.JText::_('GROUPS_ACTION_EDIT').'</a>';
							$opt .= ' <a href="'.JRoute::_('index.php?option=com_groups'.a.'gid='.$group->cn.a.'task=delete') .'">'.JText::_('GROUPS_ACTION_DELETE').'</a>';
						} else {
							if ($group->registered) {
								if ($group->regconfirmed) {
									$html .= '<span class="member status">'.JText::_('GROUPS_STATUS_APPROVED').'</span>';
								} else {
									$html .= '<span class="pending status">'.JText::_('GROUPS_STATUS_PENDING').'</span>';
								}
							} else {
								if ($group->regconfirmed) {
									$html .= '<span class="invitee status">'.JText::_('GROUPS_STATUS_INVITED').'</span>';
								} else {
									$html .= '<span class="status"> </span>';
								}
							}
						}
						$html .= '</td>'.n;
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
			}
		}
		$html .= '<div class="clear"></div>'.n;
		$html .= '<p><strong>Note:</strong> Some tools on '. $hubName .' are restricted to specifically authorized users.  Please <a href="/support">check support</a> for more information or if you have any questions.</p>'.n;

		return $html;
	}
	
	//-----------

	public function propercase($str) 
	{
		$size = 0;
		$dont_case = array('a', 'an', 'of', 'the', 'are', 'at', 'in');
		$str = trim($str);
		$str = strtoupper($str[0]) . strtolower(substr($str, 1));
		for ($i = 1; $i < strlen($str) - 1; ++$i) 
		{
			if ($str[$i] == ' ') {
				for ($j = $i + 1; $j < strlen($str) && $str[$j] != ' '; ++$j);
				$size = $j - $i - 1;
				$short_word = false;
				if ($size <= 3) {
					$word = substr($str, $i + 1, $size);
					for ($j = 0; $j < count($dont_case) && !$short_word; ++$j) 
					{
						if ($word == $dont_case[$j]) {
							$short_word = true;
						}
					}
				}
				if (!$short_word) {
					$str = substr($str, 0, $i + 1) . strtoupper($str[$i + 1]) . substr($str, $i + 2);
				}
			}   
			$i += $size;
		}
		return($str);
	}

	//-----------

	public function propercase_singleresponse($response) 
	{
		$html = '';
		switch ($response)
		{
			case '':        $html .= 'n/a';               break;
			case 'no':      $html .= 'None';              break;
			case 'refused': $html .= 'Declined Response'; break;
			default: 
				$html .= htmlentities(WhoisHtml::propercase($response));
				break;
		}
		return($html);
	}

	//-----------

	public function propercase_multiresponse($response_array) 
	{
		$html = '';
		if (count($response_array) == 0) {
			$html .= 'n/a';
		} else {
			for ($i = 0; $i < count($response_array); $i++) 
			{
				if ($i > 0) {
					$html .= ', ';
				}
				if ($response_array[$i] == 'no') {
					$html .= 'None';
				} elseif ($response_array[$i] == 'refused') {
					$html .= 'Declined Response';
				} else {
					$html .= htmlentities(WhoisHtml::propercase($response_array[$i]));
				}
			}
		}
		return($html);
	}
	
	//-----------
	
	public function valformat($value, $format) 
	{
		if ($format == 1) {
			return(number_format($value));
		} elseif ($format == 2 || $format == 3) {
			if ($format == 2) {
				$min = round($value / 60);
			} else {
				$min = floor($value / 60);
				$sec = $value - ($min * 60);
			}
			$hr = floor($min / 60);
			$min -= ($hr * 60);
			$day = floor($hr / 24);
			$hr -= ($day * 24);
			if ($day == 1) {
				$day = "1 day, ";
			} elseif ($day > 1) {
				$day = number_format($day) . " days, ";
			} else {
				$day = "";
			}
			if ($format == 2) {
				return(sprintf("%s%d:%02d", $day, $hr, $min));
			} else {
				return(sprintf("%s%d:%02d:%02d", $day, $hr, $min, $sec));
			}
		} else {
			return($value);
		}
	}
	
	//-----------
	
	public function date2epoch($datestr) 
	{
		if (empty($datestr))
			return null;

		list ($date, $time) = explode(' ', $datestr);
		list ($y, $m, $d) = explode('-', $date);
		list ($h, $i, $s) = explode(':', $time);
		return(mktime($h, $i, $s, $m, $d, $y));
	}
	
	//-----------
	
	public function activity( $option, $users, $guests ) 
	{
		$html  = WhoisHtml::div( WhoisHtml::hed(2,JText::_('Active Users and Guests')), 'full', 'content-header').n;
		$html .= '<div class="main section">'.n;
		$html .= '<table class="activeusers" summary="'.JText::_('Logged-in users').'">'.n;
		$html .= t.'<caption>'.JText::_('Table 1: Logged-in Users').'</caption>'.n;
		$html .= t.'<thead>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th>'.JText::_('Name').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('Login').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('Org Type').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('Organization').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('Resident').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('Host').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('IP').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('Idle').'</th>'.n;
		$html .= t.t.'</tr>'.n;
		$html .= t.'</thead>'.n;
		$html .= t.'<tfoot>'.n;
		$html .= t.t.'<tr class="summary">'.n;
		$html .= t.t.t.'<th colspan="7" class="numerical-data">'.JText::_('Total Users').'</th>'.n;
		$html .= t.t.t.'<td>'.count($users).'</td>'.n;
		$html .= t.t.'</tr>'.n;
		$html .= t.'</tfoot>'.n;
		$html .= t.'<tbody>'.n;
		if (count($users) > 0) {
			$cls = 'even';
			foreach (array_keys($users) as $userkey) 
			{
				$cls = (($cls == 'even') ? 'odd' : 'even');
				
				for ($i = 0; $i < count($users[$userkey]) - 4; $i++) 
				{
					if ($i) {
						$cls .=  ' sameuser';
					}
					$html .= t.t.'<tr class="'.$cls.'">'.n;
					if ($i) {
						$html .= t.t.t.'<td colspan="5">&nbsp;</td>'.n;
					} else {
						$html .= t.t.t.'<td>'. stripslashes($users[$userkey]['name']) .'</td>'.n;
						$html .= t.t.t.'<td><a href="'.JRoute::_('index.php?option='.$option.a.'task=view'.a.'username='.$userkey).'">'.$userkey.'</td>'.n;
						$html .= t.t.t.'<td>';
						switch ($users[$userkey]['orgtype']) 
						{
							case 'university':  $html .= JText::_('University / College'); break;
							case 'precollege':  $html .= JText::_('K - 12 (Pre-College)'); break;
							case 'educational': $html .= JText::_('Educational');          break;
							case 'nationallab': $html .= JText::_('National Laboratory');  break;
							case 'industry':    $html .= JText::_('Industry / Private');   break;
							case 'government':  $html .= JText::_('Government Agency');    break;
							case 'military':    $html .= JText::_('Military');             break;
							case 'personal':    $html .= JText::_('Personal');             break;
							case 'unemployed':  $html .= JText::_('Retired / Unemployed'); break;
							default: $html .=  $users[$userkey]['orgtype']; break;
						}
						$html .= '</td>'.n;
						$html .= t.t.t.'<td>'. stripslashes($users[$userkey]['org']) .'</td>'.n;
						$html .= t.t.t.'<td>'. $users[$userkey]['countryresident'] .'</td>'.n;
					}
					$html .= t.t.t.'<td>'. $users[$userkey][$i]['host'] .'</td>'.n;
					$html .= t.t.t.'<td>'. $users[$userkey][$i]['ip'] .'</td>'.n;
					$html .= t.t.t.'<td>'. WhoisHtml::valformat($users[$userkey][$i]['idle'], 3) .'</td>'.n;
					$html .= t.t.'</tr>'.n;
				}
			}
		} else {
			$html .= t.t.'<tr class="odd">'.n;
			$html .= t.t.t.'<td colspan="8">'.JText::_('No results found.').'</td>'.n;
			$html .= t.t.'</tr>'.n;
		}
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;
		
		$html .= '<table summary="'.JText::_('Guest users').'">'.n;
		$html .= t.'<caption>'.JText::_('Table 2: Guests').'</caption>'.n;
		$html .= t.'<thead>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th>'.JText::_('Name').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('Host').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('IP').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('Idle').'</th>'.n;
		$html .= t.t.'</tr>'.n;
		$html .= t.'</thead>'.n;
		$html .= t.'<tfoot>'.n;
		$html .= t.t.'<tr class="summary">'.n;
		$html .= t.t.t.'<th colspan="3" class="numerical-data">'.JText::_('Total Guests').'</th>'.n;
		$html .= t.t.t.'<td>'.count($guests).'</td>'.n;
		$html .= t.t.'</tr>'.n;
		$html .= t.'</tfoot>'.n;
		$html .= t.'<tbody>'.n;
		if (count($guests) > 0) {
			$cls = 'even';
			foreach ($guests as $guest) 
			{
				$cls = (($cls == 'even') ? 'odd' : 'even');
				
				$guest['host'] = ($guest['host']) ? $guest['host'] : JText::_('Unknown');
				$guest['ip'] = ($guest['ip']) ? $guest['ip'] : JText::_('Unknown');
				
				$html .= t.t.'<tr class="'.$cls.'">'.n;
				$html .= t.t.t.'<td>'.JText::_('(guest)').'</td>'.n;
				$html .= t.t.t.'<td>'.$guest['host'].'</td>'.n;
				$html .= t.t.t.'<td>'.$guest['ip'].'</td>'.n;
				$html .= t.t.t.'<td>'.WhoisHtml::valformat($guest['idle'], 3).'</td>'.n;
				$html .= t.t.'</tr>'.n;
			}
		} else {
			$html .= t.t.'<tr class="odd">'.n;
			$html .= t.t.t.'<td colspan="5">'.JText::_('No results found.').'</td>'.n;
			$html .= t.t.'</tr>'.n;
		}
		$html .=  t.'</tbody>'.n;
		$html .= '</table>'.n;
		$html .= '</div>'.n;
		
		return $html;
	}
}
?>
