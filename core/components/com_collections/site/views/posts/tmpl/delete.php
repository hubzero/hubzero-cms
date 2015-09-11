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
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="<?php echo Route::url('index.php?option='.$this->option.'&gid='.$this->group->cn.'&active=blog&task=delete&entry='.$this->entry->id); ?>" method="post" id="hubForm">
		<div class="explaination">
<?php if ($this->authorized) { ?>
			<p><a class="add btn" href="<?php echo Route::url('index.php?option='.$this->option.'&gid='.$this->group->cn.'&active=blog&task=new'); ?>"><?php echo Lang::txt('New entry'); ?></a></p>
<?php } ?>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE_HEADER'); ?></legend>

	 		<p class="warning"><?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE_WARNING',$this->entry->title); ?></p>

			<label>
				<input type="checkbox" class="option" name="confirmdel" value="1" />
				<?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE_CONFIRM'); ?>
			</label>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="gid" value="<?php echo $this->group->cn; ?>" />
		<input type="hidden" name="process" value="1" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="active" value="blog" />
		<input type="hidden" name="task" value="delete" />
		<input type="hidden" name="entry" value="<?php echo $this->entry->id; ?>" />

		<?php echo Html::input('token'); ?>

		<p class="submit">
			<input type="submit" value="<?php echo Lang::txt('PLG_GROUPS_BLOG_DELETE'); ?>" />
			<a href="<?php echo Route::url('index.php?option='.$this->option.'&gid='.$this->group->cn.'&active=blog&scope='.Date::of($this->entry->publish_up)->toLocal('Y').'/'.Date::of($this->entry->publish_up)->toLocal('m').'/'.$this->entry->alias); ?>"><?php echo Lang::txt('Cancel'); ?></a>
		</p>
	</form>
