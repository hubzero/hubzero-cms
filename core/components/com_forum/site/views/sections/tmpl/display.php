<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>
<header id="content-header">
    <h2><?php echo Lang::txt('COM_FORUM'); ?></h2>

    <div id="content-header-extra">
        <p>
            <?php
            $helpUrl = Route::url(
                'index.php?option=com_help&component='
                . substr($this->option, 4) . '&page=index'
            );
            ?>
            <a class="icon-info btn popup"
                href="<?php echo $helpUrl; ?>">
                <span><?php echo Lang::txt('COM_FORUM_GETTING_STARTED'); ?></span>
            </a>
        </p>
    </div>
</header>

<section class="main section">
<?php if ($this->sections->count()) { ?>
    <div class="section-inner hz-layout-with-aside">
        <div class="subject">
            <?php
            $searchAction = Route::url(
                'index.php?option=' . $this->option
                . '&controller=categories&task=search'
            );
            ?>
            <form action="<?php echo $searchAction; ?>" method="get">
                <div class="container data-entry">
                    <?php $searchTxt = Lang::txt('COM_FORUM_SEARCH'); ?>
                    <input class="entry-search-submit"
                        type="submit"
                        value="<?php echo $searchTxt; ?>" />
                    <fieldset class="entry-search">
                        <legend>
                            <span>
                                <?php echo Lang::txt('COM_FORUM_SEARCH_LEGEND'); ?>
                            </span>
                        </legend>

                        <label for="entry-search-field">
                            <?php echo Lang::txt('COM_FORUM_SEARCH_LABEL'); ?>
                        </label>
                        <?php
                        $searchVal = $this->escape($this->filters['search']);
                        $searchPlaceholder = Lang::txt(
                            'COM_FORUM_SEARCH_PLACEHOLDER'
                        );
                        ?>
                        <input type="text"
                            name="q"
                            id="entry-search-field"
                            value="<?php echo $searchVal; ?>"
                            placeholder="<?php echo $searchPlaceholder; ?>" />
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
                <div class="container"
                    id="section-<?php echo $section->get('id'); ?>">
                    <table class="entries categories">
                        <caption>
                            <?php
                            $canEditSection = $this->config->get('access-edit-section');
                            $sectionAlias = $section->get('alias');
                            $sectionId = $section->get('id');
                            if ($canEditSection && $this->edit == $sectionAlias && $sectionId) {
                                $sectionFormAction = Route::url(
                                    'index.php?option=' . $this->option
                                );
                                $sectionTitle = $this->escape(
                                    stripslashes($section->get('title'))
                                );
                                ?>
                                <form action="<?php echo $sectionFormAction; ?>"
                                    method="post"
                                    id="s<?php echo $sectionId; ?>">
                                    <input type="text"
                                        name="fields[title]"
                                        value="<?php echo $sectionTitle; ?>" />
                                    <input type="submit"
                                        value="<?php echo Lang::txt('JSUBMIT'); ?>" />
                                    <input type="hidden"
                                        name="fields[id]"
                                        value="<?php echo $sectionId; ?>" />
                                    <input type="hidden"
                                        name="fields[scope]" value="site" />
                                    <input type="hidden"
                                        name="fields[scope_id]" value="0" />
                                    <?php $accessVal = $this->escape($section->get('access')); ?>
                                    <input type="hidden"
                                        name="fields[access]"
                                        value="<?php echo $accessVal; ?>" />
                                    <?php $stateVal = $this->escape($section->get('state')); ?>
                                    <input type="hidden"
                                        name="fields[state]"
                                        value="<?php echo $stateVal; ?>" />
                                    <input type="hidden"
                                        name="controller" value="sections" />
                                    <input type="hidden"
                                        name="task" value="save" />
                                    <?php echo Html::input('token'); ?>
                                </form>
                            <?php } else { ?>
                                <?php
                                echo $this->escape(
                                    stripslashes($section->get('title'))
                                );
                                ?>
                            <?php } ?>

                            <?php
                            $canDeleteSection = $this->config->get('access-delete-section');
                            if (($canEditSection || $canDeleteSection) && $sectionId) {
                                ?>
                                <?php if ($canDeleteSection) { ?>
                                    <?php
                                    $deleteUrl = Route::url(
                                        'index.php?option=' . $this->option
                                        . '&section=' . $sectionAlias
                                        . '&task=delete'
                                    );
                                    $confirmTxt = Lang::txt('COM_FORUM_CONFIRM_DELETE');
                                    $deleteTxt = Lang::txt('JACTION_DELETE');
                                    ?>
                                    <a class="icon-delete delete"
                                        data-txt-confirm="<?php echo $confirmTxt; ?>"
                                        href="<?php echo $deleteUrl; ?>"
                                        title="<?php echo $deleteTxt; ?>">
                                        <span><?php echo $deleteTxt; ?></span>
                                    </a>
                                <?php } ?>
                                <?php
                                if ($canEditSection && $this->edit != $sectionAlias && $sectionId) {
                                    $editUrl = Route::url(
                                        'index.php?option=' . $this->option
                                        . '&section=' . $sectionAlias
                                        . '&task=edit#s' . $sectionId
                                    );
                                    $editTxt = Lang::txt('JACTION_EDIT');
                                    ?>
                                    <a class="icon-edit edit"
                                        href="<?php echo $editUrl; ?>"
                                        title="<?php echo $editTxt; ?>">
                                        <span><?php echo $editTxt; ?></span>
                                    </a>
                                <?php } ?>
                            <?php } ?>
                        </caption>
                        <?php if ($this->config->get('access-create-category')) { ?>
                            <tfoot>
                                <tr>
                                    <td<?php if ($section->categories()->total() > 0) {
                                        echo ' colspan="5"';
                                       } ?>>
                                        <?php
                                        $newCatUrl = Route::url(
                                            'index.php?option=' . $this->option
                                            . '&section=' . $sectionAlias
                                            . '&task=new'
                                        );
                                        ?>
                                        <a class="icon-add add btn"
                                            id="addto-<?php echo $sectionId; ?>"
                                            href="<?php echo $newCatUrl; ?>">
                                            <span>
                                                <?php echo Lang::txt('COM_FORUM_NEW_CATEGORY'); ?>
                                            </span>
                                        </a>
                                    </td>
                                </tr>
                            </tfoot>
                        <?php } ?>
                        <tbody>
                            <?php if ($categories->count() > 0) { ?>
                                <?php foreach ($categories as $row) { ?>
                                    <?php
                                    $row->set('section_alias', $sectionAlias);
                                    $icn = 'icon-folder';
                                    if ($row->get('closed')) {
                                        $icn = 'icon-lock';
                                    }
                                    ?>
                                    <tr<?php if ($row->get('closed')) {
                                        echo ' class="closed"';
                                       } ?>>
                                        <th class="priority-5" scope="row">
                                            <?php $rowId = $this->escape($row->get('id')); ?>
                                            <span class="entry-identifier <?php echo $icn; ?>">
                                                <?php echo $rowId; ?>
                                            </span>
                                        </th>
                                        <td>
                                            <?php $rowUrl = Route::url($row->link()); ?>
                                            <a class="entry-title"
                                                href="<?php echo $rowUrl; ?>">
                                                <span>
                                                    <?php
                                                    echo $this->escape(
                                                        stripslashes($row->get('title'))
                                                    );
                                                    ?>
                                                </span>
                                            </a>
                                            <span class="entry-details">
                                                <span class="entry-description">
                                                    <?php
                                                    echo $this->escape(
                                                        stripslashes($row->get('description'))
                                                    );
                                                    ?>
                                                </span>
                                            </span>
                                        </td>
                                        <td class="priority-3">
                                            <span><?php
                                            $threads = $row->threads()
                                                ->whereEquals('state', $this->filters['state'])
                                                ->whereIn('access', $this->filters['access'])
                                                ->total();
                                            echo $threads; ?></span>
                                            <span class="entry-details">
                                                <?php echo Lang::txt('COM_FORUM_DISCUSSIONS'); ?>
                                            </span>
                                        </td>
                                        <td  class="priority-3">
                                            <span><?php echo ($threads) ? $row->posts()
                                                ->whereEquals('state', $this->filters['state'])
                                                ->whereIn('access', $this->filters['access'])
                                                ->total() : 0; ?></span>
                                            <span class="entry-details">
                                                <?php echo Lang::txt('COM_FORUM_POSTS'); ?>
                                            </span>
                                        </td>
                                    <?php
                                    $canEditCat = $this->config->get('access-edit-category');
                                    $canDeleteCat = $this->config->get('access-delete-categort');
                                    if ($canEditCat || $canDeleteCat) {
                                        ?>
                                        <td class="entry-options">
                                            <?php
                                            $isCreator = $row->get('created_by') == User::get('id');
                                            if (($isCreator || $canEditCat) && $sectionId) {
                                                $editCatUrl = Route::url($row->link('edit'));
                                                $editCatTxt = Lang::txt('JACTION_EDIT');
                                                ?>
                                                <a class="icon-edit edit"
                                                    href="<?php echo $editCatUrl; ?>"
                                                    title="<?php echo $editCatTxt; ?>">
                                                    <span><?php echo $editCatTxt; ?></span>
                                                </a>
                                            <?php } ?>
                                            <?php
                                            $canDeleteCategory = $this->config->get('access-delete-category');
                                            if ($canDeleteCategory && $sectionId) {
                                                $delCatUrl = Route::url($row->link('delete'));
                                                $confirmDelTxt = Lang::txt('COM_FORUM_CONFIRM_DELETE');
                                                $delTxt = Lang::txt('JACTION_DELETE');
                                                ?>
                                                <a class="icon-delete delete tooltips"
                                                    data-txt-confirm="<?php echo $confirmDelTxt; ?>"
                                                    href="<?php echo $delCatUrl; ?>"
                                                    title="<?php echo $delTxt; ?>">
                                                    <span><?php echo $delTxt; ?></span>
                                                </a>
                                            <?php } ?>
                                        </td>
                                    <?php } ?>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                    <tr>
                                        <td>
                                            <?php echo Lang::txt('COM_FORUM_SECTION_EMPTY'); ?>
                                        </td>
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
                            <th scope="row">
                                <?php echo Lang::txt('COM_FORUM_CATEGORIES'); ?>
                            </th>
                            <td>
                                <span class="item-count">
                                    <?php
                                    echo $this->forum->count(
                                        'categories',
                                        $this->filters
                                    );
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo Lang::txt('COM_FORUM_DISCUSSIONS'); ?>
                            </th>
                            <td>
                                <span class="item-count">
                                    <?php
                                    echo $this->forum->count(
                                        'threads',
                                        $this->filters
                                    );
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo Lang::txt('COM_FORUM_POSTS'); ?>
                            </th>
                            <td>
                                <span class="item-count">
                                    <?php
                                    echo $this->forum->count(
                                        'posts',
                                        $this->filters
                                    );
                                    ?>
                                </span>
                            </td>
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
                            $lname = $this->escape(
                                stripslashes($post->creator->get('name', $lname))
                            );
                            $viewLevels = User::getAuthorisedViewLevels();
                            if (in_array($post->creator->get('access'), $viewLevels)) {
                                $creatorUrl = Route::url($post->creator->link());
                                $lname = '<a href="' . $creatorUrl . '">'
                                    . $lname . '</a>';
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
                        <?php $postUrl = Route::url($post->link()); ?>
                        <a class="entry-comment" href="<?php echo $postUrl; ?>">
                            <?php
                            echo \Hubzero\Utility\Str::truncate(
                                strip_tags($post->get('comment')),
                                170
                            );
                            ?>
                        </a>
                        <span class="entry-author">
                            <?php echo $lname; ?>
                        </span>
                        <span class="entry-date">
                            <span class="entry-date-at">
                                <?php echo Lang::txt('COM_FORUM_AT'); ?>
                            </span>
                            <span class="icon-time time">
                                <time datetime="<?php echo $post->get('created'); ?>">
                                    <?php echo $post->created('time'); ?>
                                </time>
                            </span>
                            <span class="entry-date-on">
                                <?php echo Lang::txt('COM_FORUM_ON'); ?>
                            </span>
                            <span class="icon-date date">
                                <time datetime="<?php echo $post->get('created'); ?>">
                                    <?php echo $post->created('date'); ?>
                                </time>
                            </span>
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
                    <?php
                    $sectionFormAction = Route::url(
                        'index.php?option=' . $this->option
                    );
                    ?>
                    <form action="<?php echo $sectionFormAction; ?>" method="post">
                        <fieldset>
                            <legend>
                                <?php echo Lang::txt('COM_FORUM_NEW_SECTION'); ?>
                            </legend>
                            <div class="form-group">
                                <label for="field-title">
                                    <?php echo Lang::txt('COM_FORUM_FIELD_TITLE'); ?>
                                    <input type="text"
                                        class="form-control"
                                        name="fields[title]"
                                        id="field-title"
                                        value="" />
                                </label>
                            </div>
                            <p class="submit">
                                <?php $createTxt = Lang::txt('JACTION_CREATE'); ?>
                                <input type="submit"
                                    class="btn"
                                    value="<?php echo $createTxt; ?>" />
                            </p>
                            <input type="hidden" name="task" value="save" />
                            <input type="hidden"
                                name="option"
                                value="<?php echo $this->option; ?>" />
                            <input type="hidden"
                                name="controller" value="sections" />
                            <input type="hidden" name="fields[id]" value="" />
                            <input type="hidden"
                                name="fields[scope]" value="site" />
                            <input type="hidden"
                                name="fields[scope_id]" value="0" />
                            <input type="hidden"
                                name="fields[access]" value="1" />
                            <input type="hidden"
                                name="fields[state]" value="1" />
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
            <?php
            $populateUrl = Route::url(
                'index.php?option=' . $this->option . '&action=populate'
            );
            $emptyMsg = Lang::txt('COM_FORUM_EMPTY_MODERATOR', $populateUrl);
            ?>
            <p class="notification"><?php echo $emptyMsg; ?></p>

            <div class="container">
                <?php
                $newSectionAction = Route::url(
                    'index.php?option=' . $this->option
                );
                ?>
                <form action="<?php echo $newSectionAction; ?>" method="post">
                    <fieldset class="entry-section">
                        <legend>
                            <?php echo Lang::txt('COM_FORUM_NEW_SECTION'); ?>
                        </legend>

                        <div class="form-group">
                            <label for="field-title">
                                <span>
                                    <?php echo Lang::txt('COM_FORUM_FIELD_TITLE'); ?>
                                </span>
                            </label>
                            <span class="input-cell">
                                <?php
                                $enterTitleTxt = Lang::txt('COM_FORUM_ENTER_TITLE');
                                ?>
                                <input type="text"
                                    class="form-control"
                                    name="fields[title]"
                                    id="field-title"
                                    value=""
                                    placeholder="<?php echo $enterTitleTxt; ?>" />
                            </span>
                            <span class="input-cell">
                                <?php $createTxt = Lang::txt('JACTION_CREATE'); ?>
                                <input type="submit"
                                    class="btn"
                                    value="<?php echo $createTxt; ?>" />
                            </span>
                        </div>

                        <input type="hidden" name="task" value="save" />
                        <input type="hidden"
                            name="option"
                            value="<?php echo $this->option; ?>" />
                        <input type="hidden"
                            name="controller" value="sections" />
                        <input type="hidden" name="fields[id]" value="" />
                        <input type="hidden"
                            name="fields[scope]" value="site" />
                        <input type="hidden"
                            name="fields[scope_id]" value="0" />
                        <input type="hidden"
                            name="fields[access]" value="1" />
                        <input type="hidden"
                            name="fields[state]" value="1" />

                        <?php echo Html::input('token'); ?>
                    </fieldset>
                </form>
            </div><!-- / .container -->
        <?php } else { ?>
            <?php $emptyNotMod = Lang::txt('COM_FORUM_EMPTY_NOT_MODERATOR'); ?>
            <p class="notification"><?php echo $emptyNotMod; ?></p>
        <?php } ?>
    </div>
<?php } ?>
</section><!-- /.main -->
