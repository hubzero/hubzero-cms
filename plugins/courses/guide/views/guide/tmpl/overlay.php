<?php 
defined('_JEXEC') or die('Restricted access');

JPluginHelper::importPlugin('courses');
$plugins = JDispatcher::getInstance()->trigger('onCourseAreas', array());

$course_plugin_access = array();
foreach ($plugins as $plugin)
{
	$course_plugin_access[$plugin['name']] = $plugin['default_access'];
}
?>
<div id="guide-overlay" class="guide-wrap" data-action="<?php echo JRoute::_('index.php?option=' . $this->option . '&gid=' . $this->course->get('alias') . '&offering=' . $this->offering->get('alias') . ($this->offering->section()->get('alias') != '__default' ? ':' . $this->offering->section()->get('alias') : '') . '&active=' . $this->plugin . '&unit=mark'); ?>">
	<div class="guide-content">

		<div class="grid">
			<div class="col span-half">
				<div class="guide-nav">
					<ul>
						<?php
						foreach ($plugins as $k => $cat)
						{
							//do we want to show category in menu?
							if ($cat['display_menu_tab'])
							{
								if (!$this->course->offering()->access('manage', 'section') 
								 && isset($course_plugin_access[$cat['name']]) 
								 && $course_plugin_access[$cat['name']] == 'managers')
								{
									continue;
								}
							}
							?>
							<li>
								<strong class="<?php echo $cat['name']; ?>"><?php echo $cat['title']; ?></strong> <span><?php echo JText::_('PLG_COURSES_' . $cat['name'] . '_BLURB'); ?></span>
							</li>
							<?php
						}
						?>
					</ul>
				</div>
			</div>
			<div class="col span-half omega">
				<div class="guide-about">
					<h3><?php echo JText::_('Welcome to the course!'); ?></h3>
					<p><?php echo JText::_('We\'ve tried to organize things to group related content and make it easier to find what you need. Feel free to explore the various menu options.'); ?></p>
					<p><?php echo JText::sprintf('You can always get back to the %s by clicking the link found under the title of this course.', '<a href="' . JRoute::_('index.php?option=com_courses&gid=' . $this->course->get('alias')) . '">Course overview</a>'); ?></p>
					<p class="guide-dismiss">
						<?php echo JText::_('Click anywhere to dismiss this guide and get started!'); ?>
					</p>
				</div>

				<div class="guide-onemorething">
					<p><?php echo JText::_('Oh, and one more thing:'); ?></p>
					<p class="guide-luck"><?php echo JText::_('Good Luck!'); ?></p>
				</div>
			</div>
		</div>

	</div><!-- / .guide-content -->
</div><!-- / .guide-wrap -->