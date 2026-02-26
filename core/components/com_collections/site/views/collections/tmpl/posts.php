<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$base  = 'index.php?option=' . $this->option;
$mode  = Request::getWord('mode', 'grid');

// This needs to be called to ensure scripts are pushed to the document
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
         ->set('active', 'posts')
         ->set('collections', $this->collections)
         ->set('posts', $this->total)
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
                    $newPostUrl = Route::url(
                        'index.php?option=com_members&id='
                        . User::get('id')
                        . '&active=collections&task=post/new'
                    );
                    ?>
                    <div class="post new-post">
                        <a class="icon-add add"
                            href="<?php echo $newPostUrl; ?>">
                            <?php echo Lang::txt('COM_COLLECTIONS_NEW_POST'); ?>
                        </a>
                    </div>
                <?php } ?>
                <?php
                foreach ($this->rows as $row) {
                    $item = $row->item();
                    $postCloseup = Route::url(
                        $base . '&controller=posts&post='
                        . $row->get('id')
                    );
                    ?>
                    <div class="post <?php echo $item->type(); ?>"
                        id="b<?php echo $row->get('id'); ?>"
                        data-id="<?php echo $row->get('id'); ?>"
                        data-closeup-url="<?php echo $postCloseup; ?>">
                        <div class="content">
                        <?php
                            $this->view('display_' . $item->type(), 'posts')
                                 ->set('option', $this->option)
                                 ->set('params', $this->config)
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
                                <div class="actions">
                                    <?php if (!User::isGuest()) { ?>
                                        <?php if ($row->get('created_by') == User::get('id')) { ?>
                                            <?php
                                            $editUrl = Route::url(
                                                $base . '&controller=posts&post='
                                                . $row->get('id') . '&task=edit'
                                            );
                                            ?>
                                            <a class="btn edit"
                                                data-id="<?php echo $row->get('id'); ?>"
                                                href="<?php echo $editUrl; ?>">
                                                <span><?php echo Lang::txt('JACTION_EDIT'); ?></span>
                                            </a>
                                        <?php } else { ?>
                                            <?php
                                            $voteUrl = Route::url(
                                                $base . '&controller=posts&post='
                                                . $row->get('id') . '&task=vote'
                                            );
                                            $voteClass = ($item->get('voted'))
                                                ? 'unlike' : 'like';
                                            $likeTxt = Lang::txt('COM_COLLECTIONS_LIKE');
                                            $unlikeTxt = Lang::txt('COM_COLLECTIONS_UNLIKE');
                                            $voteLabel = ($item->get('voted'))
                                                ? $unlikeTxt : $likeTxt;
                                            ?>
                                            <a class="btn vote <?php echo $voteClass; ?>"
                                                data-id="<?php echo $item->get('id'); ?>"
                                                data-text-like="<?php echo $likeTxt; ?>"
                                                data-text-unlike="<?php echo $unlikeTxt; ?>"
                                                href="<?php echo $voteUrl; ?>">
                                                <span><?php echo $voteLabel; ?></span>
                                            </a>
                                        <?php } ?>
                                        <?php
                                        // Display comment button only if enabled
                                        if ($this->config->get('allow_comments')) :
                                            $commentUrl = Route::url(
                                                $base . '&controller=posts&post='
                                                . $row->get('id') . '&task=comment'
                                            );
                                            ?>
                                            <a class="btn comment"
                                                data-id="<?php echo $row->get('id'); ?>"
                                                href="<?php echo $commentUrl; ?>">
                                                <span><?php echo Lang::txt('COM_COLLECTIONS_COMMENT'); ?></span>
                                            </a>
                                        <?php endif; ?>
                                            <?php
                                            $repostUrl = Route::url(
                                                $base . '&controller=posts&post='
                                                . $row->get('id') . '&task=collect'
                                            );
                                            ?>
                                            <a class="btn repost"
                                                data-id="<?php echo $row->get('id'); ?>"
                                                href="<?php echo $repostUrl; ?>">
                                                <span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
                                            </a>
                                    <?php } else { ?>
                                            <?php
                                            $loginReturn = base64_encode(Route::url(
                                                $base . '&controller=' . $this->controller
                                                . '&task=' . $this->task,
                                                false,
                                                true
                                            ));
                                            $loginUrl = Route::url(
                                                'index.php?option=com_users&view=login&return='
                                                . $loginReturn,
                                                false
                                            );
                                            $likeTip = Lang::txt(
                                                'COM_COLLECTIONS_WARNING_LOGIN_TO_LIKE'
                                            );
                                            $collectTip = Lang::txt(
                                                'COM_COLLECTIONS_WARNING_LOGIN_TO_COLLECT'
                                            );
                                            $commentUrl = Route::url(
                                                $base . '&controller=posts&post='
                                                . $row->get('id') . '&task=comment'
                                            );
                                            ?>
                                            <a class="btn vote like tooltips"
                                                href="<?php echo $loginUrl; ?>"
                                                title="<?php echo $likeTip; ?>">
                                                <span><?php echo Lang::txt('COM_COLLECTIONS_LIKE'); ?></span>
                                            </a>
                                            <?php
                                            // Display comment button only if enabled
                                            if ($this->config->get('allow_comments')) :
                                                ?>
                                            <a class="btn comment"
                                                data-id="<?php echo $row->get('id'); ?>"
                                                href="<?php echo $commentUrl; ?>">
                                                <span><?php echo Lang::txt('COM_COLLECTIONS_COMMENT'); ?></span>
                                            </a>
                                            <?php endif; ?>
                                            <a class="btn repost tooltips"
                                                href="<?php echo $loginUrl; ?>"
                                                title="<?php echo $collectTip; ?>">
                                                <span><?php echo Lang::txt('COM_COLLECTIONS_COLLECT'); ?></span>
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
                                    <?php
                                    $who = $name;
                                    if ($creatorAccess) {
                                        $who = '<a href="' . $creatorLink . '">'
                                            . $name . '</a>';
                                    }

                                    $rowLink = Route::url($row->link());
                                    $rowTitle = $this->escape(
                                        stripslashes($row->get('title'))
                                    );
                                    $where = '<a href="' . $rowLink . '">'
                                        . $rowTitle . '</a>';

                                    echo Lang::txt(
                                        'COM_COLLECTIONS_ONTO',
                                        $who,
                                        $where
                                    );
                                    ?>
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
            </div><!-- / #posts -->
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
                    </div>
                <?php } else { ?>
                    <div class="instructions">
                        <p><?php echo Lang::txt('COM_COLLECTIONS_NO_POSTS_FOUND'); ?></p>
                    </div>
                <?php } ?>
            </div><!-- / #collections-introduction -->
        <?php } ?>
    </section><!-- / .main section -->
</form>
