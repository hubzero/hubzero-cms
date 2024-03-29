<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!$this->ajax)
{
	$this->css('uploader');
}

$subdirlink = $this->subdir ? '&amp;subdir=' . urlencode($this->subdir) : '';
$rUrl = $this->url . '?action=browse&a=1' . $subdirlink;

// Incoming
$basic = Request::getInt('basic', 0);

// Directory path breadcrumbs
$bc = \Components\Projects\Helpers\Html::buildFileBrowserCrumbs($this->subdir, $this->url, $parent);

?>
<?php if ($this->ajax) { ?>
<div id="abox-content">
	<h3><?php echo Lang::txt('PLG_PROJECTS_FILES_UPLOAD_FILES'); ?></h3>
<?php } ?>

<form id="<?php echo $this->ajax ? 'hubForm-ajax' : 'plg-form'; ?>" method="post" enctype="multipart/form-data" action="<?php echo $rUrl; ?>">
	<?php if (!$this->ajax) { ?>
		<div id="plg-header">
			<h3 class="files">
				<a href="<?php echo $this->url; ?>"><?php echo $this->title; ?></a><?php if ($this->subdir) { ?> <?php echo $bc; ?><?php } ?>
			&raquo; <span class="subheader"><?php echo Lang::txt('PLG_PROJECTS_FILES_UPLOAD_FILES'); ?></span>
			</h3>
		</div>
	<?php } ?>
	<fieldset class="uploader">
		<p id="upload-instruct"><?php echo Lang::txt('PLG_PROJECTS_FILES_PICK_FILES_UPLOAD') . ' ';
			if ($this->subdir)
			{
				echo Lang::txt('PLG_PROJECTS_FILES_PICK_FILES_UPLOAD_SUBDIR') . ' <span class="prominent">' . $this->subdir . '</span> ' . Lang::txt('PLG_PROJECTS_FILES_DIR') . ':';
			}
			else
			{
				echo ' ' . Lang::txt('PLG_PROJECTS_FILES_PICK_FILES_UPLOAD_HOME') . ' ' . Lang::txt('PLG_PROJECTS_FILES_DIR') . ':';
			} ?>
		</p>

		<div class="field-wrap">
			<div class="asset-uploader">
		<?php if (!$basic) { ?>
					<div id="ajax-uploader" data-action="<?php echo $this->url . '?action=save&amp;no_html=1&amp;ajax=1' . $subdirlink; ?>" >
						<label class="addnew">
							<input name="upload[]" type="file" class="option uploader" id="uploader" multiple="multiple" />
							<p class="hint ipadded"><?php echo Lang::txt('PLG_PROJECTS_FILES_MAX_UPLOAD') . ' ' . \Hubzero\Utility\Number::formatBytes($this->sizelimit); ?></p>
						</label>
						<div id="upload-body">
							<ul id="u-selected" class="qq-upload-list">
							</ul>
						</div>
					</div>
					<script src="<?php echo rtrim(Request::base(true), '/'); ?>/core/plugins/projects/files/assets/js/jquery.fileuploader.js"></script>
					<script src="<?php echo rtrim(Request::base(true), '/'); ?>/core/plugins/projects/files/assets/js/jquery.queueuploader.js"></script>
					<script src="<?php echo rtrim(Request::base(true), '/'); ?>/core/plugins/projects/files/assets/js/fileupload.jquery.js"></script>
					<?php
						/* @TODO: this needs to be handle in a more standard site configuration way */
						$acceptedFormatsJS = PATH_APP  . '/acceptedFormats.js';
						if (file_exists($acceptedFormatsJS)): ?>
						<script src="<?php echo rtrim(Request::base(true), '/'); ?>/app/site/acceptedFormats.js">"></script>
					<?php endif; ?>
		<?php } else { ?>
				<label class="addnew">
					<input name="upload[]" type="file" class="option uploader" id="uploader" multiple="multiple" />
					<p class="hint ipadded"><?php echo Lang::txt('PLG_PROJECTS_FILES_MAX_UPLOAD') . ' ' . \Hubzero\Utility\Number::formatBytes($this->sizelimit); ?></p>
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
				<?php echo Lang::txt('PLG_PROJECTS_FILES_UPLOAD_UNZIP_ARCHIVES'); ?>
			</label>
		</div>
		<?php } ?>

		<input type="hidden" name="MAX_FILE_SIZE" id="maxsize" value="<?php echo $this->params->get('maxUpload', '104857600'); ?>" />
		<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
		<input type="hidden" name="action" id="formaction" value="save" />
		<input type="hidden" name="failed" id="failed" value="0" />
		<input type="hidden" name="uploaded" id="uploaded" value="0" />
		<input type="hidden" name="updated" id="updated" value="0" />
		<input type="hidden" name="queue" id="queue" value="" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="active" value="files" />
		<input type="hidden" name="avail" id="avail" value="<?php echo $this->unused; ?>" />
		<input type="hidden" name="repo" value="<?php echo $this->repo->get('name'); ?>" />
		<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />

		<div id="upload-submit">
		<p class="submitarea">
			<input type="submit" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_UPLOAD_NOW'); ?>" class="btn btn-success active" id="f-upload"  />
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('JCANCEL'); ?>" />
			<?php } else {  ?>
				<a id="cancel-action" class="btn btn-cancel" href="<?php echo $this->url . '?a=1' . $subdirlink; ?>"><?php echo Lang::txt('JCANCEL'); ?></a>
			<?php } ?>
		</p>
		</div>
		<?php if (!$basic) { ?>
			<p class="hint rightfloat mini faded"><?php echo Lang::txt('PLG_PROJECTS_FILES_BASIC_UPLOAD_QUESTION'); ?> <a href="<?php echo $this->url . '?action=upload&amp;basic=1' . $subdirlink; ?>"><?php echo Lang::txt('PLG_PROJECTS_FILES_BASIC_UPLOAD'); ?></a>.</p>
		<?php } ?>
	</fieldset>
</form>
<?php if ($this->ajax) { ?>
</div>
<?php }
