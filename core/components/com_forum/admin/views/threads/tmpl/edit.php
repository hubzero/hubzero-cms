<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Forum\Helpers\Permissions::getActions('thread');

$text  = ($this->row->get('parent') ? Lang::txt('COM_FORUM_POSTS') : Lang::txt('COM_FORUM_THREADS')) . ': ';
$text .= ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_FORUM') . ': ' . $text, 'forum');
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('post');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$formAction = Route::url(
    'index.php?option=' . $this->option . '&controller=' . $this->controller
);
$invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
?>

<form action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="editform form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
    enctype="multipart/form-data">
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

                <div class="grid">
                    <div class="col span6">
                        <div class="input-wrap">
                            <label for="field-scope"><?php echo Lang::txt('COM_FORUM_FIELD_SCOPE'); ?>:</label><br />
                            <?php $scopeVal = $this->escape($this->row->get('scope')); ?>
                            <input type="text"
                                name="fields[scope]"
                                id="field-scope"
                                maxlength="150"
                                value="<?php echo $scopeVal; ?>" />
                        </div>
                    </div>
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php $scopeIdLabel = Lang::txt('COM_FORUM_FIELD_SCOPE_ID'); ?>
                            <label for="field-scope_id"><?php echo $scopeIdLabel; ?>:</label><br />
                            <?php $scopeIdVal = $this->escape($this->row->get('scope_id')); ?>
                            <input type="text"
                                name="fields[scope_id]"
                                id="field-scope_id"
                                maxlength="11"
                                value="<?php echo $scopeIdVal; ?>" />
                        </div>
                    </div>
                </div>

                <div class="input-wrap">
                    <label for="field-object_id"><?php echo Lang::txt('COM_FORUM_FIELD_OBJECT_ID'); ?>:</label><br />
                    <?php $objectIdVal = $this->escape($this->row->get('object_id')); ?>
                    <input type="text"
                        name="fields[object_id]"
                        id="field-object_id"
                        maxlength="11"
                        value="<?php echo $objectIdVal; ?>" />
                </div>

                <?php if (!$this->row->get('parent')) { ?>
                    <div class="input-wrap">
                        <?php $catLabel = Lang::txt('COM_FORUM_FIELD_CATEGORY'); ?>
                        <?php $requiredTxt = Lang::txt('JOPTION_REQUIRED'); ?>
                        <label for="field-category_id">
                            <?php echo $catLabel; ?>: <span class="required"><?php echo $requiredTxt; ?></span>
                        </label><br />
                        <select name="fields[category_id]" id="field-category_id">
                            <option value="-1"><?php echo Lang::txt('COM_FORUM_FIELD_CATEGORY_SELECT'); ?></option>
                            <?php foreach ($this->sections as $group => $sections) { ?>
                                <optgroup label="<?php echo $this->escape(stripslashes($group)); ?>">
                                    <?php foreach ($sections as $section) { ?>
                                        <?php $sectionTitle = $this->escape(stripslashes($section->title)); ?>
                                        <optgroup label="&nbsp; &nbsp; <?php echo $sectionTitle; ?>">
                                            <?php foreach ($section->categories as $category) { ?>
                                                <?php
                                                $selected = ($this->row->category_id == $category->id)
                                                    ? ' selected="selected"'
                                                    : '';
                                                $catTitle = $this->escape(stripslashes($category->title));
                                                ?>
                                                <option value="<?php echo $category->id; ?>"<?php echo $selected; ?>>
                                                    &nbsp; &nbsp; <?php echo $catTitle; ?>
                                                </option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php } ?>
                                </optgroup>
                            <?php } ?>
                        </select>
                    </div>
                <?php } ?>

                <?php if ($this->row->get('parent')) { ?>
                    <div class="input-wrap">
                        <label for="field-title"><?php echo Lang::txt('COM_FORUM_FIELD_PARENT'); ?>:</label><br />
                        <select name="fields[parent]" id="field-parent">
                            <option value="0"><?php echo Lang::txt('COM_FORUM_FIELD_PARENT_SELECT'); ?></option>
                            <?php
                            $posts = \Components\Forum\Models\Post::all()
                                ->whereEquals('thread', $this->row->get('thread'))
                                ->ordered()
                                ->rows();
                            foreach ($posts as $post) {
                                if ($post->get('id') == $this->row->get('id')) {
                                    continue;
                                }
                                $selected = ($this->row->get('parent') == $post->get('id'))
                                    ? ' selected="selected"'
                                    : '';
                                $postTitle = $this->escape(stripslashes($post->get('title')));
                                ?>
                                <option value="<?php echo $post->id; ?>"<?php echo $selected; ?>>
                                    <?php echo $postTitle; ?>
                                </option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                <?php } ?>

                <div class="input-wrap">
                    <label for="field-title"><?php echo Lang::txt('COM_FORUM_FIELD_TITLE'); ?>:</label><br />
                    <?php $titleVal = $this->escape(stripslashes($this->row->get('title'))); ?>
                    <input type="text"
                        name="fields[title]"
                        id="field-title"
                        size="30"
                        maxlength="250"
                        value="<?php echo $titleVal; ?>" />
                </div>

                <div class="input-wrap">
                    <?php $commentLabel = Lang::txt('COM_FORUM_FIELD_COMMENTS'); ?>
                    <?php $requiredTxt = isset($requiredTxt) ? $requiredTxt : Lang::txt('JOPTION_REQUIRED'); ?>
                    <label for="field-comment">
                        <?php echo $commentLabel; ?> <span class="required"><?php echo $requiredTxt; ?></span>
                    </label><br />
                    <textarea name="fields[comment]"
                        id="field-comment"
                        cols="35"
                        rows="10"
                        class="required"><?php echo $this->escape($this->row->get('comment')); ?></textarea>
                </div>

                <?php if (!$this->row->get('parent')) { ?>
                    <div class="input-wrap">
                        <label for="field-tags"><?php echo Lang::txt('COM_FORUM_FIELD_TAGS'); ?></label><br />
                        <textarea name="tags"
                            id="field-tags"
                            cols="35"
                            rows="5"><?php echo $this->escape($this->row->tags('string')); ?></textarea>
                    </div>
                <?php } ?>
            </fieldset>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_FORUM_LEGEND_ATTACHMENTS'); ?></span></legend>

                <?php /*foreach ($this->row->attachments()->rows() as $attachment): ?>
                    <div class="input-wrap attachment">
                        <div class="attachment-file">
                            <?php echo $this->escape(stripslashes($attachment->get('filename'))); ?>
                        </div>
                        <div class="attachment-options">
                            <a href=""></a>
                        </div>
                    </div>
                <?php endforeach;*/ ?>
                <?php if ($this->row->get('id')) { ?>
                    <?php
                    $mediaSrc = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=media&tmpl=component&id='
                        . $this->row->get('id') . '&t=' . Date::toUnix()
                    );
                    ?>
                    <iframe width="100%"
                        height="200"
                        name="media"
                        id="media"
                        frameborder="0"
                        src="<?php echo $mediaSrc; ?>"></iframe>
                <?php } ?>

                <div class="input-wrap">
                    <label for="upload"><?php echo Lang::txt('COM_FORUM_FIELD_FILE'); ?></label><br />
                    <input type="file" name="upload" id="upload" />
                </div>

                <div class="input-wrap">
                    <?php $descLabel = Lang::txt('COM_FORUM_FIELD_DESCRIPTION'); ?>
                    <label for="field-attach-descritpion"><?php echo $descLabel; ?></label><br />
                    <input type="text" name="description" id="field-attach-descritpion" value="" />
                </div>
            </fieldset>

        </div>
        <div class="col span5">
            <table class="meta">
                <tbody>
                    <?php if ($this->row->get('parent')) { ?>
                        <tr>
                            <th><?php echo Lang::txt('COM_FORUM_FIELD_THREAD'); ?>:</th>
                            <td>
                                <?php
                                echo $this->row->get('thread');
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th><?php echo Lang::txt('COM_FORUM_FIELD_CREATOR'); ?>:</th>
                        <td>
                            <?php
                            echo $this->escape($this->row->creator->get('name'));
                            ?>
                            <input type="hidden"
                                name="fields[created_by]"
                                id="field-created_by"
                                value="<?php echo $this->row->get('created_by'); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('COM_FORUM_FIELD_CREATED'); ?>:</th>
                        <td>
                            <?php echo $this->row->get('created', Date::of('now')->toSql()); ?>
                            <input type="hidden"
                                name="fields[created]"
                                id="field-created"
                                value="<?php echo $this->row->get('created'); ?>" />
                        </td>
                    </tr>
                    <?php if ($this->row->get('modified_by')) { ?>
                        <tr>
                            <th><?php echo Lang::txt('COM_FORUM_FIELD_MODIFIER'); ?>:</th>
                            <td>
                                <?php
                                echo $this->escape($this->row->modifier->get('name'));
                                ?>
                                <input type="hidden"
                                    name="fields[modified_by]"
                                    id="field-modified_by"
                                    value="<?php echo $this->row->get('modified_by'); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo Lang::txt('COM_FORUM_FIELD_MODIFIED'); ?>:</th>
                            <td>
                                <?php echo $this->row->get('modified'); ?>
                                <input type="hidden"
                                    name="fields[modified]"
                                    id="field-modified"
                                    value="<?php echo $this->row->get('modified'); ?>" />
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

                <div class="input-wrap">
                    <?php $anonChecked = $this->row->get('anonymous') ? ' checked="checked"' : ''; ?>
                    <input class="option"
                        type="checkbox"
                        name="fields[anonymous]"
                        id="field-anonymous"
                        value="1"<?php echo $anonChecked; ?> />
                    <label for="field-anonymous"><?php echo Lang::txt('COM_FORUM_FIELD_ANONYMOUS'); ?></label>
                </div>

                <?php if (!$this->row->get('parent')) { ?>
                    <div class="input-wrap">
                        <?php $stickyChecked = $this->row->get('sticky') ? ' checked="checked"' : ''; ?>
                        <input class="option"
                            type="checkbox"
                            name="fields[sticky]"
                            id="field-sticky"
                            value="1"<?php echo $stickyChecked; ?> />
                        <label for="field-sticky"><?php echo Lang::txt('COM_FORUM_FIELD_STICKY'); ?></label>
                    </div>
                <?php } ?>

                <div class="input-wrap">
                    <label for="field-state"><?php echo Lang::txt('COM_FORUM_FIELD_STATE'); ?>:</label><br />
                    <select name="fields[state]" id="field-state">
                        <?php $sel0 = ($this->row->get('state') == 0) ? ' selected="selected"' : ''; ?>
                        <?php $sel1 = ($this->row->get('state') == 1) ? ' selected="selected"' : ''; ?>
                        <?php $sel2 = ($this->row->get('state') == 2) ? ' selected="selected"' : ''; ?>
                        <option value="0"<?php echo $sel0; ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
                        <option value="1"<?php echo $sel1; ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
                        <option value="2"<?php echo $sel2; ?>><?php echo Lang::txt('JTRASHED'); ?></option>
                    </select>
                </div>

                <div class="input-wrap">
                    <label for="field-access"><?php echo Lang::txt('COM_FORUM_FIELD_ACCESS'); ?>:</label><br />
                    <select name="fields[access]" id="field-access">
                        <?php echo Html::select(
                            'options',
                            Html::access('assetgroups'),
                            'value',
                            'text',
                            $this->row->get('access')
                        ); ?>
                    </select>
                </div>
            </fieldset>
        </div>
    </div>

    <?php if ($canDo->get('core.admin')) : ?>
        <div class="col span12">
            <fieldset class="panelform">
                <legend><span><?php echo Lang::txt('COM_FORUM_FIELDSET_RULES'); ?></span></legend>
                <?php echo $this->form->getLabel('rules'); ?>
                <?php echo $this->form->getInput('rules'); ?>
            </fieldset>
        </div>
    <?php endif; ?>

    <?php if ($this->row->get('parent')) { ?>
        <input type="hidden" name="fields[category_id]" value="<?php echo $this->row->get('category_id'); ?>" />
    <?php } else { ?>
        <input type="hidden" name="fields[parent]" value="<?php echo $this->row->get('parent'); ?>" />
    <?php } ?>
    <input type="hidden" name="fields[thread]" value="<?php echo $this->row->get('thread'); ?>" />
    <input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />

    <input type="hidden" name="thread" value="<?php echo $this->row->get('thread'); ?>" />
    <input type="hidden" name="parent" value="<?php echo $this->row->get('parent'); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
