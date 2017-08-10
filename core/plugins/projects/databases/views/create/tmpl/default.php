<?php
/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2012-2015 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2012-2015 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License,
 * version 3 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// no direct access
defined('_HZEXEC_') or die();

$this->css();

// Check for errors
if ($this->getError()) {
?> <p class="error"><?php echo $this->getError(); ?></p>
<?php return; }

Document::addScript('/core/plugins/projects/databases/res/dataTables/jquery.dataTables.js');
Document::addStyleSheet('/core/plugins/projects/databases/res/dataTables/jquery.dataTables.css');
Document::addStyleSheet('/core/plugins/projects/databases/res/chosen/chosen.css');
Document::addScript('/core/plugins/projects/databases/res/chosen/chosen.jquery.js');
Document::addStyleSheet('/core/plugins/projects/databases/res/spectrum/spectrum.css');
Document::addScript('/core/plugins/projects/databases/res/spectrum/spectrum.js');

?>
<div id="plg-header">
<h3 class="databases c-header"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&active=databases&alias=' . $this->model->get('alias')); ?>"><?php echo Lang::txt('PLG_PROJECTS_DATABASES'); ?></a> &raquo; <span class="indlist"><?php echo isset($this->db_id) ? Lang::txt('PLG_PROJECTS_DATABASES_UPDATE_DATABASE') : Lang::txt('PLG_PROJECTS_DATA_START'); ?></span></h3>
</div>
<div id="prj-db-step-1" class="prj-db-step">
<?php
	if (count($this->files) > 0 && (!isset($this->db_id)))
	{
?>
	<h3><?php echo Lang::txt('Step 1: Select a file'); ?></h3>
	<form id="prj-db-select-form" method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->model->get('id') . '&active=databases&action=preview_data&raw_op=1')?>">
		<select id="prj-db-select-src" title="<?php echo Lang::txt('Select a CSV file to convert in to a database'); ?>">
		<?php foreach ($this->files as $dir => $files): ?>
			<?php
			if ($dir == '.') {
				$dir = '';
			}
			?>
				<optgroup label="<?php echo $dir?>">
			<?php foreach ($files as $file): ?>
				<option data-dir="<?php echo $dir?>" data-hash="<?php echo $file['hash']?>" data-date="<?php echo $file['date']?>" value="<?php echo $file['name']?>" class="preview"><?php echo $file['name']?></option>
			<?php endforeach; ?>
		<?php endforeach; ?>
		</select>
		<br />
		<input type="submit" value="<?php echo Lang::txt('Next'); ?> &raquo;" id="prj-db-preview-file" class="btn" />
	</form>
<?php
	}
	elseif (isset($this->db_id) && $this->db_id)
	{
?>
	<h3><?php echo Lang::txt('Loading database'); ?>: <em><?php echo $this->title?></em>...</h3>
	<form style="display: none;" id="prj-db-select-form" method="POST" action="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->model->get('id') . '&active=databases&action=preview_data&raw_op=1')?>">
		<select id="prj-db-select-src">
			<option selected data-dir="<?php echo $this->dir?>" value="<?php echo $this->file?>" class="preview"><?php echo $this->file?></option>
		</select>
		<input type="hidden" name="dir" value="<?php echo $this->dir?>">
		<input type="hidden" name="file" value="<?php echo $this->file?>">
		<input type="hidden" name="title" value="<?php echo $this->title?>">
		<input type="hidden" name="desc" value="<?php echo $this->desc?>">
		<input type="hidden" name="db_id" value="<?php echo $this->db_id?>">
		<input type="button" value="<?php echo Lang::txt('Next'); ?> &raquo;" class="btn" id="prj-db-preview-file" />
	</form>
<?php
	}
	else
	{
?>
	<h3><?php echo Lang::txt('Sorry, you need to have CSV formatted spreadsheet files to create databases.'); ?></h3>
	<p><?php echo Lang::txt('Maybe the file has already been used for a database. Please'); ?> <a href="/projects/<?php echo $this->model->get('alias')?>/databases"><?php echo Lang::txt('go back'); ?></a> <?php echo Lang::txt('and remove the database that\'s using the file'); ?>
	<span class="and_or prominent">or</span>
	<a href="/projects/<?php echo $this->model->get('alias')?>/files"><?php echo Lang::txt('Click here'); ?></a> <?php echo Lang::txt('to upload a new CSV file'); ?>.</p>
<?php
	}
?>
</div>

<div id="prj-db-step-2" class="prj-db-step" style="display: none;">
	<input type="submit" value="<?php echo Lang::txt('Next'); ?> &raquo;" class="prj-db-next btn rightfloat" data-step='2' />
	<input type="submit" value="&laquo; <?php echo Lang::txt('Back'); ?>" class="prj-db-back btn rightfloat" data-warning="true" data-step='2' />
	<h3><?php echo Lang::txt('Step 2: Verify Data'); ?> [<span id="prj-db-rec-limit"></span>]</h3>
	<div id="prj-db-preview-table-wrapper" style="height: 400px; overflow: auto; padding: 5px;"></div>
</div>

<div id="prj-db-step-3" class="prj-db-step" style="display: none;">
	<input type="submit" value="&laquo; Back" class="prj-db-back btn rightfloat" data-step='3' />
	<h3><?php echo Lang::txt('Step 3: Title &amp; Description, Finish'); ?></h3>
	<form id="prj-db-finish-form" method="post" action="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->model->get('id') . '&active=databases&action=create_database&raw_op=1')?>">
		<label for="prj-db-title"><?php echo Lang::txt('Title'); ?>:<input type="text" name="prj-db-title" id="prj-db-title" /></label>
		<label for="prj-db-desc"><?php echo Lang::txt('Description'); ?>:<textarea name="prj-db-desc" id="prj-db-desc" cols="5" rows="10"></textarea></label>
		<input type="submit" value="<?php echo Lang::txt('Finish'); ?>" class="btn" id="prj-db-finish-btn" />
	</form>
