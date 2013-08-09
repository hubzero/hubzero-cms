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

//get objects
$config 	=& JFactory::getConfig();
$database 	=& JFactory::getDBO();

//is membership control managed on course?
$membership_control = $this->config->get('membership_control', 1);

//get no_html request var
$no_html = JRequest::getInt( 'no_html', 0 );
$tmpl    = JRequest::getWord('tmpl', false);

$base = 'index.php?option=' . $this->option . '&controller=course&gid=' . $this->course->get('alias');

if (!$no_html && $tmpl != 'component') : ?>
	<div id="content-header"<?php if ($this->course->get('logo')) { echo ' class="with-identity"'; } ?>>
		<h2>
			<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
		</h2>
		<?php if ($this->course->get('logo')) { ?>
		<p class="course-identity">
			<img src="/site/courses/<?php echo $this->course->get('id'); ?>/<?php echo $this->course->get('logo'); ?>" alt="<?php echo JText::_('Course logo'); ?>" />
		</p>
		<?php } ?>
		<p id="page_identity">
			<a class="prev" href="<?php echo JRoute::_($base); ?>">
				<?php echo JText::_('Course overview'); ?>
			</a>
			<strong>
				Offering:
			</strong>
			<span>
				<?php echo $this->escape(stripslashes($this->course->offering()->get('title'))); ?>
			</span>
			<strong>
				Section:
			</strong>
			<span>
				<?php 
			/*if ($this->course->access('manage'))
			{
				?>
				<select name="section">
				<?php
				foreach ($this->course->offering()->sections() as $section)
				{
					?>
					<option value="<?php echo $this->escape(stripslashes($section->get('alias'))); ?>"<?php if ($section->get('alias') == $this->course->offering()->section()->get('alias')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->get('title'))); ?></option>
					<?php
				}
				?>
				</select>
				<?php
			}
			else
			{*/
				echo $this->escape(stripslashes($this->course->offering()->section()->get('title'))); 
			//}
				?>
			</span>
		</p>
	</div>
	<?php /*<div id="content-header-extra">
		<ul>
			<li>
				<a class="browse btn" href="<?php echo JRoute::_($base); ?>">
					<?php echo JText::_('Course Overview'); ?>
				</a>
			</li>
		</ul>
	</div>
if ($this->course->offering()->access('manage', 'section')) { ?>
	<div id="manager_options">
		<ul id="course_options">
			<li class="no-float">
				<a href="<?php echo JRoute::_($base . '&offering=' . $this->course->offering()->get('alias')); ?>" class="dropdown course-manager">
					<?php echo 'Manager'; //($isManager) ? 'Manager' : 'Member'; ?>
					<span class="caret"></span>
				</a>
				<ul class="dropdown-menu pull-right">
			<?php if ($this->course->offering()->access('manage', 'section')) : ?> 
					<li><a class="course-invite" href="/courses/<?php echo $this->course->get('alias'); ?>/invite">Invite Students</a></li>
			<?php endif; ?>
			<?php if ($this->course->offering()->access('manage')) { ?>
					<li><a class="course-outline" href="/courses/<?php echo $this->course->get('alias'); ?>/manage/<?php echo $this->course->offering()->get('alias'); ?>">Build Outline</a></li>
					<li><a class="course-pages" href="/courses/<?php echo $this->course->get('alias'); ?>/manage/<?php echo $this->course->offering()->get('alias'); ?>/pages">Manage Pages</a></li>
			<?php } else if ($this->course->offering()->access('manage', 'section')) { ?>
					<li><a class="course-outline" href="/courses/<?php echo $this->course->get('alias'); ?>/manage/<?php echo $this->course->offering()->get('alias'); ?>">Change Dates</a></li>
			<?php } ?>
			<?php if ($this->course->access('manage')) : ?> 
					<li class="divider"></li>
					<li><a class="course-delete" href="/courses/<?php echo $this->course->get('alias'); ?>/delete">Delete Offering</a></li>
			<?php endif; ?>
				</ul>
			</li>
		</ul><!-- /#page_options -->
		<p>You're viewing this page as a course admin, <a href="<?php echo $_SERVER['REQUEST_URI']; ?>?nonadmin=1">click</a> to view it as a student</p>
	</div>
<?php }*/ ?>
	<div class="innerwrap">
		<div id="page_container">
