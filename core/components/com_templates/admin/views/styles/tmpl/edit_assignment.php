<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Initiasile related data.
require_once Component::path('com_menus') . '/helpers/menus.php';
$menuTypes = \Components\Menus\Helpers\Menus::getMenuLinks();
?>
        <fieldset class="adminform">
            <legend><?php echo Lang::txt('COM_TEMPLATES_MENUS_ASSIGNMENT'); ?></legend>
            <label id="jform_menuselect-lbl" for="jform_menuselect">
                <?php echo Lang::txt('JGLOBAL_MENU_SELECTION'); ?>
            </label>

            <button type="button" class="jform-rightbtn">
                <?php echo Lang::txt('JGLOBAL_SELECTION_INVERT'); ?>
            </button>
            <div class="clr"></div>

            <div id="menu-assignment">
                <?php echo Html::tabs('start', 'module-menu-assignment-tabs', array('useCookie' => 1));?>
                <?php foreach ($menuTypes as &$type) :
                    $typeTitle = $type->title ? $type->title : $type->menutype;
                    echo Html::tabs(
                        'panel',
                        $typeTitle,
                        $type->menutype . '-details'
                    );
                    ?>
                    <ul class="menu-links">
                        <h3><?php echo $typeTitle; ?></h3>
                        <?php foreach ($type->links as $link) :
                            $linkVal = (int) $link->value;
                            $checked = ($link->template_style_id == $this->item->id)
                                ? ' checked="checked"'
                                : '';
                            $isLocked = $link->checked_out
                                && $link->checked_out != User::get('id');
                            $extra = $isLocked
                                ? ' disabled="disabled"'
                                : ' class="chk-menulink "';
                            ?>
                            <li class="menu-link">
                                <input
                                    type="checkbox"
                                    name="jform[assigned][]"
                                    value="<?php echo $linkVal; ?>"
                                    id="link<?php echo $linkVal; ?>"
                                    <?php echo $checked . $extra; ?>
                                />
                                <label for="link<?php echo $linkVal; ?>">
                                    <?php echo $link->text; ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="clr"></div>
                <?php endforeach; ?>
                <?php echo Html::tabs('end');?>
            </div>
        </fieldset>
