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

/* Edit Wish List Settings */

		$wishlist = $this->wishlist;
		$title = $this->title;
		$option = $this->option;
		$admin = $this->admin;
		$error = $this->getError();
		$juser = $this->juser;
		
		$html  ='';
		
		// Can't view wishes on a private list if not list admin
		if(!$wishlist->public && $admin!= 2) {
		  	$html .= Hubzero_View_Helper_Html::div( Hubzero_View_Helper_Html::hed( 2, JText::_('PRIVATE_LIST') ), '', 'content-header' );
		  	$html .= '<div class="main section">'.n;
			$html .= Hubzero_View_Helper_Html::error(JText::_('ALERTNOTAUTH_PRIVATE_LIST')).n;
			$html .= '</div>'.n;	
		 }
		 else {	
			$html .= Hubzero_View_Helper_Html::div( Hubzero_View_Helper_Html::hed( 2, $title ), '', 'content-header' );
			$html .= '<div id="content-header-extra">'.n;
			$html .= t.'<ul id="useroptions">'.n;
			$html .= t.t.'<li class="last"><a class="nav_wishlist" href="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a.'category='. $wishlist->category.a.'rid='.$wishlist->referenceid) .'">'.JText::_('WISHES_ALL').'</a></li>'.n;
			$html .= t.'</ul>'.n;
			$html .= '</div><!-- / #content-header-extra -->'.n;
			$html .= '<div class="main section">'.n;
			$html .= t.' <form id="hubForm" method="post"  action="'.JRoute::_('index.php?option='.$option.a.'task=savesettings').'?listid='.$wishlist->id.'">'.n;
			$html .= t.'	 <div class="explaination">'.n;
			$html .= t.'	 <p>'.JText::_('WISHLIST_SETTINGS_INFO').'</p>'.n;
			$html .= t.'	 </div>'.n;
			$html .= t.'	 <fieldset>'.n;
			$html .= t.'	 <h3>'.JText::_('WISH_LIST_INFORMATION').'</h3>'.n;
			$html .= t.'	 	<label>'.JText::_('WISHLIST_TITLE').': '.n;
			if($wishlist->category== 'resource') {
				$html .= t.'	 	<span class="highighted">'.$wishlist->title.'</span>'.n;
				$html .= t.'	 	<input name="title" id="title" type="hidden" value="'.$wishlist->title.'" /></label>'.n;
				$html .= t.'	 	<p class="hint">'.JText::_('WISHLIST_TITLE_NOTE').'</p>'.n;
			}
			else {
				$html .= t.'	 	<input name="title" id="title" type="text" value="'.$wishlist->title.'" /></label>'.n;
			}			
			$html .= t.'	 	<label>'.JText::_('WISHLIST_DESC').' ('.JText::_('OPTIONAL').'):'.n;
			$html .= t.'	 	<textarea name="description" rows="10" cols="50">'.$wishlist->description.'</textarea></label>'.n;
			$html .= t.'	  <label>'.JText::_('WISHLIST_THIS_LIST_IS').': '.n;
			$html .= t.'      <input class="option" type="radio" name="public" value="1" ';
			if($wishlist->public==1) {
				$html .= ' checked="checked"';
			}
			if($wishlist->category=='resource' or ($wishlist->category=='general' && $wishlist->referenceid=='1')) {
				$html .= ' disabled="disabled"';
			}
			$html .= ' /> '.JText::_('WISHLIST_PUBLIC').n;
			$html .= t.t.t.'      <input class="option" type="radio" name="public" value="0" ';
			if($wishlist->public==0) {
				$html .= ' checked="checked"';
			}
			if($wishlist->category=='resource' or ($wishlist->category=='general' && $wishlist->referenceid=='1')) {
				$html .= ' disabled="disabled"';
			}
			$html .=' /> '.JText::_('WISHLIST_PRIVATE').n;
			$html .= t.t.t.'	  </label>'.n;
			$html .= t.'	 </fieldset>'.n;
			$html .= t.'	 <div class="clear"></div>'.n;
			$html .= t.'	 <div class="explaination">'.n;
			$html .= t.'	 <p>'.JText::_('WISHLIST_SETTINGS_EDIT_GROUPS').'</p>'.n;
			$html .= t.'	 </div>'.n;
			$html .= t.'	 <fieldset>'.n;
			$html .= t.'	 <h3>'.JText::_('WISHLIST_OWNER_GROUPS').'</h3>'.n;
			$html .= t.'	<table class="tktlist">'.n;
			$html .= t.' 		<thead>'.n;
			$html .= t.' 			<tr>'.n;
			$html .= t.' 			 <th style="width:20px;"></th>'.n;
			$html .= t.' 			 <th>'.JText::_('WISHLIST_SETTINGS_GROUP_CN').'</th>'.n;
			$html .= t.' 			 <th>'.JText::_('WISHLIST_GROUP_NUM_MEMBERS').'</th>'.n;
			$html .= t.' 			 <th style="width:80px;">'.JText::_('WISHLIST_GROUP_OPTIONS').'</th>'.n;
			$html .= t.' 			</tr>'.n;
			$html .= t.'		</thead>'.n;
			$html .= t.'		<tbody>'.n;
			
			$allmembers = array();
			if(count($wishlist->groups)>0) {
				$k=1;
				for ($i=0, $n=count( $wishlist->groups ); $i < $n; $i++) {
				$instance = new XGroup($wishlist->groups[$i]);
				$cn = $instance->get('cn');
				$members = $instance->get('members');
				$managers = $instance->get('managers');
				$members = array_merge($members, $managers);
				$members = array_unique($members);
				
				$allmembers = array_merge($allmembers, $members);
				$html .= t.' 			<tr>'.n;
				$html .= t.' 			 <td>'.$k.'.</td>'.n;
				$html .= t.' 			 <td>'.$cn.'</td>'.n;
				$html .= t.' 			 <td>'.count($members).'</td>'.n;
				$html .= t.' 			 <td>';
				$html .= ($n>1 && !in_array($wishlist->groups[$i], $wishlist->nativegroups)) ? '<a href="'.JRoute::_('index.php?option='.$option.a.'task=savesettings').'?listid='.$wishlist->id.a.'action=delete'.a.'group='.$wishlist->groups[$i].'" class="delete">'.JText::_('WISHLIST_OPTION_REMOVE').'</a>' : '' ;
				$html .= t.'			 </td>'.n;
				$html .= t.' 			</tr>'.n;
				$k++;
				}
			}
			else {
				$html .= t.' 			<tr>'.n;
				$html .= t.' 			 <td colspan="4">'.JText::_('WISHLIST_NO_OWNER_GROUPS_FOUND').'.</td>'.n;
				$html .= t.' 			</tr>'.n;
			}
			$html .= t.' 		</tbody>'.n;				
			$html .= t.'	</table>'.n;
			$html .= t.'	 <label>'.JText::_('WISHLIST_SETTINGS_ADD_GROUPS').': '.n;
			$html .= t.'	 	<input name="newgroups"  type="text" value="" />'.n;	
			$html .= t.'	    <span>'.JText::_('WISHLIST_GROUP_HINT').'</span></label>'.n;	
			$html .= t.'	 </fieldset>'.n;
			$html .= t.'	 <div class="clear"></div>'.n;
			$html .= t.'	 <div class="explaination">'.n;
			$html .= t.'	 <p>'.JText::_('WISHLIST_INDIVIDUALS_HINT').'</p>'.n;
			$html .= t.'	 </div>'.n;
			$html .= t.'	 <fieldset>'.n;
			$html .= t.'	 <h3>'.JText::_('WISHLIST_INDIVIDUALS').'</h3>'.n;
			$html .= t.'	<table class="tktlist">'.n;
			$html .= t.' 		<thead>'.n;
			$html .= t.' 			<tr>'.n;
			$html .= t.' 			 <th style="width:20px;"></th>'.n;
			$html .= t.' 			 <th>'.JText::_('WISHLIST_IND_NAME').'</th>'.n;
			$html .= t.' 			 <th>'.JText::_('WISHLIST_IND_LOGIN').'</th>'.n;
			$html .= t.' 			 <th style="width:80px;">'.JText::_('WISHLIST_GROUP_OPTIONS').'</th>'.n;
			$html .= t.' 			</tr>'.n;
			$html .= t.'		</thead>'.n;
			$html .= t.'		<tbody>'.n;
			
			$allmembers = array_unique($allmembers);	
			
			// if we have people outside of groups
			if(count($wishlist->owners) > count($allmembers)) {
				$k=1;					
				for ($i=0, $n=count( $wishlist->owners ); $i < $n; $i++) {				
					if(!in_array($wishlist->owners[$i], $allmembers)) {
					$kuser =& XProfile::getInstance ( $wishlist->owners[$i]);
					$html .= t.' 			<tr>'.n;
					$html .= t.' 			 <td>'.$k.'.</td>'.n;
					$html .= t.' 			 <td>'.$kuser->get('name').'</td>'.n;
					$html .= t.' 			 <td>'.$kuser->get('username').'</td>'.n;
					$html .= t.' 			 <td>';
					$html .= ($n> 1 && !in_array($wishlist->owners[$i], $wishlist->nativeowners))  ? '<a href="'.JRoute::_('index.php?option='.$option.a.'task=savesettings').'?listid='.$wishlist->id.a.'action=delete'.a.'user='.$wishlist->owners[$i].'" class="delete">'.JText::_('WISHLIST_OPTION_REMOVE').'</a>' : '' ;
					$html .= t.'			 </td>'.n;
					$html .= t.' 			</tr>'.n;
					$k++;
					}
				}
			}
			else {
				$html .= t.' 			<tr>'.n;
				$html .= t.' 			 <td colspan="4">'.JText::_('WISHLIST_NO_IND_FOUND').'</td>'.n;
				$html .= t.' 			</tr>'.n;
			}
			$html .= t.' 		</tbody>'.n;				
			$html .= t.'	</table>'.n;
			$html .= t.'	 <label>'.JText::_('WISHLIST_ADD_IND').': '.n;
			$html .= t.'	 	<input name="newowners" id="newowners" type="text" value="" />'.n;
			$html .= t.'	    <span>'.JText::_('WISHLIST_ENTER_LOGINS').'</span></label>'.n;	
			$html .= t.'	 </fieldset>'.n;
			
			if($wishlist->allow_advisory) {	
			$html .= t.'	 <div class="clear"></div>'.n;
			$html .= t.'	 <div class="explaination">'.n;
			$html .= t.'	 <p>'.JText::_('WISHLIST_ADD_ADVISORY_INFO').'</p>'.n;
			$html .= t.'	 </div>'.n;
			$html .= t.'	 <fieldset>'.n;
			$html .= t.'	 <h3>'.JText::_('WISHLIST_ADVISORY').'</h3>'.n;
			$html .= t.'	<table class="tktlist">'.n;
			$html .= t.' 		<thead>'.n;
			$html .= t.' 			<tr>'.n;
			$html .= t.' 			 <th style="width:20px;"></th>'.n;
			$html .= t.' 			 <th>'.JText::_('WISHLIST_IND_NAME').'</th>'.n;
			$html .= t.' 			 <th>'.JText::_('WISHLIST_IND_LOGIN').'</th>'.n;
			$html .= t.' 			 <th style="width:80px;">'.JText::_('WISHLIST_GROUP_OPTIONS').'</th>'.n;
			$html .= t.' 			</tr>'.n;
			$html .= t.'		</thead>'.n;
			$html .= t.'		<tbody>'.n;			
			
			// if we have people outside of groups
			if(count($wishlist->advisory) > 0) {
				$k=1;
						
				for ($i=0, $n=count( $wishlist->advisory ); $i < $n; $i++) {					
					if(!in_array($wishlist->advisory[$i], $allmembers)) {
					$quser =& XProfile::getInstance ( $wishlist->advisory[$i]);
					$html .= t.' 			<tr>'.n;
					$html .= t.' 			 <td>'.$k.'.</td>'.n;
					$html .= t.' 			 <td>'.$quser->get('name').'</td>'.n;
					$html .= t.' 			 <td>'.$quser->get('username').'</td>'.n;
					$html .= t.' 			 <td>';
					$html .=  '<a href="'.JRoute::_('index.php?option='.$option.a.'task=savesettings').'?listid='.$wishlist->id.a.'action=delete'.a.'user='.$wishlist->advisory[$i].'" class="delete">'.JText::_('WISHLIST_OPTION_REMOVE').'</a>' ;
					$html .= t.'			 </td>'.n;
					$html .= t.' 			</tr>'.n;
					$k++;
					}
				}
			}
			else {
				$html .= t.' 			<tr>'.n;
				$html .= t.' 			 <td colspan="4">'.JText::_('WISHLIST_NO_ADVISORY_FOUND').'</td>'.n;
				$html .= t.' 			</tr>'.n;
			}
			$html .= t.' 		</tbody>'.n;				
			$html .= t.'	</table>'.n;
			$html .= t.'	 <label>'.JText::_('WISHLIST_ADD_ADVISORY_MEMBERS').': '.n;
			$html .= t.'	 	<input name="newadvisory" id="newadvisory" type="text" value="" />'.n;
			$html .= t.'	    <span>'.JText::_('WISHLIST_ENTER_LOGINS').'</span></label>'.n;
			if($wishlist->category=='resource' or ($wishlist->category=='general' && $wishlist->referenceid=='1')) {
				$html .= t.'    <input type="hidden" name="public" value="'.$wishlist->public.'" />'.n;
			}		
			$html .= t.'	 </fieldset>'.n;
			} // -- end if allow advisory
		
			$html .= t.'    <div class="clear"></div>'.n;		
			$html .= t.'<p class="submit"><input type="submit" name="submit" value="'.JText::_('SAVE').'" /><span class="cancelaction">';
			$html .= '<a href="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid).'">';
			$html .= JText::_('CANCEL').'</a></span></p>'.n;
			$html .= t.'  </form>'.n;
			$html .= t.'</div>';
		
		} // end if authorized
		
		// HTML output
		echo $html;
?>