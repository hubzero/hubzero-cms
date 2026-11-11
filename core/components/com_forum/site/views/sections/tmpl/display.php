<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

defined('_HZEXEC_') or die();

$this
     ->css()
     ->js();
?>
<header id="content-header">
    <h2><?php echo Lang::txt('COM_FORUM'); ?></h2>

    <div id="content-header-extra">
        <p>
            <?php
            $linkUrl = Route::url('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index');
            ?>
            <a class="icon-info btn popup" href="<?= $linkUrl ?>">
                <span><?php echo Lang::txt('COM_FORUM_GETTING_STARTED'); ?></span>
            </a>
        </p>
    </div>
</header>

<section class="main section">
<?php if ($this->sections->count()) { ?>
    <div class="section-inner hz-layout-with-aside">
        <div class="subject">
            <form action="<?= Route::url('index.php?option=' . $this->option . '&controller=categories&task=search') ?>"
                method="get">
                <div class="container data-entry">
                    <input class="entry-search-submit" type="submit" value="<?= Lang::txt('COM_FORUM_SEARCH') ?>" />
                    <fieldset class="entry-search">
                        <legend><span><?php echo Lang::txt('COM_FORUM_SEARCH_LEGEND'); ?></span></legend>

                        <label for="entry-search-field"><?php echo Lang::txt('COM_FORUM_SEARCH_LABEL'); ?></label>
                        <input type="text" name="q" id="entry-search-field"
                            value="<?= $this->escape($this->filters['search']) ?>"
                            placeholder="<?= Lang::txt('COM_FORUM_SEARCH_PLACEHOLDER') ?>" />
                    </fieldset>
                </div>
            </form>

            <?php
            foreach ($this->sections as $section) {
                $categories = $section
                    ->categories()
                    ->whereEquals('state', $this->filters['state'])
                    ->whereIn('access', $this->filters['access'])
                    ->order('title', 'asc')
                    ->rows();
                ?>
                <div class="container" id="section-<?php echo $section->get('id'); ?>">
                    <table class="entries categories">
                        <caption>
                            <?php
                            if (
                                $this->config->get('access-edit-section')
                                && $this->edit == $section->get('alias')
                                && $section->get('id')
                            ) { ?>
                                <form action="<?= Route::url('index.php?option=' . $this->option) ?>" method="post"
                                    id="s<?= $section->get('id') ?>">
                                    <input type="text" name="fields[title]"
                                        value="<?= $this->escape(stripslashes($section->get('title'))) ?>" />
                                    <input type="submit" value="<?php echo Lang::txt('JSUBMIT'); ?>" />
                                    <input type="hidden" name="fields[id]" value="<?php echo $section->get('id'); ?>" />
                                    <input type="hidden" name="fields[scope]" value="site" />
                                    <input type="hidden" name="fields[scope_id]" value="0" />
                                    <input type="hidden" name="fields[access]"
                                        value="<?= $this->escape($section->get('access')) ?>" />
                                    <input type="hidden" name="fields[state]"
                                        value="<?= $this->escape($section->get('state')) ?>" />
                                    <input type="hidden" name="controller" value="sections" />
                                    <input type="hidden" name="task" value="save" />
                                    <?php echo Html::input('token'); ?>
                                </form>
                            <?php } else { ?>
                                <?php echo $this->escape(stripslashes($section->get('title'))); ?>
                            <?php } ?>

                            <?php
                            if (
                                (
                                    $this->config->get('access-edit-section')
                                    || $this->config->get('access-delete-section')
                                )
                                && $section->get('id')
                            ) { ?>
                                <?php if ($this->config->get('access-delete-section')) { ?>
                                    <?php
                                    $deleteUrl = Route::url(
                                        'index.php?option=' . $this->option . '&section=' . $section->get('alias')
                                            . '&task=delete',
                                    );
                                    ?>
                                    <button type="submit" form="hz-post-action" formaction="<?= $deleteUrl ?>" class="icon-delete delete" title="<?= Lang::txt('JACTION_DELETE') ?>" data-txt-confirm="<?= Lang::txt('COM_FORUM_CONFIRM_DELETE') ?>"><span><?= Lang::txt('JACTION_DELETE') ?></span></button>
                                <?php } ?>
                                <?php
                                if (
                                    $this->config->get('access-edit-section')
                                    && $this->edit != $section->get('alias')
                                    && $section->get('id')
                                ) { ?>
                                    <?php
                                    $editUrl = Route::url(
                                        'index.php?option=' . $this->option . '&section=' . $section->get('alias')
                                            . '&task=edit#s' . $section->get('id'),
                                    );
                                    ?>
                                    <a class="icon-edit edit" href="<?= $editUrl ?>" title="<?= Lang::txt('JACTION_EDIT') ?>">
                                        <span><?php echo Lang::txt('JACTION_EDIT'); ?></span>
                                    </a>
                                <?php } ?>
                            <?php } ?>
                        </caption>
                        <?php if ($this->config->get('access-create-category')) { ?>
                            <tfoot>
                                <tr>
                                    <td<?php echo $section->categories()->total() > 0 ? ' colspan="5"' : ''; ?>>
                                        <?php
                                        $newUrl = Route::url(
                                            'index.php?option=' . $this->option . '&section=' . $section->get('alias')
                                                . '&task=new',
                                        );
                                        ?>
                                        <a class="icon-add add btn" id="addto-<?= $section->get('id') ?>" href="<?= $newUrl ?>">
                                            <span><?php echo Lang::txt('COM_FORUM_NEW_CATEGORY'); ?></span>
                                        </a>
                                    </td>
                                </tr>
                            </tfoot>
                        <?php } ?>
                        <tbody>
                            <?php if ($categories->count() > 0) { ?>
                                <?php foreach ($categories as $row) { ?>
                                    <?php
                                    $row->set('section_alias', $section->get('alias'));
                                    $icn = 'icon-folder';
                                    if ($row->get('closed')) {
                                        $icn = 'icon-lock';
                                    }
                                    ?>
                                    <tr<?php echo $row->get('closed') ? ' class="closed"' : ''; ?>>
                                        <th class="priority-5" scope="row">
                                            <span class="entry-identifier <?= $icn ?>"><?= $this->escape($row->get('id')) ?></span>
                                        </th>
                                        <td>
                                            <a class="entry-title" href="<?php echo Route::url($row->link()); ?>">
                                                <span><?= $this->escape(stripslashes($row->get('title'))) ?></span>
                                            </a>
                                            <span class="entry-details">
                                                <span class="entry-description">
                                                    <?php echo $this->escape(stripslashes($row->get('description'))); ?>
                                                </span>
                                            </span>
                                        </td>
                                        <td class="priority-3">
                                            <span><?php
                                            $threads = $row
                                                ->threads()
                                                ->whereEquals('state', $this->filters['state'])
                                                ->whereIn('access', $this->filters['access'])
                                                ->total();
                                            echo $threads; ?></span>
                                            <span class="entry-details">
                                                <?php echo Lang::txt('COM_FORUM_DISCUSSIONS'); ?>
                                            </span>
                                        </td>
                                        <td  class="priority-3">
                                            <span><?php echo ($threads) ? $row
                                                ->posts()
                                                ->whereEquals('state', $this->filters['state'])
                                                ->whereIn('access', $this->filters['access'])
                                                ->total() : 0; ?></span>
                                            <span class="entry-details">
                                                <?php echo Lang::txt('COM_FORUM_POSTS'); ?>
                                            </span>
                                        </td>
                                    <?php
                                    if (
                                        $this->config->get('access-edit-category')
                                        || $this->config->get('access-delete-category')
                                    ) { ?>
                                        <td class="entry-options">
                                            <?php
                                            if (
                                                (
                                                    $row->get('created_by') == User::get('id')
                                                    || $this->config->get('access-edit-category')
                                                )
                                                && $section->get('id')
                                            ) { ?>
                                                <a class="icon-edit edit" href="<?= Route::url($row->link('edit')) ?>"
                                                    title="<?= Lang::txt('JACTION_EDIT') ?>">
                                                    <span><?php echo Lang::txt('JACTION_EDIT'); ?></span>
                                                </a>
                                            <?php } ?>
                                            <?php
                                            if (
                                                $this->config->get('access-delete-category') && $section->get('id')
                                            ) { ?>
                                                <?php
                                                $actionUrl = Route::url($row->link('delete'));
                                                ?>
                                                <button type="submit" form="hz-post-action" formaction="<?= $actionUrl ?>" class="icon-delete delete tooltips" title="<?= Lang::txt('JACTION_DELETE') ?>" data-txt-confirm="<?= Lang::txt('COM_FORUM_CONFIRM_DELETE') ?>"><span><?= Lang::txt('JACTION_DELETE') ?></span></button>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                    <tr>
                                        <td><?php echo Lang::txt('COM_FORUM_SECTION_EMPTY'); ?></td>
                                    </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php
            }
            ?>
        </div><!-- /.subject -->
        <aside class="aside">
            <div class="container">
                <table>
                    <caption><?php echo Lang::txt('COM_FORUM_STATS'); ?></caption>
                    <tbody>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_FORUM_CATEGORIES'); ?></th>
                            <td><span
                                class="item-count"><?= $this->forum->count('categories', $this->filters) ?></span></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_FORUM_DISCUSSIONS'); ?></th>
                            <td><span
                                class="item-count"><?= $this->forum->count('threads', $this->filters) ?></span></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_FORUM_POSTS'); ?></th>
                            <td><span class="item-count"><?= $this->forum->count('posts', $this->filters) ?></span></td>
                        </tr>
                    </tbody>
                </table>
            </div><!-- / .container -->
            <div class="container">
                <h3><?php echo Lang::txt('COM_FORUM_LAST_POST'); ?></h3>
                <p>
                    <?php
                    $post = $this->forum->lastActivity();

                    if ($post->get('id')) {
                        $lname = Lang::txt('COM_FORUM_ANONYMOUS');
                        if (!$post->get('anonymous')) {
                            $lname = $this->escape(stripslashes($post->creator->get('name', $lname)));
                            if (in_array($post->creator->get('access'), User::getAuthorisedViewLevels())) {
                                $lname = '<a href="' . Route::url($post->creator->link()) . '">' . $lname . '</a>';
                            }
                        }
                        foreach ($this->sections as $section) {
                            if ($section->categories()->total() > 0) {
                                foreach ($section->categories()->rows() as $row) {
                                    if ($row->get('id') == $post->get('category_id')) {
                                        $post->set('category', $row->get('alias'));
                                        $post->set('section', $section->get('alias'));
                                        break;
                                    }
                                }
                            }
                        }
                        ?>
                        <a class="entry-comment" href="<?php echo Route::url($post->link()); ?>">
                            <?php echo \Hubzero\Utility\Str::truncate(strip_tags($post->get('comment')), 170); ?>
                        </a>
                        <span class="entry-author">
                            <?php echo $lname; ?>
                        </span>
                        <span class="entry-date">
                            <span class="entry-date-at"><?php echo Lang::txt('COM_FORUM_AT'); ?></span>
                            <span class="icon-time time"><time
                                datetime="<?= $post->get('created') ?>"><?= $post->created('time') ?></time></span>
                            <span class="entry-date-on"><?php echo Lang::txt('COM_FORUM_ON'); ?></span>
                            <span class="icon-date date"><time
                                datetime="<?= $post->get('created') ?>"><?= $post->created('date') ?></time></span>
                        </span>
                    <?php } else { ?>
                        <?php echo Lang::txt('COM_FORUM_NONE'); ?>
                    <?php } ?>
                </p>
            </div><!-- / .container -->

            <?php if ($this->config->get('access-create-section')) { ?>
                <div class="container">
                    <h3><?php echo Lang::txt('COM_FORUM_SECTION'); ?></h3>
                    <p>
                        <?php echo Lang::txt('COM_FORUM_SECTION_EXPLANATION'); ?>
                    </p>
                    <form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post">
                        <fieldset>
                            <legend><?php echo Lang::txt('COM_FORUM_NEW_SECTION'); ?></legend>
                            <div class="form-group">
                                <label for="field-title">
                                    <?php echo Lang::txt('COM_FORUM_FIELD_TITLE'); ?>
                                    <input type="text" class="form-control" name="fields[title]" id="field-title"
                                        value="" />
                                </label>
                            </div>
                            <p class="submit">
                                <input type="submit" class="btn" value="<?php echo Lang::txt('JACTION_CREATE'); ?>" />
                            </p>
                            <input type="hidden" name="task" value="save" />
                            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                            <input type="hidden" name="controller" value="sections" />
                            <input type="hidden" name="fields[id]" value="" />
                            <input type="hidden" name="fields[scope]" value="site" />
                            <input type="hidden" name="fields[scope_id]" value="0" />
                            <input type="hidden" name="fields[access]" value="1" />
                            <input type="hidden" name="fields[state]" value="1" />
                            <?php echo Html::input('token'); ?>
                        </fieldset>
                    </form>
                </div>
            <?php } ?>
        </aside><!-- / .aside -->
    </div>
<?php } else { ?>
    <div class="instructions icon-comments">
        <?php if ($this->config->get('access-create-section')) { ?>
            <p class="notification">
                <?= Lang::txt(
                    'COM_FORUM_EMPTY_MODERATOR',
                    Route::url('index.php?option=' . $this->option . '&action=populate'),
                ) ?>
            </p>

            <div class="container">
                <form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post">
                    <fieldset class="entry-section">
                        <legend><?php echo Lang::txt('COM_FORUM_NEW_SECTION'); ?></legend>

                        <div class="form-group">
                            <label for="field-title"><span><?= Lang::txt('COM_FORUM_FIELD_TITLE') ?></span></label>
                            <span class="input-cell">
                                <input type="text" class="form-control" name="fields[title]" id="field-title" value=""
                                    placeholder="<?= Lang::txt('COM_FORUM_ENTER_TITLE') ?>" />
                            </span>
                            <span class="input-cell">
                                <input type="submit" class="btn" value="<?php echo Lang::txt('JACTION_CREATE'); ?>" />
                            </span>
                        </div>

                        <input type="hidden" name="task" value="save" />
                        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                        <input type="hidden" name="controller" value="sections" />
                        <input type="hidden" name="fields[id]" value="" />
                        <input type="hidden" name="fields[scope]" value="site" />
                        <input type="hidden" name="fields[scope_id]" value="0" />
                        <input type="hidden" name="fields[access]" value="1" />
                        <input type="hidden" name="fields[state]" value="1" />

                        <?php echo Html::input('token'); ?>
                    </fieldset>
                </form>
            </div><!-- / .container -->
        <?php } else { ?>
            <p class="notification"><?php echo Lang::txt('COM_FORUM_EMPTY_NOT_MODERATOR'); ?></p>
        <?php } ?>
    </div>
<?php } ?>
</section><!-- /.main -->
