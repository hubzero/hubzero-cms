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

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="group btn" href="<?php echo Route::url('index.php?option='.$this->option); ?>">
					<?php echo Lang::txt('COM_GROUPS_ACTION_BACK_TO_ALL_GROUPS'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<div class="section-inner">
		<?php
			foreach ($this->notifications as $notification)
			{
				echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
			}
		?>
		<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
			<div class="explaination">
				<p class="info"><?php echo Lang::txt('COM_GROUPS_JOIN_HELP'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_GROUPS_JOIN_SECTION_TITLE'); ?></legend>

				<?php if ($this->group->get('restrict_msg')) { ?>
					<p class="warning"><?php echo Lang::txt('NOTE') . ': ' . $this->escape(stripslashes($this->group->get('restrict_msg'))); ?></p>
				<?php } ?>

				<label for="reason">
					<?php echo Lang::txt('COM_GROUPS_JOIN_REASON'); ?>
					<textarea name="reason" id="reason" rows="10" cols="50"></textarea>
				</label>
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="membership" />
				<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
				<input type="hidden" name="task" value="dorequest" />
				<?php echo Html::input('token'); ?>
			</fieldset>
			<div class="clear"></div>

			<p class="submit">
				<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_GROUPS_JOIN_BTN_TEXT'); ?>" />
			</p>
		</form>
	</div>
</section><!-- / .main section -->
