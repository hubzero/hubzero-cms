<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

		$title 		= $this->title;
		$option 	= $this->option;
		$wishlist 	= $this->wishlist;
		$admin 		= $this->admin;
		$filters	= $this->filters;
		$error		= $this->getError();
				
				$html  = '<h3>'.n;
				$html .= '   <span><a class="add" href="'.JRoute::_('index.php?option='.$option.a.'task=add'.a.'category='. $wishlist->category.a.'rid='.$wishlist->referenceid) .'">'.JText::_('ADD_NEW_WISH').'</a></span>'.$title.n;
				$html .= '</h3>'.n;
				
				if($wishlist->items) {
					$html  .= t.'<ul id="wishlist">'.n;
					foreach ($wishlist->items as $item) {					
						$item->subject = stripslashes($item->subject);
						$item->subject = str_replace('&quote;','&quot;',$item->subject);
						$item->subject = htmlspecialchars($item->subject);
						$item->bonus = $this->config->get('banking') ? $item->bonus : 0;
						$name = $item->anonymous == 1 ? JText::_('ANONYMOUS') : $item->authorname;
						
						$html  .= t.t.'<li class="reg ';
						$html  .= (isset($item->ranked) && !$item->ranked && $item->status!=1 && ($admin==2 or $admin==3)) ? ' newwish' : '' ;				
						$html  .= ($item->private && $wishlist->public) ? ' private' : '' ;
						$html  .= ($item->status==1) ? ' grantedwish' : '' ;
						$html  .= '">'.n;
				
						$html .= t.t.'<dl class="comment-details">'.n;
						$html .= t.t.t.'<dt><span class="wish_';
						if($item->reports) {
							$html .= 'outstanding';
						}
						else if(isset($item->ranked) && !$item->ranked && $item->status!=1 && $item->status!=3 && $item->status!=4 && ($admin==2 or $admin==3))  {
							$html .= 'unranked';
						}	else if($item->status==1) {
							$html .= 'granted';
						}
						else {
							$html .= 'outstanding';
						}	
						$html .='"></span>';		
						$html .='</dt>'.n;
						$html .= t.t.'</dl>'.n;	
						
						$html .= t.t.'<div class="ensemble_left">'.n;
						if(!$item->reports) {
							$html .= t.t.t.'<p class="wishcontent"><a href="index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id.a.'filterby='.$filters['filterby'].'" class="wishtitle" title="'.htmlspecialchars(Hubzero_View_Helper_Html::xhtml($item->about)).'" >'.Hubzero_View_Helper_Html::shortenText($item->subject, 160, 0).'</a></p>'.n;
							$html .= t.t.t.'<p class="proposed">'.JText::_('WISH_PROPOSED_BY').' '.$name.' '.JText::_('ON').' '.JHTML::_('date',$item->proposed, '%d %b %Y');
							$html .= ', <a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?com=1#comments">'.$item->numreplies; 
							$html .= '<span class="nobreak">';
							$html .= $item->numreplies==1 ? ' '.JText::_('COMMENT') : ' '.JText::_('COMMENTS');
							$html .= '</span>';
							$html .= '</a>';
							if($admin && $admin != 3) {
								$assigned = $item->assignedto ? $item->assignedto : JText::_('UNKNOWN');
								$html .= $item->assigned ? '<br /> '.JText::_('WISH_ASSIGNED_TO').' '.$assigned : '';
							}
							$html .= '</p>'.n;
						}
						else {
							$html .= t.t.t.'<p class="warning adjust">'.JText::_('NOTICE_POSTING_REPORTED').'</p>'.n;
						}
						$html .= t.t.'</div>'.n;
						if(!$item->reports) {
						$html .= t.t.'<div class="ensemble_right">'.n;					
						// admin ranking			
						if(($admin or $item->status==1 or ($item->status==0 && $item->accepted==1) or $item->status==3 or $item->status==4) && !$item->reports) {
							$html .= t.t.t.'<div class="wishranking">'.n;
							$html .=($item->status==1) ?' <span class="special priority_number">'.JText::_('WISH_STATUS_GRANTED').'</span>': '';
							$html .=($item->status==1 && $item->granted!='0000-00-00 00:00:00') ?' <span class="mini">'.strtolower(JText::_('ON')).' '.JHTML::_('date',$item->granted, '%d %b %y').'</span>': '';
							if(isset($item->ranked) && !$item->ranked && $item->status==0 && ($admin==2 or $admin==3)) {
								$html .= t.t.t.'<a class="rankit" href="index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id.a.'filterby='.$filters['filterby'].'">'.JText::_('WISH_RANK_THIS').'</a>'.n;
							} else if(isset($item->ranked) && $item->ranked && $item->status==0) {
								$html .= t.t.t.'<span>'.JText::_('WISH_PRIORITY').': <span class="priority_number">'.$item->ranking.'</span></span>'.n;
							}
							$html .= ($item->status==0 && $item->accepted==1) ? '<span class="special accepted">'.JText::_('WISH_STATUS_ACCEPTED').'</span>' : '';
							$html .= ($item->status==3) ? '<span class="special rejected">'.JText::_('WISH_STATUS_REJECTED').'</span>' : '';
							$html .= ($item->status==4) ? '<span class="special withdrawn">'.JText::_('WISH_STATUS_WITHDRAWN').'</span>' : '';
							$html .=t.t.t.'</div>'.n;
						}
					
						// Thumbs ratings
						$html .= t.t.t.'<div id="wishlist_'.$item->id.'" class="'.$option.' intermed">';
						jimport('joomla.application.component.view');
						$view = new JView( array('name'=>'rateitem','base_path' => JPATH_ROOT.DS.'components'.DS.$option) );
						$view->option = $option;
						$view->item = $item;
						$view->listid = $wishlist->id;
						$view->plugin = 1;
						$view->admin = 0;
						$view->page = 'wishlist';
						$view->filters = $filters;
						$html .= $view->loadTemplate();											
						$html .= t.t.t.'</div>'.n;	
						//$html .= WishlistHtml::rateitem($item, $juser, $option, $wishlist->id, 0, 'wishlist', 1, $filters);									
					
						// Points				
						if($this->config->get('banking')) {
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
						$html .= t.t.'</div> <!-- end ensemble right -->';
					} // end if no abuse
						$html .= t.t.'<div style="clear:left;"></div>'.n;
						$html  .= t.t.'</li>'.n;				
					}
					$html .= t.'</ul>'.n;
				}
				else {
					if($filters['filterby']=="all" && !$filters['tag']) {
						$html .= t.t.t.'<p>'.JText::_('WISHLIST_NO_WISHES_BE_FIRST').'</p>'.n;
					}
					else {
						$html .= t.t.t.'<p class="noresults">'.JText::_('WISHLIST_NO_WISHES_SELECTION').'</p>'.n;
						$html .= t.t.t.'<p class="nav_wishlist"><a href="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a.'category='. $wishlist->category.a.'rid='.$wishlist->referenceid) .'">'.JText::_('WISHLIST_VIEW_ALL_WISHES').'</a></p>'.n;	
					}
				}			
				
				echo $html;
?>