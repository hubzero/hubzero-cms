<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<li class="asset-group-item <?php echo ($this->ag->isPublished()) ? 'published' : 'unpublished' ?>" id="assetgroupitem_<?php echo $this->ag->get('id') ?>">
	<div class="asset-group-controls">
		<div class="sortable-handle"></div>
		<div class="asset-group-edit"></div>
	</div>
	<div class="uploadfiles">
		<p>Drag files here to upload</p>
		<p>or</p>
		<div class="aux-attachments">
			<form action="<?php echo Request::base(true); ?>/api/courses/asset/new" class="aux-attachments-form attach-link">
				<label for"content" class="aux-attachments-content-label">Attach a link:</label>
				<textarea class="input-content" name="content" placeholder="" rows="5"></textarea>
				<input class="input-type" type="hidden" name="type" value="link" />
				<input class="aux-attachments-submit" type="submit" value="Add" />
				<input class="aux-attachments-cancel" type="reset" value="Cancel" />
				<input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
				<input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
				<input type="hidden" name="scope_id" value="<?php echo $this->ag->get('id'); ?>" />
				<a href="<?php echo Request::base(true); ?>/help/courses/builder" rel="noopener" target="_blank" class="help-info">help</a>
			</form>
			<a href="#" title="Attach a link" class="attach-link"></a>
			<a href="#" title="Embed a Kaltura or YouTube Video" class="attach-object"></a>
			<a href="#" title="Include a wiki page" class="attach-wiki"></a>
			<?php if (Components\Courses\Models\Assets\Tool::getToolDirectory() != false): ?>
				<a href="#" title="Include a tool" class="attach-tool"></a>
			<?php endif; ?>
			<a href="#" title="Browse for files" class="browse-files"></a>
		</div>
		<form action="<?php echo Request::base(true); ?>/api/courses/asset/new" class="uploadfiles-form">
			<input type="file" name="files[]" class="fileupload" multiple />
			<input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
			<input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
			<input type="hidden" name="scope_id" value="<?php echo $this->ag->get('id'); ?>" />
		</form>
	</div>
	<div class="asset-group-item-container">
		<div class="asset-group-item-title title toggle-editable"><?php echo $this->ag->get('title') ?></div>
		<div class="title-edit">
			<form action="<?php echo Request::base(true); ?>/api/courses/assetgroup/save" class="assetgroup-title-form">
				<input class="title-text" name="title" type="text" value="<?php echo $this->ag->get('title'); ?>" />
				<input class="assetgroup-title-save" type="submit" value="Save" />
				<input class="assetgroup-title-reset" type="reset" value="Cancel" />
				<input type="hidden" name="course_id" value="<?php echo $this->course->get('id'); ?>" />
				<input type="hidden" name="offering" value="<?php echo $this->course->offering()->alias(); ?>" />
				<input type="hidden" name="id" value="<?php echo $this->ag->get('id'); ?>" />
			</form>
		</div>
<?php
$hasPublishedAssets = false;

// Loop through the assets
if ($this->ag->assets()->total())
{
?>
		<ul class="assets-list sortable-assets">
<?php
		foreach ($this->ag->assets() as $a)
		{
			// Don't put deleted assets here
			if (!$a->isDeleted())
			{
				$hasPublishedAssets = true;

				$this->view('asset_partial')
				     ->set('base', $this->base)
				     ->set('course', $this->course)
				     ->set('unit', $this->unit)
				     ->set('ag', $this->ag)
				     ->set('a', $a)
				     ->display();
			}
		}

		if (!$hasPublishedAssets) // There are assets, but none are published
		{
?>
			<li class="asset-item asset missing nofiles">
				No files
				<span class="next-step-upload">
					Upload files &rarr;
				</span>
			</li>
<?php
		}
?>
		</ul>
<?php
}
else // no assets in this asset group
{
?>
	<ul class="assets-list sortable-assets">
		<li class="asset-item asset missing nofiles">
			No files
			<span class="next-step-upload">
				Upload files &rarr;
			</span>
		</li>
	</ul>
<?php
}
?>
	</div>
</li>
<div class="clear"></div>
