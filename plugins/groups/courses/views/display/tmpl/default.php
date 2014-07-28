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

$this->css();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=courses';
?>
<h3 class="section-header">
	<?php echo JText::_('PLG_GROUPS_COURSES'); ?>
</h3>

<div class="section">
<?php if (count($this->results) > 0) { ?>
	<div class="container" id="courses-container">
		<form method="get" action="<?php JRoute::_($base); ?>">

			<?php
			$qs  = ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
			//$qs .= ($this->filters['index']  ? '&index=' . $this->escape($this->filters['index'])   : '');
			//$qs .= ($this->filters['tag']    ? '&tag=' . $this->escape($this->filters['tag'])       : '');
			//$qs .= ($this->filters['group']  ? '&group=' . $this->escape($this->filters['group'])   : '');
			?>
			<ul class="entries-menu order-options">
				<li><a<?php echo ($this->filters['sortby'] == 'title') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($base . '&sortby=title' . $qs); ?>" title="<?php echo JText::_('PLG_GROUPS_COURSES_SORT_BY_TITLE'); ?>"><?php echo JText::_('PLG_GROUPS_COURSES_SORT_TITLE'); ?></a></li>
				<li><a<?php echo ($this->filters['sortby'] == 'popularity') ? ' class="active"' : ''; ?> href="<?php echo JRoute::_($base . '&sortby=popularity' . $qs); ?>" title="<?php echo JText::_('PLG_GROUPS_COURSES_SORT_BY_ENROLLED'); ?>"><?php echo JText::_('PLG_GROUPS_COURSES_SORT_ENROLLED'); ?></a></li>
			</ul>

			<table class="courses entries">
				<caption>
					<?php
					$s = ($this->total > 0) ? $this->filters['start']+1 : 0;
					$e = ($this->total > ($this->filters['start'] + $this->filters['limit'])) ? ($this->filters['start'] + $this->filters['limit']) : $this->total;

					echo $this->escape(JText::_('PLG_GROUPS_COURSES'));
					?>
					<span>(<?php echo JText::sprintf('PLG_GROUPS_COURSES_RESULTS_TOTAL', $s, $e, $this->total); ?>)</span>
				</caption>
				<tbody>
			<?php
				foreach ($this->results as $course)
				{
			?>
					<tr class="course">
						<th>
							<span class="entry-id"><?php echo $course->get('id'); ?></span>
						</th>
						<td>
							<a class="entry-title" href="<?php echo JRoute::_($course->link()); ?>">
								<?php echo $this->escape(stripslashes($course->get('title'))); ?>
							</a><br />
						<?php
							$instructors = $course->instructors();
							if (count($instructors) > 0)
							{
								$names = array();
								foreach ($instructors as $i)
								{
									$instructor = \Hubzero\User\Profile::getInstance($i->get('user_id'));

									$names[] = '<a href="' . JRoute::_('index.php?option=com_members&id=' . $i->get('user_id')) . '">' . $this->escape(stripslashes($instructor->get('name'))) . '</a>';
								}
						?>
							<span class="entry-details">
								Instructors: <span class="entry-instructors"><?php echo implode(', ', $names); ?></span>
							</span>
							<span class="entry-content">
								<?php echo \Hubzero\Utility\String::truncate(stripslashes($course->get('blurb')), 200); ?>
							</span>
						</td>
						<td>
						<?php
							}
						?>
							<span class="<?php
							switch ($course->get('state'))
							{
								case 3: echo 'draft'; break;
								case 2: echo 'trashed'; break;
								case 1: echo 'published'; break;
								case 0: echo 'unpublished'; break;
							}
							?> entry-state">
							<?php
							switch ($course->get('state'))
							{
								case 3: echo JText::_('PLG_GROUPS_COURSES_STATE_DRAFT'); break;
								case 2: echo JText::_('PLG_GROUPS_COURSES_STATE_DELETED'); break;
								case 1: echo JText::_('PLG_GROUPS_COURSES_STATE_PUBLISHED'); break;
								case 0: echo JText::_('PLG_GROUPS_COURSES_STATE_UNPUBLISHED'); break;
							}
							?>
							</span>
						</td>
					</tr>
			<?php
				}
			?>
				</tbody>
			</table>

			<?php
			jimport('joomla.html.pagination');
			$pageNav = new JPagination(
				$this->total,
				$this->filters['start'],
				$this->filters['limit']
			);
			$pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
			$pageNav->setAdditionalUrlParam('active', 'courses');
			$pageNav->setAdditionalUrlParam('action', '');
			$pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);

			echo $pageNav->getListFooter();
			?>
			<div class="clearfix"></div>
		</form>
	</div>
<?php } else { ?>
	<div id="courses-introduction">
		<div class="instructions">
			<p><?php echo JText::_('PLG_GROUPS_COURSES_NONE'); ?></p>
		</div><!-- / .instructions -->
		<div class="questions">
			<p><strong><?php echo JText::_('PLG_GROUPS_COURSES_WHAT_IS_THIS'); ?></strong></p>
			<p><?php echo JText::_('PLG_GROUPS_COURSES_ABOUT_PLUGIN'); ?><p>
			<p><strong><?php echo JText::_('PLG_GROUPS_COURSES_WHAT_ARE_COURSES'); ?></strong></p>
			<p><?php echo JText::_('PLG_GROUPS_COURSES_EXPLANATION'); ?><p>
		</div><!-- / .post-type -->
	</div><!-- / #collection-introduction -->
<?php } ?>
</div>