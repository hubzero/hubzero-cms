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

//get no_html request var
$no_html = JRequest::getInt('no_html', 0);
$tmpl    = JRequest::getWord('tmpl', false);
$sparams = new JRegistry($this->course->offering()->section()->get('params'));

if (!$no_html && $tmpl != 'component') :
	$this->css('offering.css')
	     ->js('courses.offering.js');

	$src = $this->course->logo('url');
	if ($logo = $this->course->offering()->section()->logo('url'))
	{
		$src = $logo;
	}
	else if ($logo = $this->course->offering()->logo('url'))
	{
		$src = $logo;
	}
	?>
	<header id="content-header"<?php if ($this->course->get('logo')) { echo ' class="with-identity"'; } ?>>
		<h2>
			<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
		</h2>
		<?php if ($src) { ?>
		<p class="course-identity">
			<img src="<?php echo JRoute::_($src); ?>" alt="<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>" />
		</p>
		<?php } ?>
		<p id="page_identity">
			<a class="prev" href="<?php echo JRoute::_($this->course->link()); ?>">
				<?php echo JText::_('COM_COURSES_COURSE_OVERVIEW'); ?>
			</a>
			<strong>
				<?php echo JText::_('COM_COURSES_OFFERING'); ?>:
			</strong>
			<span>
				<?php echo $this->escape(stripslashes($this->course->offering()->get('title'))); ?>
			</span>
			<strong>
				<?php echo JText::_('COM_COURSES_SECTION'); ?>:
			</strong>
			<span>
				<?php echo $this->escape(stripslashes($this->course->offering()->section()->get('title'))); ?>
			</span>
		</p>
	</header><!-- / #content-header -->

	<div class="innerwrap">
		<div id="page_container">
<?php endif; ?>

<?php if (!$this->course->offering()->access('view') && !$sparams->get('preview', 0)) { ?>
			<div id="offering-introduction">
				<div class="instructions">
					<p class="warning"><?php echo JText::_('COM_COURSES_ENROLLMENT_REQUIRED'); ?></p>
				</div><!-- / .instructions -->
				<div class="questions">
					<p><strong><?php echo JText::_('COM_COURSES_HOW_TO_ENROLL'); ?></strong></p>
					<p><?php echo JText::sprintf('COM_COURSES_HOW_TO_ENROLL_EXPLANATION', JRoute::_($this->course->link())); ?></p>
					<p><strong><?php echo JText::_('COM_COURSES_WHERE_TO_LEARN_MORE'); ?></strong></p>
					<p><?php echo JText::sprintf('COM_COURSES_WHERE_TO_LEARN_MORE_EXPLANATION', JRoute::_($this->course->link()), JRoute::_('index.php?option=' . $this->option . '&controller=courses&task=browse')); ?></p>
				</div><!-- / .post-type -->
			</div><!-- / #collection-introduction -->
<?php } else if ($this->course->offering()->section()->expired() && !$sparams->get('preview', 0)) { ?>
			<div id="offering-introduction">
				<div class="instructions">
					<p class="warning"><?php echo JText::_('COM_COURSES_SECTION_EXPIRED'); ?></p>
				</div><!-- / .instructions -->
				<div class="questions">
					<p><strong><?php echo JText::_('COM_COURSES_WHERE_TO_LEARN_MORE'); ?></strong></p>
					<p><?php echo JText::sprintf('COM_COURSES_WHERE_TO_LEARN_MORE_EXPLANATION', JRoute::_($this->course->link()), JRoute::_('index.php?option=' . $this->option . '&controller=courses&task=browse')); ?></p>
				</div><!-- / .post-type -->
			</div><!-- / #collection-introduction -->
<?php } else { ?>

	<?php if (!$no_html && $tmpl != 'component') : ?>
			<div id="page_sidebar">

				<ul id="page_menu">
					<?php
					$active = JRequest::getVar('active');

					// Loop through each plugin and build menu item
					foreach ($this->plugins as $plugin)
					{
						// Do we want to show in menu?
						if (!$plugin->get('display_menu_tab'))
						{
							continue;
						}

						// Do we have access?
						if (!$this->course->offering()->access('manage', 'section') && $plugin->get('default_access') == 'managers')
						{
							continue;
						}

						// Can we view this tab?
						if (!$this->course->offering()->access('view') && !$sparams->get('preview', 0))
						{
							?>
							<li class="protected members-only course-<?php echo $plugin->get('name'); ?>-tab" data-title="<?php echo JText::_('COM_COURSES_RESTRICTED_PAGE'); ?>">
								<span class="<?php echo $plugin->get('name'); ?>" data-icon="&#x<?php echo $plugin->get('icon', 'f0a1'); ?>">
									<?php echo $this->escape($plugin->get('title')); ?>
								</span>
							</li>
							<?php
						}
						else
						{
							$link = JRoute::_($this->course->offering()->link() . '&active=' . $plugin->get('name'));
							?>
							<li class="<?php echo ($active == $plugin->get('name') ? 'active' : ''); ?> course-<?php echo $plugin->get('name'); ?>-tab">
								<a class="<?php echo $plugin->get('name'); ?>" data-icon="&#x<?php echo $plugin->get('icon', 'f0a1'); ?>" data-title="<?php echo $this->escape($plugin->get('title')); ?>" href="<?php echo $link; ?>">
									<?php echo $this->escape($plugin->get('title')); ?>
								</a>
								<?php if ($meta_count = $plugin->get('meta_count')) { ?>
									<span class="meta">
										<span class="count"><?php echo $meta_count; ?></span>
									</span>
								<?php } ?>
								<?php echo $plugin->get('meta_alert'); ?>
							</li>
							<?php
						}
					}
					?>
				</ul><!-- /#page_menu -->
			</div><!-- /#page_sidebar -->

			<div id="page_main">
				<div id="page_notifications">
					<?php
						foreach ($this->notifications as $notification)
						{
							echo '<p class="' . $this->escape($notification['type']) . '">' . $this->escape($notification['message']) . '</p>';
						}
					?>
				</div><!-- /#page_notifications -->

				<div id="page_content" class="course_<?php echo $active; ?>">
<?php endif; ?>

					<?php
					foreach ($this->plugins as $plugin)
					{
						if ($html = $plugin->get('html'))
						{
							echo $html;
						}
					}
					?>

		<?php if (!$no_html && $tmpl != 'component') : ?>
				</div><!-- /#page_content -->
			</div><!-- /#page_main -->
		<?php endif; ?>
<?php } ?>

	<?php if (!$no_html && $tmpl != 'component') : ?>
		</div><!-- /#page_container -->
	</div><!-- /.innerwrap -->
	<?php endif; ?>