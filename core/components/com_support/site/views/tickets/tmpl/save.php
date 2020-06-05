<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

if (!$this->no_html)
{
	$this->css();
}

$tmpl = Request::getCmd('tmpl');
?>
	<header id="content-header">
		<h2><?php echo Lang::txt('COM_SUPPORT'); ?></h2>
	</header><!-- / #content-header -->
	<section class="main section">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php } ?>
		<div class="grid">
			<div class="col span-half">
				<div id="ticket-number">
					<h2>
						<span><?php echo Lang::txt('COM_SUPPORT_TICKET_NUMBER', ' '); ?></span><strong><a <?php echo ($tmpl) ? 'target="_parent"' : ''; ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=ticket&id=' . $this->ticket); ?>"><?php echo $this->ticket; ?></a></strong>
					</h2>
				</div>
			</div>
			<div class="col span-half omega">
				<div id="messagebox">
					<div class="wrap">
						<h3><?php echo Lang::txt('COM_SUPPORT_TROUBLE_THANKS'); ?></h3>
						<p><?php echo Lang::txt('COM_SUPPORT_TROUBLE_TICKET_TIMES'); ?></p>
					<?php if ($this->ticket) { ?>
						<p><?php echo Lang::txt('COM_SUPPORT_TROUBLE_TICKET_REFERENCE', $this->ticket); ?></p>
					<?php } ?>
					</div>
				</div>
				<p class="ticket-btn">
					<a class="btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=new'); ?>"><?php echo Lang::txt('COM_SUPPORT_NEW_REPORT'); ?></a>
				</p>
			</div><!-- / .col span-half omega -->
		</div><!-- / .grid -->
	</section><!-- / .main section -->
