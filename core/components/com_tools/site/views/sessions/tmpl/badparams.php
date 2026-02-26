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
        <?php
        $supportUrl = Route::url(
            'index.php?option=com_support&controller=tickets&task=new'
        );
        ?>
        <p><?php echo Lang::txt('COM_TOOLS_BADPARAMS_OPT_CONTACT_SUPPORT', $supportUrl); ?></p>
    </div><!-- / #error-box -->
</div><!-- / #error-wrap -->
