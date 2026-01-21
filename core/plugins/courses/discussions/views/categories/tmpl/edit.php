<?php

// @phpcs:disable PSR1.Files.SideEffects
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<section class="main section">
    <div class="subject">
        <h3 class="post-comment-title">
            <?php if ($this->category->get('id')) { ?>
                <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_EDIT_CATEGORY'); ?>
            <?php } else { ?>
                <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_NEW_CATEGORY'); ?>
            <?php } ?>
        </h3>

        <form action="<?php echo Route::url($this->offering->link() . '&active=discussions'); ?>"
            method="post"
            id="commentform">
            <p class="comment-member-photo">
                <img src="<?php echo User::picture(); ?>" alt="" />
            </p>

            <fieldset>
                <?php
                $sectionLabel = Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_SECTION');
                $requiredText = Lang::txt('PLG_COURSES_DISCUSSIONS_REQUIRED');
                $selectText = Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_SECTION_SELECT');
                ?>
                <label for="field-section_id">
                    <?php echo $sectionLabel; ?>
                    <span class="required"><?php echo $requiredText; ?></span>
                    <select name="fields[section_id]" id="field-section_id">
                        <option value="0"><?php echo $selectText; ?></option>
                        <?php
                        $sections = $this->forum->sections(array('state' => 1))->rows();
                        foreach ($sections as $section) {
                            $sectionId = $section->get('id');
                            $selected = ($this->category->get('section_id') == $sectionId)
                                ? ' selected="selected"'
                                : '';
                            $sectionTitle = $this->escape(stripslashes($section->get('title')));
                            ?>
                            <option
                                value="<?php echo $sectionId; ?>"<?php echo $selected; ?>
                            ><?php echo $sectionTitle; ?></option>
                        <?php } ?>
                    </select>
                </label>

                <?php $titleLabel = Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_TITLE'); ?>
                <label for="field-title">
                    <?php echo $titleLabel; ?>
                    <span class="required"><?php echo $requiredText; ?></span>
                    <input type="text"
                        name="fields[title]"
                        id="field-title"
                        value="<?php echo $this->escape(stripslashes($this->category->get('title', ''))); ?>"/>
                </label>

                <label for="field-description">
                    <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_DESCRIPTION'); ?>
                    <textarea name="fields[description]"
                        id="field-description"
                        cols="35"
                        rows="5"
                        ><?php echo $this->escape(stripslashes($this->category->get('description', ''))); ?></textarea>
                </label>

                <div class="grid">
                    <div class="col span6">
                        <label for="field-closed" id="comment-anonymous-label">
                            <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_LOCKED'); ?><br />
                            <input class="option"
                                type="checkbox"
                                name="fields[closed]"
                                id="field-closed"
                                value="3"<?php if ($this->category->get('closed')) {
                                    echo ' checked="checked"';
                                         } ?>
                                                                                                                   />
                            <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_FIELD_CLOSED'); ?>
                        </label>
                    </div>
                    <div class="col span6 omega">
                        <?php
                        $access = $this->category->get('access');
                        $sel1 = ($access == 1) ? ' selected="selected"' : '';
                        $sel2 = ($access == 2) ? ' selected="selected"' : '';
                        $sel5 = ($access == 5) ? ' selected="selected"' : '';
                        $optPublic = Lang::txt(
                            'PLG_COURSES_DISCUSSIONS_FIELD_READ_ACCESS_OPTION_PUBLIC'
                        );
                        $optRegistered = Lang::txt(
                            'PLG_COURSES_DISCUSSIONS_FIELD_READ_ACCESS_OPTION_REGISTERED'
                        );
                        $optPrivate = Lang::txt(
                            'PLG_COURSES_DISCUSSIONS_FIELD_READ_ACCESS_OPTION_PRIVATE'
                        );
                        ?>
                        <label for="field-access">
                            <?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_ACCESS_DESCRIPTION'); ?>:
                            <select name="fields[access]" id="field-access">
                                <option value="1"<?php echo $sel1; ?>><?php echo $optPublic; ?></option>
                                <option value="2"<?php echo $sel2; ?>><?php echo $optRegistered; ?></option>
                                <option value="5"<?php echo $sel5; ?>><?php echo $optPrivate; ?></option>
                            </select>
                        </label>
                    </div>
                </div>

                <p class="submit">
                    <input type="submit" value="<?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_SUBMIT'); ?>" />
                </p>
            </fieldset>
            <input type="hidden"
                name="fields[alias]"
                value="<?php echo $this->escape($this->category->get('alias')); ?>"/>
            <input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->category->get('id')); ?>" />
            <input type="hidden" name="fields[state]" value="1" />
            <input type="hidden"
                name="fields[scope]"
                value="<?php echo $this->escape($this->forum->get('scope')); ?>"/>
            <input type="hidden"
                name="fields[scope_id]"
                value="<?php echo $this->escape($this->forum->get('scope_id')); ?>"/>

            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
            <input type="hidden" name="offering" value="<?php echo $this->offering->alias(); ?>" />
            <input type="hidden" name="active" value="discussions" />
            <input type="hidden" name="unit" value="manage" />
            <input type="hidden" name="action" value="savecategory" />

            <?php echo Html::input('token'); ?>
        </form>
    </div><!-- / .subject -->
    <aside class="aside">
        <p><?php echo Lang::txt('PLG_COURSES_DISCUSSIONS_CATEGORY_HINT'); ?></p>
    </aside><!-- /.aside -->
</section><!-- / .main section -->
