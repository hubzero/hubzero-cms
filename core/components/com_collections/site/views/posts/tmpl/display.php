<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$item = $this->post->item();

$base = 'index.php?option=' . $this->option
    . '&controller=' . $this->controller;
$no_html = Request::getInt('no_html', 0);

if (!$no_html) {
    $this->css()
         ->js('post.js');

    $helpUrl = Route::url(
        'index.php?option=com_help&component='
        . substr($this->option, 4) . '&page=index'
    );
    ?>
<header id="content-header">
    <h2><?php echo Lang::txt('COM_COLLECTIONS'); ?></h2>

    <div id="content-header-extra">
        <p>
            <a class="icon-info btn popup"
                href="<?php echo $helpUrl; ?>">
                <span><?php echo Lang::txt('COM_COLLECTIONS_GETTING_STARTED'); ?></span>
            </a>
        </p>
    </div>
</header>
<?php } ?>

<section class="section">
    <div class="grid">
        <div class="col span8">

            <?php
            $commentUrl = Route::url(
                $base . '&post=' . $this->post->get('id') . '&task=comment'
            );
            ?>
            <div class="post full <?php echo $item->type(); ?>"
                id="p<?php echo $this->post->get('id'); ?>"
                data-id="<?php echo $this->post->get('id'); ?>"
                data-closeup-url="<?php echo $commentUrl; ?>"
                data-width="600"
                data-height="350">
                <div class="content">
                    <div class="creator attribution cf">
                        <?php if ($item->get('type') == 'file' || $item->get('type') == 'collection') { ?>
                            <?php
                            $name = $this->escape(
                                stripslashes($item->creator()->get('name'))
                            );
                            $creatorAccess = in_array(
                                $item->creator()->get('access'),
                                User::getAuthorisedViewLevels()
                            );
                            $creatorLink = Route::url(
                                $item->creator()->link()
                            );
                            $profilePic = $item->creator()->picture();
                            $profileAlt = Lang::txt(
                                'COM_COLLECTIONS_PROFILE_PICTURE',
                                $name
                            );

                            if ($creatorAccess) { ?>
                                <a href="<?php echo $creatorLink; ?>"
                                    title="<?php echo $name; ?>"
                                    class="img-link">
                                    <img src="<?php echo $profilePic; ?>"
                                        alt="<?php echo $profileAlt; ?>" />
                                </a>
                            <?php } else { ?>
                                <span class="img-link">
                                    <img src="<?php echo $profilePic; ?>"
                                        alt="<?php echo $profileAlt; ?>" />
                                </span>
                            <?php } ?>
                            <p>
                                <?php
                                $creatorName = $this->escape(
                                    stripslashes($item->creator()->get('name'))
                                );
                                $who = $creatorAccess
                                    ? '<a href="' . $creatorLink . '">'
                                        . $creatorName . '</a>'
                                    : $creatorName;
                                echo Lang::txt(
                                    'COM_COLLECTIONS_USER_CREATED_POST',
                                    $who
                                );
                                ?>
                                <br />
                                <span class="entry-date">
                                    <span class="entry-date-at">
                                        <?php echo Lang::txt('COM_COLLECTIONS_AT'); ?>
                                    </span>
                                    <span class="time">
                                        <time datetime="<?php echo $item->created(); ?>">
                                            <?php echo $item->created('time'); ?>
                                        </time>
                                    </span>
                                    <span class="entry-date-on">
                                        <?php echo Lang::txt('COM_COLLECTIONS_ON'); ?>
                                    </span>
                                    <span class="date">
                                        <time datetime="<?php echo $item->created(); ?>">
                                            <?php echo $item->created('date'); ?>
                                        </time>
                                    </span>
                                </span>
                            </p>
                        <?php } else { ?>
                            <p class="typeof <?php echo $item->get('type'); ?>">
                                <?php echo $this->escape($item->type('title')); ?>
                            </p>
                        <?php } ?>
                    </div><!-- / .attribution -->
                    <?php
                    $this->view('display_' . $item->type(), 'posts')
                         ->set('actual', true)
                         ->set('option', $this->option)
                         ->set('params', $this->config)
                         ->set('row', $this->post)
                         ->display();
                    ?>
                    <?php if (count($item->tags()) > 0) { ?>
                        <div class="tags-wrap">
                            <p><?php echo $item->tags('render'); ?></p>
                        </div><!-- / .tags-wrap -->
                    <?php } ?>
                    <?php
                    $metadataUrl = Route::url(
                        $base . '&task=metadata&post='
                        . $this->post->get('id')
                    );
                    ?>
                    <div class="meta"
                        data-metadata-url="<?php echo $metadataUrl; ?>">
                        <p class="stats">
                            <span class="likes">
                                <?php echo Lang::txt('COM_COLLECTIONS_NUM_LIKES', $item->get('positive', 0)); ?>
                            </span>
                            <?php
                            // Display comments count only if enabled
                            if ($this->config->get('allow_comments')) :
                                ?>
                            <span class="comments">
                                <?php echo Lang::txt('COM_COLLECTIONS_NUM_COMMENTS', $item->get('comments', 0)); ?>
                            </span>
                            <?php endif; ?>
                            <span class="reposts">
                                <?php echo Lang::txt('COM_COLLECTIONS_NUM_REPOSTS', $item->get('reposts', 0)); ?>
                            </span>
                        </p>
                    </div><!-- / .meta -->
                    <div class="convo attribution">
                        <?php
                        $name = $this->escape(
                            stripslashes($this->post->creator()->get('name'))
                        );
                        $postCreatorAccess = in_array(
                            $this->post->creator()->get('access'),
                            User::getAuthorisedViewLevels()
                        );
                        $postCreatorLink = Route::url(
                            $this->post->creator()->link()
                        );
                        $postCreatorPic = $this->post->creator()->picture();
                        $postProfileAlt = Lang::txt(
                            'COM_COLLECTIONS_PROFILE_PICTURE',
                            $name
                        );

                        if ($postCreatorAccess) { ?>
                            <a href="<?php echo $postCreatorLink; ?>"
                                title="<?php echo $name; ?>"
                                class="img-link">
                                <img src="<?php echo $postCreatorPic; ?>"
                                    alt="<?php echo $postProfileAlt; ?>" />
                            </a>
                        <?php } else { ?>
                            <span class="img-link">
                                <img src="<?php echo $postCreatorPic; ?>"
                                    alt="<?php echo $postProfileAlt; ?>" />
                            </span>
                        <?php } ?>
                        <p>
                            <?php
                            $who = $name;
                            if ($postCreatorAccess) {
                                $collectionsLink = Route::url(
                                    $this->post->creator()->link()
                                    . '&active=collections'
                                );
                                $who = '<a href="' . $collectionsLink . '">'
                                    . $name . '</a>';
                            }

                            $collectionLink = Route::url(
                                $this->collection->link()
                            );
                            $collectionTitle = $this->escape(
                                stripslashes($this->collection->get('title'))
                            );
                            $where = '<a href="' . $collectionLink . '">'
                                . $collectionTitle . '</a>';

                            echo Lang::txt('COM_COLLECTIONS_ONTO', $who, $where);
                            ?>
                            <br />
                            <span class="entry-date">
                                <span class="entry-date-at">
                                    <?php echo Lang::txt('COM_COLLECTIONS_AT'); ?>
                                </span>
                                <span class="time">
                                    <time datetime="<?php echo $this->post->created(); ?>">
                                        <?php echo $this->post->created('time'); ?>
                                    </time>
                                </span>
                                <span class="entry-date-on">
                                    <?php echo Lang::txt('COM_COLLECTIONS_ON'); ?>
                                </span>
                                <span class="date">
                                    <time datetime="<?php echo $this->post->created(); ?>">
                                        <?php echo $this->post->created('date'); ?>
                                    </time>
                                </span>
                            </span>
                        </p>
                    </div><!-- / .attribution -->
                </div><!-- / .content -->
            </div><!-- / .post -->

            <?php
            // Display comments only if enabled
            if ($this->config->get('allow_comments')) :
                ?>
            <div class="post-comments">
                <?php if ($item->get('comments')) { ?>
                    <ol class="comments">
                    <?php
                    foreach ($item->comments() as $comment) {
                        $cuser = \Components\Members\Models\Member::oneOrNew($comment->created_by);
                        $cname = Lang::txt('JANONYMOUS');
                        if (!$comment->anonymous) {
                            $cname = $this->escape(stripslashes($cuser->get('name')));
                            if (in_array($cuser->get('access'), User::getAuthorisedViewLevels())) {
                                $cname = '<a href="' . Route::url($cuser->link()) . '">' . $cname . '</a>';
                            }
                        }
                        $commentPic = $cuser->picture($comment->anonymous);
                        $commentPicAlt = Lang::txt(
                            'COM_COLLECTIONS_PROFILE_PICTURE',
                            $cname
                        );
                        $commentTime = Date::of($comment->created)->toLocal(
                            Lang::txt('TIME_FORMAT_HZ1')
                        );
                        $commentDate = Date::of($comment->created)->toLocal(
                            Lang::txt('DATE_FORMAT_HZ1')
                        );
                        ?>
                        <li class="comment" id="c<?php echo $comment->id; ?>">
                            <p class="comment-member-photo">
                                <img src="<?php echo $commentPic; ?>"
                                    alt="<?php echo $commentPicAlt; ?>" />
                            </p>
                            <div class="comment-content">
                                <p class="comment-title">
                                    <strong>
                                        <?php echo $cname; ?>
                                    </strong>
                                    <a class="permalink" href="#c">
                                        <span class="entry-date">
                                            <span class="entry-date-at">
                                                <?php echo Lang::txt('COM_COLLECTIONS_AT'); ?>
                                            </span>
                                            <span class="time">
                                                <time datetime="<?php echo $comment->created; ?>">
                                                    <?php echo $commentTime; ?>
                                                </time>
                                            </span>
                                            <span class="entry-date-on">
                                                <?php echo Lang::txt('COM_COLLECTIONS_ON'); ?>
                                            </span>
                                            <span class="date">
                                                <time datetime="<?php echo $comment->created; ?>">
                                                    <?php echo $commentDate; ?>
                                                </time>
                                            </span>
                                        </span>
                                    </a>
                                </p>
                                <div class="comment-body">
                                    <p><?php echo stripslashes($comment->content); ?></p>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                    </ol>
                <?php } ?>
                <?php if (!User::isGuest()) { ?>
                    <?php
                    $commentFormUrl = Route::url(
                        $base . '&post=' . $this->post->get('id')
                        . '&task=savecomment'
                        . ($this->no_html ? '&no_html=' . $this->no_html : '')
                    );
                    $userPic = User::picture(0);
                    $userPicAlt = Lang::txt(
                        'COM_COLLECTIONS_PROFILE_PICTURE',
                        $this->escape(stripslashes(User::get('name')))
                    );
                    $userLink = Route::url(
                        'index.php?option=com_members&id=' . User::get('id')
                    );
                    $nowTime = Date::toSql();
                    $nowTimeLocal = Date::toLocal(
                        Lang::txt('TIME_FORMAT_HZ1')
                    );
                    $nowDateLocal = Date::toLocal(
                        Lang::txt('DATE_FORMAT_HZ1')
                    );
                    ?>
                    <form action="<?php echo $commentFormUrl; ?>"
                        method="post"
                        id="commentform"
                        enctype="multipart/form-data">
                        <p class="comment-member-photo">
                            <img src="<?php echo $userPic; ?>"
                                alt="<?php echo $userPicAlt; ?>" />
                        </p>

                        <fieldset>
                            <p class="comment-title">
                                <a href="<?php echo $userLink; ?>">
                                    <?php echo $this->escape(stripslashes(User::get('name'))); ?>
                                </a>
                                <span class="permalink">
                                    <span class="entry-date-at">
                                        <?php echo Lang::txt('COM_COLLECTIONS_AT'); ?>
                                    </span>
                                    <span class="time">
                                        <time datetime="<?php echo $nowTime; ?>">
                                            <?php echo $nowTimeLocal; ?>
                                        </time>
                                    </span>
                                    <span class="entry-date-on">
                                        <?php echo Lang::txt('COM_COLLECTIONS_ON'); ?>
                                    </span>
                                    <span class="date">
                                        <time datetime="<?php echo $nowTime; ?>">
                                            <?php echo $nowDateLocal; ?>
                                        </time>
                                    </span>
                                </span>
                            </p>

                            <label for="comment-content">
                                <span class="label-text">
                                    <?php echo Lang::txt('COM_COLLECTIONS_FIELD_COMMENTS'); ?>
                                </span>
                                <?php
                                echo $this->editor(
                                    'comment[content]',
                                    '',
                                    35,
                                    5,
                                    'comment-content',
                                    array('class' => 'minimal no-footer')
                                );
                                ?>
                            </label>

                            <input type="hidden" name="comment[id]" value="0" />
                            <input type="hidden" name="comment[item_id]" value="<?php echo $item->get('id'); ?>" />
                            <input type="hidden" name="comment[item_type]" value="collection" />
                            <input type="hidden" name="comment[state]" value="1" />

                            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                            <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
                            <input type="hidden" name="post" value="<?php echo $this->post->get('id'); ?>" />
                            <input type="hidden" name="task" value="savecomment" />
                            <input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />

                            <?php echo Html::input('token'); ?>

                            <label for="comment-anonymous" id="comment-anonymous-label">
                                <input class="option"
                                    type="checkbox"
                                    name="comment[anonymous]"
                                    id="comment-anonymous"
                                    value="1" />
                                <?php echo Lang::txt('COM_COLLECTIONS_FIELD_ANONYMOUS'); ?>
                            </label>

                            <p class="submit">
                                <input type="submit" value="<?php echo Lang::txt('COM_COLLECTIONS_SAVE'); ?>" />
                            </p>
                        </fieldset>
                    </form>
                <?php } ?>
            </div>
            <?php endif; ?>

        </div>
        <div class="col span4 omega">
            <?php
            $collCloseupUrl = Route::url(
                $base . '&controller=posts&collection='
                . $this->collection->get('id')
            );
            ?>
            <div class="post full collection"
                id="b<?php echo $this->collection->get('id'); ?>"
                data-id="<?php echo $this->collection->get('id'); ?>"
                data-closeup-url="<?php echo $collCloseupUrl; ?>">
                <div class="content">
                    <?php
                        $this->view('display_collection', 'posts')
                             ->set('option', $this->option)
                             ->set('params', $this->config)
                             ->set('row', $this->collection)
                             ->display();
                    ?>
                    <?php if ($tags = $this->collection->item()->tags('cloud')) { ?>
                            <div class="tags-wrap">
                                <?php echo $tags; ?>
                            </div>
                    <?php } ?>
                    <div class="meta">
                        <p class="stats">
                            <span class="likes">
                                <?php
                                $collLikes = $this->collection->get('positive', 0);
                                echo Lang::txt('COM_COLLECTIONS_NUM_LIKES', $collLikes);
                                ?>
                            </span>
                            <?php
                            /*
                            <span class="reposts">
                                <?php echo Lang::txt('COM_COLLECTIONS_NUM_REPOSTS', ...); ?>
                            </span>
                            */
                            ?>
                            <span class="posts">
                                <?php echo Lang::txt('COM_COLLECTIONS_NUM_POSTS', $this->collection->count('post')); ?>
                            </span>
                        </p>
                        <?php if (!$no_html) { ?>
                        <div class="actions">
                            <?php if (!User::isGuest()) { ?>
                                <?php
                                $isOwner = ($this->collection->get('object_type') == 'member'
                                    && $this->collection->get('object_id') == User::get('id'));
                                $collLink = $this->collection->link();
                                ?>
                                <?php if ($isOwner) { ?>
                                        <?php
                                        $editUrl = Route::url($collLink . '/edit');
                                        $deleteUrl = Route::url($collLink . '/delete');
                                        ?>
                                        <a class="btn edit"
                                            data-id="<?php echo $this->collection->get('id'); ?>"
                                            href="<?php echo $editUrl; ?>">
                                            <span><?php echo Lang::txt('JACTION_EDIT'); ?></span>
                                        </a>
                                        <a class="btn delete"
                                            data-id="<?php echo $this->collection->get('id'); ?>"
                                            href="<?php echo $deleteUrl; ?>">
                                            <span><?php echo Lang::txt('JACTION_DELETE'); ?></span>
                                        </a>
                                <?php } else { ?>
                                        <?php
                                        $repostUrl = Route::url(
                                            $base . '&controller=posts&board='
                                            . $this->collection->get('id') . '&task=collect'
                                        );
                                        $followTxt = Lang::txt('COM_COLLECTIONS_FOLLOW');
                                        $unfollowTxt = Lang::txt('COM_COLLECTIONS_UNFOLLOW');
                                        ?>
                                        <a class="btn repost"
                                            data-id="<?php echo $this->collection->get('id'); ?>"
                                            href="<?php echo $repostUrl; ?>">
                                            <span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
                                        </a>
                                    <?php if ($this->collection->isFollowing()) { ?>
                                        <?php
                                        $unfollowUrl = Route::url($collLink . '/unfollow');
                                        ?>
                                        <a class="btn unfollow"
                                            data-id="<?php echo $this->collection->get('id'); ?>"
                                            data-text-follow="<?php echo $followTxt; ?>"
                                            data-text-unfollow="<?php echo $unfollowTxt; ?>"
                                            href="<?php echo $unfollowUrl; ?>">
                                            <span><?php echo $unfollowTxt; ?></span>
                                        </a>
                                    <?php } else { ?>
                                        <?php
                                        $followUrl = Route::url($collLink . '/follow');
                                        ?>
                                        <a class="btn follow"
                                            data-id="<?php echo $this->collection->get('id'); ?>"
                                            data-text-follow="<?php echo $followTxt; ?>"
                                            data-text-unfollow="<?php echo $unfollowTxt; ?>"
                                            href="<?php echo $followUrl; ?>">
                                            <span><?php echo $followTxt; ?></span>
                                        </a>
                                    <?php } ?>
                                <?php } ?>
                            <?php } else { ?>
                                <?php
                                $collectReturn = base64_encode(Route::url(
                                    $base . '&controller=posts&board='
                                    . $this->collection->get('id') . '&task=collect',
                                    false,
                                    true
                                ));
                                $loginCollectUrl = Route::url(
                                    'index.php?option=com_users&view=login&return='
                                    . $collectReturn,
                                    false
                                );
                                $followReturn = base64_encode(Route::url(
                                    $this->collection->link() . '/follow'
                                ));
                                $loginFollowUrl = Route::url(
                                    'index.php?option=com_users&view=login&return='
                                    . $followReturn,
                                    false
                                );
                                $loginCollectTip = Lang::txt(
                                    'COM_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'
                                );
                                $loginFollowTip = Lang::txt(
                                    'COM_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW'
                                );
                                ?>
                                <a class="btn repost tooltips"
                                    href="<?php echo $loginCollectUrl; ?>"
                                    title="<?php echo $loginCollectTip; ?>">
                                    <span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
                                </a>
                                <a class="btn follow tooltips"
                                    href="<?php echo $loginFollowUrl; ?>"
                                    title="<?php echo $loginFollowTip; ?>">
                                    <span><?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?></span>
                                </a>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="convo attribution">
                        <?php
                        $name = $this->escape(
                            stripslashes($this->collection->creator('name'))
                        );
                        $cCreatorAccess = in_array(
                            $this->collection->creator()->get('access'),
                            User::getAuthorisedViewLevels()
                        );
                        $cCreatorLink = Route::url(
                            $this->collection->creator()->link()
                        );
                        $cCreatorPic = $this->collection->creator()->picture();
                        $cProfileAlt = Lang::txt(
                            'COM_COLLECTIONS_PROFILE_PICTURE',
                            $name
                        );

                        if ($cCreatorAccess) { ?>
                            <a href="<?php echo $cCreatorLink; ?>"
                                title="<?php echo $name; ?>"
                                class="img-link">
                                <img src="<?php echo $cCreatorPic; ?>"
                                    alt="<?php echo $cProfileAlt; ?>" />
                            </a>
                        <?php } else { ?>
                            <span class="img-link">
                                <img src="<?php echo $cCreatorPic; ?>"
                                    alt="<?php echo $cProfileAlt; ?>" />
                            </span>
                        <?php } ?>
                        <p>
                            <?php if ($cCreatorAccess) { ?>
                                <a href="<?php echo $cCreatorLink; ?>">
                                    <?php echo $name; ?>
                                </a>
                            <?php } else { ?>
                                <?php echo $name; ?>
                            <?php } ?>
                            <br />
                            <span class="entry-date">
                                <span class="entry-date-at">
                                    <?php echo Lang::txt('COM_COLLECTIONS_AT'); ?>
                                </span>
                                <span class="time">
                                    <time datetime="<?php echo $this->collection->created(); ?>">
                                        <?php echo $this->collection->created('time'); ?>
                                    </time>
                                </span>
                                <span class="entry-date-on">
                                    <?php echo Lang::txt('COM_COLLECTIONS_ON'); ?>
                                </span>
                                <span class="date">
                                    <time datetime="<?php echo $this->collection->created(); ?>">
                                        <?php echo $this->collection->created('date'); ?>
                                    </time>
                                </span>
                            </span>
                        </p>
                    </div><!-- / .attribution -->
                </div><!-- / .content -->
            </div><!-- / .post -->
        </div>
    </div>
</section>

<?php
$otherCollections = $item->collections(
    'list',
    array('collection_id' => $this->collection->get('id'))
);
if ($otherCollections->total()) { ?>
    <section class="section post-collections">
        <h3><?php echo Lang::txt('COM_COLLECTIONS_ALSO_IN_THESE_COLLECTIONS'); ?></h3>
        <div id="posts">
            <?php foreach ($item->collections() as $collection) { ?>
                <?php
                $collCloseup = Route::url(
                    $base . '&controller=collection&id='
                    . $collection->get('id')
                );
                ?>
                <div class="post collection"
                    id="b<?php echo $collection->get('id'); ?>"
                    data-id="<?php echo $collection->get('id'); ?>"
                    data-closeup-url="<?php echo $collCloseup; ?>"
                    data-width="600"
                    data-height="350">
                    <div class="content">
                        <?php
                        $this->view('display_collection', 'posts')
                             ->set('option', $this->option)
                             ->set('params', $this->config)
                             ->set('row', $collection)
                             ->display();
                        ?>
                        <?php if ($tags = $collection->item()->tags('cloud')) { ?>
                            <div class="tags-wrap">
                                <?php echo $tags; ?>
                            </div>
                        <?php } ?>
                        <div class="meta">
                            <p class="stats">
                                <span class="likes">
                                    <?php
                                    $colLikes = $collection->get('positive', 0);
                                    echo Lang::txt('COM_COLLECTIONS_NUM_LIKES', $colLikes);
                                    ?>
                                </span>
                                <?php
                                /*
                                <span class="reposts">
                                    <?php echo Lang::txt('COM_COLLECTIONS_NUM_REPOSTS', ...); ?>
                                </span>
                                */
                                ?>
                                <span class="posts">
                                    <?php echo Lang::txt('COM_COLLECTIONS_NUM_POSTS', $collection->count('posts')); ?>
                                </span>
                            </p>
                            <?php if (!$no_html) { ?>
                            <div class="actions">
                                <?php if (!User::isGuest()) { ?>
                                    <?php
                                    $isCollOwner = ($collection->get('object_type') == 'member'
                                        && $collection->get('object_id') == User::get('id'));
                                    $cLink = $collection->link();
                                    ?>
                                    <?php if ($isCollOwner) { ?>
                                            <?php
                                            $cEditUrl = Route::url($cLink . '/edit');
                                            $cDeleteUrl = Route::url($cLink . '/delete');
                                            ?>
                                            <a class="btn edit"
                                                data-id="<?php echo $collection->get('id'); ?>"
                                                href="<?php echo $cEditUrl; ?>">
                                                <span><?php echo Lang::txt('JACTION_EDIT'); ?></span>
                                            </a>
                                            <a class="btn delete"
                                                data-id="<?php echo $collection->get('id'); ?>"
                                                href="<?php echo $cDeleteUrl; ?>">
                                                <span><?php echo Lang::txt('JACTION_DELETE'); ?></span>
                                            </a>
                                    <?php } else { ?>
                                            <?php
                                            $cRepostUrl = Route::url(
                                                $base . '&controller=posts&board='
                                                . $collection->get('id') . '&task=collect'
                                            );
                                            $cFollowTxt = Lang::txt('COM_COLLECTIONS_FOLLOW');
                                            $cUnfollowTxt = Lang::txt('COM_COLLECTIONS_UNFOLLOW');
                                            ?>
                                            <a class="btn repost"
                                                data-id="<?php echo $collection->get('id'); ?>"
                                                href="<?php echo $cRepostUrl; ?>">
                                                <span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
                                            </a>
                                        <?php if ($collection->isFollowing()) { ?>
                                            <?php $cUnfollowUrl = Route::url($cLink . '/unfollow'); ?>
                                            <a class="btn unfollow"
                                                data-id="<?php echo $collection->get('id'); ?>"
                                                data-text-follow="<?php echo $cFollowTxt; ?>"
                                                data-text-unfollow="<?php echo $cUnfollowTxt; ?>"
                                                href="<?php echo $cUnfollowUrl; ?>">
                                                <span><?php echo $cUnfollowTxt; ?></span>
                                            </a>
                                        <?php } else { ?>
                                            <?php $cFollowUrl = Route::url($cLink . '/follow'); ?>
                                            <a class="btn follow"
                                                data-id="<?php echo $collection->get('id'); ?>"
                                                data-text-follow="<?php echo $cFollowTxt; ?>"
                                                data-text-unfollow="<?php echo $cUnfollowTxt; ?>"
                                                href="<?php echo $cFollowUrl; ?>">
                                                <span><?php echo $cFollowTxt; ?></span>
                                            </a>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } else { ?>
                                    <?php
                                    $cCollectReturn = base64_encode(Route::url(
                                        $base . '&controller=posts&board='
                                        . $collection->get('id') . '&task=collect',
                                        false,
                                        true
                                    ));
                                    $cLoginCollectUrl = Route::url(
                                        'index.php?option=com_users&view=login&return='
                                        . $cCollectReturn,
                                        false
                                    );
                                    $cFollowReturn = base64_encode(Route::url(
                                        $cLink . '/follow'
                                    ));
                                    $cLoginFollowUrl = Route::url(
                                        'index.php?option=com_users&view=login&return='
                                        . $cFollowReturn,
                                        false
                                    );
                                    $cLoginCollectTip = Lang::txt(
                                        'COM_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'
                                    );
                                    $cLoginFollowTip = Lang::txt(
                                        'COM_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW'
                                    );
                                    ?>
                                    <a class="btn repost tooltips"
                                        href="<?php echo $cLoginCollectUrl; ?>"
                                        title="<?php echo $cLoginCollectTip; ?>">
                                        <span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
                                    </a>
                                    <a class="btn follow tooltips"
                                        href="<?php echo $cLoginFollowUrl; ?>"
                                        title="<?php echo $cLoginFollowTip; ?>">
                                        <span><?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?></span>
                                    </a>
                                <?php } ?>
                            </div><!-- / .actions -->
                            <?php } ?>
                        </div><!-- / .meta -->
                        <div class="convo attribution">
                            <?php
                            $name = $this->escape(
                                stripslashes($collection->creator()->get('name'))
                            );
                            $colCreatorAccess = in_array(
                                $collection->creator()->get('access'),
                                User::getAuthorisedViewLevels()
                            );
                            $colCreatorLink = Route::url(
                                $collection->creator()->link()
                                . '&active=collections'
                            );
                            $colCreatorPic = $collection->creator()->picture();
                            $colProfileAlt = Lang::txt(
                                'COM_COLLECTIONS_PROFILE_PICTURE',
                                $name
                            );

                            if ($colCreatorAccess) { ?>
                                <a href="<?php echo $colCreatorLink; ?>"
                                    title="<?php echo $name; ?>"
                                    class="img-link">
                                    <img src="<?php echo $colCreatorPic; ?>"
                                        alt="<?php echo $colProfileAlt; ?>" />
                                </a>
                            <?php } else { ?>
                                <span class="img-link">
                                    <img src="<?php echo $colCreatorPic; ?>"
                                        alt="<?php echo $colProfileAlt; ?>" />
                                </span>
                            <?php } ?>
                            <p>
                                <?php if ($colCreatorAccess) { ?>
                                    <a href="<?php echo $colCreatorLink; ?>">
                                        <?php echo $name; ?>
                                    </a>
                                <?php } else { ?>
                                    <?php echo $name; ?>
                                <?php } ?>
                                <br />
                                <?php
                                $colTime = Date::of($collection->get('created'))->toLocal(
                                    Lang::txt('TIME_FORMAT_HZ1')
                                );
                                $colDate = Date::of($collection->get('created'))->toLocal(
                                    Lang::txt('DATE_FORMAT_HZ1')
                                );
                                ?>
                                <span class="entry-date">
                                    <span class="entry-date-at">
                                        <?php echo Lang::txt('COM_COLLECTIONS_AT'); ?>
                                    </span>
                                    <span class="time">
                                        <?php echo $colTime; ?>
                                    </span>
                                    <span class="entry-date-on">
                                        <?php echo Lang::txt('COM_COLLECTIONS_ON'); ?>
                                    </span>
                                    <span class="date">
                                        <?php echo $colDate; ?>
                                    </span>
                                </span>
                            </p>
                        </div><!-- / .attribution -->
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
<?php }
