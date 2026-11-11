<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

defined('_HZEXEC_') or die();

$this->js();

$base = $this->offering->link() . '&active=discussions';

$instructors = [];

$inst = $this->course->instructors();
if (count($inst) > 0) {
    foreach ($inst as $i) {
        $instructors[] = $i->get('user_id');
    }
}
?>
<?php if (!$this->course->offering()->section()->access('view')) : ?>
    <?php
        $view = new \Hubzero\Plugin\View([
            'folder'  => 'courses',
            'element' => 'outline',
            'name'    => 'shared',
            'layout'  => '_not_enrolled',
        ]);

        $view
             ->set('course', $this->course)
             ->set('option', $this->option)
             ->set('message', 'You must be enrolled to utilize the discussion feature.')
             ->display();

        return;
    ?>
<?php endif; ?>
<?php if ($this->course->access('manage', 'offering')) { ?>
    <div id="manager-options">
        <p><a class="btn" href="<?= Route::url($base . '&unit=manage') ?>"><?= Lang::txt('Manage') ?></a></p>
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
                        <a class="add active" href="<?= Route::url($base) ?>"
                            title="<?= Lang::txt('Start a new discussion') ?>"><?= Lang::txt('New') ?></a>
                    </p>
                </div><!-- / .comments-toolbar -->

                <div class="comments-options-bar">
                    <form class="comments-search" action="<?php echo Route::url($base); ?>" method="get">
                        <fieldset>
                            <input type="text" name="search" class="search"
                                value="<?= $this->escape($this->filters['search']) ?>"
                                placeholder="<?= Lang::txt('search ...') ?>" />
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
                            $this
                                 ->view('_threads', 'threads')
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
                            $threads = [];

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
                                    $threads[$thread->get('category_id')] = [];
                                }
                                $threads[$thread->get('category_id')][] = $thread;
                            }
                        }
                        ?>
                        <?php foreach ($this->sections as $section) { ?>
                                <div class="category category-results closed" id="sc<?php echo $section->get('id'); ?>">
                                    <div class="category-header">
                                        <span class="category-title"><?= $this->escape(stripslashes($section->get('title'))) ?></span>
                                        <span class="category-discussions count"><?= $section->get('threads') ?></span>
                                    </div><!-- / .category-header -->
                                    <div class="category-content">
                                    <?php
                                    if ($section->categories) {
                                        foreach ($section->categories as $row) {
                                            ?>
                                            <div class="thread closed" id="ct<?= $row->get('id') ?>"
                                                data-category="<?= $row->get('id') ?>">
                                                <div class="thread-header">
                                                    <span class="thread-title"><?= $this->escape(stripslashes($row->get('title'))) ?></span>
                                                    <span class="thread-discussions count"><?= $row->get('threads') ?></span>
                                                </div><!-- / .thread-header -->
                                                <div class="thread-content">
                                                    <?php
                                                        $this
                                                             ->view('_threads', 'threads')
                                                             ->set('category', 'category' . $row->get('id'))
                                                             ->set('option', $this->option)
                                                             ->set(
                                                                 'threads',
                                                                 isset($threads[$row->get('id')])
                                                                     ? $threads[$row->get('id')]
                                                                     : null,
                                                             )
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
                    <p><span class="comments" data-comments="%s comments" data-add="<?= Lang::txt('Start a discussion') ?>"><?= Lang::txt('Start a discussion') ?></span></p>
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

                    <form action="<?= Route::url($base) ?>" method="post"
                        id="commentform"<?= $this->data ? ' class="hide"' : '' ?> enctype="multipart/form-data">
                        <p class="comment-member-photo">
                            <?php
                            $anon = 1;
                            if (!User::isGuest()) {
                                $anon = 0;
                            }
                            $now = Date::getRoot();
                            ?>
                            <img src="<?= User::picture($anon) ?>" alt="<?= Lang::txt('User photo') ?>" />
                        </p>

                        <fieldset>
                        <?php if (User::isGuest()) { ?>
                            <p class="warning"><?= Lang::txt('PLG_COURSES_DISCUSSIONS_LOGIN_COMMENT_NOTICE') ?></p>
                        <?php } else { ?>
                            <p class="comment-title">
                                <strong>
                                    <?php
                                    $linkUrl = Route::url('index.php?option=com_members&id=' . User::get('id'));
                                    ?>
                                    <a href="<?= $linkUrl ?>"><?= $this->escape(User::get('name')) ?></a>
                                </strong>
                                <span class="permalink">
                                    <span class="comment-date-at">@</span>
                                    <span class="time"><time datetime="<?= $now ?>"><?= Date::of($now)->toLocal(Lang::txt('TIME_FORMAt_HZ1')) ?></time></span>
                                    <span class="comment-date-on"><?= Lang::txt('PLG_COURSES_DISCUSSIONS_ON') ?></span>
                                    <span class="date"><time datetime="<?= $now ?>"><?= Date::of($now)->toLocal(Lang::txt('DATE_FORMAt_HZ1')) ?></time></span>
                                </span>
                            </p>

                            <label for="field_comment">
                                <span
                                    class="label-text"><?= Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_COMMENTS') ?></span>
                                <?php
                                echo $this->editor(
                                    'fields[comment]',
                                    '',
                                    35,
                                    5,
                                    'field_comment',
                                    ['class' => 'minimal no-footer'],
                                );
                                ?>
                            </label>

                            <div class="grid">
                                <div class="col span-half">
                            <label for="field-upload" id="comment-upload">
                                <span class="label-text"><?= Lang::txt('PLG_COURSES_DISCUSSIONS_LEGEND_ATTACHMENTS') ?>:</span>
                                <input type="file" name="upload" id="field-upload" />
                            </label>
                                </div>
                                <div class="col span-half omega">
                                    <label for="field-category_id">
                                    <span class="label-text"><?= Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_CATEGORY') ?></span>
                                    <select name="fields[category_id]" id="field-category_id">
                                        <option value="0">
                                            <?= Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_CATEGORY_SELECT') ?>
                                        </option>
                                        <?php
                                        foreach ($this->sections as $section) {
                                            if ($section->categories) {
                                                ?>
                                                <optgroup
                                                    label="<?= $this->escape(stripslashes($section->get('title'))) ?>">
                                                <?php
                                                foreach ($section->categories as $category) {
                                                    if ($category->get('closed')) {
                                                        continue;
                                                    }
                                                    ?>
                                                    <option value="<?= $category->get('id') ?>">
                                                        <?= $this->escape(stripslashes($category->get('title'))) ?>
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

                            <label for="field-anonymous" class="comment-anonymous-label">
                                <input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous"
                                    value="1" />
                                <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_ANONYMOUS'); ?>
                            </label>

                            <p class="submit">
                                <input type="submit" value="<?= Lang::txt('PLG_COURSES_DISCUSSIONS_SUBMIT') ?>" />
                            </p>
                        <?php } ?>
                        </fieldset>
                        <input type="hidden" name="fields[parent]" id="field-parent" value="0" />
                        <input type="hidden" name="fields[state]" id="field-state" value="1" />
                        <input type="hidden" name="fields[scope]" id="field-scope" value="course" />
                        <input type="hidden" name="fields[scope_id]" id="field-scope_id"
                            value="<?= $this->filters['scope_id'] ?>" />
                        <input type="hidden" name="fields[scope_sub_id]" id="field-scope_sub_id"
                            value="<?= $this->filters['scope_sub_id'] ?>" />
                        <input type="hidden" name="fields[id]" id="field-id" value="" />
                        <input type="hidden" name="fields[object_id]" id="field-object_id" value="" />

                        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                        <input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
                        <input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
                        <input type="hidden" name="active" value="discussions" />
                        <input type="hidden" name="action" value="savethread" />

                        <?php echo Html::input('token'); ?>

                        <p class="instructions">
                            <?= Lang::txt(
                                'Click on a section and category to the left to view a list of comments.',
                            ) ?><br /><br />
                            <?= Lang::txt(
                                'Click on a comment on the left to view a discussion or start your own above.',
                            ) ?>
                        </p>
                    </form>
                    <?php } else { ?>
                        <p class="instructions">
                            <?= Lang::txt('This forum is currently empty and requires some set-up by the course managers before it can be used.') ?>
                            <?php if ($this->course->access('manage', 'offering')) { ?>
                                <br /><br /><?= Lang::txt('Discussions require at least one section and category before posts can be made. Click the "manage" button to set up this forum.') ?>
                            <?php } ?>
                        </p>
                    <?php } ?>
                    <div class="comment-thread"><?php echo $this->data ? $this->data->html : ''; ?></div>
                </div><!-- / .comments-frame -->
            </div><!-- / .comments-panel -->

        </div><!-- / .comments-views -->
    </div><!-- / .comments-wrap -->

</div><!-- / #comments-container -->
