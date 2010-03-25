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

/* Add/Edit Wish */

		$wishlist = $this->wishlist;
		$wish = $this->wish; 
		$title = $this->title;
		$option = $this->option;
		$task = $this->task;
		$admin = $this->admin;
		$error = $this->getError();
		$infolink = $this->infolink; 
		$juser = $this->juser;
		$funds = $this->funds;
		$banking = $this->banking;
		
		$html = '';
		
		if($wishlist) {
		
			// what is submitter name?
			if($task=='editwish') {
				$login  = JText::_('UNKNOWN');
				$ruser =& JUser::getInstance($wish->proposed_by);
				if (is_object($ruser)) {
					$login = $ruser->get('username');
				}
			}
			
			$wish->subject = stripslashes($wish->subject);
			$wish->subject = str_replace('&quote;','&quot;',$wish->subject);
			$wish->subject = htmlspecialchars($wish->subject);
					
			$wish->about = trim(stripslashes($wish->about));
			$wish->about = preg_replace('/<br\\s*?\/??>/i', "", $wish->about);
			$wish->about = WishlistHtml::txt_unpee($wish->about);
	
			$html .= Hubzero_View_Helper_Html::div( Hubzero_View_Helper_Html::hed( 2, $title ), '', 'content-header' );
			$html .= '<div id="content-header-extra">'.n;
			$html .= t.'<ul id="useroptions">'.n;
			$html .= t.t.'<li class="last"><a class="nav_wishlist" href="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a.'category='. $wishlist->category.a.'rid='.$wishlist->referenceid) .'">'.JText::_('WISHES_ALL').'</a></li>'.n;
			$html .= t.'</ul>'.n;
			$html .= '</div><!-- / #content-header-extra -->'.n;
			$html .= '<div class="main section">'.n;	
			$html .= t.'<div class="aside">'.n;
			$html .= t.'<p>'.JText::_('TEXT_ADD_WISH').'</p>'.n;
			if($banking && $task!='editwish') {
				$html .= t.'<p class="help" style="margin-top: 2em;"><strong>'.JText::_('WHAT_IS_REWARD').'</strong><br />'.n;
				$html .= t.JText::_('WHY_ADDBONUS').' <a href="'.$infolink.'">'.JText::_('LEARN_MORE').'</a> '.JText::_('ABOUT_POINTS').'.</p>'.n;
			}
			$html .= t.'</div><!-- / .aside -->'.n;
			$html .= t.'<div class="subject">'.n;
			if($error) { Hubzero_View_Helper_Html::error($error).n; }
				$html .= t.t.t.' <form id="hubForm" method="post" action="index.php?option='.$option.'">'.n;
				$html .= t.t.t.'	 <fieldset>'.n;
			if($task=='editwish') {
				$html .= t.t.t.'	  <label>'.JText::_('WISH_PROPOSED_BY').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
				$html .= t.t.t.'	  <input name="by" maxlength="50" id="by" type="text" value="'.$login.'" /></label>'.n;
			}			
			$html .= t.t.t.'	  <input type="hidden" id="proposed_by" name="proposed_by" value="'.$wish->proposed_by.'" />'.n;
			$html .= t.t.t.'	  <label><input class="option" type="checkbox" name="anonymous" value="1" ';
			$html .= ($wish->anonymous) ? 'checked="checked"' : '';
			$html .= '/>'.JText::_('WISH_POST_ANONYMOUSLY').'</label>'.n;
			if($admin == 2 && $wishlist->public) { // list owner
				$html .= t.t.t.'	  <label><input class="option" type="checkbox" name="private" value="1" ';
				$html .= ($wish->private) ? 'checked="checked"' : '';
				$html .= '/>'.JText::_('WISH_MAKE_PRIVATE').'</label>'.n;
			}
			$html .= t.t.t.'	  <input type="hidden"  name="task" value="savewish" />'.n;					
			$html .= t.t.t.'	  <input type="hidden" id="wishlist" name="wishlist" value="'.$wishlist->id.'" />'.n;
			$html .= t.t.t.'	  <input type="hidden" id="status" name="status"  value="'.$wish->status.'" />'.n;
			$html .= t.t.t.'	  <input type="hidden" id="id" name="id" value="'.$wish->id.'" />'.n;	
			$html .= t.t.t.'	  <label>Summary of your wish: <span class="required">'.JText::_('REQUIRED').'</span>'.n;
			$html .= t.t.t.'	  <input name="subject" maxlength="120" id="subject" type="text" value="'.$wish->subject.'" /></label>'.n;
			$html .= t.t.t.'	  <label>'.JText::_('WISH_EXPLAIN_IN_DETAIL').': '.n;
			$html .= t.t.t.'	  <textarea name="about" rows="10" cols="50">'.$wish->about.'</textarea></label>'.n;
			$html .= t.t.t.'	  <label>'.JText::_('WISH_ADD_TAGS').': <br />'.n;
			// Tag editor plug-in
			JPluginHelper::importPlugin( 'tageditor' );
			$dispatcher =& JDispatcher::getInstance();	
			$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tags','actags','',$wish->tags,'')) );			
			if (count($tf) > 0) {
				$html .= $tf[0];
			} else {
				$html .= t.t.t.'<textarea name="tags" id="tags-men" rows="6" cols="35">'. $wish->tags .'</textarea>'.n;
			}
			$html .= '</label>';
			if($banking && $task!='editwish') {
				$html .= t.t.'<label>'.JText::_('ASSIGN_REWARD').':<br />'.n;
				$html .= t.t.'<input type="text" name="reward" value="" size="5" ';
				if ($funds <= 0 ) {
					$html .= 'disabled style="background:#e2e2e2;" ';		
				}
				$html .= '/> <span class="subtext">'.JText::_('YOU_HAVE').' <strong>'.$funds.'</strong> '.JText::_('POINTS_TO_SPEND').'.</span></label>'.n;
				$html .= t.t.t.'	  <input type="hidden"  name="funds" value="'.$funds.'" />'.n;
			}		
			$html .= t.t.t.'      <p class="submit"><input type="submit" value="'.JText::_('FORM_SUBMIT').'" /></p>'.n;
			$html .= t.t.t.'	 </fieldset>'.n;			
			$html .= t.t.t.' </form>'.n;			
			$html .= '</div><div class="clear"></div></div>'.n;					
		}
		else {
			$html  = Hubzero_View_Helper_Html::error('ERROR_WISHLIST_NOT_FOUND').n;
		}

		// HTML output
		echo $html;
?>