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

$juser =& JFactory::getUser();
$jconfig =& JFactory::getConfig();

if (!$this->filters['filterby']) {
	$this->filters['filterby'] = 'all';
}
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<div id="content-header-extra">
	<ul id="useroptions">
<?php if ($this->task != 'myquestions') { ?>
		<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=myquestions'); ?>" class="myquestions"><span><?php echo JText::_('COM_ANSWERS_MY_QUESTIONS'); ?></span></a></li>
<?php } ?>
		<li class="last"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=new'); ?>" class="add"><span><?php echo JText::_('COM_ANSWERS_NEW_QUESTION'); ?></span></a></li>
	</ul>
</div><!-- / #content-header-extra -->
<div class="clear"></div>

<?php if (!$juser->get('guest') && $this->task == 'myquestions') { ?>
<ul class="breadcrumbtrail">
	<li class="first"><span class="myquestions"></span></li>
	<li class="first">
		<?php 
		echo ($this->filters['interest'] == 0 && $this->filters['assigned'] == 0) ? '<strong>' : '<a href="'.JRoute::_('index.php?option='.$this->option.'&task=myquestions').'">';
		echo JText::_('COM_ANSWERS_QUESTIONS_I_ASKED');
		echo ($this->filters['interest'] == 0 && $this->filters['assigned'] == 0) ? '</strong>' : '</a>';
		?>
	</li>
	<li>
		<?php
		echo ($this->filters['assigned'] == 1) ? '<strong>' : '<a href="'.JRoute::_('index.php?option='.$this->option.'&task=myquestions').'?assigned=1">';
		echo JText::_('COM_ANSWERS_QUESTIONS_RELATED_TO_CONTRIBUTIONS');
		echo ($this->filters['assigned'] == 1) ? '</strong>' : '</a>';
		?>
	</li>
	<li>
		<?php
	 	echo ($this->filters['interest'] == 1) ? '<strong>' : '<a href="'.JRoute::_('index.php?option='.$this->option.'&task=myquestions').'?interest=1">';
		echo JText::_('COM_ANSWERS_QUESTIONS_TAGGED_WITH_MY_INTERESTS');
		echo ($this->filters['interest'] == 1) ? '</strong>' : '</a>';
		?>
	</li>
</ul>
<?php } ?>

<div class="main section">
	<div class="aside">
		<div class="container">
			<h3>Need an Answer?</h3>
			<p class="starter"><span class="starter-point"></span>
				<?php echo JText::_('COM_ANSWERS_CANT_FIND_ANSWER'); ?> <a href="<?php echo JRoute::_('index.php?option=com_kb'); ?>"><?php echo JText::_('COM_ANSWERS_KNOWLEDGE_BASE'); ?></a> <?php echo JText::_('COM_ANSWERS_OR_BY').' '.JText::_('COM_ANSWERS_SEARCH').'? '.JText::_('COM_ANSWERS_ASK_YOUR_FELLOW').' '.$jconfig->getValue('config.sitename').' '.JText::_('COM_ANSWERS_MEMBERS'); ?>!
			</p>
		</div><!-- / .container -->
<?php if ($this->banking) { ?>
		<div class="container">
			<h3>Earn Points!</h3>
			<p class="starter"><span class="starter-point"></span>
				<?php echo JText::_('Start earning points by posting questions and answers valuable to the community.'); ?> <a href="<?php echo $this->infolink; ?>"><?php echo JText::_('Learn more'); ?></a>.
			</p>
		</div><!-- / .container -->
<?php } ?>		
	</div><!-- / .aside -->
	<div class="subject">
		<form method="get" action="<?php echo JRoute::_('index.php?option='.$this->option); ?>">
			
			<div class="container data-entry">
				<input class="entry-search-submit" type="submit" value="Search" />
				<fieldset class="entry-search">
					<input type="text" name="q" value="<?php echo htmlentities($this->filters['q'], ENT_COMPAT, 'UTF-8'); ?>" />
					<input type="hidden" name="sortby" value="<?php echo $this->filters['sortby']; ?>" />
					<input type="hidden" name="filterby" value="<?php echo $this->filters['filterby']; ?>" />
					<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<?php if (isset($this->filters['interest'])) { ?>
					<input type="hidden" name="interest" value="<?php echo $this->filters['interest']; ?>" />
					<input type="hidden" name="assigned" value="<?php echo $this->filters['assigned']; ?>" />
<?php } ?>
				</fieldset>
			</div><!-- / .container -->
		
			<div class="container">
				<ul class="entries-menu order-options">
<?php if ($this->banking) { ?>
					<li><a<?php echo ($this->filters['sortby'] == 'rewards') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=search&filterby='.$this->filters['filterby'].'&sortby=rewards'); ?>" title="Sort by most reward points to least">&darr; <?php echo JText::_('COM_ANSWERS_REWARDS'); ?></a></li>
<?php } ?>
					<li><a<?php echo ($this->filters['sortby'] == 'votes') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=search&filterby='.$this->filters['filterby'].'&sortby=votes'); ?>" title="Sort by most liked to least liked">&darr; Popular</a></li>
					<li><a<?php echo ($this->filters['sortby'] == 'date') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=search&filterby='.$this->filters['filterby'].'&sortby=date'); ?>" title="Sort by newest to oldest">&darr; Recent</a></li>
				</ul>
				
				<ul class="entries-menu filter-options">
					<li><a<?php echo ($this->filters['filterby'] == 'all' || $this->filters['filterby'] == '') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=search&filterby=all'); ?>">All</a></li>
					<li><a<?php echo ($this->filters['filterby'] == 'open') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=search&filterby=open'); ?>">Open</a></li>
					<li><a<?php echo ($this->filters['filterby'] == 'closed') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=search&filterby=closed'); ?>">Closed</a></li>
