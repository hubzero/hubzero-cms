<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');

$text = ($this->task == 'edit' ? Lang::txt('COM_STOREFRONT_EDIT') : Lang::txt('COM_STOREFRONT_NEW'));

// get meta
$skuMeta = $this->row->getMeta();

$inventoryNotificationThreshold = '';
if (
    isset($skuMeta['inventoryNotificationThreshold'])
    && !empty($skuMeta['inventoryNotificationThreshold'])
) {
    $inventoryNotificationThreshold = $skuMeta['inventoryNotificationThreshold'];
}

$title = Lang::txt('COM_STOREFRONT') . ': '
    . Lang::txt('COM_STOREFRONT_SKU') . ': ' . $text;
Toolbar::title($title, 'storefront');
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('category');

$this->css();

?>
<script type="text/javascript">
    function submitbutton(pressbutton)
    {
        if (pressbutton == 'cancel') {
            submitform(pressbutton);
            return;
        }

        <?php echo $this->editor()->save('text'); ?>

        // do field validation
        if (document.getElementById('field-title').value == ''){
            alert("<?php echo 'Title cannot be empty' ?>");
        }
        <?php
        if (0 && $this->pInfo->ptModel == 'software') {
            ?>
        else if (document.getElementById('field-download-file').value == ''){
            alert("<?php echo 'Download file cannot be empty' ?>");
        }
            <?php
        }
        ?>
        else {
            submitform(pressbutton);
        }
    }
</script>

<?php $formAction = Route::url('index.php?option=' . $this->option); ?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="item-form">
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_STOREFRONT_DETAILS'); ?></span></legend>

                <div class="input-wrap">
<?php
$titleLabel = Lang::txt('COM_STOREFRONT_TITLE');
$requiredTxt = Lang::txt('JOPTION_REQUIRED');
$nameValue = $this->escape(stripslashes($this->row->getName()));
$priceValue = $this->escape(stripslashes($this->row->getPrice()));
?>
                    <label for="field-title">
                        <?php echo $titleLabel; ?>: <span class="required"><?php echo $requiredTxt; ?></span>
                    </label><br />
                    <input
                        type="text"
                        name="fields[sSku]"
                        id="field-title"
                        size="30"
                        maxlength="100"
                        value="<?php echo $nameValue; ?>"
                    />
                </div>

                <div class="input-wrap">
                    <label for="field-title">
                        <?php echo Lang::txt('COM_STOREFRONT_PRICE'); ?>:
                        <span class="required"><?php echo $requiredTxt; ?></span>
                    </label><br />
                    <input
                        type="text"
                        name="fields[sPrice]"
                        id="field-title"
                        size="30"
                        maxlength="100"
                        value="<?php echo $priceValue; ?>"
                    />
                </div>

                <?php
                if ($this->pInfo->ptId == 1) {
                    ?>
                    <div class="input-wrap">
                        <label for="field-title"><?php echo Lang::txt('COM_STOREFRONT_WEIGHT'); ?>:</label><br/>
                        <input type="text" name="fields[sWeight]" id="field-title" size="30" maxlength="100"
                               value="<?php echo $this->escape(stripslashes($this->row->getWeight())); ?>"/>
                    </div>
                    <?php
                }
                ?>
            </fieldset>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('Checkout options'); ?></span></legend>

                <div class="input-wrap">
<?php $checkoutLabel = Lang::txt('Checkout notes/comments message'); ?>
                    <label for="field-checkoutNotes"><?php echo $checkoutLabel; ?>:</label><br />
<?php $checkoutNotesVal = $this->escape(stripslashes($this->row->getCheckoutNotes())); ?>
                    <input
                        type="text"
                        name="fields[checkoutNotes]"
                        id="field-checkoutNotes"
                        size="30"
                        maxlength="100"
                        value="<?php echo $checkoutNotesVal; ?>"
                    />
                </div>

                <div class="input-wrap">
