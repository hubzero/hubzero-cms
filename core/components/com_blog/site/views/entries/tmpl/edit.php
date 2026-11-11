<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Document;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Pathway;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

if (Pathway::count() <= 0) {
    Pathway::append(
        Lang::txt('COM_BLOG'),
        'index.php?option=' . $this->option,
    );
}
Pathway::append(
    ($this->entry->isNew() ? Lang::txt('JACTION_NEW') : Lang::txt('JACTION_EDIT')),
    $this->entry->link('edit'),
);

Document::setTitle(
    Lang::txt('COM_BLOG') . ': ' . ($this->entry->isNew() ? Lang::txt('JACTION_NEW') : Lang::txt('JACTION_EDIT')),
);

/*if ($this->entry->id) {
    $lid = $this->entry->id;
} else {
    $lid = time().rand(0,10000);
}*/
$this
     ->css()
     ->css('jquery.ui.css', 'system')
     ->js('jquery.timepicker.js', 'system')
     ->js();

if ($this->entry->get('publish_down') && $this->entry->get('publish_down') == '0000-00-00 00:00:00') {
    $this->entry->set('publish_down', '');
}

$view = $this;

// The attribute that marks the current value in the form fields below
$allowCommentsChecked = function ($value) use ($view) {
    if ($view->entry->get('allow_comments') == $value) {
        return ' checked="checked"';
    }

    return '';
};

$accessSelected = function ($value) use ($view) {
    if ($view->entry->get('access') == $value) {
        return ' selected="selected"';
    }

    return '';
};
?>
<header id="content-header">
    <h2>
        <?= Lang::txt('COM_BLOG') . ': '
            . ($this->entry->isNew() ? Lang::txt('JACTION_NEW') : Lang::txt('JACTION_EDIT')) ?>
    </h2>

    <div id="content-header-extra">
        <p><a class="icon-archive archive btn"
            href="<?= Route::url('index.php?option=' . $this->option) ?>"><?= Lang::txt('COM_BLOG_ARCHIVE') ?></a></p>
    </div>
</header>

