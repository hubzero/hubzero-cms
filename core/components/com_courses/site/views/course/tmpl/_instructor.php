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

$name = $this->escape(stripslashes($this->instructor->get('name')));
?>
<div class="course-instructor">
	<p class="course-instructor-photo">
		<?php if ($this->instructor->get('public')) { ?>
			<a href="<?php echo Route::url($this->instructor->link()); ?>">
				<img src="<?php echo $this->instructor->picture(); ?>" alt="<?php echo $name; ?>" />
			</a>
		<?php } else { ?>
			<img src="<?php echo $this->instructor->picture(); ?>" alt="<?php echo $name; ?>" />
		<?php } ?>
	</p>

	<div class="course-instructor-content cf">
		<h4>
			<?php if (in_array($this->instructor->get('access'), User::getAuthorisedViewLevels())) { ?>
				<a href="<?php echo Route::url($this->instructor->link()); ?>">
					<?php echo $name; ?>
				</a>
			<?php } else { ?>
				<?php echo $name; ?>
			<?php } ?>
		</h4>
		<p class="course-instructor-org">
			<?php echo $this->escape(stripslashes($this->instructor->get('organization', '--'))); ?>
		</p>
	</div><!-- / .course-instructor-content cf -->

	<?php
	$params = new \Hubzero\Config\Registry($this->instructor->get('params'));
	if ($params->get('access_bio') == 0 // public
	 || ($params->get('access_bio') == 1 && !User::usGuest()) // registered members
	) {
	?>
	<div class="course-instructor-bio">
		<?php if ($bio = $this->instructor->get('bio')) { ?>
			<?php echo $bio; ?>
		<?php } else { ?>
			<em><?php echo Lang::txt('COM_COURSES_INSTRUCTOR_NO_BIO'); ?></em>
		<?php } ?>
	</div>
	<?php } ?>
</div><!-- / .course-instructor -->