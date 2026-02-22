<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css();

$this->category->set('section_alias', $this->section->get('alias'));
$this->post->set('section', $this->section->get('alias'));
$this->post->set('category', $this->category->get('alias'));

if ($this->post->get('id')) {
    $action = $this->post->link('edit');
} else {
    $this->post->set('access', 0);
    $action = $this->post->link('new');
}
$categoryUrl = Route::url($this->category->link());
?>
<header id="content-header">
    <h2><?php echo Lang::txt('COM_FORUM'); ?></h2>

    <div id="content-header-extra">
        <p>
            <a class="icon-comments comments btn"
                href="<?php echo $categoryUrl; ?>">
                <?php echo Lang::txt('COM_FORUM_ALL_DISCUSSIONS'); ?>
            </a>
        </p>
    </div>
</header>

<section class="main section">
    <div class="section-inner hz-layout-with-aside">
        <div class="subject">
            <h3>
                <?php if ($this->post->get('id')) { ?>
                    <?php echo Lang::txt('COM_FORUM_EDIT_DISCUSSION'); ?>
                <?php } else { ?>
                    <?php echo Lang::txt('COM_FORUM_NEW_DISCUSSION'); ?>
                <?php } ?>
            </h3>
            <?php $formAction = Route::url($action); ?>
            <form action="<?php echo $formAction; ?>"
                method="post"
                id="commentform"
                enctype="multipart/form-data">
                <p class="comment-member-photo">
                    <img src="<?php echo $this->post->creator->picture(); ?>"
                        alt="" />
                </p>

                <fieldset>
                <?php
                $canManage = $this->config->get('access-manage-thread');
                if ($canManage && !$this->post->get('parent')) {
                    $stickyChecked = $this->post->get('sticky')
                        ? ' checked="checked"'
                        : '';
                    $closedChecked = $this->post->get('closed')
                        ? ' checked="checked"'
                        : '';
                    ?>
                    <div class="grid">
                        <div class="col span-half">
                            <div class="form-group">
                                <label for="field-sticky">
                                    <input class="option form-check-input"
                                        type="checkbox"
                                        name="fields[sticky]"
                                        id="field-sticky"
                                        value="1"<?php echo $stickyChecked; ?> />
                                    <?php echo Lang::txt('COM_FORUM_FIELD_STICKY'); ?>
                                </label>
                            </div>
                        </div>
                        <div class="col span-half omega">
                            <div class="form-group">
                                <label for="field-closed">
                                    <input class="option form-check-input"
                                        type="checkbox"
                                        name="fields[closed]"
                                        id="field-closed"
                                        value="1"<?php echo $closedChecked; ?> />
                                    <?php echo Lang::txt('COM_FORUM_FIELD_CLOSED_THREAD'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <?php $stickyVal = $this->post->get('sticky'); ?>
                    <input type="hidden"
                        name="fields[sticky]"
                        id="field-sticky"
                        value="<?php echo $stickyVal; ?>" />
                    <?php $closedVal = $this->post->get('closed'); ?>
                    <input type="hidden"
                        name="fields[closed]"
                        id="field-closed"
                        value="<?php echo $closedVal; ?>" />
                <?php } ?>

                <?php if (!$this->post->get('parent')) { ?>
                    <div class="form-group">
                        <label for="field-access">
                            <?php echo Lang::txt('COM_FORUM_FIELD_READ_ACCESS'); ?>
                            <?php
                            $sel1 = ($this->post->get('access') == 1)
                                ? ' selected="selected"'
                                : '';
                            $sel2 = ($this->post->get('access') == 2)
                                ? ' selected="selected"'
                                : '';
                            $publicTxt = Lang::txt(
                                'COM_FORUM_FIELD_READ_ACCESS_OPTION_PUBLIC'
                            );
                            $registeredTxt = Lang::txt(
                                'COM_FORUM_FIELD_READ_ACCESS_OPTION_REGISTERED'
                            );
                            ?>
                            <select class="form-control"
                                name="fields[access]"
                                id="field-access">
                                <option value="1"<?php echo $sel1; ?>>
                                    <?php echo $publicTxt; ?>
                                </option>
                                <option value="2"<?php echo $sel2; ?>>
                                    <?php echo $registeredTxt; ?>
                                </option>
                            </select>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="field-category_id">
                            <?php echo Lang::txt('COM_FORUM_FIELD_CATEGORY'); ?>
                            <span class="required">
                                <?php echo Lang::txt('COM_FORUM_REQUIRED'); ?>
                            </span>
                            <select class="form-control"
                                name="fields[category_id]"
                                id="field-category_id">
                                <?php
                                $filters = array(
                                    'state'  => 1,
                                    'access' => User::getAuthorisedViewLevels()
                                );
                                $sections = $this->forum->sections($filters)->rows();
                                foreach ($sections as $section) {
                                    $categories = $section->categories()
                                        ->whereEquals('state', $filters['state'])
                                        ->whereIn('access', $filters['access'])
                                        ->rows();
                                    if ($categories->count() > 0) {
                                        $secTitle = $this->escape(
                                            stripslashes($section->get('title'))
                                        );
                                        ?>
                                        <optgroup label="<?php echo $secTitle; ?>">
                                            <?php foreach ($categories as $category) { ?>
                                                <?php
                                                $catId = $category->get('id');
                                                $catAlias = $category->get('alias');
                                                $myAlias = $this->category->get('alias');
                                                $selected = ($myAlias == $catAlias)
                                                    ? ' selected="selected"'
                                                    : '';
                                                $catTitle = $this->escape(
                                                    stripslashes($category->get('title'))
                                                );
                                                ?>
                                                <option value="<?php echo $catId; ?>"<?php echo $selected; ?>>
                                                    <?php echo $catTitle; ?>
                                                </option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="field-title">
                            <?php echo Lang::txt('COM_FORUM_FIELD_TITLE'); ?>
                            <?php
                            $titleVal = $this->escape(
                                stripslashes($this->post->get('title', ''))
                            );
                            ?>
                            <input type="text"
                                class="form-control"
                                name="fields[title]"
                                id="field-title"
                                value="<?php echo $titleVal; ?>" />
                        </label>
                    </div>
                <?php } else { ?>
                    <?php $catIdVal = $this->post->get('category_id'); ?>
                    <input type="hidden"
                        name="fields[category_id]"
                        id="field-category_id"
                        value="<?php echo $catIdVal; ?>" />
                    <?php $accessVal = $this->post->get('access', 0); ?>
                    <input type="hidden"
                        name="fields[access]"
                        id="field-access"
                        value="<?php echo $accessVal; ?>" />
                <?php } ?>

                    <div class="form-group">
                        <label for="fieldcomment">
                            <div>
                                <?php echo Lang::txt('COM_FORUM_FIELD_COMMENTS'); ?>
                                <span class="required">
                                    <?php echo Lang::txt('COM_FORUM_REQUIRED'); ?>
                                </span>
                                <span class="note" style='float:right'>
                                    Use an @ sign to mention users in the post
                                </span>
                            </div>

                            <?php
                            $commentVal = $this->escape(
                                stripslashes($this->post->get('comment', ''))
                            );
                            echo $this->editor(
                                'fields[comment]',
                                $commentVal,
                                35,
                                15,
                                'fieldcomment',
                                array(
                                    'class' => 'form-control minimal no-footer',
                                    'mentions' => array(
                                        array(
                                            'minChars' => 0,
                                            'feed' => '/api/members/mentions/list?search={encodedQuery}',
                                            'itemTemplate' => '<li data-id="{id}">'
                                                . '<img class="photo" src="{picture}" />'
                                                . '<strong class="username">{username}</strong>'
                                                . '<span class="fullname">{name}</span></li>',
                                            'outputTemplate' => '<a href="/members/{id}"'
                                                . ' data-user-id="{id}" target="_blank">'
                                                . '@{username}</a>&nbsp;&nbsp;',
                                        )
                                    )
                                )
                            ); ?>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="actags">
                            <?php echo Lang::txt('COM_FORUM_FIELD_TAGS'); ?>:
                            <?php
                            echo $this->autocompleter(
                                'tags',
                                'tags',
                                $this->escape($this->post->tags('string')),
                                'actags'
                            );
                            ?>
                        </label>
                    </div>

                    <fieldset>
                        <legend>
                            <?php echo Lang::txt('COM_FORUM_LEGEND_ATTACHMENTS'); ?>
                        </legend>

                        <?php $attachment = $this->post->attachments()->row(); ?>

                        <div class="grid">
                            <div class="col span-half">
                                <div class="form-group">
                                    <label for="upload">
                                        <?php echo Lang::txt('COM_FORUM_FIELD_FILE'); ?>
                                        <?php if ($attachment->get('filename')) {
                                            $attFilename = $this->escape(
                                                stripslashes($attachment->get('filename'))
                                            );
                                            echo '<strong>' . $attFilename . '</strong>';
                                        } ?>
                                        <input type="file"
                                            class="form-control-file"
                                            name="upload"
                                            id="upload" />
                                    </label>
                                </div>
                            </div>
                            <div class="col span-half omega">
                                <div class="form-group">
                                    <label for="field-attach-descritpion">
                                        <?php echo Lang::txt('COM_FORUM_FIELD_DESCRIPTION'); ?>
                                        <?php
                                        $attDesc = $this->escape(
                                            stripslashes($attachment->get('description', ''))
                                        );
                                        ?>
                                        <input type="text"
                                            class="form-control"
                                            name="description"
                                            id="field-attach-descritpion"
                                            value="<?php echo $attDesc; ?>" />
                                    </label>
                                </div>
                            </div>
                            <?php $attId = $this->escape($attachment->get('id')); ?>
                            <input type="hidden"
                                name="attachment"
                                value="<?php echo $attId; ?>" />
                        </div>
                        <?php if ($attachment->get('id')) { ?>
                            <p class="warning">
                                <?php echo Lang::txt('COM_FORUM_FIELD_FILE_WARNING'); ?>
                            </p>
                        <?php } ?>
                    </fieldset>

                    <?php if ($this->config->get('allow_anonymous')) { ?>
                        <div class="form-group">
                            <?php
                            $anonChecked = $this->post->get('anonymous')
                                ? ' checked="checked"'
                                : '';
                            ?>
                            <label for="field-anonymous"
                                id="comment-anonymous-label">
                                <input class="option form-check-input"
                                    type="checkbox"
                                    name="fields[anonymous]"
                                    id="field-anonymous"
                                    value="1"<?php echo $anonChecked; ?> />
                                <?php echo Lang::txt('COM_FORUM_FIELD_ANONYMOUS'); ?>
                            </label>
                        </div>
                    <?php } ?>

                    <p class="submit">
                        <input type="submit"
                            class="btn btn-success"
                            value="<?php echo Lang::txt('JSUBMIT'); ?>" />
                    </p>

                    <div class="sidenote">
                        <p>
                            <strong>
                                <?php echo Lang::txt('COM_FORUM_KEEP_POLITE'); ?>
                            </strong>
                        </p>
                    </div>
                </fieldset>
                <?php $parentVal = $this->post->get('parent'); ?>
                <input type="hidden"
                    name="fields[parent]"
                    value="<?php echo $parentVal; ?>" />
                <input type="hidden" name="fields[state]" value="1" />
                <?php $threadVal = $this->post->get('thread'); ?>
                <input type="hidden"
                    name="fields[thread]"
                    value="<?php echo $threadVal; ?>" />
                <input type="hidden"
                    name="fields[id]"
                    value="<?php echo $this->post->get('id'); ?>" />
                <input type="hidden" name="fields[scope]" value="site" />
                <input type="hidden" name="fields[scope_id]" value="0" />
                <?php $scopeSubId = $this->post->get('scope_sub_id'); ?>
                <input type="hidden"
                    name="fields[scope_sub_id]"
                    value="<?php echo $scopeSubId; ?>" />
                <?php $objectId = $this->post->get('object_id'); ?>
                <input type="hidden"
                    name="fields[object_id]"
                    value="<?php echo $objectId; ?>" />

                <input type="hidden"
                    name="option"
                    value="<?php echo $this->option; ?>" />
                <input type="hidden" name="controller" value="threads" />
                <input type="hidden" name="task" value="save" />
                <?php $sectionAlias = $this->escape($this->section->get('alias')); ?>
                <input type="hidden"
                    name="section"
                    value="<?php echo $sectionAlias; ?>" />

                <?php echo Html::input('token'); ?>
            </form>
        </div><!-- / .subject -->
        <aside class="aside">
        <div class="container">
            <p>
                <strong><?php echo Lang::txt('COM_FORUM_WHAT_IS_STICKY'); ?></strong>
                <br />
                <?php echo Lang::txt('COM_FORUM_STICKY_EXPLANATION'); ?>
            </p>

            <p>
                <strong><?php echo Lang::txt('COM_FORUM_WHAT_IS_LOCKING'); ?></strong>
                <br />
                <?php echo Lang::txt('COM_FORUM_LOCKING_EXPLANATION'); ?>
            </p>
        </div>
    </aside><!-- /.aside -->
    </div>
</section><!-- / .below section -->
