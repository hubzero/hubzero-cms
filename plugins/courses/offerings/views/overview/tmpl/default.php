<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->css();
?>
<?php if ($this->course->access('edit', 'course')) { ?>
<div class="manager-options">
	<a class="icon-add btn btn-secondary" id="add-offering" href="<?php echo JRoute::_($this->course->link() . '&task=newoffering'); ?>">
		<?php echo JText::_('PLG_COURSES_OFFERINGS_NEW_OFFERING'); ?>
	</a>
	<span><strong><?php echo JText::_('PLG_COURSES_OFFERINGS_NEW_OFFERING_EXPLANATION'); ?></strong></span>
</div>
<?php } ?>
<div class="container">
	<table class="entries">
		<thead>
			<tr>
				<th scope="col"><?php echo JText::_('PLG_COURSES_OFFERINGS_OFFERING'); ?></th>
				<th scope="col"><?php echo JText::_('PLG_COURSES_OFFERINGS_ENROLLED'); ?></th>
				<th scope="col"><?php echo JText::_('PLG_COURSES_OFFERINGS_ENROLLMENT'); ?></th>
			</tr>
		</thead>
		<tbody>
	<?php
	/*$offerings = $this->course->offerings(array(
		'state'    => 1,
		'sort_Dir' => 'ASC'
	), true);*/
	$offerings = $this->course->offerings();

	if ($offerings->total() > 0)
	{
		$now = JFactory::getDate()->toSql();

		foreach ($offerings as $offering)
		{
			if ($offering->isDeleted())
			{
				continue;
			}
			if ($this->course->isManager())
			{
				$offering->sections(array('available' => false));
			}
			?>
			<tr>
				<th class="offering-title">
					<span>
						<?php echo $this->escape(stripslashes($offering->get('title'))); ?>
					</span>
				</th>
			<?php if ($offering->sections()->total() <= 1) { ?>
				<?php
				$section = $offering->sections()->fetch('first');
				if (is_object($section))
				{
					$offering->section($section->get('id'));
				}
				?>
				<td>
					<?php if ($this->course->isManager()) { ?>
					<a class="access btn" href="<?php echo JRoute::_($offering->link('enter')); ?>">
						<?php echo JText::_('PLG_COURSES_OFFERINGS_ACCESS_COURSE'); ?>
					</a>
					<?php } else if ($offering->student(JFactory::getUser()->get('id'))->get('student')) { ?>
					<a class="access btn" href="<?php echo JRoute::_($offering->link('enter')); ?>">
						<?php echo JText::_('PLG_COURSES_OFFERINGS_ACCESS_COURSE'); ?>
					</a>
					<?php } else { ?>
						<?php if ($offerings->total() > 1 && $offering->isAvailable()) { ?>
						<a class="enroll btn" href="<?php echo JRoute::_($offering->link('enroll')); ?>">
							<?php echo JText::_('PLG_COURSES_OFFERINGS_ENROLL_IN_COURSE'); ?>
						</a>
						<?php } else { ?>
						--
						<?php } ?>
					<?php } ?>
				</td>
				<td>
					<?php if ($offering->isAvailable()) { ?>
					<span class="accepting enrollment">
						<?php echo JText::_('PLG_COURSES_OFFERINGS_STATUS_ACCEPTING'); ?>
					</span>
					<?php } else { ?>
					<span class="closed enrollment">
						<?php echo JText::_('PLG_COURSES_OFFERINGS_STATUS_CLOSED'); ?>
					</span>
					<?php } ?>
				</td>
			<?php } else { ?>
				<td>
					&nbsp;
				</td>
				<td>
					&nbsp;
				</td>
			<?php } ?>
			</tr>
			<?php
			if ($offering->sections()->total() > 1)
			{
				foreach ($offering->sections() as $section)
				{
					if ($section->isDeleted())
					{
						continue;
					}
					if (!$this->course->isManager())
					{
						// If section is in draft mode or not published
						if ($section->isDraft() || !$section->isPublished())
						{
							continue;
						}
						// If section hasn't started or has ended
						if (!$section->started() || $section->ended())
						{
							continue;
						}
						// If a publish down time is set and that time happened before now
						if ($section->get('publish_down') != '0000-00-00 00:00:00' && $section->get('publish_down') <= $now)
						{
							continue;
						}
						// If not already a member and enrollment is closed
						if (!$section->isMember() && $section->get('enrollment') == 2)
						{
							continue;
						}
					}
					$offering->section($section->get('id'));
				?>
			<tr>
				<th class="section-title">
					<span>
						<?php echo $this->escape(stripslashes($section->get('title'))); ?>
					</span>
				</th>
				<td>
					<?php if ($this->course->isManager()) { ?>
					<a class="access btn" href="<?php echo JRoute::_($offering->link('enter')); ?>">
						<?php echo JText::_('PLG_COURSES_OFFERINGS_ACCESS_COURSE'); ?>
					</a>
					<?php } else if ($section->isMember()) { ?>
					<a class="access btn" href="<?php echo JRoute::_($offering->link('enter')); ?>">
						<?php echo JText::_('PLG_COURSES_OFFERINGS_ACCESS_COURSE'); ?>
					</a>
					<?php } else { ?>
						<?php if ($offerings->total() > 1 && $offering->isAvailable()) { ?>
						<a class="enroll btn" href="<?php echo JRoute::_($offering->link('enroll')); ?>">
							<?php echo JText::_('PLG_COURSES_OFFERINGS_ENROLL_IN_COURSE'); ?>
						</a>
						<?php } else { ?>
						--
						<?php } ?>
					<?php } ?>
				</td>
				<td>
					<?php
					switch ($section->get('enrollment'))
					{
						case 0:
							?>
							<span class="accepting enrollment">
								<?php echo JText::_('PLG_COURSES_OFFERINGS_STATUS_ACCEPTING'); ?>
							</a>
							<?php
						break;

						case 1:
							?>
							<span class="restricted enrollment">
								<?php echo JText::_('PLG_COURSES_OFFERINGS_STATUS_RESTRICTED'); ?>
							</span>
							<?php
						break;

						case 2:
							?>
							<span class="closed enrollment">
								<?php echo JText::_('PLG_COURSES_OFFERINGS_STATUS_CLOSED'); ?>
							</span>
							<?php
						break;
					}
					?>
				</td>
			</tr>
				<?php
				}
			}
		}
	}
	else
	{
	?>
			<tr>
				<td><?php echo JText::_('PLG_COURSES_OFFERINGS_NONE_FOUND'); ?></td>
			</tr>
	<?php
	}
	?>
		</tbody>
	</table>
</div><!-- / .container -->