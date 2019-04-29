<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->getError())
{
	echo '<p class="error">' . $this->getError() . '</p>';
	return;
}
?>

<h4>
	<?php echo \Components\Projects\Models\File::drawIcon($this->file->getExtension()); ?>
	<?php echo $this->file->getName(); ?>
</h4>

<ul class="filedata">
	<?php if ($this->file->getExtension()) : ?>
		<li>
			<?php echo strtoupper($this->file->getExtension()); ?>
		</li>
	<?php endif; ?>
</ul>

<?php if ($this->file->isImage()) : ?>
	<?php $contents = $this->file->read(); ?>
	<?php $base64   = base64_encode($contents); ?>
	<?php $attr     = getimagesizefromstring($contents); ?>
	<div id="preview-image">
		<img src="data:<?php echo $attr['mime']; ?>;base64,<?php echo $base64; ?>" alt="<?php echo Lang::txt('PLG_PROJECTS_FILES_LOADING_PREVIEW'); ?>" />
	</div>
<?php endif;
