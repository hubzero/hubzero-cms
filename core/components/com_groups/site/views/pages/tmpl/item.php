<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
$cls = '';
$style = '';
$pageBase = 'index.php?option=com_groups&cn=' .
    $this->group->get('cn') .
    '&controller=pages&task=';
if ($this->category !== null) {
    $cls .= ' category-' . $this->page->get('category');
    $this->css('.category-' .
        $this->page->get('category') .
        '{ border-left-color: #' .
        $this->category->get('color') .
        '; }');
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
        <?php $url = Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')); ?>
        <a href="<?php echo $url . '&controller=pages&task=edit&pageid=' . $this->page->get('id'); ?>">
            <?php echo $this->page->get('title'); ?>
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
                $pubTitle = Lang::txt('COM_GROUPS_PAGES_PUBLISH_PAGE');
                $pubUrl = Route::url(
                    $pageBase . 'publish&pageid=' . $this->page->get('id')
                );
                ?>
                <a class="unpublished tooltips"
                    title="<?php echo $pubTitle; ?>"
                    href="<?php echo $pubUrl; ?>"><?php echo $pubTitle; ?></a>
            <?php else : ?>
                <?php
                $unpubTitle = Lang::txt('COM_GROUPS_PAGES_UNPUBLISH_PAGE');
                $unpubUrl = Route::url(
                    $pageBase . 'unpublish&pageid=' . $this->page->get('id')
                );
                ?>
                <a class="published tooltips"
                    title="<?php echo $unpubTitle; ?>"
                    href="<?php echo $unpubUrl; ?>"><?php echo $unpubTitle; ?></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="item-preview">
        <?php
        $previewTitle = Lang::txt('COM_GROUPS_PAGES_PREVIEW_PAGE');
        $previewUrl = Route::url(
            $pageBase . 'preview&pageid=' . $this->page->get('id')
        );
        ?>
        <a class="tooltips page-preview"
            title="<?php echo $previewTitle; ?>"
            href="<?php echo $previewUrl; ?>"><?php echo $previewTitle; ?></a>
    </div>

    <div class="item-controls btn-group dropdown">
        <?php $pid = $this->page->get('id'); ?>
        <?php $val1 = Route::url($pageBase . 'edit&pageid=' . $pid); ?>
        <?php $val = Lang::txt('COM_GROUPS_PAGES_MANAGE_PAGE'); ?>
        <a href="<?php echo $val1; ?>" class="btn"><?php echo $val; ?></a>
        <span class="btn dropdown-toggle"></span>
        <ul class="dropdown-menu">
            <?php $val1 = Route::url($pageBase . 'edit&pageid=' . $pid); ?>
            <?php $val = Lang::txt('COM_GROUPS_PAGES_EDIT_PAGE_BACK'); ?>
            <li><a class="icon-edit" href="<?php echo $val1; ?>"> <?php echo $val; ?></a></li>
            <?php $val1 = Route::url($pageBase . 'preview&pageid=' . $pid); ?>
            <?php $val = Lang::txt('COM_GROUPS_PAGES_PREVIEW_PAGE'); ?>
            <li><a class="icon-search page-preview" href="<?php echo $val1; ?>"> <?php echo $val; ?></a></li>
            <?php if ($this->page->get('home') == 0) : ?>
                <?php if ($this->page->get('state') == 0) : ?>
                    <?php $val1 = Route::url($pageBase . 'publish&pageid=' . $pid); ?>
                    <?php $val = Lang::txt('COM_GROUPS_PAGES_PUBLISH_PAGE'); ?>
                    <li><a class="icon-ban-circle" href="<?php echo $val1; ?>"> <?php echo $val; ?></a></li>
                <?php else : ?>
                    <?php $val1 = Route::url($pageBase . 'unpublish&pageid=' . $pid); ?>
                    <?php $val = Lang::txt('COM_GROUPS_PAGES_UNPUBLISH_PAGE'); ?>
                    <li><a class="icon-success" href="<?php echo $val1; ?>"> <?php echo $val; ?></a></li>
                <?php endif; ?>
            <?php endif; ?>
            <li class="divider"></li>
            <?php $val1 = Route::url($pageBase . 'versions&pageid=' . $pid); ?>
            <?php $val = Lang::txt('COM_GROUPS_PAGES_VERSION_HISTORY_PAGE'); ?>
            <li><a class="icon-history page-history" href="<?php echo $val1; ?>"> <?php echo $val; ?></a></li>

            <?php if ($this->page->get('home') == 0) : ?>
                <li class="divider"></li>
                <?php $val1 = Route::url($pageBase . 'delete&pageid=' . $pid); ?>
                <?php $val = Lang::txt('COM_GROUPS_PAGES_DELETE_PAGE'); ?>
                <li><a class="icon-delete" href="<?php echo $val1; ?>"> <?php echo $val; ?></a></li>
            <?php endif; ?>
        </ul>
    </div>

    <?php if ($this->page->get('home') == 0) : ?>
        <div class="item-mover"></div>
    <?php endif; ?>
</div>