<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base  = 'index.php?option=' . $this->option;
$mode  = Request::getWord('mode', 'grid');

if (!User::isGuest()) {
    $foo = $this->editor('description', '', 35, 5, 'field_description', array('class' => 'minimal no-footer'));
}

$this->css()
     ->js('jquery.masonry')
     ->js('jquery.infinitescroll')
     ->js();

$helpUrl = Route::url(
    'index.php?option=com_help&component='
    . substr($this->option, 4) . '&page=index'
);
$formUrl = Route::url(
    $base . '&controller=' . $this->controller
    . '&task=' . $this->task
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

<form method="get"
    action="<?php echo $formUrl; ?>"
    id="collections">
    <?php
    $this->view('_submenu')
         ->set('option', $this->option)
         ->set('active', 'collections')
         ->set('collections', $this->total)
         ->set('posts', $this->posts)
         ->display();
    ?>

    <section class="section filters">
        <fieldset class="input-group">
            <span class="input-cell">
                <label for="filter-search">
                    <span><?php echo Lang::txt('COM_COLLECTIONS_SEARCH_LABEL'); ?></span>
                    <?php
                    $searchVal = $this->escape($this->filters['search']);
                    $searchPlaceholder = Lang::txt(
                        'COM_COLLECTIONS_SEARCH_PLACEHOLDER'
                    );
                    ?>
                    <input type="text"
                        name="search"
                        id="filter-search"
                        value="<?php echo $searchVal; ?>"
                        placeholder="<?php echo $searchPlaceholder; ?>" />
                </label>
            </span>
            <span class="input-cell">
                <input type="submit"
                    class="btn"
                    value="<?php echo Lang::txt('COM_COLLECTIONS_GO'); ?>" />
            </span>
        </fieldset>
    </section>

    <section class="main section">
        <?php if ($this->rows->total() > 0) { ?>
            <?php
            $viewClass = $mode . ' '
                . (User::isGuest() ? 'loggedout' : 'loggedin');
            ?>
            <div id="posts"
                data-base="<?php echo Request::base(true); ?>"
                class="view-as <?php echo $viewClass; ?>">
                <?php if (!User::isGuest() && !Request::getInt('no_html', 0)) { ?>
                    <?php
                    $newCollUrl = Route::url(
                        'index.php?option=com_members&id='
                        . User::get('id') . '&active=collections&task=new'
                    );
                    ?>
                    <div class="post new-post">
                        <a class="icon-add add"
                            href="<?php echo $newCollUrl; ?>">
                            <?php echo Lang::txt('COM_COLLECTIONS_NEW_COLLECTION'); ?>
                        </a>
                    </div>
                <?php } ?>
                <?php foreach ($this->rows as $row) { ?>
                    <?php
                    $collCloseup = Route::url(
                        $base . '&controller=collection&id='
                        . $row->get('id')
                    );
                    ?>
                    <div class="post collection"
                        id="b<?php echo $row->get('id'); ?>"
                        data-id="<?php echo $row->get('id'); ?>"
                        data-closeup-url="<?php echo $collCloseup; ?>">
                        <div class="content">
                            <?php
                                $this->view('display_collection', 'posts')
                                     ->set('option', $this->option)
                                     ->set('params', $this->config)
                                     ->set('row', $row)
                                     ->display();
                            ?>
                            <?php if ($tags = $row->item()->tags('cloud')) { ?>
                                <div class="tags-wrap">
                                    <?php echo $tags; ?>
                                </div>
                            <?php } ?>
                            <div class="meta">
                                <p class="stats">
                                    <span class="likes">
                                        <?php echo Lang::txt('COM_COLLECTIONS_NUM_LIKES', $row->count('likes')); ?>
                                    </span>
                                    <?php /*<span class="reposts">
                                        <?php echo Lang::txt('COM_COLLECTIONS_NUM_REPOSTS', $row->count('reposts')); ?>
                                    </span>*/ ?>
                                    <span class="posts">
                                        <?php echo Lang::txt('COM_COLLECTIONS_NUM_POSTS', $row->count('posts')); ?>
                                    </span>
                                </p>
                                <div class="actions">
                                    <?php if (!User::isGuest()) { ?>
                                        <?php
                                        $isOwner = ($row->get('object_type') == 'member'
                                            && $row->get('object_id') == User::get('id'));
                                        $rowLink = $row->link();
                                        ?>
                                        <?php if ($isOwner) { ?>
                                                <?php
                                                $editUrl = Route::url($rowLink . '/edit');
                                                $deleteUrl = Route::url($rowLink . '/delete');
                                                ?>
                                                <a class="btn edit"
                                                    data-id="<?php echo $row->get('id'); ?>"
                                                    href="<?php echo $editUrl; ?>">
                                                    <span><?php echo Lang::txt('JACTION_EDIT'); ?></span>
                                                </a>
                                                <a class="btn delete"
                                                    data-id="<?php echo $row->get('id'); ?>"
                                                    href="<?php echo $deleteUrl; ?>">
                                                    <span><?php echo Lang::txt('JACTION_DELETE'); ?></span>
                                                </a>
                                        <?php } else { ?>
                                                <?php
                                                $repostUrl = Route::url(
                                                    $base . '&controller=posts&board='
                                                    . $row->get('id') . '&task=collect'
                                                );
                                                $followTxt = Lang::txt('COM_COLLECTIONS_FOLLOW');
                                                $unfollowTxt = Lang::txt('COM_COLLECTIONS_UNFOLLOW');
                                                ?>
                                                <a class="btn repost"
                                                    data-id="<?php echo $row->get('id'); ?>"
                                                    href="<?php echo $repostUrl; ?>">
                                                    <span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
                                                </a>
                                            <?php if ($row->isFollowing()) { ?>
                                                <?php $unfollowUrl = Route::url($rowLink . '/unfollow'); ?>
                                                <a class="btn unfollow"
                                                    data-id="<?php echo $row->get('id'); ?>"
                                                    data-text-follow="<?php echo $followTxt; ?>"
                                                    data-text-unfollow="<?php echo $unfollowTxt; ?>"
                                                    href="<?php echo $unfollowUrl; ?>">
                                                    <span><?php echo $unfollowTxt; ?></span>
                                                </a>
                                            <?php } else { ?>
                                                <?php $followUrl = Route::url($rowLink . '/follow'); ?>
                                                <a class="btn follow"
                                                    data-id="<?php echo $row->get('id'); ?>"
                                                    data-text-follow="<?php echo $followTxt; ?>"
                                                    data-text-unfollow="<?php echo $unfollowTxt; ?>"
                                                    href="<?php echo $followUrl; ?>">
                                                    <span><?php echo $followTxt; ?></span>
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <?php
                                        $loginReturn = base64_encode(
                                            Route::url($rowLink, false, true)
                                        );
                                        $loginRepostUrl = Route::url(
                                            'index.php?option=com_users&view=login&return='
                                            . $loginReturn,
                                            false
                                        );
                                        $loginFollowUrl = Route::url(
                                            'index.php?option=com_users&view=login&return='
                                            . $loginReturn,
                                            false
                                        );
                                        $collectTip = Lang::txt(
                                            'COM_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'
                                        );
                                        $followTip = Lang::txt(
                                            'COM_COLLECTIONS_WARNING_LOGIN_TO_FOLLOW'
                                        );
                                        ?>
                                        <a class="btn repost tooltips"
                                            href="<?php echo $loginRepostUrl; ?>"
                                            title="<?php echo $collectTip; ?>">
                                            <span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
                                        </a>
                                        <a class="btn follow tooltips"
                                            href="<?php echo $loginFollowUrl; ?>"
                                            title="<?php echo $followTip; ?>">
                                            <span><?php echo Lang::txt('COM_COLLECTIONS_FOLLOW'); ?></span>
                                        </a>
                                    <?php } ?>
                                </div><!-- / .actions -->
                            </div><!-- / .meta -->
                            <div class="convo attribution">
                                <?php
                                $name = $this->escape(
                                    stripslashes($row->creator()->get('name'))
                                );
                                $creatorAccess = in_array(
                                    $row->creator()->get('access'),
                                    User::getAuthorisedViewLevels()
                                );
                                $creatorLink = Route::url(
                                    $row->creator()->link()
                                    . '&active=collections'
                                );
                                $creatorPic = $row->creator()->picture();
                                $profileAlt = Lang::txt(
                                    'COM_COLLECTIONS_PROFILE_PICTURE',
                                    $name
                                );

                                if ($creatorAccess) { ?>
                                    <a href="<?php echo $creatorLink; ?>"
                                        title="<?php echo $name; ?>"
                                        class="img-link">
                                        <img src="<?php echo $creatorPic; ?>"
                                            alt="<?php echo $profileAlt; ?>" />
                                    </a>
                                <?php } else { ?>
                                    <span class="img-link">
                                        <img src="<?php echo $creatorPic; ?>"
                                            alt="<?php echo $profileAlt; ?>" />
                                    </span>
                                <?php } ?>
                                <p>
                                    <?php if ($creatorAccess) { ?>
                                        <a href="<?php echo $creatorLink; ?>">
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
                                            <time datetime="<?php echo $row->created(); ?>">
                                                <?php echo $row->created('time'); ?>
                                            </time>
                                        </span>
                                        <span class="entry-date-on">
                                            <?php echo Lang::txt('COM_COLLECTIONS_ON'); ?>
                                        </span>
                                        <span class="date">
                                            <time datetime="<?php echo $row->created(); ?>">
                                                <?php echo $row->created('date'); ?>
                                            </time>
                                        </span>
                                    </span>
                                </p>
                            </div><!-- / .attribution -->
                        </div><!-- / .content -->
                    </div><!-- / .post -->
                <?php } ?>
            </div>
            <?php
            if ($this->total > $this->filters['limit']) {
                // Initiate paging
                echo $this->pagination(
                    $this->total,
                    $this->filters['start'],
                    $this->filters['limit']
                );
            }
            ?>
            <div class="clear"></div>
        <?php } else { ?>
            <div id="collection-introduction">
                <?php if ($this->config->get('access-create-post')) { ?>
                    <div class="instructions">
                        <ol>
                            <li><?php echo Lang::txt('COM_COLLECTIONS_INSTRUCTIONS_STEP1'); ?></li>
                            <li><?php echo Lang::txt('COM_COLLECTIONS_INSTRUCTIONS_STEP2'); ?></li>
                            <li><?php echo Lang::txt('COM_COLLECTIONS_INSTRUCTIONS_STEP3'); ?></li>
                            <li><?php echo Lang::txt('COM_COLLECTIONS_INSTRUCTIONS_STEP4'); ?></li>
                        </ol>
                    </ul>
                <?php } else { ?>
                    <div class="instructions">
                        <p><?php echo Lang::txt('COM_COLLECTIONS_NO_COLLECTIONS_FOUND'); ?></p>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </section>
</form>
