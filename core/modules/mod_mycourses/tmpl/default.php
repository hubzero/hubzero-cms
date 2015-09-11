<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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