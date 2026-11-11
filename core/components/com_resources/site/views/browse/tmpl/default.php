<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Component;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

$sortbys = [];
if ($this->config->get('show_ranking')) {
    $sortbys['ranking'] = Lang::txt('COM_RESOURCES_RANKING');
}
$sortbys['date'] = Lang::txt('COM_RESOURCES_DATE_PUBLISHED');
$sortbys['date_modified'] = Lang::txt('COM_RESOURCES_DATE_MODIFIED');
$sortbys['title'] = Lang::txt('COM_RESOURCES_TITLE');

$this
     ->css()
     ->js();

// The attribute that marks the current value in the form fields below
$typeSelected = function ($value) {
    if ($this->filters['type'] == $value) {
        return ' selected="selected"';
    }

    return '';
};
?>
<header id="content-header">
    <h2><?php echo Lang::txt('COM_RESOURCES'); ?></h2>

    <div id="content-header-extra">
        <p>
            <a class="icon-add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=new'); ?>">
                <?php echo Lang::txt('COM_RESOURCES_SUBMIT_A_RESOURCE'); ?>
            </a>
        </p>
    </div>
</header>

<form action="<?= Route::url('index.php?option=' . $this->option . '&task=browse') ?>" id="resourcesform" method="get">
    <section class="main section">
        <div class="section-inner hz-layout-with-aside">
            <div class="subject">
                <div class="container data-entry">
                    <input class="entry-search-submit" type="submit" value="<?= Lang::txt('COM_RESOURCES_SEARCH') ?>" />
                    <fieldset class="entry-search">
                        <legend><?php echo Lang::txt('COM_RESOURCES_FIND_RESOURCE'); ?></legend>
                        <label for="entry-search-field"><?php echo Lang::txt('COM_RESOURCES_SEARCH_LABEL'); ?></label>
                        <input type="text" name="search" id="entry-search-field"
                            value="<?= $this->escape($this->filters['search']) ?>"
                            placeholder="<?= Lang::txt('COM_RESOURCES_SEARCH_LABEL') ?>" />
                        <input type="hidden" name="sortby" value="<?= $this->escape($this->filters['sortby']) ?>" />
                        <input type="hidden" name="tag" value="<?php echo $this->escape($this->filters['tag']); ?>" />
                    </fieldset>
                    <?php if ($this->filters['tag']) { ?>
                        <fieldset class="applied-tags">
                            <legend class="sr-only visually-hidden"><?= Lang::txt('COM_RESOURCES_TAGS') ?></legend>
                            <ol class="tags">
                            <?php
                            $url  = 'index.php?option=' . $this->option . '&task=browse';
                            $url .= (
                                $this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : ''
                            );
                            $url .= (
                                $this->filters['sortby'] ? '&sortby=' . $this->escape($this->filters['sortby']) : ''
                            );
                            $url .= ($this->filters['type'] ? '&type=' . $this->escape($this->filters['type']) : '');

                            $rt = new \Components\Resources\Helpers\Tags(0);
                            $tags = $rt->parseTopTags($this->filters['tag']);
                            foreach ($tags as $tag) {
                                ?>
                                <li>
                                    <?php
                                    $linkUrl = Route::url(
                                        $url . '&tag=' . implode(',', $rt->parseTopTags($this->filters['tag'], $tag)),
                                    );
                                    ?>
                                    <a class="tag" rel="nofollow" href="<?= $linkUrl ?>">
                                        <?php echo $this->escape(stripslashes($tag)); ?>
                                        <span class="remove" title="<?= Lang::txt('COM_RESOURCES_REMOVE_TAG') ?>">x</a>
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
                            </ol>
                        </fieldset>
                    <?php } ?>
                </div><!-- / .container -->

                <?php if (isset($this->filters['tag_ignored']) && count($this->filters['tag_ignored']) > 0) { ?>
                    <div class="warning">
                        <p><?php echo Lang::txt('COM_RESOURCES_SEARCH_TAG_LIMIT_REACHED'); ?></p>
                        <ol class="tags">
                        <?php
                        $url  = 'index.php?option=' . $this->option . '&task=browse';
                        $url .= ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
                        $url .= ($this->filters['sortby'] ? '&sortby=' . $this->escape($this->filters['sortby']) : '');
                        $url .= ($this->filters['type'] ? '&type=' . $this->escape($this->filters['type']) : '');

                        foreach ($this->filters['tag_ignored'] as $tag) {
                            ?>
                            <li>
                                <a rel="nofollow" href="<?php echo Route::url($url . '&tag=' . $tag); ?>">
                                    <?php echo $this->escape(stripslashes($tag)); ?>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                        </ol>
                    </div>
                <?php } ?>

                <div class="container">
                    <nav class="entries-filters" aria-label="<?= Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS') ?>">
                        <?php
                        $qs  = ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
                        $qs .= ($this->filters['type'] ? '&type=' . $this->escape($this->filters['type']) : '');
                        $qs .= ($this->filters['tag'] ? '&tag=' . $this->escape($this->filters['tag']) : '');
                        ?>
                        <ul class="entries-menu order-options">
                            <li>
                                <?php
                                $browseUrl = Route::url(
                                    'index.php?option=' . $this->option . '&task=browse&sortby=title' . $qs,
                                );
                                ?>
                                <a<?= ($this->filters['sortby'] == 'title') ? ' class="active"' : '' ?> href="<?= $browseUrl ?>" rel="nofollow"><?= Lang::txt('COM_RESOURCES_SORT_TITLE') ?></a>
                            </li>
                            <li>
                                <?php
                                $browseUrl = Route::url(
                                    'index.php?option=' . $this->option . '&task=browse&sortby=date' . $qs,
                                );
                                ?>
                                <a<?= ($this->filters['sortby'] == 'date') ? ' class="active"' : '' ?> href="<?= $browseUrl ?>" rel="nofollow"><?= Lang::txt('COM_RESOURCES_SORT_PUBLISHED') ?></a>
                            </li>
                            <?php if ($this->config->get('show_ranking')) { ?>
                                <li>
                                    <?php
                                    $browseUrl = Route::url(
                                        'index.php?option=' . $this->option . '&task=browse&sortby=ranking' . $qs,
                                    );
                                    ?>
                                    <a<?= ($this->filters['sortby'] == 'ranking') ? ' class="active"' : '' ?> href="<?= $browseUrl ?>" rel="nofollow"><?= Lang::txt('COM_RESOURCES_SORT_RANKING') ?></a>
                                </li>
                            <?php } ?>
                        </ul>

                        <?php if (count($this->types) > 0) { ?>
                            <ul class="entries-menu filter-options">
                                <li>
                                    <label for="filter-type"
                                        class="sr-only visually-hidden"><?= Lang::txt('COM_RESOURCES_TYPE') ?></label>
                                    <select name="type" id="filter-type"
                                        aria-label="<?= Lang::txt('COM_RESOURCES_TYPE') ?>">
                                        <option value="" <?= (!$this->filters['type']) ? ' selected="selected"' : '' ?>><?= Lang::txt('COM_RESOURCES_ALL_TYPES') ?></option>
                                        <?php foreach ($this->types as $item) { ?>
                                            <?php
                                            if (!$item->state) {
                                                continue;
                                            }
                                            if ($item->isForTools() && !Component::isEnabled('com_tools', true)) {
                                                continue;
                                            }
                                            ?>
                                            <option value="<?= $item->id ?>"<?= $typeSelected($item->id) ?>><?= $this->escape(stripslashes($item->type)) ?></option>
                                        <?php } ?>
                                    </select>
                                </li>
                            </ul>
                        <?php } ?>
                    </nav>

                    <div class="container-block">
                        <?php if ($this->results->count()) { ?>
                            <?php
                            $config = Component::params('com_resources');

                            $supported = [];

                            if ($tag = $config->get('supportedtag')) {
                                $rt = new \Components\Resources\Helpers\Tags(0);
                                $supported = $rt->getTagUsage($tag, 'id');
                            }

                            $this
                                 ->view('_list', 'browse')
                                 ->set('lines', $this->results)
                                 ->set('show_edit', $this->authorized)
                                 ->set('supported', $supported)
                                 ->display();
                            ?>
                            <div class="clear"></div>
                        <?php } else { ?>
                            <p class="warning"><?php echo Lang::txt('COM_RESOURCES_NO_RESULTS'); ?></p>
                        <?php } ?>
                    </div>
                    <?php
                    // Initiate paging
                    $pageNav = $this->results->pagination;
                    $pageNav->setAdditionalUrlParam('tag', $this->filters['tag']);
                    $pageNav->setAdditionalUrlParam('type', $this->filters['type']);
                    $pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);

                    echo $pageNav->render();
                    ?>
                    <div class="clearfix"></div>
                </div><!-- / .container -->
            </div><!-- / .subject -->
            <aside class="aside">
            <div class="container">
                <h3><?php echo Lang::txt('COM_RESOURCES_FIND_RESOURCE'); ?></h3>
                <p><?php echo Lang::txt('COM_RESOURCES_FIND_RESOURCE_DETAILS'); ?></p>
            </div><!-- / .container -->
            <div class="container">
                <h3><?php echo Lang::txt('COM_RESOURCES_POPULAR_TAGS'); ?></h3>
                <?php
                $rt = new \Components\Resources\Helpers\Tags();
                echo $rt->getTopTagCloud(20, $this->filters['tag']);
                ?>
                <p><?php echo Lang::txt('COM_RESOURCES_POPULAR_TAGS_HINT'); ?></p>
            </div>
        </aside><!-- / .aside -->
        </div>
    </section><!-- / .main section -->
</form>