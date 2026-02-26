<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

$default_title = ($this->type == 'file') ? basename($this->item) : $this->item;

$name    = \Components\Projects\Helpers\Html::shortenFileName(basename($this->item), 70);
$dirname = dirname($this->item);
$inDir   = $dirname && $dirname != '.' ? ' in /'
    . \Components\Projects\Helpers\Html::shortenFileName(basename($dirname), 40) : '';
?>
<div id="abox-content">
    <h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTENT_EDIT_ITEM'); ?></h3>
    <?php
    // Display error  message
    if ($this->getError()) {
        echo '<p class="error">' . $this->getError() . '</p>';
    } else { ?>
        <form id="hubForm-ajax" method="post" action="<?php echo $this->url; ?>">
            <fieldset>
                <input type="hidden" name="id" value="<?php echo $this->project->get('id'); ?>" />
                <input type="hidden" name="action" value="saveitem" />
                <input type="hidden" name="active" value="publications" />
                <input type="hidden"
                    name="option"
                    value="<?php echo $this->project->isProvisioned() ? 'com_publications' : $this->option; ?>"/>
                <input type="hidden" name="pid" value="<?php echo $this->pid; ?>" />
                <input type="hidden" name="vid" value="<?php echo $this->vid; ?>" />
                <input type="hidden" name="item" value="<?php echo $this->item; ?>" />
                <input type="hidden" name="role" value="<?php echo $this->role; ?>" />
                <input type="hidden" name="type" value="<?php echo $this->type; ?>" />
                <input type="hidden" name="move" value="<?php echo $this->move; ?>" />
                <input type="hidden" name="selections" id="ajax-selections" value="" />
                <input type="hidden"
                    name="provisioned"
                    id="provisioned"
                    value="<?php echo $this->project->isProvisioned() ? 1 : 0; ?>"/>
                <?php if ($this->project->isProvisioned()) { ?>
                    <input type="hidden" name="task" value="submit" />
                <?php } ?>
            </fieldset>
            <div class="content-edit">
                <?php $itemLabel = ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTENT_ITEM')); ?>
                <p>
                    <span class="leftshift faded"><?php echo $itemLabel; ?>:</span>
                    <?php echo '<span class="prominent">' . $name . '</span>' . $inDir;  ?>
                </p>
                <?php $descLabel = ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_DESCRIPTION')); ?>
                <label for="title">
                    <span class="leftshift faded"><?php echo $descLabel; ?>:</span>
                    <input type="text"
                        name="title"
                        maxlength="100"
                        class="long"
                        value="<?php echo $this->row && $this->row->title ? $this->escape($this->row->title) : ''; ?>"/>
                    <span class="optional"><?php echo Lang::txt('OPTIONAL'); ?></span>
                </label>
                <p class="submitarea">
                    <input type="submit"
                        class="btn"
                        value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SAVE'); ?>"/>
                    <?php if ($this->ajax) { ?>
                        <input type="reset"
                            id="cancel-action"
                            class="btn btn-cancel"
                            value="<?php echo Lang::txt('JCANCEL'); ?>"/>
                    <?php } else {
                        $rtn = Request::getString('HTTP_REFERER', $this->url, 'server');
                        ?>
                        <a href="<?php echo $rtn; ?>" class="btn btn-cancel"><?php echo Lang::txt('JCANCEL'); ?></a>
                    <?php } ?>
                </p>
            </div>
        </form>
        <div class="clear"></div>
    <?php } ?>
</div>