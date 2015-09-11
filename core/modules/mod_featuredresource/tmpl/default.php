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

if ($this->getError()) { ?>
	<p class="error"><?php echo Lang::txt('MOD_FEATUREDRESOURCE_MISSING_CLASS'); ?></p>
<?php } else {
	if ($this->row) {
?>
	<div class="<?php echo $this->cls; ?>">
	<?php if (is_file(PATH_APP . $this->thumb)) { ?>
		<p class="featured-img">
			<a href="<?php echo Route::url('index.php?option=com_resources&id=' . $this->id); ?>">
				<img width="50" height="50" src="<?php echo $this->thumb; ?>" alt="" />
			</a>
		</p>
	<?php } ?>
		<p>
			<a href="<?php echo Route::url('index.php?option=com_resources&id=' . $this->id); ?>">
				<?php echo $this->escape(stripslashes($this->row->title)); ?>
			</a>:
		<?php if ($this->row->introtext) { ?>
			<?php echo \Hubzero\Utility\String::truncate($this->escape(strip_tags($this->row->introtext)), $this->txt_length); ?>
		<?php } ?>
		</p>
	</div>
<?php } else { ?>
	<div class="<?php echo $this->cls; ?>">
		<p>
			<?php echo Lang::txt('MOD_FEATUREDRESOURCE_NO_RESULTS'); ?>
		</p>
	</div>
<?php
	}
}
