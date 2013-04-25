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

if (count($this->activities) > 0 ) {
	
?>
<ul>
<?php
// Loop through activities
	foreach($this->activities as $activity) { 	
		$a = $activity['activity'];
		$class = $activity['class'];
		$deletable = $activity['deletable'];
		$etbl = $activity['etbl'];
		$eid = $activity['eid'];
		$ebody = $activity['body'];
		$comments = $activity['comments'];
		$timeclass = $activity['timeclass'];

		?>
			<li id="li_<?php echo $a->id; ?>">
			<div class="mline" id="tr_<?php echo $a->id; ?>">
				<?php if($deletable) { ?>	
				<span class="m_options"><span class="delit" id="mo_<?php echo $a->id; ?>"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$this->goto.a.'task=view'.a.'active=feed').'/?action=delete'.a.'tbl='.$etbl.a.'eid='.$eid;  ?>">x</a></span></span>
				<?php } ?>
				<span class="blog-time<?php echo $timeclass; ?>"><?php echo ProjectsHtml::timeAgo($a->recorded).' '.JText::_('COM_PROJECTS_AGO'); ?> </span>
				<div class="<?php echo $class; ?> activity<?php if($a->admin) { echo ' admin-action'; } ?>">
					<span class="actor"><?php echo $a->admin == 1 ? JText::_('COM_PROJECTS_ADMIN') : $a->name; ?></span> <?php echo $a->activity; ?><?php echo stripslashes($ebody); ?>	
				</div>
			</div>

			<?php if($a->commentable) { ?>	
			<span class="comment">
				<a href="#commentform_<?php echo $a->id; ?>" id="addc_<?php echo $a->id; ?>" class="showc">&#43; <?php echo JText::_('COM_PROJECTS_COMMENT'); ?></a> 
			<?php if(count($comments) > 0) { echo count($comments) == 1 ? ' &middot; '.count($comments).' '.JText::_('COM_PROJECTS_COMMENT') : ' &middot; '.count($comments).' '.JText::_('COM_PROJECTS_COMMENTS'); } ?>  <?php if(isset($a->new) && $a->new > 0) { echo ' &middot; <span class="prominent urgency">'.$a->new.' '.JText::_('COM_PROJECTS_NEW').'</span>'; } ?> 
			</span>
				<?php				
			} // end if commentable ?>
			<?php 
				if(count($comments) > 0) { 
					// Show Comments
				?>
				<ol class="comments" id="comments_<?php echo $a->id; ?>">
					<?php foreach ($comments as $comment) { 
						$ctimeclass = $this->project->lastvisit && $this->project->lastvisit <= $comment->created ? ' class="urgency"' : '';
						
						// Is user allowed to delete item?
						$deletable = ($comment->created_by == $this->uid or $this->project->role == 1) ? 1 : 0;
						
						$author = $comment->admin == 1 ? JText::_('COM_PROJECTS_ADMIN') : $comment->author;
					?>
					<li class="quote" id="c_<?php echo $comment->id; ?>">
						<?php if($deletable) { ?>
							<span class="m_options">
								<span class="delit" id="pu_<?php echo $comment->id; ?>">
								 <a href="<?php echo JRoute::_('index.php?option=' . $this->option
								.a.$this->goto.a.'task=view'.a.'active=feed')
								.'/?action=deletecomment'.a.'cid='.$comment->id; ?>">x</a>
							</span></span>
						<?php } ?>
						<?php echo stripslashes(ProjectsHtml::replaceUrls($comment->comment, 'external')); ?>
						<span class="block mini faded"><?php echo $author; ?> &middot; <span <?php echo $ctimeclass; ?>><?php echo ProjectsHtml::timeAgo($comment->created).' '.JText::_('COM_PROJECTS_AGO'); ?></span></span>
					</li>	
					<?php } ?>
				</ol>
				<?php } 				
				// Add Comment ?>
				<?php if($a->commentable) { ?>
				<div class="addcomment hidden" id="commentform_<?php echo $a->id; ?>">
					<form action="<?php echo JRoute::_('index.php?option='.$this->option.a.'id='.$this->project->id).'/?active=feed'; ?>" method="post">
						<fieldset>
							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
							<input type="hidden" name="action" value="savecomment" />
							<input type="hidden" name="task" value="view" />
							<input type="hidden" name="active" value="feed" />
							<input type="hidden" name="itemid" value="<?php echo $eid; ?>" />
							<input type="hidden" name="tbl" value="<?php echo $etbl; ?>" />
							<input type="hidden" name="parent_activity" value="<?php echo $a->id; ?>" />
							<label>
								<textarea name="comment" rows="4" cols="50" class="commentarea" id="ca_<?php echo $a->id; ?>"></textarea>
							</label>
							<p class="blog-submit"><input type="submit" class="c-submit" id="cs_<?php echo $a->id; ?>" value="<?php echo JText::_('COM_PROJECTS_COMMENT'); ?>" /></p>
						</fieldset>
					</form>
				</div>
					<?php				
				} // end if commentable ?>
		</li>
<?php } // end foreach ?>
</ul>
<?php } else { ?>
<p class="noresults"><?php echo JText::_('COM_PROJECTS_NO_ACTIVITIES'); ?></p>
<?php } ?>
<div id="more-updates" class="nav_pager">
<?php 
//$total = (count($skipped) > 0)  ? $this->total - count($skipped) : $this->total;	
if($this->total > $this->filters['limit']) { 
	$limit = $this->filters['limit'] + $this->limit; ?>	
	<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$this->goto.a.'active=feed').'?limit='.$limit.a.'prev='.$this->filters['limit'];  ?>"><?php echo JText::_('COM_PROJECTS_VIEW_OLDER_ENTRIES'); ?></a></p>
<?php } else if($this->filters['limit'] != $this->limit) { ?>
	<p><?php echo JText::_('COM_PROJECTS_VIEW_OLDER_ENTRIES_NO_MORE'); ?></p>
<?php } else if ($this->total > 5 && $this->total < $this->filters['limit']) { ?>
	<p><?php echo JText::_('COM_PROJECTS_VIEW_OLDER_ENTRIES_NO_MORE'); ?></p>
<?php } ?>
</div><!-- / #more-updates -->
