<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('framework');
?>

<?php if ($this->getError()) { ?>
    <p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<?php $formAction = Route::url('index.php?option=' . $this->option); ?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="component-form">
    <fieldset>
        <div class="configuration" >
            <?php echo Lang::txt('Add new users') ?>
        </div>
    </fieldset>

    <div class="col span12">
        <fieldset class="adminform">
            <div class="current">
            <?php

            echo '<p><strong>' . $this->matched . '</strong> user(s) added.</p>';
            if (count($this->noUserMatch) > 0) {
                $noMatchCount = count($this->noUserMatch);
                echo '<p><strong>' . $noMatchCount
                    . '</strong> user(s) could not be added'
                    . ' (no matching users):<br>';
            }

            $i = 0;
            foreach ($this->noUserMatch as $usr) {
                if ($i) {
                    echo ', ';
                }
                echo $usr;
                $i = 1;
            }

            echo '</p>';

            ?>
            </div>

        </fieldset>
    </div>

    <?php echo Html::input('token'); ?>
</form>
