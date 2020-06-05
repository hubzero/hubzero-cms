<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<div class="grid">
		<div class="col span-half">
			<div id="ticket-number">
				<h2>
					<?php echo Lang::txt('COM_SUPPORT_REPORT_NUMBER', $this->report->id); ?>
				</h2>
			</div>
		</div><!-- / .col span-half -->
		<div class="col span-half omega">
			<div id="messagebox">
				<div class="wrap">
					<h3><?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_THANKS'); ?></h3>
				<?php if ($this->report) { ?>
					<p><?php echo Lang::txt('COM_SUPPORT_REPORT_NUMBER_REFERENCE', $this->report->id); ?></p>
				<?php } ?>
				<?php if ($this->returnlink) { ?>
					<p><a class="btn" href="<?php echo $this->returnlink; ?>"><?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_CONTINUE'); ?></a></p>
				<?php } ?>
				</div>
			</div>
		</div><!-- / .col span-half omega -->
	</div><!-- / .grid -->
</section><!-- / .main section -->
