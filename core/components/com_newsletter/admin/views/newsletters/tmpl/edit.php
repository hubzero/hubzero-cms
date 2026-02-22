<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Newsletter\Helpers\Permissions::getActions('newsletter');

$text = ($this->task == 'edit' ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW'));

Toolbar::title(Lang::txt('COM_NEWSLETTER') . ': ' . $text, 'newsletter');
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js()
     ->js('jquery.formwatcher', 'system');

// Primary and secondary stories
$primaries = $this->newsletter->primary()
    ->whereEquals('deleted', 0)
    ->ordered()
    ->rows();

$primary_highest_order = 1;
if ($primaries->count() > 0) {
    $primaries->last();
    $key = $primaries->key();
    $primary_highest_order = $primaries->seek($key)->get('order');
    $primaries->rewind();
}

$secondaries = $this->newsletter->secondary()
    ->whereEquals('deleted', 0)
    ->ordered()
    ->rows();
$secondary_highest_order = 1;
if ($secondaries->count() > 0) {
    $secondaries->last();
    $key = $secondaries->key();
    $secondary_highest_order = $secondaries->seek($key)->get('order');
    $secondaries->rewind();
}

$formAction = Route::url('index.php?option=' . $this->option);
$unsavedMsg = Lang::txt('COM_NEWSLETTER_WANRING_UNSAVED_CHANGES');
$invalidMsg = $this->escape(
    Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED')
);
$storyBase = 'index.php?option=' . $this->option
    . '&controller=stories&nid=' . $this->newsletter->id;
$storyTitleLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_TITLE');
$storyOrderLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_ORDER');
$storyLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_STORY');
$readmoreLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE');
$moveUpTxt = Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_MOVE_UP');
$moveDownTxt = Lang::txt('COM_NEWSLETTER_NEWSLETTER_STORY_MOVE_DOWN');
$editStoryTxt = Lang::txt('COM_NEWSLETTER_NEWSLETTER_EDIT_STORY');
$deleteStoryTxt = Lang::txt('COM_NEWSLETTER_NEWSLETTER_DELETE_STORY');
?>

<form
    action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="editform form-validate"
    data-formwatcher-message="<?php echo $unsavedMsg; ?>"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
>
    <div class="grid">
        <div class="col span6">
            <fieldset class="adminform">
                <legend><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_DETAILS'); ?></legend>

                <div class="input-wrap">
                    <?php
                    $nameLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_NAME');
                    $reqTxt = Lang::txt('JOPTION_REQUIRED');
                    ?>
                    <label for="newsletter-name">
                        <?php echo $nameLabel; ?>: <span class="required"><?php echo $reqTxt; ?></span>
                    </label>
                    <input
                        type="text"
                        name="newsletter[name]"
                        id="newsletter-name"
                        class="required"
                        value="<?php echo $this->escape($this->newsletter->name); ?>"
                    />
                </div>

                <?php $aliasHint = Lang::txt('COM_NEWSLETTER_NEWSLETTER_ALIAS_HINT'); ?>
                <div class="input-wrap" data-hint="<?php echo $aliasHint; ?>">
                    <label for="newsletter-alias"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_ALIAS'); ?>:</label>
                    <input
                        type="text"
                        name="newsletter[alias]"
                        id="newsletter-alias"
                        value="<?php echo $this->escape($this->newsletter->alias); ?>"
                    />
                    <span class="hint"><?php echo $aliasHint; ?></span>
                </div>

                <div class="grid">
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php $issueLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_ISSUE'); ?>
                            <label for="newsletter-issue"><?php echo $issueLabel; ?>:</label>
                            <input
                                type="text"
                                name="newsletter[issue]"
                                id="newsletter-issue"
                                value="<?php echo $this->escape($this->newsletter->issue); ?>"
                            />
                        </div>
                    </div>
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php $fmtLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_FORMAT'); ?>
                            <label for="newsletter-type"><?php echo $fmtLabel; ?>:</label>
                            <select name="newsletter[type]" id="newsletter-type">
                                <option value="html" <?php if ($this->newsletter->type == 'html') :
                                    ?>selected="selected"<?php
                                                     endif; ?>>
                                    <?php echo Lang::txt('COM_NEWSLETTER_FORMAT_HTML'); ?>
                                </option>
                                <option value="plain" <?php if ($this->newsletter->type == 'plain') :
                                    ?>selected="selected"<?php
                                                      endif; ?>>
                                    <?php echo Lang::txt('COM_NEWSLETTER_FORMAT_PLAIN'); ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="input-wrap">
                    <?php $tplLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATE'); ?>
                    <label for="newsletter-template_id"><?php echo $tplLabel; ?>:</label>
                    <select name="newsletter[template_id]" id="newsletter-template_id">
                        <?php $tplDefault = Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATE_DEFAULT'); ?>
                        <option value=""><?php echo $tplDefault; ?></option>
                        <option value="-1" <?php if ($this->newsletter->template_id == '-1') :
                            ?>selected="selected"<?php
                                           endif; ?>>
                            <?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATE_NONE'); ?>
                        </option>
                        <?php foreach ($this->templates as $t) : ?>
                            <?php
                            $sel = ($t->id == $this->newsletter->template_id)
                                ? 'selected="selected"'
                                : '';
                            ?>
                            <option <?php echo $sel; ?> value="<?php echo $t->id; ?>">
                                <?php echo $this->escape($t->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php $showHint = Lang::txt('COM_NEWSLETTER_NEWSLETTER_SHOW_HINT'); ?>
                <div class="input-wrap" data-hint="<?php echo $showHint; ?>">
                    <?php $showLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_SHOW'); ?>
                    <label for="newsletter-published"><?php echo $showLabel; ?>:</label>
                    <select name="newsletter[published]" id="newsletter-published">
                        <option value="1" <?php if ($this->newsletter->published == '1') :
                            ?>selected="selected"<?php
                                          endif; ?>>
                            <?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SHOW_SHOW'); ?>
                        </option>
                        <option value="0" <?php if ($this->newsletter->published == '0') :
                            ?>selected="selected"<?php
                                          endif; ?>>
                            <?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SHOW_DONT_SHOW'); ?>
                        </option>
                    </select>
                    <span class="hint">
                        <?php echo $showHint; ?>
                    </span>
                </div>

                <?php
                    $link = $this->config->get(
                        'email_tracking_link',
                        'http://kb.mailchimp.com/article/how-open-tracking-works'
                    );
                    $hint = Lang::txt(
                        'COM_NEWSLETTER_NEWSLETTER_WHAT_IS_TRACKING',
                        $link
                    );
                    ?>
                <div class="input-wrap" data-hint="<?php echo $this->escape(strip_tags($hint)); ?>">
                    <?php $trackLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_EMAIL_TRACKING'); ?>
                    <label for="newsletter-tracking"><?php echo $trackLabel; ?>:</label>
                    <select name="newsletter[tracking]" id="newsletter-tracking">
                        <option value="1" <?php if ($this->newsletter->tracking) :
                            ?>selected="selected"<?php
                                          endif; ?>>
                            <?php echo Lang::txt('JYES'); ?>
                        </option>
                        <option value="0" <?php if (!$this->newsletter->tracking) :
                            ?>selected="selected"<?php
                                          endif; ?>>
                            <?php echo Lang::txt('JNO'); ?>
                        </option>
                    </select>
                    <span class="hint">
                        <?php echo $hint; ?>
                    </span>
                </div>

                <?php $autogenHint = Lang::txt('COM_NEWSLETTER_NEWSLETTER_EMAIL_AUTOGEN_HINT'); ?>
                <div class="input-wrap" data-hint="<?php echo $this->escape($autogenHint); ?>">
                    <?php $autogenLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_EMAIL_AUTOGEN'); ?>
                    <label for="newsletter-autogen"><?php echo $autogenLabel; ?>:</label>
                    <select name="newsletter[autogen]" id="newsletter-autogen">
                        <option value="0" <?php if ($this->newsletter->autogen == 0) :
                            ?>selected="selected"<?php
                                          endif; ?>>
                            <?php echo Lang::txt('Disabled'); ?>
                        </option>
                        <option value="1" <?php if ($this->newsletter->autogen == 1) :
                            ?>selected="selected"<?php
                                          endif; ?>>
                            <?php echo Lang::txt('DAILY'); ?>
                        </option>
                        <option value="2" <?php if ($this->newsletter->autogen == 2) :
                            ?>selected="selected"<?php
                                          endif; ?>>
                            <?php echo Lang::txt('WEEKLY'); ?>
                        </option>
                        <option value="3" <?php if ($this->newsletter->autogen == 3) :
                            ?>selected="selected"<?php
                                          endif; ?>>
                            <?php echo Lang::txt('MONTHLY'); ?>
                        </option>
                    </select>
                    <span class="hint">
                        <?php echo $autogenHint; ?>
                    </span>
                </div>
            </fieldset>
        </div>

        <div class="col span6">
            <?php if ($this->newsletter->id) : ?>
                <table class="meta">
                    <tbody>
                        <tr>
                            <?php $idLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_ID'); ?>
                            <th scope="row"><?php echo $idLabel; ?>:</th>
                            <td>
                                <?php echo $this->newsletter->id; ?>
                            </td>
                        </tr>

                        <?php if ($this->newsletter->created) : ?>
                            <tr>
                                <?php $createdLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_CREATED_DATE'); ?>
                                <th scope="row"><?php echo $createdLabel; ?>:</th>
                                <td>
                                    <?php echo Date::of($this->newsletter->created)->toLocal('F d, Y @ g:ia'); ?>
                                    <input
                                        type="hidden"
                                        name="newsletter[created]"
                                        value="<?php echo $this->newsletter->created; ?>"
                                    />
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if ($this->newsletter->created_by) : ?>
                            <tr>
                                <?php $createdByLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_CREATED_BY'); ?>
                                <th scope="row"><?php echo $createdByLabel; ?>:</th>
                                <td>
                                    <?php
                                        $user = User::getInstance($this->newsletter->created_by);
                                        echo (is_object($user) && $user->get('name') != '')
                                            ? $user->get('name')
                                            : 'Admin';
                                    ?>
                                    <input
                                        type="hidden"
                                        name="newsletter[created_by]"
                                        value="<?php echo $this->newsletter->created_by; ?>"
                                    />
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if ($this->newsletter->modified) : ?>
                            <tr>
                                <?php $modLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_LAST_MODIFIED'); ?>
                                <th scope="row"><?php echo $modLabel; ?>:</th>
                                <td>
                                    <?php echo Date::of($this->newsletter->modified)->toLocal('F d, Y @ g:ia'); ?>
                                    <input
                                        type="hidden"
                                        name="newsletter[modified]"
                                        value="<?php echo $this->newsletter->modified; ?>"
                                    />
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php if ($this->newsletter->modified_by) : ?>
                            <tr>
                                <?php $modByLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_LAST_MODIFIED_BY'); ?>
                                <th scope="row"><?php echo $modByLabel; ?>:</th>
                                <td>
                                    <?php
                                        $user = User::getInstance($this->newsletter->modified_by);
                                        echo (is_object($user) && $user->get('name') != '')
                                            ? $user->get('name')
                                            : 'Admin';
                                    ?>
                                    <input
                                        type="hidden"
                                        name="newsletter[modified_by]"
                                        value="<?php echo $this->newsletter->modified_by; ?>"
                                    />
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php
                    $params = new \Hubzero\Config\Registry($this->newsletter->params);
                ?>
                <fieldset class="adminform">
                    <?php $mailDetails = Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_DETAILS'); ?>
                    <legend><?php echo $mailDetails; ?></legend>

                    <div class="input-wrap">
                        <?php $fromNameLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_FROM_NAME'); ?>
                        <label for="param-from_name"><?php echo $fromNameLabel; ?>:</label>
                        <input
                            type="text"
                            name="params[from_name]"
                            id="param-from_name"
                            value="<?php echo $this->escape($params->get('from_name')); ?>"
                        />
                    </div>

                    <div class="input-wrap">
                        <?php $fromEmailLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_FROM_EMAIL'); ?>
                        <label for="param-from_address"><?php echo $fromEmailLabel; ?>:</label>
                        <input
                            type="text"
                            name="params[from_address]"
                            id="param-from_address"
                            value="<?php echo $this->escape($params->get('from_address')); ?>"
                        />
                    </div>

                    <div class="input-wrap">
                        <?php $replyNameLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_REPLYTO_NAME'); ?>
                        <label for="param-replyto_name"><?php echo $replyNameLabel; ?>:</label>
                        <input
                            type="text"
                            name="params[replyto_name]"
                            id="param-replyto_name"
                            value="<?php echo $this->escape($params->get('replyto_name')); ?>"
                        />
                    </div>

                    <div class="input-wrap">
                        <?php $replyEmailLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_REPLYTO_EMAIL'); ?>
                        <label for="param-replyto_address"><?php echo $replyEmailLabel; ?>:</label>
                        <input
                            type="text"
                            name="params[replyto_address]"
                            id="param-replyto_address"
                            value="<?php echo $this->escape($params->get('replyto_address')); ?>"
                        />
                    </div>
                </fieldset>
            <?php else : ?>
                <p class="info">
                    <?php echo Lang::txt('COM_NEWSLETTER_MUST_SAVE_TO_ADD_CONTENT'); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <hr />

    <div class="col span12">
        <?php if ($this->newsletter->id != null) : ?>
            <?php if (
            $this->newsletter->template_id == '-1'
                || (!$this->newsletter->template_id && $this->newsletter->content != '')
) : ?>
                <fieldset class="adminform">
                    <?php $contentLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_CONTENT'); ?>
                    <legend><?php echo $contentLabel; ?></legend>

                    <?php $htmlHint = Lang::txt('COM_NEWSLETTER_NEWSLETTER_CONTENT_HTML_HINT'); ?>
                    <?php $htmlLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_CONTENT_HTML'); ?>
                    <div class="input-wrap" data-hint="<?php echo $htmlHint; ?>">
                        <label for="newsletter-html_content"><?php echo $htmlLabel; ?></label>
                        <textarea
                            name="newsletter[html_content]"
                            id="newsletter-html_content"
                            cols="100"
                            rows="20"
                        ><?php echo $this->escape($this->newsletter->html_content); ?></textarea>
                        <span class="hint"><?php echo $htmlHint; ?></span>
                    </div>

                    <?php $plainHint = Lang::txt('COM_NEWSLETTER_NEWSLETTER_CONTENT_PLAIN_HINT'); ?>
                    <?php $plainLabel = Lang::txt('COM_NEWSLETTER_NEWSLETTER_CONTENT_PLAIN'); ?>
                    <div class="input-wrap" data-hint="<?php echo $plainHint; ?>">
                        <label for="newsletter-plain_content"><?php echo $plainLabel; ?></label>
                        <textarea
                            name="newsletter[plain_content]"
                            id="newsletter-plain_content"
                            cols="100"
                            rows="20"
                        ><?php echo $this->escape($this->newsletter->plain_content); ?></textarea>
                        <span class="hint"><?php echo $plainHint; ?></span>
                    </div>
                </fieldset>
            <?php else : ?>
                <fieldset class="adminform" id="primary-stories">
                    <legend>
                        <?php if ($this->newsletter->autogen == 0) : ?>
                            <?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_PRIMARY_STORIES'); ?>
                            <?php
                            $addPrimaryUrl = Route::url(
                                $storyBase . '&task=add&type=primary'
                            );
                            ?>
                            <a class="fltrt add-story" href="<?php echo $addPrimaryUrl; ?>">
                                <?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_PRIMARY_STORIES_ADD'); ?>
                            </a>
                        <?php else : ?>
                            <?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_AUTOGEN_STORIES'); ?>
                            <?php
                            $addAutogenUrl = Route::url(
                                $storyBase . '&task=add&type=autogen'
                            );
                            ?>
                            <a class="fltrt add-story" href="<?php echo $addAutogenUrl; ?>">
                                <?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_AUTOGEN_STORIES_ADD'); ?>
                            </a>
                        <?php endif; ?>
                    </legend>

                    <?php echo Html::sliders('start', 'content-pane'); ?>
                        <?php $i = 0; ?>
                        <?php foreach ($primaries as $primary) : ?>
                            <?php
                            echo Html::sliders(
                                'panel',
                                ($i + 1) . ". " . $primary->title,
                                "pstory-" . ($i + 1)
                            );
                            $editPrimaryUrl = Route::url(
                                $storyBase . '&task=edit&type=primary&sid=' . $primary->id
                            );
                            $deletePrimaryUrl = Route::url(
                                $storyBase . '&task=delete&type=primary&sid=' . $primary->id
                            );
                            ?>
                                <table class="admintable">
                                    <tbody>
                                        <tr>
                                            <td colspan="2">
                                                <a href="<?php echo $editPrimaryUrl; ?>">
                                                    <?php echo $editStoryTxt; ?>
                                                </a> |
                                                <a href="<?php echo $deletePrimaryUrl; ?>">
                                                    <?php echo $deleteStoryTxt; ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="key"><?php echo $storyTitleLabel; ?>:</td>
                                            <td><?php echo $this->escape($primary->title); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="key"><?php echo $storyOrderLabel; ?>:</td>
                                            <td>
                                                <input
                                                    type="text"
                                                    readonly="readonly"
                                                    value="<?php echo $primary->order; ?>"
                                                    class="align-center"
                                                    size="3"
                                                />

                                                <?php if ($primary->order > 1) : ?>
                                                    <?php
                                                    $upUrl = Route::url(
                                                        $storyBase
                                                        . '&task=reorder&direction=up'
                                                        . '&type=primary&sid=' . $primary->id
                                                    );
                                                    ?>
                                                    <a href="<?php echo $upUrl; ?>">
                                                        <?php echo $moveUpTxt; ?>
                                                    </a>
                                                <?php endif ?>
                                                <?php if ($primary->order < $primary_highest_order) : ?>
                                                    <?php
                                                    $downUrl = Route::url(
                                                        $storyBase
                                                        . '&task=reorder&direction=down'
                                                        . '&type=primary&sid=' . $primary->id
                                                    );
                                                    ?>
                                                    <a href="<?php echo $downUrl; ?>">
                                                        <?php echo $moveDownTxt; ?>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="key"><?php echo $storyLabel; ?>:</td>
                                            <td><?php echo nl2br(stripslashes($primary->story)); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="key"><?php echo $readmoreLabel; ?>:</td>
                                            <td>
                                                <strong><?php echo $primary->readmore_title; ?></strong>
                                                - <?php echo $primary->readmore_link; ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    <?php echo Html::sliders('end'); ?>
                </fieldset>
                <hr />
                <?php
                    // Hide secondary story if AutoGen
                if ($this->newsletter->autogen == 0) :
                    ?>
                    <fieldset class="adminform" id="secondary-stories">
                        <legend>
                        <?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SECONDARY_STORIES'); ?>
                            <?php
                            $addSecondaryUrl = Route::url(
                                $storyBase . '&task=add&type=secondary'
                            );
                            ?>
                            <a class="fltrt add-story" href="<?php echo $addSecondaryUrl; ?>">
                            <?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SECONDARY_STORIES_ADD'); ?>
                            </a>
                        </legend>

                    <?php echo Html::sliders('start', 'content-pane2'); ?>
                        <?php $i = 0; ?>
                        <?php foreach ($secondaries as $secondary) : ?>
                                <?php
                                echo Html::sliders(
                                    'panel',
                                    ($i + 1) . ". " . $secondary->title,
                                    "sstory-" . ($i + 1)
                                );
                                $editSecUrl = Route::url(
                                    $storyBase . '&task=edit&type=secondary&sid=' . $secondary->id
                                );
                                $deleteSecUrl = Route::url(
                                    $storyBase . '&task=delete&type=secondary&sid=' . $secondary->id
                                );
                                ?>
                                    <table class="admintable">
                                        <tbody>
                                            <tr>
                                                <td colspan="2">
                                                    <a href="<?php echo $editSecUrl; ?>">
                                                        <?php echo $editStoryTxt; ?>
                                                    </a> |
                                                    <a href="<?php echo $deleteSecUrl; ?>">
                                                        <?php echo $deleteStoryTxt; ?>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="key"><?php echo $storyTitleLabel; ?>:</td>
                                                <td><?php echo $secondary->title; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="key"><?php echo $storyOrderLabel; ?>:</td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        readonly="readonly"
                                                        value="<?php echo $secondary->order; ?>"
                                                        class="align-center"
                                                        size="3"
                                                    />
                                                    <?php if ($secondary->order > 1) : ?>
                                                        <?php
                                                        $upSecUrl = Route::url(
                                                            $storyBase
                                                            . '&task=reorder&direction=up'
                                                            . '&type=secondary&sid=' . $secondary->id
                                                        );
                                                        ?>
                                                        <a href="<?php echo $upSecUrl; ?>">
                                                            <?php echo $moveUpTxt; ?>
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if ($secondary->order < $secondary_highest_order) : ?>
                                                        <?php
                                                        $downSecUrl = Route::url(
                                                            $storyBase
                                                            . '&task=reorder&direction=down'
                                                            . '&type=secondary&sid=' . $secondary->id
                                                        );
                                                        ?>
                                                        <a href="<?php echo $downSecUrl; ?>">
                                                            <?php echo $moveDownTxt; ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="key"><?php echo $storyLabel; ?>:</td>
                                                <td><?php echo nl2br(stripslashes($secondary->story)); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="key"><?php echo $readmoreLabel; ?>:</td>
                                                <td>
                                                    <strong><?php echo $secondary->readmore_title; ?></strong>
                                                    - <?php echo $secondary->readmore_link; ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                <?php $i++; ?>
                        <?php endforeach; ?>
                    <?php echo Html::sliders('end'); ?>
                    </fieldset>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <input type="hidden" name="newsletter[id]" value="<?php echo $this->newsletter->id; ?>" />
    <input type="hidden" name="id" value="<?php echo $this->newsletter->id; ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
