<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('tools.css');
?>
<div id="error-wrap">
	<div id="error-box" class="code-403">
		<h2><?php echo Lang::txt('COM_TOOLS_BADPARAMS'); ?></h2>
<?php if ($this->getError()) { ?>
		<p class="error-reasons"><?php echo $this->getError(); ?></p>
<?php } ?>
		<p><?php echo Lang::txt('COM_TOOLS_BADPARAMS_MESSAGE'); ?></p>
		<pre><?php echo $this->escape($this->badparams); ?></pre>
		<p><?php echo Lang::txt('COM_TOOLS_BADPARAMS_OPT_CONTACT_SUPPORT', Route::url('index.php?option=com_support&controller=tickets&task=new')); ?></p>
	</div><!-- / #error-box -->
</div><!-- / #error-wrap -->
