<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css();

if (count($this->activities) > 0 ) {
	$projects = array();
	$obj = new \Components\Projects\Tables\Project( $this->database );
	$i = 1;
	?>
	<table>
		<tbody>
	<?php
	// Loop through activities
		foreach ($this->activities as $activity)
		{
			$a = $activity['activity'];
			$class = $activity['class'];
			$deletable = $activity['deletable'];
			$etbl = $activity['etbl'];
			$eid = $activity['eid'];
			$ebody = $activity['body'];
			$comments = $activity['comments'];

			$pid = $activity['projectid'];
			if (!isset($projects[$pid]))
			{
				$projects[$pid] = $obj->getProject($pid, $this->uid);
			}
			$goto = 'alias='.$projects[$pid]->alias;

			$title = $projects[$pid]->title;
			$timeclass = $projects[$pid]->lastvisit && $projects[$pid]->lastvisit <= $activity['recorded'] ? ' urgency' : '';
			$more = count($this->activities) - $this->limit;
			?>
			<tr>
				<td class="p-ima">
					<?php if ($i == $more) { echo '<a name="more"></a>'; } ?>
					<?php echo '<a href="'.Route::url('index.php?option='.$this->option.'&task=view&'.$goto).'"><img src="'. Route::url('index.php?option=' . $this->option . '&alias=' . $projects[$pid]->alias . '&task=media') .'" alt="' . $this->escape($title) . '" /></a>'; ?>
				</td>
				<td>
					<span class="rightfloat mini faded<?php echo $timeclass; ?>">
						<?php echo JHTML::_('date.relative', $a->recorded); ?>
					</span>
					<span class="project-name">
						<a href="<?php echo Route::url('index.php?option=' . $this->option.'&task=view&'.$goto); ?>">
							<?php echo $this->escape($title); ?>
						</a>
					</span>
					<div class="mline <?php echo $class; ?><?php if ($a->admin) { echo ' admin-action'; } ?>" id="tr_<?php echo $a->id; ?>">
						<span>
							<span class="actor"><?php echo $a->admin == 1 ? Lang::txt('COM_PROJECTS_ADMIN') : $a->name; ?></span>
							<?php echo $a->activity; ?><?php echo stripslashes($ebody); ?>
						</span>
					</div>

					<?php if ($a->commentable) { ?>
						<span class="comment">
							<?php if (count($comments) > 0) { echo count($comments) == 1 ? count($comments).' '.Lang::txt('COM_PROJECTS_COMMENT') : ' '.count($comments).' '.Lang::txt('COM_PROJECTS_COMMENTS'); } ?>  <?php if (isset($a->new) && $a->new > 0) { echo ' &middot; <span class="prominent urgency">'.$a->new.' '.Lang::txt('COM_PROJECTS_NEW').'</span>'; } ?>
						</span>
						<?php  if (count($comments) > 0) { // Show Comments ?>
							<ol class="comments" id="comments_<?php echo $a->id; ?>">
								<?php foreach ($comments as $comment) {
									$ctimeclass = $projects[$pid]->lastvisit && $projects[$pid]->lastvisit <= $comment->created ? ' class="urgency"' : '';
								?>
								<li class="quote" id="c_<?php echo $comment->id; ?>">
									<?php echo stripslashes(\Components\Projects\Helpers\Html::replaceUrls($comment->comment, 'external')); ?>
									<span class="block mini faded"><?php echo $comment->author; ?> &middot; <span <?php echo $ctimeclass; ?>><?php echo JHTML::_('date.relative', $comment->created); ?></span></span>
								</li>
								<?php } ?>
							</ol>
						<?php } ?>
					<?php } // end if commentable ?>
				</td>
			</tr>
	<?php 	$i++; } // end foreach ?>
		</tbody>
	</table>
<?php } else { ?>
	<p class="noresults"><?php echo Lang::txt('COM_PROJECTS_NO_ACTIVITIES'); ?></p>
<?php } ?>

<div id="more-updates" class="nav_pager">
	<?php
	if ($this->total > $this->filters['limit']) {
		$limit = $this->filters['limit'] + $this->limit; ?>
		<p><a href="<?php echo Route::url('index.php?option=com_groups&cn='.$this->gid.'&active=projects&action=updates&limit='.$limit.'&prev='.$this->filters['limit']);  ?>"><?php echo Lang::txt('COM_PROJECTS_VIEW_OLDER_ENTRIES'); ?></a></p>
	<?php } else if ($this->filters['limit'] != $this->limit) { ?>
		<p><?php echo Lang::txt('COM_PROJECTS_VIEW_OLDER_ENTRIES_NO_MORE'); ?></p>
	<?php } ?>
</div><!-- / #more-updates -->
