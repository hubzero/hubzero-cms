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

		/* Wish view */
		
		$title 		= $this->title;
		$option 	= $this->option;
		$config 	= $this->config;
		$wishlist 	= $this->wishlist;
		$item 		= $this->wish;
		$admin 		= $this->admin;
		$task 		= $this->task;
		$filters	= $this->filters;
		$addcomment	= $this->addcomment;
		$juser		= $this->juser;
		$error		= $this->getError();
		$abuse		= $this->abuse;
		$plan		= $item->plan;
		
		$html = '';
		
		if($wishlist && $item) {
		
			// What name should we dispay for the submitter?
			$name = JText::_('ANONYMOUS');
			if ($item->anonymous != 1) {
				$name = $item->authorname;
			}
			
			$assigned = ($item->assigned && ($admin==2 or $admin==1)) ? JText::_('assigned to').' <a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?filterby='.$filters['filterby'].a.'action=editplan#plan">'.$item->assignedto.'</a>' : '';	
			
			if(!$assigned && ($admin==2 or $admin==1) && $item->status==0) {
			$assigned = '<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?filterby='.$filters['filterby'].a.'action=editplan#plan">'.JText::_('unassigned').'</a>';
			}
				
			$item->status = ($item->accepted==1 && $item->status==0) ? 6 : $item->status;
			$due  = ($item->due !='0000-00-00 00:00:00') ? JHTML::_('date',$item->due, '%Y-%m-%d') : '';
				
			switch( $item->status) 
			{
				case 0:    	$status = strtolower(JText::_('WISH_STATUS_PENDING'));
							$statusnote = JText::_('WISH_STATUS_PENDING_INFO');
				break;
				case 6:    	$status = strtolower(JText::_('WISH_STATUS_ACCEPTED'));
							$statusnote = JText::_('WISH_STATUS_ACCEPTED_INFO');
							$statusnote.= $plan ? '; '.JText::_('WISH_PLAN_STARTED') : '';
							$statusnote.= $due ? '; '.JText::_('WISH_DUE_SET').' '.$due : '';
				break;
				case 3:    	$status = strtolower(JText::_('WISH_STATUS_REJECTED'));
							$statusnote = JText::_('WISH_STATUS_REJECTED_INFO');
				break;
				case 4:    	$status = strtolower(JText::_('WISH_STATUS_WITHDRAWN'));
							$statusnote = JText::_('WISH_STATUS_WITHDRAWN_INFO');
				break;
				case 1:    	$status = strtolower(JText::_('WISH_STATUS_GRANTED'));
							$statusnote = $item->granted!='0000-00-00 00:00:00' ? strtolower(JText::_('ON')).' '.JHTML::_('date',$item->granted, '%d %b %y').' '.strtolower(JText::_('BY')).' '.$item->grantedby : '';
				break;
			}
			
			// Can't view wishes on a private list if not list admin
			if(!$wishlist->public	 &&  !$admin) {
				$html .= Hubzero_View_Helper_Html::div( Hubzero_View_Helper_Html::hed( 2, JText::_('PRIVATE_LIST') ), '', 'content-header' );
				$html .= '<div class="main section">'.n;
				$html .= Hubzero_View_Helper_Html::error(JText::_('WARNING_NOT_AUTHORIZED_PRIVATE_LIST')).n;
				$html .= '</div>'.n;	
			 }
			
			 else  {
				$html .='<div id="content-header">'.n;
				$html .='	<h2>'.$title.'</h2>'.n;
				$html .='	<h3>'.JText::_('PROPOSED_ON').' '.JHTML::_('date',$item->proposed, '%d %b %Y').' ';
				$html .= JText::_('AT').' '.JHTML::_('date', $item->proposed, '%I:%M %p').' '.JText::_('BY').' '.$name;
				$html .= $assigned ? '; '.$assigned : '';
				$html .='  </h3>'.n;			
				$html .='</div><!-- / #content-header -->'.n;			
				
				if($item->saved==3 && !$error) {
					$html  .= '<p class="passed">'.JText::_('New wish successfully posted. Thank you!').'</p>'.n;
				}
				
				if($item->saved==2 && !$error && $admin) {
					$html  .= '<p class="passed">'.JText::_('Changes to the wish successfully saved.').'</p>'.n;
				}
				
				// Navigation
				$html .= '<div id="content-header-extra">'.n;
				$html .= t.'<ul id="useroptions">'.n;				
				$html .= t.t.'<li>'.n;
				if($item->prev) {
					$html .= t.t.t.'<a href="index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->prev.a.'filterby='.$filters['filterby'].'"><span>&lsaquo; '.JText::_('PREV').'</span></a>'.n;
				} else {
					$html .=t.t.t.'<span>&lsaquo; '.JText::_('PREV').'</span>'.n;
				}
				$html .= t.t.'</li>'.n;
				
				$html .= t.t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'filterby='.$filters['filterby']).'" ><span>'.JText::_('All').'</span></a></li>'.n;
				$html .= t.t.'<li class="last">';
				if($item->next) {
					$html .= t.t.t.'<a href="index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->next.a.'filterby='.$filters['filterby'].'"><span>'.JText::_('NEXT').' &rsaquo;</span></a>'.n;
				}
				else {
					$html .=t.t.t.'<span>'.JText::_('NEXT').' &rsaquo; </span>'.n;
				}
				
				$html .= t.t.'</li>'.n;
				$html .= t.'</ul>'.n;
				$html .= '</div>'.n;
				
				// Status bar
				$html .='  <div id="topstatusbar" class="'.strtolower($status).'">'.n;
				$html .='  '.strtoupper(JText::_('STATUS')).': <span class="'.strtolower($status).'">';
				$html .= $admin==2 ? '<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=changestatus#action">' : '';
				$html .= strtoupper($status);
				$html .= $admin==2 ? '</a>' : '';
				$html .= '</span>'.n;
				$html .=' - '.$statusnote.n;
				$html .='  </div>'.n;		
				$html .= '<div class="clear"></div>'.n;	
				$html .= '<div class="main section noborder">'.n;
				if ($item->reports) {
					// this wish is abusive
					$html  .= Hubzero_View_Helper_Html::error(JText::_('NOTICE_POSTING_REPORTED')).n;	
				}
				else {
					// Wish content starts
					$html .= t.'<div id="wishitem"';
					$html .= '>'.n;	
				
					// Wish title and description	
					$html .= t.t.'<div class="wishcontentarea';
					if(!$admin) {
						$html .= ' regview';
					}
					$html .= '">'.n;
					if($item->private && $wishlist->public){
						$html .= ' <div class="privatewish"></div>';
					}
					$html .= t.t.t.'<h3';
					$html .= ' class="wish_';
					if(isset($item->ranked) && !$item->ranked && $item->status!=1 && $item->status!=3 && $item->status!=4 && ($admin==2 or $admin==3))  {
						$html .= 'unranked';
					}	else if($item->status==1) {
						$html .= 'granted';
					}
					else if ($item->urgent==1 && $admin==2) {
						$html .= 'urgent';
					}
					else if ($item->urgent==2 && $admin==2) {
						$html .= 'burning';
					}
					else {
						$html .= 'outstanding';
					}	
					$html .='"';
					$html .= '>'.$item->subject.'</h3>'.n;
				
					if($item->about) {
						$item->about = trim(stripslashes($item->about));
						$item->about = preg_replace('/<br\\s*?\/??>/i', "", $item->about);
						$item->about = htmlspecialchars(WishlistHtml::txt_unpee($item->about));
						$html .= t.t.t.'<p>'.$item->about.'</p>'.n;
					}
				
					// Show tags
					if (count($item->tags) > 0) {
						$tagarray = array();
						$tagarray[] = '<ol class="tags">';
						if (!empty($item->tags))
						foreach ($item->tags as $tag)
						{
							$tag['raw_tag'] = str_replace( '&amp;', '&', $tag['raw_tag'] );
							$tag['raw_tag'] = str_replace( '&', '&amp;', $tag['raw_tag'] );
							$tagarray[] = ' <li><a href="'.JRoute::_('index.php?option=com_tags'.a.'tag='.$tag['tag']).'" rel="tag">'.$tag['raw_tag'].'</a></li>';
						}
						$tagarray[] = '</ol>';
			
						$tags = implode( "\n", $tagarray );
					} else {
						$tags = '';
					}
					if($tags ) {
						$html .= t.t.'<div class="tagcontainer">Tags:'.$tags.'</div>'.n;
					}
			
					$html .= t.t.'</div>'.n;
									
					if(!$admin) {
						// only show thumbs ranking
						$html .= t.t.'<div class="intermed" style="padding-top:5px;">'.n;
						$html .= t.t.t.'<p id="wish_'.$item->id.'" class="'.$option.'">'.n;
						// Thumbs ratings
						$view = new JView( array('name'=>'rateitem') );
						$view->option = $option;
						$view->item = $item;
						$view->listid = $wishlist->id;
						$view->plugin = 0;
						$view->admin = $admin;
						$view->page = 'wish';
						$view->filters = $filters;
						$html .= $view->loadTemplate();																
						$html .= t.t.t.'</p>'.n;
						$html .= t.t.'</div>'.n;
						
						// Points				
						if($wishlist->banking) {
							$html .= t.t.t.'<div class="assign_bonus">'.n;				
							if(isset($item->bonus) && $item->bonus > 0 && ($item->status==0 or $item->status==6)) {
								$html .= t.t.t.'<a class="bonus tooltips" href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=addbonus#action" title="'.JText::_('WISH_ADD_BONUS').' ::'.$item->bonusgivenby.' '.JText::_('MULTIPLE_USERS').' '.JText::_('WISH_BONUS_CONTRIBUTED_TOTAL').' '.$item->bonus.' '.JText::_('POINTS').' '.JText::_('WISH_BONUS_AS_BONUS').'">+ '.$item->bonus.'</a>'.n;
							}
							else if($item->status==0 or $item->status==6) {
								$html .= t.t.t.'<a class="nobonus tooltips" href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=addbonus#action" title="'.JText::_('WISH_ADD_BONUS').' :: '.JText::_('WISH_BONUS_NO_USERS_CONTRIBUTED').'">&nbsp;</a>'.n;
							}
							else {
								$html .= t.t.t.'<span class="bonus_inactive" title="'.JText::_('WISH_BONUS_NOT_ACCEPTED').'">&nbsp;</span>'.n;
							}
							$html .= t.t.t.'</div>'.n; // end assign bonus
						}				
					} else {
						// Show priority and ratings
						$html .= t.t.'<div class="wishranked ';
						$html .= ($admin==1) ? 'narrow' : '';
						$html .='">'.n;
						$eligible = array_merge($wishlist->owners, $wishlist->advisory);
						$eligible = array_unique($eligible);
						$voters = ($item->num_votes <= count($eligible)) ? count($eligible) : $item->num_votes;
						$html .= t.t.t.'<div class="wishpriority">'.JText::_('PRIORITY').': '.$item->ranking.' <span>('.$item->num_votes.' '.JText::_('NOTICE_OUT_OF').' '.$voters.' '.JText::_('VOTES').')</span>';
						if($due && $item->status!=1) {
							$html .= ($item->due <= date( 'Y-m-d H:i:s')) ? '<span class="overdue"><a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=editplan#plan">'.JText::_('OVERDUE') : '<span class="due"><a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=editplan#plan">'.JText::_('WISH_DUE_IN').' '.WishlistHTML::nicetime($item->due);
							$html .= '</a></span>';
						}
						$html .= '</div>'.n;
						
						// My opinion is available for list owners/advisory committee only
						if($admin==2 or $admin==3) {
							$html .= t.t.t.'<div class="rankingarea">'.n;
							$html .= t.t.t.t.'<div>'.n;					
							$html .= t.t.t.t.'<h4>'.JText::_('MY_OPINION').':</h4>';
							
							if(isset($item->ranked) && !$item->ranked && ($item->status==0 or $item->status==6) or $item->action=='editvote') {
								// need to rank it
								$html .= t.t.t.t.WishlistHtml::rankingForm($option, $wishlist, 'savevote', $item, $admin).n;							
							} else if(isset($item->ranked) && $item->ranked ) {						
								// already ranked						
								$html .= ($item->status==0 or $item->status==6) ? '<span class="editbutton"><a href="index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id.a.'action=editvote" class="editopinion">['.JText::_('EDIT').']</a></span>' : '';						
								$html .= t.t.t.t.'<p>'.WishlistHtml::convertVote ($item->myvote_imp, 'importance').'</p>'.n;
								if($admin == 2) {
								$html .= t.t.t.t.'<p>'.WishlistHtml::convertVote ($item->myvote_effort, 'effort').'</p>'.n;	
								}	
								else {
								$html .= t.t.t.t.'<p>'.JText::_('NA').'</p>'.n;
								}			
							}
							else {
								$html .= t.t.t.t.'<p>'.JText::_('NA').'</p>'.n;	
							}
							$html .= t.t.t.t.'</div>'.n;	
							$html .= t.t.t.'</div>'.n;
						} 
						
						// Consensus area, show to all admins	
						$html .= t.t.'<div class="consensusarea">'.n;
						$html .= t.t.t.'<div>'.n;
						$html .= t.t.t.t.'<h4>'.JText::_('CONSENSUS').':</h4>'.n;
						if((isset($item->num_votes) && $item->num_votes==0) or !isset($item->num_votes)) {
							// no ranking available
							$html .= t.t.t.t.'<p>'.JText::_('NA').'</p>'.n;	
						}
						else { 
							// have ranking	
							if(isset($item->num_votes) && isset($item->num_skipped_votes) && $item->num_votes==$item->num_skipped_votes) {
								$item->average_effort = 7;
							}							
							$html .= t.t.t.t.'<p>'.WishlistHtml::convertVote($item->average_imp, 'importance').'</p>'.n;
							$html .= t.t.t.t.'<p>'.WishlistHtml::convertVote($item->average_effort,'effort').'</p>'.n;				
						}
						$html .= t.t.t.'</div>'.n;
						$html .= t.t.'</div>'.n;
						
						$html .= t.t.'<div class="votingarea">'.n;
						$html .= t.t.t.'<div>'.n;
						$html .= t.t.t.t.'<h4>'.JText::_('COMMUNITY_VOTE').':</h4>'.n;
						$html .= t.t.t.t.'<div id="wish_'.$item->id.'" class="'.$option.'">';
						// Thumbs ratings
						$view = new JView( array('name'=>'rateitem') );
						$view->option = $option;
						$view->item = $item;
						$view->listid = $wishlist->id;
						$view->plugin = 0;
						$view->admin = $admin;
						$view->page = 'wish';
						$view->filters = $filters;
						$html .= $view->loadTemplate();												
						$html .= t.t.t.t.'</div>'.n;
						// Points				
						if($wishlist->banking) {
							$html .= t.t.t.'<div class="assign_bonus">'.n;				
							if(isset($item->bonus) && $item->bonus > 0 && ($item->status==0 or $item->status==6)) {
								$html .= t.t.t.'<a class="bonus tooltips" href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=addbonus#action" title="'.JText::_('WISH_ADD_BONUS').' ::'.$item->bonusgivenby.' '.JText::_('MULTIPLE_USERS').' '.JText::_('WISH_BONUS_CONTRIBUTED_TOTAL').' '.$item->bonus.' '.JText::_('POINTS').' '.JText::_('WISH_BONUS_AS_BONUS').'">+ '.$item->bonus.'</a>'.n;
							}
							else if($item->status==0 or $item->status==6) {
								$html .= t.t.t.'<a class="nobonus tooltips" href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=addbonus#action" title="'.JText::_('WISH_ADD_BONUS').' :: '.JText::_('WISH_BONUS_NO_USERS_CONTRIBUTED').'">&nbsp;</a>'.n;
							}
							else {
								$html .= t.t.t.'<span class="bonus_inactive" title="'.JText::_('WISH_BONUS_NOT_ACCEPTED').'">&nbsp;</span>'.n;
							}
							$html .= t.t.t.'</div>'.n; // end assign bonus
						}				
						
						$html .= t.t.t.'</div>'.n;
						$html .= t.t.'</div>'.n;		
						$html .= t.'</div>'.n;
					} // end admin						
					$html .= t.'<div class="clear"></div>'.n;
					$html .= t.'</div>'.n;
					
					$html .= t.t.t.'<p class="comment-options">';					
					// some extra admin options
					if($admin && $admin!=3) {				
						if($item->status!=1) {
							$html .= t.t.t.t.'<a class="changestatus" href="';
							$html .= JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=changestatus#action">'.JText::_('ACTION_CHANGE_STATUS').'</a>  '.n;
						}
							$html .= t.t.t.t.'<a class="transfer" href="';
							$html .= JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=move#action">'.JText::_('MOVE').'</a>'.n;
						if($item->private) {
							$html .= t.t.t.t.'  <a class="makepublic" href="';
							$html .= JRoute::_('index.php?option='.$option.a.'task=editprivacy'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?private=0">'.JText::_('MAKE_PUBLIC').'</a>'.n;	
						}
						else {
							$html .= t.t.t.t.'  <a class="makeprivate" href="';
							$html .= JRoute::_('index.php?option='.$option.a.'task=editprivacy'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?private=1">'.JText::_('MAKE_PRIVATE').'</a>'.n;	
						}

						$html .= t.t.t.t.'<a class="editwish" href="';
						$html .= JRoute::_('index.php?option='.$option.a.'task=editwish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'">'.ucfirst(JText::_('ACTION_EDIT')).'</a>  '.n;					
					}
					// Report abuse option is for everyone
					$html .= '<a href="index.php?option=com_support'.a.'task=reportabuse'.a.'category=wish'.a.'id='.$item->id.a.'parent='.$wishlist->id.'" class="abuse">'.JText::_('REPORT_ABUSE').'</a>'.n;
					// withdraw wish option if author
					if($juser->get('id') == $item->proposed_by && $item->status==0) {
						$html .= t.t.t.t.' | <a class="deletewish" href="';
						$html .= JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=delete#action">'.JText::_('ACTION_WITHDRAW_WISH').'</a>'.n;
					}								
					$html .= t.t.t.'</p>'.n;			
		
					// delete wish?
					if($item->action == 'delete') {
						$html .= t.'<a name="action"></a>'.n;
						$html .= t.t.'<div class="error">'.n;
						$html .= t.t.'<h4>'.JText::_('ARE_YOU_SURE_DELETE_WISH').'</h4>'.n;
						$html .= t.t.'<p><span class="say_yes">';
						$html .='<a href="index.php?option='.$option.a.'task=withdraw'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id.'">';
						$html .= strtoupper(JText::_('YES')).'</a></span> <span class="say_no">';
						$html .='<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'">';
						$html .= strtoupper(JText::_('NO')).'</a></span></p>'.n;
						$html .= t.t.'</div>'.n;
					}
					// change status?
					if($item->action == 'changestatus') {
						$html .= t.'<a name="action"></a>'.n;
						$html .= t.t.'<div class="takeaction">'.n;
						$html .= t.t.'<div class="aside_note">'.n;
						$html .= t.t.'<p>'.JText::_('WISH_STATUS_INFO').'</p>'.n;
						$html .= t.t.'</div>'.n;
						$html .= t.t.'<h4>'.JText::_('ACTION_CHANGE_STATUS_TO').':</h4>'.n;
						$html .= t.t.t.' <form id="changeStatus" method="post" action="index.php?option='.$option.'">'.n;
						$html .= t.t.t.'	 <fieldset>'.n;
						$html .= t.t.t.'	  <input type="hidden"  name="task" value="editwish" />'.n;
						$html .= t.t.t.'	  <input type="hidden" id="wishlist" name="wishlist" value="'.$wishlist->id.'" />'.n;
						$html .= t.t.t.'	  <input type="hidden" id="category" name="category" value="'.$wishlist->category.'" />'.n;
						$html .= t.t.t.'	  <input type="hidden" id="rid" name="rid" value="'.$wishlist->referenceid.'" />'.n;
						$html .= t.t.t.'	  <input type="hidden" id="wishid" name="wishid" value="'.$item->id.'" />'.n;
						$html .= t.t.t.'	  <label>'.n;
						$html .= t.t.t.'      <input class="option" type="radio" name="status" value="pending" ';
						$html .= ($item->status == 0) ? 'checked="checked"' : '';
						$html .= ' /> '.JText::_('WISH_STATUS_PENDING').n;
						$html .= t.t.t.'	  </label>'.n;
						$html .= t.t.t.'	 </fieldset>'.n;
						$html .= t.t.t.'	 <fieldset>'.n;
						$html .= t.t.t.'	  <label>'.n;
						$html .= t.t.t.'      <input class="option" type="radio" name="status" value="accepted" ';
						$html .= ($item->status == 6) ? 'checked="checked"' : '';
						$html .=' /> '.JText::_('WISH_STATUS_ACCEPTED').n;
						$html .= t.t.t.'	  </label>'.n;
						$html .= t.t.t.'	 </fieldset>'.n;
						$html .= t.t.t.'	 <fieldset>'.n;
						$html .= t.t.t.'	  <label>'.n;
						$html .= t.t.t.'      <input class="option" type="radio" name="status" value="rejected" ';
						$html .= ($item->status == 3) ? 'checked="checked"' : '';
						$html .=' /> '.JText::_('WISH_STATUS_REJECTED').n;
						$html .= t.t.t.'	  </label>'.n;
						$html .= t.t.t.'	 </fieldset>'.n;
						$html .= t.t.t.'<fieldset';
						$html .= ($wishlist->category=='resource' ) ? ' class="grantstatus">'.n : '>';
						$html .= t.t.t.'	  <label>'.n;
						$html .= t.t.t.'      <input class="option" type="radio" name="status" value="granted" ';
						$html .= ($item->status == 1) ? 'checked="checked"' : '';
						$html .= ($item->assigned && $item->assigned!=$juser->get('id') ) ? 'disabled="disabled"' : '';
						$html .=' /> '.JText::_('WISH_STATUS_GRANTED').n;
						if($item->assigned && $item->assigned!=$juser->get('id') ) {
							$html .= ' <span class="forbidden"> - '.JText::_('WISH_STATUS_GRANTED_WARNING').n;
						}
						else if($wishlist->category=='resource' && isset($item->versions)) {
							$html .= t.t.t.'<label class="doubletab">'.n;
							$html .= JText::_('IN').' '.n;
							$html .= t.t.t.'	  <select name="vid" id="vid">'.n;
							foreach($item->versions as $v) {
								$v_label = $v->state == 3 ? JText::_('NEXT_TOOL_RELEASE') : JText::_('VERSION').' '.$v->version.' ('.JText::_('REVISION').' '.$v->revision.')';
								$html .= t.t.t.'	  <option value="'.$v->id.'">'.$v_label.'</option>'.n;
							}
							$html .= t.t.t.'	  </select>'.n;
							$html .= t.t.t.'	  </label>'.n;
						}
						$html .= t.t.t.'	  </label>'.n;
						$html .= t.t.t.'	 </fieldset>'.n;
					
						$html .= t.t.t.'	 <fieldset>'.n;
						$html .= t.t.t.'     <input type="submit" value="'.strtolower(JText::_('ACTION_CHANGE_STATUS')).'" /> <span class="cancelaction">';
						$html .= '<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'">';
						$html .= JText::_('CANCEL').'</a></span>'.n;
						$html .= t.t.t.'	 </fieldset>'.n;			
						$html .= t.t.t.' </form>'.n;
						$html .= t.t.'</div>'.n;
					}
					// assign a bonus to the wish?
					if($item->action == 'addbonus' && $item->status!=1 && $wishlist->banking) {
						$html .= t.'<a name="action"></a>'.n;
						$html .= t.t.'<div class="addbonus">'.n;
						$html .= t.t.'<div class="aside_note">'.n;
						$html .= t.t.'<p>'.JText::_('WHY_ADDBONUS').'</p>'.n;
						$html .= t.t.'</div>'.n;
						$bonus = $item->bonus ? $item->bonus : 0; 
						$html .= t.t.'<h4>'.JText::_('WISH_ADD_BONUS').'</h4>'.n;
						$html .= t.t.'<h4 class="summary">'.$item->bonusgivenby.' '.JText::_('user(s)').' '.JText::_('WISH_BONUS_CONTRIBUTED_TOTAL').' '.$bonus.' '.JText::_('points ').' '.JText::_('WISH_BONUS_AS_BONUS').'</h4>'.n;
						$html .= t.t.t.' <form id="addBonus" method="post" action="index.php?option='.$option.'">'.n;
						$html .= t.t.t.'	 <fieldset>'.n;
						$html .= t.t.t.'	  <input type="hidden"  name="task" value="addbonus" />'.n;
						$html .= t.t.t.'	  <input type="hidden" id="wishlist" name="wishlist" value="'.$wishlist->id.'" />'.n;
						$html .= t.t.t.'	  <input type="hidden" id="wish" name="wish" value="'.$item->id.'" />'.n;
						$html .= t.t.t.'	  <label>'.JText::_('ACTION_ADD').n;
						$html .= t.t.t.'	  <span class="price"></span> '.n;
						$html .= t.t.t.'      <input class="secondary_option" type="text" maxlength="4" name="amount" value=""';
						$html .= ($item->funds <= 0) ? ' disabled="disabled"' : '';
						$html .= '" />'.n;
						$html .= t.t.t.'	  <span>('.JText::_('NOTICE_OUT_OF').' '.$item->funds.' '.JText::_('NOTICE_POINTS_AVAILABLE').' <a href="members'.DS.$juser->get('id').DS.'points">'.JText::_('ACCOUNT').'</a>)</span>'.n;
						$html .= t.t.t.'	  </label>'.n;
						$html .= ($item->funds > 0) ? t.t.t.'     <input type="submit" class="process" value="'.strtolower(JText::_('ACTION_ADD_POINTS')).'" /> '.n : '';
						$html .= t.t.t.'<span class="cancelaction">';
						$html .= '<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'">';
						$html .= JText::_('CANCEL').'</a></span>'.n;
						$html .= t.t.t.'	 </fieldset>'.n;			
						$html .= t.t.t.' </form>'.n;
						$html .= ($item->funds <= 0) ? t.t.t.'<p class="nofunds">'.JText::_('SORRY_NO_FUNDS').'</p>'.n : '';
						$html .= t.t.'<div class="clear"></div>'.n;
						$html .= t.t.'</div>'.n;
					}
					// move wish?
					if($item->action=='move') {
						$html .= t.'<a name="action"></a>'.n;
						$html .= t.t.'<div class="moveitem">'.n;
						if($error) {
						$html  .= Hubzero_View_Helper_Html::error($error).n;
						}
						$html .= t.t.'<h4>'.JText::_('WISH_BELONGS_TO').':</h4>'.n;
						$html .= t.t.t.' <form id="moveWish" method="post" action="index.php?option='.$option.'">'.n;
						$html .= t.t.t.'	 <fieldset>'.n;
						$html .= t.t.t.'	  <input type="hidden"  name="task" value="movewish" />'.n;
						$html .= t.t.t.'	  <input type="hidden" id="wishlist" name="wishlist" value="'.$wishlist->id.'" />'.n;
						$html .= t.t.t.'	  <input type="hidden" id="wish" name="wish" value="'.$item->id.'" />'.n;
						$html .= t.t.t.'	  <label>'.n;
						$html .= t.t.t.'      <input class="option" type="radio" name="type" value="general" ';
						$html .= ($wishlist->category=='general') ? 'checked="checked"' : '';
						$html .= ' /> '.JText::_('WISHLIST_MAIN_NAME').n;
						$html .= t.t.t.'	  </label>'.n;
						$html .= t.t.t.'	 </fieldset>'.n;						
						$html .= t.t.t.'	 <fieldset>'.n;
						$html .= t.t.t.'	  <label>'.n;
						$html .= t.t.t.'      <input class="option" type="radio" name="type" value="resource" ';
						$html .= ($wishlist->category=='resource') ? 'checked="checked"' : '';
						$html .=' /> '.JText::_('WISHLIST_RESOURCE_NAME').n;
						$html .= t.t.t.'	  </label>'.n;
						$html .= t.t.t.'	  <label>'.n;
						$html .= t.t.t.'      <input class="secondary_option" type="text" name="resource" id="acresource" value="';
						$html .= ($wishlist->category=='resource') ? $wishlist->referenceid : '';
						$html .= '" autocomplete="off" />'.n;
						$html .= t.t.t.'	  </label>'.n;
						$html .= t.t.t.'	 </fieldset>'.n;
						if(isset($item->cats) && preg_replace("/group/", "", $item->cats) != $item->cats) {
							$html .= t.t.t.'	 <fieldset>'.n;
							$html .= t.t.t.'	  <label>'.n;
							$html .= t.t.t.'      <input class="option" type="radio" name="type" value="group" ';
							if($wishlist->category=='group') {
								$html .= 'checked="checked"';
							}
							$html .=' /> '.JText::_('WISHLIST_GROUP_NAME').n;
							$html .= t.t.t.'	  </label>'.n;
							$html .= t.t.t.'	  <label>'.n;
						
							$document =& JFactory::getDocument();
							$document->addScript('components'.DS.'com_support'.DS.'observer.js');
							$document->addScript('components'.DS.'com_wishlist'.DS.'autocompleter.js');
							$document->addStyleSheet('components'.DS.'com_support'.DS.'autocompleter.css');
						
							$html .= t.t.t.'<input type="text" name="group" value="';
							if($wishlist->category=='group') {
								$html .= $wishlist->cn;
							}
							$html .= '" id="acgroup"  class="secondary_option" />'.n;
							$html .= t.t.t.'	  </label>'.n;
							$html .= t.t.t.'	 </fieldset>'.n;
						}
						$html .= t.t.t.'	 <fieldset class="separated">'.n;
						$html .= t.t.'<h4>'.JText::_('TRANSFER_OPTIONS').':</h4>'.n;
						$html .= t.t.t.'	  <label>'.n;
						$html .= t.t.t.'       <input class="option" type="checkbox" name="keepcomments" value="1" checked="checked" /> ';
						$html .= JText::_('TRANSFER_OPTIONS_PRESERVE_COMMENTS').n;
						$html .= t.t.t.'	  </label>'.n;
						$html .= t.t.t.'	 </fieldset>'.n;
						$html .= t.t.t.'	 <fieldset>'.n;
						$html .= t.t.t.'	  <label>'.n;
						$html .= t.t.t.'       <input class="option" type="checkbox" name="keepplan" value="1" checked="checked" /> ';
						$html .= JText::_('TRANSFER_OPTIONS_PRESERVE_PLAN').n;
						$html .= t.t.t.'	  </label>'.n;
						$html .= t.t.t.'	 </fieldset>'.n;
						$html .= t.t.t.'	 <fieldset>'.n;
						$html .= t.t.t.'	  <label>'.n;
						$html .= t.t.t.'       <input class="option" type="checkbox" name="keepstatus" value="1" checked="checked" /> ';
						$html .= JText::_('TRANSFER_OPTIONS_PRESERVE_STATUS').n;
						$html .= t.t.t.'	  </label>'.n;
						$html .= t.t.t.'	 </fieldset>'.n;
						$html .= t.t.t.'	 <fieldset>'.n;
						$html .= t.t.t.'	  <label>'.n;
						$html .= t.t.t.'       <input class="option" type="checkbox" name="keepfeedback" value="1" checked="checked" /> ';
						$html .= JText::_('TRANSFER_OPTIONS_PRESERVE_VOTES').n;
						$html .= t.t.t.'	  </label>'.n;
						$html .= t.t.t.'	 </fieldset>'.n;
						$html .= t.t.t.'	 <fieldset class="finalblock">'.n;
						$html .= t.t.t.'     <input type="submit" value="'.strtolower(JText::_('ACTION_MOVE_THIS_WISH')).'" /> <span class="cancelaction">';
						$html .= '<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'">';
						$html .= JText::_('CANCEL').'</a></span>'.n;
						$html .= t.t.t.'	 </fieldset>'.n;			
						$html .= t.t.t.' </form>'.n;		
						$html .= t.t.'</div>'.n;						
					}
								
					// Display comments block
					$html .= t.'<a name="comments"></a>'.n;
					$html .= t.'<div id="section-comments">'.n;
					$html .= t.t.'<h3><a href="javascript:void(0);" class="';
					$html .= 'collapse';
					$html .= '" id="part_com">&nbsp;</a> '.JText::_('COMMENTS').' (';
					$html .= $item->numreplies;
					$html .= ')</h3>'.n;
					$html .= t.t.'<div class="aside_note">'.n;
					$html .= t.t.t.'<p class="add">';
					$html .= '<a href="index.php?option='.$option.a.'task=reply'.a.'cat=wish'.a.'id='.$wishlist->id.a.'refid='.$item->id.a.'wishid='.$item->id.'">';
					$html .= JText::_('ADD_A_COMMENT').'</a></p>'.n;
					$html .= t.t.'</div><!-- / .aside -->'.n;
					$html .= t.t.'<div class="section-content ';
					$html .= '" id="full_com">'.n;
				
					// Add Comment
					$view = new JView( array('name'=>'wish', 'layout'=>'addcomment') );
					$view->option = $option;
					$view->refid = $item->id;
					$view->wishid = $item->id;
					$view->juser = $juser;
					$view->level = 0;
					$view->listid = $wishlist->id;
					$view->addcomment = $addcomment;
					$html .= $view->loadTemplate();

					if(isset($item->replies)) {
						//$html .= t.t.t.WishlistHtml::comments($item->replies, $item->id, $juser, $wishlist->id, $option, $addcomment, $item->proposed_by, $abuse).n;
						$o = 'even';
						if (count($item->replies) > 0) {
							$html .= '<ol class="comments pass2">';
							foreach ($item->replies as $reply) 
							{
								$o = ($o == 'odd') ? 'even' : 'odd';
								if($reply->state==4) {
									// comment removed by author
									$html .= '<li class="comment '.$o.' comment-removed">'.JText::_('COMMENT_REMOVED_BY_AUTHOR');
								}
								else {
									// Comment
									$html .= '<li class="comment '.$o;
									if ($this->abuse && $reply->reports > 0) {
										$html .= ' abusive';
									}
									$html .= '" id="c'.$reply->id.'r">';
			
									$view = new JView( array('name'=>'wish', 'layout'=>'comment') );
									$view->option = $option;
									$view->reply = $reply;
									$view->juser = $juser;
									$view->listid = $wishlist->id;
									$view->wishid = $item->id;
									$view->wishauthor = $item->proposed_by;
									$view->level = 1;
									$view->abuse = $abuse;
									$view->addcomment = $addcomment;
									$html .= $view->loadTemplate();
								}
								
								// Another level? 
								if (count($reply->replies) > 0) {
									$html .= '<ol class="comments pass3">';
									foreach ($reply->replies as $r) 
									{
										$o = ($o == 'odd') ? 'even' : 'odd';
										if($r->state==4) {
											// comment removed by author
											$html .= '<li class="comment '.$o.' comment-removed">'.JText::_('COMMENT_REMOVED_BY_AUTHOR');
										}
										else {
											$html .= '<li class="comment '.$o;
											if ($this->abuse && $r->reports > 0) {
												$html .= ' abusive';
											}
											$html .= '" id="c'.$r->id.'r">';
											
											$view = new JView( array('name'=>'wish', 'layout'=>'comment') );
											$view->option = $option;
											$view->reply = $r;
											$view->juser = $juser;
											$view->listid = $wishlist->id;
											$view->wishid = $item->id;
											$view->wishauthor = $item->proposed_by;
											$view->level = 2;
											$view->abuse = $abuse;
											$view->addcomment = $addcomment;
											$html .= $view->loadTemplate();
										}
		
										// Yet another level?? 
										if (count($r->replies) > 0) {
											$html .= '<ol class="comments pass4">';
											foreach ($r->replies as $rr) 
											{
												$o = ($o == 'odd') ? 'even' : 'odd';
												if($rr->state==4) {
													// comment removed by author
													$html .= '<li class="comment '.$o.' comment-removed">'.JText::_('COMMENT_REMOVED_BY_AUTHOR');
													$html .= '</li>';
												}
												else {
													$html .= t.'<li class="comment '.$o;
													if ($this->abuse && $rr->reports > 0) {
														$html .= ' abusive';
													}
													$html .= '" id="c'.$rr->id.'r">';
													$view = new JView( array('name'=>'wish', 'layout'=>'comment') );
													$view->option = $option;
													$view->reply = $rr;
													$view->juser = $juser;
													$view->listid = $wishlist->id;
													$view->wishid = $item->id;
													$view->wishauthor = $item->proposed_by;
													$view->level = 3;
													$view->abuse = $abuse;
													$view->addcomment = $addcomment;
													$html .= $view->loadTemplate();
													$html .= '</li>';
												}
											}
											$html .= '</ol><!-- end pass4 -->';
										}
										$html .= '</li>';
									}
									$html .= '</ol><!-- end pass3 -->';
								}
								$html .= '</li>';
							}
							$html .= '</ol><!-- end pass2 -->';
						}
					}
					if(!isset($item->replies) or count($item->replies)==0) {
						$html .= t.t.t.'<p>'.JText::_('NO_COMMENTS').' <a href="index.php?option='.$option.a.'task=reply'.a.'cat=wish'.a.'id='.$wishlist->id.a.'refid='.$item->id.a.'wishid='.$item->id.'">'.JText::_('MAKE_A_COMMENT').'</a>.</p>'.n;
					}
					$html .= t.t.'</div>'.n;
					$html .= t.'</div>'.n;
				
					// Implementation plan block for list administrators
					if($admin) {  // let advisory committee view this too
						$html .=t.t.'<a name="plan"></a>'.n;
						$html .=t.t.'<div id="section-plan">'.n;
						$html .=t.t.'<h3><a href="javascript:void(0);" class="';
						$html .= 'collapse';
						$html .='" id="part_plan">&nbsp;</a> '.JText::_('IMPLEMENTATION_PLAN').' ';
						$html .= ($plan) ? '(<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=editplan#plan">edit</a>)' : '(not started)';
						$html .= '</h3>'.n;
						$html .= '<div class="aside_note">'.n;
						$html .= '<p class="add"><a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=editplan#plan">'.JText::_('ADD_TO_THE_PLAN').'</a></p>'.n;
						$html .= '</div><!-- / .aside -->'.n;
						$html .=t.t.t.'<div class="section-content" id="full_plan">'.n;
						
						// we are editing
						if($item->action=='editplan') {							
							$document =& JFactory::getDocument();
							$document->addScript('components'.DS.'com_events'.DS.'js'.DS.'calendar.rc4.js');
							$document->addScript('components'.DS.'com_events'.DS.'js'.DS.'events.js');
			
							$html .= '<form action="index.php" method="post" id="hubForm">'.n;
							$html .= t.'<div class="explaination">'.n;
							$html .= t.'	 <p>'.JText::_('You can set a deadline and describe the implementation plan for this wish.').'</p>'.n;
							$html .= t.'</div>'.n;
							$html .= t.'<fieldset style="padding-top:1.5em;">'.n;
							
							// due date						
							$html .= t.t.t.'<label style="display:inline;">'.JText::_('DUE').': '.n;
							$html .= t.t.t.t.'<input class="option" type="radio" name="isdue" id="nodue" value="0" ';
							$html .= ($due == '') ? 'checked="checked"' : '';
							$html .=' /> '.JText::_('DUE_NEVER').n;
							$html .= t.t.t.'</label>'.n;
							$html .= t.t.t.'<span class="or">'.JText::_('OR').'</span>'.n;
							$html .= t.t.t.'<label  style="display:inline;">'.n;
							$html .= t.t.t.t.'<input class="option" type="radio" id="isdue" name="isdue" value="1" ';
							$html .= ($due != '') ? 'checked="checked"' : '';
							$html .=' /> '.n;
							$html .= t.t.t.'</label>'.n;
							$html .= t.t.t.'<label  style="display:inline;">'.JText::_('ON').n;
							$html .= t.t.t.t.'<input class="option" type="text" name="publish_up" id="publish_up" size="10" maxlength="10" value="'.$due.'" />'.n;
							$html .= t.t.t.'</label>'.n;
							
							// Assigned to
							$html .= '<label>'.JText::_('WISH_ASSIGNED_TO').':'.n;
							$html .= $item->assignlist;
							$html .= '</label>'.n;
							
							$html .= t.t.'<label>'.n;
							$html .= t.t.t.JText::_('ACTION_INSERT_TEXT').' ('.JText::_('ACTION_PLEASE_USE').' <a href="/topics/Help:WikiFormatting" rel="external">'.JText::_('WIKI_FORMATTING').'</a>) '.n;	
							if($plan) {
								$html .= t.t.'<div class="newrev">'.n;
								$html .= '<input type="checkbox" class="option" name="create_revision" value="1" />';
								$html .= JText::_('PLAN_NEW_REVISION').n;
								$html .= t.t.'</div>'.n;
							}
							else {
								$html .= '<input type="hidden" name="create_revision" value="0" />';
							}
							$html .= t.t.'</label>'.n;
							$html .= t.t.'<label style="margin-top:-2em;">'.n;
							$html .= t.t.t.'<ul id="wiki-toolbar" class="hidden"></ul>'.n;
							$html .= t.t.t.'<textarea name="pagetext" id="pagetext" rows="40" cols="35">';
							$html .= isset($plan->pagetext) ? $plan->pagetext : '';
							$html .= '</textarea>'.n;
							$html .= t.t.'</label>'.n;
							$html .= '<input type="hidden" name="pageid" value="';
							$html .= isset($plan->id) ? $plan->id : '';
							$html .= '" />'.n;
							$html .= '<input type="hidden" name="version" value="';
							$html .= isset($plan->version) ? $plan->version : 1;
							$html .= '" />'.n;
							$html .= t.t.'<input type="hidden" name="wishid" value="'. $item->id .'" />'.n;
							$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
							$html .= t.t.'<input type="hidden" name="created_by" value="'. $juser->get('id').'" />'.n;
							$html .= t.t.'<input type="hidden" name="task" value="saveplan" />'.n;
							$html .= t.'<p class="submit"><input type="submit" name="submit" value="'.JText::_('SAVE').'" /><span class="cancelaction">';
							$html .= '<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'">';
							$html .= JText::_('CANCEL').'</a></span></p>'.n;
							$html .= t.'</fieldset>'.n;						
							$html .= '</form>'.n;
							$html .= t.'<div class="clear"></div>'.n;
						}
						else if(!$plan) {
							// there is no plan yet
							$html .=t.t.t.'<p>'.JText::_('THERE_IS_NO_PLAN').' <a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=editplan#plan">'.JText::_('START_PLAN').'</a>.</p>'.n;
							if($item->status==0 or $item->status==6) {
								$html .= t.t.t.'<p>';
								$html .= JText::_('PLAN_IS_ASSIGNED').' '.$assigned;
								$html .= ' '.JText::_('PLAN_IS_DUE').' <a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=editplan#plan">';
								$html .= ($due) ? ' '.$due : JText::_('DUE_NEVER');
								$html .= '</a>.';
								$html .=t.t.t.'</p>'.n;
							}
						}
						else {
							// we have a plan!
							if($item->status==0 or $item->status==6) {
								$html .= t.t.t.'<p>';
								$html .= JText::_('PLAN_IS_ASSIGNED').' '.$assigned;
								$html .= ' '.JText::_('PLAN_IS_DUE').' <a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=editplan#plan">';
								$html .= ($due) ? ' '.$due : JText::_('DUE_NEVER');
								$html .= '</a>.';
								$html .=t.t.t.'</p>'.n;
							}
							
							$html .=t.t.t.'<div class="planbody">'.n;
							$html .=t.t.t.'<p class="plannote">'.JText::_('PLAN_LAST_EDIT').' '.JHTML::_('date', $plan->created, '%d %b %Y').' at '.JHTML::_('date',$plan->created, '%I:%M %p').' '.JText::_('by').' '.$plan->authorname.'</p>'.n;
							ximport('wiki.parser');
							$p = new WikiParser( $wishlist->title, $option, 'wishlist'.DS.$wishlist->id, 'resources', $wishlist->id);
							$maintext = $p->parse( n.stripslashes($plan->pagetext) );					
							$html .=t.t.t.$maintext.n;
							$html .=t.t.t.'</div>'.n;
						}
						$html .=t.t.t.'</div>'.n;
						$html .=t.t.'</div>'.n;		
					}
				  } // end if not abusive
				
				$html .= t.'<div class="clear"></div></div>'.n;
			}	// end if not private	
		}
		else {
			// throw error, shouldn't be here
			$html = Hubzero_View_Helper_Html::error(JText::_('ERROR_WISH_NOT_FOUND')).n;
		}
		
		echo $html;	
?>
