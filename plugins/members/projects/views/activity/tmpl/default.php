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
	$projects = array();
	$obj = new Project( $this->database );
	$i = 1;
?>
<table>
	<tbody>
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
		
		$pid = $activity['projectid'];
		if(!isset($projects[$pid])) {
			$projects[$pid] = $obj->getProject($pid, $this->uid);
		}
		$goto = 'alias='.$projects[$pid]->alias;
		$thumb = ProjectsHTML::getThumbSrc($pid, $projects[$pid]->alias, $projects[$pid]->picture, $this->config);
		$title = htmlentities(ProjectsHtml::cleanText($projects[$pid]->title));
		$timeclass = $projects[$pid]->lastvisit && $projects[$pid]->lastvisit <= $activity['recorded'] ? ' urgency' : '';
		$more = count($this->activities) - $this->limit;
		?>
		<tr>
			<td class="p-ima">
				<?php if($i == $more) { echo '<a name="more"></a>'; } ?>
				<?php echo '<a href="'.JRoute::_('index.php?option='.$this->option.'&task=view&'.$goto).'"><img src="'.$thumb.'" alt="'.$title.'" /></a>'; ?></td>
			<td>
				<span class="rightfloat mini faded<?php echo $timeclass; ?>"><?php echo ProjectsHTML::timeAgo($a->recorded).' '.JText::_('PLG_MEMBERS_PROJECTS_AGO'); ?> </span>
				<span class="project-name"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=view&'.$goto); ?>"><?php echo Hubzero_View_Helper_Html::shortenText($title, 65, 0); ?></a></span>
				<div class="mline <?php echo $class; ?><?php if($a->admin) { echo ' admin-action'; } ?>" id="tr_<?php echo $a->id; ?>">
				<span>
				<span class="actor"><?php echo $a->admin == 1 ? JText::_('PLG_MEMBERS_PROJECTS_ADMIN') : $a->name; ?></span> <?php echo $a->activity; ?><?php echo stripslashes($ebody); ?>	
				</span>
				</div>
				<?php if($a->commentable) { ?>	
				<span class="comment">
				<?php if(count($comments) > 0) { echo count($comments) == 1 ? count($comments).' '.JText::_('PLG_MEMBERS_PROJECTS_COMMENT') : ' '.count($comments).' '.JText::_('PLG_MEMBERS_PROJECTS_COMMENTS'); } ?>  <?php if(isset($a->new) && $a->new > 0) { echo ' &middot; <span class="prominent urgency">'.$a->new.' '.JText::_('PLG_MEMBERS_PROJECTS_NEW').'</span>'; } ?> 
				</span>
			<?php 
				if(count($comments) > 0) { 
					// Show Comments
				?>
				<ol class="comments" id="comments_<?php echo $a->id; ?>">
					<?php foreach ($comments as $comment) { 
						$ctimeclass = $projects[$pid]->lastvisit && $projects[$pid]->lastvisit <= $comment->created ? ' class="urgency"' : '';
					?>
					<li class="quote" id="c_<?php echo $comment->id; ?>">
						<?php echo stripslashes(ProjectsHTML::replaceUrls($comment->comment, 'external')); ?>
						<span class="block mini faded"><?php echo $comment->author; ?> &middot; <span <?php echo $ctimeclass; ?>><?php echo ProjectsHTML::timeAgo($comment->created).' '.JText::_('PLG_MEMBERS_PROJECTS_AGO'); ?></span></span>
					</li>	
					<?php } ?>
				</ol>
				<?php } 								
			} // end if commentable ?>
		</td>
	</tr>
<?php 	$i++; } // end foreach ?>
</tbody>
</table>
<?php } else { ?>
<p class="noresults"><?php echo JText::_('PLG_MEMBERS_PROJECTS_NO_ACTIVITIES'); ?></p>
<?php } ?>
<div id="more-updates" class="nav_pager">
<?php 
if($this->total > $this->filters['limit']) { 
	$limit = $this->filters['limit'] + $this->limit; ?>	
	<p><a href="<?php echo JRoute::_('index.php?option=com_members&id='.$this->uid.'&active=projects').'?action=updates&limit='.$limit.'&prev='.$this->filters['limit'].'#more';  ?>"><?php echo JText::_('PLG_MEMBERS_PROJECTS_VIEW_OLDER_ENTRIES'); ?></a></p>
<?php } else if($this->filters['limit'] != $this->limit) { ?>
	<p><?php echo JText::_('PLG_MEMBERS_PROJECTS_VIEW_OLDER_ENTRIES_NO_MORE'); ?></p>
<?php } ?>
</div><!-- / #more-updates -->
