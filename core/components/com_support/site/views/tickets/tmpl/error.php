<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>
<div id="report-response">
	<div>
		<p><?php echo Lang::txt('COM_SUPPORT_ERROR_PROCESSING_FORM'); ?></p>
		<p><a href="javascript:HUB.Modules.ReportProblems.reshowForm();" title="<?php echo Lang::txt('COM_SUPPORT_EDIT_REPORT'); ?>"><?php echo Lang::txt('COM_SUPPORT_EDIT_REPORT'); ?></a></p>
	</div>
	<h3><?php echo Lang::txt('COM_SUPPORT_ERROR'); ?></h3>
	<p><?php echo Lang::txt('COM_SUPPORT_ERROR_PROCESSING_DESCRIPTION'); ?></p>
<?php if ($this->getError()) { echo '<p>' . $this->getError() . '</p>'; } ?>
</div>

<script type="text/javascript">window.top.window.HUB.Modules.ReportProblems.hideTimer();</script>