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
$name = $this->file->get('name');
// Is this a duplicate remote?
if ($this->file->get('remote') && $this->file->get('name') != $this->file->get('remoteTitle'))
{
	$append = \Components\Projects\Helpers\Html::getAppendedNumber($this->file->get('name'));

	if ($append > 0)
	{
		$name = \Components\Projects\Helpers\Html::fixFileName($this->file->get('remoteTitle'), ' (' . $append . ')', $this->file->get('ext'));
	}
}

// Do not display Google native extension
$native = \Components\Projects\Helpers\Google::getGoogleNativeExts();
if (in_array($this->file->get('ext'), $native))
{
	$name = preg_replace("/." . $this->file->get('ext') . "\z/", "", $name);
}

?>
	<h4><img src="<?php echo $this->file->getIcon(); ?>" alt="<?php echo $this->file->get('ext'); ?>" /> <?php echo $name; ?></h4>
	<ul class="filedata">
		<?php echo $this->file->get('ext') && !$this->file->get('converted') ? '<li>' . strtoupper($this->file->get('ext')) . '</li>' : ''; ?>
		<?php echo $this->file->get('converted') ? '<li>' . Lang::txt('PLG_PROJECTS_FILES_REMOTE_FILE_GOOGLE') . '</li>' : ''; ?>
		<?php
		if ($this->file->get('converted') && $this->file->get('originalPath'))
		{
			echo '<li>From ' . basename($this->file->get('originalPath'));
			if ($this->file->get('originalFormat')) { echo ' (' . $this->file->get('originalFormat') . ')'; }
			echo '</li>';
		}
		?>
		<?php echo $this->file->get('originalPath') && $this->file->getSize() ? '<li>' . strtoupper($this->file->getSize('formatted')) . '</li>' : ''; ?>
	</ul>

	<?php if ($this->file->getPreview($this->model, $this->file->get('hash'), 'fullPath')) { ?>
		<div id="preview-image"><img src="<?php echo $this->file->getPreview($this->model, $this->file->get('hash'), 'url'); ?>" alt="<?php echo Lang::txt('PLG_PROJECTS_FILES_LOADING_PREVIEW'); ?>" /></div>
	<?php } elseif ($this->file->get('content')) { ?>
		<pre><?php echo $this->file->get('content'); ?></pre>
	<?php }
