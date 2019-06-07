<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>
<?php if ($this->no_html) { ?>
	<div id="report-response">
		<div>
			<p><?php echo Lang::txt('COM_FEEDBACK_YOUR_TICKET'); ?> # <span><a href="<?php echo Route::url('index.php?option=com_support&task=ticket&id=' . $this->ticket); ?>" title="View ticket"><?php echo $this->ticket; ?></a></span></p>
			<p><button class="btn btn-reset" title="<?php echo Lang::txt('COM_FEEDBACK_NEW_REPORT'); ?>"><?php echo Lang::txt('COM_FEEDBACK_NEW_REPORT'); ?></button></p>
		</div>
		<p>
			<?php echo Lang::txt('COM_FEEDBACK_TROUBLE_THANKS'); ?><br /><br />
			<?php echo Lang::txt('COM_FEEDBACK_TROUBLE_TICKET_TIMES'); ?>
		</p>
	</div>
	<script type="text/javascript">window.top.window.HUB.ReportProblem.hideTimer();</script>
<?php } else { ?>
	<header id="content-header">
		<h2><?php echo $this->title; ?></h2>
	</header><!-- / #content-header -->

	<section class="main section">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>
		<p><?php echo Lang::txt('COM_FEEDBACK_TROUBLE_THANKS'); ?></p>
		<p class="information"><?php echo Lang::txt('COM_FEEDBACK_TROUBLE_TICKET_TIMES'); ?></p>
		<?php if ($this->ticket) { ?>
			<p><?php echo Lang::txt('COM_FEEDBACK_TROUBLE_TICKET_REFERENCE', $this->ticket); ?></p>
		<?php } ?>
	</section><!-- / .main section -->
<?php }
