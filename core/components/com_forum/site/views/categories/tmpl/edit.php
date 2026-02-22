<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css();
$allCategoriesUrl = Route::url('index.php?option=' . $this->option);
?>
    <header id="content-header">
        <h2><?php echo Lang::txt('COM_FORUM'); ?></h2>

        <div id="content-header-extra">
            <p>
                <a class="icon-folder categories btn"
                    href="<?php echo $allCategoriesUrl; ?>">
                    <?php echo Lang::txt('COM_FORUM_ALL_CATEGORIES'); ?>
                </a>
            </p>
        </div>
    </header>

    <section class="main section">
        <?php $formAction = Route::url('index.php?option=' . $this->option); ?>
        <form action="<?php echo $formAction; ?>"
            method="post"
            id="hubForm">
            <div class="explaination">
                <p>
                    <strong><?php echo Lang::txt('COM_FORUM_WHAT_IS_LOCKING'); ?></strong>
                    <br />
                    <?php echo Lang::txt('COM_FORUM_LOCKING_EXPLANATION'); ?>
                </p>
            </div><!-- / .explaination -->
            <fieldset>
                <legend>
                    <?php if ($this->category->get('id')) { ?>
                        <?php echo Lang::txt('COM_FORUM_EDIT_CATEGORY'); ?>
                    <?php } else { ?>
                        <?php echo Lang::txt('COM_FORUM_NEW_CATEGORY'); ?>
                    <?php } ?>
                </legend>

                <div class="grid">
                    <div class="col span6">
                        <div class="form-group">
                            <?php
                            $closedChecked = $this->category->get('closed')
                                ? ' checked="checked"'
                                : '';
                            ?>
                            <label for="field-closed" id="comment-anonymous-label">
                                <input class="option form-control"
                                    type="checkbox"
                                    name="fields[closed]"
                                    id="field-closed"
                                    value="3"<?php echo $closedChecked; ?> />
                                <?php echo Lang::txt('COM_FORUM_FIELD_CLOSED'); ?>
                            </label>
                        </div>
                    </div>
                    <div class="col span6 omega">
                        <div class="form-group">
                            <label for="field-access">
                                <?php echo Lang::txt('COM_FORUM_FIELD_VIEW_ACCESS'); ?>
                                <?php
                                $sel1 = ($this->category->get('access') == 1)
                                    ? ' selected="selected"'
                                    : '';
                                $sel2 = ($this->category->get('access') == 2)
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
                    </div>
                </div>

                <div class="form-group">
                    <label for="field-section_id">
                        <?php echo Lang::txt('COM_FORUM_FIELD_SECTION'); ?>
                        <span class="required">
                            <?php echo Lang::txt('COM_FORUM_REQUIRED'); ?>
                        </span>
                        <select class="form-control"
                            name="fields[section_id]"
                            id="field-section_id">
                            <?php
                            $sections = $this->forum->sections(array('state' => 1))->rows();
                            foreach ($sections as $section) {
                                $selected = '';
                                $secId = $section->get('id');
                                if ($this->category->get('section_id') == $secId) {
                                    $selected = ' selected="selected"';
                                }
                                $secTitle = $this->escape(
                                    stripslashes($section->get('title'))
                                );
                                ?>
                                <option value="<?php echo $secId; ?>"<?php echo $selected; ?>>
                                    <?php echo $secTitle; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </label>
                </div>

                <div class="form-group">
                    <label for="field-title">
                        <?php echo Lang::txt('COM_FORUM_FIELD_TITLE'); ?>
                        <span class="required">
                            <?php echo Lang::txt('COM_FORUM_REQUIRED'); ?>
                        </span>
                        <?php
                        $titleVal = $this->escape(
                            stripslashes($this->category->get('title', ''))
                        );
                        ?>
                        <input type="text"
                            class="form-control"
                            name="fields[title]"
                            id="field-title"
                            value="<?php echo $titleVal; ?>" />
                    </label>
                </div>

                <div class="form-group">
                    <label for="field-description">
                        <?php echo Lang::txt('COM_FORUM_FIELD_DESCRIPTION'); ?>
                        <?php
                        $descVal = $this->escape(
                            stripslashes($this->category->get('description', ''))
                        );
                        ?>
                        <textarea class="form-control"
                            name="fields[description]"
                            id="field-description"
                            cols="35"
                            rows="5"><?php echo $descVal; ?></textarea>
                    </label>
                </div>
            </fieldset>
            <div class="clear"></div>

            <p class="submit">
                <input class="btn btn-success"
                    type="submit"
                    value="<?php echo Lang::txt('JSUBMIT'); ?>" />

                <a class="btn btn-secondary"
                    href="<?php echo $allCategoriesUrl; ?>">
                    <?php echo Lang::txt('JCANCEL'); ?>
                </a>
            </p>

            <?php $aliasVal = $this->category->get('alias'); ?>
            <input type="hidden"
                name="fields[alias]"
                value="<?php echo $aliasVal; ?>" />
            <input type="hidden"
                name="fields[id]"
                value="<?php echo $this->category->get('id'); ?>" />
            <input type="hidden"
                name="fields[state]"
                value="<?php echo $this->category->get('state', 1); ?>" />
            <input type="hidden" name="fields[scope]" value="site" />
            <input type="hidden" name="fields[scope_id]" value="0" />

            <input type="hidden"
                name="option"
                value="<?php echo $this->option; ?>" />
            <input type="hidden" name="controller" value="categories" />
            <input type="hidden" name="task" value="save" />

            <?php echo Html::input('token'); ?>
        </form>
    </section><!-- / .below section -->
