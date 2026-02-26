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
if (!in_array($viewas, array('grid', 'list'))) {
    $viewas = 'grid';
}

$this->css()
     ->js('jquery.masonry', 'com_collections')
     ->js('jquery.infinitescroll', 'com_collections')
     ->js();

// Get the comments config value
$allow_comments = Component::params('com_collections')->get('allow_comments');
?>

<ul id="page_options">
    <li>
        <a class="icon-info btn popup"
            href="<?php echo Route::url('index.php?option=com_help&component=collections&page=index'); ?>">
            <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_GETTING_STARTED'); ?></span>
        </a>
    </li>
</ul>

<form method="get"
    action="<?php echo Route::url($base . '&task=' . $this->collection->get('alias')); ?>"
    id="collections">
    <?php

    $this->view('_submenu', 'collection')
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
                        <a class="icon-unfollow unfollow btn tooltips"
                            data-text-follow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_THIS'); ?>"
                            data-text-unfollow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_THIS'); ?>"
                            title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_TITLE'); ?>"
                            href="<?php echo Route::url($this->collection->link() . '/unfollow'); ?>">
                            <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_THIS'); ?></span>
                        </a>
                    <?php } else { ?>
                        <a class="icon-follow follow btn tooltips"
                            data-text-follow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_THIS'); ?>"
                            data-text-unfollow="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_UNFOLLOW_THIS'); ?>"
                            title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_TITLE'); ?>"
                            href="<?php echo Route::url($this->collection->link() . '/follow'); ?>">
                            <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FOLLOW_THIS'); ?></span>
                        </a>
                    <?php } ?>
                    <a class="icon-repost repost btn tooltips"
                        title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT_TITLE'); ?>"
                        href="<?php echo Route::url($this->collection->link() . '/collect'); ?>">
                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
                    </a>
                <?php } ?>
            <?php } ?>
            <span class="options sort-options">
                <?php
                $createdSortUrl = Route::url(
                    $this->collection->link() . '&sort=created&viewas=' . $viewas
                );
                $createdSortTxt = Lang::txt('PLG_MEMBERS_COLLECTIONS_CREATED_SORT');
                $createdCls = 'icon-created';
                if ($this->filters['sort'] == 'created') {
                    $createdCls .= ' selected';
                }
                ?>
                <a href="<?php echo $createdSortUrl; ?>"
                    class="<?php echo $createdCls; ?>"
                    data-view="sort-created"
                    title="<?php echo $createdSortTxt; ?>"><?php echo $createdSortTxt; ?></a>
                <?php
                $orderingSortUrl = Route::url(
                    $this->collection->link() . '&sort=ordering&viewas=' . $viewas
                );
                $orderingSortTxt = Lang::txt('PLG_MEMBERS_COLLECTIONS_ORDERING_SORT');
                $orderingCls = 'icon-ordering';
                if ($this->filters['sort'] == 'ordering') {
                    $orderingCls .= ' selected';
                }
                ?>
                <a href="<?php echo $orderingSortUrl; ?>"
                    class="<?php echo $orderingCls; ?>"
                    data-view="sort-ordering"
                    title="<?php echo $orderingSortTxt; ?>"><?php echo $orderingSortTxt; ?></a>
            </span>
            <span class="options view-options">
                <?php
                $gridViewUrl = Route::url(
                    $this->collection->link()
                    . '&sort=' . $this->filters['sort']
                    . '&viewas=grid'
                );
                $gridViewTxt = Lang::txt('PLG_MEMBERS_COLLECTIONS_GRID_VIEW');
                $gridCls = 'icon-grid';
                if ($viewas == 'grid') {
                    $gridCls .= ' selected';
                }
                ?>
                <a href="<?php echo $gridViewUrl; ?>"
                    class="<?php echo $gridCls; ?>"
                    data-view="view-grid"
                    title="<?php echo $gridViewTxt; ?>"><?php echo $gridViewTxt; ?></a>
                <?php
                $listViewUrl = Route::url(
                    $this->collection->link()
                    . '&sort=' . $this->filters['sort']
                    . '&viewas=list'
                );
                $listViewTxt = Lang::txt('PLG_MEMBERS_COLLECTIONS_LIST_VIEW');
                $listCls = 'icon-list';
                if ($viewas == 'list') {
                    $listCls .= ' selected';
                }
                ?>
                <a href="<?php echo $listViewUrl; ?>"
                    class="<?php echo $listCls; ?>"
                    data-view="view-list"
                    title="<?php echo $listViewTxt; ?>"><?php echo $listViewTxt; ?></a>
            </span>
        </p>
    <?php } ?>

    <?php if ($this->rows->total() > 0) { ?>
        <?php
        $baseUrl = rtrim(Request::base(true), '/');
        $updateUrl = Route::url(
            'index.php?option=com_collections&controller=posts&task=reorder&'
            . Session::getFormToken() . '=1'
        );
        $viewCls = $viewas . ' '
            . (User::isGuest() ? 'loggedout' : 'loggedin');
        ?>
        <div id="posts"
            data-base="<?php echo $baseUrl; ?>"
            data-update="<?php echo $updateUrl; ?>"
            class="view-<?php echo $viewCls; ?>">
            <?php if ($this->params->get('access-create-collection') && !Request::getInt('no_html', 0)) { ?>
                <div class="post new-post" id="post_0">
                    <?php
                    $newPostUrl = Route::url(
                        $base . '&task=post/new&board='
                        . $this->collection->get('alias')
                    );
                    ?>
                    <a class="icon-add add"
                        href="<?php echo $newPostUrl; ?>">
                        <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_NEW_POST'); ?>
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
                data-closeup-url="<?php echo Route::url($base . '&task=post/' . $row->get('id')); ?>">
                <div class="content">
                    <?php

                        $this->view('default_' . $item->type(), 'post')
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
                        'index.php?option=com_collections&controller=posts'
                        . '&task=metadata&post=' . $row->get('id')
                    );
                    ?>
                    <div class="meta"
                        data-metadata-url="<?php echo $metadataUrl; ?>">
                        <p class="stats">
                            <span class="likes">
                                <?php
                                $likesCount = $item->get('positive', 0);
                                echo Lang::txt(
                                    'PLG_MEMBERS_COLLECTIONS_NUM_LIKES',
                                    $likesCount
                                );
                                ?>
                            </span>
                            <?php

                            // Display comments count only if enabled
                            if ($allow_comments) :
                                ?>
                            <span class="comments">
                                <?php
                                $commentsCount = $item->get('comments', 0);
                                echo Lang::txt(
                                    'PLG_MEMBERS_COLLECTIONS_NUM_COMMENTS',
                                    $commentsCount
                                );
                                ?>
                            </span>
                            <?php endif; ?>
                            <span class="reposts">
                                <?php
                                $repostsCount = $item->get('reposts', 0);
                                echo Lang::txt(
                                    'PLG_MEMBERS_COLLECTIONS_NUM_REPOSTS',
                                    $repostsCount
                                );
                                ?>
                            </span>
                        </p>
                        <div class="actions">
                            <?php if (!User::isGuest()) { ?>
                                <?php //if ($item->get('created_by') == User::get('id')) { ?>
                                <?php if ($row->get('created_by') == User::get('id')) { ?>
                                    <?php
                                    $editUrl = Route::url(
                                        $base . '&task=post/' . $row->get('id') . '/edit'
                                    );
                                    ?>
                                    <a class="btn edit"
                                        data-id="<?php echo $row->get('id'); ?>"
                                        href="<?php echo $editUrl; ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_EDIT'); ?></span>
                                    </a>
                                <?php } else { ?>
                                    <?php
                                    $voteCls = ($item->get('voted')) ? 'unlike' : 'like';
                                    $likeTxt = Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE');
                                    $unlikeTxt = Lang::txt('Unlike');
                                    $voteUrl = Route::url(
                                        $base . '&task=post/' . $row->get('id') . '/vote'
                                    );
                                    $voteLabel = ($item->get('voted'))
                                        ? Lang::txt('PLG_MEMBERS_COLLECTIONS_UNLIKE')
                                        : Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE');
                                    ?>
                                    <a class="btn vote <?php echo $voteCls; ?>"
                                        data-id="<?php echo $row->get('id'); ?>"
                                        data-text-like="<?php echo $likeTxt; ?>"
                                        data-text-unlike="<?php echo $unlikeTxt; ?>"
                                        href="<?php echo $voteUrl; ?>">
                                        <span><?php echo $voteLabel; ?></span>
                                    </a>
                                <?php } ?>
                                <?php

                                // Let comment only if enabled
                                if ($allow_comments) :
                                    ?>
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
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COMMENT'); ?></span>
                                    </a>
                                <?php endif; ?>
                                    <?php
                                    $collectUrl = Route::url(
                                        $base . '&task=post/'
                                        . $row->get('id') . '/collect'
                                    );
                                    ?>
                                    <a class="btn repost"
                                        data-id="<?php echo $row->get('id'); ?>"
                                        href="<?php echo $collectUrl; ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
                                    </a>
                                <?php
                                $isOriginalOwner = $row->get('original')
                                    && ($item->get('created_by') == User::get('id')
                                    || $this->params->get('access-delete-item'));
                                $isEditor = $row->get('created_by') == User::get('id')
                                    || $this->params->get('access-edit-item');
                                ?>
                                <?php if ($isOriginalOwner) { ?>
                                    <?php
                                    $deleteUrl = Route::url(
                                        $base . '&task=post/'
                                        . $row->get('id') . '/delete'
                                    );
                                    ?>
                                    <a class="btn delete"
                                        data-id="<?php echo $row->get('id'); ?>"
                                        href="<?php echo $deleteUrl; ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE'); ?></span>
                                    </a>
                                <?php } elseif ($isEditor) { ?>
                                    <?php
                                    $removeUrl = Route::url(
                                        $base . '&task=post/'
                                        . $row->get('id') . '/remove'
                                    );
                                    ?>
                                    <a class="btn unpost"
                                        data-id="<?php echo $row->get('id'); ?>"
                                        href="<?php echo $removeUrl; ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_REMOVE'); ?></span>
                                    </a>
                                <?php } ?>
                            <?php } else { ?>
                                    <?php
                                    $collAlias = $this->collection->get('alias');
                                    $returnUrl = base64_encode(
                                        Route::url(
                                            $base . '&task=' . $collAlias,
                                            false,
                                            true
                                        )
                                    );
                                    $loginUrl = Route::url(
                                        'index.php?option=com_users&view=login&return='
                                        . $returnUrl,
                                        false
                                    );
                                    $likeTitleTxt = Lang::txt(
                                        'PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_LIKE'
                                    );
                                    ?>
                                    <a class="btn vote like tooltips"
                                        href="<?php echo $loginUrl; ?>"
                                        title="<?php echo $likeTitleTxt; ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE'); ?></span>
                                    </a>
                                <?php

                                    // Let comment only if enabled
                                if ($allow_comments) :
                                    ?>
                                    <?php
                                    $guestCommentUrl = Route::url(
                                        'index.php?option=com_collections'
                                        . '&controller=posts&post='
                                        . $row->get('id') . '&task=comment'
                                    );
                                    ?>
                                    <a class="btn comment"
                                        data-id="<?php echo $row->get('id'); ?>"
                                        href="<?php echo $guestCommentUrl; ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COMMENT'); ?></span>
                                    </a>
                                <?php endif; ?>
                                    <?php
                                    $collectTitleTxt = Lang::txt(
                                        'PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'
                                    );
                                    ?>
                                    <a class="btn repost tooltips"
                                        href="<?php echo $loginUrl; ?>"
                                        title="<?php echo $collectTitleTxt; ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
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
                                    alt="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $name); ?>"/>
                            </a>
                        <?php } else { ?>
                            <span class="img-link">
                                <img src="<?php echo $row->creator()->picture(); ?>"
                                    alt="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $name); ?>"/>
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

                            echo Lang::txt('PLG_MEMBERS_COLLECTIONS_ONTO', $who, $where);
                            ?>
                            <br />
                            <span class="entry-date">
                                <span class="entry-date-at">
                                    <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_AT'); ?>
                                </span>
                                <span class="time">
                                    <time datetime="<?php echo $row->created(); ?>">
                                        <?php echo $row->created('time'); ?>
                                    </time>
                                </span>
                                <span class="entry-date-on">
                                    <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_ON'); ?>
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
                    $canReorder = !User::isGuest()
                        && $this->params->get('access-create-item')
                        && $this->filters['sort'] == 'ordering';
                    ?>
                    <?php if ($canReorder) { ?>
                        <div class="sort-handle tooltips"
                            title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_GRAB_TO_REORDER'); ?>"></div>
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
                $this->filters['limit']
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
                        <?php
                        $newPostUrl2 = Route::url(
                            $base . '&task=post/new&board='
                            . $this->collection->get('alias')
                        );
                        ?>
                        <a class="icon-add add"
                            href="<?php echo $newPostUrl2; ?>">
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
