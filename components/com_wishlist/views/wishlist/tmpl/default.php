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

$dateformat = '%d %b %Y';
$dateformat2 = '%d %b %y';
$timeformat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateformat = 'd M Y';
	$dateformat2 = 'd M y';
	$timeformat = 'h:i A';
	$tz = false;
}

/* Wish List */
if ($this->wishlist) {	
	if (!$this->wishlist->public && !$this->admin) {
?>
		<div class="main section">
			<p class="waring"><?php echo JText::_('WARNING_NOT_AUTHORIZED_PRIVATE_LIST'); ?></p>
		</div><!-- / .main section -->
<?php
	} else {
		$base = 'index.php?option=' . $this->option . '&task=wishlist&category=' . $this->wishlist->category . '&rid=' . $this->wishlist->referenceid;
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a class="icon-add add btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=add&category='. $this->wishlist->category.'&rid='.$this->wishlist->referenceid); ?>">
				<?php echo JText::_('COM_WISHLIST_TASK_ADD'); ?>
			</a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
<?php 
// Admin messages
if ($this->admin && !$this->getError()) {
	// Wish was deleted from the list
	if ($this->task == 'deletewish') {
		echo '<p class="passed">'.JText::_('COM_WISHLIST_NOTICE_WISH_DELETED').'</p>'."\n";
	}
	
	// Wish was moved to a new list
	if ($this->task == 'movewish') {
		echo '<p class="passed">'.JText::_('COM_WISHLIST_NOTICE_WISH_MOVED').'</p>'."\n";
	}
	
	switch ($this->wishlist->saved) 
	{
		case '1':
			// List settings saved    
			echo '<p class="passed">'.JText::_('COM_WISHLIST_NOTICE_LIST_SETTINGS_SAVED').'</p>'."\n";
		break;
		case '2':
			// Changes to wish saved  
			echo '<p class="passed">'.JText::_('COM_WISHLIST_NOTICE_WISH_CHANGES_SAVED').'</p>'."\n";
		break;
		case '3': 
			// New wish posted     
			echo '<p class="passed">'.JText::_('COM_WISHLIST_NOTICE_WISH_POSTED').'</p>'."\n";
		break;
	}
}
?>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

	<form method="get" action="<?php echo JRoute::_($base); ?>">
		<div class="aside">
<?php 
	// Popular tags
	if ($this->wishlist->category == 'general') {
		$obj = new TagsTag($this->database);
		$tags = $obj->getTopTags(5, 'wishlist', 'tcount DESC', 0);

		if ($tags) { ?>
			<div class="container">
				<h3><?php echo JText::_('COM_WISHLIST_POPULAR_TAGS'); ?></h3>
				<ol class="tags">
				<?php
					$lst = array();
					$tll = array();
					foreach ($tags as $tag)
					{
						if ($this->filters['tag'])
						{
							if (!in_array($tag->tag, $lst))
							{
								$lst[] = $tag->tag;
							}
						}
						else
						{
							$lst = array($tag->tag);
						}

						$class = ($tag->admin == 1) ? ' class="admin"' : '';

						$tll[$tag->tag] = '<li'.$class.'><a href="'.JRoute::_($base . '&filterby='.$this->filters['filterby'].'&sortby='.$this->filters['sortby'].'&tags='. implode(',', $lst)).'">'.$this->escape(stripslashes($tag->raw_tag)).'</a></li>';
					}
					ksort($tll);
					echo implode('',$tll);
				?>
				</ol>
				<p>Click a tag to filter results.</p>
			</div><!-- / .container -->
<?php 
		} // end if ($tags)
	} // end if ($this->wishlist->category == 'general')
	
	if (isset($this->wishlist->resource) && $this->wishlist->category == 'resource') {
		$html  = '<p>'.JText::sprintf('COM_WISHLIST_THIS_LIST_IS_FOR_RES', strtolower(substr($this->wishlist->resource->typetitle,0,strlen($this->wishlist->resource->typetitle) - 1)).' '.JText::_('COM_WISHLIST_RESOURCE_ENTITLED').' <a href="'.JRoute::_('index.php?option=com_resources&id='.$this->wishlist->referenceid).'">'.$this->escape($this->wishlist->resource->title).'</a>').'.</p>';
	} else if ($this->wishlist->description) {
		$html  = '<p>'.$this->escape($this->wishlist->description).'<p>';
	} else {
		$html  = '<p>'.JText::sprintf('COM_WISHLIST_HELP_US_IMPROVE', $sitename).'</p>';
	}				
			
	switch ($this->admin) 
	{
		case '1':
			$html .= '<p class="info">'.JText::_('COM_WISHLIST_NOTICE_SITE_ADMIN').'</p>'."\n";
		break;
		case '2':
			$html .= '<p class="info">'.JText::_('COM_WISHLIST_NOTICE_LIST_ADMIN').' Edit <a href="'.JRoute::_('index.php?option='.$this->option.'&task=settings&id='. $this->wishlist->id) .'">'.JText::_('COM_WISHLIST_LIST_SETTINGS').'</a>.</p>'."\n";
		break;
		case '3':
			$html .= '<p class="info">'.JText::_('COM_WISHLIST_NOTICE_ADVISORY_ADMIN').'</p>'."\n";
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
				<input class="entry-search-submit" type="submit" value="<?php echo JText::_('COM_WISHLIST_SEARCH'); ?>" />
				<fieldset class="entry-search">
					<legend><?php echo JText::_('COM_WISHLIST_SEARCH_LEGEND'); ?></legend>

					<label for="entry-search-field"><?php echo JText::_('COM_WISHLIST_SEARCH_LABEL'); ?></label>
					<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_WISHLIST_SEARCH_PLACEHOLDER'); ?>" />

					<input type="hidden" name="tags" value="<?php echo $this->escape($this->filters['tag']); /* xss fix for ticket 1413/1417 */ ?>" />
					<input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); /* xss fix for ticket 1413/1417 */ ?>" />
					<input type="hidden" name="filterby" value="<?php echo $this->escape($this->filters['filterby']); /* xss fix for ticket 1412/1419 */?>" />

					<input type="hidden" name="task" value="<?php echo $this->escape($this->task); /* XSS fix, see ticket 1420*/ ?>" />
					<input type="hidden" name="newsearch" value="1" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				</fieldset>
				<?php if ($this->filters['tag']) { ?>
				<fieldset class="applied-tags">
					<ol class="tags">
					<?php
					$url  = $base;
					$url .= ($this->filters['search']   ? '&search=' . $this->escape($this->filters['search'])     : '');
					$url .= ($this->filters['sortby']   ? '&sortby=' . $this->escape($this->filters['sortby'])     : '');
					$url .= ($this->filters['filterby'] ? '&filterby=' . $this->escape($this->filters['filterby']) : '');

					$tagmodel = new WishTags($this->database);
					$tags = $tagmodel->parseTags($this->filters['tag']);
					foreach ($tags as $tag)
					{
						?>
						<li>
							<a href="<?php echo JRoute::_($url . '&tag=' . implode(',', $tagmodel->parseTags($this->filters['tag'], $tag))); ?>">
								<?php echo $this->escape(stripslashes($tag)); ?>
								<span class="remove">x</a>
							</a>
						</li>
						<?php
					}
					?>
					</ol>
				</fieldset>
				<?php } ?>
			</div><!-- / .container data-entry -->
	
			<div class="container">
				<ul class="entries-menu order-options">
<?php 			if ($this->admin) { ?>
					<li><a class="sort-ranking<?php if ($this->filters['sortby'] == 'ranking') { echo ' active'; } ?>" href="<?php echo JRoute::_($base .'&filterby='.$this->filters['filterby'].'&sortby=ranking&tags='.$this->filters['tag']); ?>" title="<?php echo JText::_('COM_WISHLIST_SORT_RANKING_TITLE'); ?>">&darr; <?php echo JText::_('COM_WISHLIST_SORT_RANKING'); ?></a></li>
<?php 			} ?>
<?php 			if ($this->wishlist->banking) { ?>
					<li><a class="sort-bonus<?php if ($this->filters['sortby'] == 'bonus') { echo ' active'; } ?>" href="<?php echo JRoute::_($base .'&filterby='.$this->filters['filterby'].'&sortby=bonus&tags='.$this->filters['tag']); ?>" title="<?php echo JText::_('COM_WISHLIST_SORT_BONUS_TITLE'); ?>">&darr; <?php echo JText::_('COM_WISHLIST_SORT_BONUS'); ?></a></li>
<?php 			} ?>
					<li><a class="sort-feedback<?php if ($this->filters['sortby'] == 'feedback') { echo ' active'; } ?>" href="<?php echo JRoute::_($base .'&filterby='.$this->filters['filterby'].'&sortby=feedback&tags='.$this->filters['tag']); ?>" title="<?php echo JText::_('COM_WISHLIST_SORT_FEEDBACK_TITLE'); ?>">&darr; <?php echo JText::_('COM_WISHLIST_SORT_FEEDBACK'); ?></a></li>
					<li><a class="sort-submitter<?php if ($this->filters['sortby'] == 'submitter') { echo ' active'; } ?>" href="<?php echo JRoute::_($base .'&filterby='.$this->filters['filterby'].'&sortby=submitter&tags='.$this->filters['tag']); ?>" title="<?php echo JText::_('COM_WISHLIST_SORT_SUBMITTER_TITLE'); ?>">&darr; <?php echo JText::_('COM_WISHLIST_SORT_SUBMITTER'); ?></a></li>
					<li><a class="sort-date<?php if ($this->filters['sortby'] == 'date') { echo ' active'; } ?>" href="<?php echo JRoute::_($base .'&filterby='.$this->filters['filterby'].'&sortby=date&tags='.$this->filters['tag']); ?>" title="<?php echo JText::_('COM_WISHLIST_SORT_DATE'); ?>">&darr; <?php echo JText::_('COM_WISHLIST_SORT_DATE'); ?></a></li>
				</ul>

				<ul class="entries-menu filter-options">
					<li><a class="filter-all<?php if ($this->filters['filterby'] == 'all') { echo ' active'; } ?>" href="<?php echo JRoute::_($base .'&filterby=all&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo JText::_('COM_WISHLIST_FILTER_ALL'); ?></a></li>
					<li><a class="filter-open<?php if ($this->filters['filterby'] == 'open') { echo ' active'; } ?>" href="<?php echo JRoute::_($base .'&filterby=open&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo JText::_('COM_WISHLIST_FILTER_OPEN'); ?></a></li>
					<li><a class="filter-accepted<?php if ($this->filters['filterby'] == 'accepted') { echo ' active'; } ?>" href="<?php echo JRoute::_($base .'&filterby=accepted&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo JText::_('COM_WISHLIST_FILTER_ACCEPTED'); ?></a></li>
					<li><a class="filter-rejected<?php if ($this->filters['filterby'] == 'rejected') { echo ' active'; } ?>" href="<?php echo JRoute::_($base .'&filterby=rejected&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo JText::_('COM_WISHLIST_FILTER_REJECTED'); ?></a></li>
					<li><a class="filter-granted<?php if ($this->filters['filterby'] == 'granted') { echo ' active'; } ?>" href="<?php echo JRoute::_($base .'&filterby=granted&sortby='.$this->filters['sortby'].'&tags='.$this->filters['tag']); ?>"><?php echo JText::_('COM_WISHLIST_FILTER_GRANTED'); ?></a></li>
<?php 			if (!$this->juser->get('guest')) { ?>
					<li><a class="filter-submitter<?php if ($this->filters['filterby'] == 'submitter') { echo ' active'; } ?>" href="<?php echo JRoute::_($base . '&filterby=submitter&sortby=' . $this->filters['sortby'] . '&tags=' . $this->filters['tag']); ?>"><?php echo JText::_('COM_WISHLIST_FILTER_SUBMITTER'); ?></a></li>
<?php 			} ?>
<?php 			if ($this->admin == 1 || $this->admin == 2) { ?>
					<li><a class="filter-public<?php if ($this->filters['filterby'] == 'public') { echo ' active'; } ?>" href="<?php echo JRoute::_($base . '&filterby=public&sortby=' . $this->filters['sortby'] . '&tags=' . $this->filters['tag']); ?>"><?php echo JText::_('COM_WISHLIST_FILTER_PUBLIC'); ?></a></li>
					<li><a class="filter-private<?php if ($this->filters['filterby'] == 'private') { echo ' active'; } ?>" href="<?php echo JRoute::_($base . '&filterby=private&sortby=' . $this->filters['sortby'] . '&tags=' . $this->filters['tag']); ?>"><?php echo JText::_('COM_WISHLIST_FILTER_PRIVATE'); ?></a></li>
<?php 				if ($this->admin == 2) { ?>
					<li><a class="filter-mine<?php if ($this->filters['filterby'] == 'mine') { echo ' active'; } ?>" href="<?php echo JRoute::_($base . '&filterby=mine&sortby=' . $this->filters['sortby'] . '&tags=' . $this->filters['tag']); ?>"><?php echo JText::_('COM_WISHLIST_FILTER_MINE'); ?></a></li>
<?php 				} ?>
<?php 			} ?>
				</ul>

				<table class="ideas entries" summary="<?php echo JText::_('Ideas submitted by the community'); ?>">
					<caption>
						<?php echo JText::_('COM_WISHLIST_FILTER_'.strtoupper($this->filters['filterby'])); ?> 
						<?php echo ($this->filters['tag'] != '') ? JText::sprintf('COM_WISHLIST_WISHES_TAGGED_WITH', $this->filters['tag']) : ''; ?>
						<span>
							(<?php echo ($this->pageNav->total > 0) ? ($this->filters['start'] + 1) : $this->filters['start']; ?> - <?php echo $this->filters['start'] + count($this->wishlist->items); ?> of <?php echo $this->pageNav->total; ?>)
						</span>
					</caption>
					<tbody>
<?php
				if ($this->wishlist->items) {
					$y = 1;
					$filters  = '';
					$filters .= ($this->filters['filterby']) ? '&filterby='.$this->filters['filterby'] : '';
					$filters .= ($this->filters['sortby'])   ? '&sortby='.$this->filters['sortby']     : '';
					$filters .= ($this->filters['tag'])      ? '&tags='.$this->filters['tag']          : '';
					$filters .= ($this->filters['limit'])    ? '&limit='.$this->filters['limit']       : '';
					$filters .= ($this->filters['start'])    ? '&start='.$this->filters['start']       : '';
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
							$item->authorname = '<a href="'.JRoute::_('index.php?option=com_members&id='.$item->proposed_by).'">'.$this->escape($item->authorname).'</a>';
						}
?>
						<tr class="<?php echo $state; ?>">
							<th class="<?php echo $status; ?>">
								<span class="entry-id"><?php echo $item->id; ?></span>
							</th>
							<td<?php if (!$item->reports) { echo ' colspan="' . ($this->wishlist->banking ? '4' : '3') . '"'; } ?>>
<?php 					if (!$item->reports) { ?>
								<a class="entry-title" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid .'&wishid='.$item->id . $filters); ?>"><?php echo $item->subject; ?></a><br />
								<span class="entry-details">
									<?php echo JText::_('COM_WISHLIST_WISH_PROPOSED_BY'); ?> <?php echo ($item->anonymous == 1) ? JText::_('COM_WISHLIST_ANONYMOUS') : $item->authorname; ?> @
									<span class="entry-time"><time datetime="<?php echo $item->proposed; ?>"><?php echo JHTML::_('date', $item->proposed, $timeformat, $tz); ?></time></span> <?php echo JText::_('COM_WISHLIST_ON'); ?> 
									<span class="entry-date"><time datetime="<?php echo $item->proposed; ?>"><?php echo JHTML::_('date', $item->proposed, $dateformat, $tz); ?></time></span>
									<span class="entry-details-divider">&bull;</span>
									<span class="entry-comments"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid .'&wishid='.$item->id.'&com=1' . $filters . '#comments'); ?>" title="<?php echo $item->numreplies; ?> <?php echo JText::_('COM_WISHLIST_COMMENTS'); ?>"><?php echo $item->numreplies; ?></a></span>
								</span>
<?php 					} else { ?>
								<span class="warning adjust"><?php echo JText::_('COM_WISHLIST_NOTICE_POSTING_REPORTED'); ?></span>
<?php 					} ?>
							</td>
