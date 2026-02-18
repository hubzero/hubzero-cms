<?php

// @phpcs:disable PSR1.Files.SideEffects
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js('discussions.lecture.js');

$base = $this->course->offering()->link();
$outlineUrl = Route::url(
    $base . '&active=outline&unit='
    . $this->unit->get('alias') . '&b='
    . $this->lecture->get('alias')
);
?>
<div id="comments-container"
    data-action="<?php echo $outlineUrl; ?>">

    <div class="comments-wrap">

        <div class="comments-views">
            <div class="comments-feed">
                <div class="comments-toolbar cf">
                    <p class="comment-sort-options">
                        <?php
                        $countText = Lang::txt(
                            '%s Discussions',
                            count($this->threads)
                        );
                        echo $countText;
                        ?>
                    </p>
                    <p class="comments-controls">
                        <?php
                        $addActive = '';
                        if (!$this->thread) {
                            $addActive = ' active';
                        }
                        $newText = Lang::txt('New');
                        $startTitle = Lang::txt(
                            'Start a new discussion'
                        );
                        ?>
                        <a class="add<?php echo $addActive; ?>"
                            href="<?php echo $outlineUrl; ?>"
                            title="<?php echo $startTitle; ?>"
                            ><?php echo $newText; ?></a>
                    </p>
                </div><!-- / .comments-toolbar -->

                <div class="comments-options-bar">
                    <form class="comments-search"
                        action="<?php echo $outlineUrl; ?>"
                        method="get">
                        <fieldset>
                            <input type="text"
                                name="search"
                                class="search"
                                value="<?php echo $this->escape($this->filters['search']); ?>"
                                placeholder="<?php echo Lang::txt('search ...'); ?>"/>
                            <input type="submit" class="submit" value="<?php echo Lang::txt('Go'); ?>" />
                            <input type="hidden" name="action" value="search" />
                        </fieldset>
                    </form>
                </div><!-- / .comments-options-bar -->

                <div class="comment-threads">
                    <div class="category search-results hide">
                        <div class="category-header">
                            <?php
                            $searchTitle = Lang::txt(
                                'PLG_COURSES_DISCUSSIONS_SEARCH'
                            );
                            ?>
                            <span class="category-title">
                                <?php echo $searchTitle; ?>
                            </span>
                        </div>
                        <div class="category-content">
                        </div>
                    </div>
                    <div class="category category-results">
                        <div class="category-content">
                            <?php
                            $threads_lastchange = '0000-00-00 00:00:00';
                            if ($this->threads) {
                                $threads_lastchange = $this->threads[0]->get('created');
                                $category = $this->threads[0]->get('category_id');
                            }

                            $instructors = array();
                            $inst = $this->course->instructors();
                            if (count($inst) > 0) {
                                foreach ($inst as $i) {
                                    $instructors[] = $i->get('user_id');
                                }
                            }
                            $this->view('_threads')
                                ->set('category', 'category' . $this->post->get('category_id'))
                                ->set('option', $this->option)
                                ->set('threads', $this->threads)
                                ->set('unit', $this->unit->get('alias'))
                                ->set('lecture', $this->lecture->get('alias'))
                                ->set('config', $this->config)
                                ->set('cls', 'odd')
                                ->set('instructors', $instructors)
                                ->set('base', $base . '&active=outline')
                                ->set('course', $this->course)
                                ->set('search', $this->filters['search'])
                                ->set('active', $this->thread)
                                ->display();
                            ?>
                            <input type="hidden"
                                name="threads_lastchange"
                                id="threads_lastchange"
                                value="<?php echo $threads_lastchange; ?>"/>
                        </div>
                    </div>
                </div><!-- / .comment-threads -->
            </div><!-- / .comments-feed -->

            <div class="comments-panel">
                <div class="comments-toolbar">
                    <?php
                    $commentsData = Lang::txt('%s comments');
                    $addData = Lang::txt('Start a discussion');
                    ?>
                    <p><span class="comments"
                        data-comments="<?php echo $commentsData; ?>"
                        data-add="<?php echo $addData; ?>"
                        ><?php echo $addData; ?></span></p>
                </div>
                <div class="comments-frame">

                    <?php
                    $formClass = '';
                    if ($this->data) {
                        $formClass = ' class="hide"';
                    }
                    ?>
                    <form action="<?php echo $outlineUrl; ?>"
                        method="post"
                        id="commentform"
                        <?php echo $formClass; ?>
                        enctype="multipart/form-data">

                        <p class="comment-member-photo">
                            <a class="comment-anchor" name="commentform"></a>
                            <?php
                            $anone = 1;
                            if (!User::isGuest()) {
                                $anon = 0;
                            }
                            ?>
                            <img src="<?php echo User::picture($anon); ?>"
                                alt="<?php echo Lang::txt('User photo'); ?>"/>
                        </p>

                        <fieldset>
                        <?php if (User::isGuest()) { ?>
                            <?php
                            $loginNotice = Lang::txt(
                                'PLG_COURSES_DISCUSSIONS_LOGIN_COMMENT_NOTICE'
                            );
                            ?>
                            <p class="warning">
                                <?php echo $loginNotice; ?>
                            </p>
                        <?php } else { ?>
                            <p class="comment-title">
                                <strong>
                                    <?php
                                    $memberUrl = Route::url(
                                        'index.php?option=com_members&id='
                                        . User::get('id')
                                    );
                                    $memberName = $this->escape(
                                        User::get('name')
                                    );
                                    ?>
                                    <a href="<?php echo $memberUrl; ?>"
                                        ><?php echo $memberName; ?></a>
                                </strong>
                                <span class="permalink">
                                    <span class="comment-date-at">@</span>
                                    <?php
                                    $nowSql = Date::of('now')->toSql();
                                    $timeFmt = Date::of('now')->toLocal(
                                        Lang::txt('TIME_FORMAt_HZ1')
                                    );
                                    $dateFmt = Date::of('now')->toLocal(
                                        Lang::txt('DATE_FORMAt_HZ1')
                                    );
                                    $onText = Lang::txt(
                                        'PLG_COURSES_DISCUSSIONS_ON'
                                    );
                                    ?>
                                    <span class="time">
                                        <time datetime="<?php echo $nowSql; ?>">
                                            <?php echo $timeFmt; ?>
                                        </time>
                                    </span>
                                    <span class="comment-date-on">
                                        <?php echo $onText; ?>
                                    </span>
                                    <span class="date">
                                        <time datetime="<?php echo $nowSql; ?>">
                                            <?php echo $dateFmt; ?>
                                        </time>
                                    </span>
                                </span>
                            </p>

                            <label for="field_comment">
                                <?php
                                $commentsLabel = Lang::txt(
                                    'PLG_COURSES_DISCUSSIONS_FIELD_COMMENTS'
                                );
                                ?>
                                <span class="label-text">
                                    <?php echo $commentsLabel; ?>
                                </span>
                                <?php
                                echo $this->editor('fields[comment]', '', 35, 5, 'field_comment', array('class' =>
                                'minimal no-footer'));
                                ?>
                            </label>

                            <label for="field-upload" id="comment-upload">
                                <?php
                                $attachLabel = Lang::txt(
                                    'PLG_COURSES_DISCUSSIONS_LEGEND_ATTACHMENTS'
                                );
                                ?>
                                <span class="label-text">
                                    <?php echo $attachLabel; ?>:
                                </span>
                                <input type="file" name="upload" id="field-upload" />
                            </label>

                            <label for="field-anonymous" id="comment-anonymous-label">
                                <input class="option"
                                    type="checkbox"
                                    name="fields[anonymous]"
                                    id="field-anonymous"
                                    value="1"/>
                                <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_ANONYMOUS'); ?>
                            </label>

                            <p class="submit">
                                <input type="submit"
                                    value="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_SUBMIT'); ?>"/>
                            </p>
                        <?php } ?>
                        </fieldset>
                        <input type="hidden"
                            name="fields[category_id]"
                            id="field-category_id"
                            value="<?php echo $this->post->get('category_id'); ?>"/>
                        <input type="hidden" name="fields[parent]" id="field-parent" value="0" />
                        <input type="hidden" name="fields[state]" id="field-state" value="1" />
                        <input type="hidden"
                            name="fields[scope]"
                            id="field-scope"
                            value="<?php echo $this->post->get('scope'); ?>"/>
                        <input type="hidden"
                            name="fields[scope_id]"
                            id="field-scope_id"
                            value="<?php echo $this->post->get('scope_id'); ?>"/>
                        <input type="hidden"
                            name="fields[scope_sub_id]"
                            id="field-scope_sub_id"
                            value="<?php echo $this->post->get('scope_sub_id'); ?>"/>
                        <input type="hidden" name="fields[id]" id="field-id" value="" />
                        <input type="hidden"
                            name="fields[object_id]"
                            id="field-object_id"
                            value="<?php echo $this->post->get('object_id'); ?>"/>

                        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                        <input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
                        <input type="hidden"
                            name="offering"
                            value="<?php echo $this->course->offering()->alias(); ?>"/>
                        <input type="hidden" name="active" value="discussions" />
                        <input type="hidden" name="action" value="savethread" />
                        <input type="hidden"
                            name="section"
                            value="<?php echo $this->filters['section']; ?>"/>
                        <?php
                        $returnUrl = base64_encode(
                            Route::url(
                                $base . '&active=outline&unit='
                                . $this->unit->get('alias') . '&b='
                                . $this->lecture->get('alias')
                            )
                        );
                        ?>
                        <input type="hidden"
                            name="return"
                            value="<?php echo $returnUrl; ?>"/>

                        <?php echo Html::input('token'); ?>

                        <p class="instructions">
                            <?php
                            echo Lang::txt(
                                'Click on a comment on the left to view'
                                . ' a discussion or start your own above.'
                            );
                            ?>
                        </p>
                    </form>

                    <div class="comment-thread"><?php if ($this->data) {
                        echo $this->data->html;
                                                } ?></div>

                </div><!-- / .comments-frame -->
            </div><!-- / .comments-panel -->
        </div><!-- / .comments-views -->

     </div><!-- / .comments-wrap -->
</div><!-- / #comments-container -->
