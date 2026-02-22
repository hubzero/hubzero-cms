<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
    ->js()
    ->js('setup')
    ->css('jquery.fancybox.css', 'system');

// Display page title
$this->view('_title')
     ->set('model', $this->model)
     ->set('step', $this->step)
     ->set('option', $this->option)
     ->set('title', $this->title)
     ->display();
?>

<section class="main section" id="setup">
    <?php
    // Display status message
    $this->view('_statusmsg', 'projects')
         ->set('error', $this->getError())
         ->set('msg', $this->msg)
         ->display();

    // Display metadata
    $this->view('_metadata')
         ->set('model', $this->model)
         ->set('step', $this->step)
         ->set('option', $this->option)
         ->display();

    // Display steps
    $this->view('_steps')
         ->set('model', $this->model)
         ->set('step', $this->step)
         ->display();
    ?>
    <div class="clear"></div>
    <div class="setup-wrap">
        <form
            id="hubForm"
            method="post"
            action="<?php echo Route::url('index.php?option=' . $this->option); ?>"
            enctype="multipart/form-data">
            <div class="explaination">
                <h4><?php echo Lang::txt('COM_PROJECTS_HOWTO_TITLE_NAME_PROJECT'); ?></h4>
                <p><?php echo Lang::txt('COM_PROJECTS_HOWTO_NAME_PROJECT'); ?></p>
            </div>
            <fieldset>
                <legend><?php echo Lang::txt('COM_PROJECTS_PICK_NAME'); ?></legend>

                <?php // Display form fields
                $this->view('_form')
                     ->set('model', $this->model)
                     ->set('step', $this->step)
                     ->set('option', $this->option)
                     ->set('controller', 'setup')
                     ->set('section', $this->section)
                     ->display();
                ?>
                <input type="hidden" name="extended" id="extended" value="0" />
                <input type="hidden" name="verified" id="verified" value="0" />

                <div class="form-group">
                    <label for="field-title">
                        <?php
                        $langTxt1 = Lang::txt('COM_PROJECTS_TITLE');
                        ?>
                        <?php echo $langTxt1; ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
                        <span class="verification"></span>
                        <input
                            name="title"
                            maxlength="250"
                            id="field-title"
                            type="text"
                            value="<?php echo $this->escape($this->model->get('title')); ?>"
                            class="form-control verifyme"
                        />
                    </label>
                    <p class="hint"><?php echo Lang::txt('COM_PROJECTS_HINTS_TITLE'); ?></p>
                </div>

                <div class="form-group">
                    <label for="field-alias">
                        <?php
                        $langTxt2 = Lang::txt('COM_PROJECTS_ALIAS_NAME');
                        ?>
                        <?php echo $langTxt2; ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
                        <span class="verification"></span>
                        <?php
                        $verifyUrl = Route::url(
                            'index.php?option=com_projects'
                            . '&task=verify&no_html=1&ajax=1&text='
                        );
                        $suggestUrl = Route::url(
                            'index.php?option=com_projects'
                            . '&task=suggestalias&no_html=1&ajax=1&text='
                        );
                        $disabledAttr = $this->model->get('id')
                            ? ' disabled="disabled"' : '';
                        ?>
                        <input
                            name="name"
                            maxlength="30"
                            id="field-alias"
                            type="text"
                            value="<?php echo $this->model->get('alias'); ?>"
                            <?php echo $disabledAttr; ?>
                            class="form-control verifyme"
                            data-verify="<?php echo $verifyUrl; ?>"
                            data-suggest="<?php echo $suggestUrl; ?>"
                        />
                    </label>
                    <p class="hint"><?php echo Lang::txt('COM_PROJECTS_HINTS_NAME'); ?></p>
                </div>

                <div id="moveon" class="nogo">
                    <p class="submitarea">
                        <?php $saveContinueTxt = Lang::txt('COM_PROJECTS_SAVE_AND_CONTINUE'); ?>
                        <input
                            type="submit"
                            value="<?php echo $saveContinueTxt; ?>"
                            class="btn disabled"
                            disabled="disabled"
                        />
                    </p>
                </div>

                <div id="describe">
                    <h2><?php echo Lang::txt('COM_PROJECTS_DESCRIBE_PROJECT'); ?></h2>
                    <p class="question"><?php echo Lang::txt('COM_PROJECTS_QUESTION_DESCRIBE_NOW_OR_LATER'); ?></p> 
                    <p>
                        <?php
                        $saveUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&task=save&id=' . $this->model->get('id')
                        );
                        $descYes = Lang::txt('COM_PROJECTS_QUESTION_DESCRIBE_YES');
                        $descNo = Lang::txt('COM_PROJECTS_QUESTION_DESCRIBE_NO');
                        ?>
                        <a
                            href="<?php echo $saveUrl; ?>"
                            id="next_desc"
                            class="btn btn-success"
                        ><?php echo $descYes; ?></a>
                        <a
                            href="<?php echo $saveUrl; ?>"
                            id="next_step"
                            class="btn"
                        ><?php echo $descNo; ?></a>
                    </p>
                </div>
            </fieldset>
            <div class="clear"></div>

            <div id="describearea">
                <div class="explaination">
                    <h4><?php echo Lang::txt('COM_PROJECTS_HOWTO_TITLE_DESC'); ?></h4>
                    <p><?php echo Lang::txt('COM_PROJECTS_HOWTO_DESC_PROJECT'); ?></p>
                </div>
                <fieldset>
                    <legend><?php echo Lang::txt('COM_PROJECTS_DESCRIBE_PROJECT'); ?></legend>

                    <div class="form-group">
                        <label for="field-about">
                            <?php
                            $langTxt9 = Lang::txt('COM_PROJECTS_ABOUT');
                            ?>
                            <?php echo $langTxt9; ?> <span class="optional"><?php echo Lang::txt('OPTIONAL'); ?></span>
                            <?php echo $this->editor(
                                'about',
                                $this->escape($this->model->about('raw')),
                                35,
                                25,
                                'field-about',
                                array('class' => 'form-control minimal no-footer')
                            ); ?>
                        </label>
                    </div>
                </fieldset>

                <fieldset>
                    <legend><?php echo Lang::txt('COM_PROJECTS_SETTING_APPEAR_IN_SEARCH'); ?></legend>

                    <?php
                    $accessVal = $this->model->get('access');
                    $langTxt10 = Lang::txt('COM_PROJECTS_PRIVACY_EDIT_PRIVATE');
                    $checkedPrivate = $accessVal == 5 ? 'checked="checked"' : '';
                    ?>
                    <div class="form-group form-check">
                        <label>
                            <input
                                class="option"
                                name="access"
                                type="radio"
                                value="5"
                                <?php echo $checkedPrivate; ?>
                            /> <?php echo $langTxt10; ?>
                        </label>
                    </div>

                    <?php
                    $langTxt11 = Lang::txt('COM_PROJECTS_PRIVACY_EDIT_PUBLIC');
                    $checkedPublic = $accessVal != 5 ? 'checked="checked"' : '';
                    ?>
                    <div class="form-group form-check">
                        <label>
                            <input
                                class="option"
                                name="access"
                                type="radio"
                                value="1"
                                <?php echo $checkedPublic; ?>
                            /> <?php echo $langTxt11; ?>
                        </label>
                    </div>
                </fieldset>

                <?php if ($this->model->get('id')) : ?>
                    <div class="js">
                        <div class="clear"></div>
                        <div class="explaination">
                            <h4><?php echo Lang::txt('COM_PROJECTS_HOWTO_TITLE_THUMB'); ?></h4>
                            <p><?php echo Lang::txt('COM_PROJECTS_HOWTO_THUMB'); ?></p>
                        </div>
                        <fieldset>
                            <legend><?php echo Lang::txt('COM_PROJECTS_ADD_PICTURE'); ?></legend>

                            <?php
                            // Display project image upload
                            $this->view('_picture')
                                 ->set('model', $this->model)
                                 ->set('step', $this->step)
                                 ->set('option', $this->option)
                                 ->display();
                            ?>
                        </fieldset>
                    </div>
                <?php endif; ?>

                <div class="submitarea">
                    <?php $saveContinueTxt2 = Lang::txt('COM_PROJECTS_SAVE_AND_CONTINUE'); ?>
                    <input
                        type="submit"
                        value="<?php echo $saveContinueTxt2; ?>"
                        class="btn btn-success"
                        id="gonext"
                    />
                </div>
            </div>
        </form>
    </div>
</section>