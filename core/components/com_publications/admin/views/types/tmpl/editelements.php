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

$this->css()
     ->js('curation.js');

$label = Lang::txt('COM_PUBLICATIONS_PUBLICATION');
$label2 = Lang::txt('COM_PUBLICATIONS_MASTER_TYPE');
$label3 = Lang::txt('COM_PUBLICATIONS_EDIT_BLOCK_ELEMENTS');
Toolbar::title(
    $label . ' ' . $label2 . ' - ' . $this->row->type . ': ' . $label3,
    'publications'
);
Toolbar::save('saveelements');
Toolbar::cancel();

$params = new \Hubzero\Config\Registry($this->row->params);
$manifest  = $this->curation->_manifest;
$curParams = $manifest->params;
$blocks    = $manifest->blocks;
$blockId   = $this->blockId;
$block     = $blocks->$blockId;
?>

<?php
$backUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&task=edit&id=' . $this->row->id
);
$backLabel = Lang::txt('COM_PUBLICATIONS_MTYPE_BACK') . ' '
    . $this->row->type . ' '
    . Lang::txt('COM_PUBLICATIONS_MASTER_TYPE');
?>
<p class="backto"><a href="<?php echo $backUrl; ?>"><?php echo $backLabel; ?></a></p>

<form
    action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"
    method="post"
    id="item-form"
    name="adminForm"
