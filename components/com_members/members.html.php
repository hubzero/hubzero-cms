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
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class MembersHtml 
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
	
	public function locked( $msg, $tag='p' )
	{
		return '<'.$tag.' class="locked">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function help( $msg, $tag='p' )
	{
		return '<'.$tag.' class="help">'.$msg.'</'.$tag.'>'.n;
	}	
	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
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
	
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}
	
	//-----------

	public function aside($txt, $id='')
	{
		return MembersHtml::div($txt, 'aside', $id);
	}
	
	//-----------
	
	public function subject($txt, $id='')
	{
		return MembersHtml::div($txt, 'subject', $id);
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

	//-------------------------------------------------------------
	// Sections
	//-------------------------------------------------------------

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
				$html .= MembersHtml::div( $section['html'], $cls.'section', key($cats[$k]).'-section' );
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
				if (strtolower($name) == $active) {
					$app =& JFactory::getApplication();
					$pathway =& $app->getPathway();
					$pathway->addItem($cat[$name],'index.php?option='.$option.a.'id='.$id.a.'active='.$name);
				}
				
				$html .= t.t.'<li id="sm-'.$i.'"';
				$html .= (strtolower($name) == $active) ? ' class="active"' : '';
				$html .= '><a class="tab" rel="'.$name.'" href="'.JRoute::_('index.php?option='.$option.a.'id='.$id.a.'active='.$name).'"><span>'.$cat[$name].'</span></a></li>'.n;
				$i++;	
			}
		}
		$html .= t.'</ul>'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</div><!-- / #sub-menu -->'.n;
		
		return $html;
	}
	
	//-----------
	
	public function metadata($sections)
	{
		$html  = '';
		foreach ($sections as $section)
		{
			$html .= (isset($section['metadata'])) ? $section['metadata'] : '';
		}
		//$html .= MembersHtml::div('', 'clear');
		
		if ($html) {
			return MembersHtml::div($html, 'metadata');
		} else {
			return '';
		}
	}
	
	//-----------
	
	public function title( $profile, $authorized, $option ) 
	{
		$html = MembersHtml::div( MembersHtml::hed( 2, '<span class="fn">'.stripslashes($profile->get('name')).'</span>' ), '', 'content-header').n;
		$html .= '<div id="content-header-extra">'.n;
		$html .= t.'<ul id="useroptions">'.n;
		if ($authorized) {
			$html .= t.t.'<li><a class="edit-member" href="'.JRoute::_('index.php?option='.$option.a.'task=edit'.a.'id='. $profile->get('uidNumber')) .'">'.JText::_('Edit profile').'</a></li>'.n;
		}
		$juser =& JFactory::getUser();
		if (!$juser->get('guest') && ($profile->get('uidNumber') != $juser->get('id'))) {
			$html .= t.t.'<li class="last"><a class="message" href="'.JRoute::_('index.php?option='.$option.a.'id='. $juser->get('id').'&active=messages&task=new&to='.$profile->get('uidNumber')) .'">'.JText::_('Send Message').'</a></li>'.n;
		}
		$html .= t.'</ul>'.n;
		$html .= '</div><!-- / #content-header-extra -->'.n;
		
		return $html;
	}

	//-----------

	public function selectAccess($name, $value, $class='', $id='')
	{
		$arr = array( JText::_('Public'), JText::_('Registered users'), JText::_('Private') );
		
		$html  = '<select name="'.$name.'"';
		$html .= ($id) ? ' id="'.$id.'">'.n : '>'.n;
		$html .= ($class) ? ' class="'.$class.'">'.n : '>'.n;
		foreach ($arr as $k => $v) 
		{
			$selected = ($k == $value)
					  ? ' selected="selected"'
					  : '';
			$html .= ' <option value="'.$k.'"'.$selected.'>'.$v.'</option>'.n;
		}
		$html .= '</select>'.n;
		return $html;
	}
	
	//-----------

	public function profile( $sections, $option, $title, $profile, $authorized, $params, $registration )
	{
		$juser =& JFactory::getUser();
		$loggedin = false;
		if (!$juser->get('guest')) {
			$loggedin = true;
		}

		$html  = MembersHtml::hed(3, JText::_('PROFILE')).n;
		$html .= '<div class="aside">'.n;
		/*if ($authorized) {
			$html .= t.t.'<p class="help">'.JText::_('Passwords can be changed with <a href="'.JRoute::_('index.php?option='.$option.a.'id='.$profile->get('uidNumber').a.'task=changepassword').'">this form</a>.').'</p>'.n;
			$html .= t.t.'<p class="help">'.JText::_('Request for more storage or sessions may be made with <a href="'.JRoute::_('index.php?option='.$option.a.'id='.$profile->get('uidNumber').a.'task=raiselimit').'">this form</a>.').'</p>'.n;
		}*/
		$html .= MembersHtml::metadata( $sections );
		if ($profile->get('picture')) {
			list($width,$height) = getimagesize(JPATH_ROOT.$profile->get('picture'));
			$html .= t.'<p class="portrait userImage"><img class="photo" src="'.$profile->get('picture').'"';
			$html .= ($width && $width > 190) ? ' width="190"' : '';
			$html .= ' alt="'.JText::_('MEMBER_PICTURE').'" /></p>'.n;
			if ($authorized) {
				$html .= t.'<p><a href="'.JRoute::_('index.php?option='.$option.a.'task=edit'.a.'id='.$profile->get('uidNumber')).'">'.JText::_('MEMBERS_UPLOAD_IMAGE').'</a></p>'.n;
			}
		}
		$html .= '</div>'.n;
		$html .= '<div class="subject">'.n;
		if ($authorized) {
			$html .= '<form method="post" action="'.JRoute::_('index.php?option='.$option.a.'id='.$profile->get('uidNumber')).'">'.n;
		}
		if ($profile->get('public') != 1) {
			$msg = JText::_('MEMBERS_NOT_PUBLIC');
			if ($authorized) {
				$msg .= ' <a class="edit-member" href="'.JRoute::_('index.php?option='.$option.a.'task=edit'.a.'id='. $profile->get('uidNumber')) .'">'.JText::_('Edit this profile').'</a>';
			}
			$html .= t.MembersHtml::locked( $msg ).n;
		}
		$html .= t.'<table class="profile" summary="'.JText::_('PROFILE_TBL_SUMMARY').'">'.n;
		if ($authorized) {
			$html .= t.t.'<tfoot>'.n;
			$html .= t.t.t.'<tr>'.n;
			$html .= t.t.t.t.'<td> </td>'.n;
			$html .= t.t.t.t.'<td> </td>'.n;
			$html .= t.t.t.t.'<td><input type="submit" value="'.JText::_('Save changes').'"/></td>'.n;
			$html .= t.t.t.'</tr>'.n;
			$html .= t.t.'</tfoot>'.n;
		}
		$html .= t.t.'<tbody>'.n;
		
		if ($authorized) {
			$html .= t.t.t.'<tr class="private">'.n;
			$html .= t.t.t.t.'<th>'.JText::_('Password').'</th>'.n;
			$html .= t.t.t.t.'<td colspan="2">'.JText::_('Passwords can be changed with <a href="'.JRoute::_('index.php?option='.$option.a.'id='.$profile->get('uidNumber').a.'task=changepassword').'">this form</a>.').'</td>'.n;
			$html .= t.t.t.'</tr>'.n;
		}
		
		//if ($profile->get('organization')) {
		if ($registration->Organization != REG_HIDE) {
			if ($params->get('access_org') == 0 
			 || ($params->get('access_org') == 1 && $loggedin) 
			 || ($params->get('access_org') == 2 && $authorized)
			) {
				$html .= t.t.t.'<tr';
				$html .= ($params->get('access_org') == 2) ? ' class="private"' : '';
				$html .= '>'.n;
				$html .= t.t.t.t.'<th>'.JText::_('COL_ORGANIZATION').'</th>'.n;
				$html .= t.t.t.t.'<td><span class="org">'.MembersHtml::xhtml(stripslashes($profile->get('organization'))).'</span></td>'.n;
				if ($authorized) {
					$html .= t.t.t.t.'<td>'.MembersHtml::selectAccess('access[org]',$params->get('access_org')).'</td>'.n;
				}
				$html .= t.t.t.'</tr>'.n;
			}
		}
		if ($registration->Employment != REG_HIDE) {
		//if ($profile->get('orgtype')) {
			if ($params->get('access_orgtype') == 0 
			 || ($params->get('access_orgtype') == 1 && $loggedin) 
			 || ($params->get('access_orgtype') == 2 && $authorized)
			) {
				$html .= t.t.t.'<tr';
				$html .= ($params->get('access_orgtype') == 2) ? ' class="private"' : '';
				$html .= '>'.n;
				$html .= t.t.t.t.'<th>'.JText::_('Employment Status').'</th>'.n;
				$html .= t.t.t.t.'<td><span class="userType">';
				switch ($profile->get('orgtype'))
				{
					case '':
						$html .= JText::_('n/a');
						break;
					case 'universitystudent':
						$html .= JText::_('University / College Student');
						break;
					case 'university':
					case 'universityfaculty':
						$html .= JText::_('University / College Faculty');
						break;
					case 'universitystaff':
						$html .= JText::_('University / College Staff');
						break;
					case 'precollege':
					case 'precollegefacultystaff':
						$html .= JText::_('K-12 (Pre-College) Faculty or Staff');
						break;
					case 'precollegestudent':
						$html .= JText::_('K-12 (Pre-College) Student');
						break;
					case 'nationallab':
						$html .= JText::_('National Laboratory');
						break;
					case 'industry':
						$html .= JText::_('Industry / Private Company');
						break;
					case 'government':
						$html .= JText::_('Government Agency');
						break;
					case 'military':
						$html .= JText::_('Military');
						break;
					case 'unemployed':
						$html .= JText::_('Retired / Unemployed');
						break;
					default:
						$html .= htmlentities($profile->get('orgtype'),ENT_COMPAT,'UTF-8');
						break;
				}
				$html .= '</span></td>'.n;
				if ($authorized) {
					$html .= t.t.t.t.'<td>'.MembersHtml::selectAccess('access[orgtype]',$params->get('access_orgtype')).'</td>'.n;
				}
				$html .= t.t.t.'</tr>'.n;
			}
		}
		//if ($registration->Email != REG_HIDE) {
		if ($profile->get('email')) {
			if ($params->get('access_email') == 0 
			 || ($params->get('access_email') == 1 && $loggedin) 
			 || ($params->get('access_email') == 2 && $authorized)
			) {
				$html .= t.t.t.'<tr';
				$html .= ($params->get('access_email') == 2) ? ' class="private"' : '';
				$html .= '>'.n;
				$html .= t.t.t.t.'<th>'.JText::_('E-mail').'</th>'.n;
				$html .= t.t.t.t.'<td><a class="email" href="mailto:'.MembersHtml::obfuscate($profile->get('email')).'" rel="nofollow">'. MembersHtml::obfuscate($profile->get('email')).'</a></td>';
				if ($authorized) {
					$html .= t.t.t.t.'<td>'.MembersHtml::selectAccess('access[email]',$params->get('access_email')).'</td>'.n;
				}
				$html .= t.t.t.'</tr>'.n;
			}
		}
		
		if ($registration->URL != REG_HIDE) {
		//if ($profile->get('url')) {
			if ($params->get('access_url') == 0 
			 || ($params->get('access_url') == 1 && $loggedin) 
			 || ($params->get('access_url') == 2 && $authorized)
			) {
				$url = stripslashes($profile->get('url'));
				if ($url) {
					$href = '<a class="url" href="'.$url.'">'.$url.'</a>';
				} else {
					$href = JText::_('None');
				}
				
				$html .= t.t.t.'<tr';
				$html .= ($params->get('access_url') == 2) ? ' class="private"' : '';
				$html .= '>'.n;
				$html .= t.t.t.t.'<th>'.JText::_('COL_WEBSITE').'</th>'.n;
				$html .= t.t.t.t.'<td>'.$href.'</td>'.n;
				if ($authorized) {
					$html .= t.t.t.t.'<td>'.MembersHtml::selectAccess('access[url]',$params->get('access_url')).'</td>'.n;
				}
				$html .= t.t.t.'</tr>'.n;
			}
		}
		
		if ($registration->Phone != REG_HIDE) {
		//if ($profile->get('phone')) {
			if ($params->get('access_phone') == 0 
			 || ($params->get('access_phone') == 1 && $loggedin) 
			 || ($params->get('access_phone') == 2 && $authorized)
			) {
				$phone = htmlentities($profile->get('phone'),ENT_COMPAT,'UTF-8');
				$phone = ($phone) ? $phone : JText::_('None');
				
				$html .= t.t.t.'<tr';
				$html .= ($params->get('access_phone') == 2) ? ' class="private"' : '';
				$html .= '>'.n;
				$html .= t.t.t.t.'<th>'.JText::_('Telephone').'</th>'.n;
				$html .= t.t.t.t.'<td><span class="phone">'. $phone .'</span></td>'.n;
				if ($authorized) {
					$html .= t.t.t.t.'<td>'.MembersHtml::selectAccess('access[phone]',$params->get('access_phone')).'</td>'.n;
				}
				$html .= t.t.t.'</tr>'.n;
			}
		}
		
		if ($params->get('access_bio') == 0 
		 || ($params->get('access_bio') == 1 && $loggedin) 
		 || ($params->get('access_bio') == 2 && $authorized)
		) {
			if ($profile->get('bio')) {
				ximport('wiki.parser');
				$p = new WikiParser( $profile->get('name'), $option, 'members'.DS.'profile', 'member' );
				$bio = $p->parse( n.stripslashes($profile->get('bio')), 0, 0 );
			} else {
				$bio = JText::_('NO_BIOGRAPHY');
			}
			$html .= t.t.t.'<tr';
			$html .= ($params->get('access_bio') == 2) ? ' class="private"' : '';
			$html .= '>'.n;
			$html .= t.t.t.t.'<th>'.JText::_('COL_BIOGRAPHY').'</th>'.n;
			$html .= t.t.t.t.'<td>'.$bio.'</td>'.n;
			if ($authorized) {
				$html .= t.t.t.t.'<td>'.MembersHtml::selectAccess('access[bio]',$params->get('access_bio')).'</td>'.n;
			}
			$html .= t.t.t.'</tr>'.n;
		}
		
		if ($registration->Interests != REG_HIDE) {
			if ($params->get('access_tags') == 0 
			 || ($params->get('access_tags') == 1 && $loggedin) 
			 || ($params->get('access_tags') == 2 && $authorized)
			) {
				$database =& JFactory::getDBO();
				$mt = new MembersTags( $database );
				$tags = $mt->get_tag_cloud(0,0,$profile->get('uidNumber'));
				if (!$tags) {
					$tags = JText::_('None');
				}
				//if ($tags) {
					$html .= t.t.t.'<tr';
					$html .= ($params->get('access_tags') == 2) ? ' class="private"' : '';
					$html .= '>'.n;
					$html .= t.t.t.t.'<th>'.JText::_('COL_INTERESTS').'</th>'.n;
					$html .= t.t.t.t.'<td>'.$tags.'</td>'.n;
					if ($authorized) {
						$html .= t.t.t.t.'<td>'.MembersHtml::selectAccess('access[tags]',$params->get('access_tags')).'</td>'.n;
					}
					$html .= t.t.t.'</tr>'.n;
				//}
			}
		}
		
		if ($registration->Citizenship != REG_HIDE) {
		//if ($profile->get('countryorigin')) {
			if ($params->get('access_countryorigin') == 0 
			 || ($params->get('access_countryorigin') == 1 && $loggedin) 
			 || ($params->get('access_countryorigin') == 2 && $authorized)
			) {
				$img = '';
				if (is_file(JPATH_ROOT.DS.'components'.DS.$option.DS.'images'.DS.'flags'.DS.strtolower($profile->get('countryorigin')).'.gif')) {
					$img = '<img src="/components/'.$option.'/images/flags/'.strtolower($profile->get('countryorigin')).'.gif" alt="'.$profile->get('countryorigin').' '.JText::_('flag').'" /> ';
				}
				$html .= t.t.t.'<tr';
				$html .= ($params->get('access_countryorigin') == 2) ? ' class="private"' : '';
				$html .= '>'.n;
				$html .= t.t.t.t.'<th>'.JText::_('Citizenship').'</th>'.n;
				$html .= t.t.t.t.'<td><span class="country '.strtolower($profile->get('countryorigin')).'">'.$img. htmlentities($profile->get('countryorigin'),ENT_COMPAT,'UTF-8') .'</span></td>'.n;
				if ($authorized) {
					$html .= t.t.t.t.'<td>'.MembersHtml::selectAccess('access[countryorigin]',$params->get('access_countryorigin')).'</td>'.n;
				}
				$html .= t.t.t.'</tr>'.n;
			}
		}
		if ($registration->Residency != REG_HIDE) {
		//if ($profile->get('countryresident')) {
			if ($params->get('access_countryresident') == 0 
			 || ($params->get('access_countryresident') == 1 && $loggedin) 
			 || ($params->get('access_countryresident') == 2 && $authorized)
			) {
				$img = '';
				if (is_file(JPATH_ROOT.DS.'components'.DS.$option.DS.'images'.DS.'flags'.DS.strtolower($profile->get('countryresident')).'.gif')) {
					$img = '<img src="/components/'.$option.'/images/flags/'.strtolower($profile->get('countryresident')).'.gif" alt="'.$profile->get('countryresident').' '.JText::_('flag').'" /> ';
				}
				$html .= t.t.t.'<tr';
				$html .= ($params->get('access_countryresident') == 2) ? ' class="private"' : '';
				$html .= '>'.n;
				$html .= t.t.t.t.'<th>'.JText::_('Residence').'</th>'.n;
				$html .= t.t.t.t.'<td><span class="country '.strtolower($profile->get('countryresident')).'">'.$img. htmlentities($profile->get('countryresident'),ENT_COMPAT,'UTF-8') .'</span></td>'.n;
				if ($authorized) {
					$html .= t.t.t.t.'<td>'.MembersHtml::selectAccess('access[countryresident]',$params->get('access_countryresident')).'</td>'.n;
				}
				$html .= t.t.t.'</tr>'.n;
			}
		}
		if ($registration->Sex != REG_HIDE) {
		//if ($profile->get('gender') && $profile->get('gender') != 'refused') {
			if ($params->get('access_gender') == 0 
			 || ($params->get('access_gender') == 1 && $loggedin) 
			 || ($params->get('access_gender') == 2 && $authorized)
			) {
				$html .= t.t.t.'<tr';
				$html .= ($params->get('access_gender') == 2) ? ' class="private"' : '';
				$html .= '>'.n;
				$html .= t.t.t.t.'<th>'.JText::_('Sex').'</th>'.n;
				$html .= t.t.t.t.'<td>'. MembersHtml::propercase_singleresponse($profile->get('gender')) .'</td>'.n;
				if ($authorized) {
					$html .= t.t.t.t.'<td>'.MembersHtml::selectAccess('access[gender]',$params->get('access_gender')).'</td>'.n;
				}
				$html .= t.t.t.'</tr>'.n;
			}
		}
		if ($registration->Disability != REG_HIDE) {
		//if ($profile->get('disability')) {
			$dis = MembersHtml::propercase_multiresponse($profile->get('disability'));
			if ($dis) {
				if ($params->get('access_disability') == 0 
				 || ($params->get('access_disability') == 1 && $loggedin) 
				 || ($params->get('access_disability') == 2 && $authorized)
				) {
					$html .= t.t.t.'<tr';
					$html .= ($params->get('access_disability') == 2) ? ' class="private"' : '';
					$html .= '>'.n;
					$html .= t.t.t.t.'<th>'.JText::_('Disability').'</th>'.n;
					$html .= t.t.t.t.'<td>'. $dis .'</td>'.n;
					if ($authorized) {
						$html .= t.t.t.t.'<td>'.MembersHtml::selectAccess('access[disability]',$params->get('access_disability')).'</td>'.n;
					}
					$html .= t.t.t.'</tr>'.n;
				}
			}
		}
		if ($registration->Hispanic != REG_HIDE) {
		//if ($profile->get('hispanic')) {
			$his = MembersHtml::propercase_multiresponse($profile->get('hispanic'));
			if ($his) {
				if ($params->get('access_hispanic') == 0 
				 || ($params->get('access_hispanic') == 1 && $loggedin) 
				 || ($params->get('access_hispanic') == 2 && $authorized)
				) {
					$html .= t.t.t.'<tr';
					$html .= ($params->get('access_hispanic') == 2) ? ' class="private"' : '';
					$html .= '>'.n;
					$html .= t.t.t.t.'<th>'.JText::_('Hispanic Heritage').'</th>'.n;
					$html .= t.t.t.t.'<td>'. $his .'</td>'.n;
					if ($authorized) {
						$html .= t.t.t.t.'<td>'.MembersHtml::selectAccess('access[hispanic]',$params->get('access_hispanic')).'</td>'.n;
					}
					$html .= t.t.t.'</tr>'.n;
				}
			}
		}
		if ($registration->Race != REG_HIDE) {
		//if ($profile->get('race')) {
			$rac = MembersHtml::propercase_multiresponse($profile->get('race'));
			if ($rac) {
				if ($params->get('access_race') == 0 
				 || ($params->get('access_race') == 1 && $loggedin) 
				 || ($params->get('access_race') == 2 && $authorized)
				) {
					$html .= t.t.t.'<tr';
					$html .= ($params->get('access_race') == 2) ? ' class="private"' : '';
					$html .= '>'.n;
					$html .= t.t.t.t.'<th>'.JText::_('Racial Background').'</th>'.n;
					$html .= t.t.t.t.'<td>'. $rac .'</td>'.n;
					if ($authorized) {
						$html .= t.t.t.t.'<td>'.MembersHtml::selectAccess('access[race]',$params->get('access_race')).'</td>'.n;
					}
					$html .= t.t.t.'</tr>'.n;
				}
			}
		}
		if ($registration->OptIn != REG_HIDE) {
			if ($params->get('access_optin') == 0 
			 || ($params->get('access_optin') == 1 && $loggedin) 
			 || ($params->get('access_optin') == 2 && $authorized)
			) {
				$html .= t.t.t.'<tr';
				$html .= ($params->get('access_optin') == 2) ? ' class="private"' : '';
				$html .= '>'.n;
				$html .= t.t.t.t.'<th>'.JText::_('E-mail Updates').'</th>'.n;
				$html .= t.t.t.t.'<td>';
				$html .= ($profile->get('mailPreferenceOption')) ? JText::_('Yes') : JText::_('No');
				$html .= '</td>'.n;
				if ($authorized) {
					$html .= t.t.t.t.'<td>'.MembersHtml::selectAccess('access[optin]',$params->get('access_optin')).'</td>'.n;
				}
				$html .= t.t.t.'</tr>'.n;
			}
		}
		$html .= t.t.'</tbody>'.n;
		$html .= t.'</table>'.n;
		if ($authorized) {
			$html .= '<input type="hidden" name="task" value="saveaccess" />';
			$html .= '</form>'.n;
		}
		$html .= '</div>'.n;
		$html .= '<div class="clear"></div>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function propercase_singleresponse($response) 
	{
		$html = '';
		switch ($response)
		{
			case '':        $html .= JText::_('n/a');               break;
			case 'no':      $html .= JText::_('None');              break;
			case 'refused': $html .= JText::_('Declined Response'); break;
			default:        $html .= htmlentities(ucfirst($response),ENT_COMPAT,'UTF-8');       break;
		}
		return $html;
	}

	//-----------

	public function propercase_multiresponse($response_array) 
	{
		$html = '';
		if (count($response_array) == 0) {
			$html .= JText::_('n/a');
		} else {
			for ($i = 0; $i < count($response_array); $i++) 
			{
				if ($i > 0) {
					$html .= ', ';
				}
				if ($response_array[$i] == 'no') {
					$html .= JText::_('None');
				} elseif ($response_array[$i] == 'refused') {
					$html .= JText::_('Declined Response');
				} else {
					$html .= htmlentities(ucfirst($response_array[$i]),ENT_COMPAT,'UTF-8');
				}
			}
		}
		return $html;
	}
	
	//-----------
	
	public function obfuscate( $email )
	{
		$length = strlen($email);
		$obfuscatedEmail = '';
		for ($i = 0; $i < $length; $i++) 
		{
			$obfuscatedEmail .= '&#'. ord($email[$i]) .';';
		}
		
		return $obfuscatedEmail;
	}
	
	//-----------

	public function view( $profile, $authorized, $option, $cats, $sections, $tab ) 
	{
		$no_html = JRequest::getInt( 'no_html', 0 );
		
		$html  = '';
		if (!$no_html) {
			$html .= MembersHtml::title( $profile, $authorized, $option );
			$html .= MembersHtml::tabs( $option, $profile->get('uidNumber'), $cats, $tab );
		}
		$html .= MembersHtml::sections( $sections, $cats, $tab, 'hide', 'main' );
		
		return MembersHtml::div( $html, 'vcard' );
	}
	
	//-----------
	
	public function edit( $authorized, $title, $profile, $option, $tags, $registration, $xregistration ) 
	{
		/*$errors = array();
		$errors['fname_class'] = '';
		$errors['fname_msg']   = '';
		$errors['lname_class'] = '';
		$errors['lname_msg']   = '';
		if ($err != 0) {
			if ($row->givenName == '') {
				$errors['fname_class'] = ' class="fieldWithErrors"';
				$errors['fname_msg']   = MembersHtml::error(JText::_('ERROR_NO_FIRST_NAME'));
			} else {
				$errors['fname_class'] = '';
				$errors['fname_msg']   = '';
			}
		
			if ($row->surname == '') {
				$errors['lname_class'] = ' class="fieldWithErrors"';
				$errors['lname_msg']   = MembersHtml::error(JText::_('ERROR_NO_LAST_NAME'));
			} else {
				$errors['lname_class'] = '';
				$errors['lname_msg']   = '';
			}
		}*/
		
		$name = stripslashes($profile->get('name'));
		$surname = stripslashes($profile->get('surname'));
		$givenName = stripslashes($profile->get('givenName'));
		$middleName = stripslashes($profile->get('middleName'));
		
		if (!$surname) {
			$bits = explode(' ', $name);
			$surname = array_pop($bits);
			if (count($bits) >= 1) {
				$givenName = array_shift($bits);
			}
			if (count($bits) >= 1) {
				$middleName = implode(' ',$bits);
			}
		}
		
		$html  = MembersHtml::div( MembersHtml::hed( 2, $title ), '', 'content-header').n;;
		$html .= '<div id="content-header-extra">'.n;
		$html .= '<ul id="useroptions">'.n;
		$html .= t.'<li class="last"><a href="'.JRoute::_('index.php?option='.$option.a.'task=cancel'.a.'id='. $profile->get('uidNumber')) .'">'.JText::_('CANCEL').'</a></li>'.n;
		$html .= '</ul>'.n;
		$html .= '</div><!-- / #content-header-extra -->'.n;
		$html .= '<div class="main section">'.n;
		$html .= '<form id="hubForm" method="post" action="index.php" enctype="multipart/form-data">'.n;
		
		if ($authorized === 'admin') {
			$html .= t.'<div class="explaination">'.n;
			$html .= t.t.'<p>'.JText::_('The following options are available to administrators only.').'</p>'.n;
			$html .= t.'</div>'.n;
			$html .= t.'<fieldset>'.n;
			$html .= t.t.MembersHtml::hed(3, JText::_('Admin Options')).n;
			$html .= t.t.'<label>'.n;
			$html .= t.t.t.'<input type="checkbox" class="option" name="profile[vip]" value="1"';
			if ($profile->get('vip') == 1) { 
				$html .= ' checked="checked"';
			}
			$html .= '/>'.n;
			$html .= t.t.t.JText::_('VIP').n;
			$html .= t.t.'</label>'.n;
			$html .= t.'</fieldset><div class="clear"></div>'.n;
		} else {
			$html .= t.t.'<input type="hidden" name="profile[vip]" value="'. $profile->get('vip') .'" />'.n;
		}
		
		$html .= t.'<div class="explaination">'.n;
		//$html .= t.t.'<p class="help">'.JText::_('E-mail may be changed with <a href="/hub/registration/edit">this form</a>.').n;
		$html .= t.t.'<p class="help">'.JText::_('Passwords can be changed with <a href="'.JRoute::_('index.php?option='.$option.a.'id='.$profile->get('uidNumber').a.'task=changepassword').'">this form</a>.').'</p>'.n;
		
		$mwconfig =& JComponentHelper::getParams( 'com_mw' );
		$enabled = $mwconfig->get('mw_on');
		if ($enabled) {
			$html .= t.t.'<p class="help">'.JText::_('Request for more storage or sessions may be made with <a href="'.JRoute::_('index.php?option='.$option.a.'id='.$profile->get('uidNumber').a.'task=raiselimit').'">this form</a>.').'</p>'.n;
		}
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.MembersHtml::hed(3, JText::_('Contact Information')).n;
		$html .= t.t.'<input type="hidden" name="id" value="'. $profile->get('uidNumber') .'" />'.n;
		$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$html .= t.t.'<input type="hidden" name="task" value="save" />'.n;

		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'<input type="checkbox" class="option" name="profile[public]" value="1"';
		if ($profile->get('public') == 1) { 
			$html .= ' checked="checked"';
		}
		$html .= '/>'.n;
		$html .= t.t.t.JText::_('Public profile (others may view your profile)').n;
		$html .= t.t.'</label>'.n;

		if ($registration->Fullname != REG_HIDE) {
			$required = ($registration->Fullname == REG_REQUIRED) ? ' <span class="required">'.JText::_('REQUIRED').'</span>' : '';
			$message = (!empty($xregistration->_invalid['name'])) ? MembersHtml::error($xregistration->_invalid['name']) : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';
			
			$html .= t.t.'<div class="threeup group">'.n;
			$html .= t.t.'<label'.$fieldclass.'>'.n;
			$html .= t.t.t.JText::_('FIRST_NAME').': '.$required.n;
			$html .= t.t.t.'<input type="text" name="name[first]" value="'. $givenName .'" />'.n;
			//$html .= ($errors['fname_msg']) ? t.t.t.$errors['fname_msg'].n : '';
			$html .= t.t.'</label>'.n;

			$html .= t.t.'<label>'.n;
			$html .= t.t.t.JText::_('MIDDLE_NAME').':'.n;
			$html .= t.t.t.'<input type="text" name="name[middle]" value="'. $middleName .'" />'.n;
			$html .= t.t.'</label>'.n;

			$html .= t.t.'<label'.$fieldclass.'>'.n;
			$html .= t.t.t.JText::_('LAST_NAME').': '.$required.n;
			$html .= t.t.t.'<input type="text" name="name[last]" value="'. $surname .'" />'.n;
			//$html .= ($errors['lname_msg']) ? t.t.t.$errors['lname_msg'].n : '';
			$html .= t.t.'</label>'.n;
			$html .= t.t.'</div>'.n;
			$html .= $message;
		}
		
		if ($registration->Email != REG_HIDE 
		 || $registration->ConfirmEmail != REG_HIDE) {
			$html .= t.t.'<div class="group twoup">'.n;
			
			// Email
			if ($registration->Email != REG_HIDE) {
				$required = ($registration->Email == REG_REQUIRED) ? '<span class="required">'.JText::_('required').'</span>' : '';
				$message = (!empty($xregistration->_invalid['email'])) ? MembersHtml::error($xregistration->_invalid['email'],'span') : '';
				$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';

				$html .= t.t.t.'<label '.$fieldclass.'>'.n;
				$html .= t.t.t.t.JText::_('Valid E-mail').': '.$required.n;
				$html .= t.t.t.t.'<input name="email" id="email" type="text" value="'.htmlentities($profile->get('email'),ENT_COMPAT,'UTF-8').'" />'.n;
				$html .= ($message) ? t.t.t.t.$message.n : '';
				$html .= t.t.t.'</label>'.n;
			}
			
			// Confirm email
			if ($registration->ConfirmEmail != REG_HIDE) {
				$message = '';
				$confirmEmail = $profile->get('email');
				if (!empty($xregistration->_invalid['email'])) {
					$confirmEmail = '';
				}
				if (!empty($xregistration->_invalid['confirmEmail'])) {
					$message = MembersHtml::error($xregistration->_invalid['confirmEmail'],'span');
				}
					
				$required = ($registration->ConfirmEmail == REG_REQUIRED) ? '<span class="required">'.JText::_('required').'</span>' : '';
				$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';
	
				$html .= t.t.t.'<label'.$fieldclass.'>'.n;
				$html .= t.t.t.t.JText::_('Confirm E-mail').': '.$required.n;
				$html .= t.t.t.t.'<input name="email2" id="email2" type="text" value="'.htmlentities($confirmEmail,ENT_COMPAT,'UTF-8').'" />'.n;
				$html .= ($message) ? t.t.t.t.$message.n : '';
				$html .= t.t.t.'</label>'.n;
			}

			$html .= t.t.'</div>'.n;
		
			if ($registration->Email != REG_HIDE) {
				$html .= t.t.MembersHtml::warning('Important! If you change your E-Mail address you <strong>must</strong> confirm receipt of the confirmation e-mail in order to re-activate your account.');
			}
		}
		
		if ($registration->URL != REG_HIDE) {
			$required = ($registration->URL == REG_REQUIRED) ? '<span class="required">'.JText::_('REQUIRED').'</span>' : '';
			$message = (!empty($xregistration->_invalid['web'])) ? MembersHtml::error($xregistration->_invalid['web']) : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';
			
			$html .= t.t.'<label'.$fieldclass.'>'.n;
			$html .= t.t.t.JText::_('WEBSITE').': '.$required.n;
			$html .= t.t.t.'<input type="text" name="web" value="'. stripslashes($profile->get('url')) .'" /></td>'.n;
			$html .= $message;
			$html .= t.t.'</label>'.n;
		}
		
		if ($registration->Phone != REG_HIDE) {
			$required = ($registration->Phone == REG_REQUIRED) ? '<span class="required">'.JText::_('REQUIRED').'</span>' : '';
			$message = (!empty($xregistration->_invalid['phone'])) ? MembersHtml::error($xregistration->_invalid['phone']) : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';
			
			$html .= t.t.'<label'.$fieldclass.'>'.n;
			$html .= t.t.t.JText::_('Phone').': '.$required.n;
			$html .= t.t.t.'<input type="text" name="phone" value="'. stripslashes($profile->get('phone')) .'" /></td>'.n;
			$html .= $message;
			$html .= t.t.'</label>'.n;
		}
		
		$html .= t.'</fieldset><div class="clear"></div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<h3>'.JText::_('Personal Information').'</h3>'.n;
		
		if ($registration->Employment != REG_HIDE) {
			$required = ($registration->Employment == REG_REQUIRED) ? '<span class="required">'.JText::_('REQUIRED').'</span>' : '';
			$message = (!empty($xregistration->_invalid['orgtype'])) ? MembersHtml::error($xregistration->_invalid['orgtype']) : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';
			
			$orgtype = stripslashes($profile->get('orgtype'));

			$html .= t.t.'<label'.$fieldclass.'>'.JText::_('Employment Status').': '.$required.n;
			$html .= t.t.'<select name="orgtype" id="orgtype">'.n;
			if (empty($orgtype)) {
				$html .= t.t.t.'<option value="" selected="selected">'.JText::_('(select from list)').'</option>'.n;
			}
			/*$html .= t.t.t.'<option value="university"';
			if ($orgtype == 'university') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('University / College Student or Staff').'</option>'.n;
			$html .= t.t.t.'<option value="precollege"';
			if ($orgtype == 'precollege') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('K-12 (Pre-College) Student or Staff').'</option>'.n;*/
			$html .= t.t.t.'<option value="nationallab"';
			if ($orgtype == 'nationallab') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('National Laboratory').'</option>'.n;
			$html .= t.t.t.'<option value="universityundergraduate"';
			if ($orgtype == 'universityundergraduate') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('University / College Undergraduate').'</option>'.n;
			$html .= t.t.t.'<option value="universitygraduate"';
			if ($orgtype == 'universitygraduate') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('University / College Graduate Student').'</option>'.n;
			$html .= t.t.t.'<option value="universityfaculty"';
			if ($orgtype == 'universityfaculty' || $orgtype == 'university') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('University / College Faculty').'</option>'.n;
			$html .= t.t.t.'<option value="universitystaff"';
			if ($orgtype == 'universitystaff') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('University / College Staff').'</option>'.n;
			$html .= t.t.t.'<option value="precollegestudent"';
			if ($orgtype == 'precollegestudent') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('K-12 (Pre-College) Student').'</option>'.n;
			$html .= t.t.t.'<option value="precollegefacultystaff"';
			if ($orgtype == 'precollege' || $orgtype == 'precollegefacultystaff') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('K-12 (Pre-College) Faculty/Staff').'</option>'.n;
			$html .= t.t.t.'<option value="industry"';
			if ($orgtype == 'industry') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('Industry / Private Company').'</option>'.n;
			$html .= t.t.t.'<option value="government"';
			if ($orgtype == 'government') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('Government Agency').'</option>'.n;
			$html .= t.t.t.'<option value="military"';
			if ($orgtype == 'military') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('Military').'</option>'.n;
			$html .= t.t.t.'<option value="unemployed"';
			if ($orgtype == 'unemployed') {
				$html .= ' selected="selected"';
			}
			$html .= '>'.JText::_('Retired / Unemployed').'</option>'.n;
			$html .= t.t.'</select>'.n;
			$html .= $message;
			$html .= t.t.t.'</label>'.n;
		}

		if ($registration->Organization != REG_HIDE) {
			$required = ($registration->Organization == REG_REQUIRED) ? '<span class="required">'.JText::_('REQUIRED').'</span>' : '';
			$message = (!empty($xregistration->_invalid['org'])) ? MembersHtml::error($xregistration->_invalid['org']) : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';
			
			$organization = stripslashes($profile->get('organization'));
			$orgtext = $organization;
			$org_known = 0;
			
			//$orgs = array();
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_hub'.DS.'xorganization.php' );
			$database =& JFactory::getDBO();
			$xo = new XOrganization( $database );
			$orgs = $xo->getOrgs();
			
			if (count($orgs) <= 0) {
				$orgs[0] = 'Purdue University';
				$orgs[1] = 'University of Pennsylvania';
				$orgs[2] = 'University of California at Berkeley';
				$orgs[3] = 'Vanderbilt University';
			}
			
			foreach ($orgs as $org) 
			{
				$org_known = ($org == $organization) ? 1 : 0;
			}
			
			$html .= t.t.'<label'.$fieldclass.'>'.n;
			$html .= t.t.t.JText::_('ORG').': '.$required.n;
			$html .= t.t.t.'<select name="org">'.n;
			$html .= t.t.t.t.' <option value=""';
			if (!$org_known) {
				$html .= ' selected="selected"';
			}
			$html .= '>';
			if ($org_known) {
				$html .= JText::_('(other / none)');
			} else {
				$html .= JText::_('(select from list or enter below)');
			}
			$html .= '</option>'.n;
			foreach ($orgs as $org) 
			{
				$html .= t.t.t.t.'<option value="'. htmlentities($org,ENT_COMPAT,'UTF-8') .'"';
				if ($org == $organization) {
					$orgtext = '';
					$html .= ' selected="selected"';
				}
				$html .= '>' . htmlentities($org) . '</option>'.n;
			}
			$html .= t.t.t.'</select>'.n;
			$html .= $message;
			$html .= t.t.'</label>'.n;
			$html .= t.t.'<label for="orgtext" id="orgtextlabel">'.JText::_('Enter organization below').'</label>'.n;
			$html .= t.t.'<input type="text" name="orgtext" id="orgtext" value="'. htmlentities($orgtext,ENT_COMPAT,'UTF-8') .'" />'.n;
		}
		
		if ($registration->Interests != REG_HIDE) {
			$required = ($registration->Interests == REG_REQUIRED) ? '<span class="required">'.JText::_('REQUIRED').'</span>' : '';
			$message = (!empty($xregistration->_invalid['interests'])) ? MembersHtml::error($xregistration->_invalid['interests']) : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';
			
			JPluginHelper::importPlugin( 'tageditor' );
			$dispatcher =& JDispatcher::getInstance();
			$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tags','actags','',$tags,'')) );
			
			$html .= t.t.'<label'.$fieldclass.'>'.n;
			$html .= t.t.t.JText::_('MEMBER_FIELD_TAGS').': '.$required.n;
			if (count($tf) > 0) {
				$html .= $tf[0];
			} else {
				$html .= t.t.t.'<input type="text" name="tags" value="'. $tags .'" />'.n;
			}
			$html .= t.t.t.'<span>'.JText::_('MEMBER_FIELD_TAGS_HINT').'</span>'.n;
			$html .= $message;
			$html .= t.t.'</label>'.n;
		}
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('BIO').':'.n;
		$html .= t.t.t.'<textarea name="profile[bio]" rows="10" cols="40">'. htmlentities(stripslashes($profile->get('bio')),ENT_COMPAT,'UTF-8') .'</textarea>'.n;
		$html .= t.t.t.'<span class="hint"><a href="'.JRoute::_('index.php?option=com_topics&scope=&pagename=Help:WikiFormatting').'">Wiki formatting</a> is allowed for Bios.</span>'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.'</fieldset><div class="clear"></div>'.n;
		
		if ($registration->Citizenship != REG_HIDE
		 || $registration->Residency != REG_HIDE
		 || $registration->Sex != REG_HIDE 
		 || $registration->Disability != REG_HIDE
		 || $registration->Hispanic != REG_HIDE
		 || $registration->Race != REG_HIDE) 
		{
			$html .= t.'<fieldset>'.n;
			$html .= t.t.'<h3>'.JText::_('Demographics').'</h3>'.n;
			
			if ($registration->Citizenship != REG_HIDE 
			 || $registration->Residency != REG_HIDE) {
				$countries = getcountries();
			}

			if ($registration->Citizenship != REG_HIDE) {
				$required = ($registration->Citizenship == REG_REQUIRED) ? ' <span class="required">'.JText::_('REQUIRED').'</span>' : '';
				$message = (!empty($xregistration->_invalid['countryorigin'])) ? MembersHtml::error($xregistration->_invalid['countryorigin']) : '';
				$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

				$countryorigin = $profile->get('countryorigin');

				$html .= t.t.'<fieldset'.$fieldclass.'>'.n;
				$html .= t.t.t.'<legend>'.JText::_('Are you a Legal Citizen or Permanent Resident of the <abbr title="United States">US</abbr>?').$required.'</legend>'.n;
				$html .= $message;
				$html .= t.t.t.'<label><input type="radio" class="option" name="corigin_us" id="corigin_usyes" value="yes"';
				if (strcasecmp($countryorigin,'US') == 0) {
					$html .= ' checked="checked"';
				}
				$html .= ' /> '.JText::_('Yes').'</label>'.n;
				$html .= t.t.t.t.'<label><input type="radio" class="option" name="corigin_us" id="corigin_usno" value="no"';
				if (!empty($countryorigin) && (strcasecmp($countryorigin,'US') != 0)) {
					$html .= ' checked="checked"';
				}
				$html .= ' /> '.JText::_('No').'</label>'.n;
				$html .= t.t.t.t.'<label>'.JText::_('Citizen or Permanent Resident of').':'.n;
				$html .= t.t.t.t.'<select name="corigin" id="corigin">'.n;
				if (!$countryorigin || $countryorigin == 'US') {
					$html .= t.t.t.t.' <option value="">'.JText::_('(select from list)').'</option>'.n;
				}
				foreach ($countries as $country) 
				{
					if ($country['code'] != 'US') {
						$html .= t.t.t.t.' <option value="' . $country['code'] . '"';
						if ($countryorigin == $country['code']) {
							$html .= ' selected="selected"';
						}
						$html .= '>' . htmlentities($country['name'],ENT_COMPAT,'UTF-8') . '</option>'.n;
					}
				}
				$html .= t.t.t.t.'</select></label>'.n;
				$html .= t.t.'</fieldset>'.n;
			}

			if ($registration->Residency != REG_HIDE) {
				$required = ($registration->Residency == REG_REQUIRED) ? ' <span class="required">'.JText::_('REQUIRED').'</span>' : '';
				$message = (!empty($xregistration->_invalid['countryresident'])) ? MembersHtml::error($xregistration->_invalid['countryresident']) : '';
				$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';

				$countryresident = $profile->get('countryresident');

				$html .= t.t.'<fieldset'.$fieldclass.'>';
				$html .= t.t.t.'<legend>'.JText::_('Do you Currently Live in the <abbr title="United States">US</abbr>?').$required.'</legend>'.n;
				$html .= $message;
				$html .= t.t.t.'<label><input type="radio" class="option" name="cresident_us" id="cresident_usyes" value="yes"';
				if (strcasecmp($countryresident,'US') == 0) {
					$html .= ' checked="checked"';
				}
				$html .= ' /> '.JText::_('Yes').'</label>'.n;
				$html .= t.t.t.t.'<label><input type="radio" class="option" name="cresident_us" id="cresident_usno" value="no"';
				if (!empty($countryresident) && strcasecmp($countryresident,'US') != 0) {
					$html .= ' checked="checked"';
				}
				$html .= ' /> '.JText::_('No').'</label>'.n;
				$html .= t.t.t.t.'<label>'.JText::_('Currently Living in').':'.n;
				$html .= t.t.t.t.'<select name="cresident" id="cresident">'.n;
				if (!$countryresident || strcasecmp($countryresident,'US') == 0) {
					$html .= t.t.t.t.t.' <option value="">'.JText::_('(select from list)').'</option>'.n;
				}
				foreach ($countries as $country) 
				{
					if (strcasecmp($country['code'],"US") != 0) {
						$html .= t.t.t.t.t.'<option value="' . $country['code'] . '"';
						if (strcasecmp($countryresident,$country['code']) == 0) {
							$html .= ' selected="selected"';
						}
						$html .= '>' . htmlentities($country['name'],ENT_COMPAT,'UTF-8') . '</option>'.n;
					}
				}
				$html .= t.t.t.t.'</select></label>'.n;
				$html .= t.t.'</fieldset>'.n;
			}
			
			if ($registration->Sex != REG_HIDE) {
				$message = (!empty($xregistration->_invalid['countryresident'])) ? MembersHtml::error($xregistration->_invalid['countryresident']) : '';
				$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';
				$required = ($registration->Sex == REG_REQUIRED) ? ' <span class="required">'.JText::_('REQUIRED').'</span>' : '';
				
				$html .= t.'<fieldset'.$fieldclass.'>'.n;
				$html .= $message;
				$html .= t.t.'<legend>'.JText::_('Sex').':'.$required.'</legend>'.n;
				$html .= t.t.'<input type="hidden" name="sex" value="unspecified" />'.n;
				$html .= t.t.'<label>'.MembersHtml::radio('sex','male','option',$profile->get('gender')).' '.JText::_('Male').'</label>'.n;
				$html .= t.t.'<label>'.MembersHtml::radio('sex','female','option',$profile->get('gender')).' '.JText::_('Female').'</label>'.n;
				$html .= t.t.'<label>'.MembersHtml::radio('sex','refused','option',$profile->get('gender')).' '.JText::_('Do not wish to reveal').'</label>'.n;
				$html .= t.'</fieldset>'.n;
			}

			// Disability
			if ($registration->Disability != REG_HIDE) {
				$message = (!empty($xregistration->_invalid['disability'])) ? MembersHtml::error($xregistration->_invalid['disability']) : '';
				$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';
				$required = ($registration->Disability == REG_REQUIRED) ? ' <span class="required">'.JText::_('REQUIRED').'</span>' : '';
				
				$disabilities = $profile->get('disability');
				if (!is_array($disabilities)) {
					$disabilities = array();
				}

				$disabilityyes = false;
				$disabilityother = '';
				foreach ($disabilities as $disabilityitem) 
				{
					if ($disabilityitem != 'no' 
					 && $disabilityitem != 'refused') {
						if (!$disabilityyes) {
							$disabilityyes = true;
						}

						if ($disabilityitem != 'blind' 
						 && $disabilityitem != 'deaf' 
						 && $disabilityitem != 'physical' 
						 && $disabilityitem != 'learning' 
						 && $disabilityitem != 'vocal' 
						 && $disabilityitem != 'yes') {
							$disabilityother = $disabilityitem;
						}
					}
				}

				$html .= t.t.'<fieldset'.$fieldclass.'>'.n;
				$html .= $message;
				$html .= t.t.t.'<legend>'.JText::_('Disability').':'.$required.'</legend>'.n;
				$html .= t.t.t.t.'<label><input type="radio" class="option" name="disability" id="disabilityyes" value="yes"';
				if ($disabilityyes) {
					$html .= ' checked="checked"';
				}
				$html .= ' /> '.JText::_('Yes').'</label>'.n;
				$html .= t.t.t.'<fieldset>'.n;
				$html .= t.t.t.t.'<label><input type="checkbox" class="option" name="disabilityblind" id="disabilityblind" ';
				if (in_array('blind', $disabilities)) {
					$html .= 'checked="checked" ';
				}
				$html .= '/> '.JText::_('Blind / Visually Impaired').'</label>'.n;
				$html .= t.t.t.t.'<label><input type="checkbox" class="option" name="disabilitydeaf" id="disabilitydeaf" ';
				if (in_array('deaf', $disabilities)) {
					$html .= 'checked="checked" ';
				}
				$html .= '/> '.JText::_('Deaf / Hard of Hearing').'</label>'.n;
				$html .= t.t.t.t.'<label><input type="checkbox" class="option" name="disabilityphysical" id="disabilityphysical" ';
				if (in_array('physical', $disabilities)) {
					$html .= 'checked="checked" ';
				}
				$html .= '/> '.JText::_('Physical / Orthopedic Disability').'</label>'.n;
				$html .= t.t.t.t.'<label><input type="checkbox" class="option" name="disabilitylearning" id="disabilitylearning" ';
				if (in_array('learning', $disabilities)) {
					$html .= 'checked="checked" ';
				}
				$html .= '/> '.JText::_('Learning / Cognitive Disability').'</label>'.n;
				$html .= t.t.t.t.'<label><input type="checkbox" class="option" name="disabilityvocal" id="disabilityvocal" ';
				if (in_array('vocal', $disabilities)) {
					$html .= 'checked="checked" ';
				}
				$html .= '/> '.JText::_('Vocal / Speech Disability').'</label>'.n;
				$html .= t.t.t.t.'<label>'.JText::_('Other (please specify)').':'.n;
				$html .= t.t.t.t.'<input name="disabilityother" id="disabilityother" type="text" value="'. htmlentities($disabilityother,ENT_COMPAT,'UTF-8') .'" /></label>'.n;
				$html .= t.t.t.'</fieldset>'.n;
				$html .= t.t.t.'<label><input type="radio" class="option" name="disability" id="disabilityno" value="no"';
				if (in_array('no', $disabilities)) {
					$html .= ' checked="checked"';
				}
				$html .= '> '.JText::_('No (none)').'</label>'.n;
				$html .= t.t.t.'<label><input type="radio" class="option" name="disability" id="disabilityrefused" value="refused"';
				if (in_array('refused', $disabilities)) {
					$html .= ' checked="checked"';
				}
				$html .= '> '.JText::_('Do not wish to reveal').'</label>'.n;
				$html .= t.t.'</fieldset>'.n;
			}

			// Hispanic
			if ($registration->Hispanic != REG_HIDE) {
				$message = (!empty($xregistration->_invalid['hispanic'])) ? MembersHtml::error($xregistration->_invalid['hispanic']) : '';
				$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';
				$required = ($registration->Hispanic == REG_REQUIRED) ? ' <span class="required">'.JText::_('REQUIRED').'</span>' : '';
				
				$hispanic = $profile->get('hispanic');
				if (!is_array($hispanic)) {
					$hispanic = array();
				}

				$hispanicyes = false;
				$hispanicother = '';
				foreach ($hispanic as $hispanicitem) 
				{
					if ($hispanicitem != 'no' 
					 && $hispanicitem != 'refused') {
						if (!$hispanicyes) {
							$hispanicyes = true;
						}

						if ($hispanicitem != 'cuban' 
						 && $hispanicitem != 'mexican' 
						 && $hispanicitem != 'puertorican') {
							$hispanicother = $hispanicitem;
						}
					}
				}

				$html .= t.t.'<fieldset'.$fieldclass.'>'.n;
				$html .= $message;
				$html .= t.t.t.'<legend>'.JText::_('Hispanic or Latino').':'.$required.'</legend>'.n;
				$html .= t.t.t.t.'<label><input type="radio" class="option" name="hispanic" id="hispanicyes" value="yes" ';
				if ($hispanicyes) {
					$html .= 'checked="checked"';
				}
				$html .= ' /> '.JText::_('Yes (Hispanic Origin or Descent)').'</label>'.n;
				$html .= t.t.t.'<fieldset>'.n;
				$html .= t.t.t.t.'<label><input type="checkbox" class="option" name="hispaniccuban" id="hispaniccuban" ';
				if (in_array('cuban', $hispanic)) {
					$html .= 'checked="checked" ';
				}
				$html .= '/> '.JText::_('Cuban').'</label>'.n;
				$html .= t.t.t.t.'<label><input type="checkbox" class="option" name="hispanicmexican" id="hispanicmexican" ';
				if (in_array('mexican', $hispanic)) {
					$html .= 'checked="checked" ';
				}
				$html .= '/> '.JText::_('Mexican American or Chicano').'</label>'.n;
				$html .= t.t.t.t.'<label><input type="checkbox" class="option" name="hispanicpuertorican" id="hispanicpuertorican" ';
				if (in_array('puertorican', $hispanic)) {
					$html .= 'checked="checked" ';
				}
				$html .= '/> '.JText::_('Puerto Rican').'</label>'.n;
				$html .= t.t.t.t.'<label>'.JText::_('Other Hispanic or Latino').':'.n;
				$html .= t.t.t.t.'<input name="profile[hispanic][other]" id="hispanicother" type="text" value="'. htmlentities($hispanicother,ENT_COMPAT,'UTF-8') .'" /></label>'.n;
				$html .= t.t.t.'</fieldset>'.n;
				$html .= t.t.t.'<label><input type="radio" class="option" name="hispanic" id="hispanicno" value="no"';
				if (in_array('no', $hispanic)) {
					$html .= ' checked="checked"';
				}
				$html .= '> '.JText::_('No (not Hispanic or Latino)').'</label>'.n;
				$html .= t.t.t.'<label><input type="radio" class="option" name="hispanic" id="hispanicrefused" value="refused"';
				if (in_array('refused', $hispanic)) {
					$html .= ' checked="checked"';
				}
				$html .= '> '.JText::_('Do not wish to reveal').'</label>'.n;
				$html .= t.t.'</fieldset>'.n;
			}

			// Race
			if ($registration->Race != REG_HIDE) {
				$message = (!empty($xregistration->_invalid['race'])) ? MembersHtml::error($xregistration->_invalid['race']) : '';
				$fieldclass = ($message) ? ' class="fieldsWithErrors"' : '';
				$required = ($registration->Race == REG_REQUIRED) ? ' <span class="required">'.JText::_('REQUIRED').'</span>' : '';
				
				$race = $profile->get('race');
				if (!is_array($race)) {
					$race = array();
				}

				$html .= t.t.'<fieldset'.$fieldclass.'>'.n;
				$html .= $message;
				$html .= t.t.t.'<legend>'.JText::_('Racial Background').':'.$required.'</legend>'.n;
				$html .= t.t.t.'<p class="hint">'.JText::_('Select one or more that apply.').'</p>'.n;

				$html .= t.t.t.'<label><input type="checkbox" class="option" name="racenativeamerican" id="racenativeamerican" value="nativeamerican" ';
				if (in_array('nativeamerican', $race)) {
					$html .= 'checked="checked" ';
				}
				$html .= '/> '.JText::_('American Indian or Alaska Native').'</label>'.n;
				$html .= t.t.t.'<label class="indent">'.JText::_('Tribal Affiliation(s)').':'.n;
				$html .= t.t.t.'<input name="profile[nativetribe]" id="racenativetribe" type="text" value="'. htmlentities($profile->get('nativeTribe'),ENT_COMPAT,'UTF-8') .'" /></label>'.n;
				$html .= t.t.t.'<label><input type="checkbox" class="option" name="raceasian" id="raceasian" ';
				if (in_array('asian', $race)) {
					$html .= 'checked="checked" ';
				}
				$html .= '/> '.JText::_('Asian').'</label>'.n;
				$html .= t.t.t.'<label><input type="checkbox" class="option" name="raceblack" id="raceblack" ';
				if (in_array('black', $race)) {
					$html .= 'checked="checked" ';
				}
				$html .= '/> '.JText::_('Black or African American').'</label>'.n;
				$html .= t.t.t.'<label><input type="checkbox" class="option" name="racehawaiian" id="racehawaiian" ';
				if (in_array('hawaiian', $race)) {
					$html .= 'checked="checked" ';
				}
				$html .= '/> '.JText::_('Native Hawaiian or Other Pacific Islander').'</label>'.n;
				$html .= t.t.t.'<label><input type="checkbox" class="option" name="racewhite" id="racewhite" ';
				if (in_array('white', $race)) {
					$html .= 'checked="checked" ';
				}
				$html .= '/> '.JText::_('White').'</label>'.n;
				$html .= t.t.t.'<label><input type="checkbox" class="option" name="racerefused" id="racerefused" ';
				if (in_array('refused', $race)) {
					$html .= 'checked="checked" ';
				}
				$html .= '/> '.JText::_('Do not wish to reveal').'</label>'.n;
				$html .= t.t.'</fieldset>'.n;
			}
			
			$html .= t.'</fieldset><div class="clear"></div>'.n;
		}
		
		if ($registration->OptIn != REG_HIDE) // newsletter Opt-In
		{
			$required = ($registration->OptIn == REG_REQUIRED) ? '<span class="required">'.JText::_('required').'</span>' : '';
			$message = (!empty($xregistration->_invalid['mailPreferenceOption'])) ? MembersHtml::error($xregistration->_invalid['mailPreferenceOption']) : '';
			$fieldclass = ($message) ? ' class="fieldWithErrors"' : '';
			
			$html .= t.'<fieldset>'.n;
			$html .= t.t.'<h3>'.JText::_('Updates').'</h3>'.n;
			$html .= t.t.'<input type="hidden" name="mailPreferenceOption" value="unset" />'.n;
			$html .= t.t.'<label '.$fieldclass.'><input type="checkbox" class="option" id="mailPreferenceOption" name="mailPreferenceOption" value="1" ';
			if ($profile->get('mailPreferenceOption')) {
				$html .= 'checked="checked" ';
			}
			$html .= '/> '.$required.' '.JText::_('Yes, I would like to receive newsletters and other updates by e-mail.').'</label>'.n;
			$html .= $message;
			$html .= t.'</fieldset><div class="clear"></div>'.n;
		}

		$html .= t.'<fieldset>'.n;
		$html .= t.t.MembersHtml::hed(3, JText::_('MEMBER_PICTURE')).n;
		$html .= t.t.'<iframe width="100%" height="350" border="0" name="filer" id="filer" src="index.php?option='.$option.a.'no_html=1'.a.'task=img'.a.'file='.stripslashes($profile->get('picture')).a.'id='.$profile->get('uidNumber').'"></iframe>'.n;
		$html .= t.'</fieldset><div class="clear"></div>'.n;

		$html .= t.'<p class="submit"><input type="submit" name="submit" value="'.JText::_('SAVE').'" /></p>'.n;
		$html .= '</form>'.n;
		$html .= '</div>'.n;
		
		return $html;
	}

	public function radio($name, $value, $class='', $checked='', $id='')
	{
		$o = '<input type="radio" name="'.$name.'" value="'.$value.'"';
		$o .= ($id) ? ' id="'.$id.'"' : '';
		$o .= ($class) ? ' class="'.$class.'"' : '';
		$o .= ($checked==$value) ? ' checked="checked"' : '';
		$o .= ' />';
		return $o;
	}

	//-----------

	public function xhtml($text)
	{
		$text = stripslashes($text);
		$text = str_replace('&amp;','&',$text);
		$text = str_replace('&','&amp;',$text);
		$text = str_replace('"','&quot;',$text);
		return $text;
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
	
	public function writeImage( $app, $option, $webpath, $default_picture, $path, $file, $file_path, $id, $errors=array() )
	{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php echo JText::_('MEMBER_PICTURE'); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<style type="text/css" media="screen">@import url(templates/<?php echo $app->getTemplate(); ?>/css/main.css);</style>
<?php
	if (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$option.DS.'members.css')) {
		echo '<link rel="stylesheet" href="'.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$option.DS.'members.css" type="text/css" />'.n;
	} else {
		echo '<link rel="stylesheet" href="'.DS.'components'.DS.$option.DS.'members.css" type="text/css" />'.n;
	}
?>
 </head>
 <body id="member-picture">
 	<form action="index.php" method="post" enctype="multipart/form-data" name="filelist" id="filelist">
		<fieldset>
			<legend><?php echo JText::_('UPLOAD'); ?> <?php echo JText::_('WILL_REPLACE_EXISTING_IMAGE'); ?></legend>
			
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="no_html" value="1" />
			<input type="hidden" name="task" value="upload" />
			<input type="hidden" name="id" value="<?php echo $id; ?>" />
			
			<input type="file" name="upload" id="upload" size="17" /> 
			<input type="submit" value="<?php echo JText::_('UPLOAD'); ?>" />
		</fieldset>
		
<?php
	if (count($errors) > 0) {
		echo MembersHtml::error( implode('<br />',$errors) ).n;
	}
?>
		
		<table>
			<caption><label for="image"><?php echo JText::_('MEMBER_PICTURE'); ?></label></caption>
			<tbody>
<?php
	$k = 0;

	if ($file && file_exists( $file_path.DS.$file )) {
		$this_size = filesize($file_path.DS.$file);
		list($width, $height, $type, $attr) = getimagesize($file_path.DS.$file);
?>
				<tr>
					<td rowspan="6"><img src="<?php echo $webpath.DS.$path.DS.$file; ?>" alt="<?php echo JText::_('MEMBER_PICTURE'); ?>" id="conimage" /></td>
					<td><?php echo JText::_('FILE'); ?>:</td>
					<td><?php echo $file; ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('SIZE'); ?>:</td>
					<td><?php echo FileUploadUtils::formatsize($this_size); ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('WIDTH'); ?>:</td>
					<td><?php echo $width; ?> px</td>
				</tr>
				<tr>
					<td><?php echo JText::_('HEIGHT'); ?>:</td>
					<td><?php echo $height; ?> px</td>
				</tr>
				<tr>
					<td><input type="hidden" name="currentfile" value="<?php echo $file; ?>" /></td>
					<td><a href="index.php?option=<?php echo $option; ?>&amp;no_html=1&amp;task=deleteimg&amp;file=<?php echo $file; ?>&amp;id=<?php echo $id; ?>">[ <?php echo JText::_('DELETE'); ?> ]</a></td>
				</tr>
<?php } else { ?>
				<tr>
					<td colspan="4">
						<img src="<?php echo $default_picture; ?>" alt="<?php echo JText::_('NO_MEMBER_PICTURE'); ?>" />
						<input type="hidden" name="currentfile" value="" />
					</td>
				</tr>
<?php } ?>
			</tbody>
		</table>
   </form>
 </body>
</html>
<?php
	}
}
?>
