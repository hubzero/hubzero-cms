<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();
?>
<ul class="toolbar toolbar-categories">
    <li class="new">
        <?php
        $addUrl = Route::url(
            'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&controller=categories&task=add',
        );
        ?>
        <a class="btn icon-add" href="<?= $addUrl ?>">
            <?php echo Lang::txt('COM_GROUPS_PAGES_NEW_CATEGORY'); ?>
        </a>
    </li>
</ul>

<ul class="item-list categories">
    <?php if ($this->categories->count() > 0) : ?>
        <?php foreach ($this->categories as $category) : ?>
            <li>
                <div class="item-container">
                    <div class="item-title">
                        <?php
                        $editUrl = Route::url(
                            'index.php?option=com_groups&cn=' . $this->group->get('cn')
                                . '&controller=categories&task=edit&categoryid=' . $category->get('id'),
                        );
                        ?>
                        <a href="<?= $editUrl ?>">
                            <?php echo $this->escape($category->get('title')); ?>
                        </a>
                    </div>

                    <div class="item-sub">
                        <?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_X_PAGES', $category->getPages('count')); ?>
                    </div>

                    <?php $this->css(
                        '.item-list .category-' . $category->get('id') . ' { background-color: #'
                            . $category->get('color') . '; }',
                    ); ?>
                    <div class="item-color category-<?php echo $category->get('id'); ?>"></div>

                    <div class="item-controls btn-group dropdown">
                        <?php
                        $editUrl = Route::url(
                            'index.php?option=com_groups&cn=' . $this->group->get('cn')
                                . '&controller=categories&task=edit&categoryid=' . $category->get('id'),
                        );
                        ?>
                        <a href="<?= $editUrl ?>" class="btn">
                            <?php echo Lang::txt('COM_GROUPS_PAGES_MANAGE_CATEGORY'); ?>
                        </a>
                        <span class="btn dropdown-toggle"></span>
                        <ul class="dropdown-menu">
                            <?php
                            $editUrl = Route::url(
                                'index.php?option=com_groups&cn=' . $this->group->get('cn')
                                    . '&controller=categories&task=edit&categoryid=' . $category->get('id'),
                            );
                            ?>
                            <li><a class="icon-edit" href="<?= $editUrl ?>"> <?= Lang::txt('COM_GROUPS_PAGES_EDIT_CATEGORY') ?></a></li>
                            <li class="divider"></li>
                            <?php
                            $deleteUrl = Route::url(
                                'index.php?option=com_groups&cn=' . $this->group->get('cn')
                                    . '&controller=categories&task=delete&categoryid=' . $category->get('id'),
                            );
                            ?>
                            <li><button type="submit" form="hz-post-action" formaction="<?= $deleteUrl ?>" class="icon-delete"> <?= Lang::txt('COM_GROUPS_PAGES_DELETE_CATEGORY') ?></button></li>
                        </ul>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    <?php else : ?>
        <li class="no-results">
            <p><?php echo Lang::txt('COM_GROUPS_PAGES_NO_CATEGORIES'); ?></p>
        </li>
    <?php endif; ?>
</ul>