<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Pathway;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access.
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{book: mixed, page: mixed} $this
 */

Pathway::append(
    Lang::txt('COM_WIKI_SPECIAL_NEW_PAGES'),
    $this->page->link(),
);

$sort = strtolower(Request::getString('sort', 'created'));
if (!in_array($sort, ['created', 'title', 'summary', 'created_by'])) {
    $sort = 'created';
}
$dir = strtoupper(Request::getString('dir', 'DESC'));
if (!in_array($dir, ['ASC', 'DESC'])) {
    $dir = 'DESC';
}

$limit = Request::getInt('limit', Config::get('list_limit'));
$start = Request::getInt('limitstart', 0);

$filters = ['state' => [0, 1]];

if ($space = Request::getString('namespace', '')) {
    $filters['namespace'] = urldecode($space);
}

$rows = $this->book
    ->pages($filters)
    ->including([
        'versions',
        function ($version) {
            $version
                ->select('id')
                ->select('page_id')
                ->select('version')
                ->select('created_by')
                ->select('summary');
        },
    ])
    ->order('created', $dir)
    ->paginated()
    ->rows();

$altdir = ($dir == 'ASC') ? 'DESC' : 'ASC';
?>
<form method="get" action="<?php echo Route::url($this->page->link()); ?>">
    <p>
        <?php echo Lang::txt('COM_WIKI_SPECIAL_NEW_PAGES_ABOUT'); ?>
    </p>
    <div class="container">
        <table class="file entries">
            <thead>
                <tr>
                    <th scope="col">
                        <?php
                        $linkUrl = Route::url($this->page->link() . '&sort=created&dir=' . $altdir);
                        ?>
                        <a<?= $sort == 'created' ? ' class="active"' : '' ?> href="<?= $linkUrl ?>">
                            <?php
                            $colDateText = Lang::txt('COM_WIKI_COL_DATE');
                            ?>
                            <?= $sort == 'created' ? ($dir == 'ASC') ? '&uarr;' : '&darr;' : '' ?> <?= $colDateText ?>
                        </a>
                    </th>
                    <th scope="col">
                        <?php
                        $linkUrl = Route::url($this->page->link() . '&sort=title&dir=' . $altdir);
                        ?>
                        <a<?= $sort == 'title' ? ' class="active"' : '' ?> href="<?= $linkUrl ?>">
                            <?php
                            $colTitleText = Lang::txt('COM_WIKI_COL_TITLE');
                            ?>
                            <?= $sort == 'title' ? ($dir == 'ASC') ? '&uarr;' : '&darr;' : '' ?> <?= $colTitleText ?>
                        </a>
                    </th>
                    <th scope="col">
                        <a<?php echo $sort == 'created_by' ? ' class="active"' : ''; ?> href="<?php
                              echo Route::url($this->page->link() . '&sort=created_by&dir=' . $altdir);
                        ?>">
                            <?= $sort == 'created_by' ? ($dir == 'ASC') ? '&uarr;' : '&darr;' : '' ?>
                                <?= Lang::txt('COM_WIKI_COL_CREATOR') ?>
                        </a>
                    </th>
                    <th scope="col">
                        <?php
                        $linkUrl = Route::url($this->page->link() . '&sort=summary&dir=' . $altdir);
                        ?>
                        <a<?= $sort == 'summary' ? ' class="active"' : '' ?> href="<?= $linkUrl ?>">
                            <?= $sort == 'summary' ? ($dir == 'ASC') ? '&uarr;' : '&darr;' : '' ?>
                                <?= Lang::txt('COM_WIKI_COL_EDIT_SUMMARY') ?>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($rows) {
                foreach ($rows as $row) {
                    $creatorName = $row->creator->get('name', Lang::txt('COM_WIKI_UNKNOWN')) ?? '';
                    $name = $this->escape(stripslashes($creatorName));
                    if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels())) {
                        $name = '<a href="' . Route::url($row->creator->link()) . '">' . $name . '</a>';
                    }
                    ?>
                    <tr>
                        <td>
                            <time datetime="<?php echo $row->get('created'); ?>">
                                <?php echo $row->get('created'); ?>
                            </time>
                        </td>
                        <td>
                            <?php $linkText = trim($row->title ?? '') ?: trim($row->pagename ?? '') ?: Lang::txt(
                                'COM_WIKI_UNTITLED',
                            ); ?>
                            <a href="<?php echo Route::url($row->link()); ?>">
                                <?php echo $this->escape(stripslashes($linkText)); ?>
                            </a>
                        </td>
                        <td>
                            <?php echo $name; ?>
                        </td>
                        <td>
                            <span><?php
                                echo $this->escape(stripslashes($row->version->get('summary', '') ?? ''));
                            ?></span>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="4">
                        <?php echo Lang::txt('COM_WIKI_NONE'); ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <?php
        $pageNav = $rows->pagination;
        $pageNav->setAdditionalUrlParam('scope', $this->page->get('scope'));
        $pageNav->setAdditionalUrlParam('pagename', $this->page->get('pagename'));

        echo $pageNav;
        ?>
        <div class="clearfix"></div>
    </div>
</form>