<?php $checkoutReqLabel = Lang::txt('Checkout notes/comments required'); ?>
                    <label for="field-checkoutNotesRequired"><?php echo $checkoutReqLabel; ?>:</label>
                    <select name="fields[checkoutNotesRequired]" id="field-checkoutNotesRequired">
                        <option value="0"<?php if ($this->row->getCheckoutNotesRequired() == 0) {
                            echo ' selected="selected"';
                                         } ?>><?php echo Lang::txt('COM_STOREFRONT_NO'); ?></option>
                        <option value="1"<?php if ($this->row->getCheckoutNotesRequired() == 1) {
                            echo ' selected="selected"';
                                         } ?>><?php echo Lang::txt('COM_STOREFRONT_YES'); ?></option>
                    </select>
                </div>

            </fieldset>

            <?php
            if (!empty($this->allOptions)) {
                ?>
                <fieldset class="adminform">
                    <legend><span><?php echo 'Product options'; ?></span></legend>

                    <?php
                    foreach ($this->allOptions as $optionGroup) {
                        ?>
                        <div class="input-wrap">
                            <label for="field-options-<?php echo $optionGroup->ogId; ?>">
                                <?php echo $optionGroup->ogName; ?>:
                                <span class="required"><?php echo $requiredTxt; ?></span>
                            </label><br />

                            <?php
                            // First check if there are any options to display
                            $optionsToDisplay = false;
                            foreach ($optionGroup->options as $option) {
                                if ($option->oActive || in_array($option->oId, $this->options)) {
                                    $optionsToDisplay = true;
                                }
                            }
                            ?>

                            <?php
                            if ($optionsToDisplay) {
                                ?>
                                <select name="fields[options][]" id="field-options-<?php echo $optionGroup->ogId; ?>">
                                    <option value="">-- please select an option --</option>
                                    <?php
                                    foreach ($optionGroup->options as $option) {
                                        if ($option->oActive || in_array($option->oId, $this->options)) {
                                            ?>
                                            <?php
                                            $optSelected = in_array($option->oId, $this->options)
                                            ? ' selected="selected"' : '';
                                            $optVal = $option->oId;
                                            $optName = $option->oName;
                                            ?>
                                            <option
                                                value="<?php echo $optVal; ?>"
                                                <?php echo $optSelected; ?>
                                            ><?php echo $optName; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <?php
                            } else {
                                ?>
                                <?php
                                $ogAdminUrl = Route::url(
                                    'index.php?option=' . $this->option
                                    . '&controller=options&task=display&id=' . $optionGroup->ogId
                                );
                                $editOgTitle = Lang::txt('Edit option group');
                                ?>
                                <p class="warning">
                                    There are currently no available options for this option group.
                                    Please go to the
                                    <a href="<?php echo $ogAdminUrl; ?>" title="<?php echo $editOgTitle; ?>">
                                        <?php echo $optionGroup->ogName; ?> options administration</a>
                                    and make sure to create new or enable existing options.
                                </p>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </fieldset>
                <?php
            }
            ?>

            <?php
            // Product type specific meta options

            if ($this->pInfo->ptModel == 'software') {
                $view = new \Hubzero\Component\View(array('name' => 'meta', 'layout' => 'sku-software'));
                $view->parent = $this;
                $view->skuMeta = $skuMeta;
                $view->display();
            }

            ?>

        </div>
        <div class="col span5">
            <table class="meta">
                <tbody>
                <tr>
                    <th class="key"><?php echo Lang::txt('COM_STOREFRONT_ID'); ?>:</th>
                    <td>
                        <?php echo $this->row->getId(); ?>
                        <input
                            type="hidden"
                            name="fields[sId]"
                            id="field-sid"
                            value="<?php echo $this->escape($this->row->getId()); ?>"
                        />
                    </td>
                </tr>
                <tr>
                    <th class="key"><?php echo Lang::txt('COM_STOREFRONT_PRODUCT'); ?>:</th>
                    <td>
                        <?php echo $this->pInfo->pName; ?>
                        <input
                            type="hidden"
                            name="pId"
                            id="pid"
                            value="<?php echo $this->escape($this->pInfo->pId); ?>"
                        />
                    </td>
                </tr>
                <?php
                if ($this->pInfo->ptModel == 'software') {
                    ?>
                    <tr>
                        <th class="key"><?php echo Lang::txt('COM_STOREFRONT_DOWNLOADED'); ?>:</th>
                        <td>
                            <?php
                            echo $this->downloaded;
                            if ($this->downloaded == 0 || $this->downloaded > 1) {
                                echo ' times';
                            } else {
                                echo ' time';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <th class="key"><?php echo Lang::txt('COM_STOREFRONT_DIRECT_URL'); ?>:</th>
                    <td>
                        <?php
                        $directUrl = Request::root();
                        $productSlug = !empty($this->pInfo->pAlias) ? $this->pInfo->pAlias : $this->pInfo->pId;
                        $directUrl .= 'storefront/product/' . $productSlug;
                        if (!empty($this->options)) {
                            $directUrl .= '/';
                            $i = 0;
                            foreach ($this->options as $o) {
                                if ($i) {
                                    $directUrl .= ',';
                                }
                                $directUrl .= $o;
                                $i++;
                            }
                        }

                        echo $directUrl;
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_STOREFRONT_OPTIONS'); ?></span></legend>

                <div class="input-wrap">
                    <label for="field-sAllowMultiple"><?php echo Lang::txt('COM_STOREFRONT_ALLOW_MULTIPLE'); ?>:</label>
                    <select name="fields[sAllowMultiple]" id="field-pAllowMultiple">
                        <option value="0"<?php if ($this->row->getAllowMultiple() == 0) {
                            echo ' selected="selected"';
                                         } ?>><?php echo Lang::txt('COM_STOREFRONT_NO'); ?></option>
                        <option value="1"<?php if ($this->row->getAllowMultiple() == 1) {
                            echo ' selected="selected"';
                                         } ?>><?php echo Lang::txt('COM_STOREFRONT_YES'); ?></option>
                    </select>
                </div>

                <?php
                $showInventoryOptions = true;
                if (
                    $this->pInfo->ptModel == 'software'
                    && isset($skuMeta['serialManagement'])
                    && $skuMeta['serialManagement'] == 'multiple'
                ) {
                    $showInventoryOptions = false;
                }
                if ($showInventoryOptions) {
                    ?>

                    <?php $trackHint = 'Should the inventory level be kept tracked? If yes set the inventory.'; ?>
                    <div class="input-wrap" data-hint="<?php echo $trackHint; ?>">
                        <label for="field-sTrackInventory"><?php echo 'Track Inventory'; ?>:</label>
                        <select name="fields[sTrackInventory]" id="field-sTrackInventory">
                            <option value="0"<?php if ($this->row->getTrackInventory() == 0) {
                                echo ' selected="selected"';
                                             } ?>><?php echo Lang::txt('COM_STOREFRONT_NO'); ?></option>
                            <option value="1"<?php if ($this->row->getTrackInventory() == 1) {
                                echo ' selected="selected"';
                                             } ?>><?php echo Lang::txt('COM_STOREFRONT_YES'); ?></option>
                        </select>
                    </div>

                    <?php
                    $inventoryHint = 'Number of items available for sale in the inventory. Non-negative integer.';
                    ?>
                    <div class="input-wrap" data-hint="<?php echo $inventoryHint; ?>">
                        <label for="field-inventory"><?php echo 'Inventory'; ?>:</label>
                        <input
                            type="text"
                            name="fields[sInventory]"
                            id="field-inventory"
                            size="30"
                            maxlength="10"
                            value="<?php echo $this->row->getInventoryLevel(); ?>"
                        />
                    </div>

                    <?php
                }
                ?>

<?php
$invThresholdHint = 'Inventory threshold: when reached or below an email notification'
    . ' is sent to the admin on each inventory change';
?>
                <div class="input-wrap" data-hint="<?php echo $invThresholdHint; ?>">
                    <label for="field-inventory-notification-threshold">
                        <?php echo 'Inventory notification threshold'; ?>:
                    </label>
                    <input
                        type="text"
                        name="fields[meta][inventoryNotificationThreshold]"
                        id="field-inventory-notification-threshold"
                        size="30"
                        maxlength="10"
                        value="<?php echo $inventoryNotificationThreshold; ?>"
                    />
                </div>
            </fieldset>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_STOREFRONT_PUBLISH_OPTIONS'); ?></span></legend>

                <div class="input-wrap">
                    <label for="field-state"><?php echo Lang::txt('COM_STOREFRONT_STATE'); ?>:</label>
                    <select name="fields[state]" id="field-state">
                        <option value="0"<?php if ($this->row->getActiveStatus() == 0) {
                            echo ' selected="selected"';
                                         } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
                        <option value="1"<?php if ($this->row->getActiveStatus() == 1) {
                            echo ' selected="selected"';
                                         } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
                    </select>
                </div>

                <div class="input-wrap">
<?php
$publishUp = $this->row->getPublishTime()->publish_up;
$publishUpVal = ($publishUp && $publishUp != '0000-00-00 00:00:00')
    ? $this->escape(Date::of($publishUp)->toLocal('Y-m-d H:i:s'))
    : '';
?>
<?php $publishUpLabel = Lang::txt('COM_STOREFRONT_FIELD_PUBLISH_UP'); ?>
                    <label for="field-publish_up"><?php echo $publishUpLabel; ?>:</label><br />
                    <?php echo Html::input(
                        'calendar',
                        'fields[publish_up]',
                        $publishUpVal,
                        array('id' => 'field-publish_up')
                    ); ?>
                </div>

                <div class="input-wrap">
<?php
$publishDown = $this->row->getPublishTime()->publish_down;
$publishDownVal = ($publishDown && $publishDown != '0000-00-00 00:00:00')
    ? $this->escape(Date::of($publishDown)->toLocal('Y-m-d H:i:s'))
    : '';
?>
<?php $publishDownLabel = Lang::txt('COM_STOREFRONT_FIELD_PUBLISH_DOWN'); ?>
                    <label for="field-publish_down"><?php echo $publishDownLabel; ?>:</label><br />
                    <?php echo Html::input(
                        'calendar',
                        'fields[publish_down]',
                        $publishDownVal,
                        array('id' => 'field-publish_down')
                    ); ?>
                </div>
            </fieldset>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('Restrictions'); ?></span></legend>

                <div class="input-wrap">
                    <label for="field-restricted"><?php echo Lang::txt('Restrict by users'); ?>:</label>
                    <select name="fields[restricted]" id="field-restricted">
                        <option value="0"<?php if ($this->row->getRestricted() == 0) {
                            echo ' selected="selected"';
                                         } ?>><?php echo Lang::txt('COM_STOREFRONT_NO'); ?></option>
                        <option value="1"<?php if ($this->row->getRestricted() == 1) {
                            echo ' selected="selected"';
                                         } ?>><?php echo Lang::txt('COM_STOREFRONT_YES'); ?></option>
                    </select>
                </div>

                <?php
                if ($this->row->getRestricted()) {
                    ?>
                    <p>
                    <?php
                    $restrictUrl = 'index.php?option=' . $this->option
                    . '&controller=restrictions&id=' . $this->row->getId();
                    ?>
                    <a class="options-link" href="<?php echo $restrictUrl; ?>">
                        Manage restrictions
                    </a>
                </p>
                    <?php
                }
                ?>
            </fieldset>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('Whitelist'); ?></span></legend>

                <div class="input-wrap">
                    Whitelisting grants users access to the SKU despite any other
                    existing access controls or restrictions
                </div>

                <p>
<?php
$whitelistUrl = 'index.php?option=' . $this->option
    . '&controller=whitelist&id=' . $this->row->getId();
?>
                    <a class="options-link" href="<?php echo $whitelistUrl; ?>">
                        Manage whitelist
                    </a>
                </p>

            </fieldset>

        </div>
    </div>

    <?php /*
        <?php if ($canDo->get('core.admin')): ?>
            <div class="col span12">
                <fieldset class="panelform">
                    <?php echo $this->form->getLabel('rules'); ?>
                    <?php echo $this->form->getInput('rules'); ?>
                </fieldset>
            </div>
            <div class="clr"></div>
        <?php endif; ?>
    */ ?>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
