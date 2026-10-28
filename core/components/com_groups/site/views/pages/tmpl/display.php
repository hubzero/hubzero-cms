<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<ul class="toolbar toolbar-pages">
    <li class="new">
        <?php $url = Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')); ?>
        <a class="btn icon-add" href="<?php echo $url . '&controller=pages&task=add'; ?>">
            <?php echo Lang::txt('COM_GROUPS_PAGES_NEW_PAGE'); ?>
        </a>
    </li>
    <li class="filter">
        <select name="filer">
            <option value=""><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_FILTER'); ?></option>
            <?php foreach ($this->categories as $category) : ?>
                <?php $v2 = $category->get('color'); ?>
                <?php $v1 = $category->get('id'); ?>
                <?php $v0 = $category->get('title'); ?>
                <option data-color="#<?php echo $v2; ?>" value="<?php echo $v1; ?>"><?php echo $v0; ?></option>
            <?php endforeach; ?>
        </select>
    </li>
    <li class="filter-search-divider"><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_OR'); ?></li>
    <li class="search">
        <?php $val1 = Lang::txt('COM_GROUPS_PAGES_PAGE_SEARCH'); ?>
        <?php $val = $this->escape(isset($this->search) ? $this->search : ''); ?>
        <input type="text" name="search" placeholder="<?php echo $val1; ?>" value="<?php echo $val; ?>" />
    </li>
</ul>

<?php
    $this->view('list')
         ->set('level', 0)
         ->set('pages', $this->pages)
         ->set('categories', $this->categories)
         ->set('group', $this->group)
         ->set('config', $this->config)
         ->display();