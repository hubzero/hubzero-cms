<?php
/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2012-2013 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2012-2013 by Purdue Research Foundation, West Lafayette, IN 47906.
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
defined('_JEXEC') or die('Restricted access');
$document =& JFactory::getDocument();

$document->addScript('/plugins/projects/databases/res/dataTables/jquery.dataTables.js');
$document->addStyleSheet('/plugins/projects/databases/res/dataTables/jquery.dataTables.css');


$document->addStyleSheet('/plugins/projects/databases/res/chosen/chosen.css');
$document->addScript('/plugins/projects/databases/res/chosen/chosen.jquery.js');

$document->addStyleSheet('/plugins/projects/databases/res/spectrum/spectrum.css');
$document->addScript('/plugins/projects/databases/res/spectrum/spectrum.js');

?>
<style>
	.chzn-single {
		border: 1px solid #AAAAAA;
	}
</style>
<div id="plg-header">
<h3 class="databases c-header"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'active=databases'.a. 'alias=' . $this->project->alias); ?>">Databases</a> &raquo; <span class="indlist"><?= isset($this->db_id) ? 'Update Database' : JText::_('PLG_PROJECTS_DATA_START'); ?></span></h3>
</div>
<div id="prj-db-step-1" class="prj-db-step">
<?php
	if(count($this->files) > 0 && (!isset($this->db_id)))
	{
?>
	<h3>Step 1: Select a file</h3>
	<form id="prj-db-select-form" method="post" action="<?=JRoute::_('index.php?option=' . $this->option . a . 'id=' . $this->project->id . a . 'active=databases' . a . 'action=preview_data' . a . 'raw_op=1')?>">
		<select id="prj-db-select-src" title="Select a CSV file to convert in to a database">
		<?foreach($this->files as $dir => $files):?>
			<?
			if($dir == '.') {
				$dir = '';
			}
			?>
				<optgroup label="/<?=$dir?>">
			<?foreach($files as $file):?>
				<option data-dir="<?=$dir?>" data-hash="<?=$file['hash']?>" data-date="<?=$file['date']?>" value="<?=$file['name']?>" class="preview"><?=$file['name']?></option>
			<?endforeach;?>
		<?endforeach;?>
		</select>
		<br />
		<input type="button" value="Next >>" id="prj-db-preview-file" />
	</form>
<?php
	}
	elseif (isset($this->db_id) && $this->db_id)
	{
?>
	<h3>Loading database : <em><?=$this->title?></em>...</h3>
	<form style="display: none;" id="prj-db-select-form" method="POST" action="<?=JRoute::_('index.php?option=' . $this->option . a . 'id=' . $this->project->id . a . 'active=databases' . a . 'action=preview_data' . a . 'raw_op=1')?>">
		<select id="prj-db-select-src">
			<option selected data-dir="<?=$this->dir?>" value="<?=$this->file?>" class="preview"><?=$this->file?></option>
		</select>
		<input type="hidden" name="dir" value="<?=$this->dir?>">
		<input type="hidden" name="file" value="<?=$this->file?>">
		<input type="hidden" name="title" value="<?=$this->title?>">
		<input type="hidden" name="desc" value="<?=$this->desc?>">
		<input type="hidden" name="db_id" value="<?=$this->db_id?>">
		<input type="button" value="Next >>" id="prj-db-preview-file" />
	</form>
<?php
	}
	else
	{
?>
	<h3>Sorry, You need to have CSV formatted spreadsheet files to create databases.</h3>
	<p>Maybe the file has already been used for a database. Please <a href="/projects/<?=$this->project->alias?>/databases">go back</a> and remove the database that's using the file</p>
	<p>or</p>
	<p>Please <a href="/projects/<?=$this->project->alias?>/files">Click here</a> to upload a new CSV file.</p>
<?php
	}
?>
</div>

<div id="prj-db-step-2" class="prj-db-step" style="display: none;">
	<input type="submit" value="Next >>" class="prj-db-next" data-step='2' style="float: right;" />
	<input type="submit" value="<< Back" class="prj-db-back" data-warning="true" data-step='2' style="float: right;" />
	<h3>Step 2: Verify Data [<span id="prj-db-rec-limit"></span>]</h3>
	<div id="prj-db-preview-table-wrapper" style="height: 400px; overflow: auto; padding: 5px;"></div>
</div>

