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

$activity 	= $this->activity;
$a 			= $activity['activity'];
$class 		= $activity['class'];
$deletable 	= $activity['deletable'];
$etbl 		= $activity['etbl'];
$eid 		= $activity['eid'];
$ebody 		= $activity['body'];
$comments 	= $activity['comments'];
$new 		= $activity['new'];
$preview    = $activity['preview'];

$creator = \Hubzero\User\Profile::getInstance($a->userid);

?>
		<div id="li_<?php echo $a->id; ?>" class="activity-item <?php echo $new ? ' newitem' : ''; ?>">
			<div id="tr_<?php echo $a->id; ?>" class="item-control">
				<?php if ($deletable) { ?>
				<span class="m_options">
					<span class="delit" id="mo_<?php echo $a->id; ?>"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&active=feed') . '/?action=delete' . a . 'tbl=' . $etbl . a . 'eid=' . $eid;  ?>">x</a>
					</span>
				</span>
				<?php } ?>
				<div class="blog-item"><img class="blog-author" src="<?php echo $creator->getPicture($a->admin); ?>" alt="" />
					<span class="actor"><?php echo $a->admin == 1 ? JText::_('COM_PROJECTS_ADMIN') : $a->name; ?></span>
					<span class="item-time">&middot; <?php echo ProjectsHtml::showTime($a->recorded, true); ?></span>
					<?php  if ($a->commentable && count($comments) == 0) { ?>
					<span class="item-time">
						&middot; <a href="#commentform_<?php echo $a->id; ?>" id="addc_<?php echo $a->id; ?>" class="showc"><?php echo JText::_('COM_PROJECTS_COMMENT'); ?></a>
					</span>
					<?php } ?>
					<div class="<?php echo $class; ?> activity <?php if ($a->admin) { echo ' admin-action'; } ?>">
						 <?php echo $a->activity; ?><?php echo stripslashes($ebody); ?>
					</div>
					<?php echo stripslashes($preview); ?>
				</div>
			</div>

			<?php
				// Show comments
				$this->view('_comments')
			     ->set('option', $this->option)
			     ->set('comments', $comments)
			     ->set('project', $this->project)
				 ->set('activity', $a)
				 ->set('uid', $this->uid)
			     ->display();

				// Add comment
				$this->view('_addcomment')
			     ->set('option', $this->option)
			     ->set('comments', $comments)
			     ->set('project', $this->project)
				 ->set('activity', $a)
				 ->set('uid', $this->uid)
				 ->set('etbl', $etbl)
				 ->set('eid', $eid)
			     ->display(); ?>
		</div>