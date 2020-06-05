<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css();

$total = count($this->courses);
?>
<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : '';?>>
	<?php if ($this->params->get('button_show_all', 1)) { ?>
	<ul class="module-nav">
		<li>
			<a class="icon-browse" href="<?php echo Route::url('index.php?option=com_courses&task=browse'); ?>">
				<?php echo Lang::txt('MOD_MYCOURSES_ALL_COURSES'); ?>
			</a>
		</li>
	</ul>
	<?php } ?>

	<?php if ($this->courses && $total > 0) { ?>
		<ul class="compactlist">
			<?php
			$i = 0;
			foreach ($this->courses as $course)
			{
				if ($i < $this->limit)
				{
					$sfx = '';

					if (isset($course->offering_alias))
					{
						$sfx .= '&offering=' . $course->offering_alias;
					}
					if (isset($course->section_alias) && !$course->is_default)
					{
						$sfx .= ':' . $course->section_alias;
					}
					//$status = $this->getStatus($group);
					?>
					<li class="course">
						<a href="<?php echo Route::url('index.php?option=com_courses&gid=' . $course->alias . $sfx); ?>">
							<?php echo $this->escape(stripslashes($course->title)); ?>
						</a>
						<?php if ($course->section_title) { ?>
							<small>
								<strong><?php echo Lang::txt('MOD_MYCOURSES_SECTION'); ?></strong> <?php echo $this->escape($course->section_title); ?>
							</small>
						<?php } ?>
						<?php
						switch ($course->state)
						{
							case 3: ?><small><?php echo Lang::txt('MOD_MYCOURSES_COURSE_STATE_DRAFT'); ?></small><?php break;
							case 2: ?><small><?php echo Lang::txt('MOD_MYCOURSES_COURSE_STATE_DELETED'); ?></small><?php break;
							case 1: ?><small><?php echo Lang::txt('MOD_MYCOURSES_COURSE_STATE_PUBLISHED'); ?></small><?php break;
							case 0: ?><small><?php echo Lang::txt('MOD_MYCOURSES_COURSE_STATE_UNPUBLISHED'); ?></small><?php break;
						}
						?>
						<span>
							<span class="<?php echo $this->escape($course->role); ?> status"><?php echo $this->escape($course->role); ?></span>
						</span>
					</li>
					<?php
					$i++;
				}
			}
			?>
		</ul>
	<?php } else { ?>
		<p><em><?php echo Lang::txt('MOD_MYCOURSES_NO_RESULTS'); ?></em></p>
	<?php } ?>

	<?php if ($total > $this->limit) { ?>
		<p class="note"><?php echo Lang::txt('MOD_MYCOURSES_YOU_HAVE_MORE', $this->limit, $total, Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=courses')); ?></p>
	<?php } ?>
</div>