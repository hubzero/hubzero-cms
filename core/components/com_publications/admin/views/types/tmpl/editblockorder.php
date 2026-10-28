<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js('curation.js');

$label = Lang::txt('COM_PUBLICATIONS_PUBLICATION');
$label2 = Lang::txt('COM_PUBLICATIONS_MASTER_TYPE');
$label3 = Lang::txt('COM_PUBLICATIONS_EDIT_BLOCK_ORDER');
Toolbar::title(
    $label . ' ' . $label2 . ' - ' . $this->row->type . ': ' . $label3,
    'publications'
);
Toolbar::save('saveblockorder');
Toolbar::cancel();

$params = new \Hubzero\Config\Registry($this->row->params);
$manifest  = $this->curation->_manifest;
$curParams = $manifest->params;
$blocks    = $manifest->blocks;

$blockSelection = array('active' => array());
$masterBlocks = array();
foreach ($this->blocks as $b) {
    $masterBlocks[$b->block] = $b;
}

?>

<?php
$backUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&task=edit&id=' . $this->row->id
);
$backLabel = Lang::txt('COM_PUBLICATIONS_MTYPE_BACK')
    . ' ' . $this->row->type
    . ' ' . Lang::txt('COM_PUBLICATIONS_MASTER_TYPE');
?>
<p class="backto"><a href="<?php echo $backUrl; ?>"><?php echo $backLabel; ?></a></p>

<form
    action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"
    method="post"
    id="item-form"
    name="adminForm"
>
    <fieldset class="adminform">
        <legend><span><?php echo Lang::txt('COM_PUBLICATIONS_EDIT_BLOCK_ORDER'); ?></span></legend>

        <input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="task" value="saveblockorder" />
        <input type="hidden" name="neworder" id="neworder" value="" />

        <p class="hint"><?php echo Lang::txt('COM_PUBLICATIONS_EDIT_BLOCK_ORDER_HINT'); ?></p>

        <div class="input-wrap">
            <ul class="orderlist" id="blockorder">
                <?php foreach ($blocks as $blockId => $block) {
                    $blockMaster = $masterBlocks[$block->name];
                    ?>
                    <li id="s-<?php echo $blockId; ?>" class="pick<?php if ($block->name != 'review') {
                        echo ' reorder';
                              } ?>"><?php echo $block->name; ?></li>
                <?php } ?>
            </ul>
        </div>
    </fieldset>
    <?php echo Html::input('token'); ?>
</form>