<div id="prj-db-step-3" class="prj-db-step" style="display: none;">
	<input type="submit" value="<< Back" class="prj-db-back" data-step='3' style="float: right;" />
	<h3>Step 3: Title & Description, Finish</h3>
	<form id="prj-db-finish-form" method="post" action="<?=JRoute::_('index.php?option=' . $this->option . a . 'id=' . $this->project->id . a . 'active=databases' . a . 'action=create_database' . a . 'raw_op=1')?>">
		<label for="prj-db-title" >Title:</label><br /><input type="text" name="prj-db-title" id="prj-db-title" style="width: 450px;" /><br /><br />
		<label for="prj-db-desc" >Description:</label><br /><textarea type="text" name="prj-db-desc" id="prj-db-desc" style="width: 450px; height: 150px;"></textarea><br /><br />
		<input type="submit" value="Finish" id="prj-db-finish-btn" />
	</form>
</div>

<div id="col-prop-dialog" style="display: none;" title="">
	<div class="tabs">
		<ul>
			<li><a href="#tabs-1">General</a></li>
			<li><a href="#tabs-2">Column Type</a></li>
			<li><a href="#tabs-3">Other</a></li>
		</ul>
		<div id="tabs-1">
			<label for="prj-db-col-label">Label:</label><br />
			<input id="prj-db-col-label" class="col-prop" type="text" value="" /><br /><br />

			<label for="prj-db-col-desc">Description:</label><br />
			<textarea id="prj-db-col-desc" class="col-prop" type="text" style="width: 400px;"></textarea><br /><br />

			<label for="prj-db-col-width">Width:</label><br />
			<input id="prj-db-col-width" class="col-prop" type="text" value="" /><br /><br />

			<label for="prj-db-col-units">Units:</label><br />
			<input id="prj-db-col-units" class="col-prop" type="text" value="" />
		</div>
		<div id="tabs-2">
			<label for="prj-db-col-type">Column Type:</label><br />
			<select id="prj-db-col-type" class="col-prop">
				<option value="text_small">Text [small]</option>
				<option value="text_large">Text [large]</option>
				<option value="link">Link</option>
				<option value="image">Image</option>
				<option value="email">Email</option>
				<option value="int">Integer</option>
				<option value="float">Floating Point</option>
				<option value="numeric">Numeric [4 decimal places]</option>
				<option value="date">Date [yyyy-mm-dd]</option>
				<option value="datetime">Date & Time [yyyy-mm-dd HH:MM:SS]</option>
			</select><br /><br />
			<div class="col-type-props" id="prj-db-col-type-text">
				<label for="prj-db-col-truncate" title="Hide the overflow text, full text will be visible by hover-over or by clicking on the visible text">
					<input type="checkbox" class="col-prop" value="truncate" id="prj-db-col-truncate" />Limit text to a single line
				</label>
			</div>
			<div class="col-type-props" id="prj-db-col-type-link" style="display: none;">
				<label for="prj-db-col-linktype" title="Select if files are stored in the repository">
					<input type="checkbox" class="col-prop" value="repofiles" id="prj-db-col-linktype" />Repository Files?
				</label>
				<br />
				<label for="prj-db-col-linkpath">Repository Path:</label><br />
				<select class="col-prop" id="prj-db-col-linkpath"></select><br />
				<span style="font-size: .8em;">
					<ul>
						<li>Only files in the source CSV file folder or  its sub folders can be used here.</li>
						<li>Your CSV file should list <strong>only the file name</strong> for repository files.</li>
					</ul>
				</span>
			</div>
		</div>
		<div id="tabs-3">
			<label for="prj-db-col-align">Alignment:</label><br />
			<select id="prj-db-col-align" class="col-prop">
				<option value="left">Left</option>
				<option value="center">Center</option>
				<option value="right">Right</option>
			</select><br /><br />
			<label for="prj-db-col-text-color">Text Color:</label><br />
			<input type='text' id="prj-db-col-text-color" class="col-prop color-picker dv-style" data-default='rgba(255, 255, 255, 0)' data-style-val='' data-style-type="color" />
			<input type='button' class="color-picker-clear" data-target="prj-db-col-text-color" value="Clear" style="width: 50px;" />
			<br /><br />
			<label for="prj-db-col-bg-color">Background Color:</label><br />
			<input type='text' id="prj-db-col-bg-color" class="col-prop color-picker dv-style" data-default='rgba(255, 255, 255, 0)' data-style-val='' data-style-type="background" />
			<input type='button' class="color-picker-clear" data-target="prj-db-col-bg-color" value="Clear" style="width: 50px;" />
			<br /><br />
		</div>
	</div>
</div>
