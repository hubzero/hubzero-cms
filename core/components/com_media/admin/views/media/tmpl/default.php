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

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_MEDIA'));
if (User::authorise('core.admin', 'com_media'))
{
	Toolbar::preferences($this->option, 450, 800, 'JToolbar_Options', '', 'window.location.reload()');
	Toolbar::spacer();
}
if (User::authorise('core.delete', 'com_media'))
{
	Toolbar::deleteList('', 'delete');
	Toolbar::spacer();
}
Toolbar::help('media');
?>
<script type="text/javascript">
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	submitform(pressbutton);
}
</script>
<table width="100%">
	<tr valign="top">
		<td class="media-tree">
			<fieldset id="treeview">
				<legend><?php echo Lang::txt('COM_MEDIA_FOLDERS'); ?></legend>
				<div id="media-tree_tree">
					<?php echo $this->loadTemplate('folders'); ?>
				</div>
			</fieldset>
		</td>
		<td class="media-browser">
			<?php if ((User::authorise('core.create', 'com_media')) and $this->require_ftp): ?>
				<form action="<?php echo Route::url('index.php?option=com_media&task=ftpValidate'); ?>" name="ftpForm" id="ftpForm" method="post">
					<fieldset title="<?php echo Lang::txt('COM_MEDIA_DESCFTPTITLE'); ?>">
						<legend><?php echo Lang::txt('COM_MEDIA_DESCFTPTITLE'); ?></legend>
						<?php echo Lang::txt('COM_MEDIA_DESCFTP'); ?>
						<label for="username"><?php echo Lang::txt('JGLOBAL_USERNAME'); ?></label>
						<input type="text" id="username" name="username" class="inputbox" size="70" value="" />

						<label for="password"><?php echo Lang::txt('JGLOBAL_PASSWORD'); ?></label>
						<input type="password" id="password" name="password" class="inputbox" size="70" value="" />
					</fieldset>
				</form>
			<?php endif; ?>

			<form action="<?php echo Route::url('index.php?option=com_media&' . Session::getFormToken() . '=1', true, true); ?>" name="adminForm" id="mediamanager-form" method="post" enctype="multipart/form-data">
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="token" value="<?php echo Session::getFormToken(); ?>" />
				<input type="hidden" name="folder" id="folder" value="<?php echo $this->folder; ?>" />
			</form>

			<form action="<?php echo Route::url('index.php?option=com_media&task=folder.create&tmpl=' . Request::getCmd('tmpl', 'index'));?>" name="folderForm" id="folderForm" method="post">
				<fieldset id="folderview">
					<legend><?php echo Lang::txt('COM_MEDIA_FILES'); ?></legend>
					<div class="view">
						<iframe src="<?php echo Route::url('index.php?option=com_media&controller=medialist&view=medialist&tmpl=component&folder=' . $this->folder); ?>" id="folderframe" name="folderframe" width="100%" marginwidth="0" marginheight="0" scrolling="auto"></iframe>
					</div>
				</fieldset>
			</form>
		</td>
	</tr>
</table>
<?php echo Html::input('token'); ?>
