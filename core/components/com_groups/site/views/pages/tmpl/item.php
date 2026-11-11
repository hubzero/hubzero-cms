<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();
$cls = '';
$style = '';
if ($this->category !== null) {
    $cls .= ' category-' . $this->page->get('category');
    // the colour is a stored value going into a <style> block: hex digits only
    $this->css(
        '.category-' . (int) $this->page->get('category') . '{ border-left-color: #'
            . preg_replace('/[^0-9a-fA-F]/', '', (string) $this->category->get('color')) . '; }',
    );
}
if (isset($this->version) && $this->version->get('approved') == 0) {
    $cls .= ' not-approved';
}
?>
<div class="item-container <?php echo $cls; ?>">
    <div class="item-title">
        <?php if ($this->page->get('privacy') == 'members') : ?>
            <span class="icon-lock tooltips" title="<?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_PRIVATE'); ?>"></span>
        <?php endif; ?>
        <?php
        $editUrl = Route::url(
            'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&controller=pages&task=edit&pageid='
                . $this->page->get('id'),
        );
        ?>
        <a href="<?= $editUrl ?>">
            <?php echo $this->escape($this->page->get('title')); ?>
        </a>
    </div>

    <div class="item-sub" >
        <span tabindex="-1"><?php echo $this->page->url(); ?></span>
    </div>

    <?php if ($this->checkout) : ?>
        <div class="item-checkout">
            <?php $user = User::getInstance($this->checkout->userid); ?>
            <img width="15" src="<?php echo $user->picture(); ?>" />
            <?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_CHECKED_OUT', $user->get('id'), $user->get('name')); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($this->version) && $this->version->get('approved') == 0) : ?>
        <div class="item-approved">
            <?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_PENDING_APPROVAL'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->page->get('home') == 0) : ?>
        <div class="item-state">
            <?php if ($this->page->get('state') == 0) : ?>
                <?php
                $publishUrl = Route::url(
                    'index.php?option=com_groups&cn=' . $this->group->get('cn')
                        . '&controller=pages&task=publish&pageid=' . $this->page->get('id'),
                );
                $titleText = Lang::txt('COM_GROUPS_PAGES_PUBLISH_PAGE');
                ?>
                <button type="submit" form="hz-post-action" formaction="<?= $publishUrl ?>" class="unpublished tooltips"
                    title="<?= $titleText ?>"><?= Lang::txt('COM_GROUPS_PAGES_PUBLISH_PAGE') ?></button>
            <?php else : ?>
                <?php
                $unpublishUrl = Route::url(
                    'index.php?option=com_groups&cn=' . $this->group->get('cn')
                        . '&controller=pages&task=unpublish&pageid=' . $this->page->get('id'),
                );
                $titleText = Lang::txt('COM_GROUPS_PAGES_UNPUBLISH_PAGE');
                ?>
                <button type="submit" form="hz-post-action" formaction="<?= $unpublishUrl ?>" class="published tooltips"
                    title="<?= $titleText ?>"><?= Lang::txt('COM_GROUPS_PAGES_UNPUBLISH_PAGE') ?></button>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="item-preview">
        <?php
        $previewUrl = Route::url(
            'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&controller=pages&task=preview&pageid='
                . $this->page->get('id'),
        );
        ?>
        <a class="tooltips page-preview" title="<?= Lang::txt('COM_GROUPS_PAGES_PREVIEW_PAGE') ?>"
            href="<?= $previewUrl ?>"><?= Lang::txt('COM_GROUPS_PAGES_PREVIEW_PAGE') ?></a>
    </div>

    <div class="item-controls btn-group dropdown">
        <?php
        $editUrl = Route::url(
            'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&controller=pages&task=edit&pageid='
                . $this->page->get('id'),
        );
        ?>
        <a href="<?= $editUrl ?>" class="btn"><?= Lang::txt('COM_GROUPS_PAGES_MANAGE_PAGE') ?></a>
        <span class="btn dropdown-toggle"></span>
        <ul class="dropdown-menu">
            <?php
            $editUrl = Route::url(
                'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&controller=pages&task=edit&pageid='
                    . $this->page->get('id'),
            );
            ?>
            <li>
                <a class="icon-edit" href="<?= $editUrl ?>"> <?= Lang::txt('COM_GROUPS_PAGES_EDIT_PAGE_BACK') ?></a>
            </li>
            <?php
            $previewUrl = Route::url(
                'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&controller=pages&task=preview&pageid='
                    . $this->page->get('id'),
            );
            ?>
            <li><a class="icon-search page-preview"
                href="<?= $previewUrl ?>"> <?= Lang::txt('COM_GROUPS_PAGES_PREVIEW_PAGE') ?></a></li>
            <?php if ($this->page->get('home') == 0) : ?>
                <?php if ($this->page->get('state') == 0) : ?>
                    <?php
                    $publishUrl = Route::url(
                        'index.php?option=com_groups&cn=' . $this->group->get('cn')
                            . '&controller=pages&task=publish&pageid=' . $this->page->get('id'),
                    );
                    ?>
                    <li><button type="submit" form="hz-post-action" formaction="<?= $publishUrl ?>"
                        class="icon-ban-circle"> <?= Lang::txt('COM_GROUPS_PAGES_PUBLISH_PAGE') ?></button></li>
                <?php else : ?>
                    <?php
                    $unpublishUrl = Route::url(
                        'index.php?option=com_groups&cn=' . $this->group->get('cn')
                            . '&controller=pages&task=unpublish&pageid=' . $this->page->get('id'),
                    );
                    ?>
                    <li><button type="submit" form="hz-post-action" formaction="<?= $unpublishUrl ?>"
                        class="icon-success"> <?= Lang::txt('COM_GROUPS_PAGES_UNPUBLISH_PAGE') ?></button></li>
                <?php endif; ?>
            <?php endif; ?>
            <li class="divider"></li>
            <?php
            $versionsUrl = Route::url(
                'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&controller=pages&task=versions&pageid='
                    . $this->page->get('id'),
            );
            ?>
            <li><a class="icon-history page-history"
                href="<?= $versionsUrl ?>"> <?= Lang::txt('COM_GROUPS_PAGES_VERSION_HISTORY_PAGE') ?></a></li>
            

            <?php if ($this->page->get('home') == 0) : ?>
                <li class="divider"></li>
                <?php
                $deleteUrl = Route::url(
                    'index.php?option=com_groups&cn=' . $this->group->get('cn')
                        . '&controller=pages&task=delete&pageid=' . $this->page->get('id'),
                );
                ?>
                <li><button type="submit" form="hz-post-action" formaction="<?= $deleteUrl ?>"
                    class="icon-delete"> <?= Lang::txt('COM_GROUPS_PAGES_DELETE_PAGE') ?></button></li>
            <?php endif; ?>
        </ul>
    </div>

    <?php if ($this->page->get('home') == 0) : ?>
        <div class="item-mover"></div>
    <?php endif; ?>
</div>
