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
<li class="collections-entry">
	<p class="title">
		<a href="<?php echo Route::url($this->entry->link()); ?>">
			<?php if ($title = $this->entry->get('title')) { ?>
				<?php echo $this->escape(stripslashes($title)); ?>
			<?php } else { ?>
				<?php echo Lang::txt('PLG_TAGS_COLLECTIONS_POST_NUM', $this->entry->get('id')); ?>
			<?php } ?>
		</a>
	</p>
	<p class="details">
		<?php echo Lang::txt('PLG_TAGS_COLLECTIONS'); ?>
		<span>|</span>
		<?php echo $this->entry->created('date'); ?>
		<span>|</span>
		<?php echo Lang::txt('PLG_TAGS_COLLECTIONS_POSTED_BY', '<cite><a href="' . Route::url('index.php?option=com_members&id=' . $this->entry->get('created_by')) . '">' . $this->escape(stripslashes($this->entry->creator('name'))) . '</a></cite>'); ?>
	</p>
	<?php if ($content = $this->entry->description('clean', 200)) { ?>
		<p><?php echo $content; ?></p>
	<?php } ?>
	<p class="href">
		<?php echo rtrim(Request::base(), '/') . '/' . ltrim(Route::url($this->entry->link()), '/'); ?>
	</p>
</li>