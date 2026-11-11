<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Component;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\User;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

$base = $this->member->link() . '&active=' . $this->name;

if (!$this->collection->get('layout')) {
    $this->collection->set('layout', 'grid');
}
$viewas = Request::getWord('viewas', $this->collection->get('layout'));
if (!in_array($viewas, ['grid', 'list'])) {
    $viewas = 'grid';
}

$this
     ->css()
     ->js('jquery.masonry', 'com_collections')
     ->js('jquery.infinitescroll', 'com_collections')
     ->js();

// Get the comments config value
$allow_comments = Component::params('com_collections')->get('allow_comments');
?>

<ul id="page_options">
    <li>
        <a class="icon-info btn popup"
            href="<?= Route::url('index.php?option=com_help&component=collections&page=index') ?>">
            <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_GETTING_STARTED'); ?></span>
        </a>
    </li>
</ul>

<form method="get" action="<?= Route::url($base . '&task=' . $this->collection->get('alias')) ?>" id="collections">
    <?php
    $this
         ->view('_submenu', 'collection')
         ->set('params', $this->params)
         ->set('option', $this->option)
         ->set('member', $this->member)
         ->set('name', $this->name)
         ->set('active', ($this->collection->exists() ? '' : 'posts'))
         ->set('collections', $this->collections)
         ->set('posts', $this->posts)
         ->set('followers', $this->followers)
         ->set('following', $this->following)
         ->display();
    ?>

    <?php if ($this->collection->exists()) { ?>
        <p class="overview">
            <span class="title count">
                "<?php echo $this->escape(stripslashes($this->collection->get('title'))); ?>"
            </span>
            <span class="posts count">
                <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_POSTS', $this->total); ?>
            </span>
            <?php if (!User::isGuest()) { ?>
                <?php if (!$this->params->get('access-create-item')) { ?>
                    <?php if ($this->collection->isFollowing()) { ?>
                        <button type="submit" form="hz-post-action"
                            formaction="<?= Route::url($this->collection->link() . '/unfollow') ?>"
                            class="icon-unfollow unfollow btn tooltips"
                            data-text-follow="<?= Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_THIS') ?>"
                            data-text-unfollow="<?= Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_THIS') ?>"
                            title="<?= Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_TITLE') ?>">
                            <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_THIS'); ?></span>
                        </button>
                    <?php } else { ?>
                        <button type="submit" form="hz-post-action"
                            formaction="<?= Route::url($this->collection->link() . '/follow') ?>"
                            class="icon-follow follow btn tooltips"
                            data-text-follow="<?= Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_THIS') ?>"
                            data-text-unfollow="<?= Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_THIS') ?>"
                            title="<?= Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_TITLE') ?>">
                            <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_THIS'); ?></span>
                        </button>
                    <?php } ?>
                    <a class="icon-repost repost btn tooltips"
                        title="<?= Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT_TITLE') ?>"
                        href="<?= Route::url($this->collection->link() . '/collect') ?>">
                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
                    </a>
                <?php } ?>
            <?php } ?>
            <span class="options sort-options">
                <?php
                $linkUrl = Route::url($this->collection->link() . '&sort=created&viewas=' . $viewas);
                $titleText = Lang::txt('PLG_MEMBERS_COLLECTIONS_CREATED_SORT');
                ?>
                <a href="<?= $linkUrl ?>"
                    class="icon-created<?= $this->filters['sort'] == 'created' ? ' selected' : '' ?>"
                    data-view="sort-created"
                    title="<?= $titleText ?>"><?= Lang::txt('PLG_MEMBERS_COLLECTIONS_CREATED_SORT') ?></a>
                <?php
                $linkUrl = Route::url($this->collection->link() . '&sort=ordering&viewas=' . $viewas);
                $titleText = Lang::txt('PLG_MEMBERS_COLLECTIONS_ORDERING_SORT');
                ?>
                <a href="<?= $linkUrl ?>"
                    class="icon-ordering<?= $this->filters['sort'] == 'ordering' ? ' selected' : '' ?>"
                    data-view="sort-ordering"
                    title="<?= $titleText ?>"><?= Lang::txt('PLG_MEMBERS_COLLECTIONS_ORDERING_SORT') ?></a>
            </span>
            <span class="options view-options">
                <?php
                $linkUrl = Route::url($this->collection->link() . '&sort=' . $this->filters['sort'] . '&viewas=grid');
                $titleText = Lang::txt('PLG_MEMBERS_COLLECTIONS_GRID_VIEW');
                ?>
                <a href="<?= $linkUrl ?>" class="icon-grid<?= $viewas == 'grid' ? ' selected' : '' ?>"
                    data-view="view-grid"
                    title="<?= $titleText ?>"><?= Lang::txt('PLG_MEMBERS_COLLECTIONS_GRID_VIEW') ?></a>
                <?php
                $linkUrl = Route::url($this->collection->link() . '&sort=' . $this->filters['sort'] . '&viewas=list');
                $titleText = Lang::txt('PLG_MEMBERS_COLLECTIONS_LIST_VIEW');
                ?>
                <a href="<?= $linkUrl ?>" class="icon-list<?= $viewas == 'list' ? ' selected' : '' ?>"
                    data-view="view-list"
                    title="<?= $titleText ?>"><?= Lang::txt('PLG_MEMBERS_COLLECTIONS_LIST_VIEW') ?></a>
            </span>
        </p>
    <?php } ?>

    <?php if ($this->rows->total() > 0) { ?>
        <?php
        $reorderUrl = Route::url(
            'index.php?option=com_collections&controller=posts&task=reorder&' . Session::getFormToken() . '=1',
        );
        ?>
        <div id="posts" data-base="<?= rtrim(Request::base(true), '/') ?>" data-update="<?= $reorderUrl ?>"
            class="view-<?= $viewas . ' ' . (User::isGuest() ? 'loggedout' : 'loggedin') ?>">
            <?php if ($this->params->get('access-create-collection') && !Request::getInt('no_html', 0)) { ?>
                <div class="post new-post" id="post_0">
                    <a class="icon-add add"
                        href="<?= Route::url($base . '&task=post/new&board=' . $this->collection->get('alias')) ?>">
                        <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NEW_POST'); ?>
                    </a>
                </div>
            <?php } ?>
        <?php
        foreach ($this->rows as $row) {
            $item = $row->item();
            ?>
            <div class="post <?= $item->type() ?>" id="post_<?= $row->get('id') ?>" data-id="<?= $row->get('id') ?>"
                data-closeup-url="<?= Route::url($base . '&task=post/' . $row->get('id')) ?>">
                <div class="content">
                    <?php
                        $this
                             ->view(
                                 'default_'
                                     . $item->layout(dirname(__DIR__, 2) . DS . 'post' . DS . 'tmpl', 'default_'),
                                 'post',
                             )
                             ->set('name', $this->name)
                             ->set('option', $this->option)
                             ->set('member', $this->member)
                             ->set('params', $this->params)
                             ->set('row', $row)
                             ->display();
                    ?>
                    <?php if ($tags = $item->tags('cloud')) { ?>
                        <div class="tags-wrap">
                            <?php echo $tags; ?>
                        </div>
                    <?php } ?>
                    <?php
                    $metadataUrl = Route::url(
                        'index.php?option=com_collections&controller=posts&task=metadata&post=' . $row->get('id'),
                    );
                    ?>
                    <div class="meta" data-metadata-url="<?= $metadataUrl ?>">
                        <p class="stats">
                            <span class="likes">
                                <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_LIKES', $item->get('positive', 0)); ?>
                            </span>
                            <?php
                            // Display comments count only if enabled
                            if ($allow_comments) :
                                ?>
                            <span class="comments">
                                <?= Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_COMMENTS', $item->get('comments', 0)) ?>
                            </span>
                            <?php endif; ?>
                            <span class="reposts">
                                <?= Lang::txt('PLG_MEMBERS_COLLECTIONS_NUM_REPOSTS', $item->get('reposts', 0)) ?>
                            </span>
                        </p>
                        <div class="actions">
                            <?php if (!User::isGuest()) { ?>
                                <?php //if ($item->get('created_by') == User::get('id')) {?>
                                <?php if ($row->get('created_by') == User::get('id')) { ?>
                                    <a class="btn edit" data-id="<?= $row->get('id') ?>"
                                        href="<?= Route::url($base . '&task=post/' . $row->get('id') . '/edit') ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_EDIT'); ?></span>
                                    </a>
                                <?php } else { ?>
                                    <?php
                                    $postUrl = Route::url($base . '&task=post/' . $row->get('id') . '/vote');
                                    ?>
                                    <button type="submit" form="hz-post-action" formaction="<?= $postUrl ?>"
                                        class="btn vote <?= ($item->get('voted')) ? 'unlike' : 'like' ?>"
                                        data-id="<?= $row->get('id') ?>"
                                        data-text-like="<?= Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE') ?>"
                                        data-text-unlike="<?= Lang::txt('Unlike') ?>">
                                        <span><?= ($item->get('voted')) ? Lang::txt('PLG_MEMBERS_COLLECTIONS_UNLIKE') : Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE') ?></span>
                                    </button>
                                <?php } ?>
                                <?php
                                // Let comment only if enabled
                                if ($allow_comments) :
                                    ?>
                                    <a class="btn comment" data-id="<?= $row->get('id') ?>" href="<?php echo Route::url('index.php?option=com_collections&controller=posts&post=' . $row->get('id') . '&task=comment'); //$base . '&task=post/' . $row->get('id') . '/comment');?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COMMENT'); ?></span>
                                    </a>
                                <?php endif; ?>
                                    <a class="btn repost" data-id="<?= $row->get('id') ?>"
                                        href="<?= Route::url($base . '&task=post/' . $row->get('id') . '/collect') ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
                                    </a>
                                <?php
                                if (
                                    $row->get('original')
                                    && (
                                        $item->get('created_by') == User::get('id')
                                        || $this->params->get('access-delete-item')
                                    )
                                ) { ?>
                                    <a class="btn delete" data-id="<?= $row->get('id') ?>"
                                        href="<?= Route::url($base . '&task=post/' . $row->get('id') . '/delete') ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE'); ?></span>
                                    </a>
                                <?php } elseif ($row->get('created_by') == User::get('id') || $this->params->get('access-edit-item')) { ?>
                                    <?php
                                    $postUrl = Route::url($base . '&task=post/' . $row->get('id') . '/remove');
                                    ?>
                                    <button type="submit" form="hz-post-action" formaction="<?= $postUrl ?>"
                                        class="btn unpost" data-id="<?= $row->get('id') ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_REMOVE'); ?></span>
                                    </button>
                                <?php } ?>
                            <?php } else { ?>
                                    <?php
                                    $linkUrl = Route::url(
                                        'index.php?option=com_users&view=login&return='
                                            . base64_encode(
                                                Route::url(
                                                    $base . '&task=' . $this->collection->get('alias'),
                                                    false,
                                                    true,
                                                ),
                                            ),
                                        false,
                                    );
                                    ?>
                                    <a class="btn vote like tooltips" href="<?= $linkUrl ?>"
                                        title="<?= Lang::txt('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_LIKE') ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE'); ?></span>
                                    </a>
                                <?php
                                    // Let comment only if enabled
                                if ($allow_comments) :
                                    ?>
                                    <a class="btn comment" data-id="<?= $row->get('id') ?>" href="<?php echo Route::url('index.php?option=com_collections&controller=posts&post=' . $row->get('id') . '&task=comment'); //$base . '&task=post/' . $row->get('id') . '/comment');?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COMMENT'); ?></span>
                                    </a>
                                <?php endif; ?>
                                    <?php
                                    $linkUrl = Route::url(
                                        'index.php?option=com_users&view=login&return='
                                            . base64_encode(
                                                Route::url(
                                                    $base . '&task=' . $this->collection->get('alias'),
                                                    false,
                                                    true,
                                                ),
                                            ),
                                        false,
                                    );
                                    ?>
                                    <a class="btn repost tooltips" href="<?= $linkUrl ?>"
                                        title="<?= Lang::txt('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT') ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
                                    </a>
                            <?php } ?>
                        </div><!-- / .actions -->
                    </div><!-- / .meta -->
                    <div class="convo attribution reposted">
                        <?php
                        $name = $this->escape(stripslashes($row->creator()->get('name')));
                        if (in_array($row->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
                            <a href="<?= Route::url($row->creator()->link()) ?>" title="<?= $name ?>" class="img-link">
                                <img src="<?= $row->creator()->picture() ?>"
                                    alt="<?= Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $name) ?>" />
                            </a>
                        <?php } else { ?>
                            <span class="img-link">
                                <img src="<?= $row->creator()->picture() ?>"
                                    alt="<?= Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $name) ?>" />
                            </span>
                        <?php } ?>
                        <p>
                            <?php
                            $who = $name;
                            if (in_array($row->creator()->get('access'), User::getAuthorisedViewLevels())) {
                                $who = '<a href="' . Route::url($row->creator()->link()) . '">' . $name . '</a>';
                            }

                            $where = '<a href="' . Route::url($row->link()) . '">'
                                . $this->escape(stripslashes($row->get('title'))) . '</a>';

                            echo Lang::txt('PLG_MEMBERS_COLLECTIONS_ONTO', $who, $where);
                            ?>
                            <br />
                            <span class="entry-date">
                                <span class="entry-date-at"><?= Lang::txt('PLG_MEMBERS_COLLECTIONS_AT') ?></span>
                                <span class="time"><time
                                    datetime="<?= $row->created() ?>"><?= $row->created('time') ?></time></span>
                                <span class="entry-date-on"><?= Lang::txt('PLG_MEMBERS_COLLECTIONS_ON') ?></span>
                                <span class="date"><time
                                    datetime="<?= $row->created() ?>"><?= $row->created('date') ?></time></span>
                            </span>
                        </p>
                    </div><!-- / .attribution -->
                    <?php
                    if (
                        !User::isGuest()
                        && $this->params->get('access-create-item')
                        && $this->filters['sort'] == 'ordering'
                    ) { ?>
                        <div class="sort-handle tooltips"
                            title="<?= Lang::txt('PLG_MEMBERS_COLLECTIONS_GRAB_TO_REORDER') ?>"></div>
                    <?php } ?>
                </div><!-- / .content -->
            </div><!-- / .post -->
        <?php } ?>
        </div><!-- / #posts -->

        <?php
        if ($this->total > $this->filters['limit']) {
            $pageNav = $this->pagination(
                $this->total,
                $this->filters['start'],
                $this->filters['limit'],
            );
            $pageNav->setAdditionalUrlParam('id', $this->member->get('id'));
            $pageNav->setAdditionalUrlParam('active', 'collections');
            $pageNav->setAdditionalUrlParam('task', $this->task);
            $pageNav->setAdditionalUrlParam('viewas', $viewas);
            $pageNav->setAdditionalUrlParam('sort', $this->filters['sort']);
            echo $pageNav->render();
        }
        ?>
        <div class="clear"></div>
    <?php } else { ?>
        <div id="collection-introduction">
            <?php if ($this->params->get('access-create-item')) { ?>
                <div class="instructions">
                    <ol>
                        <li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_POST_INSTRUCTIONS_STEP1'); ?></li>
                        <li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_POST_INSTRUCTIONS_STEP2'); ?></li>
                        <li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_POST_INSTRUCTIONS_STEP3'); ?></li>
                        <li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_POST_INSTRUCTIONS_STEP4'); ?></li>
                    </ol>
                    <div class="new-post">
                        <a class="icon-add add"
                            href="<?= Route::url($base . '&task=post/new&board=' . $this->collection->get('alias')) ?>">
                            <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NEW_POST'); ?>
                        </a>
                    </div>
                </div><!-- / .instructions -->
                <div class="questions">
                    <p><strong><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_POST'); ?></strong></p>
                    <p><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_POST_EXPLANATION'); ?><p>
                </div>
            <?php } else { ?>
                <div class="instructions">
                    <p><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_EMPTY_COLLECTION'); ?></p>
                </div><!-- / .instructions -->
            <?php } ?>
        </div><!-- / #collection-introduction -->
    <?php } ?>
</form>
