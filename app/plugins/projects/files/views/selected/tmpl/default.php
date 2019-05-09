<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->file->get('converted'))
{
	$slabel = $this->file->get('type') == 'folder' ? Lang::txt('PLG_PROJECTS_FILES_REMOTE_FOLDER') : Lang::txt('PLG_PROJECTS_FILES_REMOTE_FILE');
}

$multi = isset($this->multi) && $this->multi ? '[]' : '';

?>
<li><img src="<?php echo $this->file->getIcon(); ?>" alt="<?php echo $this->file->get('name'); ?>" />
<?php echo $this->file->get('name'); ?>
<?php if ($this->file->get('converted')) { echo '<span class="remote-file">' . $slabel . '</span>'; } ?>
<?php
if ($this->file->get('converted') && $this->file->get('originalPath'))
{
	echo '<span class="remote-file faded">' . Lang::txt('PLG_PROJECTS_FILES_CONVERTED_FROM_ORIGINAL'). ' ' . basename($this->file->get('originalPath'));
	if ($this->file->get('originalFormat'))
	{
		echo ' (' . $this->file->get('originalPath') . ')';
	}
	echo '</span>';
}
?>

<?php if (isset($this->skip) && $this->skip == true) { echo '<span class="file-skipped">' . Lang::txt('PLG_PROJECTS_FILES_SKIPPED') . '</span>'; } ?>
<?php echo $this->file->get('type') == 'folder'
	? '<input type="hidden" name="folder' . $multi . '" value="' . urlencode($this->file->get('name')) . '" />'
	: '<input type="hidden" name="asset' . $multi . '" value="' . urlencode($this->file->get('name')) . '" />'; ?>

<?php if (isset($this->extras)) { echo $this->extras; } ?>
</li>