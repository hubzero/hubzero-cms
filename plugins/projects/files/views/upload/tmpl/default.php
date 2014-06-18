<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$subdirlink = $this->subdir ? a . 'subdir=' . urlencode($this->subdir) : '';
$rUrl = $this->url . '?a=1' . $subdirlink;

$slimit = ProjectsHtml::formatSize($this->sizelimit);

// Incoming
$basic   = JRequest::getInt('basic', 0);

// Directory path breadcrumbs
$desect_path = explode(DS, $this->subdir);
$path_bc = '';
$url = '';
$parent = '';
if (count($desect_path) > 0)
{
	for ($p = 0; $p < count($desect_path); $p++)
	{
		$parent .= count($desect_path) > 1 && $p != count($desect_path)  ? $url  : '';
		$url 	.= DS . $desect_path[$p];
		$path_bc .= ' &raquo; <span><a href="'.$this->url.'/?subdir='.urlencode($url)
			.'" class="folder">'.$desect_path[$p].'</a></span> ';
	}
}

?>
<?php if ($this->ajax) { ?>
<div id="abox-content">
<h3><?php echo JText::_('COM_PROJECTS_FILES_UPLOAD_FILES'); ?></h3>
<?php } ?>
<?php
// Display error or success message
if ($this->getError()) {
	echo ('<p class="witherror">'.$this->getError().'</p>');
}
?>
<?php
if (!$this->getError()) {
?>

<form id="hubForm-ajax" method="post" enctype="multipart/form-data" action="<?php echo $rUrl; ?>">
	<?php if (!$this->ajax) { ?>
		<div id="plg-header">
			<h3 class="files">
				<a href="<?php echo $this->url; ?>"><?php echo $this->title; ?></a><?php if ($this->subdir) { ?> <?php echo $path_bc; ?><?php } ?>
			&raquo; <span class="subheader"><?php echo JText::_('COM_PROJECTS_FILES_UPLOAD_FILES'); ?></span>
			</h3>
		</div>
	<?php } ?>
	<fieldset class="uploader">
		<p id="upload-instruct"><?php echo JText::_('COM_PROJECTS_FILES_PICK_FILES_UPLOAD') . ' ';
			if ($this->subdir)
			{
				echo JText::_('COM_PROJECTS_FILES_PICK_FILES_UPLOAD_SUBDIR') . ' <span class="prominent">' . $this->subdir . '</span> ' . JText::_('COM_PROJECTS_FILES_DIR') . ':';
			}
			else
			{
				echo ' ' . JText::_('COM_PROJECTS_FILES_PICK_FILES_UPLOAD_HOME') . ' ' . JText::_('COM_PROJECTS_FILES_DIR') . ':';
			} ?>
		</p>

		<div class="field-wrap">
			<div class="asset-uploader">
		<?php if (JPluginHelper::isEnabled('system', 'jquery') && !$basic) { ?>
					<div id="ajax-uploader" data-action="<?php echo $this->url . '?' . $this->do . '=save&amp;no_html=1&amp;ajax=1'  . $subdirlink; ?>" >
						<label class="addnew">
							<input name="upload[]" type="file" class="option uploader" id="uploader" multiple="multiple" />
							<p class="hint ipadded"><?php echo JText::_('COM_PROJECTS_FILES_MAX_UPLOAD').' '.$slimit; ?></p>
						</label>
						<div id="upload-body">
							<ul id="u-selected" class="qq-upload-list">
							</ul>
						</div>
					</div>
					<script src="/plugins/projects/files/js/jquery.fileuploader.js"></script>
					<script src="/plugins/projects/files/js/jquery.queueuploader.js"></script>
					<script src="/plugins/projects/files/js/fileupload.jquery.js"></script>
		<?php } else { ?>
				<label class="addnew">
					<input name="upload[]" type="file" class="option uploader" id="uploader" multiple="multiple" />
					<p class="hint ipadded"><?php echo JText::_('COM_PROJECTS_FILES_MAX_UPLOAD').' '.$slimit; ?></p>
				</label>
		<?php } ?>
			</div>
		</div>
		<div id="upload-csize">
		</div>
		<?php if (!$this->ajax || $basic) { ?>
		<div class="sharing-option-extra" id="archiveCheck">
			<label class="sharing-option">
				<input type="checkbox" name="expand_zip" id="expand_zip" value="1" />
				<?php echo JText::_('COM_PROJECTS_FILES_UPLOAD_UNZIP_ARCHIVES'); ?>
			</label>
		</div>
		<?php } ?>

		<input type="hidden" name="MAX_FILE_SIZE" id="maxsize" value="<?php echo $this->params->get('maxUpload', '104857600'); ?>" />
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="pid" value="<?php echo $this->pid; ?>" />
		<input type="hidden" name="action" id="formaction" value="save" />
		<input type="hidden" name="failed" id="failed" value="0" />
		<input type="hidden" name="uploaded" id="uploaded" value="0" />
		<input type="hidden" name="updated" id="updated" value="0" />
		<input type="hidden" name="queue" id="queue" value="" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="active" value="files" />
		<input type="hidden" name="avail" id="avail" value="<?php echo $this->unused; ?>" />
		<input type="hidden" name="case" value="<?php echo $this->case; ?>" />
		<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />

		<div id="upload-submit">
		<p class="submitarea">
			<input type="submit" value="<?php echo JText::_('COM_PROJECTS_UPLOAD_NOW'); ?>" class="btn btn-success active" id="f-upload"  />
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo JText::_('COM_PROJECTS_CANCEL'); ?>" />
			<?php } else {  ?>
				<a id="cancel-action" class="btn btn-cancel" href="<?php echo $this->url . '?a=1' .$subdirlink; ?>"><?php echo JText::_('COM_PROJECTS_CANCEL'); ?></a>
			<?php } ?>
		</p>
		</div>
		<?php if (JPluginHelper::isEnabled('system', 'jquery') && !$basic) { ?>
			<p class="hint rightfloat mini faded">Having trouble with the file upload? Try using <a href="<?php echo $this->url . '?action=upload&basic=1' .$subdirlink; ?>">basic upload</a>.</p>
		<?php } ?>
	</fieldset>
</form>
<?php } ?>
<?php if ($this->ajax) { ?>
</div>
<?php } ?>