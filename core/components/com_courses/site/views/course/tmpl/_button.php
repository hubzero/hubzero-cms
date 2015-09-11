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
?>
	<div class="btn-group-wrap">
		<div class="btn-group dropdown">
			<?php if ($this->course->isManager()) { ?>
				<a class="btn" href="<?php echo Route::url($this->offering->link('enter')); ?>"><?php echo $this->escape(stripslashes($this->offering->get('title'))); ?></a>
			<?php } else { ?>
				<a class="btn" href="<?php echo Route::url($this->offering->link('enter')); ?>"><?php echo $this->escape(stripslashes($this->section->get('title'))); ?></a>
			<?php } ?>
			<span class="btn dropdown-toggle"></span>
			<ul class="dropdown-menu">
			<?php
			foreach ($this->sections as $key => $section)
			{
				// Skip the first one
				if ($key == 0 && $this->course->isStudent())
				{
					continue;
				}
				// Set the section
				$this->offering->section($section);
				?>
				<li>
					<a href="<?php echo Route::url($this->offering->link()); ?>">
						<?php echo $this->escape(stripslashes($section->get('title'))); ?>
					</a>
				</li>
				<?php
			}
			?>
			</ul>
			<div class="clear"></div>
		</div><!-- /btn-group -->
	</div>