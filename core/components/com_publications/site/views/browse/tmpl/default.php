<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$database = App::get('db');

$this->css()
     ->js();
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<form
    action="<?php echo Route::url('index.php?option=' . $this->option . '&task=browse'); ?>"
    id="resourcesform"
    method="get"
>
    <section class="main section">
        <div class="section-inner hz-layout-with-aside">
            <div class="subject">
                <div class="container data-entry">
                    <input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('Search'); ?>" />
                    <fieldset class="entry-search">
                        <legend><?php echo Lang::txt('Search'); ?></legend>
                        <label for="entry-search-field"><?php echo Lang::txt('Enter keyword or phrase'); ?></label>
                        <input
                            type="text"
                            name="search"
                            id="entry-search-field"
                            value="<?php echo $this->escape($this->filters['search']); ?>"
                            placeholder="<?php echo Lang::txt('Enter keyword or phrase'); ?>"
                        />
                        <input
                            type="hidden"
                            name="sortby"
                            value="<?php echo $this->escape($this->filters['sortby']); ?>"
                        />
                        <input type="hidden" name="tag" value="<?php echo $this->escape($this->filters['tag']); ?>" />
                    </fieldset>
                    <?php if ($this->filters['tag']) { ?>
                        <div class="applied-tags">
                            <ol class="tags">
                            <?php
                            $url  = 'index.php?option=' . $this->option . '&task=browse';
                            $url .= ($this->filters['search']
                                ? '&search=' . $this->escape($this->filters['search'])
                                : '');
                            $url .= ($this->filters['sortby']
                                ? '&sortby=' . $this->escape($this->filters['sortby'])
                                : '');
                            $url .= ($this->filters['category']
                                ? '&category=' . $this->escape($this->filters['category'])
                                : '');

                            $rt = new \Components\Publications\Helpers\Tags($database);
                            $tags = $rt->parseTopTags($this->filters['tag']);
                            foreach ($tags as $tag) {
                                ?>
                                <li>
                                    <?php
                                    $remainingTags = implode(',', $rt->parseTopTags($this->filters['tag'], $tag));
                                    $tagUrl = Route::url($url . '&tag=' . $remainingTags);
                                    ?>
                                    <a href="<?php echo $tagUrl; ?>">
                                        <?php echo $this->escape(stripslashes($tag)); ?>
                                        <span class="remove">x</a>
                                    </a>
                                </li>
                                <?php
                            }
                            ?>
                            </ol>
                        </div>
                    <?php } ?>
                </div><!-- / .container -->

                <?php if (isset($this->filters['tag_ignored']) && count($this->filters['tag_ignored']) > 0) { ?>
                    <div class="warning">
                        <p><
                            ?php echo Lang
                            ::txt('Searching only allows up to 5 tags. The following tags were ignored:'); ?></p>
                        <ol class="tags">
                        <?php
                        $url  = 'index.php?option=' . $this->option . '&task=browse';
                        $url .= ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
                        $url .= ($this->filters['sortby'] ? '&sortby=' . $this->escape($this->filters['sortby']) : '');
                        $url .= ($this->filters['type']
                            ? '&category=' . $this->escape($this->filters['category'])
                            : '');

                        foreach ($this->filters['tag_ignored'] as $tag) {
                            ?>
                            <li>
                                <a href="<?php echo Route::url($url . '&tag=' . $tag); ?>">
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
                    <?php $langTxt = Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS'); ?>
                    <nav class="entries-filters" aria-label="<?php echo $langTxt; ?>">
                        <?php
                        $qs  = ($this->filters['search'] ? '&search=' . $this->escape($this->filters['search']) : '');
                        $qs .= ($this->filters['category']
                            ? '&category=' . $this->escape($this->filters['category'])
                            : '');
                        $qs .= ($this->filters['tag']    ? '&tag=' . $this->escape($this->filters['tag'])       : '');
                        ?>
                        <?php
                        $browseBase = 'index.php?option=' . $this->option . '&task=browse&sortby=';
                        $titleUrl = Route::url($browseBase . 'title' . $qs);
                        $dateUrl = Route::url($browseBase . 'date' . $qs);
                        $titleCls = ($this->filters['sortby'] == 'title') ? ' class="active"' : '';
                        $dateCls = ($this->filters['sortby'] == 'date') ? ' class="active"' : '';
                        ?>
                        <ul class="entries-menu order-options">
                            <li>
                                <a<?php echo $titleCls; ?>
                                    href="<?php echo $titleUrl; ?>"
                                    title="<?php echo Lang::txt('Sort by title'); ?>"
                                >&darr; <?php echo Lang::txt('Title'); ?></a>
                            </li>
                            <li>
                                <a<?php echo $dateCls; ?>
                                    href="<?php echo $dateUrl; ?>"
                                    title="<?php echo Lang::txt('Sort by date published'); ?>"
                                >&darr; <?php echo Lang::txt('Published'); ?></a>
                            </li>
                            <?php if ($this->config->get('show_ranking')) {
                                $rankUrl = Route::url($browseBase . 'ranking' . $qs);
                                $rankCls = ($this->filters['sortby'] == 'ranking') ? ' class="active"' : '';
                                ?>
                            <li>
                                <a<?php echo $rankCls; ?>
                                    href="<?php echo $rankUrl; ?>"
                                    title="<?php echo Lang::txt('Sort by ranking'); ?>"
                                >&darr; <?php echo Lang::txt('Ranking'); ?></a>
                            </li>
                            <?php } ?>
                        </ul>
                        <?php if (count($this->categories) > 0) { ?>
                            <ul class="entries-menu filter-options">
                                <li>
                                    <label for="filter-type"><?php echo Lang::txt('Category'); ?></label>
                                    <select name="category" id="filter-type">
                                        <?php $langTxt1 = Lang::txt('All Categories'); ?>
                                        <?php $val2 = (!$this->filters['category']) ? ' selected="selected"' : ''; ?>
                                        <option value="" <?php echo $val2; ?>><?php echo $langTxt1; ?></option>
                                        <?php foreach ($this->categories as $item) { ?>
                                            <?php
                                            $catSel = ($this->filters['category'] == $item->id)
                                                ? ' selected="selected"' : '';
                                            $catName = $this->escape(stripslashes($item->name));
                                            ?>
                                            <option value="<?php echo $item->id; ?>"<?php echo $catSel; ?>>
                                                <?php echo $catName; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </li>
                            </ul>
                        <?php } ?>
                    </nav>

                    <?php
                    if ($this->results && $this->results->count() > 0) {
                        // Display List of items
                        $this->view('_list')
                             ->set('results', $this->results)
                             ->set('filters', $this->filters)
                             ->set('config', $this->config)
                             ->display();

                        $this->pageNav->setAdditionalUrlParam('tag', $this->filters['tag']);
                        $this->pageNav->setAdditionalUrlParam('category', $this->filters['category']);
                        $this->pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);

                        echo $this->pageNav->render();

                        echo '<div class="clear"></div>';
                    } else { ?>
                        <p class="warning"><?php echo Lang::txt('COM_PUBLICATIONS_NO_RESULTS'); ?></p>
                    <?php } ?>

                    <div class="clearfix"></div>
                </div><!-- / .container -->
            </div><!-- / .subject -->
            <div class="aside">
                <div class="container">
                    <h3><?php echo Lang::txt('Popular Tags'); ?></h3>
                    <?php
                    $rt = new \Components\Publications\Helpers\Tags($database);
                    echo $rt->getTopTagCloud(20, $this->filters['tag']);
                    ?>
                    <p><?php echo Lang::txt('Click a tag to see only publications with that tag.'); ?></p>
                </div>
            </div><!-- / .aside -->
        </div>
    </section><!-- / .main section -->
</form>