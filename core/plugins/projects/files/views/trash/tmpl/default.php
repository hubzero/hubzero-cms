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

?>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_FILES_DELETED_FILES'); ?></h3>

<form id="hubForm-ajax" method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->model->get('id')); ?>">
	<fieldset >
<?php if (empty($this->files)) { ?>
	<p class="warning"><?php echo Lang::txt('PLG_PROJECTS_FILES_TRASH_EMPTY'); ?></p>
<?php } else { ?>
	<div class="wrapper">
		<table id="filelist" class="listing">
			<thead>
				<tr>
					<th><?php echo Lang::txt('PLG_PROJECTS_FILES_FILE'); ?></th>
					<th><?php echo Lang::txt('PLG_PROJECTS_FILES_DELETED'); ?></th>
					<th><?php echo Lang::txt('PLG_PROJECTS_FILES_OPTIONS'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->files as $filename => $file) {

					$dirname = dirname($filename);
				?>
				<tr class="mini">
					<td>
						<span class="icon-image"></span>
						<span><?php echo basename($filename); ?></span>
						<?php if ($dirname != '.') { ?>
						<span class="faded block ipadded">
							<span class="icon-folder"></span>
							<?php echo $dirname; ?></span>
						<?php } ?>
					</td>
					<td class="faded">
						<?php echo \Components\Projects\Helpers\Html::formatTime($file['date'], true, true); ?>
						<span class="block"><?php echo $file['author']; ?></span>
					</td>
					<td><a href="<?php echo Route::url($this->url . '&action=restore&asset=' . urlencode($filename) . '&hash=' . $file['hash']);  ?>"><?php echo Lang::txt('PLG_PROJECTS_FILES_RESTORE'); ?></a></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
		<p class="submitarea">
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_CLOSE'); ?>" />
			<?php } else {  ?>
			<a id="cancel-action" class="btn btn-cancel" href="<?php echo $this->url . '?a=1' . $subdirlink; ?>"><?php echo Lang::txt('PLG_PROJECTS_FILES_GO_BACK'); ?></a>
			<?php } ?>
		</p>

<?php } ?>
	</fieldset>
</form>
</div>