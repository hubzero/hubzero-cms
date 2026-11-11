<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

if (!isset($this->controller)) {
    $this->controller = Request::getWord('controller', 'page');
}
?>
    <div id="<?php echo ($this->sub) ? 'sub-content-header-extra' : 'content-header-extra'; ?>">
        <ul id="<?php echo ($this->sub) ? 'page_options' : 'useroptions'; ?>">
        <?php if (!User::isGuest() && $this->page->access('create')) { ?>
            <li class="page-new" data-title="<?php echo Lang::txt('COM_WIKI_NEW_PAGE'); ?>">
                <?php
                    $newPageUrl = Route::url(
                        $this->page->link('base') . '&'
                        . ($this->sub ? 'action' : 'task') . '=new',
                    );
                ?>
                <a class="icon-add add btn"
                    href="<?php echo $newPageUrl; ?>">
                    <?php echo Lang::txt('COM_WIKI_NEW_PAGE'); ?>
                </a>
            </li>
        <?php } ?>
            <?php /*<li class="page-index" data-title="<?php echo Lang::txt('COM_WIKI_PAGE_INDEX'); ?>">
                <a class="icon-index index btn"
                    href="<?php echo Route::url($this->page->link('base') . '&pagename=Special:AllPages'); ?>"
                    title="<?php echo Lang::txt('COM_WIKI_PAGE_INDEX'); ?>">
                    <span><?php echo Lang::txt('COM_WIKI_INDEX'); ?></span>
                </a>
            </li>
            <li class="page-search" data-title="<?php echo Lang::txt('COM_WIKI_SEARCH'); ?>">
                <a class="icon-search search btn"
                    href="<?php echo Route::url($this->page->link('base') . '&pagename=Special:Search'); ?>">
                    <?php echo Lang::txt('COM_WIKI_SEARCH'); ?>
                </a>
                <div class="page-search-form">
                    <form action="<?php echo Route::url($this->page->link('base') . '&pagename=Special:Search'); ?>"
                        method="post">
                        <fieldset>
                            <legend><?php echo Lang::txt('COM_WIKI_SEARCH_LEGEND'); ?></legend>
                            <label for="page-search-q">
                                <span><?php echo Lang::txt('COM_WIKI_SEARCH'); ?></span>
                                <input type="text"
                                    name="q"
                                    id="page-search-q"
                                    value=""
                                    placeholder="<?php echo Lang::txt('COM_WIKI_SEARCH_PLACEHOLDER'); ?>"/>
                            </label>
                            <input type="submit"
                                class="page-search-submit"
                                value="<?php echo Lang::txt('COM_WIKI_GO'); ?>"/>
                        </fieldset>
                    </form>
                </div>
            </li>*/ ?>
        </ul>
    </div><!-- / #content-header-extra -->

    <ul class="sub-menu">
        <?php
        $activeClass = $this->controller == 'page' && ($this->task == 'display' || !$this->task) ? ' active' : '';
        ?>
        <li class="page-text<?= $activeClass ?>">
            <a href="<?php echo Route::url($this->page->link()); ?>"
                title="<?php echo Lang::txt('COM_WIKI_TAB_ARTICLE'); ?>">
                <span><?php echo Lang::txt('COM_WIKI_TAB_ARTICLE'); ?></span>
            </a>
        </li>
<?php if ($this->page->exists() && !$this->page->isDeleted() && $this->page->get('namespace') != 'special') { ?>
    <?php if (
        ($this->page->isLocked() && $this->page->access('manage'))
        || (!$this->page->isLocked() && $this->page->access('edit'))
) { ?>
        <li class="page-edit<?php echo $this->controller == 'page' && $this->task == 'edit' ? ' active' : ''; ?>">
            <a href="<?php echo Route::url($this->page->link('edit')); ?>"
                title="<?php echo Lang::txt('COM_WIKI_TAB_EDIT'); ?>">
                <span><?php echo Lang::txt('COM_WIKI_TAB_EDIT'); ?></span>
            </a>
        </li>
    <?php } ?>
    <?php if ($this->page->access('view', 'comment')) { ?>
        <li class="page-comments<?php echo $this->controller == 'comments' ? ' active' : ''; ?>">
            <a href="<?php echo Route::url($this->page->link('comments')); ?>"
                title="<?php echo Lang::txt('COM_WIKI_TAB_COMMENTS'); ?>">
                <span><?php echo Lang::txt('COM_WIKI_TAB_COMMENTS'); ?></span>
            </a>
        </li>
    <?php } ?>
        <li class="page-history<?php echo $this->controller == 'history' ? ' active' : ''; ?>">
            <a href="<?php echo Route::url($this->page->link('history')); ?>"
                title="<?php echo Lang::txt('COM_WIKI_TAB_HISTORY'); ?>">
                <span><?php echo Lang::txt('COM_WIKI_TAB_HISTORY'); ?></span>
            </a>
        </li>
        <li class="page-pdf">
            <a href="<?php echo Route::url($this->page->link('pdf')); ?>"
                title="<?php echo Lang::txt('COM_WIKI_TAB_PDF'); ?>">
                <span><?php echo Lang::txt('COM_WIKI_TAB_PDF'); ?></span>
            </a>
        </li>
    <?php
    if (
        ($this->page->isLocked() && $this->page->access('manage', 'page'))
            || (!$this->page->isLocked() && $this->page->access('delete', 'page'))
    ) { ?>
        <li class="page-delete<?php echo $this->controller == 'page' && $this->task == 'delete' ? ' active' : ''; ?>">
            <a href="<?php echo Route::url($this->page->link('delete')); ?>"
                title="<?php echo Lang::txt('COM_WIKI_DELETE_PAGE'); ?>">
                <span><?php echo Lang::txt('COM_WIKI_DELETE_PAGE'); ?></span>
            </a>
        </li>
    <?php } ?>
<?php } ?>
    </ul>
