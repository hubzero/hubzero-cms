<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

Html::behavior('chart', 'pie');

$this->css();

$this->css('
.wishlist-removed {
	background-color: ' . $this->params->get("color_removed", "#cccccc") . ';
}
.wishlist-granted {
	background-color: ' . $this->params->get("color_granted", "#999") . ';
}
.wishlist-withdrawn {
	background-color: ' . $this->params->get("color_withdrawn", "#ffffff") . ';
}
.wishlist-pending {
	background-color: ' . $this->params->get("color_pending", "#656565") . ';
}
.wishlist-rejected {
	background-color: ' . $this->params->get("color_rejected", "#333333") . ';
}
.wishlist-accepted {
	background-color: ' . $this->params->get("color_accepted", "#f9d180") . ';
}
');

$this->js();

$total = $this->granted + $this->accepted + $this->pending
    + $this->removed + $this->withdrawn + $this->removed
    + $this->rejected;
if ($total == 0) {
    // Show nothing if no wishes (otherwise get division by zero error) - snowwitje
    return false;
}
?>
<div class="<?php echo $this->module->module; ?>">
    <div class="overview-container">
        <?php
        $containerId = 'wishlist-container' . $this->module->id;
        $dataId = $this->module->module . '-data' . $this->module->id;
        ?>
        <div id="<?php echo $containerId; ?>"
            class="<?php echo $this->module->module; ?>-chart chrt"
            data-datasets="<?php echo $dataId; ?>"
        ></div>

        <script type="application/json"
            id="<?php echo $dataId; ?>"
        >
            {
                "datasets": [
                    {
                        "label": "<?php echo strtolower(Lang::txt('MOD_WISHLIST_PENDING')); ?>",
                        "data": <?php echo round(($this->pending / $total) * 100, 2); ?>,
                        "color": "<?php echo $this->params->get("color_pending", "#656565"); ?>"
                    },
                    {
                        "label": "<?php echo strtolower(Lang::txt('MOD_WISHLIST_GRANTED')); ?>",
                        "data": <?php echo round(($this->granted / $total) * 100, 2); ?>,
                        "color": "<?php echo $this->params->get("color_granted", "#999"); ?>"
                    },
                    {
                        "label": "<?php echo strtolower(Lang::txt('MOD_WISHLIST_ACCEPTED')); ?>",
                        "data": <?php echo round(($this->accepted / $total) * 100, 2); ?>,
                        "color": "<?php echo $this->params->get("color_accepted", "#f9d180"); ?>"
                    },
                    {
                        "label": "<?php echo strtolower(Lang::txt('MOD_WISHLIST_REMOVED')); ?>",
                        "data": <?php echo round(($this->removed / $total) * 100, 2); ?>,
                        "color": "<?php echo $this->params->get("color_removed", "#cccccc"); ?>"
                    },
                    {
                        "label": "<?php echo strtolower(Lang::txt('MOD_WISHLIST_WITHDRAWN')); ?>",
                        "data": <?php echo round(($this->withdrawn / $total) * 100, 2); ?>,
                        "color": "<?php echo $this->params->get("color_withdrawn", "#ffffff"); ?>"
                    },
                    {
                        "label": "<?php echo strtolower(Lang::txt('MOD_WISHLIST_REJECTED')); ?>",
                        "data": <?php echo round(($this->rejected / $total) * 100, 2); ?>,
                        "color": "<?php echo $this->params->get("color_rejected", "#333333"); ?>"
                    }
                ]
            }
        </script>

        <p class="wishlist-total"><?php echo $total; ?></p>
    </div>
    <div class="overview-container wishlist-stats-overview">
        <table>
            <tbody>
                <?php
                $wishBase = 'index.php?option=com_wishlist'
                    . '&controller=wishes&wishlist=' . $this->wishlist;

                $pendUrl = Route::url($wishBase . '&filterby=pending');
                $pendTitle = Lang::txt('MOD_WISHLIST_PENDING_TITLE');
                $accUrl = Route::url($wishBase . '&filterby=accepted');
                $accTitle = Lang::txt('MOD_WISHLIST_ACCEPTED_TITLE');
                $grantUrl = Route::url($wishBase . '&filterby=granted');
                $grantTitle = Lang::txt('MOD_WISHLIST_GRANTED_TITLE');
                $rejUrl = Route::url($wishBase . '&filterby=rejected');
                $rejTitle = Lang::txt('MOD_WISHLIST_REJECTED_TITLE');
                $withUrl = Route::url($wishBase . '&filterby=withdrawn');
                $withTitle = Lang::txt('MOD_WISHLIST_WITHDRAWN_TITLE');
                $delUrl = Route::url($wishBase . '&filterby=deleted');
                $delTitle = Lang::txt('MOD_WISHLIST_REMOVED_TITLE');
                ?>
                <tr class="pending-items">
                    <th scope="row">
                        <a href="<?php echo $pendUrl; ?>"
                            title="<?php echo $pendTitle; ?>"
                        >
                            <span class="wishlist-pending"></span><?php
                                echo Lang::txt('MOD_WISHLIST_PENDING');
                            ?>
                        </a>
                    </th>
                    <td>
                        <a href="<?php echo $pendUrl; ?>"
                            title="<?php echo $pendTitle; ?>"
                        >
                            <?php echo $this->escape($this->pending); ?>
                        </a>
                    </td>
                </tr>
                <tr class="accepted-items">
                    <th scope="row">
                        <a href="<?php echo $accUrl; ?>"
                            title="<?php echo $accTitle; ?>"
                        >
                            <span class="wishlist-accepted"></span><?php
                                echo Lang::txt('MOD_WISHLIST_ACCEPTED');
                            ?>
                        </a>
                    </th>
                    <td>
                        <a href="<?php echo $accUrl; ?>"
                            title="<?php echo $accTitle; ?>"
                        >
                            <?php echo $this->escape($this->accepted); ?>
                        </a>
                    </td>
                </tr>
                <tr class="granted-items">
                    <th scope="row">
                        <a href="<?php echo $grantUrl; ?>"
                            title="<?php echo $grantTitle; ?>"
                        >
                            <span class="wishlist-granted"></span><?php
                                echo Lang::txt('MOD_WISHLIST_GRANTED');
                            ?>
                        </a>
                    </th>
                    <td>
                        <a href="<?php echo $grantUrl; ?>"
                            title="<?php echo $grantTitle; ?>"
                        >
                            <?php echo $this->escape($this->granted); ?>
                        </a>
                    </td>
                </tr>
                <tr class="rejected-items">
                    <th scope="row">
                        <a href="<?php echo $rejUrl; ?>"
                            title="<?php echo $rejTitle; ?>"
                        >
                            <span class="wishlist-rejected"></span><?php
                                echo Lang::txt('MOD_WISHLIST_REJECTED');
                            ?>
                        </a>
                    </th>
                    <td>
                        <a href="<?php echo $rejUrl; ?>"
                            title="<?php echo $rejTitle; ?>"
                        >
                            <?php echo $this->escape($this->rejected); ?>
                        </a>
                    </td>
                </tr>
                <tr class="withdrawn-items">
                    <th scope="row">
                        <a href="<?php echo $withUrl; ?>"
                            title="<?php echo $withTitle; ?>"
                        >
                            <span class="wishlist-withdrawn"></span><?php
                                echo Lang::txt('MOD_WISHLIST_WITHDRAWN');
                            ?>
                        </a>
                    </th>
                    <td>
                        <a href="<?php echo $withUrl; ?>"
                            title="<?php echo $withTitle; ?>"
                        >
                            <?php echo $this->escape($this->withdrawn); ?>
                        </a>
                    </td>
                </tr>
                <tr class="removed-items">
                    <th scope="row">
                        <a href="<?php echo $delUrl; ?>"
                            title="<?php echo $delTitle; ?>"
                        >
                            <span class="wishlist-removed"></span><?php
                                echo Lang::txt('MOD_WISHLIST_REMOVED');
                            ?>
                        </a>
                    </th>
                    <td>
                        <a href="<?php echo $delUrl; ?>"
                            title="<?php echo $delTitle; ?>"
                        >
                            <?php echo $this->escape($this->removed); ?>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
