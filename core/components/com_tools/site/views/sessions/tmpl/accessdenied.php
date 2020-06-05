<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('tools.css');
?>
<div id="error-wrap">
	<div id="error-box" class="code-403">
		<h2><?php echo Lang::txt('COM_TOOLS_ACCESSDENIED'); ?></h2>
<?php if ($this->getError()) { ?>
		<p class="error-reasons"><?php echo $this->getError(); ?></p>
<?php } ?>

		<p><?php echo Lang::txt('COM_TOOLS_ACCESSDENIED_MESSAGE'); ?></p>
		<h3><?php echo Lang::txt('COM_TOOLS_ACCESSDENIED_HOW_TO_FIX'); ?></h3>
		<ul>
			<li><?php echo Lang::txt('COM_TOOLS_ACCESSDENIED_OPT_CONTACT_SUPPORT', Route::url('index.php?option=com_support&controller=tickets&task=new')); ?></li>
			<li><?php echo Lang::txt('COM_TOOLS_ACCESSDENIED_OPT_BROWSE', Route::url('index.php?option=' . $this->option)); ?></li>
		</ul>
	</div><!-- / #error-box -->
</div><!-- / #error-wrap -->
