<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->js('jquery.masonry.js', 'com_collections')
     ->js('jquery.infinitescroll.js', 'com_collections')
     ->js();

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;

if (!$this->collection->get('layout')) {
    $this->collection->set('layout', 'grid');
}
$viewas = Request::getWord('viewas', $this->collection->get('layout'));
if (!in_array($viewas, array('grid', 'list'))) {
    $viewas = 'grid';
}
?>

<ul id="page_options">
    <li>
        <a class="icon-info btn popup"
            href="<?php echo Route::url('index.php?option=com_help&component=collections&page=index'); ?>">
            <span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_GETTING_STARTED'); ?></span>
        </a>
    </li>
</ul>

<form method="get"
    action="<?php echo Route::url($base . '&scope=' . $this->collection->get('alias', 'posts')); ?>"
    id="collections">
    <?php
    $this->view('_submenu', 'collection')
         ->set('option', $this->option)
         ->set('group', $this->group)
         ->set('params', $this->params)
         ->set('name', $this->name)
         ->set('active', ($this->collection->exists() ? '' : 'posts'))
         ->set('collections', $this->collections)
         ->set('posts', $this->posts)
         ->set('followers', $this->followers) //->set('following', $this->following)
         ->display();
    ?>

    <?php if (!User::isGuest() && $this->params->get('access-manage-collection')) { ?>
        <p class="guest-options">
            <a class="icon-config config btn" href="<?php echo Route::url($base . '&scope=settings'); ?>">
                <span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS'); ?></span>
            </a>
        </p>
    <?php } ?>

    <?php if ($this->collection->exists()) { ?>
        <p class="overview">
            <span class="title count">
                "<?php echo $this->escape(stripslashes($this->collection->get('title'))); ?>"
            </span>
            <span class="posts count">
                <?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_STATS_POSTS', '<strong>' . $this->count . '</strong>'); ?>
            </span>
            <?php if (!User::isGuest()) { ?>
                <?php if ($this->collection->isFollowing()) { ?>
                    <?php
                    $txtFollow = Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW');
                    $txtUnfollow = Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW');
                    $txtUnfollowTitle = Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW_TITLE');
                    $unfollowUrl = Route::url(
                        $base . '&scope=' . $this->collection->get('alias') . '/unfollow'
                    );
                    ?>
                    <a class="icon-unfollow unfollow btn tooltips"
                        data-text-follow="<?php echo $txtFollow; ?>"
                        data-text-unfollow="<?php echo $txtUnfollow; ?>"
                        title="<?php echo $txtUnfollowTitle; ?>"
                        href="<?php echo $unfollowUrl; ?>">
                        <span><?php echo $txtUnfollow; ?></span>
                    </a>
                <?php } else { ?>
                    <?php
                    $txtFollow = Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW');
                    $txtUnfollow = Lang::txt('PLG_GROUPS_COLLECTIONS_UNFOLLOW');
                    $txtFollowTitle = Lang::txt('PLG_GROUPS_COLLECTIONS_FOLLOW_TITLE');
                    $followUrl = Route::url(
                        $base . '&scope=' . $this->collection->get('alias') . '/follow'
                    );
                    ?>
                    <a class="icon-follow follow btn tooltips"
                        data-text-follow="<?php echo $txtFollow; ?>"
                        data-text-unfollow="<?php echo $txtUnfollow; ?>"
                        title="<?php echo $txtFollowTitle; ?>"
                        href="<?php echo $followUrl; ?>">
                        <span><?php echo $txtFollow; ?></span>
                    </a>
                <?php } ?>
                <?php
                $collectUrl = Route::url(
                    $base . '&scope=' . $this->collection->get('alias') . '/collect'
                );
                ?>
                <!-- <a class="repost btn tooltips"
                    title="<?php echo Lang::txt('Repost :: Collect this collection'); ?>"
                    href="<?php echo $collectUrl; ?>">
                    <span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_COLLECT'); ?></span>
                </a> -->
            <?php } ?>
            <span class="options sort-options">
                <?php
                $createdSortUrl = Route::url(
                    $this->collection->link() . '&sort=created&viewas=' . $viewas
                );
                $txtCreatedSort = Lang::txt(
                    'PLG_GROUPS_COLLECTIONS_CREATED_SORT'
                );
                $createdClass = 'icon-created';
                if ($this->filters['sort'] == 'created') {
                    $createdClass .= ' selected';
                }
                ?>
                <a href="<?php echo $createdSortUrl; ?>"
                    class="<?php echo $createdClass; ?>"
                    data-view="sort-created"
                    title="<?php echo $txtCreatedSort; ?>"
                    ><?php echo $txtCreatedSort; ?></a>
                <?php
                $orderingSortUrl = Route::url(
                    $this->collection->link() . '&sort=ordering&viewas=' . $viewas
                );
                $txtOrderingSort = Lang::txt(
                    'PLG_GROUPS_COLLECTIONS_ORDERING_SORT'
                );
                $orderingClass = 'icon-ordering';
                if ($this->filters['sort'] == 'ordering') {
                    $orderingClass .= ' selected';
                }
                ?>
                <a href="<?php echo $orderingSortUrl; ?>"
                    class="<?php echo $orderingClass; ?>"
                    data-view="sort-ordering"
                    title="<?php echo $txtOrderingSort; ?>"
                    ><?php echo $txtOrderingSort; ?></a>
            </span>
            <span class="options view-options">
                <?php
                $gridUrl = Route::url(
                    $this->collection->link()
                    . '&sort=' . $this->filters['sort'] . '&viewas=grid'
                );
                $txtGridView = Lang::txt(
                    'PLG_GROUPS_COLLECTIONS_GRID_VIEW'
                );
                $gridClass = 'icon-grid';
                if ($viewas == 'grid') {
                    $gridClass .= ' selected';
                }
                ?>
                <a href="<?php echo $gridUrl; ?>"
                    class="<?php echo $gridClass; ?>"
                    data-view="view-grid"
                    title="<?php echo $txtGridView; ?>"
                    ><?php echo $txtGridView; ?></a>
                <?php
                $listUrl = Route::url(
                    $this->collection->link()
                    . '&sort=' . $this->filters['sort'] . '&viewas=list'
                );
                $txtListView = Lang::txt(
                    'PLG_GROUPS_COLLECTIONS_LIST_VIEW'
                );
                $listClass = 'icon-list';
                if ($viewas == 'list') {
                    $listClass .= ' selected';
                }
                ?>
                <a href="<?php echo $listUrl; ?>"
                    class="<?php echo $listClass; ?>"
                    data-view="view-list"
                    title="<?php echo $txtListView; ?>"
                    ><?php echo $txtListView; ?></a>
            </span>
        </p>
    <?php } ?>

    <?php if ($this->rows->total() > 0) { ?>
        <?php
        $baseTrimmed = rtrim(Request::base(true), '/');
        $reorderUrl = Route::url(
            'index.php?option=com_collections&controller=posts'
            . '&task=reorder&' . Session::getFormToken() . '=1'
        );
        $viewClass = 'view-' . $viewas . ' '
            . (User::isGuest() ? 'loggedout' : 'loggedin');
        ?>
        <div id="posts"
            data-base="<?php echo $baseTrimmed; ?>"
            data-update="<?php echo $reorderUrl; ?>"
            class="<?php echo $viewClass; ?>">
            <?php if ($this->params->get('access-create-item') && !Request::getInt('no_html', 0)) { ?>
                <div class="post new-post" id="post_0">
                    <?php
                    $newPostUrl = Route::url(
                        $base . '&scope=post/new&board='
                        . $this->collection->get('alias')
                    );
                    ?>
                    <a class="icon-add add"
                        href="<?php echo $newPostUrl; ?>">
                        <?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_NEW_POST'); ?>
                    </a>
                </div>
            <?php } ?>
            <?php
            foreach ($this->rows as $row) {
                $item = $row->item();
                ?>
                <div class="post <?php echo $item->type(); ?>"
                    id="post_<?php echo $row->get('id'); ?>"
                    data-id="<?php echo $row->get('id'); ?>"
                    data-closeup-url="<?php echo Route::url($base . '&scope=post/' . $row->get('id')); ?>">
                    <div class="content">
                        <?php
                            $this->view('default_' . $item->type(), 'post')
                                 ->set('name', $this->name)
                                 ->set('option', $this->option)
                                 ->set('group', $this->group)
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
                            'index.php?option=com_collections'
                            . '&controller=posts&task=metadata&post='
                            . $row->get('id')
                        );
                        ?>
                        <div class="meta"
                            data-metadata-url="<?php echo $metadataUrl; ?>">
                            <p class="stats">
                                <span class="likes">
                                    <?php echo Lang::txt(
                                        'PLG_GROUPS_COLLECTIONS_POST_LIKES',
                                        $item->get('positive', 0)
                                    ); ?>
                                </span>
                                <span class="comments">
                                    <?php echo Lang::txt(
                                        'PLG_GROUPS_COLLECTIONS_POST_COMMENTS',
                                        $item->get('comments', 0)
                                    ); ?>
                                </span>
                                <span class="reposts">
                                    <?php echo Lang::txt(
                                        'PLG_GROUPS_COLLECTIONS_POST_REPOSTS',
                                        $item->get('reposts', 0)
                                    ); ?>
                                </span>
                            </p>
                            <div class="actions">
                                <?php if (!User::isGuest()) { ?>
                                    <?php if ($this->group->published == 1) { ?>
                                        <?php if ($item->get('created_by') != User::get('id')) { ?>
                                            <?php
                                            $voteClass = 'btn vote '
                                                . ($item->get('voted') ? 'unlike' : 'like');
                                            $txtLike = Lang::txt('PLG_GROUPS_COLLECTIONS_LIKE');
                                            $txtUnlike = Lang::txt('PLG_GROUPS_COLLECTIONS_UNLIKE');
                                            $voteUrl = Route::url(
                                                $base . '&scope=post/'
                                                . $row->get('id') . '/vote'
                                            );
                                            $voteLabel = $item->get('voted')
                                                ? $txtUnlike
                                                : $txtLike;
                                            ?>
                                            <a class="<?php echo $voteClass; ?>"
                                                data-id="<?php echo $row->get('id'); ?>"
                                                data-text-like="<?php echo $txtLike; ?>"
                                                data-text-unlike="<?php echo $txtUnlike; ?>"
                                                href="<?php echo $voteUrl; ?>">
                                                <span><?php echo $voteLabel; ?></span>
                                            </a>
                                        <?php } ?>
                                        <?php
                                        $commentUrl = Route::url(
                                            'index.php?option=com_collections'
                                            . '&controller=posts&post='
                                            . $row->get('id') . '&task=comment'
                                        );
                                        ?>
                                        <a class="btn comment"
                                            data-id="<?php echo $row->get('id'); ?>"
                                            href="<?php echo $commentUrl; ?>">
                                            <span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_COMMENT'); ?></span>
                                        </a>
                                    <?php } ?>
                                    <?php
                                    $repostUrl = Route::url(
                                        $base . '&scope=post/'
                                        . $row->get('id') . '/collect'
                                    );
                                    ?>
                                    <a class="btn repost"
                                        data-id="<?php echo $row->get('id'); ?>"
                                        href="<?php echo $repostUrl; ?>">
                                        <span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_COLLECT'); ?></span>
                                    </a>
                                    <?php if ($this->group->published == 1) { ?>
                                        <?php
                                        $isCreator = $item->get('created_by') == User::get('id');
                                        $canManage = $this->params->get('access-manage-collection');
                                        ?>
                                        <?php if ($isCreator || $canManage) { ?>
                                            <?php
                                            $editUrl = Route::url(
                                                $base . '&scope=post/'
                                                . $row->get('id') . '/edit'
                                            );
                                            $txtEdit = Lang::txt(
                                                'PLG_GROUPS_COLLECTIONS_EDIT'
                                            );
                                            ?>
                                            <a class="btn edit"
                                                data-id="<?php echo $row->get('id'); ?>"
                                                href="<?php echo $editUrl; ?>"
                                                title="<?php echo $txtEdit; ?>">
                                                <span><?php echo $txtEdit; ?></span>
                                            </a>
                                        <?php } ?>
                                        <?php if ($row->get('original') && ($isCreator || $canManage)) { ?>
                                            <?php
                                            $deleteUrl = Route::url(
                                                $base . '&scope=post/'
                                                . $row->get('id') . '/delete'
                                            );
                                            $txtDelete = Lang::txt(
                                                'PLG_GROUPS_COLLECTIONS_DELETE'
                                            );
                                            ?>
                                            <a class="btn delete"
                                                data-id="<?php echo $row->get('id'); ?>"
                                                href="<?php echo $deleteUrl; ?>"
                                                title="<?php echo $txtDelete; ?>">
                                                <span><?php echo $txtDelete; ?></span>
                                            </a>
                                        <?php } elseif ($row->get('created_by') == User::get('id') || $canManage) { ?>
                                            <?php
                                            $removeUrl = Route::url(
                                                $base . '&scope=post/'
                                                . $row->get('id') . '/remove'
                                            );
                                            ?>
                                            <a class="btn unpost"
                                                data-id="<?php echo $row->get('id'); ?>"
                                                href="<?php echo $removeUrl; ?>">
                                                <span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_REMOVE'); ?></span>
                                            </a>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } else { ?>
                                    <?php
                                    $loginReturn = base64_encode(
                                        Route::url(
                                            $base . '&scope='
                                            . $this->collection->get('alias'),
                                            false,
                                            true
                                        )
                                    );
                                    $loginUrl = Route::url(
                                        'index.php?option=com_users&view=login&return='
                                        . $loginReturn,
                                        false
                                    );
                                    $txtLoginLike = Lang::txt(
                                        'PLG_GROUPS_COLLECTIONS_WARNING_LOGIN_TO_LIKE'
                                    );
                                    $txtLoginCollect = Lang::txt(
                                        'PLG_GROUPS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'
                                    );
                                    $guestCommentUrl = Route::url(
                                        'index.php?option=com_collections'
                                        . '&controller=posts&post='
                                        . $row->get('id') . '&task=comment'
                                    );
                                    ?>
                                    <a class="btn vote like tooltips"
                                        href="<?php echo $loginUrl; ?>"
                                        title="<?php echo $txtLoginLike; ?>">
                                        <span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_LIKE'); ?></span>
                                    </a>
                                    <a class="btn comment"
                                        data-id="<?php echo $row->get('id'); ?>"
                                        href="<?php echo $guestCommentUrl; ?>">
                                        <span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_COMMENT'); ?></span>
                                    </a>
                                    <a class="btn repost tooltips"
                                        href="<?php echo $loginUrl; ?>"
                                        title="<?php echo $txtLoginCollect; ?>">
                                        <span><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_COLLECT'); ?></span>
                                    </a>
                                <?php } ?>
                            </div><!-- / .actions -->
                        </div><!-- / .meta -->
                        <div class="convo attribution reposted">
                            <?php
                            $name = $this->escape(stripslashes($row->creator()->get('name')));
                            if (in_array($row->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
                                <a href="<?php echo Route::url($row->creator()->link()); ?>"
                                    title="<?php echo $name; ?>"
                                    class="img-link">
                                    <img src="<?php echo $row->creator()->picture(); ?>"
                                        alt="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_PROFILE_PICTURE', $name); ?>"
                                        />
                                </a>
                            <?php } else { ?>
                                <span class="img-link">
                                    <img src="<?php echo $row->creator()->picture(); ?>"
                                        alt="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_PROFILE_PICTURE', $name); ?>"
                                        />
                                </span>
                            <?php } ?>
                            <p>
                                <?php
                                $who = $name;
                                if (in_array($row->creator()->get('access'), User::getAuthorisedViewLevels())) {
                                    $who = '<a href="' . Route::url($row->creator()->link()) . '">' . $name . '</a>';
                                }

                                $where = '<a href="'
                                    . Route::url($row->link())
                                    . '">'
                                    . $this->escape(stripslashes($row->get('title')))
                                    . '</a>';

                                echo Lang::txt('PLG_GROUPS_COLLECTIONS_ONTO', $who, $where);
                                ?>
                                <br />
                                <span class="entry-date">
                                    <span class="entry-date-at">
                                        <?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_DATE_AT'); ?>
                                    </span>
                                    <span class="time">
                                        <time datetime="<?php echo $row->created(); ?>">
                                            <?php echo $row->created('time'); ?>
                                        </time>
                                    </span>
                                    <span class="entry-date-on">
                                        <?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_DATE_ON'); ?>
                                    </span>
                                    <span class="date">
                                        <time datetime="<?php echo $row->created(); ?>">
                                            <?php echo $row->created('date'); ?>
                                        </time>
                                    </span>
                                </span>
                            </p>
                        </div><!-- / .attribution -->
                        <?php
                        $canCreate = $this->params->get('access-create-item');
                        $isSortOrdering = $this->filters['sort'] == 'ordering';
                        ?>
                        <?php if (!User::isGuest() && $canCreate && $isSortOrdering) { ?>
                            <div class="sort-handle tooltips"
                                title="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_GRAB_TO_REORDER'); ?>"></div>
                        <?php } ?>
                    </div><!-- / .content -->
                </div><!-- / .post -->
            <?php } ?>
        </div><!-- / #posts -->
        <?php
        if ($this->posts > $this->filters['limit']) {
            $pageNav = $this->pagination(
                $this->count,
                $this->filters['start'],
                $this->filters['limit']
            );
            $pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
            $pageNav->setAdditionalUrlParam('active', 'collections');
            $pageNav->setAdditionalUrlParam('scope', $this->scope);
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
                        <li><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_STEP1'); ?></li>
                        <li><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_STEP2'); ?></li>
                        <li><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_STEP3'); ?></li>
                        <li><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_STEP4'); ?></li>
                    </ol>
                    <div class="new-post">
                        <?php
                        $introNewPostUrl = Route::url(
                            $base . '&scope=post/new&board='
                            . $this->collection->get('alias')
                        );
                        ?>
                        <a class="icon-add add"
                            href="<?php echo $introNewPostUrl; ?>">
                            <?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_NEW_POST'); ?>
                        </a>
                    </div>
                </div><!-- / .instructions -->
                <div class="questions">
                    <p><strong><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_TITLE'); ?></strong></p>
                    <p><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_INSTRUCT_POST_DESC'); ?><p>
                </div>
            <?php } else { ?>
                <div class="instructions">
                    <p><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_NO_POSTS_FOUND'); ?></p>
                </div><!-- / .instructions -->
            <?php } ?>
        </div><!-- / #collection-introduction -->
    <?php } ?>
</form>
