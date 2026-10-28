<?php

// @phpcs:disable PSR1.Files.SideEffects
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('jquery.datepicker.css', 'system')
    ->css('jquery.timepicker.css', 'system')
    ->css()
    ->js('jquery.timepicker.js', 'system')
    ->js();
?>
<section class="main section">
    <?php if ($this->getError()) { ?>
        <p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
    <?php } ?>
    <form action="<?php echo Route::url($this->offering->link() . '&active=announcements'); ?>" method="post"
        id="hubForm" class="full">
        <fieldset>
            <legend>
                <?php if ($this->model->get('id')) { ?>
                    <?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_EDIT'); ?>
                <?php } else { ?>
                    <?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_NEW'); ?>
                <?php } ?>
            </legend>

            <label for="field_content">
                <?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_FIELD_CONTENT'); ?> <span
                    class="required"><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_REQUIRED'); ?></span>
                <?php
                $editorContent = $this->escape($this->model->content('raw'));
                echo $this->editor(
                    'fields[content]',
                    $editorContent,
                    35,
                    5,
                    'field_content',
                    array('class' => 'minimal no-footer')
                );
                ?>
            </label>

            <fieldset>
                <legend><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_FIELD_PUBLISH_WINDOW'); ?></legend>
                <p><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_HINT'); ?></p>
                <div class="grid">
                    <div class="col span-half">
                        <label for="field-publish_up" id="priority-publish_up">
                            <?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_FIELD_START'); ?>
                            <?php
                            $pubUp = $this->model->get('publish_up');
                            $pubUpVal = ($pubUp && $pubUp != '0000-00-00 00:00:00')
                                ? $this->escape(Date::of($pubUp)->toSql())
                                : '';
                            ?>
                            <input class="datepicker" type="text"
                                name="fields[publish_up]"
                                id="field-publish_up"
                                value="<?php echo $pubUpVal; ?>" />
                            <span class="hint">
                                <?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_PUBLISH_HINT'); ?>
                            </span>
                        </label>
                    </div>
                    <div class="col span-half omega">
                        <label for="field-publish_down" id="priority-publish_down">
                            <?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_FIELD_END'); ?>
                            <?php
                            $pubDown = $this->model->get('publish_down');
                            $pubDownVal = ($pubDown && $pubDown != '0000-00-00 00:00:00')
                                ? $this->escape(Date::of($pubDown)->toSql())
                                : '';
                            ?>
                            <input class="datepicker" type="text"
                                name="fields[publish_down]"
                                id="field-publish_down"
                                value="<?php echo $pubDownVal; ?>" />
                            <span class="hint">
                                <?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_PUBLISH_HINT'); ?>
                            </span>
                        </label>
                    </div>
                </div>
            </fieldset>

            <label for="field-priority" id="priority-label">
                <input class="option"
                    type="checkbox"
                    name="fields[priority]"
                    id="field-priority"
                    value="1" <?php if ($this->model->get('priority')) {
                        echo ' checked="checked"';
                              } ?> />
                <?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_FIELD_PRIORITY'); ?>
            </label>
        </fieldset>
        <div class="clear"></div>

        <p class="submit">
            <input class="btn btn-success" type="submit"
                value="<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_SUBMIT'); ?>" />
            <a class="btn btn-secondary"
                href="<?php echo Route::url($this->offering->link() . '&active=announcements'); ?>">
                <?php echo Lang::txt('JCANCEL'); ?>
            </a>
        </p>

        <input type="hidden" name="fields[id]" value="<?php echo $this->model->get('id'); ?>" />
        <input type="hidden" name="fields[state]" value="1" />
        <input type="hidden" name="fields[offering_id]" value="<?php echo $this->offering->get('id'); ?>" />
        <input type="hidden" name="fields[section_id]" value="<?php echo $this->offering->section()->get('id'); ?>" />

        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
        <input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
        <input type="hidden" name="active" value="announcements" />
        <input type="hidden" name="action" value="save" />

        <?php echo Html::input('token'); ?>
    </form>
</section><!-- / .main section -->
