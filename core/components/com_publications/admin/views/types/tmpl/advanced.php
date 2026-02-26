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
$label3 = Lang::txt('COM_PUBLICATIONS_MTYPE_ADVANCED');
Toolbar::title(
    $label . ' ' . $label2 . ' - ' . $this->row->type . ': ' . $label3,
    'publications'
);
Toolbar::save('saveadvanced');
Toolbar::cancel();

$params = new Hubzero\Config\Registry($this->row->params);
$manifest  = $this->curation->_manifest;
$curParams = $manifest->params;
$blocks    = $manifest->blocks;

$blockSelection = array('active' => array());
?>

<?php
$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&task=saveadvanced'
);
$backUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&task=edit&id=' . $this->row->id
);
$backLabel = Lang::txt('COM_PUBLICATIONS_MTYPE_BACK')
    . ' ' . $this->row->type
    . ' ' . Lang::txt('COM_PUBLICATIONS_MASTER_TYPE');
?>
<form action="<?php echo $formAction; ?>" method="post" id="item-form" name="adminForm">
    <p><a class="button" href="<?php echo $backUrl; ?>"><?php echo $backLabel; ?></a></p>

    <fieldset class="adminform">
        <legend><span><?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_ADVANCED_CURATION_EDITING'); ?></span></legend>

        <input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="task" value="saveadvanced" />
        <input type="hidden" name="neworder" id="neworder" value="" />

        <p class="hint"><?php echo Lang::txt('COM_PUBLICATIONS_MTYPE_ADVANCED_CURATION_EDITING_HINT'); ?></p>

        <div class="input-wrap">
            <?php $val = json_encode($manifest, JSON_PRETTY_PRINT); ?>
            <textarea cols="50" rows="30" name="curation"><?php echo $val; ?></textarea>
        </div>
    </fieldset>
    <?php echo Html::input('token'); ?>
</form>
