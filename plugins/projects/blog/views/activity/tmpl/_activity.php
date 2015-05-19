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

$activity 	 = $this->activity;
$a 			 = $activity['activity'];
$class 		 = $activity['class'];
$deletable 	 = empty($this->edit) ? $activity['deletable'] : false;
$etbl 		 = $activity['etbl'];
$eid 		 = $activity['eid'];
$ebody 		 = $activity['body'];
$comments 	 = $activity['comments'];
$preview     = $activity['preview'];
$showProject = isset($this->showProject) ? $this->showProject : false;
$edit 	     = isset($this->edit) ? $this->edit : true;

$creator = \Hubzero\User\Profile::getInstance($a->userid);

$new = $this->model->member()->lastvisit
	&& $this->model->member()->lastvisit <= $a->recorded
	? true : false;

?>
		<div id="li_<?php echo $a->id; ?>" class="activity-item <?php echo $new ? ' newitem' : ''; ?>">
			<div id="tr_<?php echo $a->id; ?>" class="item-control">
				<?php if ($deletable) { ?>
				<span class="m_options">
					<span class="delit" id="mo_<?php echo $a->id; ?>"><a href="<?php echo Route::url($this->model->link('feed') .  '&action=delete&tbl=' . $etbl . '&eid=' . $eid);  ?>">x</a>
					</span>
				</span>
				<?php } ?>
				<div class="blog-item">
					<?php if ($showProject) { ?>
						<img class="project-image" src="<?php echo Route::url($this->model->link('thumb')); ?>" alt="" />
					<?php } else { ?>
						<img class="blog-author" src="<?php echo $creator->getPicture($a->admin); ?>" alt="" />
					<?php } ?>
					<div class="blog-content">
						<?php if ($showProject) { ?>
						<span class="project-name"><a href="<?php echo Route::url($this->model->link()); ?>"><?php echo \Hubzero\Utility\String::truncate($this->model->get('title'), 65); ?></a>
						</span>
						<?php } ?>
						<span class="actor"><?php echo $a->admin == 1 ? Lang::txt('COM_PROJECTS_ADMIN') : $a->name; ?></span>
						<span class="item-time">&middot; <?php echo \Components\Projects\Helpers\Html::showTime($a->recorded, true); ?></span>
						<?php  if ($edit && $a->commentable && count($comments) == 0) { ?>
						<span class="item-time">
						<?php if ($this->model->access('content')) { ?>
							&middot; <a href="#commentform_<?php echo $a->id; ?>" id="addc_<?php echo $a->id; ?>" class="showc"><?php echo Lang::txt('COM_PROJECTS_COMMENT'); ?></a>
						</span><?php } ?>
						<?php } ?>
						<div class="<?php echo $class; ?> activity <?php if ($a->admin) { echo ' admin-action'; } ?>">
							 <?php echo $a->activity; ?><?php echo stripslashes($ebody); ?>
						</div>
						<?php echo stripslashes($preview); ?>
					</div>
				</div>
			</div>

			<?php
				// Show comments
				$this->view('_comments')
			     ->set('comments', $comments)
			     ->set('model', $this->model)
				 ->set('activity', $a)
				 ->set('uid', $this->uid)
				 ->set('edit', $edit)
			     ->display();

				// Add comment
				if ($edit && $this->model->access('content'))
				{
					$this->view('_addcomment')
				     ->set('comments', $comments)
				     ->set('model', $this->model)
					 ->set('activity', $a)
					 ->set('uid', $this->uid)
					 ->set('etbl', $etbl)
					 ->set('eid', $eid)
				     ->display();
				}
			?>
		</div>