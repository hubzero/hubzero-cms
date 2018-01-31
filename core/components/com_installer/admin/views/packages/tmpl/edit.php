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

Toolbar::title(Lang::txt('COM_INSTALLER_PACKAGES_PACKAGE') . ': ' . $this->packageName, 'packages');

Toolbar::cancel();

$authors = array();
$packageAuthors = $this->installedPackage->getAuthors();
if ($packageAuthors)
{
	foreach ($packageAuthors as $author)
	{
		$authors[] = $author['name'] . ' &lt' . $author['email'] . '&gt';
	}
}
// Determine status & options
$status = '';

?>
</script>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=install'); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_INSTALLER_PACKAGES_BASIC_INFO'); ?></span></legend>

				<div class="input-wrap">
					<label for="version"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_AVAILABLE_VERSIONS'); ?>:</label>
					<select name='packageVersion'>
						<?php foreach ($this->versions as $version): ?>
						<option value="<?php echo $version->getVersion(); ?>" <?php echo ($this->installedPackage->getVersion() == $version->getVersion()) ? 'selected="true"' : '';?> > <?php echo $version->getFullPrettyVersion(); ?></option>
						<?php endforeach; ?>
					</select>

				</div>
				<input type="submit" value="<?php echo Lang::txt('COM_INSTALLER_PACKAGES_INSTALL_VERSION'); ?>">

			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_INSTALLER_PACKAGES_INSTALLED_VERSION'); ?>:</th>
						<td><?php echo $this->installedPackage->getFullPrettyVersion(); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_INSTALLER_PACKAGES_RELEASE_DATE'); ?>:</th>
						<td><?php echo $this->installedPackage->getReleaseDate()->format("Y-m-d H:i:s"); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_INSTALLER_PACKAGES_TYPE'); ?>:</th>
						<td><?php echo $this->installedPackage->getType(); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_INSTALLER_PACKAGES_AUTHORS'); ?>:</th>
						<td><?php echo implode(', ', $authors); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="packageName" value="<?php echo $this->packageName; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="install" />

	<?php echo Html::input('token'); ?>
</form>
