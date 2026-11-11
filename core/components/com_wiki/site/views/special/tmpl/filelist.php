<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Pathway;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access.
defined('_HZEXEC_') or die();

Pathway::append(
    Lang::txt('COM_WIKI_SPECIAL_FILE_LIST'),
    $this->page->link(),
);

$database = App::get('db');

$sort = strtolower(Request::getString('sort', 'created'));
if (!in_array($sort, ['created', 'filename', 'description', 'created_by'])) {
    $sort = 'created';
}
$dir = strtoupper(Request::getString('dir', 'DESC'));
if (!in_array($dir, ['ASC', 'DESC'])) {
    $dir = 'DESC';
}

$pages  = \Components\Wiki\Models\Page::blank()->getTableName();
$attach = \Components\Wiki\Models\Attachment::blank()->getTableName();

$rows = \Components\Wiki\Models\Attachment::all()
    ->select($attach . '.*')
    ->select($pages . '.pagename')
    ->select($pages . '.path')
    ->select($pages . '.scope')
    ->select($pages . '.scope_id')
    ->join($pages, $pages . '.id', $attach . '.page_id')
    ->whereEquals($pages . '.scope', $this->book->get('scope'))
    ->whereEquals($pages . '.scope_id', $this->book->get('scope_id'))
    ->whereEquals($pages . '.state', '1')
    ->paginated()
    ->rows();

$altdir = ($dir == 'ASC') ? 'DESC' : 'ASC';
?>
<form method="get" action="<?php echo Route::url($this->page->link()); ?>">
    <p>
        <?php echo Lang::txt('COM_WIKI_SPECIAL_FILE_LIST_ABOUT'); ?>
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
                        $linkUrl = Route::url($this->page->link() . '&sort=filename&dir=' . $altdir);
                        ?>
                        <a<?= $sort == 'filename' ? ' class="active"' : '' ?> href="<?= $linkUrl ?>">
                            <?php
                            $colNameText = Lang::txt('COM_WIKI_COL_NAME');
                            ?>
                            <?= $sort == 'filename' ? ($dir == 'ASC') ? '&uarr;' : '&darr;' : '' ?> <?= $colNameText ?>
                        </a>
                    </th>
                    <th scope="col">
                        <?php echo Lang::txt('COM_WIKI_COL_PREVIEW'); ?>
                    </th>
                    <th scope="col">
                        <?php echo Lang::txt('COM_WIKI_COL_SIZE'); ?>
                    </th>
                    <th scope="col">
                        <a<?php echo $sort == 'created_by' ? ' class="active"' : ''; ?> href="<?php
                              echo Route::url($this->page->link() . '&sort=created_by&dir=' . $altdir);
                        ?>">
                            <?= $sort == 'created_by' ? ($dir == 'ASC') ? '&uarr;' : '&darr;' : '' ?>
                                <?= Lang::txt('COM_WIKI_COL_UPLOADER') ?>
                        </a>
                    </th>
                    <th scope="col">
                        <a<?php echo $sort == 'description' ? ' class="active"' : ''; ?> href="<?php
                              echo Route::url($this->page->link() . '&sort=description&dir=' . $altdir);
                        ?>">
                            <?= $sort == 'description' ? ($dir == 'ASC') ? '&uarr;' : '&darr;' : '' ?>
                                <?= Lang::txt('COM_WIKI_COL_DESCRIPTION') ?>
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ($rows->count()) {
                foreach ($rows as $row) {
                    $fsize = Lang::txt('COM_WIKI_UNKNOWN');
                    $filePath = $row->filespace() . DS . $row->get('page_id') . DS . $row->get('filename');
                    if (is_file($filePath)) {
                        $fsize = \Hubzero\Utility\Number::formatBytes(filesize($filePath));
                    }

                    $creatorName = $row->creator->get('name', Lang::txt('COM_WIKI_UNKNOWN')) ?? '';
                    $name = $this->escape(stripslashes($creatorName));
                    if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels())) {
                        $name = '<a href="' . Route::url($row->creator->link()) . '">' . $name . '</a>';
                    }
                    $pathPrefix = $row->get('path') ? $row->get('path') . '/' : '';
                    $filePagename = $pathPrefix . $row->get('pagename') . '/File:' . $row->get('filename');
                    $fileUrl = Route::url($this->page->link('base') . '&pagename=' . $filePagename);
                    $fileAlt = $this->escape(stripslashes($row->get('filename') ?? ''));
                    ?>
                    <tr>
                        <td>
                            <time datetime="<?php echo $row->get('created'); ?>">
                                <?php echo $row->get('created'); ?>
                            </time>
                        </td>
                        <td>
                            <a href="<?php echo $fileUrl; ?>">
                                <?php echo $fileAlt; ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($row->isImage()) { ?>
                                <a rel="lightbox" href="<?php echo $fileUrl; ?>">
                                    <img src="<?php echo $fileUrl; ?>" width="50"
                                        alt="<?php echo $fileAlt; ?>" />
                                </a>
                            <?php } ?>
                        </td>
                        <td>
                            <span><?php echo $fsize; ?></span>
                        </td>
                        <td>
                            <?php echo $name; ?>
                        </td>
                        <td>
                            <span><?php echo $this->escape(stripslashes($row->get('description', '') ?? '')); ?></span>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="6">
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
        $pageNav->setAdditionalUrlParam('scope', $this->page->get('path'));
        $pageNav->setAdditionalUrlParam('pagename', $this->page->get('pagename'));
        $pageNav->setAdditionalUrlParam('sort', $sort);
        $pageNav->setAdditionalUrlParam('dir', $dir);

        echo $pageNav;
        ?>
        <div class="clearfix"></div>
    </div>
</form>
