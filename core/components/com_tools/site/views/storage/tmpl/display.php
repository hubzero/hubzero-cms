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

// No direct access.
defined('_HZEXEC_') or die();

$this->css('storage.css');
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_TOOLS_STORAGE'); ?></h2>

	<div id="content-header-extra">
		<?php echo $this->monitor; ?>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<?php if ($this->exceeded) { ?>
		<p class="warning"><?php echo Lang::txt('COM_TOOLS_ERROR_STORAGE_EXCEEDED'); ?></p>
	<?php } ?>

	<?php if ($this->output) { ?>
		<p class="passed"><?php echo $this->output; ?></p>
	<?php } else if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>

	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=storage'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p class="help">
				<strong><?php echo Lang::txt('COM_TOOLS_STORAGE_WHAT_DOES_PURGE_DO'); ?></strong><br />
				<?php echo Lang::txt('COM_TOOLS_STORAGE_WHAT_PURGE_DOES'); ?>
			</p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_TOOLS_STORAGE_AUTOMATIC'); ?></legend>
			<div class="grid">
				<div class="col span6">
					<label>
						<?php echo Lang::txt('COM_TOOLS_STORAGE_CLEAN_UP_DISCK_SPACE'); ?>
						<select name="degree">
							<option value="default"><?php echo Lang::txt('COM_TOOLS_STORAGE_OPT_MINIMALLY'); ?></option>
							<option value="olderthan1"><?php echo Lang::txt('COM_TOOLS_STORAGE_OPT_OLDER_DAY'); ?></option>
							<option value="olderthan7"><?php echo Lang::txt('COM_TOOLS_STORAGE_OPT_OLDER_WEEK'); ?></option>
							<option value="olderthan30"><?php echo Lang::txt('COM_TOOLS_STORAGE_OPT_OLDER_MONTH'); ?></option>
							<option value="all"><?php echo Lang::txt('COM_TOOLS_STORAGE_OPT_ALL'); ?></option>
						</select>
					</label>
				</div>
				<div class="col span6 omega">
					<label>
						<br />
						<input type="submit" class="option" name="action" value="Purge" />
					</label>
				</div>
			</div>
			<p class="hint"><?php echo Lang::txt('COM_TOOLS_STORAGE_AUTOMATIC_HINT'); ?></p>
		</fieldset>
		<fieldset>
			<legend><?php echo Lang::txt('COM_TOOLS_STORAGE_MANUAL'); ?></legend>
			<div class="filebrowser field-wrap">
				<?php echo Lang::txt('COM_TOOLS_STORAGE_BROWSE_STORAGE'); ?>
				<iframe src="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=filelist&tmpl=component'); ?>" name="filer" id="filer" width="98%" height="500" border="0" frameborder="0"></iframe>
			</div>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="purge" />

			<?php echo Html::input('token'); ?>
		</fieldset><div class="clear"></div>
	</form>
</section><!-- / .main section -->