<?php 					if (!$item->reports && $this->wishlist->banking) { ?>
							<td class="reward">
								<span class="entry-reward">
<?php 							if (isset($item->bonus) && $item->bonus > 0 && ($item->status==0 or $item->status==6)) { ?>
									<a class="bonus tooltips" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid .'&wishid='.$item->id.'&action=addbonus' . $filters . '#action'); ?>" title="<?php echo JText::_('COM_WISHLIST_WISH_ADD_BONUS').' ::'.$item->bonusgivenby.' '.JText::_('COM_WISHLIST_MULTIPLE_USERS').' '.JText::_('COM_WISHLIST_WISH_BONUS_CONTRIBUTED_TOTAL').' '.$item->bonus.' '.JText::_('COM_WISHLIST_POINTS').' '.JText::_('COM_WISHLIST_WISH_BONUS_AS_BONUS'); ?>"><?php echo $item->bonus; ?> <span><?php echo JText::_('COM_WISHLIST_POINTS'); ?></span></a>
<?php 							} else if ($item->status == 0 || $item->status == 6) { ?>
									<a class="nobonus tooltips" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid .'&wishid='.$item->id.'&action=addbonus' . $filters . '#action'); ?>" title="<?php echo JText::_('COM_WISHLIST_WISH_ADD_BONUS').' :: '.JText::_('COM_WISHLIST_WISH_BONUS_NO_USERS_CONTRIBUTED'); ?>"><?php echo $item->bonus; ?> <span><?php echo JText::_('COM_WISHLIST_POINTS'); ?></span></a>