<section class="main section">
    <div class="section-inner">
        <?php /*if ($this->getError()) { ?>
            <p class="error"><?php echo $this->getError(); ?></p>
        <?php }*/ ?>

        <form action="<?= Route::url('index.php?option=' . $this->option . '&task=save') ?>" method="post" id="hubForm">
            <div class="explaination">
                <h4 id="files-header"><?php echo Lang::txt('COM_BLOG_FIELD_FILES'); ?></h4>
                <?php
                $sourceUrl = Route::url('index.php?option=' . $this->option . '&tmpl=component&controller=media');
                ?>
                <iframe width="100%" height="370" name="filer" id="filer" title="<?= Lang::txt('COM_BLOG_FIELD_FILES') ?>" src="<?= $sourceUrl ?>"></iframe>
            </div>
            <fieldset>
                <legend>
                    <?= Lang::txt($this->entry->isNew() ? 'COM_BLOG_NEW_ENTRY' : 'COM_BLOG_EDIT_DETAILS') ?>
                </legend>

                <div class="form-group">
                    <label for="field-title"<?= $this->task == 'save' && !$this->entry->get('title') ? ' class="fieldWithErrors"' : '' ?>>
                        <?= Lang::txt('COM_BLOG_FIELD_TITLE') ?> <span
                            class="required"><?= Lang::txt('COM_BLOG_REQUIRED') ?></span>
                        <input type="text" class="form-control" name="entry[title]" id="field-title" size="35"
                            value="<?= $this->escape(stripslashes($this->entry->get('title', ''))) ?>" />
                    </label>
                    <?php if ($this->task == 'save' && !$this->entry->get('title')) { ?>
                        <p class="error"><?php echo Lang::txt('COM_BLOG_ERROR_PROVIDE_TITLE'); ?></p>
                    <?php } ?>
                </div>

                <div class="form-group">
                    <label for="entrycontent"<?= $this->task == 'save' && !$this->entry->get('content') ? ' class="fieldWithErrors"' : '' ?>>
                        <?= Lang::txt('COM_BLOG_FIELD_CONTENT') ?> <span
                            class="required"><?= Lang::txt('COM_BLOG_REQUIRED') ?></span>
                        <?= $this->editor(
                            'entry[content]',
                            $this->escape($this->entry->content('raw')),
                            50,
                            40,
                            'entrycontent',
                            ['class' => 'form-control'],
                        ) ?>
                    </label>
                    <?php if ($this->task == 'save' && !$this->entry->get('content')) { ?>
                        <p class="error"><?php echo Lang::txt('COM_BLOG_ERROR_PROVIDE_CONTENT'); ?></p>
                    <?php } ?>
                </div>

                <div class="form-group">
                    <label for="actags">
                        <?php echo Lang::txt('COM_BLOG_FIELD_TAGS'); ?>
                        <?php echo $this->autocompleter('tags', 'tags', $this->escape($this->entry->tags('string'))); ?>
                        <span class="hint"><?php echo Lang::txt('COM_BLOG_FIELD_TAGS_HINT'); ?></span>
                    </label>
                </div>

                <div class="grid">
                    <div class="col span-half">
                        <div class="form-group form-check">
                            <label for="field-allow_comments" class="form-check-label">
                                <input type="checkbox" class="option form-check-input" name="entry[allow_comments]"
                                    id="field-allow_comments" value="1"<?= $allowCommentsChecked(1) ?> />
                                <?php echo Lang::txt('COM_BLOG_FIELD_ALLOW_COMMENTS'); ?>
                            </label>
                        </div>
                    </div>

                    <div class="col span-half omega">
                        <div class="form-group">
                            <label for="field-access">
                                <?php echo Lang::txt('COM_BLOG_FIELD_PRIVACY'); ?>
                                <select class="form-control" name="entry[access]" id="field-access">
                                    <option value="1"<?= $accessSelected(1) ?>>
                                        <?= Lang::txt('COM_BLOG_FIELD_PRIVACY_PUBLIC') ?>
                                    </option>
                                    <option value="2"<?= $accessSelected(2) ?>>
                                        <?= Lang::txt('COM_BLOG_FIELD_PRIVACY_REGISTERED') ?>
                                    </option>
                                    <option value="5"<?= $this->entry->get('access') > 2 ? ' selected="selected"' : '' ?>><?= Lang::txt('COM_BLOG_FIELD_PRIVACY_PRIVATE') ?></option>
                                </select>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="grid">
                    <div class="col span-half">
                        <div class="form-group">
                            <label for="field-publish_up">
                                <?php echo Lang::txt('COM_BLOG_FIELD_PUBLISH_UP'); ?>
                                <input type="text" name="entry[publish_up]" class="form-control datetime-field" id="field-publish_up" data-timezone="<?= (timezone_offset_get(new DateTimeZone(Config::get('offset')), Date::getRoot()) / 60) ?>" value="<?= ($this->entry->get('publish_up')) ? $this->escape(Date::of($this->entry->get('publish_up'))->toLocal('Y-m-d H:i:s')) : '' ?>" />
                                <span class="hint"><?php echo Lang::txt('COM_BLOG_FIELD_PUBLISH_HINT'); ?></span>
                            </label>
                        </div>
                    </div>

                    <div class="col span-half omega">
                        <div class="form-group">
                            <label for="field-publish_down">
                                <?php echo Lang::txt('COM_BLOG_FIELD_PUBLISH_DOWN'); ?>
                                <input type="text" name="entry[publish_down]" class="form-control datetime-field" id="field-publish_down" data-timezone="<?= (timezone_offset_get(new DateTimeZone(Config::get('offset')), Date::getRoot()) / 60) ?>" value="<?= ($this->entry->get('publish_down')) ? $this->escape(Date::of($this->entry->get('publish_down'))->toLocal('Y-m-d H:i:s')) : '' ?>" />
                                <span class="hint"><?php echo Lang::txt('COM_BLOG_FIELD_PUBLISH_HINT'); ?></span>
                            </label>
                        </div>
                    </div>
                </div>
            </fieldset>
            <div class="clear"></div>

            <input type="hidden" name="lid" value="<?php //echo $lid;?>" />
            <input type="hidden" name="id" value="<?php echo $this->entry->get('id'); ?>" />
            <input type="hidden" name="entry[id]" value="<?php echo $this->entry->get('id'); ?>" />
            <input type="hidden" name="entry[alias]" value="<?php echo $this->entry->get('alias'); ?>" />
            <input type="hidden" name="entry[created]" value="<?php echo $this->entry->get('created'); ?>" />
            <input type="hidden" name="entry[created_by]" value="<?php echo $this->entry->get('created_by'); ?>" />
            <input type="hidden" name="entry[scope]" value="site" />
            <input type="hidden" name="entry[scope_id]" value="0" />
            <input type="hidden" name="entry[state]" value="<?php echo $this->entry->get('state', 1); ?>" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="task" value="save" />

            <?php echo Html::input('token'); ?>

            <p class="submit">
                <input class="btn btn-success" type="submit" value="<?php echo Lang::txt('JSAVE'); ?>" />

                <a class="btn btn-secondary" href="<?= $this->entry->get('id') ? Route::url($this->entry->link()) : Route::url('index.php?option=' . $this->option) ?>">
                    <?php echo Lang::txt('JCANCEL'); ?>
                </a>
            </p>
        </form>
    </div><!-- / .section-inner -->
</section><!-- / .section -->
