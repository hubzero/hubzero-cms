<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\Event;

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Tags\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_TAGS') . ': ' . Lang::txt('COM_TAGS_MERGE'), 'tags');
if ($canDo->get('core.edit')) {
    Toolbar::save('merge');
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('merge');
?>

<?php $mergeAction = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>
<form action="<?php echo $mergeAction; ?>" method="post" name="adminForm" class="editform" id="item-form">
    <p class="warning"><?php echo Lang::txt('COM_TAGS_MERGED_EXPLANATION'); ?></p>

    <div class="grid">
        <div class="col span6">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_TAGS_MERGING'); ?></span></legend>

                <div class="input-wrap">
                    <ul>
                        <?php
                        foreach ($this->tags as $tag) {
                            $rawTag = $this->escape(stripslashes($tag->get('raw_tag')));
                            $normTag = $this->escape($tag->get('tag'));
                            $total = $tag->objects()->total();
                            echo '<li>' . $rawTag . ' (' . $normTag . ' - ' . $total . ')</li>' . "\n";
                        }
                        ?>
                    </ul>
                </div>
            </fieldset>
        </div>
        <div class="col span6">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_TAGS_MERGE_TO'); ?></span></legend>

                <div class="input-wrap">
                    <label for="newtag"><?php echo Lang::txt('COM_TAGS_TAG'); ?>:</label><br />
                    <?php
                    $tf = Event::trigger(
                        'hubzero.onGetMultiEntry',
                        array(
                            array('tags', 'newtag', 'newtag')
                        )
                    );
                    if (count($tf)) {
                        echo implode("\n", $tf);
                    } else {
                        echo '<input type="text" name="newtag" id="newtag" size="25" value="" />';
                    }
                    ?>
                </div>
                <p><?php echo Lang::txt('COM_TAGS_SELECT_TAG'); ?></p>
            </fieldset>
        </div>
    </div>

    <input type="hidden" name="ids" value="<?php echo $this->idstr; ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="step" value="<?php echo $this->step; ?>" />
    <input type="hidden" name="task" value="merge" />

    <?php echo Html::input('token'); ?>
</form>