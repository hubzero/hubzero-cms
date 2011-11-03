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

$juser =& JFactory::getUser();
$loggedin = false;
if (!$juser->get('guest')) {
	$loggedin = true;
}

$html  = '<h3>'.JText::_('PROFILE').'</h3>'."\n";
$html .= '<div class="aside">'."\n";
$html .= "\t".'<div class="metadata">'."\n";
foreach ($this->sections as $section)
{
	$html .= (isset($section['metadata'])) ? $section['metadata'] : '';
}
$html .= "\t".'</div><!-- / .metadata -->'."\n";
if ($this->profile->get('picture')) {
	list($width,$height) = getimagesize(JPATH_ROOT.$this->profile->get('picture'));
	$name = $this->profile->get('name');
	$is_generic_image = preg_match('/profile.(?:gif|png)$/', $this->profile->get('picture'));
	$html .= "\t".'<p class="portrait userImage"><img class="photo" src="'.$this->profile->get('picture').'"';
	$html .= ($width && $width > 190) ? ' width="190"' : '';
	if (!$is_generic_image)
		$html .= ' title="'.htmlentities($name).'"';
	$html .= ' alt="'.($is_generic_image ? JText::_('MEMBER_PICTURE') : htmlentities($name)).'" /></p>'."\n";
	if ($this->authorized) {
		$html .= "\t".'<p><a href="'.JRoute::_('index.php?option='.$this->option.'&task=edit&id='.$this->profile->get('uidNumber')).'">'.JText::_('MEMBERS_UPLOAD_IMAGE').'</a></p>'."\n";
	}
}
$html .= '</div>'."\n";
$html .= '<div class="subject">'."\n";
if ($this->authorized) {
	$html .= '<form method="post" action="'.JRoute::_('index.php?option='.$this->option.'&id='.$this->profile->get('uidNumber')).'">'."\n";
}
if ($this->profile->get('public') != 1) {
	$msg = JText::_('MEMBERS_NOT_PUBLIC');
	if ($this->authorized) {
		$msg .= ' <a class="edit-member" href="'.JRoute::_('index.php?option='.$this->option.'&task=edit&id='. $this->profile->get('uidNumber')) .'">'.JText::_('Edit this profile').'</a>';
	}
	$html .= "\t".'<p class="locked">'. $msg .'</p>'."\n";
}
$html .= "\t".'<table class="profile" summary="'.JText::_('PROFILE_TBL_SUMMARY').'">'."\n";
if ($this->authorized) {
	$html .= "\t\t".'<tfoot>'."\n";
	$html .= "\t\t\t".'<tr>'."\n";
	$html .= "\t\t\t\t".'<td> </td>'."\n";
	$html .= "\t\t\t\t".'<td> </td>'."\n";
	$html .= "\t\t\t\t".'<td><input type="submit" value="'.JText::_('Save changes').'"/></td>'."\n";
	$html .= "\t\t\t".'</tr>'."\n";
	$html .= "\t\t".'</tfoot>'."\n";
}
$html .= "\t\t".'<tbody>'."\n";

if ($this->authorized) {
	$html .= "\t\t\t".'<tr class="private">'."\n";
	$html .= "\t\t\t\t".'<th>'.JText::_('Password').'</th>'."\n";
	$html .= "\t\t\t\t".'<td colspan="2">'.JText::_('Passwords can be changed with <a href="'.JRoute::_('index.php?option='.$this->option.a.'id='.$this->profile->get('uidNumber').a.'task=changepassword').'">this form</a>.').'</td>'."\n";
	$html .= "\t\t\t".'</tr>'."\n";
}