>
    <fieldset class="adminform">
        <legend><span><?php echo Lang::txt('COM_PUBLICATIONS_EDIT_BLOCK_ELEMENTS'); ?></span></legend>

        <input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
        <input type="hidden" name="bid" value="<?php echo $this->blockId; ?>" />
        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="task" value="saveelements" />

        <p class="warning"><?php echo Lang::txt('COM_PUBLICATIONS_EDIT_BLOCK_ELEMENTS_WARNING'); ?></p>

        <?php foreach ($block->elements as $elementId => $element) { ?>
            <?php
            $elType = $element->name == 'metadata'
                ? $element->params->input
                : $element->params->type;
            $idLabel = Lang::txt('COM_PUBLICATIONS_FIELD_ID');
            $legendTxt = $idLabel . ': ' . $elementId
                . ' - ' . $element->name . ' - ' . $elType;
            $elBase = 'curation[blocks][' . $blockId
                . '][elements][' . $elementId . ']';
            ?>
            <fieldset class="adminform">
                <legend><span class="block-id"><?php echo $legendTxt; ?></span></legend>
                <div class="input-wrap">
                    <?php $langTxt = Lang::txt('COM_PUBLICATIONS_FIELD_LABEL'); ?>
                    <label for="field-el-<?php echo $elementId; ?>-label"><?php echo $langTxt; ?>:</label>
                    <input
                        type="text"
                        name="<?php echo $elBase; ?>[label]"
                        id="field-el-<?php echo $elementId; ?>-label"
                        maxlength="255"
                        value="<?php echo $element->label; ?>"
                    />
                </div>
                <div class="input-wrap">
                    <?php $langTxt = Lang::txt('COM_PUBLICATIONS_FIELD_ELEMENT_ABOUT'); ?>
                    <?php $aboutId = 'field-el-' . $elementId . '-about'; ?>
                    <label for="<?php echo $aboutId; ?>"><?php echo $langTxt; ?>:</label>
                    <textarea
                        name="<?php echo $elBase; ?>[about]"
                        id="<?php echo $aboutId; ?>"
                    ><?php echo htmlspecialchars($element->about); ?></textarea>
                </div>
                <div class="input-wrap">
                    <?php $langTxt = Lang::txt('COM_PUBLICATIONS_FIELD_ELEMENT_ADMIN_TIPS'); ?>
                    <?php $tipsId = 'field-el-' . $elementId . '-adminTips'; ?>
                    <label for="<?php echo $tipsId; ?>"><?php echo $langTxt; ?>:</label>
                    <textarea
                        name="<?php echo $elBase; ?>[adminTips]"
                        id="<?php echo $tipsId; ?>"
                    ><?php echo htmlspecialchars($element->adminTips); ?></textarea>
                </div>

                <?php foreach ($element->params as $paramname => $paramvalue) {
                    $pName = $elBase . '[params][' . $paramname . ']';
                    ?>
                <div class="input-wrap">
                    <label><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_PARAMS_' . strtoupper($paramname)); ?></label>
                    <?php
                    if ($element->type == 'attachment' && $paramname == 'type') {
                        ?>
                        <select name="<?php echo $pName; ?>">
                            <?php $sel = $paramvalue == 'file' ? ' selected="selected"' : ''; ?>
                            <option value="file"<?php echo $sel; ?>>
                                <?php echo Lang::txt('COM_PUBLICATIONS_TYPE_FILE'); ?></option>
                            <?php $sel = $paramvalue == 'link' ? ' selected="selected"' : ''; ?>
                            <option value="link"<?php echo $sel; ?>>
                                <?php echo Lang::txt('COM_PUBLICATIONS_TYPE_LINK'); ?></option>
                            <?php $sel = $paramvalue == 'data' ? ' selected="selected"' : ''; ?>
                            <option value="data"<?php echo $sel; ?>>
                                <?php echo Lang::txt('COM_PUBLICATIONS_TYPE_DATA'); ?></option>
                            <?php $sel = $paramvalue == 'publication' ? ' selected="selected"' : ''; ?>
                            <?php $pubTxt = Lang::txt('COM_PUBLICATIONS_TYPE_PUBLICATION'); ?>
                            <option value="publication"<?php echo $sel; ?>><?php echo $pubTxt; ?></option>
                        </select>
                    <?php } elseif ($paramname == 'required') {
                        ?>
                        <select name="<?php echo $pName; ?>">
                            <?php $sel = $paramvalue == 1 ? ' selected="selected"' : ''; ?>
                            <option value="1"<?php echo $sel; ?>>
                                <?php echo Lang::txt('JYES'); ?></option>
                            <?php $sel = $paramvalue == 0 ? ' selected="selected"' : ''; ?>
                            <option value="0"<?php echo $sel; ?>>
                                <?php echo Lang::txt('JNO'); ?></option>
                        </select>
                    <?php } elseif ($element->type == 'attachment' && $paramname == 'role') { ?>
                        <select name="<?php echo $pName; ?>">
                            <?php $sel = $paramvalue == 1 ? ' selected="selected"' : ''; ?>
                            <option value="1"<?php echo $sel; ?>>
                                <?php echo Lang::txt('COM_PUBLICATIONS_ROLE_PRIMARY'); ?></option>
                            <?php $sel = ($paramvalue == 2 || $paramvalue == 0) ? ' selected="selected"' : ''; ?>
                            <?php $supTxt = Lang::txt('COM_PUBLICATIONS_ROLE_SUPPORTING'); ?>
                            <option value="2"<?php echo $sel; ?>><?php echo $supTxt; ?></option>
                            <?php $sel = $paramvalue == 3 ? ' selected="selected"' : ''; ?>
                            <option value="3"<?php echo $sel; ?>>
                                <?php echo Lang::txt('COM_PUBLICATIONS_ROLE_GALLERY'); ?></option>
                        </select>
                    <?php } elseif ($paramname == 'typeParams') {
                        foreach ($paramvalue as $tpName => $tpValue) {
                            $tpInputName = $pName . '[' . $tpName . ']';
                            ?>
                            <div class="input-wrap">
                                <?php $tpLabel = Lang::txt('COM_PUBLICATIONS_FIELD_PARAMS_' . strtoupper($tpName)); ?>
                                <label><?php echo $tpLabel; ?></label>
                                <?php
                                if ($tpName == 'handler') { ?>
                                <select name="<?php echo $tpInputName; ?>">
                                    <?php $sel = !$tpValue ? ' selected="selected"' : ''; ?>
                                    <option value=""<?php echo $sel; ?>>
                                        <?php echo Lang::txt('JNONE'); ?></option>
                                    <?php $sel = $tpValue == 'imageviewer' ? ' selected="selected"' : ''; ?>
                                    <?php $imgTxt = Lang::txt('COM_PUBLICATIONS_HANDLER_IMAGE'); ?>
                                    <option value="imageviewer"<?php echo $sel; ?>><?php echo $imgTxt; ?></option>
                                </select>
                                <?php } elseif ($tpName == 'reuse') { ?>
                            <select name="<?php echo $tpInputName; ?>">
                                    <?php $sel = $tpValue == 1 ? ' selected="selected"' : ''; ?>
                                <option value="1"<?php echo $sel; ?>>
                                    <?php echo Lang::txt('JYES'); ?></option>
                                    <?php $sel = ($tpValue == 0) ? ' selected="selected"' : ''; ?>
                                <option value="0"<?php echo $sel; ?>>
                                    <?php echo Lang::txt('JNO'); ?></option>
                            </select>
                                <?php } elseif ($tpName == 'dirHierarchy') { ?>
                            <select name="<?php echo $tpInputName; ?>">
                                    <?php
                                    $sel1 = $tpValue == 1 ? ' selected="selected"' : '';
                                    $sel0 = ($tpValue == 0) ? ' selected="selected"' : '';
                                    $sel2 = ($tpValue == 2) ? ' selected="selected"' : '';
                                    $pfx = 'COM_PUBLICATIONS_CURATION_ELEMENT_PARAMS_DIRHIERARCHY_';
                                    $prTxt = Lang::txt($pfx . 'PRESERVE');
                                    $npIdTxt = Lang::txt($pfx . 'NOT_PRESERVE_APPEND_ID');
                                    $npNumTxt = Lang::txt($pfx . 'NOT_PRESERVE_APPEND_NUMBER');
                                    ?>
                                <option value="1"<?php echo $sel1; ?>><?php echo $prTxt; ?></option>
                                <option value="0"<?php echo $sel0; ?>><?php echo $npIdTxt; ?></option>
                                <option value="2"<?php echo $sel2; ?>><?php echo $npNumTxt; ?></option>
                            </select>
                                <?php } elseif ($tpName == 'includeInPackage') { ?>
                            <select name="<?php echo $tpInputName; ?>">
                                    <?php $sel = $tpValue == 1 ? ' selected="selected"' : ''; ?>
                                <option value="1"<?php echo $sel; ?>>
                                    <?php echo Lang::txt('JYES'); ?></option>
                                    <?php $sel = $tpValue != 1 ? ' selected="selected"' : ''; ?>
                                <option value="0"<?php echo $sel; ?>>
                                    <?php echo Lang::txt('JNO'); ?></option>
                            </select>
                                <?php } elseif ($tpName == 'bundleDirHierarchy') { ?>
                            <select name="<?php echo $tpInputName; ?>">
                                    <?php
                                    $sel1 = $tpValue == 1 ? ' selected="selected"' : '';
                                    $sel0 = ($tpValue == 0) ? ' selected="selected"' : '';
                                    $pfx = 'COM_PUBLICATIONS_CURATION_ELEMENT_PARAMS_DIRHIERARCHY_';
                                    $prTxt = Lang::txt($pfx . 'PRESERVE');
                                    $npNumTxt = Lang::txt($pfx . 'NOT_PRESERVE_APPEND_NUMBER');
                                    ?>
                                <option value="1"<?php echo $sel1; ?>><?php echo $prTxt; ?></option>
                                <option value="0"<?php echo $sel0; ?>><?php echo $npNumTxt; ?></option>
                            </select>
                                <?php } elseif ($tpName == 'multiZip') { ?>
                            <select name="<?php echo $tpInputName; ?>">
                                    <?php
                                    $sel1 = $tpValue == 1 ? ' selected="selected"' : '';
                                    $sel0 = ($tpValue == 0) ? ' selected="selected"' : '';
                                    $sel2 = ($tpValue == 2) ? ' selected="selected"' : '';
                                    $oneTxt = Lang::txt('COM_PUBLICATIONS_CURATION_ELEMENT_PARAMS_MULTIZIP_ONE');
                                    $zeroTxt = Lang::txt('COM_PUBLICATIONS_CURATION_ELEMENT_PARAMS_MULTIZIP_ZERO');
                                    $twoTxt = Lang::txt('COM_PUBLICATIONS_CURATION_ELEMENT_PARAMS_MULTIZIP_TWO');
                                    ?>
                                <option value="1"<?php echo $sel1; ?>><?php echo $oneTxt; ?></option>
                                <option value="0"<?php echo $sel0; ?>><?php echo $zeroTxt; ?></option>
                                <option value="2"<?php echo $sel2; ?>><?php echo $twoTxt; ?></option>
                            </select>
                                <?php } elseif (is_array($tpValue)) {
                                    $tpVal = implode(',', $tpValue); ?>
                                <input
                                    type="text"
                                    name="<?php echo $tpInputName; ?>"
                                    value="<?php echo $tpVal; ?>"
                                />
                                <?php } else { ?>
                            <input
                                type="text"
                                name="<?php echo $tpInputName; ?>"
                                value="<?php echo $tpValue; ?>"
                            />
                                <?php } ?>
                            </div>
                        <?php }
                    } elseif (is_array($paramvalue)) {
                        $val = implode(',', $paramvalue);
                        ?>
                    <input
                        type="text"
                        name="<?php echo $pName; ?>"
                        value="<?php echo $val; ?>"
                    />
                    <?php } else {
                        $inputType = ($paramname == 'min' || $paramname == 'max')
                            ? 'number' : 'text';
                        $minAttr = ($paramname == 'min' || $paramname == 'max')
                            ? ' min="0"' : '';
                        ?>
                        <input
                            type="<?php echo $inputType; ?>"
                            name="<?php echo $pName; ?>"
                            value="<?php echo $paramvalue; ?>"<?php echo $minAttr; ?>
                        />
                    <?php } ?>
                </div>
                <?php } ?>
            </fieldset>
        <?php } ?>
    </fieldset>
    <?php echo Html::input('token'); ?>
</form>