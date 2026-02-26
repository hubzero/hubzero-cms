<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();
?>
<div id="abox-content">
    <h3><?php echo Lang::txt('PLG_PROJECTS_FILES_ADD_NEW_FOLDER'); ?> <?php if ($this->subdir) {
        $browseUrl = $this->model->link('files')
            . '&action=browse&connection=' . $this->connection->id;
        $crumbs = \Components\Projects\Helpers\Html::buildFileBrowserCrumbs(
            $this->subdir,
            $browseUrl,
            $parent,
            false,
            $this->connection->adapter()
        );
        ?> <?php echo Lang::txt('PLG_PROJECTS_FILES_IN'); ?> <?php echo $crumbs; ?></span> <?php
        } ?></h3>
            <?php  ?>
    <?php if ($this->getError()) : ?>
        <p class="witherror"><?php echo $this->getError(); ?></p>
    <?php else : ?>
        <form id="hubForm-ajax" method="post" action="<?php echo Route::url($this->url); ?>">
            <fieldset>
                <input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
                <input type="hidden" name="action" value="savedir" />
                <label>
                    <?php
                        $folderImg = rtrim(Request::base(true), '/')
                            . '/core/plugins/projects/files/assets/img/folder.gif';
                    ?>
                    <img src="<?php echo $folderImg; ?>" alt="" />
                    <input type="text" name="newdir" maxlength="100" value="untitled" />
                </label>
                <input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_SAVE'); ?>" />
                <input type="reset"
                    class="btn btn-cancel"
                    id="cancel-action"
                    value="<?php echo Lang::txt('JCANCEL'); ?>"/>
            </fieldset>
        </form>
    <?php endif; ?>
</div>
