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
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_CART') . ': View order');

Toolbar::cancel();

if (User::authorise('core.edit', $this->option . '.component')) {
    Toolbar::custom('edit', 'edit.png', '', 'Edit', false);
}

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
                    ?>
                    <?php
                    if (!empty($this->tInfo->tiShipping) && floatval($this->tInfo->tiShipping)) {
                        ?>
                        <tr>
                            <th>Shipping cost:</th>
                            <td><span><?php echo '$' . number_format($this->tInfo->tiShipping, 2); ?></span></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <?php
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

                <p>
                    <strong>Payment method:</strong><br>
                    <?php echo $this->tInfo->tiPayment; ?>
                </p>

                <?php
                if (!empty($this->tInfo->tiPaymentDetails)) {
                    echo '<p><strong>Payment details:</strong><br>' . $this->tInfo->tiPaymentDetails . '</p>';
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
                            $pName = isset($itemInfo->pName) ? $itemInfo->pName : 'N/A';
                            $sName = isset($itemInfo->sSku) ? $itemInfo->sSku : 'N/A';
                            $product = $this->escape(stripslashes($pName))
                                . ', ' . $this->escape(stripslashes($sName));
                            $product .= ' <br><em>&nbsp;&mdash;&nbsp;Item is no longer available</em>';
                        }
                        ?>
                            <span><?php echo $product; ?></span>
                        </td>
                        <td><span><?php
                            $tiPrice = isset($itemOrdered['transactionInfo']->tiPrice)
                                ? $itemOrdered['transactionInfo']->tiPrice
                                : 'N/A';
                            echo $tiPrice;
                        ?></span></td>
                        <td><span><?php echo $itemOrdered['transactionInfo']->qty; ?></span></td>
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
                    //'label' => '<strong>' . $item['info']->pName . ', ' . $item['info']->sSku . '</strong>',
                    'label' => '<strong>'
                        . $this->tInfo->tiItems[$sId]['info']->pName
                        . ', ' . $this->tInfo->tiItems[$sId]['info']->sSku
                        . '</strong>',
                    'notes' => $meta->checkoutNotes);
            }
        }

        $genericNotesLabel = '';
        if (!empty($notes)) {
            $genericNotesLabel = '<strong>' . 'Other notes/comments' . '</strong>';
        }

        if ($this->tInfo->tiNotes) {
            $notes[] = array(
                'label' => $genericNotesLabel,
                'notes' => $this->tInfo->tiNotes);
        }

        if (!empty($notes)) {
            echo '<fieldset class="adminform">';
            echo '<legend><span>Notes/Comments</span></legend>';
            foreach ($notes as $note) {
                echo '<p>';
                echo $note['label'];
                if ($note['label']) {
                    echo ': ';
                }
                echo $note['notes'];
                echo '</p>';
            }
            echo '</fieldset>';
        };

        ?>

    </div>
    </div>

    <?php
    if (isset($this->log)) {
        ?>
    <div class="log">
        <h3>Changelog</h3>
        <div class="inner">

        <?php
        // let's show the logs
        foreach ($this->log as $log) {
            echo '<article>';
            $logDate = date("F j, Y, g:i a", strtotime($log->created));
            $header = '<header>' . $log->description
                . ' on ' . $logDate
                . ' by ' . $log->user
                . ' [' . $log->created_by . ']</header>';
            echo $header;

            foreach ($log->details as $change) {
                echo '<div class="change">';
                echo '<p class="msg">' . $change->message . '</p>';
                ?>

                <div class="diff">
                    <div>
                        <p>New value:</p>
                        <div class="value"><?php echo $change->new; ?></div>
                    </div>
                    <div>
                        <p>Old value:</p>
                        <div class="value old"><?php echo $change->old; ?></div>
                    </div>
                </div>

                <?php
                echo '</div>';
            }

            echo '</article>';
        }
        ?>

        </div>
    </div>

        <?php
    }
    ?>

    <input type="hidden" name="id" value="<?php echo $this->tId; ?>" />
    <input type="hidden" name="task" value="view" />

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

    <?php echo Html::input('token'); ?>
</form>
