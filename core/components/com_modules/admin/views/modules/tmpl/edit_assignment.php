<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Initiasile related data.
$menuTypes = \Components\Menus\Helpers\Menus::getMenuLinks();

$assignment = $this->item->disableCaching()->purgeCache()->menuAssignment();
?>
        <fieldset class="adminform">
            <legend><span><?php echo Lang::txt('COM_MODULES_MENU_ASSIGNMENT'); ?></span></legend>

            <div class="input-wrap">
                <?php $assignLabel = Lang::txt('COM_MODULES_MODULE_ASSIGN'); ?>
                <label id="jform_menus-lbl" for="jform_assignment"><?php echo $assignLabel; ?></label>
            <!-- <fieldset id="jform_menus" class="radio"> -->
                <select name="menu[assignment]" id="jform_assignment">
                    <?php
                    $assignOpts = \Components\Modules\Helpers\Modules::getAssignmentOptions(
                        $this->item->client_id
                    );
                    echo Html::select(
                        'options',
                        $assignOpts,
                        'value',
                        'text',
                        $assignment,
                        true
                    );
                    ?>
                </select>
            <!-- </fieldset> -->
            </div>

            <div class="input-wrap">
                <?php $menuSelectLabel = Lang::txt('JGLOBAL_MENU_SELECTION'); ?>
                <label id="jform_menuselect-lbl" for="jform_menuselect"><?php echo $menuSelectLabel; ?></label>

                <?php $invertJs = "\$('.chkbox').each(function(i, el) { el.checked = !el.checked; });"; ?>
                <button
                    type="button"
                    class="jform-assignments-button jform-rightbtn"
                    onclick="<?php echo $invertJs; ?>"
                >
                    <?php echo Lang::txt('JGLOBAL_SELECTION_INVERT'); ?>
                </button>

                <?php $noneJs = "\$('.chkbox').each(function(i, el) { el.checked = false; });"; ?>
                <button
                    type="button"
                    class="jform-assignments-button jform-rightbtn"
                    onclick="<?php echo $noneJs; ?>"
                >
                    <?php echo Lang::txt('JGLOBAL_SELECTION_NONE'); ?>
                </button>

                <?php $allJs = "\$('.chkbox').each(function(i, el) { el.checked = true; });"; ?>
                <button
                    type="button"
                    class="jform-assignments-button jform-rightbtn"
                    onclick="<?php echo $allJs; ?>"
                >
                    <?php echo Lang::txt('JGLOBAL_SELECTION_ALL'); ?>
                </button>
            </div>

            <div class="clr"></div>

            <div id="menu-assignment">

            <?php echo Html::tabs('start', 'module-menu-assignment-tabs', array('useCookie' => 1));?>

            <?php foreach ($menuTypes as &$type) :
                echo Html::tabs('panel', $type->title ? $type->title : $type->menutype, $type->menutype . '-details');

                $chkbox_class = 'chk-menulink-' . $type->id; ?>

                <?php $clsInvert = "\$('.{$chkbox_class}').each(function(i, el) { el.checked = !el.checked; });"; ?>
                <button
                    type="button"
                    class="jform-assignments-button jform-rightbtn"
                    onclick="<?php echo $clsInvert; ?>"
                >
                    <?php echo Lang::txt('JGLOBAL_SELECTION_INVERT'); ?>
                </button>

                <?php $clsNone = "\$('.{$chkbox_class}').each(function(i, el) { el.checked = false; });"; ?>
                <button
                    type="button"
                    class="jform-assignments-button jform-rightbtn"
                    onclick="<?php echo $clsNone; ?>"
                >
                    <?php echo Lang::txt('JGLOBAL_SELECTION_NONE'); ?>
                </button>

                <?php $clsAll = "\$('.{$chkbox_class}').each(function(i, el) { el.checked = true; });"; ?>
                <button
                    type="button"
                    class="jform-assignments-button jform-rightbtn"
                    onclick="<?php echo $clsAll; ?>"
                >
                    <?php echo Lang::txt('JGLOBAL_SELECTION_ALL'); ?>
                </button>

                <div class="clr"></div>

                <?php
                $count = count($type->links);
                $i     = 0;
                if ($count) :
                    ?>
                <ul class="menu-links">
                    <?php
                    foreach ($type->links as $link) :
                        if (trim($assignment) == '-') :
                            $checked = '';
                        elseif ($assignment == 0) :
                            $checked = ' checked="checked"';
                        elseif ($assignment < 0) :
                            $checked = in_array(-$link->value, $this->item->menuAssigned()) ? ' checked="checked"' : '';
                        elseif ($assignment > 0) :
                            $checked = in_array($link->value, $this->item->menuAssigned()) ? ' checked="checked"' : '';
                        endif;
                        ?>
                    <li class="menu-link">
                        <?php $linkVal = (int) $link->value; ?>
                        <input
                            type="checkbox"
                            class="chkbox <?php echo $chkbox_class; ?>"
                            name="menu[assigned][]"
                            value="<?php echo $linkVal; ?>"
                            id="link<?php echo $linkVal; ?>"
                            <?php echo $checked; ?>
                        />
                        <label for="link<?php echo (int) $link->value;?>">
                            <?php echo $link->text; ?>
                        </label>
                    </li>
                        <?php if ($count > 20 && ++$i == ceil($count / 2)) :?>
                    </ul><ul class="menu-links">
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <div class="clr"></div>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php echo Html::tabs('end');?>

            </div>
        </fieldset>
