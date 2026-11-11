<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Module\Module&object{duration: mixed, message: mixed, moduleid: mixed, uri: mixed} $this
 */

$this->css();
$this->js();
?>
<div class="<?php echo $this->module->module; ?>" id="<?php echo $this->moduleid; ?>">
    <div class="<?php echo $this->module->module; ?>-message">
        <?php echo $this->message; ?>

        <a class="<?php echo $this->module->module; ?>-close"
            href="<?php echo $this->uri; ?>"
            data-duration="<?php echo $this->duration; ?>"
            title="<?php echo Lang::txt('MOD_EPRIVACY_CLOSE_TITLE'); ?>"
        >
            <span><?php echo Lang::txt('MOD_EPRIVACY_CLOSE'); ?></span>
        </a>
    </div>
</div>
