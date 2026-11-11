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

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{collection: mixed, config: mixed, no_html: mixed, post: mixed} $this
 */

$item = $this->post->item();

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller;
$no_html = Request::getInt('no_html', 0);

if (!$no_html) {
    $this
         ->css()
         ->js('post.js');
    ?>
<header id="content-header">
    <h2><?php echo Lang::txt('COM_COLLECTIONS'); ?></h2>

    <div id="content-header-extra">
        <p>
            <?php
            $linkUrl = Route::url('index.php?option=com_help&component=' . substr($this->option, 4) . '&page=index');
            ?>
            <a class="icon-info btn popup" href="<?= $linkUrl ?>">
                <span><?php echo Lang::txt('COM_COLLECTIONS_GETTING_STARTED'); ?></span>
            </a>
        </p>
    </div>
</header>
<?php } ?>

<section class="section">
    <div class="grid">
        <div class="col span8">

            <div class="post full <?= $item->type() ?>" id="p<?= $this->post->get('id') ?>"
                data-id="<?= $this->post->get('id') ?>"
                data-closeup-url="<?= Route::url($base . '&post=' . $this->post->get('id') . '&task=comment') ?>"
                data-width="600" data-height="350">
                <div class="content">
                    <div class="creator attribution cf">
                        <?php if ($item->get('type') == 'file' || $item->get('type') == 'collection') { ?>
                            <?php
                            $name = $this->escape(stripslashes($item->creator()->get('name')));

                            if (in_array($item->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
                                <a href="<?= Route::url($item->creator()->link()) ?>" title="<?= $name ?>"
                                    class="img-link">
                                    <img src="<?= $item->creator()->picture() ?>"
                                        alt="<?= Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name) ?>" />
                                </a>
                            <?php } else { ?>
                                <span class="img-link">
                                    <img src="<?= $item->creator()->picture() ?>"
                                        alt="<?= Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name) ?>" />
                                </span>
                            <?php } ?>
                            <p>
                                <?= Lang::txt(
                                    'COM_COLLECTIONS_USER_CREATED_POST',
                                    (
                                        in_array($item->creator()->get('access'), User::getAuthorisedViewLevels())
                                            ? '<a href="' . Route::url($item->creator()->link()) . '">'
                                            : ''
                                    ) . $this->escape(stripslashes($item->creator()->get('name')))
                                        . (
                                            in_array($item->creator()->get('access'), User::getAuthorisedViewLevels())
                                                ? '</a>'
                                                : ''
                                        ),
                                ) ?>
                                <br />
                                <span class="entry-date">
                                    <span class="entry-date-at"><?php echo Lang::txt('COM_COLLECTIONS_AT'); ?></span>
                                    <span class="time"><time
                                        datetime="<?= $item->created() ?>"><?= $item->created('time') ?></time></span>
                                    <span class="entry-date-on"><?php echo Lang::txt('COM_COLLECTIONS_ON'); ?></span>
                                    <span class="date"><time
                                        datetime="<?= $item->created() ?>"><?= $item->created('date') ?></time></span>
                                </span>
                            </p>
                        <?php } else { ?>
                            <p class="typeof <?php echo $this->escape($item->get('type')); ?>">
                                <?php echo $this->escape($item->type('title')); ?>
                            </p>
                        <?php } ?>
                    </div><!-- / .attribution -->
                    <?php
                    $this
                         ->view('display_' . $item->layout(__DIR__, 'display_'), 'posts')
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
                    <div class="meta"
                        data-metadata-url="<?= Route::url($base . '&task=metadata&post=' . $this->post->get('id')) ?>">
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
                        $name = $this->escape(stripslashes($this->post->creator()->get('name')));

                        if (in_array($this->post->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
                            <a href="<?= Route::url($this->post->creator()->link()) ?>" title="<?= $name ?>"
                                class="img-link">
                                <img src="<?= $this->post->creator()->picture() ?>"
                                    alt="<?= Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name) ?>" />
                            </a>
                        <?php } else { ?>
                            <span class="img-link">
                                <img src="<?= $this->post->creator()->picture() ?>"
                                    alt="<?= Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name) ?>" />
                            </span>
                        <?php } ?>
                        <p>
                            <?php
                            $who = $name;
                            if (in_array($this->post->creator()->get('access'), User::getAuthorisedViewLevels())) {
                                $who = '<a href="' . Route::url($this->post->creator()->link() . '&active=collections')
                                    . '" aria-label="' . Lang::txt('COM_COLLECTIONS_VIEW_COLLECTIONS_BY', $name) . '">'
                                    . $name . '</a>';
                            }

                            $where = '<a href="' . Route::url($this->collection->link()) . '">'
                                . $this->escape(stripslashes($this->collection->get('title'))) . '</a>';

                            echo Lang::txt('COM_COLLECTIONS_ONTO', $who, $where);
                            ?>
                            <br />
                            <span class="entry-date">
                                <span class="entry-date-at"><?php echo Lang::txt('COM_COLLECTIONS_AT'); ?></span>
                                <?php
                                $datetime = $this->post->created();
                                ?>
                                <span class="time"><time
                                    datetime="<?= $datetime ?>"><?= $this->post->created('time') ?></time></span>
                                <span class="entry-date-on"><?php echo Lang::txt('COM_COLLECTIONS_ON'); ?></span>
                                <?php
                                $datetime = $this->post->created();
                                ?>
                                <span class="date"><time
                                    datetime="<?= $datetime ?>"><?= $this->post->created('date') ?></time></span>
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
                        $cuser = Components\Members\Models\Member::oneOrNew($comment->created_by);
                        $cname = Lang::txt('JANONYMOUS');
                        if (!$comment->anonymous) {
                            $cname = $this->escape(stripslashes($cuser->get('name')));
                            if (in_array($cuser->get('access'), User::getAuthorisedViewLevels())) {
                                $cname = '<a href="' . Route::url($cuser->link()) . '">' . $cname . '</a>';
                            }
                        }
                        ?>
                        <li class="comment" id="c<?php echo $comment->id; ?>">
                            <p class="comment-member-photo">
                                <img src="<?= $cuser->picture($comment->anonymous) ?>"
                                    alt="<?= Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $cname) ?>" />
                            </p>
                            <div class="comment-content">
                                <p class="comment-title">
                                    <strong>
                                        <?php echo $cname; ?>
                                    </strong>
                                    <a class="permalink" href="#c">
                                        <span class="entry-date">
                                            <span class="entry-date-at"><?= Lang::txt('COM_COLLECTIONS_AT') ?></span>
                                            <?php
                                            $timeText = Date::of($comment->created)->toLocal(
                                                Lang::txt('TIME_FORMAT_HZ1'),
                                            );
                                            ?>
                                            <span class="time"><time
                                                datetime="<?= $comment->created ?>"><?= $timeText ?></time></span>
                                            <span class="entry-date-on"><?= Lang::txt('COM_COLLECTIONS_ON') ?></span>
                                            <?php
                                            $timeText = Date::of($comment->created)->toLocal(
                                                Lang::txt('DATE_FORMAT_HZ1'),
                                            );
                                            ?>
                                            <span class="date"><time
                                                datetime="<?= $comment->created ?>"><?= $timeText ?></time></span>
                                        </span>
                                    </a>
                                </p>
                                <div class="comment-body">
                                    <p><?php echo $this->escape(stripslashes($comment->content)); ?></p>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                    </ol>
                <?php } ?>
                <?php if (!User::isGuest()) { ?>
                    <?php
                    $savecommentUrl = Route::url(
                        $base . '&post=' . $this->post->get('id') . '&task=savecomment'
                            . ($this->no_html ? '&no_html=' . $this->no_html : ''),
                    );
                    ?>
                    <form action="<?= $savecommentUrl ?>" method="post" id="commentform" enctype="multipart/form-data">
                        <p class="comment-member-photo">
                            <?php
                            $altText = Lang::txt(
                                'COM_COLLECTIONS_PROFILE_PICTURE',
                                $this->escape(stripslashes(User::get('name'))),
                            );
                            ?>
                            <img src="<?= User::picture(0) ?>" alt="<?= $altText ?>" />
                        </p>

                        <fieldset>
                            <p class="comment-title">
                                <a href="<?= Route::url('index.php?option=com_members&id=' . User::get('id')) ?>">
                                    <?php echo $this->escape(stripslashes(User::get('name'))); ?>
                                </a>
                                <span class="permalink">
                                    <span class="entry-date-at"><?php echo Lang::txt('COM_COLLECTIONS_AT'); ?></span>
                                    <?php
                                    $timeText = Date::toLocal(Lang::txt('TIME_FORMAT_HZ1'));
                                    ?>
                                    <span class="time"><time
                                        datetime="<?= Date::toSql() ?>"><?= $timeText ?></time></span>
                                    <span class="entry-date-on"><?php echo Lang::txt('COM_COLLECTIONS_ON'); ?></span>
                                    <?php
                                    $timeText = Date::toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                                    ?>
                                    <span class="date"><time
                                        datetime="<?= Date::toSql() ?>"><?= $timeText ?></time></span>
                                </span>
                            </p>

                            <label for="comment-content">
                                <span class="label-text"><?= Lang::txt('COM_COLLECTIONS_FIELD_COMMENTS') ?></span>
                                <?= $this->editor(
                                    'comment[content]',
                                    '',
                                    35,
                                    5,
                                    'comment-content',
                                    ['class' => 'minimal no-footer'],
                                ) ?>
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

                            <label for="comment-anonymous" class="comment-anonymous-label">
                                <input class="option" type="checkbox" name="comment[anonymous]" id="comment-anonymous"
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
            $dataCloseupUrlUrl = Route::url($base . '&controller=posts&collection=' . $this->collection->get('id'));
            ?>
            <div class="post full collection" id="b<?= $this->collection->get('id') ?>"
                data-id="<?= $this->collection->get('id') ?>" data-closeup-url="<?= $dataCloseupUrlUrl ?>">
                <div class="content">
                    <?php
                        $this
                             ->view('display_collection', 'posts')
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
                                <?= Lang::txt('COM_COLLECTIONS_NUM_LIKES', $this->collection->get('positive', 0)) ?>
                            </span>
                            <?php /*<span class="reposts">
                                <?php echo Lang::txt('COM_COLLECTIONS_NUM_REPOSTS', $this->collection->get('reposts',
                                    0)); ?>
                            </span> */ ?>
                            <span class="posts">
                                <?php echo Lang::txt('COM_COLLECTIONS_NUM_POSTS', $this->collection->count('post')); ?>
                            </span>
                        </p>
                        <?php if (!$no_html) { ?>
                        <div class="actions">
                            <?php if (!User::isGuest()) { ?>
                                <?php
                                if (
                                    $this->collection->get('object_type') == 'member'
                                    && $this->collection->get('object_id') == User::get('id')
                                ) { ?>
                                        <a class="btn edit" data-id="<?= $this->collection->get('id') ?>"
                                            href="<?= Route::url($this->collection->link() . '/edit') ?>">
                                            <span><?php echo Lang::txt('JACTION_EDIT'); ?></span>
                                        </a>
                                        <a class="btn delete" data-id="<?= $this->collection->get('id') ?>"
                                            href="<?= Route::url($this->collection->link() . '/delete') ?>">
                                            <span><?php echo Lang::txt('JACTION_DELETE'); ?></span>
                                        </a>
                                <?php } else { ?>
                                        <?php
                                        $collectUrl = Route::url(
                                            $base . '&controller=posts&board=' . $this->collection->get('id')
                                                . '&task=collect',
                                        );
                                        ?>
                                        <a class="btn repost" data-id="<?= $this->collection->get('id') ?>"
                                            href="<?= $collectUrl ?>">
                                            <span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
                                        </a>
                                    <?php if ($this->collection->isFollowing()) { ?>
                                        <button type="submit" form="hz-post-action"
                                            formaction="<?= Route::url($this->collection->link() . '/unfollow') ?>"
                                            class="btn unfollow" data-id="<?= $this->collection->get('id') ?>"
                                            data-text-follow="<?= Lang::txt('COM_COLLECTIONS_FOLLOW') ?>"
                                            data-text-unfollow="<?= Lang::txt('COM_COLLECTIONS_UNFOLLOW') ?>">
                                            <span><?php echo Lang::txt('COM_COLLECTIONS_UNFOLLOW'); ?></span>
                                        </button>
                                    <?php } else { ?>
                                        <button type="submit" form="hz-post-action"
                                            formaction="<?= Route::url($this->collection->link() . '/follow') ?>"
                                            class="btn follow" data-id="<?= $this->collection->get('id') ?>"
                                            data-text-follow="<?= Lang::txt('COM_COLLECTIONS_FOLLOW') ?>"
                                            data-text-unfollow="<?= Lang::txt('COM_COLLECTIONS_UNFOLLOW') ?>">
                                            <span><?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?></span>
                                        </button>
                                    <?php } ?>
                                <?php } ?>
                            <?php } else { ?>
                                <?php
                                $collectUrl = Route::url(
                                    'index.php?option=com_users&view=login&return='
                                        . base64_encode(
                                            Route::url(
                                                $base . '&controller=posts&board=' . $this->collection->get('id')
                                                    . '&task=collect',
                                                false,
                                                true,
                                            ),
                                        ),
                                    false,
                                );
                                $ariaLabel = Lang::txt('COM_COLLECTIONS_COLLECT') . ': '
                                    . $this->escape($this->collection->get('title'));
                                ?>
                                <a class="btn repost tooltips" href="<?= $collectUrl ?>"
                                    title="<?= Lang::txt('COM_COLLECTIONS_WARNING_LOGIN_TO_COLLECT') ?>"
                                    aria-label="<?= $ariaLabel ?>">
                                    <span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
                                </a>
                                <?php
                                $linkUrl = Route::url(
                                    'index.php?option=com_users&view=login&return='
                                        . base64_encode(Route::url($this->collection->link(), false, true)),
                                    false,
                                );
                                $ariaLabel = Lang::txt('COM_COLLECTIONS_FOLLOW') . ': '
                                    . $this->escape($this->collection->get('title'));
                                ?>
                                <a class="btn follow tooltips" href="<?= $linkUrl ?>"
                                    title="<?= Lang::txt('COM_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW') ?>"
                                    aria-label="<?= $ariaLabel ?>">
                                    <span><?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?></span>
                                </a>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="convo attribution">
                        <?php
                        $name = $this->escape(stripslashes($this->collection->creator('name')));

                        if (in_array($this->collection->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
                            <a href="<?= Route::url($this->collection->creator()->link()) ?>" title="<?= $name ?>"
                                class="img-link">
                                <img src="<?= $this->collection->creator()->picture() ?>"
                                    alt="<?= Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name) ?>" />
                            </a>
                        <?php } else { ?>
                            <span class="img-link">
                                <img src="<?= $this->collection->creator()->picture() ?>"
                                    alt="<?= Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name) ?>" />
                            </span>
                        <?php } ?>
                        <p>
                            <?php
                            if (
                                in_array($this->collection->creator()->get('access'), User::getAuthorisedViewLevels())
                            ) { ?>
                                <a href="<?php echo Route::url($this->collection->creator()->link()); ?>">
                                    <?php echo $name; ?>
                                </a>
                            <?php } else { ?>
                                <?php echo $name; ?>
                            <?php } ?>
                            <br />
                            <span class="entry-date">
                                <span class="entry-date-at"><?php echo Lang::txt('COM_COLLECTIONS_AT'); ?></span>
                                <?php
                                $datetime = $this->collection->created();
                                ?>
                                <span class="time"><time
                                    datetime="<?= $datetime ?>"><?= $this->collection->created('time') ?></time></span>
                                <span class="entry-date-on"><?php echo Lang::txt('COM_COLLECTIONS_ON'); ?></span>
                                <?php
                                $datetime = $this->collection->created();
                                ?>
                                <span class="date"><time
                                    datetime="<?= $datetime ?>"><?= $this->collection->created('date') ?></time></span>
                            </span>
                        </p>
                    </div><!-- / .attribution -->
                </div><!-- / .content -->
            </div><!-- / .post -->
        </div>
    </div>
</section>

<?php if ($item->collections('list', ['collection_id' => $this->collection->get('id')])->total()) { ?>
    <section class="section post-collections">
        <h3><?php echo Lang::txt('COM_COLLECTIONS_ALSO_IN_THESE_COLLECTIONS'); ?></h3>
        <div id="posts">
            <?php foreach ($item->collections() as $collection) { ?>
                <div class="post collection" id="b<?= $collection->get('id') ?>" data-id="<?= $collection->get('id') ?>"
                    data-closeup-url="<?= Route::url($base . '&controller=collection&id=' . $collection->get('id')) ?>"
                    data-width="600" data-height="350">
                    <div class="content">
                        <?php
                        $this
                             ->view('display_collection', 'posts')
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
                                    <?= Lang::txt('COM_COLLECTIONS_NUM_LIKES', $collection->get('positive', 0)) ?>
                                </span>
                                <?php /*<span class="reposts">
                                    <?php echo Lang::txt('COM_COLLECTIONS_NUM_REPOSTS', $collection->count('reposts'));
                                        ?>
                                </span>*/ ?>
                                <span class="posts">
                                    <?php echo Lang::txt('COM_COLLECTIONS_NUM_POSTS', $collection->count('posts')); ?>
                                </span>
                            </p>
                            <?php if (!$no_html) { ?>
                            <div class="actions">
                                <?php if (!User::isGuest()) { ?>
                                    <?php
                                    if (
                                        $collection->get('object_type') == 'member'
                                        && $collection->get('object_id') == User::get('id')
                                    ) { ?>
                                            <a class="btn edit" data-id="<?= $collection->get('id') ?>"
                                                href="<?= Route::url($collection->link() . '/edit') ?>">
                                                <span><?php echo Lang::txt('JACTION_EDIT'); ?></span>
                                            </a>
                                            <a class="btn delete" data-id="<?= $collection->get('id') ?>"
                                                href="<?= Route::url($collection->link() . '/delete') ?>">
                                                <span><?php echo Lang::txt('JACTION_DELETE'); ?></span>
                                            </a>
                                    <?php } else { ?>
                                            <?php
                                            $collectUrl = Route::url(
                                                $base . '&controller=posts&board=' . $collection->get('id')
                                                    . '&task=collect',
                                            );
                                            ?>
                                            <a class="btn repost" data-id="<?= $collection->get('id') ?>"
                                                href="<?= $collectUrl ?>">
                                                <span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
                                            </a>
                                        <?php if ($collection->isFollowing()) { ?>
                                            <button type="submit" form="hz-post-action"
                                                formaction="<?= Route::url($collection->link() . '/unfollow') ?>"
                                                class="btn unfollow" data-id="<?= $collection->get('id') ?>"
                                                data-text-follow="<?= Lang::txt('COM_COLLECTIONS_FOLLOW') ?>"
                                                data-text-unfollow="<?= Lang::txt('COM_COLLECTIONS_UNFOLLOW') ?>">
                                                <span><?php echo Lang::txt('COM_COLLECTIONS_UNFOLLOW'); ?></span>
                                            </button>
                                        <?php } else { ?>
                                            <button type="submit" form="hz-post-action"
                                                formaction="<?= Route::url($collection->link() . '/follow') ?>"
                                                class="btn follow" data-id="<?= $collection->get('id') ?>"
                                                data-text-follow="<?= Lang::txt('COM_COLLECTIONS_FOLLOW') ?>"
                                                data-text-unfollow="<?= Lang::txt('COM_COLLECTIONS_UNFOLLOW') ?>">
                                                <span><?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?></span>
                                            </button>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } else { ?>
                                    <?php
                                    $collectUrl = Route::url(
                                        'index.php?option=com_users&view=login&return='
                                            . base64_encode(
                                                Route::url(
                                                    $base . '&controller=posts&board=' . $collection->get('id')
                                                        . '&task=collect',
                                                    false,
                                                    true,
                                                ),
                                            ),
                                        false,
                                    );
                                    $ariaLabel = Lang::txt('COM_COLLECTIONS_COLLECT') . ': '
                                        . $this->escape($collection->get('title'));
                                    ?>
                                    <a class="btn repost tooltips" href="<?= $collectUrl ?>"
                                        title="<?= Lang::txt('COM_COLLECTIONS_WARNING_LOGIN_TO_COLLECT') ?>"
                                        aria-label="<?= $ariaLabel ?>">
                                        <span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
                                    </a>
                                    <?php
                                    $linkUrl = Route::url(
                                        'index.php?option=com_users&view=login&return='
                                            . base64_encode(Route::url($collection->link(), false, true)),
                                        false,
                                    );
                                    $ariaLabel = Lang::txt('COM_COLLECTIONS_FOLLOW') . ': '
                                        . $this->escape($collection->get('title'));
                                    ?>
                                    <a class="btn follow tooltips" href="<?= $linkUrl ?>"
                                        title="<?= Lang::txt('COM_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW') ?>"
                                        aria-label="<?= $ariaLabel ?>">
                                        <span><?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?></span>
                                    </a>
                                <?php } ?>
                            </div><!-- / .actions -->
                            <?php } ?>
                        </div><!-- / .meta -->
                        <div class="convo attribution">
                            <?php
                            $name = $this->escape(stripslashes($collection->creator()->get('name')));

                            if (in_array($collection->creator()->get('access'), User::getAuthorisedViewLevels())) { ?>
                                <a href="<?= Route::url($collection->creator()->link() . '&active=collections') ?>"
                                    title="<?= $name ?>" class="img-link"
                                    aria-label="<?= Lang::txt('COM_COLLECTIONS_VIEW_COLLECTIONS_BY', $name) ?>">
                                    <img src="<?php echo $collection->creator()->picture(); ?>" alt="" />
                                </a>
                            <?php } else { ?>
                                <span class="img-link">
                                    <img src="<?= $collection->creator()->picture() ?>"
                                        alt="<?= Lang::txt('COM_COLLECTIONS_PROFILE_PICTURE', $name) ?>" />
                                </span>
                            <?php } ?>
                            <p>
                                <?php
                                if (
                                    in_array($collection->creator()->get('access'), User::getAuthorisedViewLevels())
                                ) { ?>
                                    <a href="<?= Route::url($collection->creator()->link() . '&active=collections') ?>"
                                        aria-label="<?= Lang::txt('COM_COLLECTIONS_VIEW_COLLECTIONS_BY', $name) ?>">
                                        <?php echo $name; ?>
                                    </a>
                                <?php } else { ?>
                                    <?php echo $name; ?>
                                <?php } ?>
                                <br />
                                <span class="entry-date">
                                    <span class="entry-date-at"><?php echo Lang::txt('COM_COLLECTIONS_AT'); ?></span>
                                    <?php
                                    $timeText = Date::of($collection->get('created'))->toLocal(
                                        Lang::txt('TIME_FORMAT_HZ1'),
                                    );
                                    ?>
                                    <span class="time"><?= $timeText ?></span>
                                    <span class="entry-date-on"><?php echo Lang::txt('COM_COLLECTIONS_ON'); ?></span>
                                    <?php
                                    $dateText = Date::of($collection->get('created'))->toLocal(
                                        Lang::txt('DATE_FORMAT_HZ1'),
                                    );
                                    ?>
                                    <span class="date"><?= $dateText ?></span>
                                </span>
                            </p>
                        </div><!-- / .attribution -->
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
<?php }