</div>

<div id="col-prop-dialog" style="display: none;" title="">
	<div class="tabs">
		<ul>
			<li><a href="#tabs-1"><?php echo Lang::txt('General'); ?></a></li>
			<li><a href="#tabs-2"><?php echo Lang::txt('Column Type'); ?></a></li>
			<li><a href="#tabs-3"><?php echo Lang::txt('Other'); ?></a></li>
		</ul>
		<div id="tabs-1">
			<label for="prj-db-col-label"><?php echo Lang::txt('Label'); ?>:</label><br />
			<input id="prj-db-col-label" class="col-prop" type="text" value="" /><br /><br />

			<label for="prj-db-col-desc"><?php echo Lang::txt('Description'); ?>:</label><br />
			<textarea id="prj-db-col-desc" class="col-prop" style="width: 400px;"></textarea><br /><br />

			<label for="prj-db-col-width"><?php echo Lang::txt('Width'); ?>:</label><br />
			<input id="prj-db-col-width" class="col-prop" type="text" value="" /><br /><br />

			<label for="prj-db-col-units"><?php echo Lang::txt('Units'); ?>:</label><br />
			<input id="prj-db-col-units" class="col-prop" type="text" value="" />
		</div>
		<div id="tabs-2">
			<label for="prj-db-col-type"><?php echo Lang::txt('Column Type'); ?>:</label><br />
			<select id="prj-db-col-type" class="col-prop">
				<option value="text_small"><?php echo Lang::txt('Text [small]'); ?></option>
				<option value="text_large"><?php echo Lang::txt('Text [large]'); ?></option>
				<option value="link"><?php echo Lang::txt('Link'); ?></option>
				<option value="image"><?php echo Lang::txt('Image'); ?></option>
				<option value="email"><?php echo Lang::txt('Email'); ?></option>
				<option value="int"><?php echo Lang::txt('Integer'); ?></option>
				<option value="float"><?php echo Lang::txt('Floating Point'); ?></option>
				<option value="numeric"><?php echo Lang::txt('Numeric [4 decimal places]'); ?></option>
				<option value="date"><?php echo Lang::txt('Date [yyyy-mm-dd]'); ?></option>
				<option value="datetime"><?php echo Lang::txt('Date &amp; Time [yyyy-mm-dd HH:MM:SS]'); ?></option>
			</select><br /><br />
			<div class="col-type-props" id="prj-db-col-type-text">
				<label for="prj-db-col-truncate" title="<?php echo Lang::txt('Hide the overflow text, full text will be visible by hover-over or by clicking on the visible text'); ?>">
					<input type="checkbox" class="col-prop" value="truncate" id="prj-db-col-truncate" /><?php echo Lang::txt('Limit text to a single line'); ?>
				</label>
			</div>
			<div class="col-type-props" id="prj-db-col-type-link" style="display: none;">
				<label for="prj-db-col-linktype" title="<?php echo Lang::txt('Select if files are stored in the repository'); ?>">
					<input type="checkbox" class="col-prop" value="repofiles" id="prj-db-col-linktype" /><?php echo Lang::txt('Repository Files?'); ?>
				</label>
				<br />
				<label for="prj-db-col-linkpath"><?php echo Lang::txt('Repository Path'); ?>:</label><br />
				<select class="col-prop" id="prj-db-col-linkpath"></select><br />
				<div style="font-size: .8em;">
					<ul>
						<li><?php echo Lang::txt('Only files in the source CSV file folder or  its sub folders can be used here.'); ?></li>
						<li><?php echo Lang::txt('Your CSV file should list <strong>only the file name</strong> for repository files.'); ?></li>
					</ul>
				</div>
			</div>
		</div>
		<div id="tabs-3">
			<label for="prj-db-col-align"><?php echo Lang::txt('Alignment'); ?>:</label><br />
			<select id="prj-db-col-align" class="col-prop">
				<option value="left"><?php echo Lang::txt('Left'); ?></option>
				<option value="center"><?php echo Lang::txt('Center'); ?></option>
				<option value="right"><?php echo Lang::txt('Right'); ?></option>
			</select><br /><br />
			<label for="prj-db-col-text-color"><?php echo Lang::txt('Text Color'); ?>:</label><br />
			<input type='text' id="prj-db-col-text-color" class="col-prop color-picker dv-style" data-default='rgba(255, 255, 255, 0)' data-style-val='' data-style-type="color" />
			<input type='button' class="color-picker-clear" data-target="prj-db-col-text-color" value="<?php echo Lang::txt('Clear'); ?>" style="width: 50px;" />
			<br /><br />
			<label for="prj-db-col-bg-color"><?php echo Lang::txt('Background Color'); ?>:</label><br />
			<input type='text' id="prj-db-col-bg-color" class="col-prop color-picker dv-style" data-default='rgba(255, 255, 255, 0)' data-style-val='' data-style-type="background" />
			<input type='button' class="color-picker-clear" data-target="prj-db-col-bg-color" value="<?php echo Lang::txt('Clear'); ?>" style="width: 50px;" />
			<br /><br />
		</div>
	</div>
</div>