if ($this->registration->Organization != REG_HIDE) {
	if ($this->params->get('access_org') == 0
	 || ($this->params->get('access_org') == 1 && $loggedin)
	 || ($this->params->get('access_org') == 2 && $this->authorized)
	) {
		$html .= "\t\t\t".'<tr';
		$html .= ($this->params->get('access_org') == 2) ? ' class="private"' : '';
		$html .= '>'."\n";
		$html .= "\t\t\t\t".'<th>'.JText::_('COL_ORGANIZATION').'</th>'."\n";
		$html .= "\t\t\t\t".'<td><span class="org">'.Hubzero_View_Helper_Html::xhtml(stripslashes($this->profile->get('organization'))).'</span></td>'."\n";
		if ($this->authorized) {
			$html .= "\t\t\t\t".'<td>'.MembersHtml::selectAccess('access[org]',$this->params->get('access_org')).'</td>'."\n";
		}
		$html .= "\t\t\t".'</tr>'."\n";
	}
}

if ($this->registration->Employment != REG_HIDE) {
	if ($this->params->get('access_orgtype') == 0
	 || ($this->params->get('access_orgtype') == 1 && $loggedin)
	 || ($this->params->get('access_orgtype') == 2 && $this->authorized)
	) {
		$html .= "\t\t\t".'<tr';
		$html .= ($this->params->get('access_orgtype') == 2) ? ' class="private"' : '';
		$html .= '>'."\n";
		$html .= "\t\t\t\t".'<th>'.JText::_('Employment Status').'</th>'."\n";
		$html .= "\t\t\t\t".'<td><span class="userType">';

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_register' . DS . 'tables' . DS . 'organizationtype.php');
		$database =& JFactory::getDBO();
		$rot = new RegisterOrganizationType($database);

		if ($rot->loadType($this->profile->get('orgtype'))) {
			$html .= stripslashes($rot->title);
		} else {
			$html .= htmlentities($this->profile->get('orgtype'), ENT_COMPAT, 'UTF-8');
		}
		$html .= '</span></td>'."\n";
		if ($this->authorized) {
			$html .= "\t\t\t\t".'<td>'.MembersHtml::selectAccess('access[orgtype]',$this->params->get('access_orgtype')).'</td>'."\n";
		}
		$html .= "\t\t\t".'</tr>'."\n";
	}
}

if ($this->profile->get('email')) {
	if ($this->params->get('access_email') == 0
	 || ($this->params->get('access_email') == 1 && $loggedin)
	 || ($this->params->get('access_email') == 2 && $this->authorized)
	) {
		$html .= "\t\t\t".'<tr';
		$html .= ($this->params->get('access_email') == 2) ? ' class="private"' : '';
		$html .= '>'."\n";
		$html .= "\t\t\t\t".'<th>'.JText::_('E-mail').'</th>'."\n";
		$html .= "\t\t\t\t".'<td><a class="email" href="mailto:'.MembersHtml::obfuscate($this->profile->get('email')).'" rel="nofollow">'. MembersHtml::obfuscate($this->profile->get('email')).'</a></td>';
		if ($this->authorized) {
			$html .= "\t\t\t\t".'<td>'.MembersHtml::selectAccess('access[email]',$this->params->get('access_email')).'</td>'."\n";
		}
		$html .= "\t\t\t".'</tr>'."\n";
	}
}

if ($this->registration->URL != REG_HIDE) {
	if ($this->params->get('access_url') == 0
	 || ($this->params->get('access_url') == 1 && $loggedin)
	 || ($this->params->get('access_url') == 2 && $this->authorized)
	) {
		$url = stripslashes($this->profile->get('url'));
		if ($url) {
			$href = '<a class="url" href="'.$url.'">'.$url.'</a>';
		} else {
			$href = JText::_('None');
		}

		$html .= "\t\t\t".'<tr';
		$html .= ($this->params->get('access_url') == 2) ? ' class="private"' : '';
		$html .= '>'."\n";
		$html .= "\t\t\t\t".'<th>'.JText::_('COL_WEBSITE').'</th>'."\n";
		$html .= "\t\t\t\t".'<td>'.$href.'</td>'."\n";
		if ($this->authorized) {
			$html .= "\t\t\t\t".'<td>'.MembersHtml::selectAccess('access[url]',$this->params->get('access_url')).'</td>'."\n";
		}
		$html .= "\t\t\t".'</tr>'."\n";
	}
}