<?php if (!$juser->get('guest')) { ?>
					<li><a<?php echo ($this->filters['filterby'] == 'mine') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=search&filterby=mine'); ?>">Mine</a></li>
<?php } ?>
				</ul>
			
				<table class="questions entries" summary="Questions submitted by the community">
					<caption>
<?php
	$s = $this->filters['start']+1;
	$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;
	
	if ($this->filters['q'] != '') {
		echo 'Search for "'.$this->filters['q'].'" in ';
	}
?>
						<?php echo ucfirst($this->filters['filterby']); ?> 
						<span>(<?php echo $s.'-'.$e; ?> of <?php echo $this->total; ?>)</span>
					</caption>
					<tbody>
<?php 
	if (count($this->results) > 0) {
		foreach ($this->results as $row) 
		{
			$row->reports = (isset($row->reports)) ? $row->reports : 0;	
			$row->points = $row->points ? $row->points : 0;
			
			//if (!$row->reports) {
				// author name
				$name = JText::_('COM_ANSWERS_ANONYMOUS');
				if ($row->anonymous == 0) {
					//$user =& JUser::getInstance( $row->created_by );
					//if (is_object($user)) {
						//$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$user->get('id')).'">'.stripslashes($user->get('name')).'</a>';
					//} else {
					//	$name = JText::_('COM_ANSWERS_UNKNOWN');
					//}
					$name = '<a href="'.JRoute::_('index.php?option=com_members&id='.$row->userid).'">'.stripslashes($row->name).'</a>';
				}
				
				/*$cls  = (isset($row->reward) && $row->reward == 1 && $this->banking) ? ' hasreward' : '';
				$cls .= ($row->state == 1) ? ' answered' : '';
				
				if ($row->question != '') {
					$row->question = stripslashes($row->question);
					$fulltext = htmlspecialchars(Hubzero_View_Helper_Html::purifyText($row->question));
				} else {
				 	$fulltext = stripslashes($row->subject);
				}*/
				$cls = ($row->state == 1) ? 'answered' : '';
				$cls = ($row->reports) ? 'flagged' : $cls;
				$cls .= ($row->created_by == $juser->get('username')) ? ' mine' : '';
?>
						<tr<?php echo ($cls) ? ' class="'.$cls.'"' : ''; ?>>
							<th>
								<span class="entry-id"><?php echo $row->id; ?></span>
							</th>
							<td>
<?php
							if (!$row->reports) {
?>
								<a class="entry-title" href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id='.$row->id); ?>"><?php echo stripslashes($row->subject); ?></a><br />
<?php
							} else {
?>
								<span class="entry-title"><?php echo JText::_('COM_ANSWERS_QUESTION_UNDER_REVIEW'); ?></span><br />
<?php
							}
?>
								<span class="entry-details">
									<?php echo JText::sprintf('COM_ANSWERS_ASKED_BY', $name); ?> @ 
									<span class="entry-time"><?php echo JHTML::_('date',$row->created, '%I:%M %p', 0); ?></span> on 
									<span class="entry-date"><?php echo JHTML::_('date',$row->created, '%d %b %Y', 0); ?></span>
									<span class="entry-details-divider">&bull;</span>
									<span class="entry-state">
										<?php echo ($row->state==1) ? JText::_('Closed') : JText::_('Open'); ?>
									</span>
									<span class="entry-details-divider">&bull;</span>
									<span class="entry-comments">
										<a href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id='.$row->id.'#answers'); ?>" title="<?php echo JText::sprintf('COM_ANSWERS_RESPONSES_TO_THIS_QUESTION', $row->rcount); ?>">
											<?php echo $row->rcount; ?>
										</a>
									</span>
								</span>
							</td>
<?php if ($this->banking) { ?>
							<td class="reward">
<?php 		if (isset($row->reward) && $row->reward == 1 && $this->banking) { ?>
								<span class="entry-reward"><?php echo $row->points; ?> <a href="<?php echo $this->infolink; ?>" title="<?php echo JText::sprintf('COM_ANSWERS_THERE_IS_A_REWARD_FOR_ANSWERING', $row->points); ?>">Points</a></span>
<?php 		} ?>
							</td>
<?php } ?>
							<td class="voting">
								<span class="vote-like">
<?php if ($juser->get('guest')) { ?>
									<span class="vote-button <?php echo ($row->helpful > 0) ? 'like' : 'neutral'; ?> tooltips" title="Vote this up :: Please login to vote."><?php echo $row->helpful; ?><span> Like</span></span>
<?php } else { ?>
									<a class="vote-button <?php echo ($row->helpful > 0) ? 'like' : 'neutral'; ?> tooltips" href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id='.$row->id.'&vote=1'); ?>" title="Vote this up :: <?php echo $row->helpful; ?> people liked this"><?php echo $row->helpful; ?><span> Like</span></a>
<?php } ?>
								</span>
							</td>
						</tr>
<?php 
	} // end foreach
?>
<?php } else { ?>
						<tr class="noresults">
							<td>
								<?php echo JText::_('COM_ANSWERS_NO_RESULTS'); ?>
							</td>
						</tr>
<?php } // end if (count($this->results) > 0) { ?>
					</tbody>
				</table>
				<?php 
				$pagenavhtml = $this->pageNav->getListFooter();
				$pagenavhtml = str_replace('&amp;&amp;','&amp;',$pagenavhtml);
				$pagenavhtml = str_replace('?&amp;','?',$pagenavhtml);
				echo $pagenavhtml;
				?>
				<div class="clearfix"></div>
			</div><!-- / .container -->
		</form>
	</div><!-- / .subject -->
</div><!-- / .main section -->
