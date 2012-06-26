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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
$jconfig = JFactory::getConfig();
$sitename = $jconfig->getValue('config.sitename');

/* Wish List */
if ($this->wishlist) {	
	if (!$this->wishlist->public && !$this->admin) {
?>
		<div class="main section">
			<p class="waring"><?php echo JText::_('WARNING_NOT_AUTHORIZED_PRIVATE_LIST'); ?></p>
		</div><!-- / .main section -->
<?php
	} else {
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last"><a class="add" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=add&category='. $this->wishlist->category.'&rid='.$this->wishlist->referenceid); ?>"><?php echo JText::_('TASK_ADD'); ?></a></li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
<?php 
// Admin messages
if ($this->admin && !$this->getError()) {
	// Wish was deleted from the list
	if ($this->task == 'deletewish') {
		echo '<p class="passed">'.JText::_('NOTICE_WISH_DELETED').'</p>'."\n";
	}
	
	// Wish was moved to a new list
	if ($this->task == 'movewish') {
		echo '<p class="passed">'.JText::_('NOTICE_WISH_MOVED').'</p>'."\n";
	}
	
	switch ($this->wishlist->saved) 
	{
		case '1':
			// List settings saved    
			echo '<p class="passed">'.JText::_('NOTICE_LIST_SETTINGS_SAVED').'</p>'."\n";
		break;
		case '2':
			// Changes to wish saved  
			echo '<p class="passed">'.JText::_('NOTICE_WISH_CHANGES_SAVED').'</p>'."\n";
		break;
		case '3': 
			// New wish posted     
			echo '<p class="passed">'.JText::_('NOTICE_WISH_POSTED').'</p>'."\n";
		break;
	}
}
?>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

	<form method="get" action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid); ?>">
		<div class="aside">			
<?php 
	// Popular tags
	if ($this->wishlist->category == 'general') {
		$obj = new TagsTag( $this->database );
		$tags = $obj->getTopTags( 5, 'wishlist', 'tcount DESC', 0 );
				
		if ($tags) { ?>
			<div class="container">
				<h3><?php echo JText::_('WISHLIST_POPULAR_TAGS'); ?></h3>
				<span class="starter"><span class="starter-point"></span></span>
				<ol class="tags">
<?php
					$tll = array();
					foreach ($tags as $tag)
					{
						$class = ($tag->admin == 1) ? ' class="admin"' : '';
				
						$tag->raw_tag = str_replace( '&amp;', '&', $tag->raw_tag );
						$tag->raw_tag = str_replace( '&', '&amp;', $tag->raw_tag );
						$tll[$tag->tag] = "\t\t\t\t\t".'<li'.$class.'><a href="'.JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&filterby='.$this->filters['filterby'].'&sortby='.$this->filters['sortby'].'&tags='.$tag->tag).'">'.stripslashes($tag->raw_tag).'</a></li>'."\n";				
					}
					ksort($tll);
					echo implode('',$tll);
?>
				</ol>
			</div><!-- / .container -->
<?php 
		} // end if ($tags)
	} // end if ($this->wishlist->category == 'general')
	
	if (isset($this->wishlist->resource) && $this->wishlist->category == 'resource') {
		$html  = '<p>'.JText::_('THIS_LIST_IS_FOR').' ';
		$html .= strtolower(substr($this->wishlist->resource->typetitle,0,strlen($this->wishlist->resource->typetitle) - 1)).' '.JText::_('RESOURCE_ENTITLED').' <a href="'.JRoute::_('index.php?option=com_resources&id='.$this->wishlist->referenceid).'">'.$this->wishlist->resource->title.'</a>.';
		$html .= '</p>'."\n";
	} else if ($this->wishlist->description) {
		$html  = '<p>'.$this->wishlist->description.'<p>';
	} else {
		$xhub =& Hubzero_Factory::getHub();	
		$html  = '<p>'.JText::_('HELP_US_IMPROVE').' '.$sitename.' '.JText::_('HELP_IMPROVE_BY_IDEAS').'</p>';
	}				
			
	switch ($this->admin) 
	{
		case '1':
			$html .= '<p class="info">'.JText::_('NOTICE_SITE_ADMIN').'</p>'."\n";
		break;
		case '2':
			$html .= '<p class="info">'.JText::_('NOTICE_LIST_ADMIN').' Edit <a href="'.JRoute::_('index.php?option='.$this->option.'&task=settings&id='. $this->wishlist->id) .'">'.JText::_('LIST_SETTINGS').'</a>.</p>'."\n";
		break;
		case '3':
			$html .= '<p class="info">'.JText::_('NOTICE_ADVISORY_ADMIN').'</p>'."\n";
		break;
	}
	echo $html;	
			
	// Show what's popular
	if (($this->admin == 2 || $this->admin == 3) 
	 && count($this->wishlist->items) >= 10 
	 && $this->wishlist->category == 'general' 
	 && $this->filters['filterby'] == 'all') {
		ximport('Hubzero_Module_Helper');
		echo Hubzero_Module_Helper::renderModules('wishvoters');
	}
?>
		</div><!-- / .aside -->
		<div class="subject">
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="Search" />
				<fieldset class="entry-search">
<?php
				JPluginHelper::importPlugin( 'hubzero' );
				$dispatcher =& JDispatcher::getInstance();
				$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',$this->filters['tag'])) );
?>
					<label for="actags">
						<?php echo JText::_('WISH_FIND_BY_TAGS'); ?>
					</label>
<?php 
				if (count($tf) > 0) {
					echo $tf[0];
				} else { ?>
					<input type="text" name="tags" id="tags-men" value="<?php echo $this->filters['tag']; ?>" />
<?php 
				} 
?>
					<input type="hidden" name="sortby" value="<?php echo htmlentities($this->filters['sortby']); /* xss fix for ticket 1413/1417 */ ?>" />
					<input type="hidden" name="filterby" value="<?php echo htmlentities($this->filters['filterby']); /* xss fix for ticket 1412/1419 */?>" />

					<input type="hidden" name="task" value="<?php echo htmlentities($this->task); /* XSS fix, see ticket 1420*/ ?>" />
					<input type="hidden" name="newsearch" value="1" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				</fieldset>
			</div><!-- / .container data-entry -->
	
			<div class="container">
				<ul class="entries-menu order-options">
<?php 			if ($this->admin) { ?>
					<li><a<?php if ($this->filters['sortby'] == 'ranking') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&filterby='.$this->filters['filterby'].'&sortby=ranking&tags='.$this->filters['tag']); ?>" title="Sort by ranking">&darr; <?php echo JText::_('Ranking'); ?></a></li>
<?php 			} ?>
<?php 			if ($this->wishlist->banking) { ?>
					<li><a<?php if ($this->filters['sortby'] == 'bonus') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&filterby='.$this->filters['filterby'].'&sortby=bonus&tags='.$this->filters['tag']); ?>" title="Sort by largest to smallest bonus">&darr; <?php echo JText::_('Bonus'); ?></a></li>
<?php 			} ?>
					<li><a<?php if ($this->filters['sortby'] == 'feedback') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&filterby='.$this->filters['filterby'].'&sortby=feedback&tags='.$this->filters['tag']); ?>" title="Sort by number of comments">&darr; <?php echo JText::_('Feedback'); ?></a></li>
					<li><a<?php if ($this->filters['sortby'] == 'submitter') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&filterby='.$this->filters['filterby'].'&sortby=submitter&tags='.$this->filters['tag']); ?>" title="Sort by submitter">&darr; <?php echo JText::_('Submitter'); ?></a></li>
					<li><a<?php if ($this->filters['sortby'] == 'date') { echo ' class="active"'; } ?>  href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&filterby='.$this->filters['filterby'].'&sortby=date&tags='.$this->filters['tag']); ?>" title="Sort by newest to oldest">&darr; <?php echo JText::_('Date'); ?></a></li>
				</ul>

				<ul class="entries-menu filter-options">
					<li><a<?php if ($this->filters['filterby'] == 'all') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&filterby=all&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo JText::_('All'); ?></a></li>
					<li><a<?php if ($this->filters['filterby'] == 'open') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&filterby=open&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo JText::_('Active'); ?></a></li>
					<li><a<?php if ($this->filters['filterby'] == 'accepted') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&filterby=accepted&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo JText::_('Accepted'); ?></a></li>
					<li><a<?php if ($this->filters['filterby'] == 'rejected') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&filterby=rejected&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo JText::_('Rejected'); ?></a></li>
					<li><a<?php if ($this->filters['filterby'] == 'granted') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&filterby=granted&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo JText::_('Granted'); ?></a></li>
<?php 			if (!$this->juser->get('guest')) { ?>
					<li><a<?php if ($this->filters['filterby'] == 'submitter') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&filterby=submitter&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo JText::_('Submitted by me'); ?></a></li>
<?php 			} ?>
<?php 			if ($this->admin == 1 || $this->admin == 2) { ?>
					<li><a<?php if ($this->filters['filterby'] == 'public') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&filterby=public&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo JText::_('Public'); ?></a></li>
					<li><a<?php if ($this->filters['filterby'] == 'private') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&filterby=private&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo JText::_('Private'); ?></a></li>
<?php 				if ($this->admin == 2) { ?>
					<li><a<?php if ($this->filters['filterby'] == 'mine') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&filterby=mine&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo JText::_('Assigned to me'); ?></a></li>
<?php 				} ?>
<?php 			} ?>
				</ul>

				<table class="ideas entries" summary="<?php echo JText::_('Ideas submitted by the community'); ?>">
					<caption>
						<?php echo JText::_('COM_WISHLIST_FILTER_'.strtoupper($this->filters['filterby'])); ?> 
						<?php echo ($this->filters['tag'] != '') ? JText::_('WISHES_TAGGED_WITH').' "'.$this->filters['tag'].'"' : ''; ?>
						<span>
							(<?php echo ($this->filters['start'] + 1); ?> - <?php echo $this->filters['start'] + count($this->wishlist->items); ?> of <?php echo $this->pageNav->total; ?>)
						</span>
					</caption>
					<tbody>
<?php
				if ($this->wishlist->items) {
					$y = 1;			
					foreach ($this->wishlist->items as $item) 
					{	
						// Do some text cleanup
						$item->subject = stripslashes($item->subject);
						$item->subject = str_replace('&quote;','&quot;',$item->subject);
						$item->subject = htmlspecialchars($item->subject);
						$item->bonus = ($this->wishlist->banking) ? $item->bonus : 0;

						if ($item->reports) {
							$status = 'outstanding';
						} else if (isset($item->ranked) && !$item->ranked && $item->status!=1 && $item->status!=3 && $item->status!=4 && ($this->admin==2 or $this->admin==3))  {
							$status = 'unranked';
						} else {
							$status = 'outstanding';
						}
				
						$state  = (isset($item->ranked) && !$item->ranked && $item->status!=1 && ($this->admin==2 or $this->admin==3)) ? 'new' : '' ;				
						$state .= ($item->private && $this->wishlist->public) ? ' private' : '' ;
						switch ($item->status) 
						{
							case 3:
								$state .= ' rejected';
							break;
							case 2:
								$state .= '';
							break;
							case 1:
								$state .= ' granted';
							break;
							case 0:
							default:
								if ($item->accepted == 1) {
									$state .= ' accepted';
								} else {
									$state .= ' pending';
								}
							break;
						}
						
						if (!$item->anonymous) {
							$item->authorname = '<a href="'.JRoute::_('index.php?option=com_members&id='.$item->proposed_by).'">'.$item->authorname.'</a>';
						}
?>
						<tr class="<?php echo $state; ?>">
							<th class="<?php echo $status; ?>">
								<span class="entry-id"><?php echo $item->id; ?></span>
							</th>
							<td>
<?php 					if (!$item->reports) { ?>
								<a class="entry-title" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$item->id.'&filterby='.$this->filters['filterby'].'&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo $item->subject; ?></a><br />
								<span class="entry-details">
									<?php echo JText::_('WISH_PROPOSED_BY'); ?> <?php echo ($item->anonymous == 1) ? JText::_('ANONYMOUS') : $item->authorname; ?> @ 
									<span class="entry-time"><?php echo JHTML::_('date', $item->proposed, '%I:%M %p', 0); ?></span> <?php echo JText::_('on'); ?> 
									<span class="entry-date"><?php echo JHTML::_('date', $item->proposed, '%d %b %Y', 0); ?></span>
									<span class="entry-details-divider">&bull;</span>
									<span class="entry-comments"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$item->id.'&com=1&filterby='.$this->filters['filterby'].'&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag'].'#comments'); ?>" title="<?php echo $item->numreplies; ?> <?php echo JText::_('COMMENTS'); ?>"><?php echo $item->numreplies; ?></a></span>
								</span>
<?php 					} else { ?>
								<span class="warning adjust"><?php echo JText::_('NOTICE_POSTING_REPORTED'); ?></span>
<?php 					} ?>
							</td>			
<?php 					if ($this->wishlist->banking) { ?>
							<td class="reward">
								<span class="entry-reward">			
<?php 							if (isset($item->bonus) && $item->bonus > 0 && ($item->status==0 or $item->status==6)) { ?>
									<a class="bonus tooltips" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$item->id.'&action=addbonus&filterby='.$this->filters['filterby'].'&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag'].'#action'); ?>" title="<?php echo JText::_('WISH_ADD_BONUS').' ::'.$item->bonusgivenby.' '.JText::_('MULTIPLE_USERS').' '.JText::_('WISH_BONUS_CONTRIBUTED_TOTAL').' '.$item->bonus.' '.JText::_('POINTS').' '.JText::_('WISH_BONUS_AS_BONUS'); ?>"><?php echo $item->bonus; ?> <span><?php echo JText::_('POINTS'); ?></span></a>
<?php 							} else if ($item->status == 0 || $item->status == 6) { ?>
									<a class="nobonus tooltips" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$item->id.'&action=addbonus&filterby='.$this->filters['filterby'].'&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag'].'#action'); ?>" title="<?php echo JText::_('WISH_ADD_BONUS').' :: '.JText::_('WISH_BONUS_NO_USERS_CONTRIBUTED'); ?>"><?php echo $item->bonus; ?> <span><?php echo JText::_('POINTS'); ?></span></a>
