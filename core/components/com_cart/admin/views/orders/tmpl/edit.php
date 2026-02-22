<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$canDo = \Components\Cart\Admin\Helpers\Permissions::getActions('order');

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_CART') . ': Edit order info');

if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
}
Toolbar::cancel();

$this->css()
    ->js();
?>

<?php
$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
?>
<form action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="item-form">
    <div class="grid">
    <div class="col span5">
        <fieldset class="adminform">
            <legend><span>Order Details</span></legend>

            <table class="formed">
                <tbody>
                    <tr>
                        <th>Order number:</th>
                        <td><span><?php echo $this->tInfo->tId; ?></span></td>
                    </tr>
                    <tr>
                        <th>Order placed:</th>
                        <td><span><?php echo $this->tInfo->tLastUpdated; ?></span></td>
                    </tr>
                    <tr>
                        <th>Ordered by:</th>
                        <td><span><?php
                            $userName = $this->user->get('id')
                                ? $this->user->get('name') . ' (' . $this->user->get('username') . ')'
                                : Lang::txt('COM_CART_UNKNOWN');
                            echo $userName;
                        ?></span></td>
                    </tr>
                    <tr>
                        <th>Order subtotal:</th>
                        <td><span><?php echo '$' . number_format($this->tInfo->tiSubtotal, 2); ?></span></td>
                    </tr>
                    <?php
                    if (!empty($this->tInfo->tiTax) && $this->tInfo->tiTax) {
                        ?>
                        <tr>
                            <th>Tax:</th>
                            <td><span><?php echo '$' . number_format($this->tInfo->tiTax, 2); ?></span></td>
                        </tr>
                        <?php
                    }
                    if (!empty($this->tInfo->tiShipping) && floatval($this->tInfo->tiShipping)) {
                        ?>
                        <tr>
                            <th>Shipping cost:</th>
                            <td><span><?php echo '$' . number_format($this->tInfo->tiShipping, 2); ?></span></td>
                        </tr>
                        <?php
                    }
                    if (!empty($this->tInfo->tiDiscounts) && floatval($this->tInfo->tiDiscounts)) {
                        ?>
                        <tr>
                            <th>Discounts:</th>
                            <td><span><?php echo '$' . number_format($this->tInfo->tiDiscounts, 2); ?></span></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <th>Order total:</th>
                        <td><span><?php echo '$' . number_format($this->tInfo->tiTotal, 2); ?></span></td>
                    </tr>
                </tbody>
            </table>

        </fieldset>

        <?php
        if (!empty($this->tInfo->tiShippingToFirst)) {
            ?>
            <fieldset class="adminform">
                <legend><span>Shipping info</span></legend>

                <p>
                    <strong>Ship to:</strong><br>
                    <?php
                        echo $this->tInfo->tiShippingToFirst . ' ' . $this->tInfo->tiShippingToLast . '<br>';
                        echo $this->tInfo->tiShippingAddress . '<br>';
                        echo $this->tInfo->tiShippingCity . ', '
                            . $this->tInfo->tiShippingState . ' '
                            . $this->tInfo->tiShippingZip . '<br>';
                    ?>
                </p>

            </fieldset>
            <?php
        }
        ?>

        <?php
        if (!empty($this->tInfo->tiPayment)) {
            ?>
            <fieldset class="adminform">
                <legend><span>Payment info</span></legend>

                <p>Payment method: <?php echo $this->escape($this->tInfo->tiPayment); ?></p>

                <?php
                if (!empty($this->tInfo->tiPaymentDetails)) {
                    ?>
                    <p>
                        <strong>Payment details:</strong><br>
                        <?php $payDetails = $this->escape($this->tInfo->tiPaymentDetails); ?>
                        <input type="text"
                            name="tiPaymentDetails"
                            value="<?php echo $payDetails; ?>" />
                    </p>
                    <?php
                }
                ?>
            </fieldset>
            <?php
        }
        ?>
    </div>
    <div class="col span7">
        <fieldset class="adminform">
            <legend><span>Items Ordered</span></legend>

            <table class="formed">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>QTY</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $itemsOrdered = $this->items;

                foreach ($itemsOrdered as $itemOrdered) {
                    $itemInfo = $itemOrdered['info'];
                    ?>
                    <tr>
                        <td>
                            <?php
                            if ($itemInfo->available) {
                                $pUrl = Route::url(
                                    'index.php?option=com_storefront&controller=products&task=edit&id='
                                    . $itemInfo->pId
                                );
                                $pName = $this->escape(stripslashes($itemInfo->pName));
                                $product = '<a href="' . $pUrl
                                    . '" target="_blank" rel="noopener">'
                                    . $pName . '</a>';
                                $sUrl = Route::url(
                                    'index.php?option=com_storefront&controller=skus&task=edit&id='
                                    . $itemInfo->sId
                                );
                                $sName = $this->escape(stripslashes($itemInfo->sSku));
                                $product .= ', <a href="' . $sUrl
                                    . '" target="_blank" rel="noopener">'
                                    . $sName . '</a>';
                            } else {
                                $product = $this->escape(stripslashes($itemInfo->pName))
                                    . ', ' . $this->escape(stripslashes($itemInfo->sSku));
                                $product .= ' <br><em>&nbsp;&mdash;&nbsp;Item is no longer available</em>';
                            }
                            ?>
                            <span><?php echo $product; ?></span>
                        </td>
                        <td>
                            <input type="text"
                                name="tiPrice[<?php echo $itemInfo->sId; ?>]"
                                size="10"
                                maxlength="100"
                                value="<?php echo $itemOrdered['transactionInfo']->tiPrice; ?>" />
                        </td>
                        <td>
                            <input type="text"
                                name="tiQty[<?php echo $itemInfo->sId; ?>]"
                                size="10"
                                maxlength="100"
                                value="<?php echo $itemOrdered['transactionInfo']->qty; ?>" />
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </fieldset>

        <?php
        // Check the notes, both SKU-specific and other
        $notes = array();
        foreach ($this->items as $sId => $item) {
            $meta = $item['transactionInfo']->tiMeta;

            if (!empty($meta->checkoutNotes)) {
                $notes[] = array(
                    'object' => 'transactionItem',
                    'objectId' => $item['info']->sId,
                    'label' => '<strong>'
                        . $this->tInfo->tiItems[$sId]['info']->pName
                        . ', ' . $this->tInfo->tiItems[$sId]['info']->sSku
                        . '</strong>',
                    'notes' => $meta->checkoutNotes
                );
            }
        }

        $genericNotesLabel = '';
        if (!empty($notes)) {
            $genericNotesLabel = '<strong>' . 'Other notes/comments' . '</strong>';
        }

        if ($this->tInfo->tiNotes) {
            $notes[] = array(
                'object' => 'transaction',
                'objectId' => $this->tInfo->tId,
                'label' => $genericNotesLabel,
                'notes' => $this->tInfo->tiNotes
            );
        }

        if (!empty($notes)) {
            echo '<fieldset class="adminform">';
            echo '<legend><span>Notes/Comments</span></legend>';
            foreach ($notes as $note) {
                //print_r($note); die;
                echo '<p>';
                echo $note['label'];
                if ($note['object'] == 'transactionItem') {
                    echo '<textarea rows="6" name="checkoutNotes['
                        . $note['objectId'] . ']">'
                        . $note['notes'] . '</textarea>';
                } elseif ($note['object'] == 'transaction') {
                    echo '<textarea rows="6" name="tiNotes">' . $note['notes'] . '</textarea>';
                }
                echo '</p>';
            }
            echo '</fieldset>';
        };
        ?>
    </div>
    </div>

    <input type="hidden" name="id" value="<?php echo $this->tId; ?>" />
    <input type="hidden" name="task" value="edit" />
    <input type="hidden" name="from" value="edit" />

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

    <?php echo Html::input('token'); ?>
</form>