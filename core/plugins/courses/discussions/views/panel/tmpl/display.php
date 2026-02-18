<?php

// @phpcs:disable PSR1.Files.SideEffects
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->js();

$base = $this->offering->link() . '&active=discussions';

$instructors = array();

$inst = $this->course->instructors();
if (count($inst) > 0) {
    foreach ($inst as $i) {
        $instructors[] = $i->get('user_id');
    }
}
?>
<?php if (!$this->course->offering()->section()->access('view')) : ?>
    <?php
        $view = new \Hubzero\Plugin\View(array(
            'folder'  => 'courses',
            'element' => 'outline',
            'name'    => 'shared',
            'layout'  => '_not_enrolled'
        ));

        $view->set('course', $this->course)
             ->set('option', $this->option)
             ->set('message', 'You must be enrolled to utilize the discussion feature.')
             ->display();

        return;
    ?>
<?php endif; ?>
<?php if ($this->course->access('manage', 'offering')) { ?>
    <div id="manager-options">
        <?php $manageUrl = Route::url($base . '&unit=manage'); ?>
        <p>
            <a class="btn" href="<?php echo $manageUrl; ?>">
                <?php echo Lang::txt('Manage'); ?>
            </a>
        </p>
    </div>
<?php } ?>
<div id="comments-container">
    <div class="comments-wrap">
        <div class="comments-views">

            <div class="comments-feed">
                <div class="comments-toolbar cf">
                    <p class="comment-sort-options">
                        <?php echo Lang::txt('%s Discussions', $this->stats->threads); ?>
                    </p>
                    <p class="comments-controls">
                        <a class="add active"
                            href="<?php echo Route::url($base); ?>"
                            title="<?php echo Lang::txt('Start a new discussion'); ?>"
                            ><?php echo Lang::txt('New'); ?></a>
                    </p>
                </div><!-- / .comments-toolbar -->

                <div class="comments-options-bar">
                    <form class="comments-search" action="<?php echo Route::url($base); ?>" method="get">
                        <fieldset>
                            <input type="text"
                                name="search"
                                class="search"
                                value="<?php echo $this->escape($this->filters['search']); ?>"
                                placeholder="<?php echo Lang::txt('search ...'); ?>"/>
                            <input type="submit" class="submit" value="<?php echo Lang::txt('Go'); ?>" />

                            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                            <input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
                            <input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
                            <input type="hidden" name="active" value="discussions" />
                            <input type="hidden" name="action" value="search" />
                        </fieldset>
                    </form>
                </div><!-- / .comments-options-bar -->

                <div class="comment-threads">
                    <div class="category search-results hide">
                        <div class="category-header">
                            <span class="category-title"><?php echo Lang::txt('Search'); ?></span>
                        </div>
                        <div class="category-content">
                        </div>
                    </div>

                    <div class="category category-results" id="ctmine">
                        <?php
                        $posts = \Components\Forum\Models\Post::all()
                            ->whereEquals('scope', $this->filters['scope'])
                            ->whereEquals('scope_id', $this->filters['scope_id'])
                            ->whereEquals('state', \Components\Forum\Models\Post::STATE_PUBLISHED)
                            ->whereEquals('created_by', User::get('id'));

                        if ($this->config->get('discussions_threads', 'all') != 'all') {
                            $posts->whereEquals('scope_sub_id', $this->filters['scope_sub_id']);
                        }

                        $threads = $posts
                            ->order('created', 'desc')
                            ->limit(100)
                            ->rows();
                        ?>
                        <div class="category-header">
                            <span class="category-title"><?php echo Lang::txt('Mine'); ?></span>
                            <span class="category-discussions count"><?php echo $threads->count(); ?></span>
                        </div><!-- / .category-header -->
                        <div class="category-content">
                            <?php
                            $this->view('_threads', 'threads')
                                 ->set('category', 'categorymine')
                                 ->set('option', $this->option)
                                 ->set('threads', $threads)
                                 ->set('unit', '')
                                 ->set('lecture', 0)
                                 ->set('config', $this->config)
                                 ->set('instructors', $instructors)
                                 ->set('cls', 'odd')
                                 ->set('base', $base)
                                 ->set('course', $this->course)
                                 ->set('prfx', 'mine')
                                 ->set('active', $this->thread)
                                 ->display();
                            ?>
                        </div><!-- / .category-content -->
                    </div><!-- / .category -->
                    <?php if (count($this->sections) > 0) { ?>
                        <?php
                            $threads = array();

                            $posts = \Components\Forum\Models\Post::all()
                                ->whereEquals('scope', $this->filters['scope'])
                                ->whereEquals('scope_id', $this->filters['scope_id'])
                                ->whereEquals('state', \Components\Forum\Models\Post::STATE_PUBLISHED)
                                ->whereEquals('parent', 0);

                        if ($this->config->get('discussions_threads', 'all') != 'all') {
                            $posts->whereEquals('scope_sub_id', $this->filters['scope_sub_id']);
                        }

                            $results = $posts
                                ->order('created', 'desc')
                                ->limit(100 * count($this->sections))
                                ->rows();
                        if ($results->count()) {
                            foreach ($results as $thread) {
                                if (!isset($threads[$thread->get('category_id')])) {
                                    $threads[$thread->get('category_id')] = array();
                                }
                                $threads[$thread->get('category_id')][] = $thread;
                            }
                        }
                        ?>
                        <?php foreach ($this->sections as $section) { ?>
                                <?php $sectionId = $section->get('id'); ?>
                                <?php $sectionTitle = $this->escape(stripslashes($section->get('title'))); ?>
                                <?php $sectionThreads = $section->get('threads'); ?>
                                <div class="category category-results closed"
                                    id="sc<?php echo $sectionId; ?>">
                                    <div class="category-header">
                                        <span class="category-title">
                                            <?php echo $sectionTitle; ?>
                                        </span>
                                        <span class="category-discussions count">
                                            <?php echo $sectionThreads; ?>
                                        </span>
                                    </div><!-- / .category-header -->
                                    <div class="category-content">
                                    <?php
                                    if ($section->categories) {
                                        foreach ($section->categories as $row) {
                                            ?>
                                            <div class="thread closed"
                                                id="ct<?php echo $row->get('id'); ?>"
                                                data-category="<?php echo $row->get('id'); ?>">
                                                <?php $rowTitle = $this->escape(stripslashes($row->get('title'))); ?>
                                                <?php $rowThreads = $row->get('threads'); ?>
                                                <div class="thread-header">
                                                    <span class="thread-title">
                                                        <?php echo $rowTitle; ?>
                                                    </span>
                                                    <span class="thread-discussions count">
                                                        <?php echo $rowThreads; ?>
                                                    </span>
                                                </div><!-- / .thread-header -->
                                                <div class="thread-content">
                                                    <?php
                                                        $this->view('_threads', 'threads')
                                                             ->set('category', 'category' . $row->get('id'))
                                                             ->set('option', $this->option)
                                                             ->set('threads', isset($threads[$row->get('id')]) ?
                                                             $threads[$row->get('id')] : null)
                                                             ->set('unit', $row->get('alias'))
                                                             ->set('lecture', $row->get('id'))
                                                             ->set('config', $this->config)
                                                             ->set('instructors', $instructors)
                                                             ->set('cls', 'odd')
                                                             ->set('base', $base)
                                                             ->set('course', $this->course)
                                                             ->set('active', $this->thread)
                                                             ->display();
                                                    ?>
                                                </div><!-- / .thread-content -->
                                            </div><!-- / .thread -->
                                            <?php
                                        }
                                        ?>
                                    <?php } else { ?>
                                        <p class="instructions">
                                            <?php echo Lang::txt('There are no categories for this section.'); ?>
                                        </p>
                                    <?php } ?>
                                    </div><!-- / .category-content -->
                                </div><!-- / .category -->
                        <?php } ?>
                    <?php } ?>
                </div><!-- / .comment-threads -->

            </div><!-- / .comments-feed -->

            <div class="comments-panel">
                <div class="comments-toolbar">
                    <?php $startDiscussion = Lang::txt('Start a discussion'); ?>
                    <p>
                        <span class="comments"
                            data-comments="%s comments"
                            data-add="<?php echo $startDiscussion; ?>">
                            <?php echo $startDiscussion; ?>
                        </span>
                    </p>
                </div><!-- / .comments-toolbar -->
                <div class="comments-frame">

                    <?php
                    $c = 0;
                    foreach ($this->sections as $section) {
                        if ($section->categories) {
                            $c++;
                        }
                    }
                    if ($c) {
                        ?>

                    <form action="<?php echo Route::url($base); ?>"
                        method="post"
                        id="commentform"<?php if ($this->data) {
                            echo ' class="hide"';
                                        } ?> enctype="multipart/form-data">
                        <p class="comment-member-photo">
                            <?php
                            $anon = 1;
                            if (!User::isGuest()) {
                                $anon = 0;
                            }
                            $now = Date::getRoot();
                            ?>
                            <img src="<?php echo User::picture($anon); ?>"
                                alt="<?php echo Lang::txt('User photo'); ?>"/>
                        </p>

                        <fieldset>
                        <?php if (User::isGuest()) { ?>
                            <p class="warning">
                                <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_LOGIN_COMMENT_NOTICE'); ?>
                            </p>
                        <?php } else { ?>
                            <p class="comment-title">
                                <strong>
                                    <?php
                                    $memberUrl = Route::url(
                                        'index.php?option=com_members&id=' . User::get('id')
                                    );
                                    ?>
                                    <?php $memberName = $this->escape(User::get('name')); ?>
                                    <a href="<?php echo $memberUrl; ?>">
                                        <?php echo $memberName; ?>
                                    </a>
                                </strong>
                                <span class="permalink">
                                    <span class="comment-date-at">@</span>
                                    <?php $timeFormatted = Date::of($now)->toLocal(Lang::txt('TIME_FORMAt_HZ1')); ?>
                                    <?php $dateFormatted = Date::of($now)->toLocal(Lang::txt('DATE_FORMAt_HZ1')); ?>
                                    <span class="time">
                                        <time datetime="<?php echo $now; ?>">
                                            <?php echo $timeFormatted; ?>
                                        </time>
                                    </span>
                                    <span class="comment-date-on">
                                        <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ON'); ?>
                                    </span>
                                    <span class="date">
                                        <time datetime="<?php echo $now; ?>">
                                            <?php echo $dateFormatted; ?>
                                        </time>
                                    </span>
                                </span>
                            </p>

                            <label for="field_comment">
                                <span class="label-text">
                                    <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_COMMENTS'); ?>
                                </span>
                                <?php
                                echo $this->editor('fields[comment]', '', 35, 5, 'field_comment', array('class' =>
                                'minimal no-footer'));
                                ?>
                            </label>

                            <div class="grid">
                                <div class="col span-half">
                            <label for="field-upload" id="comment-upload">
                                <span class="label-text">
                                    <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_LEGEND_ATTACHMENTS'); ?>:
                                </span>
                                <input type="file" name="upload" id="field-upload" />
                            </label>
                                </div>
                                <div class="col span-half omega">
                                    <label for="field-category_id">
                                    <span class="label-text">
                                        <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_CATEGORY'); ?>
                                    </span>
                                    <select name="fields[category_id]" id="field-category_id">
                                        <?php
                                        $selectLabel = Lang::txt(
                                            'PLG_COURSES_DISCUSSIONS_FIELD_CATEGORY_SELECT'
                                        );
                                        ?>
                                        <option value="0">
                                            <?php echo $selectLabel; ?>
                                        </option>
                                        <?php
                                        foreach ($this->sections as $section) {
                                            if ($section->categories) {
                                                ?>
                                                <?php
                                                $optLabel = $this->escape(
                                                    stripslashes($section->get('title'))
                                                );
                                                ?>
                                                <optgroup label="<?php echo $optLabel; ?>">
                                                <?php
                                                foreach ($section->categories as $category) {
                                                    if ($category->get('closed')) {
                                                        continue;
                                                    }
                                                    ?>
                                                    <?php $catId = $category->get('id'); ?>
                                                    <?php
                                                    $catTitle = $this->escape(
                                                        stripslashes($category->get('title'))
                                                    );
                                                    ?>
                                                    <option value="<?php echo $catId; ?>">
                                                        <?php echo $catTitle; ?>
                                                    </option>
                                                    <?php
                                                }
                                                ?>
                                                </optgroup>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </label>
                                </div>
                            </div>

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
                        <input type="hidden" name="fields[parent]" id="field-parent" value="0" />
                        <input type="hidden" name="fields[state]" id="field-state" value="1" />
                        <input type="hidden" name="fields[scope]" id="field-scope" value="course" />
                        <input type="hidden"
                            name="fields[scope_id]"
                            id="field-scope_id"
                            value="<?php echo $this->filters['scope_id']; ?>"/>
                        <input type="hidden"
                            name="fields[scope_sub_id]"
                            id="field-scope_sub_id"
                            value="<?php echo $this->filters['scope_sub_id']; ?>"/>
                        <input type="hidden" name="fields[id]" id="field-id" value="" />
                        <input type="hidden" name="fields[object_id]" id="field-object_id" value="" />

                        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                        <input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
                        <input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
                        <input type="hidden" name="active" value="discussions" />
                        <input type="hidden" name="action" value="savethread" />

                        <?php echo Html::input('token'); ?>

                        <p class="instructions">
                            <?php
                            $clickSection = Lang::txt(
                                'Click on a section and category to the left to view a list of comments.'
                            );
                            $clickComment = Lang::txt(
                                'Click on a comment on the left to view a discussion or start your own above.'
                            );
                            ?>
                            <?php echo $clickSection; ?><br /><br />
                            <?php echo $clickComment; ?>
                        </p>
                    </form>
                    <?php } else { ?>
                        <p class="instructions">
                            <?php
                            $emptyMsg = Lang::txt(
                                'This forum is currently empty and requires some set-up'
                                . ' by the course managers before it can be used.'
                            );
                            echo $emptyMsg;
                            ?>
                            <?php if ($this->course->access('manage', 'offering')) { ?>
                                <?php
                                $manageMsg = Lang::txt(
                                    'Discussions require at least one section and category before posts can be made.'
                                    . ' Click the "manage" button to set up this forum.'
                                );
                                ?>
                                <br /><br /><?php echo $manageMsg; ?>
                            <?php } ?>
                        </p>
                    <?php } ?>
                    <div class="comment-thread"><?php if ($this->data) {
                        echo $this->data->html;
                                                } ?></div>
                </div><!-- / .comments-frame -->
            </div><!-- / .comments-panel -->

        </div><!-- / .comments-views -->
    </div><!-- / .comments-wrap -->

</div><!-- / #comments-container -->
