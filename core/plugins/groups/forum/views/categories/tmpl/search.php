<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

defined('_HZEXEC_') or die();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=forum';

$this->css()
     ->js();
?>
<ul id="page_options">
    <li>
        <a class="icon-folder categories btn"
            href="<?php echo Route::url($base); ?>"><?php echo Lang::txt('PLG_GROUPS_FORUM_ALL_CATEGORIES'); ?></a>
    </li>
</ul>

<section class="main section">
    <form action="<?php echo Route::url($base . '&scope=search'); ?>" method="get">
        <div class="container data-entry">
            <input class="entry-search-submit"
                type="submit"
                value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH'); ?>"/>
            <fieldset class="entry-search">
                <legend><?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_LEGEND'); ?></legend>
                <label for="entry-search-field"><?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_LABEL'); ?></label>
                <input type="text"
                    name="q"
                    id="entry-search-field"
                    value="<?php echo $this->escape($this->filters['search']); ?>"
                    placeholder="<?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_PLACEHOLDER'); ?>"/>
            </fieldset>
        </div><!-- / .container -->

        <div class="container">
            <table class="entries">
                <caption>
                    <?php echo Lang::txt('PLG_GROUPS_FORUM_SEARCH_FOR', $this->escape($this->filters['search'])); ?>
                </caption>
                <tbody>
                    <?php
                    $rows = $this->forum->posts($this->filters)
                        ->paginated()
                        ->rows();

                    if ($this->filters['search'] && $rows->count() > 0) {
                        foreach ($rows as $row) {
                            $title = $this->escape(stripslashes($row->get('title')));
                            $title = preg_replace(
                                '#' . $this->filters['search'] . '#i',
                                "<span class=\"highlight\">\\0</span>",
                                $title
                            );

                            $name = Lang::txt('JANONYMOUS');
                            if (!$row->get('anonymous')) {
                                $name = $this->escape(stripslashes($row->creator->get('name')));
                                if (in_array($row->creator->get('access'), User::getAuthorisedViewLevels())) {
                                    $name = '<a href="' . Route::url($row->creator->link()) . '">' . $name . '</a>';
                                }
                            }
                            $cls = array();
                            if ($row->get('closed')) {
                                $cls[] = 'closed';
                            }
                            if ($row->get('sticky')) {
                                $cls[] = 'sticky';
                            }
                            ?>
                            <tr<?php if (count($cls) > 0) {
                                echo ' class="' . implode(' ', $cls) . '"';
                               } ?>>
                                <th class="priority-5">
                                    <span class="entry-id"><?php echo $this->escape($row->get('id')); ?></span>
                                </th>
                                <td>
                                    <?php
                                    $catId = $row->get('category_id');
                                    $secId = $this->categories[$catId]->get('section_id');
                                    $entryUrl = Route::url(
                                        $base . '&scope='
                                        . $this->sections[$secId]->get('alias')
                                        . '/' . $this->categories[$catId]->get('alias')
                                        . '/' . $row->get('thread')
                                    );
                                    ?>
                                    <a class="entry-title"
                                        href="<?php echo $entryUrl; ?>">
                                        <span><?php echo $title; ?></span>
                                    </a>
                                    <span class="entry-details">
                                        <span class="entry-date">
                                            <?php echo $row->created('date'); ?>
                                        </span>
                                        <?php echo Lang::txt('by'); ?>
                                        <span class="entry-author">
                                            <?php echo $name; ?>
                                        </span>
                                    </span>
                                </td>
                                <td class="priority-4">
                                    <span><?php echo Lang::txt('PLG_GROUPS_FORUM_SECTION'); ?></span>
                                    <?php
                                    $sectionTitle = $this->sections[
                                        $this->categories[$row->get('category_id')]
                                            ->get('section_id')
                                    ]->get('title');
                                    $sectionName = $this->escape(
                                        \Hubzero\Utility\Str::truncate(
                                            $sectionTitle,
                                            100,
                                            array('exact' => true)
                                        )
                                    );
                                    ?>
                                    <span class="entry-details section-name">
                                        <?php echo $sectionName; ?>
                                    </span>
                                </td>
                                <td class="priority-3">
                                    <span><?php echo Lang::txt('PLG_GROUPS_FORUM_CATEGORY'); ?></span>
                                    <?php
                                    $categoryTitle = $this->categories[
                                        $row->get('category_id')
                                    ]->get('title');
                                    $categoryName = $this->escape(
                                        \Hubzero\Utility\Str::truncate(
                                            $categoryTitle,
                                            100,
                                            array('exact' => true)
                                        )
                                    );
                                    ?>
                                    <span class="entry-details category-name">
                                        <?php echo $categoryName; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td><?php echo Lang::txt('PLG_GROUPS_FORUM_CATEGORY_EMPTY'); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
            $pageNav = $rows->pagination;
            $pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
            $pageNav->setAdditionalUrlParam('active', 'forum');
            $pageNav->setAdditionalUrlParam('search', $this->filters['search']);
            echo $pageNav;
            ?>
        </div><!-- / .container -->
    </form>
</section><!-- /.main -->