<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

defined('_HZEXEC_') or die();

Html::behavior('chart', 'pie');

$this->css();

$this->css('
.resource-published {
	background-color: ' . $this->params->get("color_published", "#656565") . ';
}
.resource-unpublished {
	background-color: ' . $this->params->get("color_unpublished", "#fff") . ';
}
.resource-draft {
	background-color: ' . $this->params->get("color_draft", "#999") . ';
}
.resource-pending {
	background-color: ' . $this->params->get("color_pending", "#f9d180") . ';
}
.resource-removed {
	background-color: ' . $this->params->get("color_removed", "#ccc") . ';
}
');

$this->js();

$total = $this->draftInternal + $this->draftUser
    + $this->pending + $this->published
    + $this->unpublished + $this->removed;

$this->draft = $this->draftInternal + $this->draftUser;
?>
<div class="mod_resources">
    <div class="overview-container">
        <?php
        $containerId = 'resources-container' . $this->module->id;
        $dataId = $this->module->module . '-data' . $this->module->id;
        ?>
        <div id="<?php echo $containerId; ?>"
            class="<?php echo $this->module->module; ?>-chart chrt"
            data-datasets="<?php echo $dataId; ?>"
        ></div>
        <?php if ($total > 0) : ?>
            <script type="application/json"
                id="<?php echo $dataId; ?>"
            >
            {
                "datasets": [
                    {
                        "label": "<?php echo strtolower(Lang::txt('MOD_RESOURCES_PUBLISHED')); ?>",
                        "data": <?php echo round(($this->published / $total) * 100, 2); ?>,
                        "color": "<?php echo $this->params->get("color_published", "#656565"); ?>"
                    },
                    {
                        "label": "<?php echo strtolower(Lang::txt('MOD_RESOURCES_DRAFT')); ?>",
                        "data": <?php echo round(($this->draft / $total) * 100, 2); ?>,
                        "color": "<?php echo $this->params->get("color_draft", "#999"); ?>"
                    },
                    {
                        "label": "<?php echo strtolower(Lang::txt('MOD_RESOURCES_PENDING')); ?>",
                        "data": <?php echo round(($this->pending / $total) * 100, 2); ?>,
                        "color": "<?php echo $this->params->get("color_pending", "#f9d180"); ?>"
                    },
                    {
                        "label": "<?php echo strtolower(Lang::txt('MOD_RESOURCES_REMOVED')); ?>",
                        "data": <?php echo round(($this->removed / $total) * 100, 2); ?>,
                        "color": "<?php echo $this->params->get("color_removed", "#ccc"); ?>"
                    },
                    {
                        "label": "<?php echo strtolower(Lang::txt('MOD_RESOURCES_UNPUBLISHED')); ?>",
                        "data": <?php echo round(($this->unpublished / $total) * 100, 2); ?>,
                        "color": "<?php echo $this->params->get("color_unpublished", "#fff"); ?>"
                    }
                ]
            }
            </script>
        <?php endif; ?>
        <p class="resources-total"><?php echo $total; ?></p>
    </div>
    <div class="overview-container resources-stats-overview">
        <table>
            <tbody>
                <tr>
                    <?php
                    $pubUrl = Route::url(
                        'index.php?option=com_resources&c=resources&status=1'
                    );
                    $pubTitle = Lang::txt('MOD_RESOURCES_PUBLISHED_TITLE');
                    ?>
                    <th scope="row">
                        <a href="<?php echo $pubUrl; ?>"
                            title="<?php echo $pubTitle; ?>"
                        >
                            <span class="resource-published"></span><?php
                                echo Lang::txt('MOD_RESOURCES_PUBLISHED');
                            ?>
                        </a>
                    </th>
                    <td>
                        <a href="<?php echo $pubUrl; ?>"
                            title="<?php echo $pubTitle; ?>"
                        >
                            <?php echo $this->escape($this->published); ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <?php
                    $pendUrl = Route::url(
                        'index.php?option=com_resources&c=resources&status=3'
                    );
                    $pendTitle = Lang::txt('MOD_RESOURCES_PENDING_TITLE');
                    ?>
                    <th scope="row" class="pending-items">
                        <a href="<?php echo $pendUrl; ?>"
                            title="<?php echo $pendTitle; ?>"
                        >
                            <span class="resource-pending"></span><?php
                                echo Lang::txt('MOD_RESOURCES_PENDING');
                            ?>
                        </a>
                    </th>
                    <td class="pending-items">
                        <a href="<?php echo $pendUrl; ?>"
                            title="<?php echo $pendTitle; ?>"
                        >
                            <?php echo $this->escape($this->pending); ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <?php
                    $draftUrl = Route::url(
                        'index.php?option=com_resources&c=resources&status=2'
                    );
                    $draftTitle = Lang::txt('MOD_RESOURCES_DRAFT_TITLE');
                    ?>
                    <th scope="row">
                        <a href="<?php echo $draftUrl; ?>"
                            title="<?php echo $draftTitle; ?>"
                        >
                            <span class="resource-draft"></span><?php
                                echo Lang::txt('MOD_RESOURCES_DRAFT');
                            ?>
                        </a>
                    </th>
                    <td>
                        <a href="<?php echo $draftUrl; ?>"
                            title="<?php echo $draftTitle; ?>"
                        >
                            <?php echo $this->escape($this->draft); ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <?php
                    $unpubUrl = Route::url(
                        'index.php?option=com_resources&c=resources&status=0'
                    );
                    $unpubTitle = Lang::txt('MOD_RESOURCES_UNPUBLISHED_TITLE');
                    ?>
                    <th scope="row">
                        <a href="<?php echo $unpubUrl; ?>"
                            title="<?php echo $unpubTitle; ?>"
                        >
                            <span class="resource-removed"></span><?php
                                echo Lang::txt('MOD_RESOURCES_UNPUBLISHED');
                            ?>
                        </a>
                    </th>
                    <td>
                        <a href="<?php echo $unpubUrl; ?>"
                            title="<?php echo $unpubTitle; ?>"
                        >
                            <?php echo $this->escape($this->unpublished); ?>
                        </a>
                    </td>
                </tr>
                <tr>
                    <?php
                    $remUrl = Route::url(
                        'index.php?option=com_resources&c=resources&status=4'
                    );
                    $remTitle = Lang::txt('MOD_RESOURCES_REMOVED_TITLE');
                    ?>
                    <th scope="row">
                        <a href="<?php echo $remUrl; ?>"
                            title="<?php echo $remTitle; ?>"
                        >
                            <span class="resource-unpublished"></span><?php
                                echo Lang::txt('MOD_RESOURCES_REMOVED');
                            ?>
                        </a>
                    </th>
                    <td>
                        <a href="<?php echo $remUrl; ?>"
                            title="<?php echo $remTitle; ?>"
                        >
                            <?php echo $this->escape($this->removed); ?>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>