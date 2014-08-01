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
$no_html = JRequest::getInt( 'no_html', 0 );
$tmpl    = JRequest::getWord('tmpl', false);
$sparams = new JRegistry($this->course->offering()->section()->get('params'));

if (!$no_html && $tmpl != 'component') :
	$this->css('offering.css')
	     ->js('courses.offering.js');

	$src = $this->course->logo();
	if ($logo = $this->course->offering()->section()->logo())
	{
		$src = $logo;
	}
	else if ($logo = $this->course->offering()->logo())
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
			<img src="<?php echo $src; ?>" alt="<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>" />
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
						//instantiate objects
						$juser = JFactory::getUser();

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
								$title  = $cat['title'];
								$cls    = $cat['name'];
								if (!isset($cat['icon']))
								{
									$cat['icon'] = 'f0a1';
								}

								//get the menu items access level
								//$access = $access_levels[$cat['name']];

								//menu link
								$link = JRoute::_($this->course->offering()->link() . '&active=' . $active);

								//Are we on the overview tab with sub course pages?
								if ($cat['name'] == 'outline') // && count($this->pages) > 0
								{
									$true_active_tab = JRequest::getVar('active', 'outline');
									$li_cls = ($true_active_tab != $this->active) ? '' : $li_cls;

									if (!$this->course->offering()->access('view') && !$sparams->get('preview', 0))
									{
										$menu_item  = '<li class="protected course-overview-tab"><span class="outline">' . JText::_('COM_COURSES_OUTLINE') . '</span>';
									}
									else
									{
										$menu_item  = "<li class=\"{$li_cls} course-overview-tab\">";
										$menu_item .= '<a class="outline" href="' . $link . '" data-icon="&#x' . $cat['icon'] . ';" data-title="' . JText::_('COM_COURSES_OUTLINE') . '">' . JText::_('COM_COURSES_OUTLINE') . '</a>';
									}
									$menu_item .= '</li>';
								}
								else
								{
									if (!$this->course->offering()->access('view') && !$sparams->get('preview', 0))
									{
										$menu_item  = '<li class="protected members-only course-' . $cls . '-tab" data-title="' . JText::_('COM_COURSES_RESTRICTED_PAGE') . '">';
										$menu_item .= '<span class="' . $cls . '" data-icon="&#x' . $cat['icon'] . '">' . $title . '</span>';
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
										$menu_item .= '<a class="' . $cls . '" data-icon="&#x' . $cat['icon'] . '" data-title="' . $this->escape(stripslashes($title)) . '" href="' . $link . '">' . $this->escape($title) . '</a>';
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