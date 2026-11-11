<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{no_html: mixed, ticket: mixed, title: mixed} $this
 */
?>
<?php if ($this->no_html) { ?>
    <div id="report-response">
        <div>
            <p><?= Lang::txt('COM_SUPPORT_YOUR_TICKET') ?> # <span><a
                href="<?= Route::url('index.php?option=com_support&task=ticket&id=' . $this->ticket) ?>"
                title="View ticket"><?= $this->ticket ?></a></span></p>
            <?php
            $titleText = Lang::txt('COM_SUPPORT_NEW_REPORT');
            ?>
            <p><button class="btn btn-reset"
                title="<?= $titleText ?>"><?= Lang::txt('COM_SUPPORT_NEW_REPORT') ?></button></p>
        </div>
        <p>
            <?php echo Lang::txt('COM_SUPPORT_TROUBLE_THANKS'); ?><br /><br />
            <?php echo Lang::txt('COM_SUPPORT_TROUBLE_TICKET_TIMES'); ?>
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
        <p><?php echo Lang::txt('COM_SUPPORT_TROUBLE_THANKS'); ?></p>
        <p class="information"><?php echo Lang::txt('COM_SUPPORT_TROUBLE_TICKET_TIMES'); ?></p>
        <?php if ($this->ticket) { ?>
            <p><?php echo Lang::txt('COM_SUPPORT_TROUBLE_TICKET_REFERENCE', $this->ticket); ?></p>
        <?php } ?>
    </section><!-- / .main section -->
<?php }
