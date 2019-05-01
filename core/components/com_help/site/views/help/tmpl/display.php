<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Document::setTitle(\Lang::txt('COM_HELP'));

$this->js();
?>
<div class="help-header" id="help-top">
	<?php if ($this->page != 'index') : ?>
		<button class="back" id="back" title="<?php echo Lang::txt('COM_HELP_GO_BACK'); ?>"><?php echo Lang::txt('COM_HELP_GO_BACK'); ?></button>
	<?php endif; ?>
</div>

<?php echo $this->content; ?>

<div class="help-footer">
	<a class="top" href="#help-top"><?php echo Lang::txt('COM_HELP_BACK_TO_TOP'); ?></a>
	<?php if ($this->page != 'index') : ?>
		<a class="index" href="<?php echo Route::url('index.php?option=com_help&component=' . str_replace('com_', '', $this->component) . '&page=index'); ?>">
			<?php echo Lang::txt('COM_HELP_INDEX'); ?>
		</a>
	<?php endif; ?>
	<p class="modified">
		<?php echo Lang::txt('COM_HELP_LAST_MODIFIED', date('l, F d, Y @ g:ia', $this->modified)); ?>
	</p>
</div>
