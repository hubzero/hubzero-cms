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

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));
$label = Lang::txt('COM_PUBLICATIONS_PUBLICATION');
$label2 = Lang::txt('COM_PUBLICATIONS_MASTER_TYPE');
Toolbar::title(
    $label . ' ' . $label2 . ': ' . $text,
    'publications'
);
if ($this->row->id) {
    Toolbar::apply();
}
Toolbar::save();
Toolbar::cancel();

$params = new \Hubzero\Config\Registry($this->row->params);

// Get curator group cn
$curatorGroup = '';
if ($this->row->curatorgroup && $group = \Hubzero\User\Group::getInstance($this->row->curatorgroup)) {
    $curatorGroup = $group->get('cn');
}
$manifest  = $this->curation->_manifest;
$curParams = $manifest->params;
$blocks    = $manifest->blocks;

$blockSelection = array('active' => array());
$masterBlocks = array();
foreach ($this->blocks as $b) {
    $masterBlocks[$b->block] = $b;
}

?>

<form
    action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"
    method="post"
    id="item-form"
    name="adminForm"
>
    <div class="grid">
        <div class="col span6">
            <?php if ($this->row->id) { ?>
                <table class="meta">
                    <tbody>
                        <tr>
                            <th><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ID'); ?></th>
                            <td><?php echo $this->row->id; ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php } ?>
            <fieldset class="adminform">
                <input type="hidden" name="fields[ordering]" value="<?php echo $this->row->ordering; ?>" />
                <input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
                <input type="hidden" name="task" value="save" />
                <legend><span><?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_INFO'); ?></span></legend>

                <div class="input-wrap">
                                        <label for="field-type">
                        <?php echo Lang::txt('COM_PUBLICATIONS_FIELD_NAME'); ?>:<span class="required">
                        <?php echo Lang::txt('JOPTION_REQUIRED'); ?>
                        </span>
                        </label>
                    <input
                        type="text"
                        name="fields[type]"
                        id="field-type"
                        maxlength="100"
                        value="<?php echo $this->escape($this->row->type); ?>"
                    />
                </div>
                <div class="input-wrap">
                    <label for="field-alias"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ALIAS'); ?>:</label>
                    <input
                        type="text"
                        name="fields[alias]"
                        id="field-alias"
                        maxlength="100"
                        value="<?php echo $this->escape($this->row->alias); ?>"
                    />
                </div>
                <div class="input-wrap">
                    <?php $langTxt = Lang::txt('COM_PUBLICATIONS_FIELD_DESCRIPTION'); ?>
                    <label for="field-description"><?php echo $langTxt; ?>:</label>
                    <input
                        type="text"
                        name="fields[description]"
                        id="field-description"
                        maxlength="255"
                        value="<?php echo $this->escape($this->row->description); ?>"
                    />
                </div>
                <div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_OFFER_CHOICE'); ?>">
                    <?php $langTxt = Lang::txt('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE'); ?>
                    <label for="field-contributable"><?php echo $langTxt; ?></label>
                    <select name="fields[contributable]" id="field-contributable">
                        <option value="0" <?php echo $this->row->contributable == 0 ? ' selected="selected"' : ''; ?>>
                            <?php echo Lang::txt('JNO'); ?></option>
                        <option value="1" <?php echo $this->row->contributable == 1 ? ' selected="selected"' : ''; ?>>
                            <?php echo Lang::txt('JYES'); ?></option>
                    </select>
                </div>
            </fieldset>
        </div>
        <div class="col span6">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_CURATION_CONFIG'); ?></span></legend>
                <?php if ($this->row->id) { ?>
                    <div class="input-wrap">
                        <?php $langTxt = Lang::txt('COM_PUBLICATIONS_FIELD_CURATOR_GROUP'); ?>
                        <label for="field-curatorgroup"><?php echo $langTxt; ?>:</label>
                        <input
                            type="text"
                            name="curatorgroup"
                            id="field-curatorgroup"
                            maxlength="255"
                            value="<?php echo $curatorGroup; ?>"
                        />
                    </div>
                    <div class="input-wrap">
                        <?php $langTxt = Lang::txt('COM_PUBLICATIONS_CURATION_DEFAULT_TITLE'); ?>
                        <label for="field-defaulttitle"><?php echo $langTxt; ?>:</label>
                        <input
                            type="text"
                            name="curation[params][default_title]"
                            id="field-defaulttitle"
                            maxlength="255"
                            value="<?php echo $curParams->default_title;  ?>"
                        />
                    </div>
                    <div class="input-wrap">
                        <?php $langTxt = Lang::txt('COM_PUBLICATIONS_CURATION_DEFAULT_CATEGORY'); ?>
                        <label for="field-defaultcategory"><?php echo $langTxt; ?>:</label>
                        <select name="curation[params][default_category]" id="field-defaultcategory">
                        <?php foreach ($this->cats as $cat) { ?>
                            <?php $isSel = $curParams->default_category == $cat->id; ?>
                            <option
                                value="<?php echo $cat->id; ?>"
                                <?php echo $isSel ? 'selected="selected"' : ''; ?>
                            ><?php echo $cat->name; ?></option>
                        <?php } ?>
                        </select>
                    </div>
                    <?php $langTxt = Lang::txt('COM_PUBLICATIONS_CURATION_REQUIRE_DOI_HINT'); ?>
                    <div class="input-wrap" data-hint="<?php echo $langTxt; ?>">
                        <?php $langTxt = Lang::txt('COM_PUBLICATIONS_CURATION_REQUIRE_DOI'); ?>
                        <label for="field-requiredoi"><?php echo $langTxt; ?></label>

                        <select name="curation[params][require_doi]" id="field-requiredoi">
                            <option value="0" <?php echo $curParams->require_doi == 0 ? ' selected="selected"' : ''; ?>>
                                <?php echo Lang::txt('COM_PUBLICATIONS_CURATION_REQUIRE_DOI_NO'); ?></option>
                            <option value="1" <?php echo $curParams->require_doi == 1 ? ' selected="selected"' : ''; ?>>
                                <?php echo Lang::txt('COM_PUBLICATIONS_CURATION_REQUIRE_DOI_YES'); ?></option>
                            <option value="2" <?php echo $curParams->require_doi == 2 ? ' selected="selected"' : ''; ?>>
                                <?php echo Lang::txt('COM_PUBLICATIONS_CURATION_REQUIRE_DOI_OPTIONAL'); ?></option>
                        </select>
                    </div>
                    <?php $langTxt = Lang::txt('COM_PUBLICATIONS_CURATION_SHOW_ARCHIVAL_HINT'); ?>
                    <div class="input-wrap" data-hint="<?php echo $langTxt; ?>">
                        <?php $langTxt = Lang::txt('COM_PUBLICATIONS_CURATION_SHOW_ARCHIVAL'); ?>
                        <label for="field-showarchive"><?php echo $langTxt; ?></label>
                        <select name="curation[params][show_archival]" id="field-showarchive">
                            <?php
                            $sa = isset($curParams->show_archival)
                                ? $curParams->show_archival : 0;
                            ?>
                            <option value="0"<?php echo ($sa == 0) ? ' selected="selected"' : ''; ?>>
                                <?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SHOW_ARCHIVAL_NO'); ?>
                            </option>
                            <option value="1"<?php echo ($sa == 1) ? ' selected="selected"' : ''; ?>>
                                <?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SHOW_ARCHIVAL_YES'); ?>
                            </option>
                            <option value="2"<?php echo ($sa == 2) ? ' selected="selected"' : ''; ?>>
                                <?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SHOW_ARCHIVAL_PARTIAL'); ?>
                            </option>
                        </select>
                    </div>
                    <div class="input-wrap">
                        <?php $langTxt = Lang::txt('COM_PUBLICATIONS_CURATION_LIST_ALL'); ?>
                        <label for="field-listall"><?php echo $langTxt; ?></label>
                        <select name="curation[params][list_all]" id="field-listall">
                            <?php $la = isset($curParams->list_all)
                                ? $curParams->list_all : 0; ?>
                            <option value="0"<?php echo ($la == 0) ? ' selected="selected"' : ''; ?>>
                                <?php echo Lang::txt('JNO'); ?></option>
                            <option value="1"<?php echo ($la == 1) ? ' selected="selected"' : ''; ?>>
                                <?php echo Lang::txt('JYES'); ?></option>
                        </select>
                    </div>
                    <div class="input-wrap">
                        <?php $langTxt = Lang::txt('COM_PUBLICATIONS_CURATION_LIST_LABEL'); ?>
                        <label for="field-listlabel"><?php echo $langTxt; ?>:</label>
                        <?php
                        $listLabel = (isset($curParams->list_label) && $curParams->list_label)
                            ? $curParams->list_label : '';
                        ?>
                        <input
                            type="text"
                            name="curation[params][list_label]"
                            id="field-listlabel"
                            maxlength="255"
                            value="<?php echo $listLabel; ?>"
                        />
                    </div>
                    <div class="input-wrap">
                        <?php $langTxt = Lang::txt('COM_PUBLICATIONS_CURATION_AUTO_APPROVE'); ?>
                        <label for="field-autoapprove"><?php echo $langTxt; ?></label>
                        <select name="curation[params][auto_approve]" id="field-autoapprove">
                            <?php $aa = isset($curParams->auto_approve)
                                ? $curParams->auto_approve : 0; ?>
                            <option value="0"<?php echo ($aa == 0) ? ' selected="selected"' : ''; ?>>
                                <?php echo Lang::txt('JNO'); ?></option>
                            <option value="1"<?php echo ($aa == 1) ? ' selected="selected"' : ''; ?>>
                                <?php echo Lang::txt('JYES'); ?></option>
                        </select>
                    </div>
                    <div class="input-wrap">
                        <?php $langTxt = Lang::txt('COM_PUBLICATIONS_CURATION_REQUEST_REVIEW'); ?>
                        <label for="field-requestreview"><?php echo $langTxt; ?></label>
                        <select name="curation[params][request_review]" id="field-requestreview">
                            <?php $rr = isset($curParams->request_review)
                                ? $curParams->request_review : 0; ?>
                            <option value="0"<?php echo ($rr == 0) ? ' selected="selected"' : ''; ?>>
                                <?php echo Lang::txt('JNO'); ?></option>
                            <option value="1"<?php echo ($rr == 1) ? ' selected="selected"' : ''; ?>>
                                <?php echo Lang::txt('JYES'); ?></option>
                        </select>
                    </div>
                <?php } else {
                    echo '<p class="warning">' . Lang::txt('COM_PUBLICATIONS_CURATION_SAVE_NEW') . '</p>';
                } ?>
            </fieldset>
        </div>
    </div>

    <?php if ($this->row->id) {
        $i = 1; ?>
        <fieldset class="adminform">
            <?php
            $addBlockUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=addblock&id=' . $this->row->id
            );
            $blocksLabel = Lang::txt('COM_PUBLICATIONS_FIELD_CURATION_BLOCKS');
            $addLabel = Lang::txt('COM_PUBLICATIONS_FIELD_CURATION_ADD_BLOCK');
            ?>
            <legend><span><?php echo $blocksLabel; ?></span>
                <a class="editthis" href="<?php echo $addBlockUrl; ?>">[<?php echo $addLabel; ?>]</a></legend>
            <?php foreach ($blocks as $blockId => $block) {
                $blockMaster = $masterBlocks[$block->name];
                ?>
            <fieldset class="adminform">
                <legend><span class="block-id"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ID') . ': ' . $blockId; ?> -
                    <?php echo $block->name; ?></span></legend>
                <div class="grid">
                    <div class="col span2">
                        <?php
                        $activeChecked = (!isset($block->active) || $block->active == 1)
                            ? ' checked="checked"' : '';
                        $inactiveChecked = (isset($block->active) && $block->active == 0)
                            ? ' checked="checked"' : '';
                        $disabled = ($blockMaster->minimum > 0
                            && !in_array($block->name, $blockSelection['active']))
                            ? ' disabled="disabled"' : '';
                        $radioName = 'curation[blocks][' . $blockId . '][active]';
                        $activeLabel = Lang::txt('COM_PUBLICATIONS_STATUS_ACTIVE');
                        $inactiveLabel = Lang::txt('COM_PUBLICATIONS_STATUS_INACTIVE');
                        ?>
                        <div class="input-wrap">
                            <label class="block">
                                <input
                                    type="radio"
                                    name="<?php echo $radioName; ?>"
                                    value="1"<?php echo $activeChecked; ?>
                                /> <?php echo $activeLabel; ?>
                            </label>
                            <label class="block">
                                <input
                                    type="radio"
                                    name="<?php echo $radioName; ?>"
                                    value="0"<?php echo $inactiveChecked; ?><?php echo $disabled; ?>
                                /> <?php echo $inactiveLabel; ?>
                            </label>
                        </div>
                        <?php
                        $editOrderUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&task=editblockorder&id=' . $this->row->id
                        );
                        $removeUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&task=removeblock&id=' . $this->row->id
                            . '&blockid=' . $blockId
                        );
                        $editLabel = Lang::txt('COM_PUBLICATIONS_EDIT');
                        $deleteLabel = Lang::txt('COM_PUBLICATIONS_DELETE');
                        ?>
                        <div class="input-wrap tweakblock">
                            <label><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ORDER'); ?>: <?php echo $i; ?>
                                <a href="<?php echo $editOrderUrl; ?>">[<?php echo $editLabel; ?>]</a>
                                <a href="<?php echo $removeUrl; ?>">[<?php echo $deleteLabel; ?>]</a>
                            </label>
                        </div>
                    </div>
                    <div class="col span5 blockprop">
                        <h5><?php echo Lang::txt('COM_PUBLICATIONS_BLOCK_PROPERTIES'); ?></h5>
                        <div class="input-wrap">
                            <?php $langTxt = Lang::txt('COM_PUBLICATIONS_FIELD_BLOCK_LABEL'); ?>
                            <label for="field-block-<?php echo $blockId; ?>-label"><?php echo $langTxt; ?>:</label>
                            <input
                                type="text"
                                name="curation[blocks][<?php echo $blockId; ?>][label]"
                                id="field-block-<?php echo $blockId; ?>-label"
                                maxlength="255"
                                value="<?php echo $block->label;  ?>"
                            />
                        </div>
                        <div class="input-wrap">
                            <?php $langTxt = Lang::txt('COM_PUBLICATIONS_FIELD_TITLE'); ?>
                            <label for="field-block-<?php echo $blockId; ?>-title"><?php echo $langTxt; ?>:</label>
                            <input
                                type="text"
                                name="curation[blocks][<?php echo $blockId; ?>][title]"
                                id="field-block-<?php echo $blockId; ?>-title"
                                maxlength="255"
                                value="<?php echo $block->title;  ?>"
                            />
                        </div>
                        <div class="input-wrap">
                                                        <label for="field-block-<?php echo $blockId; ?>-draftHeading">
                                <?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DRAFT_HEADING'); ?>:</label>
                            <input
                                type="text"
                                name="curation[blocks][<?php echo $blockId; ?>][draftHeading]"
                                id="field-block-<?php echo $blockId; ?>-draftHeading"
                                maxlength="255"
                                value="<?php echo $block->draftHeading;  ?>"
                            />
                        </div>
                        <div class="input-wrap">
                                                        <label for="field-block-<?php echo $blockId; ?>-draftTagline">
                                <?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DRAFT_TAGLINE'); ?>:</label>
                            <input
                                type="text"
                                name="curation[blocks][<?php echo $blockId; ?>][draftTagline]"
                                id="field-block-<?php echo $blockId; ?>-draftTagline"
                                maxlength="255"
                                value="<?php echo $block->draftTagline;  ?>"
                            />
                        </div>
                        <div class="input-wrap">
                            <?php $langTxt = Lang::txt('COM_PUBLICATIONS_FIELD_BLOCK_ABOUT'); ?>
                            <label for="field-block-<?php echo $blockId; ?>-about"><?php echo $langTxt; ?>:</label>
                            <textarea name="curation[blocks][<?php echo $blockId; ?>][about]" id="field-block-
                                <?php echo $blockId; ?>-about"><?php echo $block->about;  ?></textarea>
                        </div>
                        <div class="input-wrap">
                            <?php $langTxt = Lang::txt('COM_PUBLICATIONS_FIELD_BLOCK_ADMIN_TIPS'); ?>
                            <label for="field-block-<?php echo $blockId; ?>-adminTips"><?php echo $langTxt; ?>:</label>
                            <textarea name="curation[blocks][<?php echo $blockId; ?>][adminTips]" id="field-block-
                                <?php echo $blockId; ?>-adminTips"><?php echo $block->adminTips;  ?></textarea>
                        </div>
                    </div>
                    <div class="col span5 blockparams">
                        <h5><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_PARAMS'); ?></h5>
                        <?php foreach ($block->params as $paramname => $paramvalue) { ?>
                            <?php
                            $paramLabel = Lang::txt(
                                'COM_PUBLICATIONS_FIELD_PARAMS_' . strtoupper($paramname)
                            );
                            $inputName = 'curation[blocks][' . $blockId
                                . '][params][' . $paramname . ']';
                            ?>
                            <div class="input-wrap">
                                <label><?php echo $paramLabel; ?></label>
                                <?php
                                if (is_array($paramvalue)) {
                                    $val = implode(',', $paramvalue);
                                    ?>
                                <input
                                    type="text"
                                    name="<?php echo $inputName; ?>"
                                    value="<?php echo $val; ?>"
                                />
                                <?php } elseif (is_numeric($paramvalue)) { ?>
                                    <?php $sel0 = $paramvalue == 0 ? ' selected="selected"' : ''; ?>
                                    <?php $sel1 = $paramvalue == 1 ? ' selected="selected"' : ''; ?>
                                <select name="<?php echo $inputName; ?>">
                                    <option value="0"<?php echo $sel0; ?>>
                                        <?php echo Lang::txt('JNO'); ?>
                                    </option>
                                    <option value="1"<?php echo $sel1; ?>>
                                        <?php echo Lang::txt('JYES'); ?>
                                    </option>
                                </select>
                                <?php } else { ?>
                                    <input
                                        type="text"
                                        name="<?php echo $inputName; ?>"
                                        value="<?php echo $paramvalue; ?>"
                                    />
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if ($block->elements) { ?>
                            <?php
                            $editElUrl = Route::url(
                                'index.php?option=' . $this->option
                                . '&controller=' . $this->controller
                                . '&task=editelements&id=' . $this->row->id
                                . '&bid=' . $blockId
                            );
                            $elLabel = Lang::txt('COM_PUBLICATIONS_FIELD_BLOCK_ELEMENTS');
                            ?>
                            <h5><?php echo $elLabel; ?>
                                <span class="editthis">
                                    <?php $editTxt = Lang::txt('COM_PUBLICATIONS_EDIT'); ?>
                                    <a href="<?php echo $editElUrl; ?>">[<?php echo $editTxt; ?>]</a>
                                </span>
                            </h5>
                            <?php foreach ($block->elements as $elementId => $element) { ?>
                                <div class="input-wrap">
                                    <?php
                                    $elType = $element->name == 'metadata'
                                        ? $element->params->input
                                        : $element->params->type;
                                    $idLabel = Lang::txt('COM_PUBLICATIONS_FIELD_ID');
                                    ?>
                                    <?php
                                    $blockIdTxt = $idLabel . ': ' . $elementId
                                        . ' - ' . $element->name . ' - ' . $elType;
                                    ?>
                                    <span class="block-id"><?php echo $blockIdTxt; ?></span>
                                    <span class="el-details"><?php echo $element->label; ?></span>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </fieldset>
                <?php
                $blockSelection['active'][] = $block->name;
                $i++;
            }
            ?>
        </fieldset>
    <?php } ?>

    <?php echo Html::input('token'); ?>

    <?php if ($this->row->id) { ?>
        <?php
        $advancedUrl = Route::url(
            'index.php?option=' . $this->option
            . '&controller=' . $this->controller
            . '&task=advanced&id=' . $this->row->id
        );
        $advLabel = Lang::txt('COM_PUBLICATIONS_MTYPE_ADVANCED_CURATION_EDITING');
        ?>
        <p class="sublink"><a href="<?php echo $advancedUrl; ?>"><?php echo $advLabel; ?></a></p>
    <?php } ?>
</form>
