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

use Components\Projects\Models\Orm\Project;

if (!$this->ajax)
{
	$this->css('selector');
}
$min = $this->min;
$max = $this->max;
$minName = \Components\Projects\Helpers\Html::getNumberName($min);
$maxName = \Components\Projects\Helpers\Html::getNumberName($max);

// Spell out requirement
$req = Lang::txt('PLG_PROJECTS_FILES_SELECTOR_CHOOSE') . ' ';
if ($min && $max > $min)
{
	if ($max > 100)
	{
		// Do not say how many
		$req .= '<strong>' . $minName . ' ' . Lang::txt('PLG_PROJECTS_FILES_SELECTOR_OR_MORE') . '</strong>';
	}
	else
	{
		$req .= '<strong>' . $min . '-' . $max . ' ' . Lang::txt('PLG_PROJECTS_FILES_SELECTOR_FILES') . '</strong>';
	}
}
elseif ($min && $min == $max)
{
	$req .= ' <strong>' . $minName . ' ' . Lang::txt('PLG_PROJECTS_FILES_SELECTOR_FILE');
	$req .= $min > 1 ? 's' : '';
	$req .= '</strong>';
}
else
{
	$req .= $max == 1 ? Lang::txt('PLG_PROJECTS_FILES_SELECTOR_COUNT', $max) : Lang::txt('PLG_PROJECTS_FILES_S');
}

if (!empty($allowed))
{
	$req .= ' ' . Lang::txt('PLG_PROJECTS_FILES_SELECTOR_OF_FORMAT');
	$req .= count($allowed) > 1 ? 's - ' : ' - ';
	$x = 1;
	foreach ($allowed as $al)
	{
		$req .= '.' . strtoupper($al);
		$req .= $x == count($allowed) ? '' : ', ';
		$x++;
	}
}
else
{
	$req .= ' ' . Lang::txt('PLG_PROJECTS_FILES_SELECTOR_OF_ANY_TYPE');
}
$req .= ':';

$this->req = $req;
// Get folder array
$subdirOptions = array();
$subdirOptions[] = array('path' => '', 'label' => 'home directory');
if ($this->folders)
{
	foreach ($this->folders as $folder)
	{
		$subdirOptions[] = array('path' => $folder->get('localPath'), 'label' => $folder->get('localPath'));
	}
}

?>
<script src="<?php echo rtrim(Request::base(true), '/'); ?>/core/plugins/projects/files/assets/js/fileselector.js"></script>
<script src="<?php echo rtrim(Request::base(true), '/'); ?>/core/components/com_projects/site/assets/js/projects.js"></script>
<div id="abox-content">
	<h3>
		<?php echo Lang::txt('PLG_PROJECTS_FILES_SELECTOR'); ?>
		<span class="abox-controls">
			<a class="btn btn-success active" id="b-filesave"><?php echo Lang::txt('PLG_PROJECTS_FILES_SELECTOR_SAVE_SELECTION'); ?></a>
			<?php if ($this->ajax) { ?>
				<a class="btn btn-cancel" id="cancel-action"><?php echo Lang::txt('JCANCEL'); ?></a>
			<?php } ?>
		</span>
	</h3>
	<?php echo $this->loadTemplate('selectform'); ?>
	<?php if (isset($this->publication)): ?>
	<form id="upload-form" class="upload-form" method="post" enctype="multipart/form-data" action="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>">
		<fieldset>
			<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />
			<input type="hidden" name="pid" value="<?php echo $this->publication->id; ?>" />
			<input type="hidden" name="vid" value="<?php echo $this->publication->version_id; ?>" />
			<input type="hidden" name="alias" value="<?php echo $this->model->get('alias'); ?>" />
			<input type="hidden" name="active" value="files" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="json" value="1" />
			<input type="hidden" name="ajax" value="1" />
			<input type="hidden" name="no_html" value="1" />
			<?php if ($this->model->isProvisioned()) { ?>
				<input type="hidden" name="provisioned" id="provisioned" value="1" />
				<input type="hidden" name="id" value="<?php echo $this->publication->id; ?>" />
				<input type="hidden" name="task" value="submit" />
				<input type="hidden" name="option" value="com_publications" />
			<?php } else { ?>
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
			<?php } ?>
		</fieldset>
		<div id="status-box"></div>

		<div id="quick-upload" class="quick-uploader">
			<p><?php echo Lang::txt('PLG_PROJECTS_FILES_SELECTOR_NEED_ADD_FILES'); ?> <?php echo Lang::txt('PLG_PROJECTS_FILES_SELECTOR_QUICK_UPLOAD'); ?>:</p>

			<label for="uploader">
				<input name="upload[]" type="file" id="uploader" multiple="multiple" />
			</label>

			<?php if (count($subdirOptions) > 1) { ?>
				<label for="subdir">
					<?php echo Lang::txt('PLG_PROJECTS_FILES_UPLOAD_INTO_SUBDIR'); ?>
					<select name="subdir" id="subdir">
						<?php foreach ($subdirOptions as $sd) { ?>
							<option value="<?php echo $sd['path']; ?>"><?php echo $sd['label']; ?></options>
						<?php } ?>
					</select>
				</label>
			<?php } ?>
			<input type="submit" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_UPLOAD'); ?>" class="upload-file" id="upload-file" />
		</div>
	</form>
	<?php endif; ?>
</div>