<?php 							} else { ?>
									<span class="inactive" title="<?php echo JText::_('WISH_BONUS_NOT_ACCEPTED'); ?>">&nbsp;</span>
<?php 							} ?>
								</span>
							</td>
<?php 					} ?>
<?php 					if (!$item->reports) { ?>
							<td class="voting">
<?php
								$view = new JView( array('name'=>'rateitem') );
								$view->option = $this->option;
								$view->item = $item;
								$view->listid = $this->wishlist->id;
								$view->plugin = 0;
								$view->admin = 0;
								$view->page = 'wishlist';
								$view->filters = $this->filters;
								$view->display();
?>
							</td>
							<td class="ranking">
<?php 						/*if ($this->admin 
								|| $item->status == 1 
								|| ($item->status == 0 && $item->accepted == 1) 
								|| $item->status == 3 
								|| $item->status == 4
							) { */
								$html = '';
								switch ($item->status) 
								{
									case 0:
										if (isset($item->ranked) && !$item->ranked && ($this->admin==2 or $this->admin==3)) {
											$html .= '<a class="rankit" href="index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid.'&wishid='.$item->id.'&filterby='.$this->filters['filterby'].'&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag'].'">'.JText::_('WISH_RANK_THIS').'</a>'."\n";
										} else if (isset($item->ranked) && $item->ranked) {
											//$html .= JText::_('WISH_PRIORITY').': <span class="priority">'.$item->ranking.'</span>'."\n";
											$html .= '<span class="priority-level-base">
												<span class="priority-level" style="width: '.(($item->ranking/50)*100).'%">
													<span>'.JText::_('WISH_PRIORITY').': '.$item->ranking.'</span>
												</span>
											</span>';
										}
										if ($item->accepted == 1) {
											$html .= '<span class="accepted">'.JText::_('WISH_STATUS_ACCEPTED').'</span>';
										}
									break;
									case 1:
										$html .= '<span class="granted">'.JText::_('WISH_STATUS_GRANTED').'</span>';
										/*if ($item->granted != '0000-00-00 00:00:00') {
											$html .= ' <span class="mini">'.strtolower(JText::_('ON')).' '.JHTML::_('date',$item->granted, '%d %b %y').'</span>';
										}*/
									break;
									case 3:
										$html .= '<span class="rejected">'.JText::_('WISH_STATUS_REJECTED').'</span>';
									break;
									case 4:
										$html .= '<span class="withdrawn">'.JText::_('WISH_STATUS_WITHDRAWN').'</span>';
									break;
								}
								echo $html;
 							//} ?>
							</td>
<?php 					} // end if (!$item->reports) ?>
						</tr>
