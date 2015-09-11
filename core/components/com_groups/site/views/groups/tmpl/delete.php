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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="group btn" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn')); ?>">
					<?php echo Lang::txt('COM_GROUPS_ACTION_BACK_TO_GROUP'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<?php foreach ($this->notifications as $notification) : ?>
		<p class="<?php echo $notification['type']; ?>">
			<?php echo $notification['message']; ?>
		</p>
	<?php endforeach; ?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=delete'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p><strong><?php echo Lang::txt('COM_GROUPS_DELETE_ARE_YOU_SURE_TITLE'); ?></strong></p>
			<p><?php echo Lang::txt('COM_GROUPS_DELETE_ARE_YOU_SURE_DESC'); ?></p>

			<p><strong><?php echo Lang::txt('COM_GROUPS_DELETE_ALTERNATIVE_TITLE'); ?></strong></p>
			<p><?php echo Lang::txt('COM_GROUPS_DELETE_ALTERNATIVE_DESC'); ?></p>
			<p>
				<a class="config btn" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&task=edit'); ?>">
					<?php echo Lang::txt('COM_GROUPS_DELETE_ALTERNATIVE_BTN_TEXT'); ?>
				</a>
			</p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_GROUPS_DELETE_CONFIRM_BOX_HEADING'); ?></legend>

	 		<p class="warning"><?php echo Lang::txt('COM_GROUPS_DELETE_CONFIRM_BOX_WARNING', $this->group->get('description')) . '<br /><br />' . $this->log; ?></p>

			<label for="msg">
				<?php echo Lang::txt('COM_GROUPS_DELETE_CONFIRM_BOX_MESSAGE_LABEL'); ?>
				<textarea name="msg" id="msg" rows="12" cols="50"><?php echo htmlentities($this->msg); ?></textarea>
			</label>

			<label for="confirmdel">
				<input type="checkbox" class="option" name="confirmdel" id="confirmdel" value="1" />
				<?php echo Lang::txt('COM_GROUPS_DELETE_CONFIRM_CONFIRM'); ?>
			</label>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
		<input type="hidden" name="task" value="dodelete" />

		<p class="submit">
			<input class="btn btn-danger" type="submit" value="<?php echo Lang::txt('DELETE'); ?>" />
		</p>
	</form>
</section><!-- / .main section -->
