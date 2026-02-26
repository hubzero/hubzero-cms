<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Modules\Search\Helper;
use Hubzero\Facades\Route;

// no direct access
defined('_HZEXEC_') or die;

$inputId = 'searchword' . (Helper::$instances > 1 ? $this->module->id : '');
$formId  = 'searchform' . (Helper::$instances > 1 ? $this->module->id : '');
?>
<?php
$formSuffix = (Helper::$instances > 1) ? $this->module->id : '';
?>
<form action="<?php echo Route::url('index.php?option=com_search'); ?>"
    method="get"
    id="searchform<?php echo $formSuffix; ?>"
    class="<?php echo $moduleclass_sfx; ?>searchform"
>
    <fieldset>
        <legend><?php echo ($text ?: $label); ?></legend>

        <?php
            $sfx = Helper::$instances > 1 ? $this->module->id : '';
            $output = '<label for="searchword' . $sfx . '"'
                . ' class="' . $moduleclass_sfx . 'searchword-label"'
                . ' id="searchword-label' . $sfx . '">'
                . $label . '</label>';
            $output .= '<input type="text" name="terms"'
                . ' class="' . $moduleclass_sfx . 'searchword"'
                . ' id="searchword' . $sfx . '"'
                . ' size="' . $width . '"'
                . ' placeholder="' . $text . '" />';

        if ($button) :
            $button = '<input type="submit"'
                . ' class="' . $moduleclass_sfx . 'searchsubmit"'
                . ' id="submitquery' . $sfx . '"'
                . ' value="' . $button_text . '" />';
        endif;

        switch ($button_pos) :
            case 'top':
                $output = $button . '<br />' . $output;
                break;

            case 'bottom':
                $output = $output . '<br />' . $button;
                break;

            case 'right':
                $output = $output . $button;
                break;

            case 'left':
            default:
                $output = $button . $output;
                break;
        endswitch;

            echo $output;
        ?>
    </fieldset>
</form>