if ($this->registration->Phone != REG_HIDE) {
	if ($this->params->get('access_phone') == 0
	 || ($this->params->get('access_phone') == 1 && $loggedin)
	 || ($this->params->get('access_phone') == 2 && $this->authorized)
	) {
		$phone = htmlentities($this->profile->get('phone'),ENT_COMPAT,'UTF-8');
		$phone = ($phone) ? $phone : JText::_('None');

		$html .= "\t\t\t".'<tr';
		$html .= ($this->params->get('access_phone') == 2) ? ' class="private"' : '';
		$html .= '>'."\n";
		$html .= "\t\t\t\t".'<th>'.JText::_('Telephone').'</th>'."\n";
		$html .= "\t\t\t\t".'<td><span class="phone">'. $phone .'</span></td>'."\n";
		if ($this->authorized) {
			$html .= "\t\t\t\t".'<td>'.MembersHtml::selectAccess('access[phone]',$this->params->get('access_phone')).'</td>'."\n";
		}
		$html .= "\t\t\t".'</tr>'."\n";
	}
}

if ($this->params->get('access_bio') == 0
 || ($this->params->get('access_bio') == 1 && $loggedin)
 || ($this->params->get('access_bio') == 2 && $this->authorized)
) {
	if ($this->profile->get('bio')) {
		// Transform the wikitext to HTML
		$wikiconfig = array(
			'option'   => $this->option,
			'scope'    => 'members'.DS.'profile',
			'pagename' => 'member',
			'pageid'   => 0,
			'filepath' => '',
			'domain'   => ''
		);
		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();
		$bio = $p->parse(stripslashes($this->profile->get('bio')), $wikiconfig);
	} else {
		$bio = JText::_('NO_BIOGRAPHY');
	}
	$html .= "\t\t\t".'<tr';
	$html .= ($this->params->get('access_bio') == 2) ? ' class="private"' : '';
	$html .= '>'."\n";
	$html .= "\t\t\t\t".'<th>'.JText::_('COL_BIOGRAPHY').'</th>'."\n";
	$html .= "\t\t\t\t".'<td>'.$bio.'</td>'."\n";
	if ($this->authorized) {
		$html .= "\t\t\t\t".'<td>'.MembersHtml::selectAccess('access[bio]',$this->params->get('access_bio')).'</td>'."\n";
	}
	$html .= "\t\t\t".'</tr>'."\n";
}

if ($this->registration->Interests != REG_HIDE) {
	if ($this->params->get('access_tags') == 0
	 || ($this->params->get('access_tags') == 1 && $loggedin)
	 || ($this->params->get('access_tags') == 2 && $this->authorized)
	) {
		$database =& JFactory::getDBO();
		$mt = new MembersTags( $database );
		$tags = $mt->get_tag_cloud(0,0,$this->profile->get('uidNumber'));
		if (!$tags) {
			$tags = JText::_('None');
		}
		$html .= "\t\t\t".'<tr';
		$html .= ($this->params->get('access_tags') == 2) ? ' class="private"' : '';
		$html .= '>'."\n";
		$html .= "\t\t\t\t".'<th>'.JText::_('COL_INTERESTS').'</th>'."\n";
		$html .= "\t\t\t\t".'<td>'.$tags.'</td>'."\n";
		if ($this->authorized) {
			$html .= "\t\t\t\t".'<td>'.MembersHtml::selectAccess('access[tags]',$this->params->get('access_tags')).'</td>'."\n";
		}
		$html .= "\t\t\t".'</tr>'."\n";
	}
}

