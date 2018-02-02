<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2017 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2017 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Utility\Arr;

Toolbar::title(Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY') . ': ' . Arr::getValue($this->config, 'name', ''), 'packages');

Toolbar::cancel();

// Determine status & options
$status = '';

?>
</script>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=save'); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_INSTALLER_PACKAGES_BASIC_INFO'); ?></span></legend>

				<div class="input-wrap">
					<label for="name"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_NAME'); ?></label>
					<input name="name" type="text" value="<?php echo Arr::getValue($this->config, 'name', ''); ?>"></input>
				</div>
				<div class="input-wrap">
					<label for="alias"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_ALIAS'); ?></label>
					<input name="alias" type="text" value="<?php echo isset($this->alias) ? $this->alias : ''; ?>"></input>
				</div>
				<div class="input-wrap">
					<label for="description"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_DESCRIPTION'); ?></label>
					<input name="description" type="text" value="<?php echo Arr::getValue($this->config, 'description', ''); ?>"></input>
				</div>
				<div class="input-wrap">
					<label for="url"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_URL'); ?></label>
					<input name="url" type="text" value="<?php echo Arr::getValue($this->config, 'url', ''); ?>"></input>
				</div>
				<div class="input-wrap">
					<label for="type"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_TYPE'); ?></label>
					<select name="type">
						<option value="github" selected="<?php echo Arr::getValue($this->config, 'type', '') == 'github' ? 'true' : ''; ?>">Github</option>
						<option value="gitlab" selected="<?php echo Arr::getValue($this->config, 'type', '') == 'gitlab' ? 'true' : ''; ?>">Gitlab</option>
					</select>
				</div>

				<input type="submit" value="<?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_UPDATE'); ?>">

			</fieldset>
		<p class="warning">
			<a class="button" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&alias=' . $this->alias . '&task=remove'); ?>">Remove Repository</a>
		</p>
		</div>
	</div>
	<input type="hidden" name="oldAlias" value="<?php echo $this->alias; ?>" />
	<input type="hidden" name="isNew" value="<?php echo $this->isNew ? "true" : "false" ?>" />

	<?php echo Html::input('token'); ?>
</form>
