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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$base = 'index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=courses';
?>
<h3 class="section-header">
	<?php echo JText::_('PLG_MEMBERS_COURSES'); ?>
</h3>

<div class="section">
<?php if ($this->hasRoles) {?>

<?php if ($this->roles && $this->hasRoles > 1) { ?>
	<ul class="entries-menu user-options">
		<?php foreach ($this->roles as $s) { ?>
			<?php 
			if ($s->total <= 0)
			{
				continue;
			}
			$sel = '';
			if ($this->filters['task'] == $s->alias)
			{
				//$active = $s;
				$sel = 'active';
			}
			?>
			<li>
				<a class="<?php echo $s->alias . ' ' . $sel; ?>" title="<?php echo $this->escape(stripslashes($s->title)); ?>" href="<?php echo JRoute::_($base . '&task=' . $s->alias . '&sort=' . $this->filters['sort']); ?>">
					<?php echo $this->escape(stripslashes($s->title)); ?> (<?php echo $this->escape($s->total); ?>)
				</a>
			</li>
		<?php } ?>
	</ul>
<?php } ?>

	<div class="container" id="courses-container">
		<form method="get" action="<?php JRoute::_($base); ?>">

			<ul class="entries-menu order-options">
				<li>
					<a<?php echo ($this->filters['sort'] == 'title') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($base . '&task=' . urlencode($this->filters['task']) . '&sort=title'); ?>" title="<?php echo JText::_('PLG_MEMBERS_COURSES_SORT_BY_TITLE'); ?>">
						<?php echo JText::_('PLG_MEMBERS_COURSES_SORT_TITLE'); ?>
					</a>
				</li>
				<li>
					<a<?php echo ($this->filters['sort'] == 'enrolled') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($base . '&task=' . urlencode($this->filters['task']) . '&sort=enrolled'); ?>" title="<?php echo JText::_('PLG_MEMBERS_COURSES_SORT_BY_ENROLLED'); ?>">
						<?php echo JText::_('PLG_MEMBERS_COURSES_SORT_ENROLLED'); ?>
					</a>
				</li>
			</ul>

			<table class="courses entries" summary="<?php echo JText::_('PLG_MEMBERS_COURSES_RESULTS_SUMMARY'); ?>">
				<caption>
					<?php 
					$s = ($this->total > 0) ? $this->filters['start']+1 : 0; //($this->filters['start'] > 0) ? $this->filters['start']+1 : $this->filters['start'];
					$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;

					echo $this->escape(stripslashes($this->active->title)); //JText::_('PLG_MEMBERS_COURSES_' . strtoupper($this->filters['task'])); 
					?>
					<span>(<?php echo JText::sprintf('PLG_MEMBERS_COURSES_RESULTS_TOTAL', $s, $e, $this->total); ?>)</span>
				</caption>
				<tbody>
		<?php if (count($this->results) > 0) { ?>
			<?php
				foreach ($this->results as $row)
				{
					$cls = '';
					$sfx = '';

					if (isset($row->offering_alias))
					{
						$sfx .= '&offering=' . $row->offering_alias;
					}
					if (isset($row->section_alias) && !$row->is_default)
					{
						$sfx .= ':' . $row->section_alias;
					}

					switch ($this->filters['task'])
					{
						case 'student':
							$cls = 'student';
							$dateText = JText::_('PLG_MEMBERS_COURSES_ENROLLED');
						break;

						case 'manager':
						case 'instructor':
						case 'ta':
						default:
							$cls = 'manager';
							$dateText = JText::_('PLG_MEMBERS_COURSES_EMPOWERED');
						break;
					}
			?>
					<tr class="course<?php echo ($cls) ? ' ' . $cls : ''; ?>">
						<th>
							<span class="entry-id"><?php echo $row->id; ?></span>
						</th>
						<td>
							<a class="entry-title" href="<?php echo JRoute::_('index.php?option=com_courses&gid=' . $row->alias . $sfx); ?>">
								<?php echo $this->escape(stripslashes($row->title)); ?>
							</a><br />
							<span class="entry-details">
								<?php echo $dateText; ?> 
								<!-- 
								<span class="entry-date-at"><?php echo JText::_('PLG_MEMBERS_COURSES_AT'); ?></span> 
								<span class="entry-time"><time datetime="<?php echo $row->enrolled; ?>"><?php echo JHTML::_('date', $row->enrolled, JText::_('TIME_FORMAT_HZ1')); ?></time></span> 
								-->
								<span class="entry-date-on"><?php echo JText::_('PLG_MEMBERS_COURSES_ON'); ?></span> 
								<span class="entry-date"><time datetime="<?php echo $row->enrolled; ?>"><?php echo JHTML::_('date', $row->enrolled, JText::_('DATE_FORMAT_HZ1')); ?></time></span>
								<?php if ($row->section_title) { ?>
								<span class="entry-section">
									 &mdash; <strong><?php echo JText::_('PLG_MEMBERS_COURSES_SECTION'); ?></strong> <?php echo $this->escape(stripslashes($row->section_title)); ?>
								</span>
								<?php } ?>
							</span>
						</td>
						<td>
							<?php if ($row->state == 3) { ?>
							<span class="entry-state draft">
								<?php echo JText::_('PLG_MEMBERS_COURSES_STATE_DRAFT'); ?>
							</span>
							<?php } ?>
						</td>
						<td>
							<?php if ($row->starts) { ?>
							<?php echo JText::_('PLG_MEMBERS_COURSES_STARTS'); ?><br />
							<span class="entry-details">
							<?php if ($row->starts != '0000-00-00 00:00:00') { ?>
								<span class="entry-date-at"><?php echo JText::_('PLG_MEMBERS_COURSES_AT'); ?></span> 
								<span class="entry-time"><time datetime="<?php echo $row->starts; ?>"><?php echo JHTML::_('date', $row->starts, JText::_('TIME_FORMAT_HZ1')); ?></time></span> 
								<span class="entry-date-on"><?php echo JText::_('PLG_MEMBERS_COURSES_ON'); ?></span> 
								<span class="entry-date"><time datetime="<?php echo $row->starts; ?>"><?php echo JHTML::_('date', $row->starts, JText::_('DATE_FORMAT_HZ1')); ?></time></span>
							<?php } else { ?>
								<?php echo JText::_('PLG_MEMBERS_COURSES_NA'); ?>
							<?php } ?>
							</span>
							<?php } ?>
						</td>
						<td>
							<?php if ($row->ends) { ?>
							<?php echo JText::_('PLG_MEMBERS_COURSES_ENDS'); ?><br />
							<span class="entry-details">
								<?php if ($row->ends != '0000-00-00 00:00:00') { ?>
								<span class="entry-date-at"><?php echo JText::_('PLG_MEMBERS_COURSES_AT'); ?></span> 
								<span class="entry-time"><time datetime="<?php echo $row->ends; ?>"><?php echo JHTML::_('date', $row->ends, JText::_('TIME_FORMAT_HZ1')); ?></time></span> 
								<span class="entry-date-on"><?php echo JText::_('PLG_MEMBERS_COURSES_ON'); ?></span> 
								<span class="entry-date"><time datetime="<?php echo $row->ends; ?>"><?php echo JHTML::_('date', $row->ends, JText::_('DATE_FORMAT_HZ1')); ?></time></span>
								<?php } else { ?>
									<?php echo JText::_('PLG_MEMBERS_COURSES_NA'); ?>
								<?php } ?>
							</span>
							<?php } ?>
						</td>
					</tr>
			<?php
				}
			?>
		<?php } else { ?>
					<tr>
						<td>
							<?php echo JText::_('PLG_MEMBERS_COURSES_NO_RESULTS'); ?>
						</td>
					</tr>
		<?php } // end if (count($this->results) > 0) { ?>
				</tbody>
			</table>
			
			<?php 
			$this->pageNav->setAdditionalUrlParam('id', $this->member->get('uidNumber'));
			$this->pageNav->setAdditionalUrlParam('active', 'courses');
			$this->pageNav->setAdditionalUrlParam('task', $this->filters['task']);
			$this->pageNav->setAdditionalUrlParam('action', '');
			$this->pageNav->setAdditionalUrlParam('sort', $this->filters['sort']);
			
			echo $this->pageNav->getListFooter();
			?>
			<div class="clearfix"></div>
		</form>
	</div>
<?php } else { ?>
	<div id="courses-introduction">
		<div class="instructions">
			<ol>
				<li><?php echo JText::sprintf('PLG_MEMBERS_COURSES_FIND_COURSE', JRoute::_('index.php?option=com_courses')); ?></li>
				<li><?php echo JText::_('PLG_MEMBERS_COURSES_ENROLL'); ?></li>
				<li><?php echo JText::_('PLG_MEMBERS_COURSES_GET_LEARNING'); ?></li>
			</ol>
		</div><!-- / .instructions -->
		<div class="questions">
			<p><strong><?php echo JText::_('PLG_MEMBERS_COURSES_WHAT_ARE_COURSES'); ?></strong></p>
			<p><?php echo JText::_('PLG_MEMBERS_COURSES_EXPLANATION'); ?><p>
		</div><!-- / .post-type -->
	</div><!-- / #collection-introduction -->
<?php } ?>
</div>