<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

// No direct access
defined('_HZEXEC_') or die();
?>
<div class="width-100">
    <fieldset class="adminform">
        <legend><span><?php echo Lang::txt('COM_CONFIG_OTHER_SETTINGS', $this->section); ?></span></legend>

        <?php
        foreach ($this->values as $key => $val) :
            if (is_array($val)) :
                foreach ($val as $k => $v) :
                    $fieldId = 'hzform_' . $this->section . '_' . $key . '_' . $k;
                    $fieldName = 'hzother[' . $this->section . '][' . $key . '][' . $k . ']';
                    ?>
                    <div class="input-wrap">
                        <label for="<?php echo $fieldId; ?>"><?php echo $key; ?></label>
                        <input
                            type="text"
                            name="<?php echo $fieldName; ?>"
                            id="<?php echo $fieldId; ?>"
                            value="<?php echo $this->escape($v); ?>"
                        />
                    </div>
                    <?php
                endforeach;
            else :
                $fieldId = 'hzform_' . $this->section . '_' . $key;
                $fieldName = 'hzother[' . $this->section . '][' . $key . ']';
                ?>
                <div class="input-wrap">
                    <label for="<?php echo $fieldId; ?>"><?php echo $key; ?></label>
                    <input
                        type="text"
                        name="<?php echo $fieldName; ?>"
                        id="<?php echo $fieldId; ?>"
                        value="<?php echo $this->escape($val); ?>"
                    />
                </div>
                <?php
            endif;
        endforeach;
        ?>
    </fieldset>
</div>
