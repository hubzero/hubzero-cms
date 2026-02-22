<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Newsletter\Helpers\Permissions::getActions('newsletter');

//set title
$text = ($this->task == 'edit' ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW'));
Toolbar::title(Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATES') . ': ' . $text, 'template.png');
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::help('index.php?option=com_help&component=com_newsletter&page=template', true);
?>

<?php $formAction = Route::url('index.php?option=' . $this->option); ?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="item-form">
    <div class="grid">
        <div class="col span8">
            <fieldset class="adminform">
                <?php $legendTxt = Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATES') . ': ' . $text; ?>
                <legend><span><?php echo $legendTxt; ?></span></legend>

                <div class="input-wrap">
                    <label for="field-name"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_NAME'); ?>:</label><br />
                    <input
                        type="text"
                        name="fields[name]"
                        id="field-name"
                        value="<?php echo $this->escape($this->template->name); ?>"
                    />
                </div>

                <?php
                $colorHint = Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT');
                $priTitleLabel = Lang::txt('COM_NEWSLETTER_TEMPLATE_PRIMARY_TITLE_COLOR');
                $priTextLabel = Lang::txt('COM_NEWSLETTER_TEMPLATE_PRIMARY_TEXT_COLOR');
                $secTitleLabel = Lang::txt('COM_NEWSLETTER_TEMPLATE_SECONDARY_TITLE_COLOR');
                $secTextLabel = Lang::txt('COM_NEWSLETTER_TEMPLATE_SECONDARY_TEXT_COLOR');
                ?>
                <fieldset class="adminform">
                    <legend><span><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_PRIMARY'); ?></span></legend>

                    <div class="input-wrap" data-hint="<?php echo $colorHint; ?>">
                        <label for="field-primary_title_color"><?php echo $priTitleLabel; ?>:</label><br />
                        <span class="hint"><?php echo $colorHint; ?></span>
                        <input
                            type="text"
                            name="fields[primary_title_color]"
                            id="field-primary_title_color"
                            value="<?php echo $this->escape($this->template->primary_title_color); ?>"
                        />
                    </div>

                    <div class="input-wrap" data-hint="<?php echo $colorHint; ?>">
                        <label for="field-primary_text_color"><?php echo $priTextLabel; ?>:</label><br />
                        <span class="hint"><?php echo $colorHint; ?></span>
                        <input
                            type="text"
                            name="fields[primary_text_color]"
                            id="field-primary_text_color"
                            value="<?php echo $this->escape($this->template->primary_text_color); ?>"
                        />
                    </div>
                </fieldset>

                <fieldset class="adminform">
                    <legend><span><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_SECONDARY'); ?></span></legend>

                    <div class="input-wrap" data-hint="<?php echo $colorHint; ?>">
                        <label for="field-secondary_title_color"><?php echo $secTitleLabel; ?>:</label><br />
                        <span class="hint"><?php echo $colorHint; ?></span>
                        <input
                            type="text"
                            name="fields[secondary_title_color]"
                            id="field-secondary_title_color"
                            value="<?php echo $this->escape($this->template->secondary_title_color); ?>"
                        />
                    </div>

                    <div class="input-wrap" data-hint="<?php echo $colorHint; ?>">
                        <label for="field-secondary_text_color"><?php echo $secTextLabel; ?>:</label><br />
                        <span class="hint"><?php echo $colorHint; ?></span>
                        <input
                            type="text"
                            name="fields[secondary_text_color]"
                            id="field-secondary_text_color"
                            value="<?php echo $this->escape($this->template->secondary_text_color); ?>"
                        />
                    </div>
                </fieldset>

                <div class="input-wrap">
                    <label for="field-template">
                        <?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_TEMPLATE') ?>:
                    </label><br />
                    <textarea
                        name="fields[template]"
                        id="field-template"
                        cols="100"
                        rows="30"
                    ><?php echo $this->escape($this->template->template); ?></textarea>
                </div>
            </fieldset>
        </div>
        <div class="col span4">
            <?php if ($this->config->get('template_tips')) : ?>
                <?php $tipsUrl = $this->config->get('template_tips'); ?>
                <span class="hint">
                    <?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_TIPS') ?><br />
                    <a href="<?php echo $tipsUrl; ?>">
                        <?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_TIPS_HINT'); ?>
                    </a>
                </span>
                <br /><br />
            <?php endif; ?>
            <?php if ($this->config->get('template_templates')) : ?>
                <?php $examplesUrl = $this->config->get('template_templates'); ?>
                <span class="hint">
                    <?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_EXAMPLES'); ?><br />
                    <a href="<?php echo $examplesUrl; ?>">
                        <?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_EXAMPLES_HINT'); ?>
                    </a>
                </span>
                <br /><br />
            <?php endif; ?>
            <span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_PLACEHOLDERS'); ?></span><br />
            <?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_PLACEHOLDERS_HINT'); ?>
        </div>
    </div>

    <input type="hidden" name="fields[id]" value="<?php echo $this->template->id; ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>