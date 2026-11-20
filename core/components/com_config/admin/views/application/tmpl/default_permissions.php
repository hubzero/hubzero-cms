<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{form: mixed} $this
 */
?>
<div class="width-100">
    <fieldset>
        <legend class="hidden"><span><?php echo Lang::txt('COM_CONFIG_PERMISSION_SETTINGS'); ?></span></legend>

        <?php foreach ($this->form->getFieldset('permissions') as $field) : ?>
            <?php //echo $field->label;?>
            <?php echo $field->input; ?>
        <?php endforeach; ?>
    </fieldset>
</div>
