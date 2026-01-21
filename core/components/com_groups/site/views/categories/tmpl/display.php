<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<ul class="toolbar toolbar-categories">
    <li class="new">
        <?php $url = Route::url(
            'index.php?option=com_groups&cn=' .
            $this->group->get('cn') .
            '&controller=categories&task=add'
        ); ?>
        <a class="btn icon-add" href="<?php echo $url; ?>">
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
                        <?php $val = Route::url(
                            'index.php?option=com_groups&cn=' .
                                $this->group->get('cn') .
                                '&controller=categories&task=edit&categoryid=' .
                                $category->get('id')
                        ); ?>
                        <a href="<?php echo $val; ?>">
                            <?php echo $category->get('title'); ?>
                        </a>
                    </div>

                    <div class="item-sub">
                        <?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_X_PAGES', $category->getPages('count')); ?>
                    </div>

                    <?php $this->css(
                        '.item-list .category-' . $category->get('id') .
                        ' { background-color: #' . $category->get('color') . '; }'
                    ); ?>
                    <div class="item-color category-<?php echo $category->get('id'); ?>"></div>

                    <div class="item-controls btn-group dropdown">
                        <?php $val = Route::url(
                            'index.php?option=com_groups&cn=' .
                                $this->group->get('cn') .
                                '&controller=categories&task=edit&categoryid=' .
                                $category->get('id')
                        ); ?>
                        <a href="<?php echo $val; ?>" class="btn">
                            <?php echo Lang::txt('COM_GROUPS_PAGES_MANAGE_CATEGORY'); ?>
                        </a>
                        <span class="btn dropdown-toggle"></span>
                        <ul class="dropdown-menu">
                            <?php $val1 = Route::url(
                                'index.php?option=com_groups&cn=' .
                                    $this->group->get('cn') .
                                    '&controller=categories&task=edit&categoryid=' .
                                    $category->get('id')
                            ); ?>
                            <?php $val = Lang::txt('COM_GROUPS_PAGES_EDIT_CATEGORY'); ?>
                            <li><a class="icon-edit" href="<?php echo $val1; ?>"> <?php echo $val; ?></a></li>
                            <li class="divider"></li>
                            <?php $val1 = Route::url(
                                'index.php?option=com_groups&cn=' .
                                    $this->group->get('cn') .
                                    '&controller=categories&task=delete&categoryid=' .
                                    $category->get('id')
                            ); ?>
                            <?php $val = Lang::txt('COM_GROUPS_PAGES_DELETE_CATEGORY'); ?>
                            <li><a class="icon-delete" href="<?php echo $val1; ?>"> <?php echo $val; ?></a></li>
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