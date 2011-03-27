<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
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

/* Wish List */

		$wishlist = $this->wishlist; 
		$title = $this->title;
		$option = $this->option;
		$task = $this->task;
		$admin = $this->admin;
		$error = $this->getError();
		$filters = $this->filters; 
		$juser = $this->juser;
		$pageNav = $this->pageNav;
		$abuse = $this->abuse;
		$database = $this->database;
	
		$html = '';		
		$xhub =& Hubzero_Factory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');				
		
		if($wishlist) {	
		  if(!$wishlist->public	 && !$admin) {
		  	$html .= '<div class="main section">'.n;
			$html .= Hubzero_View_Helper_Html::warning(JText::_('WARNING_NOT_AUTHORIZED_PRIVATE_LIST')).n;
			$html .= '</div>'.n;	
		  }
		  else {
			$html .= Hubzero_View_Helper_Html::div( Hubzero_View_Helper_Html::hed( 2, $title ), '', 'content-header' );
			
			// admin messages
			if($admin && !$error) {
				// wish was deleted from the list
				if($task == 'deletewish') {
				$html  .= '<p class="passed">'.JText::_('NOTICE_WISH_DELETED').'</p>'.n;
				}
			
				// wish was moved to a new list
				if($task == 'movewish') {
				$html  .= '<p class="passed">'.JText::_('NOTICE_WISH_MOVED').'</p>'.n;
				}
			
				switch($wishlist->saved ) 
				{
					case '1':
					// List settings saved    
					$html  .= '<p class="passed">'.JText::_('NOTICE_LIST_SETTINGS_SAVED').'</p>'.n;
					break;
					case '2':
					// Changes to wish saved  
					$html  .= '<p class="passed">'.JText::_('NOTICE_WISH_CHANGES_SAVED').'</p>'.n;
					break;
					case '3': 
					// New wish posted     
					$html  .= '<p class="passed">'.JText::_('NOTICE_WISH_POSTED').'</p>'.n;
					break;
				}
			}
			
			// display error
			if($error) {
				$html .= Hubzero_View_Helper_Html::error($error).n;
			}
								
			// navigation options
			$html .= '<div id="content-header-extra">'.n;
			$html .= t.'<ul id="useroptions">'.n;
			$html .= t.t.'<li class="last"><a class="add" href="'.JRoute::_('index.php?option='.$option.a.'task=add'.a.'category='. $wishlist->category.a.'rid='.$wishlist->referenceid) .'">'.JText::_('TASK_ADD').'</a></li>'.n;
			$html .= t.'</ul>'.n;
			$html .= '</div><!-- / #content-header-extra -->'.n;	
			
			$html .= '<div class="main section">'.n;
			$html .= t.'<form method="get" action="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid).'">'.n;		
			
			// Display wishlist description
			$html .= t.'<div class="aside">'.n;			
			//$html .= WishlistHtml::browseForm($option, $filters, $admin, $wishlist->id, count($wishlist->items), $wishlist, $pageNav);
			$sortbys = array();
			if($admin) {
				$sortbys['ranking']=JText::_('RANKING');
			}
			$sortbys['date'] = JText::_('DATE');
			$sortbys['feedback'] = JText::_('FEEDBACK');
			
			if($wishlist->banking) {
				$sortbys['bonus']=JText::_('BONUS_AND_POPULARITY');
			}
			$filterbys = array('all'=>JText::_('ALL_WISHES_ON_THIS_LIST'),'open'=>JText::_('ACTIVE'),'granted'=>JText::_('GRANTED'), 'accepted'=>JText::_('WISH_STATUS_ACCEPTED'), 'rejected'=>JText::_('WISH_STATUS_REJECTED'));
			
			if($admin == 1 or $admin == 2) { // a few extra options
				$filterbys['private'] = JText::_('PRIVATE');
				$filterbys['public'] = JText::_('PUBLIC');
				if($admin == 2) {
					$filterbys['mine'] = JText::_('MSG_ASSIGNED_TO_ME');
				}
			}
			if(!$juser->get('guest')) {
				$filterbys['submitter'] = JText::_('Submitted by me');
			}

			$html .= t.t.'<fieldset>'.n;
			$html .= t.t.'<label class="tagdisplay">'.JText::_('WISH_FIND_BY_TAGS').': '.n;
	
			JPluginHelper::importPlugin( 'hubzero' );
			$dispatcher =& JDispatcher::getInstance();
			$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',$filters['tag'])) );	
				
			if (count($tf) > 0) {
				$html .= $tf[0];
			} else {
				$html .= t.t.t.'<input type="text" name="tags" id="tags-men" value="'.$filters['tag'].'" />'.n;
			}
			$html .= '</label>';
			// get popular tags
			if($wishlist->category=='general') {
				$obj = new TagsTag( $database );
				$tags = $obj->getTopTags( 5, 'wishlist', 'tcount DESC', 0 );
				
				if ($tags) {
					$html .= '<p>'.JText::_('WISHLIST_POPULAR_TAGS').'</p>'."\n";
					$html .= '<ol class="tags">'."\n";
					$tll = array();
					foreach ($tags as $tag)
					{
						$class = ($tag->admin == 1) ? ' class="admin"' : '';
				
						$tag->raw_tag = str_replace( '&amp;', '&', $tag->raw_tag );
						$tag->raw_tag = str_replace( '&', '&amp;', $tag->raw_tag );
						$tll[$tag->tag] = "\t".'<li'.$class.'><a href="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'filterby='.$filters['filterby'].a.'sortby='.$filters['sortby'].a.'tags='.$tag->tag).'">'.stripslashes($tag->raw_tag).'</a></li>'."\n";				
					}
					ksort($tll);
					$html .= implode('',$tll);
					$html .= '</ol><br />'."\n";
				}
			}
			$html .= t.t.t.'<label >'.JText::_('SHOW').': '.n;
			$html .= WishlistHtml::formSelect('filterby', $filterbys, $filters['filterby'], '', '');
			$html .= t.t.t.'</label>'.n;		
			$html .= t.t.t.' &nbsp; <label> '.JText::_('SORTBY').':'.n;
			$html .= WishlistHtml::formSelect('sortby', $sortbys, $filters['sortby'], '', '');
			$html .= t.t.t.'</label>'.n;
			$html .= t.t.'<input type="hidden" name="newsearch" value="1" />'.n;
			$html .= t.t.t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
			$html .= t.t.'</fieldset>'.n;
							
			if(isset($wishlist->resource) && $wishlist->category== 'resource') {
				$html .= t.t.'<p>'.JText::_('THIS_LIST_IS_FOR').' ';
				$type  = substr($wishlist->resource->typetitle,0,strlen($wishlist->resource->typetitle) - 1);
				$html .= strtolower($type).' '.JText::_('RESOURCE_ENTITLED').' <a href="'.JRoute::_('index.php?option=com_resources'.a.'id='.$wishlist->referenceid).'">'.$wishlist->resource->title.'</a>.';
				$html .= '</p>'.n;
			}
			else if($wishlist->description) {
				$html .= t.t.'<p>'.$wishlist->description.'<p>';
			}
			else {
				$html .= t.t.'<p>'.JText::_('HELP_US_IMPROVE').' '.$hubShortName.' '.JText::_('HELP_IMPROVE_BY_IDEAS').'</p>';
			}				
			
			switch($admin ) 
			{
				case '1':
				$html .= t.'<p class="info">'.JText::_('NOTICE_SITE_ADMIN').'</p>'.n;
				break;
				case '2':
				$html .= t.'<p class="info">'.JText::_('NOTICE_LIST_ADMIN').' Edit <a href="'.JRoute::_('index.php?option='.$option.a.'task=settings'.a.'id='. $wishlist->id) .'">'.JText::_('LIST_SETTINGS').'</a>.</p>'.n;
				break;
				case '3':
				$html .= t.'<p class="info">'.JText::_('NOTICE_ADVISORY_ADMIN').'</p>'.n;
				break;
			}		
			
			if(($admin==2 or $admin==3) && count($wishlist->items) >= 10 && $wishlist->category=='general' && $filters['filterby']=='all') {
				// show what's popular
				ximport('Hubzero_Module_Helper');
				$html .= Hubzero_Module_Helper::renderModules('wishvoters');
			}
			$html .= t.'</div><!-- / .aside -->'.n;
			$html .= t.'<div class="subject">'.n;		
			// Display items
			if($wishlist->items) {
				$html .= t.t.t.'<p class="note_total" >'.JText::_('NOTICE_DISPLAYING').' ';
				if($filters['start'] == 0) {
					$html .= $pageNav->total > count($wishlist->items) ? ' '.JText::_('NOTICE_TOP').' '.count($wishlist->items).' '.JText::_('NOTICE_OUT_OF').' '.$pageNav->total : strtolower(JText::_('ALL')).' '.count($wishlist->items) ;
				}
				else {
					$html .= ($filters['start'] + 1);
					$html .= ' - ';
					$html .=$filters['start'] + count($wishlist->items);
					$html .=' out of '.$pageNav->total;
				}
				$html .= ' '.strtolower(JText::_('WISHES'));
				$html .= $filters['tag'] != '' ? ' '.JText::_('WISHES_TAGGED_WITH').' <span class="tagname">'.$filters['tag'].'</span>.' : '.';
				if($this->config->get('show_percentage_granted') && $filters['filterby']=='all') {
					$html .= ' '.JText::_('PERCENT_GRANTED_WISHES').': '.$wishlist->granted_percentage.'% '.JText::_('FROM_TOTAL').'.';
				}
				$html .= '</p>'.n;
			
				$html  .= t.'<ul id="wishlist">'.n;
				$y = 1;			
				foreach ($wishlist->items as $item) {
				
					// Do some text cleanup
					$item->subject = stripslashes($item->subject);
					$item->subject = str_replace('&quote;','&quot;',$item->subject);
					$item->subject = htmlspecialchars($item->subject);
					$item->bonus = $wishlist->banking ? $item->bonus : 0;				
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
					
					// wish title & text details				
					$html .= t.t.'<div class="ensemble_left">'.n;
					if(!$item->reports) {
						$html .= t.t.t.'<p class="wishcontent"><a href="index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id.a.'filterby='.$filters['filterby'].a.'sortby='.$filters['sortby'].a.'tags='.$filters['tag'].'" class="wishtitle" title="'.htmlspecialchars(Hubzero_View_Helper_Html::xhtml($item->about)).'" >'.Hubzero_View_Helper_Html::shortenText($item->subject, 160, 0).'</a></p>'.n;
						$html .= t.t.t.'<p class="proposed">'.JText::_('WISH_PROPOSED_BY').' '.$name.' '.JText::_('ON').' '.JHTML::_('date',$item->proposed, '%d %b %Y');
						$html .= ', <a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?com=1'.a.'filterby='.$filters['filterby'].a.'sortby='.$filters['sortby'].a.'tags='.$filters['tag'].'#comments">'.$item->numreplies; 
						$html .= '<span class="nobreak">';
						$html .= $item->numreplies==1 ? ' '.JText::_('COMMENT') : ' '.JText::_('COMMENTS');
						$html .= '</span>';
						$html .= '</a>';
						if($admin && $admin != 3) {
							$assigned = $item->assignedto ? $item->assignedto : JText::_('UNKNOWN');
							$html .= $item->assigned ? '<br /> '.JText::_('WISH_ASSIGNED_TO').' '.$assigned : '';
							if($item->status==1) {
								$grantedby = $item->grantedby ? $item->grantedby : JText::_('UNKNOWN');
								$html .= $item->grantedby ? ', '.JText::_('WISH_GRANTED_BY').' '.$grantedby : '';
							}
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
								$html .= t.t.t.'<a class="rankit" href="index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id.a.'filterby='.$filters['filterby'].a.'sortby='.$filters['sortby'].a.'tags='.$filters['tag'].'">'.JText::_('WISH_RANK_THIS').'</a>'.n;
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
						$view = new JView( array('name'=>'rateitem') );
						$view->option = $this->option;
						$view->item = $item;
						$view->listid = $wishlist->id;
						$view->plugin = 0;
						$view->admin = 0;
						$view->page = 'wishlist';
						$view->filters = $filters;
						$html .= $view->loadTemplate();												
						$html .= t.t.t.'</div>'.n;
					
						// Points				
						if($wishlist->banking) {
							$html .= t.t.t.'<div class="assign_bonus">'.n;				
							if(isset($item->bonus) && $item->bonus > 0 && ($item->status==0 or $item->status==6)) {
								$html .= t.t.t.'<a class="bonus tooltips" href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=addbonus'.a.'filterby='.$filters['filterby'].a.'sortby='.$filters['sortby'].a.'tags='.$filters['tag'].'#action" title="'.JText::_('WISH_ADD_BONUS').' ::'.$item->bonusgivenby.' '.JText::_('MULTIPLE_USERS').' '.JText::_('WISH_BONUS_CONTRIBUTED_TOTAL').' '.$item->bonus.' '.JText::_('POINTS').' '.JText::_('WISH_BONUS_AS_BONUS').'">+ '.$item->bonus.'</a>'.n;
							}
							else if($item->status==0 or $item->status==6) {
								$html .= t.t.t.'<a class="nobonus tooltips" href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=addbonus'.a.'filterby='.$filters['filterby'].a.'sortby='.$filters['sortby'].a.'tags='.$filters['tag'].'#action" title="'.JText::_('WISH_ADD_BONUS').' :: '.JText::_('WISH_BONUS_NO_USERS_CONTRIBUTED').'">&nbsp;</a>'.n;
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
				  } // end foreach wish
							
				$html .= t.'</ul>'.n;
				// Page navigation
				$pagenavhtml = $pageNav->getListFooter();
				$pagenavhtml = str_replace('wishlist/?','wishlist/'.$wishlist->category.'/'.$wishlist->referenceid.'/?',$pagenavhtml);
				$pagenavhtml = str_replace('newsearch=1','newsearch=0',$pagenavhtml);
				$html .= t.t.t.t.t.$pagenavhtml;
			} // end if wishlist items
			else {
				if($filters['filterby']=="all" && !$filters['tag']) {
					$html .= t.t.t.'<p>'.JText::_('WISHLIST_NO_WISHES_BE_FIRST').'</p>'.n;
				}
				else {
					$html .= t.t.t.'<p class="noresults">'.JText::_('WISHLIST_NO_WISHES_SELECTION').'</p>'.n;
					$html .= t.t.t.'<p class="nav_wishlist"><a href="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a.'category='. $wishlist->category.a.'rid='.$wishlist->referenceid) .'">'.JText::_('WISHLIST_VIEW_ALL_WISHES').'</a></p>'.n;	
				}
			}		
			$html .= '</div></form><div class="clear"></div></div>'.n;			
		  } // end if public
		}  // end if wish list
		else {
			// Display error
			$html  .= Hubzero_View_Helper_Html::error(JText::_('ERROR_LIST_NOT_FOUND')).n;
		}
		
		// HTML output
		echo $html;
?>
