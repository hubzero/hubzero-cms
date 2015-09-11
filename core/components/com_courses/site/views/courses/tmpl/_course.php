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

switch ($this->count)
{
	case 0: $cls = 'span4';  break;
	case 1: $cls = 'span4'; break;
	case 2: $cls = 'span4 omega'; break;
}
?>
<div class="col <?php echo $cls; ?>">
	<div class="course">
		<a href="<?php echo Route::url($this->course->link()); ?>">
			<div class="course-details">
				<div class="course-identity">
					<?php if ($logo = $this->course->logo('url')) { ?>
						<img src="<?php echo Route::url($logo); ?>" alt="<?php echo $this->escape($this->course->get('title')); ?>" />
					<?php } else { ?>
						<span></span>
					<?php } ?>

					<?php if ($this->course->get('rating', 0) > 4) { ?>
					<div>
						<strong><?php echo Lang::txt('COM_COURSES_TOP_RATED_COURSE'); ?></strong> <span class="rating">&#x272D;&#x272D;&#x272D;&#x272D;&#x272D;</span>
					</div>
					<?php } else if ($this->course->get('popularity', 0) > 7) { ?>
					<div>
						<strong><?php echo Lang::txt('COM_COURSES_POPULAR_COURSE'); ?></strong> <span class="popularity">&#xf091;</span>
					</div>
					<?php } ?>
				</div>
				<h3 class="course-title">
					<?php echo $this->escape($this->course->get('title')); ?>
				</h3>
			<?php if ($this->course->get('blurb')) { ?>
				<p class="course-description">
					<?php echo \Hubzero\Utility\String::truncate($this->escape($this->course->get('blurb')), 130); ?>
				</p>
			<?php } ?>
			</div>
		</a>
	</div>
</div>