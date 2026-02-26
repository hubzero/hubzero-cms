<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Component;
use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

$item = $this->post->item();
$base = $this->member->link() . '&active=' . $this->name;

$this->css()
    ->js();

// Get the comments config value
$allow_comments = Component::params('com_collections')->get('allow_comments');
?>

<div class="post full <?php echo $item->type(); ?>"
    id="b<?php echo $this->post->get('id'); ?>"
    data-id="<?php echo $this->post->get('id'); ?>"
    data-closeup-url="<?php echo Route::url($base . '&task=post/' . $this->post->get('id')); ?>"
    data-width="600"
    data-height="350">
    <div class="content">
        <div class="creator attribution cf">
            <?php if ($item->get('type') == 'file' || $item->get('type') == 'collection') { ?>
                <?php

                $name = $this->escape(stripslashes($item->creator()->get('name')));

                if (in_array($item->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
                    <a href="<?php echo Route::url($item->creator()->link()); ?>"
                        title="<?php echo $name; ?>"
                        class="img-link">
                        <img src="<?php echo $item->creator()->picture(); ?>"
                            alt="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $name); ?>"/>
                    </a>
                <?php } else { ?>
                    <span class="img-link">
                        <img src="<?php echo $item->creator()->picture(); ?>"
                            alt="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_PROFILE_PICTURE', $name); ?>"/>
                    </span>
                <?php } ?>
                <p>
                    <a href="<?php echo Route::url($item->creator()->link()); ?>">
                        <?php echo $this->escape(stripslashes($item->creator()->get('name'))); ?>
                    </a> created this post
                    <br />
                    <?php
                    $itemDatetime = $item->created();
                    $itemTime = $item->created('time');
                    $itemDate = $item->created('date');
                    ?>
                    <span class="entry-date">
                        <span class="entry-date-at">@</span>
                        <span class="time">
                            <time datetime="<?php echo $itemDatetime; ?>">
                                <?php echo $itemTime; ?>
                            </time>
                        </span>
                        <span class="entry-date-on">on</span>
                        <span class="date">
                            <time datetime="<?php echo $itemDatetime; ?>">
                                <?php echo $itemDate; ?>
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

        $this->view('default_' . $item->type(), 'post')
            ->set('actual', true)
            ->set('name', $this->name)
            ->set('option', $this->option)
            ->set('member', $this->member)
            ->set('params', $this->params)
            ->set('row', $this->post)
            ->display();
        ?>
        <?php if (count($item->tags()) > 0) { ?>
            <div class="tags-wrap">
                <?php echo $item->tags('render'); ?>
            </div>
        <?php } ?>
        <div class="meta">
            <p class="stats">
                <span class="likes">
                    <?php echo Lang::txt('%s likes', $item->get('positive', 0)); ?>
                </span>
                <?php

                // Display comments count only if enabled
                if ($allow_comments) :
                    ?>
                <span class="comments">
                    <?php echo Lang::txt('%s comments', $item->get('comments', 0)); ?>
                </span>
                <?php endif; ?>
                <span class="reposts">
                    <?php echo Lang::txt('%s reposts', $item->get('reposts', 0)); ?>
                </span>
            </p>
        </div><!-- / .meta -->
        <?php
        $postCreatorName = $this->escape(
            stripslashes($this->post->creator()->get('name'))
        );
        $postCreatorLink = Route::url(
            $this->post->creator()->link()
        );
        $postCreatorPic = $this->post->creator()->picture();
        $postCreatorAlt = 'Profile picture of ' . $postCreatorName;
        ?>
        <div class="convo attribution clearfix">
            <a href="<?php echo $postCreatorLink; ?>"
                title="<?php echo $postCreatorName; ?>"
                class="img-link">
                <img src="<?php echo $postCreatorPic; ?>"
                    alt="<?php echo $postCreatorAlt; ?>" />
            </a>
            <p>
                <?php

                $who = $this->escape(stripslashes($this->post->creator()->get('name')));
                if (in_array($this->post->creator()->get('access'), User::getAuthorisedViewLevels())) {
                    $who = '<a href="' . Route::url($this->post->creator()->link()) . '">' . $name . '</a>';
                }

                $where = '<a href="'
                    . Route::url($base . '&task=' . $this->collection->get('alias'))
                    . '">'
                    . $this->escape(stripslashes($this->collection->get('title')))
                    . '</a>';

                echo Lang::txt('PLG_MEMBERS_COLLECTIONS_ONTO', $who, $where);
                ?>
                <br />
                <?php
                $postDatetime = $this->post->created();
                $postTime = $this->post->created('time');
                $postDate = $this->post->created('date');
                ?>
                <span class="entry-date">
                    <span class="entry-date-at">@</span>
                    <span class="time">
                        <time datetime="<?php echo $postDatetime; ?>">
                            <?php echo $postTime; ?>
                        </time>
                    </span>
                    <span class="entry-date-on">on</span>
                    <span class="date">
                        <time datetime="<?php echo $postDatetime; ?>">
                            <?php echo $postDate; ?>
                        </time>
                    </span>
                </span>
            </p>
        </div><!-- / .attribution -->

    <?php

    if ($item->get('comments') && $allow_comments) {
        ?>
        <div class="commnts">
            <?php

            foreach ($item->comments() as $comment) {
                $cuser = $comment->creator;
                ?>
                <div class="comment convo clearfix" id="c<?php echo $comment->get('id'); ?>">
                    <a href="<?php echo Route::url($cuser->link()); ?>" class="img-link">
                        <img src="<?php echo $cuser->picture($comment->anonymous); ?>"
                            class="profile user_image"
                            alt="Profile picture of <?php echo $this->escape(stripslashes($cuser->get('name'))); ?>"/>
                    </a>
                    <?php
                    $cuserLink = Route::url($cuser->link());
                    $cuserName = $this->escape(
                        stripslashes($cuser->get('name'))
                    );
                    $cmtDatetime = $comment->get('created');
                    $cmtTime = $comment->created('time');
                    $cmtDate = $comment->created('date');
                    ?>
                    <p>
                        <a href="<?php echo $cuserLink; ?>">
                            <?php echo $cuserName; ?>
                        </a> said <br />
                        <span class="entry-date">
                            <span class="entry-date-at">@</span>
                            <span class="time">
                                <time datetime="<?php echo $cmtDatetime; ?>">
                                    <?php echo $cmtTime; ?>
                                </time>
                            </span>
                            <span class="entry-date-on">on</span>
                            <span class="date">
                                <time datetime="<?php echo $cmtDatetime; ?>">
                                    <?php echo $cmtDate; ?>
                                </time>
                            </span>
                        </span>
                    </p>
                    <blockquote>
                        <p><?php echo stripslashes($comment->content); ?></p>
                    </blockquote>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }

    if (!User::isGuest() && $allow_comments) {
        $now = Date::of('now');
        $userProfileUrl = Route::url(
            'index.php?option=com_members&id=' . User::get('id')
        );
        $userName = $this->escape(
            stripslashes(User::get('name'))
        );
        $userPic = User::picture(0);
        $userAlt = 'Profile picture of ' . $userName;
        $nowTime = Date::of($now)->toLocal(
            Lang::txt('TIME_FORMAT_HZ1')
        );
        $nowDate = Date::of($now)->toLocal(
            Lang::txt('DATE_FORMAT_HZ1')
        );
        $commentFormAction = Route::url(
            $base . '&task=post/'
            . $this->post->get('id') . '/savecomment'
        );
        ?>
        <div class="commnts">
            <div class="comment convo clearfix">
                <a href="<?php echo $userProfileUrl; ?>"
                    class="img-link">
                    <img src="<?php echo $userPic; ?>"
                        class="profile user_image"
                        alt="<?php echo $userAlt; ?>"/>
                </a>
                <p>
                    <a href="<?php echo $userProfileUrl; ?>">
                        <?php echo $userName; ?>
                    </a> will say <br />
                    <span class="entry-date">
                        <span class="entry-date-at">@</span>
                        <span class="time">
                            <time datetime="<?php echo $now; ?>">
                                <?php echo $nowTime; ?>
                            </time>
                        </span>
                        <span class="entry-date-on">on</span>
                        <span class="date">
                            <time datetime="<?php echo $now; ?>">
                                <?php echo $nowDate; ?>
                            </time>
                        </span>
                    </span>
                </p>
                <form action="<?php echo $commentFormAction; ?>"
                    method="post"
                    id="comment-form"
                    enctype="multipart/form-data">
                    <fieldset>
                        <input type="hidden" name="comment[id]" value="0" />
                        <input type="hidden" name="comment[item_id]" value="<?php echo $item->get('id'); ?>" />
                        <input type="hidden" name="comment[item_type]" value="collection" />
                        <input type="hidden" name="comment[state]" value="1" />

                        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                        <input type="hidden" name="id" value="<?php echo $this->member->get('id'); ?>" />
                        <input type="hidden"
                            name="scope"
                            value="post/<?php echo $this->post->get('id'); ?>/savecomment"/>
                        <input type="hidden" name="action" value="savecomment" />
                        <input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />

                        <?php echo Html::input('token'); ?>

                        <textarea name="comment[content]" cols="35" rows="3"></textarea>
                        <input type="submit" class="comment-submit" value="<?php echo Lang::txt('Post comment'); ?>" />
                    </fieldset>
                </form>
            </div>
        </div>
        <?php
    }
    ?>
    </div><!-- / .content -->
</div><!-- / .bulletin -->