<?php endif; ?>
<?php if (!$this->course->offering()->access('view')) { ?>
			<div id="offering-introduction">
				<div class="instructions">
					<p class="warning"><?php echo JText::_('You must be enrolled in this course to view the content.'); ?></p>
				</div><!-- / .instructions -->
				<div class="questions">
					<p><strong><?php echo JText::_('How can I enroll?'); ?></strong></p>
					<p><?php echo JText::sprintf('To find out if enrollment is still open and how to enroll, visit the <a href="%s">course overview page</a>', JRoute::_($base)); ?></p>
					<p><strong><?php echo JText::_('Where can I learn more bout this course?'); ?></strong></p>
					<p><?php echo JText::sprintf('To learn more, either visit the <a href="%s">course overview page</a> or browse the <a href="%s">course listing</a>.', JRoute::_($base), JRoute::_('index.php?option=' . $this->option . '&controller=courses&task=browse')); ?></p>
				</div><!-- / .post-type -->
			</div><!-- / #collection-introduction -->
<?php } else { ?>
	<?php if (!$no_html && $tmpl != 'component') : ?>
			<div id="page_sidebar">

				<ul id="page_menu">
					<?php
						//instantiate objects
						$juser =& JFactory::getUser();

						//variable to hold course menu html
						$course_menu = '';

						//loop through each category and build menu item
						foreach ($this->plugins as $k => $cat)
						{
							//do we want to show category in menu?
							if ($cat['display_menu_tab'])
							{
								if (!$this->course->offering()->access('manage', 'section') 
								 && isset($this->course_plugin_access[$cat['name']]) 
								 && $this->course_plugin_access[$cat['name']] == 'managers')
								{
									continue;
								}
								//active menu item
								$li_cls = ($this->active == $cat['name']) ? 'active' : '';

								//menu name & title
								$active = $cat['name'];
								$title = $cat['title'];
								$cls = $cat['name'];

								//get the menu items access level
								//$access = $access_levels[$cat['name']];

								//menu link
								$link = JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->course->get('alias') . '&offering=' . $this->course->offering()->get('alias') . ($this->course->offering()->section()->get('alias') != '__default' ? ':' . $this->course->offering()->section()->get('alias') : '') . '&active=' . $active);

								//Are we on the overview tab with sub course pages?
								if ($cat['name'] == 'outline') // && count($this->pages) > 0
								{
									$true_active_tab = JRequest::getVar('active', 'outline');
									$li_cls = ($true_active_tab != $this->active) ? '' : $li_cls;

									if (!$this->course->offering()->access('view'))
									{
										$menu_item  = '<li class="protected course-overview-tab"><span class="outline">' . JText::_('Outline') . '</span>';
									}
									else
									{
										$menu_item  = "<li class=\"{$li_cls} course-overview-tab\">";
										$menu_item .= '<a class="outline" href="' . $link . '" data-title="' . JText::_('Outline') . '">' . JText::_('Outline') . '</a>';
									} 
									$menu_item .= '</li>';
									$menu_item .= '</li>';
								}
								else
								{
									if (!$this->course->offering()->access('view'))
									{
										$menu_item  = '<li class="protected members-only course-' . $cls . '-tab" data-title="This page is restricted to course members only!">';
										$menu_item .= '<span class="' . $cls . '">' . $title . '</span>';
										$menu_item .= '</li>';
									}
									else
									{
										//menu item meta data vars
										$metadata   = (isset($this->sections[$k]['metadata'])) ? $this->sections[$k]['metadata'] : array();
										$meta_count = (isset($metadata['count']) && $metadata['count'] != '') ? $metadata['count'] : '';
										$meta_alert = (isset($metadata['alert']) && $metadata['alert'] != '') ? $metadata['alert'] : '';

										//create menu item
										$menu_item  = '<li class="' . $li_cls . ' course-' . $cls . '-tab">';
										$menu_item .= '<a class="' . $cls . '" data-title="' . $this->escape(stripslashes($title)) . '" href="' . $link . '">' . $title . '</a>';
										if ($meta_count)
										{
											$menu_item .= '<span class="meta">';
											$menu_item .= '<span class="count">' . $meta_count . '</span>';
											$menu_item .= '</span>';
										}
										$menu_item .= $meta_alert;
										$menu_item .= '</li>';
									}
								} 

								//add menu item to variable holding entire menu
								$course_menu .= $menu_item;
							}
						}
						echo $course_menu;
					?>
				</ul><!-- /#page_menu -->

				<?php /* <div id="page_info">
					<?php 
						$dateFormat = '%d %b, %Y';
						$timeFormat = '%I:%M %p';
						$tz = 0;
						if (version_compare(JVERSION, '1.6', 'ge'))
						{
							$dateFormat = 'd M, Y';
							$timeFormat = 'h:m a';
							$tz = true;
						}
					?>
					<div class="course-info">
						<ul>
							<li class="info-join-policy">
								<span class="label">Starts</span>
								<span class="value"><?php echo JHTML::_('date', $this->course->offering()->get('publish_up'), $dateFormat, $tz); ?></span>
							</li>
							<li class="info-created">
								<span class="label">Ends</span>
								<span class="value"><?php echo JHTML::_('date', $this->course->offering()->get('publish_down'), $dateFormat, $tz); ?></span>
							</li>
						</ul>
					</div>
				</div> */ ?>
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

				<div id="page_content" class="course_<?php echo $this->active; ?>">
<?php endif; ?>

					<?php
					for ($i=0, $n=count($this->plugins); $i < $n; $i++)
					{
						if ($this->active == $this->plugins[$i]['name'])
						{
							echo $this->sections[$i]['html'];
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