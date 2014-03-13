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
defined('_JEXEC') or die( 'Restricted access' );

?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div>

<?php if ($this->config->get('access-create-course')) { ?>
<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a class="add btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=course&task=new'); ?>"><?php echo JText::_('COM_COURSES_CREATE_COURSE'); ?></a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->
<?php } ?>

<?php
if (count($this->notifications) > 0)
{
	foreach ($this->notifications as $notification) 
	{
		echo '<p class="' . $this->escape($notification['type']) . '">' . $notification['message'] . '</p>';
	}
}
?>

<div id="introduction" class="section">
	<div class="aside">
		<h3>Questions?</h3>
		<ul>
			<li>
				<a class="popup" href="<?php echo JRoute::_('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index'); ?>">Need Help?</a>
			</li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
		<div class="grid">
			<div class="col span-half">
				<h3>About courses</h3>
				<p>When you take one of our courses, you will watch lectures taught by world-class professors, learn at your own pace, test your knowledge, and reinforce concepts through interactive exercises. Every course is created based on a syllabus and presented online using an ordered, easy-to-follow framework.</p>
			</div>
			<div class="col span-half omega">
				<h3>How a course works</h3>
				<p>As the course progresses, you have access to all the notes, instructor lectures and discussions you've engaged in up to that point, as well as all the course materials. And the dialogue and interaction extends beyond your classmates; For many courses, Professors are actively involved in online classroom discussions and available to answer questions.</p>
			</div>
		</div>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / #introduction.section -->

<div class="section">
	<div class="grid">
		<div class="col span3">
			<h2><?php echo JText::_('Find a course'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<div class="grid">
				<div class="col span6">
					<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>" method="get" class="search">
						<fieldset>
							<p>
								<label for="gsearch"><?php echo JText::_('Keyword or phrase:'); ?></label>
								<input type="text" name="search" id="gsearch" value="" />
								<input type="submit" value="<?php echo JText::_('COM_COURSES_SEARCH'); ?>" />
							</p>
							<p>
								<?php echo JText::_('Search course names and descriptions.'); ?>
							</p>
						</fieldset>
					</form>
				</div><!-- / .col span6 omega -->
				<div class="col span6 omega">
					<div class="browse">
						<p><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo JText::_('Browse the list of available courses'); ?></a></p>
						<p><?php echo JText::_('A list of all available courses.'); ?></p>
					</div><!-- / .browse -->
				</div><!-- / .col span6 omega -->
			</div><!-- / .grid -->
		</div><!-- / .col span9 -->
	</div><!-- / .grid -->

	<?php if (!$this->user->get("guest")) : ?>
		<?php if ($this->config->get("intro_mycourses", 1)) : ?>
			<div class="grid">
				<div class="col span3">
					<h2><?php echo JText::_('COM_COURSES_MY_COURSES'); ?></h2>
				</div><!-- / .col span3 -->
				<div class="col span9 omega">
					<div class="mycourses clearfix top">
						<?php 
						if (count($this->mycourses) > 0)
						{
							$mycourses = array();
							foreach ($this->mycourses as $k => $course)
							{
								if (!isset($mycourses[$course->get('alias')]))
								{
									$mycourses[$course->get('alias')] = $course;
								}
								continue;
							}

							$count = 0;
							foreach ($mycourses as $course)
							{
								$this->view('_course')
								     ->set('count', $count)
								     ->set('columns', 2)
								     ->set('course', $course)
								     ->display();


								if ($count == 1) 
								{
									$count = 0;
									echo '<div class="clear"></div>';
								}
								else
								{
									$count++;
								}
							}
						}
						else
						{
							?>
							<p class="info"><?php echo JText::sprintf('COM_COURSES_NO_MY_COURSES', $this->user->get('id')); ?></p>
							<?php
						}
						?>
					</div><!-- / .mycourses clearfix top -->
				</div><!-- / .col span9 -->
			</div><!-- / .grid -->
		<?php endif; ?>
	<?php endif; ?>

	<?php if (!$this->user->get("guest")) : ?>
		<?php if ($this->config->get("intro_interestingcourses", 1)) : ?>
			<div class="grid">
				<div class="col span3">
					<h2><?php echo JText::_('COM_COURSES_INTERESTING_COURSES'); ?></h2>
				</div><!-- / .col span3 -->
				<div class="col span9 omega">
					<div class="interestingcourses clearfix top">
						<?php 
						if (count($this->interestingcourses) > 0)
						{
							$count = 0;
							foreach ($this->interestingcourses as $course)
							{
								$this->view('_course')
								     ->set('count', $count)
								     ->set('columns', 2)
								     ->set('course', $course)
								     ->display();

								if ($count == 1) 
								{
									$count = 0;
									echo '<div class="clear"></div>';
								}
								else
								{
									$count++;
								}
							}
						}
						else
						{
							?>
							<p class="info"><?php echo JText::sprintf('COM_COURSES_NO_INTERESTING_COURSES', $this->user->get('id')); ?></p>
							<?php
						}
						?>
					</div>
				</div><!-- / .col span9 -->
			</div><!-- / .grid -->
		<?php endif; ?>
	<?php endif; ?>

	<?php if ($this->config->get("intro_popularcourses", 1)) : ?>
		<div class="grid">
			<div class="col span3">
				<h2><?php echo JText::_('COM_COURSES_POPULAR_COURSES'); ?></h2>
			</div><!-- / .col span3 -->
			<div class="col span9 omega">
				<div class="popularcourses clearfix top">
					<?php 
					if (count($this->popularcourses) > 0)
					{
						$count = 0;
						foreach ($this->popularcourses as $course)
						{
							$this->view('_course')
							     ->set('count', $count)
							     ->set('columns', 2)
							     ->set('course', $course)
							     ->display();

							if ($count == 1) 
							{
								$count = 0;
								echo '<div class="clear"></div>';
							}
							else
							{
								$count++;
							}
						}
					}
					else
					{
						?>
						<p class="info"><?php echo JText::sprintf('COM_COURSES_NO_POPULAR_COURSES', $this->user->get('id')); ?></p>
						<?php
					}
					?>
				</div><!-- / .popularcourses clearfix top -->
			</div><!-- / .col span9 omega -->
		</div><!-- /.grid -->
	<?php endif; ?>
</div><!-- / .section -->
