<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Collections\Helpers\Permissions::getActions('collection');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_COLLECTIONS') . ': ' . $text, 'collection');
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('collection');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$formAction = Route::url(
    'index.php?option=' . $this->option . '&controller=' . $this->controller
);
$invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
?>

<form action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    class="editform form-validate"
    id="item-form"
    data-invalid-msg="<?php echo $invalidMsg; ?>">
    <?php if ($this->getError()) { ?>
        <p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
    <?php } ?>
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

                <div class="grid">
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php
                            $ownerTypeLabel = Lang::txt('COM_COLLECTIONS_FIELD_OWNER_TYPE');
                            $requiredTxt = Lang::txt('JOPTION_REQUIRED');
                            ?>
                            <label for="field-object_type">
                                <?php echo $ownerTypeLabel; ?>:
                                <span class="required"><?php echo $requiredTxt; ?></span>
                            </label><br />
                            <select name="fields[object_type]"
                                id="field-object_type"
                                class="required">
                                <?php
                                $memberSel = ($this->row->get('object_type') == 'member')
                                    ? ' selected="selected"' : '';
                                $groupSel = ($this->row->get('object_type') == 'group')
                                    ? ' selected="selected"' : '';
                                $memberTxt = Lang::txt('COM_COLLECTIONS_FIELD_OWNER_TYPE_MEMBER');
                                $groupTxt = Lang::txt('COM_COLLECTIONS_FIELD_OWNER_TYPE_GROUP');
                                ?>
                                <option value="member"<?php echo $memberSel; ?>>
                                    <?php echo $memberTxt; ?>
                                </option>
                                <option value="group"<?php echo $groupSel; ?>>
                                    <?php echo $groupTxt; ?>
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col span6">
                        <?php $ownerIdHint = Lang::txt('COM_COLLECTIONS_FIELD_OWNER_ID_HINT'); ?>
                        <div class="input-wrap"
                            data-hint="<?php echo $ownerIdHint; ?>">
                            <?php
                            $ownerIdLabel = Lang::txt('COM_COLLECTIONS_FIELD_OWNER_ID');
                            $objectIdVal = $this->escape(
                                stripslashes($this->row->get('object_id'))
                            );
                            ?>
                            <label for="field-object_id">
                                <?php echo $ownerIdLabel; ?>:
                                <span class="required"><?php echo $requiredTxt; ?></span>
                            </label>
                            <input type="text"
                                name="fields[object_id]"
                                id="field-object_id"
                                class="required"
                                maxlength="250"
                                value="<?php echo $objectIdVal; ?>" />
                            <span class="hint"><?php echo $ownerIdHint; ?></span>
                        </div>
                    </div>
                </div>

                <div class="input-wrap">
                    <?php
                    $titleLabel = Lang::txt('COM_COLLECTIONS_FIELD_TITLE');
                    $titleVal = $this->escape(stripslashes($this->row->get('title')));
                    ?>
                    <label for="field-title">
                        <?php echo $titleLabel; ?>:
                        <span class="required"><?php echo $requiredTxt; ?></span>
                    </label>
                    <input type="text"
                        name="fields[title]"
                        id="field-title"
                        class="required"
                        maxlength="250"
                        value="<?php echo $titleVal; ?>" />
                </div>

                <?php $aliasHint = Lang::txt('COM_COLLECTIONS_FIELD_ALIAS_HINT'); ?>
                <div class="input-wrap"
                    data-hint="<?php echo $aliasHint; ?>">
                    <?php $aliasLabel = Lang::txt('COM_COLLECTIONS_FIELD_ALIAS'); ?>
                    <?php $aliasVal = $this->escape(stripslashes($this->row->get('alias'))); ?>
                    <label for="field-alias"><?php echo $aliasLabel; ?>:</label>
                    <input type="text"
                        name="fields[alias]"
                        id="field-alias"
                        size="30"
                        maxlength="250"
                        value="<?php echo $aliasVal; ?>" />
                    <span class="hint"><?php echo $aliasHint; ?></span>
                </div>

                <div class="input-wrap">
                    <?php $descLabel = Lang::txt('COM_COLLECTIONS_FIELD_DESCRIPTION'); ?>
                    <label for="field-description"><?php echo $descLabel; ?></label>
                    <?php
                    echo $this->editor(
                        'fields[description]',
                        $this->escape($this->row->get('description')),
                        35,
                        10,
                        'field-description',
                        array('class' => 'required minimal no-footer', 'buttons' => false)
                    );
                    ?>
                </div>

                <div class="grid">
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php $layoutLabel = Lang::txt('COM_COLLECTIONS_FIELD_LAYOUT'); ?>
                            <label for="field-layout"><?php echo $layoutLabel; ?></label>
                            <select name="fields[layout]" id="field-layout">
                                <?php
                                $gridSel = ($this->row->get('layout') == 'grid')
                                    ? ' selected="selected"' : '';
                                $listSel = ($this->row->get('layout') == 'list')
                                    ? ' selected="selected"' : '';
                                $gridTxt = Lang::txt('COM_COLLECTIONS_FIELD_LAYOUT_GRID');
                                $listTxt = Lang::txt('COM_COLLECTIONS_FIELD_LAYOUT_LIST');
                                ?>
                                <option value="grid"<?php echo $gridSel; ?>>
                                    <?php echo $gridTxt; ?>
                                </option>
                                <option value="list"<?php echo $listSel; ?>>
                                    <?php echo $listTxt; ?>
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php $sortLabel = Lang::txt('COM_COLLECTIONS_FIELD_SORT'); ?>
                            <label for="field-sort"><?php echo $sortLabel; ?></label>
                            <select name="fields[sort]" id="field-sort">
                                <?php
                                $createdSel = ($this->row->get('sort') == 'created')
                                    ? ' selected="selected"' : '';
                                $orderingSel = ($this->row->get('sort') == 'ordering')
                                    ? ' selected="selected"' : '';
                                $createdTxt = Lang::txt('COM_COLLECTIONS_FIELD_SORT_CREATED');
                                $orderingTxt = Lang::txt('COM_COLLECTIONS_FIELD_SORT_ORDERING');
                                ?>
                                <option value="created"<?php echo $createdSel; ?>>
                                    <?php echo $createdTxt; ?>
                                </option>
                                <option value="ordering"<?php echo $orderingSel; ?>>
                                    <?php echo $orderingTxt; ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="col span5">
            <table class="meta">
                <tbody>
                    <tr>
                        <?php $creatorLabel = Lang::txt('COM_COLLECTIONS_FIELD_CREATOR'); ?>
                        <th class="key"><?php echo $creatorLabel; ?>:</th>
                        <td>
                            <?php
                            $editor = User::getInstance($this->row->get('created_by'));
                            echo $this->escape(stripslashes($editor->get('name')));
                            $createdByVal = $this->escape($this->row->get('created_by'));
                            ?>
                            <input type="hidden"
                                name="fields[created_by]"
                                id="field-created_by"
                                value="<?php echo $createdByVal; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <?php $createdLabel = Lang::txt('COM_COLLECTIONS_FIELD_CREATED'); ?>
                        <th class="key"><?php echo $createdLabel; ?>:</th>
                        <td>
                            <?php echo Date::of($this->row->get('created'))->toLocal(); ?>
                            <?php $createdVal = $this->escape($this->row->get('created')); ?>
                            <input type="hidden"
                                name="fields[created]"
                                id="field-created"
                                value="<?php echo $createdVal; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <?php $likesLabel = Lang::txt('COM_COLLECTIONS_FIELD_LIKES'); ?>
                        <th class="key"><?php echo $likesLabel; ?>:</th>
                        <td>
                            <?php echo $this->row->get('positive', 0); ?>
                            <?php $positiveVal = $this->escape($this->row->get('positive', 0)); ?>
                            <input type="hidden"
                                name="fields[positive]"
                                id="field-positive"
                                value="<?php echo $positiveVal; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <?php $postsLabel = Lang::txt('COM_COLLECTIONS_FIELD_POSTS'); ?>
                        <th class="key"><?php echo $postsLabel; ?>:</th>
                        <td>
                            <?php echo $this->row->posts()->total(); ?>
                        </td>
                    </tr>
                    <tr>
                        <?php $followersLabel = Lang::txt('COM_COLLECTIONS_FIELD_FOLLOWERS'); ?>
                        <th class="key"><?php echo $followersLabel; ?>:</th>
                        <td>
                            <?php //echo $this->row->count('followers'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <fieldset class="adminform">
                <?php $pubLabel = Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?>
                <legend><span><?php echo $pubLabel; ?></span></legend>

                <div class="input-wrap">
                    <?php $stateLabel = Lang::txt('COM_COLLECTIONS_FIELD_STATE'); ?>
                    <label for="field-state"><?php echo $stateLabel; ?>:</label>
                    <select name="fields[state]" id="field-state">
                        <?php
                        $s0 = ($this->row->get('state') == 0) ? ' selected="selected"' : '';
                        $s1 = ($this->row->get('state') == 1) ? ' selected="selected"' : '';
                        $s2 = ($this->row->get('state') == 2) ? ' selected="selected"' : '';
                        ?>
                        <option value="0"<?php echo $s0; ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
                        <option value="1"<?php echo $s1; ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
                        <option value="2"<?php echo $s2; ?>><?php echo Lang::txt('JTRASHED'); ?></option>
                    </select>
                </div>

                <div class="input-wrap">
                    <?php $accessLabel = Lang::txt('COM_COLLECTIONS_FIELD_ACCESS'); ?>
                    <label for="field-access"><?php echo $accessLabel; ?>:</label>
                    <select name="fields[access]" id="field-access">
                        <?php
                        $a0 = ($this->row->get('access') == 0) ? ' selected="selected"' : '';
                        $a1 = ($this->row->get('access') == 1) ? ' selected="selected"' : '';
                        $a4 = ($this->row->get('access') == 4) ? ' selected="selected"' : '';
                        $pubTxt = Lang::txt('COM_COLLECTIONS_ACCESS_PUBLIC');
                        $regTxt = Lang::txt('COM_COLLECTIONS_ACCESS_REGISTERED');
                        $privTxt = Lang::txt('COM_COLLECTIONS_ACCESS_PRIVATE');
                        ?>
                        <option value="0"<?php echo $a0; ?>><?php echo $pubTxt; ?></option>
                        <option value="1"<?php echo $a1; ?>><?php echo $regTxt; ?></option>
                        <option value="4"<?php echo $a4; ?>><?php echo $privTxt; ?></option>
                    </select>
                </div>
            </fieldset>
        </div>
    </div>

    <input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
