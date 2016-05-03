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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_MEMBERS_QUOTAS_IMPORT'), 'user');
?>

<?php
	$this->view('_submenu')
	     ->display();
?>

<div id="item-form">
	<?php if ($this->getError()) : ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php endif; ?>

	<div class="grid">
		<div class="col span8">
			<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm">
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_MEMBERS_QUOTA_IMPORT_LEGEND'); ?></span></legend>

					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
					<input type="hidden" name="task" value="processImport" />

					<div class="input-wrap">
						<label for="conf_text"><?php echo Lang::txt('COM_MEMBERS_QUOTA_CONF_TEXT'); ?>:</label>
						<p class="info conf-text-note"><?php echo Lang::txt('COM_MEMBERS_QUOTA_CONF_TEXT_NOTE'); ?></p>
						<textarea name="conf_text" id="conf_text" cols="30" rows="10"></textarea>
					</div>
					<div class="input-wrap">
						<label for="overwrite_existing"><?php echo Lang::txt('COM_MEMBERS_QUOTA_OVERWRITE_EXISTING'); ?></label>
						<input type="checkbox" name="overwrite_existing" id="overwrite_existing" value="1" />
					</div>
					<p class="submit-button">
						<input class="btn btn-primary" type="submit" value="<?php echo Lang::txt('COM_MEMBERS_QUOTA_IMPORT_SUBMIT'); ?>" />
					</p>
				</fieldset>
				<?php echo Html::input('token'); ?>
			</form>
		</div>
		<div class="col span4">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_MEMBERS_QUOTA_IMPORT_MISSING_USERS'); ?>:</th>
						<td>
							<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post">
								<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
								<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
								<input type="hidden" name="task" value="importMissing" />
								<input type="submit" value="<?php echo Lang::txt('COM_MEMBERS_QUOTA_IMPORT_SUBMIT'); ?>" />
							</form>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<p>
								<?php echo Lang::txt('COM_MEMBERS_QUOTA_MISSING_USERS_IMPORT_DESCRIPTION'); ?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>