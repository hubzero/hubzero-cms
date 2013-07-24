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

	<div class="guide-instructions">
		<h2><?php echo JText::_('Quick guide'); ?></h2>
		<p class="guide-dismiss">
			<?php echo JText::_('Click anywhere to dismiss this guide and get started!'); ?>
		</p>
	</div>
	
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
							<strong><?php echo $cat['title']; ?></strong> <span><?php echo JText::_('PLG_COURSES_' . $cat['name'] . '_BLURB'); ?></span>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
		</div>
		<div class="col span-half omega">
			<p><?php echo JText::_('Over here you will find other stuff liek a video or something.'); ?></p>
		</div>
	</div>
	
	</div><!-- / .guide-content -->
</div><!-- / .guide-wrap -->