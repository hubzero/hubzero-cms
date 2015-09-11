<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$subdirlink    = $this->subdir ? '&amp;subdir=' . urlencode($this->subdir) : '';

$remoteControl = false;
if (!empty($this->file))
{
	$remoteControl = $this->file->get('converted') ? true : false;
}

?>
<div id="abox-content">
<h3><?php echo $remoteControl ? Lang::txt('PLG_PROJECTS_FILES_UNSHARE_PROJECT_FILES') : Lang::txt('PLG_PROJECTS_FILES_SHARE_PROJECT_FILES'); ?></h3>
<?php
// Display error or success message
if ($this->getError()) {
	echo ('<p class="witherror">' . $this->getError() . '</p>');
}
else
{ ?>
<form id="hubForm-ajax" method="post" class="" action="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->model->get('id')); ?>">
	<p class="notice">
	<?php echo $remoteControl ? Lang::txt('PLG_PROJECTS_FILES_UNSHARE_FILES_CONFIRM') : Lang::txt('PLG_PROJECTS_FILES_SHARE_FILES_CONFIRM');  ?>
	</p>

	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
		<input type="hidden" name="action" value="shareit" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="active" value="files" />
		<input type="hidden" name="repo" value="<?php echo $this->repo->get('name'); ?>" />
		<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="service" value="<?php echo $this->service; ?>" />
		<p class="send_to"><span class="<?php echo $remoteControl ? 'send_to_local' : 'send_to_remote'; ?>"><span>&nbsp;</span></span></p>
		<ul class="sample">
			<?php
				// Display list item with file data
				$this->view('default', 'selected')
				     ->set('skip', false)
				     ->set('file', $this->file)
				     ->set('action', 'share')
				     ->set('multi', 'multi')
				     ->display();
			?>
		</ul>
		<?php if ($remoteControl)
		{
			$ext = $this->file->get('ext');
			if ($this->file->get('originalPath'))
			{
				$ext = \Components\Projects\Helpers\Google::getImportExt($this->file->get('originalPath'));
			}

			$formats = \Components\Projects\Helpers\Google::getGoogleConversionFormat($this->file->get('mimeType'), false, false, true, $ext);
			$first = isset($formats[$ext]) ? 0 : 1;

			if (!empty($formats))
			{
			?>
				<h4><?php echo Lang::txt('PLG_PROJECTS_FILES_SHARING_CHOOSE_CONVERSION_FORMAT'); ?></h4>
				<div class="sharing-option-extra">
			<?php
				$i = 0;
				foreach ($formats as $key => $value)
				{
			?>
				<label <?php if ($ext == $key) { echo 'class="original-format"'; } ?> >
					<input type="radio" name="format" value="<?php echo $key; ?>" <?php if (($first && $i == 0) || $ext == $key) { echo 'checked="checked"'; } ?> />
					<?php echo $value; ?> <?php if ($ext == $key) { echo '<span class="hint mini rightfloat"> [original format]</span>'; } ?>
				</label>
			<?php
				$i++;
				}
			?>
				</div>
			<?php
			}
		}
?>
		<p class="submitarea">
			<?php echo $this->file->get('type') == 'folder'
				? '<input type="hidden" name="folder" value="' . $this->file->get('name') . '" />'
				: '<input type="hidden" name="asset" value="' . $this->file->get('name') . '" />'; ?>
			<input type="submit" value="<?php echo $remoteControl ? Lang::txt('PLG_PROJECTS_FILES_ACTION_UNSHARE') : Lang::txt('PLG_PROJECTS_FILES_ACTION_SHARE'); ?>" id="submit-ajaxform" class="btn" />
			<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_CANCEL'); ?>" />
		</p>
	</fieldset>
</form>
<?php } ?>
</div>