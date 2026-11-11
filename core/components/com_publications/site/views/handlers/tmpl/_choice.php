<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// no direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{
 *     configs: mixed,
 *     handler: mixed,
 *     item: mixed,
 *     props: mixed,
 *     publication: mixed,
 * } $this
 */

$class = $this->item->assigned && $this->item->active ? ' assigned' : ' unassigned';
?>

<div class="handlertype-<?php echo $this->handler->get('_name') . $class; ?>">
    <h3><?php echo $this->escape($this->configs->label); ?></h3>
    <p class="manage-handler">
        <?php
        $manageUrl = Route::url(
            'index.php?option=com_projects&alias=' . $this->publication->project_alias
                . '&active=publications&pid=' . $this->publication->id,
        );
        $manageUrl .= '?vid=' . $this->publication->version_id . '&amp;action=handler&amp;h='
            . $this->handler->get('_name') . '&amp;p=' . $this->props;
        if ($this->item->assigned && $this->item->active) {
            $manageLabel = Lang::txt('COM_PUBLICATIONS_HANDLER_VIEW_MANAGE');
        } else {
            $manageLabel = Lang::txt('COM_PUBLICATIONS_HANDLER_ACTIVATE');
        }
        ?>
        <a href="<?= $manageUrl ?>" class="showinbox box-expanded"><?= $manageLabel ?></a>
    </p>
</div>