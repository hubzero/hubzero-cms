<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{archive: mixed, category: mixed, catid: mixed, filters: mixed} $this
 */

// Category navigation. Expects: option, archive, catid (the active top-level
// category id, 0 for all), category (the active category), filters (sort,
// search), which the links carry along so a reader keeps their search while
// narrowing by category.
$filters    = ['state' => 1, 'access' => User::getAuthorisedViewLevels()];
$categories = $this->archive->categories($filters);

$carry = '';
if (!empty($this->filters['sort'])) {
    $carry .= '&sort=' . $this->escape($this->filters['sort']);
}
if (!empty($this->filters['search'])) {
    $carry .= '&search=' . urlencode($this->filters['search']);
}
?>
<nav aria-label="<?php echo Lang::txt('COM_KB_CATEGORIES'); ?>">
    <div class="container">
        <h3><?php echo Lang::txt('COM_KB_CATEGORIES'); ?></h3>
        <ul class="categories">
            <li>
                <?php
                $linkUrl = Route::url('index.php?option=' . $this->option . '&section=all' . $carry);
                ?>
                <a<?= $this->catid <= 0 ? ' class="active" aria-current="page"' : '' ?> href="<?= $linkUrl ?>">
                    <?php echo Lang::txt('COM_KB_ALL_ARTICLES'); ?>
                </a>
            </li>
            <?php foreach ($categories as $row) { ?>
                <?php
                if ($row->get('articles', 0) <= 0) {
                    continue;
                }
                ?>
                <li>
                    <?php
                    $linkUrl = Route::url($row->link() . $carry);
                    ?>
                    <a <?= $this->catid == $row->get('id') ? 'class="active" aria-current="page" ' : '' ?>
                        href="<?= $linkUrl ?>">
                        <?= $this->escape(stripslashes($row->get('title'))) ?> <span
                            class="item-count"><?= $row->get('articles', 0) ?></span>
                    </a>
                    <?php if ($this->catid == $row->get('id') && $row->children($filters)->total() > 0) { ?>
                        <ul class="categories">
                        <?php foreach ($row->children() as $cat) { ?>
                            <li>
                                <?php
                                $linkUrl = Route::url($cat->link() . $carry);
                                $attributes = $this->category->get('id') == $cat->get('id')
                                    ? 'class="active" aria-current="page" '
                                    : '';
                                ?>
                                <a <?= $attributes ?> href="<?= $linkUrl ?>">
                                    <?= $this->escape(stripslashes($cat->get('title'))) ?> <span
                                        class="item-count"><?= $cat->get('articles', 0) ?></span>
                                </a>
                            </li>
                        <?php } ?>
                        </ul>
                    <?php } ?>
                </li>
            <?php } ?>
        </ul>
    </div><!-- / .container -->
</nav>