<?php 							} else { ?>
									<span class="inactive" title="<?php echo JText::_('COM_WISHLIST_WISH_BONUS_NOT_ACCEPTED'); ?>">&nbsp;</span>
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
											$html .= '<a class="rankit" href="index.php?option='.$this->option.'&task=wish&category='.$this->wishlist->category.'&rid='.$this->wishlist->referenceid .'&wishid='.$item->id . $filters . '">'.JText::_('COM_WISHLIST_WISH_RANK_THIS').'</a>'."\n";
										} else if (isset($item->ranked) && $item->ranked) {
											//$html .= JText::_('WISH_PRIORITY').': <span class="priority">'.$item->ranking.'</span>'."\n";
											$html .= '<span class="priority-level-base">
												<span class="priority-level" style="width: '.(($item->ranking/50)*100).'%">
													<span>'.JText::_('COM_WISHLIST_WISH_PRIORITY').': '.$item->ranking.'</span>
												</span>
											</span>';
										}
										if ($item->accepted == 1) {
											$html .= '<span class="accepted">'.JText::_('COM_WISHLIST_WISH_STATUS_ACCEPTED').'</span>';
										}
									break;
									case 1:
										$html .= '<span class="granted">'.JText::_('COM_WISHLIST_WISH_STATUS_GRANTED').'</span>';
										/*if ($item->granted != '0000-00-00 00:00:00') {
											$html .= ' <span class="mini">'.strtolower(JText::_('ON')).' '.JHTML::_('date',$item->granted, $dateFormat, $tz).'</span>';
										}*/
									break;
									case 3:
										$html .= '<span class="rejected">'.JText::_('COM_WISHLIST_WISH_STATUS_REJECTED').'</span>';
									break;
									case 4:
										$html .= '<span class="withdrawn">'.JText::_('COM_WISHLIST_WISH_STATUS_WITHDRAWN').'</span>';
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
								<p><?php echo JText::_('COM_WISHLIST_NO_WISHES_BE_FIRST'); ?></p>
<?php 				} else { ?>
								<p class="noresults"><?php echo JText::_('COM_WISHLIST_NO_WISHES_SELECTION'); ?></p>
								<p class="nav_wishlist"><a href="<?php echo JRoute::_($base); ?>"><?php echo JText::_('COM_WISHLIST_VIEW_ALL_WISHES'); ?></a></p>
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
				$this->pageNav->setAdditionalUrlParam('filterby', $this->filters['filterby']);
				$this->pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
				$this->pageNav->setAdditionalUrlParam('tag', $this->filters['tag']);
				$this->pageNav->setAdditionalUrlParam('newsearch', 0);
				echo $this->pageNav->getListFooter();
?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</div><!-- / .subject -->
	</form>
</div><!-- / .main section -->
<?php 	} // end if public ?>
<?php } else { ?>
	<p class="error"><?php echo JText::_('COM_WISHLIST_ERROR_LIST_NOT_FOUND'); ?></p>
<?php } // end if wish list ?>
