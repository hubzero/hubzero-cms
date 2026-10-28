<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<section class="main section">
    <div class="section-inner">
        <h2><?php echo Lang::txt('COM_MASSMAIL_UNSUBSCRIBE_TITLE'); ?></h2>
        <?php if (!$this->valid) { ?>
            <p class="error"><?php echo Lang::txt('COM_MASSMAIL_UNSUBSCRIBE_INVALID'); ?></p>
        <?php } elseif ($this->done) { ?>
            <p class="passed"><?php echo Lang::txt('COM_MASSMAIL_UNSUBSCRIBE_OK', $this->escape($this->email)); ?></p>
        <?php } else { ?>
            <form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=unsubscribe'); ?>" method="post">
                <p><?php echo Lang::txt('COM_MASSMAIL_UNSUBSCRIBE_CONFIRM', $this->escape($this->email)); ?></p>
                <p>
                    <input type="hidden" name="e" value="<?php echo $this->escape($this->email); ?>" />
                    <input type="hidden" name="m" value="<?php echo (int) $this->mailing; ?>" />
                    <input type="hidden" name="t" value="<?php echo $this->escape($this->token); ?>" />
                    <input type="submit" class="btn" value="<?php echo $this->escape(Lang::txt('COM_MASSMAIL_UNSUBSCRIBE_BUTTON')); ?>" />
                </p>
            </form>
        <?php } ?>
    </div>
</section>