if ($this->registration->Citizenship != REG_HIDE) {
	if ($this->params->get('access_countryorigin') == 0
	 || ($this->params->get('access_countryorigin') == 1 && $loggedin)
	 || ($this->params->get('access_countryorigin') == 2 && $this->authorized)
	) {
		$img = '';
		if (is_file(JPATH_ROOT.DS.'components'.DS.$this->option.DS.'images'.DS.'flags'.DS.strtolower($this->profile->get('countryorigin')).'.gif')) {
			$img = '<img src="/components/'.$this->option.'/images/flags/'.strtolower($this->profile->get('countryorigin')).'.gif" alt="'.$this->profile->get('countryorigin').' '.JText::_('flag').'" /> ';
		}
		$html .= "\t\t\t".'<tr';
		$html .= ($this->params->get('access_countryorigin') == 2) ? ' class="private"' : '';
		$html .= '>'."\n";
		$html .= "\t\t\t\t".'<th>'.JText::_('Citizenship').'</th>'."\n";
		$html .= "\t\t\t\t".'<td><span class="country '.strtolower($this->profile->get('countryorigin')).'">'.$img. htmlentities($this->profile->get('countryorigin'),ENT_COMPAT,'UTF-8') .'</span></td>'."\n";
		if ($this->authorized) {
			$html .= "\t\t\t\t".'<td>'.MembersHtml::selectAccess('access[countryorigin]',$this->params->get('access_countryorigin')).'</td>'."\n";
		}
		$html .= "\t\t\t".'</tr>'."\n";
	}
}
if ($this->registration->Residency != REG_HIDE) {
	if ($this->params->get('access_countryresident') == 0
	 || ($this->params->get('access_countryresident') == 1 && $loggedin)
	 || ($this->params->get('access_countryresident') == 2 && $this->authorized)
	) {
		$img = '';
		if (is_file(JPATH_ROOT.DS.'components'.DS.$this->option.DS.'images'.DS.'flags'.DS.strtolower($this->profile->get('countryresident')).'.gif')) {
			$img = '<img src="/components/'.$this->option.'/images/flags/'.strtolower($this->profile->get('countryresident')).'.gif" alt="'.$this->profile->get('countryresident').' '.JText::_('flag').'" /> ';
		}
		$html .= "\t\t\t".'<tr';
		$html .= ($this->params->get('access_countryresident') == 2) ? ' class="private"' : '';
		$html .= '>'."\n";
		$html .= "\t\t\t\t".'<th>'.JText::_('Residence').'</th>'."\n";
		$html .= "\t\t\t\t".'<td><span class="country '.strtolower($this->profile->get('countryresident')).'">'.$img. htmlentities($this->profile->get('countryresident'),ENT_COMPAT,'UTF-8') .'</span></td>'."\n";
		if ($this->authorized) {
			$html .= "\t\t\t\t".'<td>'.MembersHtml::selectAccess('access[countryresident]',$this->params->get('access_countryresident')).'</td>'."\n";
		}
		$html .= "\t\t\t".'</tr>'."\n";
	}
}
if ($this->registration->Sex != REG_HIDE) {
	if ($this->params->get('access_gender') == 0
	 || ($this->params->get('access_gender') == 1 && $loggedin)
	 || ($this->params->get('access_gender') == 2 && $this->authorized)
	) {
		$html .= "\t\t\t".'<tr';
		$html .= ($this->params->get('access_gender') == 2) ? ' class="private"' : '';
		$html .= '>'."\n";
		$html .= "\t\t\t\t".'<th>'.JText::_('Sex').'</th>'."\n";
		$html .= "\t\t\t\t".'<td>'. MembersHtml::propercase_singleresponse($this->profile->get('gender')) .'</td>'."\n";
		if ($this->authorized) {
			$html .= "\t\t\t\t".'<td>'.MembersHtml::selectAccess('access[gender]',$this->params->get('access_gender')).'</td>'."\n";
		}
		$html .= "\t\t\t".'</tr>'."\n";
	}
}
if ($this->registration->Disability != REG_HIDE) {
	$dis = MembersHtml::propercase_multiresponse($this->profile->get('disability'));
	if ($dis) {
		if ($this->params->get('access_disability') == 0
		 || ($this->params->get('access_disability') == 1 && $loggedin)
		 || ($this->params->get('access_disability') == 2 && $this->authorized)
		) {
			$html .= "\t\t\t".'<tr';
			$html .= ($this->params->get('access_disability') == 2) ? ' class="private"' : '';
			$html .= '>'."\n";
			$html .= "\t\t\t\t".'<th>'.JText::_('Disability').'</th>'."\n";
			$html .= "\t\t\t\t".'<td>'. $dis .'</td>'."\n";
			if ($this->authorized) {
				$html .= "\t\t\t\t".'<td>'.MembersHtml::selectAccess('access[disability]',$this->params->get('access_disability')).'</td>'."\n";
			}
			$html .= "\t\t\t".'</tr>'."\n";
		}
	}
}
if ($this->registration->Hispanic != REG_HIDE) {
	$his = MembersHtml::propercase_multiresponse($this->profile->get('hispanic'));
	if ($his) {
		if ($this->params->get('access_hispanic') == 0
		 || ($this->params->get('access_hispanic') == 1 && $loggedin)
		 || ($this->params->get('access_hispanic') == 2 && $this->authorized)
		) {
			$html .= "\t\t\t".'<tr';
			$html .= ($this->params->get('access_hispanic') == 2) ? ' class="private"' : '';
			$html .= '>'."\n";
			$html .= "\t\t\t\t".'<th>'.JText::_('Hispanic Heritage').'</th>'."\n";
			$html .= "\t\t\t\t".'<td>'. $his .'</td>'."\n";
			if ($this->authorized) {
				$html .= "\t\t\t\t".'<td>'.MembersHtml::selectAccess('access[hispanic]',$this->params->get('access_hispanic')).'</td>'."\n";
			}
			$html .= "\t\t\t".'</tr>'."\n";
		}
	}
}
if ($this->registration->Race != REG_HIDE) {
	$rac = MembersHtml::propercase_multiresponse($this->profile->get('race'));
	if ($rac) {
		if ($this->params->get('access_race') == 0
		 || ($this->params->get('access_race') == 1 && $loggedin)
		 || ($this->params->get('access_race') == 2 && $this->authorized)
		) {
			$html .= "\t\t\t".'<tr';
			$html .= ($this->params->get('access_race') == 2) ? ' class="private"' : '';
			$html .= '>'."\n";
			$html .= "\t\t\t\t".'<th>'.JText::_('Racial Background').'</th>'."\n";
			$html .= "\t\t\t\t".'<td>'. $rac .'</td>'."\n";
			if ($this->authorized) {
				$html .= "\t\t\t\t".'<td>'.MembersHtml::selectAccess('access[race]',$this->params->get('access_race')).'</td>'."\n";
			}
			$html .= "\t\t\t".'</tr>'."\n";
		}
	}
}
if ($this->registration->OptIn != REG_HIDE) {
	if ($this->params->get('access_optin') == 0
	 || ($this->params->get('access_optin') == 1 && $loggedin)
	 || ($this->params->get('access_optin') == 2 && $this->authorized)
	) {
		$html .= "\t\t\t".'<tr';
		$html .= ($this->params->get('access_optin') == 2) ? ' class="private"' : '';
		$html .= '>'."\n";
		$html .= "\t\t\t\t".'<th>'.JText::_('E-mail Updates').'</th>'."\n";
		$html .= "\t\t\t\t".'<td>';
		$html .= ($this->profile->get('mailPreferenceOption')) ? JText::_('Yes') : JText::_('No');
		$html .= '</td>'."\n";
		if ($this->authorized) {
			$html .= "\t\t\t\t".'<td>'.MembersHtml::selectAccess('access[optin]',$this->params->get('access_optin')).'</td>'."\n";
		}
		$html .= "\t\t\t".'</tr>'."\n";
	}
}
$html .= "\t\t".'</tbody>'."\n";
$html .= "\t".'</table>'."\n";
if ($this->authorized) {
	$html .= '<input type="hidden" name="task" value="saveaccess" />';
	$html .= '</form>'."\n";
}
$html .= '</div>'."\n";
$html .= '<div class="clear"></div>'."\n";

echo $html;
?>