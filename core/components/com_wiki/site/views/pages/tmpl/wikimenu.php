<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access.
defined('_HZEXEC_') or die();

?>
        <div class="container">
            <h3><?php echo Lang::txt('COM_WIKI_SEARCH'); ?></h3>

            <?php $searchAction = Route::url($this->page->link('base') . '&pagename=Special:Search'); ?>
            <form action="<?php echo $searchAction; ?>" method="get">
                <fieldset>
                    <legend><?php echo Lang::txt('COM_WIKI_SEARCH_LEGEND'); ?></legend>
                    <label for="page-search-q">
                        <span><?php echo Lang::txt('COM_WIKI_SEARCH'); ?></span>
                        <input type="text" name="q" id="page-search-q" value=""
                            placeholder="<?php echo Lang::txt('COM_WIKI_SEARCH_PLACEHOLDER'); ?>" />
                    </label>
                    <input type="submit" class="btn page-search-submit"
                        value="<?php echo Lang::txt('COM_WIKI_GO'); ?>" />
                </fieldset>
            </form>
        </div>

        <div class="container">
            <h3><?php echo Lang::txt('COM_WIKI'); ?></h3>
            <ul id="useroptions">
                <li class="page-main">
                    <a href="<?php echo Route::url($this->page->link('base')); ?>">
                        <?php echo Lang::txt('COM_WIKI_MAIN_PAGE'); ?>
                    </a>
                </li>
                <li class="page-help">
                    <a href="<?php echo Route::url($this->page->link('base') . '&pagename=Help:Index'); ?>">
                        <?php echo Lang::txt('COM_WIKI_HELP'); ?>
                    </a>
                </li>
                <li class="page-index">
                    <a href="<?php echo Route::url($this->page->link('base') . '&pagename=Special:AllPages'); ?>">
                        <?php echo Lang::txt('COM_WIKI_PAGE_INDEX'); ?>
                    </a>
                </li>
                <li class="page-recent">
                    <a href="<?php echo Route::url($this->page->link('base') . '&pagename=Special:RecentChanges'); ?>">
                        <?php echo Lang::txt('COM_WIKI_SPECIAL_RECENT_CHANGES'); ?>
                    </a>
                </li>
            </ul>
        </div>

    <?php if ($this->page->getNamespace() != 'special') { ?>
        <div class="container">
            <h3><?php echo Lang::txt('COM_WIKI_TOOLS'); ?></h3>
            <ul>
                <li class="page-links">
                    <?php
                    $linksUrl = Route::url(
                        $this->page->link('base')
                        . '&pagename=Special:Links&page=' . $this->page->get('pagename')
                        . '&version=' . $this->page->get('version_id')
                    );
                    ?>
                    <a href="<?php echo $linksUrl; ?>">
                        <?php echo Lang::txt('COM_WIKI_SPECIAL_LINKS'); ?>
                    </a>
                </li>
                <li class="page-cite">
                    <?php
                    $citeUrl = Route::url(
                        $this->page->link('base')
                        . '&pagename=Special:Cite&page=' . $this->page->get('pagename')
                        . '&version=' . $this->page->get('version_id')
                    );
                    ?>
                    <a href="<?php echo $citeUrl; ?>">
                        <?php echo Lang::txt('COM_WIKI_SPECIAL_CITE'); ?>
                    </a>
                </li>
                <li class="page-pdf">
                    <a href="<?php echo Route::url($this->page->link('pdf')); ?>">
                        <?php echo Lang::txt('COM_WIKI_TAB_PDF'); ?>
                    </a>
                </li>
                <?php if (!User::isGuest() && $this->page->access('create')) { ?>
                    <li class="page-new" data-title="<?php echo Lang::txt('COM_WIKI_NEW_PAGE'); ?>">
                        <?php $newPageParam = ($this->page->get('scope') != 'site' ? 'action' : 'task'); ?>
                        <a href="<?php echo Route::url($this->page->link('base') . '&' . $newPageParam . '=new'); ?>">
                            <?php echo Lang::txt('COM_WIKI_NEW_PAGE'); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } else { ?>
        <?php if (!User::isGuest() && $this->page->access('create')) { ?>
            <div class="container">
                <h3><?php echo Lang::txt('COM_WIKI_TOOLS'); ?></h3>
                <ul>
                    <li class="page-new" data-title="<?php echo Lang::txt('COM_WIKI_NEW_PAGE'); ?>">
                        <?php $newPageParam = ($this->page->get('scope') != 'site' ? 'action' : 'task'); ?>
                        <a href="<?php echo Route::url($this->page->link('base') . '&' . $newPageParam . '=new'); ?>">
                            <?php echo Lang::txt('COM_WIKI_NEW_PAGE'); ?>
                        </a>
                    </li>
                </ul>
            </div>
        <?php } ?>
    <?php }
