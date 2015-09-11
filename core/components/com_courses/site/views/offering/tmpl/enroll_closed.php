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

// No direct access
defined('_HZEXEC_') or die();

$this->css('offering');
?>
<header id="content-header"<?php if ($this->course->get('logo')) { echo ' class="with-identity"'; } ?>>
	<h2>
		<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
	</h2>

	<?php if ($logo = $this->course->logo('url')) { ?>
		<p class="course-identity">
			<img src="<?php echo $logo; ?>" alt="<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>" />
		</p>
	<?php } ?>

	<p id="page_identity">
		<a class="prev" href="<?php echo Route::url($this->course->link()); ?>">
			<?php echo Lang::txt('COM_COURSES_COURSE_OVERVIEW'); ?>
		</a>
		<strong>
			<?php echo Lang::txt('COM_COURSES_OFFERING'); ?>:
		</strong>
		<span>
			<?php echo $this->escape(stripslashes($this->course->offering()->get('title'))); ?>
		</span>
		<strong>
			<?php echo Lang::txt('COM_COURSES_SECTION'); ?>:
		</strong>
		<span>
			<?php echo $this->escape(stripslashes($this->course->offering()->section()->get('title'))); ?>
		</span>
	</p>
</header><!-- #content-header -->

<section class="main section enroll-closed">
	<div class="section-inner">
		<div id="offering-introduction">
			<div class="instructions">
				<p class="warning"><?php echo Lang::txt('COM_COURSES_ENROLLMENT_CLOSED'); ?></p>
			</div><!-- / .instructions -->
			<div class="questions">
				<p><strong><?php echo Lang::txt('COM_COURSES_I_SHOULD_HAVE_ACCESS'); ?></strong></p>
				<p><?php echo Lang::txt('COM_COURSES_I_SHOULD_HAVE_ACCESS_EXPLANATION', Route::url('index.php?option=com_support')); ?></p>
				<p><strong><?php echo Lang::txt('COM_COURSES_WHERE_CAN_I_FIND_THER_COURSES'); ?></strong></p>
				<p><?php echo Lang::txt('COM_COURSES_WHERE_CAN_I_FIND_THER_COURSES_EXPLANATIONS', Route::url('index.php?option=' . $this->option . '&controller=courses&task=browse')); ?></p>
			</div><!-- / .questions -->
		</div><!-- / #offering-introduction -->
	</div>
</section><!-- /.main section -->