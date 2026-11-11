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

$this
     ->css()
     ->css('connections')
     ->js()
     ->js('connections');

$layout = Request::getCmd('layout', 'list');
$hasPrivate = false;
$defaultName = $this->params->get('default_connection_name', '%s Master Repository');
?>

<ul id="page_options" class="layout">
    <li>
        <a class="layout-control layout-large-icon first<?= ($layout == 'large-icon') ? ' active' : '' ?>"
            data-class="large-icon" href="#" aria-label="<?= Lang::txt('PLG_PROJECTS_FILES_LAYOUT_GRID') ?>"><span
            class="sr-only"><?= Lang::txt('PLG_PROJECTS_FILES_LAYOUT_GRID') ?></span></a>
        <a class="layout-control layout-small-icon<?= ($layout == 'small-icon') ? ' active' : '' ?>"
            data-class="small-icon" href="#" aria-label="<?= Lang::txt('PLG_PROJECTS_FILES_LAYOUT_LIST') ?>"><span
            class="sr-only"><?= Lang::txt('PLG_PROJECTS_FILES_LAYOUT_LIST') ?></span></a>
        <a class="layout-control layout-list last<?= ($layout == 'list') ? ' active' : '' ?>" data-class="list" href="#"
            aria-label="<?= Lang::txt('PLG_PROJECTS_FILES_LAYOUT_DETAILS') ?>"><span
            class="sr-only"><?= Lang::txt('PLG_PROJECTS_FILES_LAYOUT_DETAILS') ?></span></a>
    </li>
</ul>

<div class="connections">
    <div class="connection-wrap default <?php echo $layout; ?>">
        <a class="connection" href="<?php echo Route::url($this->model->link('files') . '&action=browse'); ?>">
            <img src="/core/plugins/filesystem/local/assets/img/icon.png" alt="">
            <div class="name"><?php echo Lang::txt($defaultName, $this->model->get('title')); ?></div>
        </a>
    </div>

    <?php foreach ($this->connections as $connection) : ?>
        <?php $imgRel = '/plugins/filesystem/' . $connection->provider->alias . '/assets/img/icon.png'; ?>
        <?php $img = (is_file(PATH_APP . DS . $imgRel)) ? '/app' . $imgRel : '/core' . $imgRel; ?>
        <div class="connection-wrap <?php echo $layout; ?>">
            <a class="connection"
                href="<?= Route::url($this->model->link('files') . '&action=browse&connection=' . $connection->id) ?>">
                <?php if (!$connection->isShared()) : ?>
                    <?php $hasPrivate = true; ?>
                    <div class="private-connection"></div>
                <?php endif; ?>
                <img src="<?php echo $img; ?>" alt="" />
                <div class="name"><?php echo $this->escape($connection->name); ?></div>
            </a>
            <?php
                // Match connections.php::connectionOwnedOrManageableByUser(): a
                // project manager may act on any of the project's connections,
                // anyone else only on a private one of their own. thatICanView()
                // also returns the project's SHARED connections (owner_id
                // 0/NULL), so rendering these four links unconditionally handed
                // every member controls that then 403'd.
                //
                // The manager half is connectionManageableByUser() spelled out
                // rather than $this->model->access('manager'), which is also true
                // for a site-wide com_projects manager who is not a member of
                // this project -- someone the controller still refuses.
                $member = $this->model->member();

                $isProjectManager = $member
                    && $member->get('id')
                    && $member->get('status') == 1
                    && $member->get('role') == \Components\Projects\Models\Orm\Owner::ROLE_MANAGER;

                $mayActOnConnection = $isProjectManager
                    || (!empty($connection->owner_id)
                        && (int) $connection->owner_id === (int) User::get('id'));
            ?>
            <?php if ($mayActOnConnection) : ?>
            <div class="connection-actions">
                <?php
                $refreshaccessUrl = Route::url(
                    $this->model->link('files') . '&action=refreshaccess&connection=' . $connection->id,
                );
                ?>
                <a class="connection-refresh icon-refresh" title="<?= Lang::txt('Refresh Connection Credentials') ?>"
                    href="<?= $refreshaccessUrl ?>">
                    <?php echo Lang::txt('Refresh Connection Credentials'); ?>
                </a>
                <?php
                $refreshpathUrl = Route::url(
                    $this->model->link('files') . '&action=refreshpath&connection=' . $connection->id,
                );
                ?>
                <a class="connection-refreshpath icon-folder" title="<?= Lang::txt('Refresh Connection Path') ?>"
                    href="<?= $refreshpathUrl ?>">
                    <?php echo Lang::txt('Refresh Connection Path'); ?>
                </a>
                <?php
                $editconnectionUrl = Route::url(
                    $this->model->link('files') . '&action=editconnection&connection=' . $connection->id,
                );
                ?>
                <a class="connection-edit icon-edit" title="<?= Lang::txt('Edit Connection') ?>"
                    href="<?= $editconnectionUrl ?>">
                    <?php echo Lang::txt('Edit'); ?>
                </a>
                <?php
                $deleteconnectionUrl = Route::url(
                    $this->model->link('files') . '&action=deleteconnection&connection=' . $connection->id,
                );
                $confirmText = Lang::txt('Are you sure you want to delete this connection?');
                ?>
                <button type="submit" form="hz-post-action" formaction="<?= $deleteconnectionUrl ?>" class="connection-delete icon-delete" title="<?= Lang::txt('Delete Connection') ?>" data-confirm="<?= $confirmText ?>"><?= Lang::txt('Delete') ?></button>
            </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <form class="connection-wrap new-connection <?= $layout ?>"
        action="<?= Route::url($this->model->link('files') . '&action=newconnection') ?>" method="post">
        <fieldset class="connection">
            <div class="new"></div>
            <div class="name">
                <select name="provider_id" class="connection-type"
                    aria-label="<?= Lang::txt('PLG_PROJECTS_FILES_CONNECTION_TYPE') ?>">
                    <option value=""><?php echo Lang::txt('New Connection'); ?></option>
                    <?php foreach (\Components\Projects\Models\Orm\Provider::all() as $provider) : ?>
                        <option value="<?= $provider->id ?>"><?= $this->escape($provider->name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </fieldset>
    </form>

    <?php if ($hasPrivate) : ?>
        <div class="info private-explanation clear">
            <?php echo Lang::txt('PLG_PROJECTS_FILES_CONNECTIONS_PRIVATE_EXPLANATION'); ?>
        </div>
    <?php endif; ?>
</div>