<?php
					} // end foreach wish
				} else {
?>
						<tr>
							<td>
<?php 				if ($this->filters['filterby'] == 'all' && !$this->filters['tag']) { ?>
								<p><?php echo JText::_('WISHLIST_NO_WISHES_BE_FIRST'); ?></p>
<?php 				} else { ?>
								<p class="noresults"><?php echo JText::_('WISHLIST_NO_WISHES_SELECTION'); ?></p>
								<p class="nav_wishlist"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wishlist&category='. $this->wishlist->category.'&rid='.$this->wishlist->referenceid); ?>"><?php echo JText::_('WISHLIST_VIEW_ALL_WISHES'); ?></a></p>
<?php 				} ?>
							</td>
						</tr>

<?php
				} // end if wishlist items
?>
					</tbody>
				</table>
<?php
				// Page navigation
				$pagenavhtml = $this->pageNav->getListFooter();
				$pagenavhtml = str_replace('wishlist/?','wishlist/'.$this->wishlist->category.'/'.$this->wishlist->referenceid.'/?',$pagenavhtml);
				$pagenavhtml = str_replace('newsearch=1','newsearch=0',$pagenavhtml);
				$pagenavhtml = str_replace('?/wishlist/'.$this->wishlist->category.'/'.$this->wishlist->referenceid,'?',$pagenavhtml);
				$pagenavhtml = str_replace('?','?filterby='.$this->filters['filterby'].a.'sortby='.$this->filters['sortby'].a.'tags='.$this->filters['tag'].'&amp;',$pagenavhtml);
				echo $pagenavhtml;
?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
	</form>
</div><!-- / .main section -->
<?php 	} // end if public ?>
<?php } else { ?>
	<p class="error"><?php echo JText::_('ERROR_LIST_NOT_FOUND'); ?></p>
<?php } // end if wish list ?>
