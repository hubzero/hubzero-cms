<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

$base = $this->member->link() . '&active=' . $this->name;

$this->css()
    ->js('jquery.masonry', 'com_collections')
    ->js('jquery.infinitescroll', 'com_collections')
    ->js();
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
        ->set('active', 'livefeed')
        ->set('collections', $this->collections)
        ->set('posts', $this->posts)
        ->set('followers', $this->followers)
        ->set('following', $this->following)
        ->display();
    ?>

<?php if ($this->rows->total() > 0) { ?>
    <div id="posts" data-base="<?php echo rtrim(Request::base(true), '/'); ?>">
        <?php

        foreach ($this->rows as $row) {
            $item = $row->item();
            ?>
            <div class="post <?php echo $item->type(); ?>"
                id="b<?php echo $row->get('id'); ?>"
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
                            ->set('board', $this->collection)
                            ->display();
                    ?>
                    <?php if (count($item->tags()) > 0) { ?>
                        <div class="tags-wrap">
                            <?php echo $item->tags('render'); ?>
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
                            <?php
                            $likesText = Lang::txt(
                                'PLG_MEMBERS_COLLECTIONS_NUM_LIKES',
                                $item->get('positive', 0)
                            );
                            $commentsText = Lang::txt(
                                'PLG_MEMBERS_COLLECTIONS_NUM_COMMENTS',
                                $item->get('comments', 0)
                            );
                            $repostsText = Lang::txt(
                                'PLG_MEMBERS_COLLECTIONS_NUM_REPOSTS',
                                $item->get('reposts', 0)
                            );
                            ?>
                            <span class="likes">
                                <?php echo $likesText; ?>
                            </span>
                            <span class="comments">
                                <?php echo $commentsText; ?>
                            </span>
                            <span class="reposts">
                                <?php echo $repostsText; ?>
                            </span>
                        </p>
                        <div class="actions">
                            <?php if (!User::isGuest()) { ?>
                                <?php if ($item->get('created_by') == User::get('id')) { ?>
                                    <?php $editUrl = Route::url($base . '&task=post/' . $row->get('id') . '/edit'); ?>
                                    <a class="btn edit"
                                        data-id="<?php echo $row->get('id'); ?>"
                                        href="<?php echo $editUrl; ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_EDIT'); ?></span>
                                    </a>
                                <?php } else { ?>
                                    <?php
                                    $voteClass = ($item->get('voted')) ? 'unlike' : 'like';
                                    $voteTxt = ($item->get('voted'))
                                        ? Lang::txt('PLG_MEMBERS_COLLECTIONS_UNLIKE')
                                        : Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE');
                                    $likeTxt = Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE');
                                    $unlikeTxt = Lang::txt('PLG_MEMBERS_COLLECTIONS_UNLIKE');
                                    $voteUrl = Route::url(
                                        $base . '&task=post/' . $row->get('id') . '/vote'
                                    );
                                    ?>
                                    <a class="btn vote <?php echo $voteClass; ?>"
                                        data-id="<?php echo $row->get('id'); ?>"
                                        data-text-like="<?php echo $likeTxt; ?>"
                                        data-text-unlike="<?php echo $unlikeTxt; ?>"
                                        href="<?php echo $voteUrl; ?>">
                                        <span><?php echo $voteTxt; ?></span>
                                    </a>
                                <?php } ?>
                                    <?php
                                    $commentUrl = Route::url(
                                        'index.php?option=com_collections&controller=posts&post='
                                        . $row->get('id') . '&task=comment'
                                    );
                                    $repostUrl = Route::url(
                                        $base . '&task=post/' . $row->get('id') . '/collect'
                                    );
                                    ?>
                                    <a class="btn comment"
                                        data-id="<?php echo $row->get('id'); ?>"
                                        href="<?php echo $commentUrl; ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COMMENT'); ?></span>
                                    </a>
                                    <a class="btn repost"
                                        data-id="<?php echo $row->get('id'); ?>"
                                        href="<?php echo $repostUrl; ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COLLECT'); ?></span>
                                    </a>
                                <?php if ($row->get('original') && $item->get('created_by') == User::get('id')) { ?>
                                    <?php
                                    $deleteUrl = Route::url(
                                        $base . '&task=post/' . $row->get('id') . '/delete'
                                    );
                                    ?>
                                    <a class="btn delete"
                                        data-id="<?php echo $row->get('id'); ?>"
                                        href="<?php echo $deleteUrl; ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE'); ?></span>
                                    </a>
                                <?php } ?>
                            <?php } else { ?>
                                    <?php
                                    $likeReturn = base64_encode(
                                        Route::url($base . '&task=post/' . $row->get('id') . '/vote', false, true)
                                    );
                                    $likeLoginUrl = Route::url(
                                        'index.php?option=com_users&view=login&return=' . $likeReturn,
                                        false
                                    );
                                    $likeTitle = Lang::txt('PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_LIKE');
                                    ?>
                                    <a class="btn vote like tooltips"
                                        href="<?php echo $likeLoginUrl; ?>"
                                        title="<?php echo $likeTitle; ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_LIKE'); ?></span>
                                    </a>
                                    <?php
                                    $guestCommentUrl = Route::url(
                                        'index.php?option=com_collections&controller=posts&post='
                                        . $row->get('id') . '&task=comment'
                                    );
                                    $collectReturn = base64_encode(
                                        Route::url(
                                            $base . '&task=post/' . $row->get('id') . '/collect',
                                            false,
                                            true
                                        )
                                    );
                                    $collectLoginUrl = Route::url(
                                        'index.php?option=com_users&view=login&return=' . $collectReturn,
                                        false
                                    );
                                    $collectTitle = Lang::txt(
                                        'PLG_MEMBERS_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'
                                    );
                                    ?>
                                    <a class="btn comment"
                                        data-id="<?php echo $row->get('id'); ?>"
                                        href="<?php echo $guestCommentUrl; ?>">
                                        <span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_COMMENT'); ?></span>
                                    </a>
                                    <a class="btn repost tooltips"
                                        href="<?php echo $collectLoginUrl; ?>"
                                        title="<?php echo $collectTitle; ?>">
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
                            <?php
                            $atTxt = Lang::txt('PLG_MEMBERS_COLLECTIONS_AT');
                            $onTxt = Lang::txt('PLG_MEMBERS_COLLECTIONS_ON');
                            $createdDt = $row->created();
                            $createdTime = $row->created('time');
                            $createdDate = $row->created('date');
                            ?>
                            <span class="entry-date">
                                <span class="entry-date-at">
                                    <?php echo $atTxt; ?>
                                </span>
                                <span class="time">
                                    <time datetime="<?php echo $createdDt; ?>">
                                        <?php echo $createdTime; ?>
                                    </time>
                                </span>
                                <span class="entry-date-on">
                                    <?php echo $onTxt; ?>
                                </span>
                                <span class="date">
                                    <time datetime="<?php echo $createdDt; ?>">
                                        <?php echo $createdDate; ?>
                                    </time>
                                </span>
                            </span>
                        </p>
                    </div><!-- / .attribution -->
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
        echo $pageNav->render();
    }
    ?>
    <div class="clear"></div>
<?php } else { ?>
    <div id="collection-introduction">
        <?php if ($this->params->get('access-create-item')) { ?>
            <?php if ($this->following <= 0) { ?>
                <div class="instructions">
                    <ol>
                        <?php
                        $collectionsUrl = Route::url('index.php?option=com_collections');
                        $step1 = Lang::txt(
                            'PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP1',
                            $collectionsUrl
                        );
                        ?>
                        <li><?php echo $step1; ?></li>
                        <li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP2'); ?></li>
                        <li><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_INSTRUCTIONS_STEP3'); ?></li>
                    </ol>
                </div><!-- / .instructions -->
                <div class="questions">
                    <p><strong><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_FEED'); ?></strong></p>
                    <p><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_FEED_EXPLANATION'); ?><p>
                    <p><strong><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_FOLLOWING'); ?></strong></p>
                    <p><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHAT_IS_FOLLOWING_EXPLANATION'); ?></p>
                    <p><strong><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHERE_ARE_MY_POSTS'); ?></strong></p>
                    <p><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_WHERE_ARE_MY_POSTS_EXPLANATION'); ?></p>
                </div>
            <?php } else { ?>
                <div class="instructions">
                    <p>
                        <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_NO_POSTS_AVAILABLE_FOR_YOU'); ?>
                    </p>
                </div><!-- / .instructions -->
            <?php } ?>
        <?php } else { ?>
            <div class="instructions">
                <?php if ($this->filters['collection_id'][0] == -1) { ?>
                    <p>
                        <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_MEMBER_NOT_FOLLOWING'); ?>
                    </p>
                <?php } else { ?>
                    <p>
                        <?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_NO_POSTS_AVAILABLE_FOR_THIS_MEMBER'); ?>
                    </p>
                <?php } ?>
            </div><!-- / .instructions -->
        <?php } ?>
    </div><!-- / #collection-introduction -->
<?php } ?>
</form>
