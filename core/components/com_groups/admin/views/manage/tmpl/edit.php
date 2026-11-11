<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Component;
use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->group->get('gidNumber') ? Lang::txt('COM_GROUPS_EDIT') : Lang::txt('COM_GROUPS_NEW'));

$canDo = \Components\Groups\Helpers\Permissions::getActions('group', $this->group->get('gidNumber'));

Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . $text, 'groups');
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('group');

Html::behavior('framework');
Html::behavior('switcher', 'submenu');

// are we using the email gateway for group forum
$params = Component::params('com_groups');
$emailForumComments = $params->get('email_forum_comments', 0);

$autoEmailResponses  = $this->group->get('discussion_email_autosubscribe');
if (is_null($autoEmailResponses)) {
    $autoEmailResponses = $params->get('email_member_groupsidcussionemail_autosignup', 0);
}
if (
    $this->group->get('discussion_email_autosubscribe', null) == 1
    || ($this->group->get('discussion_email_autosubscribe', null) == null && $autoEmailResponses)
) {
    $autoEmailResponses = 1;
}

// get groups params
$gparams              = new \Hubzero\Config\Registry($this->group->params);
$membership_control   = $gparams->get('membership_control', 1);
$display_system_users = $gparams->get('display_system_users', 'global');
$comments             = $gparams->get('page_comments', $params->get('page_comments', 0));
$author               = $gparams->get('page_author', $params->get('page_author', 0));
$trusted              = $gparams->get('page_trusted', $params->get('page_trusted', 0));

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$view = $this;

// The attribute that marks the current value in the form fields below
$typeSelected = function ($value) use ($view) {
    if ($view->group->type == $value) {
        return ' selected="selected"';
    }

    return '';
};

$publishedSelected = function ($value) use ($view) {
    if ($view->group->published == $value) {
        return 'selected="selected"';
    }

    return '';
};

$approvedSelected = function ($value) use ($view) {
    if ($view->group->approved == $value) {
        return 'selected="selected"';
    }

    return '';
};

$joinPolicyChecked = function ($value) use ($view) {
    if ($view->group->join_policy == $value) {
        return ' checked="checked"';
    }

    return '';
};

$discoverabilityChecked = function ($value) use ($view) {
    if ($view->group->discoverability == $value) {
        return ' checked="checked"';
    }

    return '';
};
?>

<?php if ($this->getErrors()) { ?>
    <p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<div id="item-form-wrap">
    <form action="<?= Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller) ?>"
        method="post" name="adminForm" id="item-form" class="editform form-validate"
        data-invalid-msg="<?= $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED')) ?>">
        <nav role="navigation" class="sub-navigation">
            <div id="submenu-box">
                <div class="submenu-box">
                    <div class="submenu-pad">
                        <ul id="submenu" class="coursesection">
                            <li><a href="#page-details" onclick="return false;" id="details"
                                class="active"><?= Lang::txt('JDETAILS') ?></a></li>
                            <li><a href="#page-files" onclick="return false;"
                                id="files"><?= Lang::txt('COM_GROUPS_MEDIA') ?></a></li>
                            <!-- <li><a href="#" onclick="return false;" id="pages"><?= Lang::txt('COM_COURSES_FIELDSET_PAGES') ?></a></li> -->
                        </ul>
                        <div class="clr"></div>
                    </div>
                </div>
                <div class="clr"></div>
            </div>
        </nav><!-- / .sub-navigation -->

        <div id="section-document">
            <div id="page-details" class="tab">

                <div class="grid">
                    <div class="col span7">
                        <fieldset class="adminform">
                            <legend><span><?php echo Lang::txt('COM_GROUPS_DETAILS'); ?></span></legend>

                            <input type="hidden" name="group[gidNumber]" value="<?= $this->group->gidNumber ?>" />
                            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                            <input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
                            <input type="hidden" name="task" value="save" />

                            <div class="input-wrap">
                                <label for="field-type"><?= Lang::txt('COM_GROUPS_TYPE') ?>: <span
                                    class="required"><?= Lang::txt('JOPTION_REQUIRED') ?></span></label><br />
                                <select name="group[type]" id="field-type" class="required">
                                    <option value="1"<?= $typeSelected('1') ?>>
                                        <?= Lang::txt('COM_GROUPS_TYPE_HUB') ?>
                                    </option>
                                    <option value="3"<?= $typeSelected('3') ?>>
                                        <?= Lang::txt('COM_GROUPS_TYPE_SUPER') ?>
                                    </option>
                                    <?php if ($canDo->get('core.admin')) { ?>
                                        <option value="0"<?= $typeSelected('0') ?>>
                                            <?= Lang::txt('COM_GROUPS_TYPE_SYSTEM') ?>
                                        </option>
                                    <?php } ?>
                                    <option value="2"<?= $typeSelected('2') ?>>
                                        <?= Lang::txt('COM_GROUPS_TYPE_PROJECT') ?>
                                    </option>
                                    <option value="4"<?= $typeSelected('4') ?>>
                                        <?= Lang::txt('COM_GROUPS_TYPE_COURSE') ?>
                                    </option>
                                </select>
                            </div>

                            <div class="grid">
                                <div class="col span6">
                                    <div class="input-wrap">
                                        <label for="field-published"><?= Lang::txt('COM_GROUPS_PUBLISHED') ?>:</label>
                                        <select name="group[published]" id="field-published">
                                            <option <?= $publishedSelected(0) ?> value="0">
                                                <?= Lang::txt('COM_GROUPS_UNPUBLISHED') ?>
                                            </option>
                                            <option <?= $publishedSelected(1) ?> value="1">
                                                <?= Lang::txt('COM_GROUPS_PUBLISHED') ?>
                                            </option>
                                            <option <?= $publishedSelected(2) ?> value="2">
                                                <?= Lang::txt('COM_GROUPS_ARCHIVED') ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col span6">
                                    <div class="input-wrap">
                                        <label for="field-approved"><?= Lang::txt('COM_GROUPS_APPROVE') ?>:</label>
                                        <select name="group[approved]" id="field-approved">
                                            <option <?= $approvedSelected(0) ?> value="0">
                                                <?= Lang::txt('COM_GROUPS_UNAPPROVED') ?>
                                            </option>
                                            <option <?= $approvedSelected(1) ?> value="1">
                                                <?= Lang::txt('COM_GROUPS_APPROVED') ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="input-wrap" data-hint="<?php echo Lang::txt('COM_GROUPS_CN_HINT'); ?>">
                                <label for="field-cn"><?= Lang::txt('COM_GROUPS_CN') ?>: <span
                                    class="required"><?= Lang::txt('JOPTION_REQUIRED') ?></span></label><br />
                                <input type="text" name="group[cn]" id="field-cn" class="required" <?= $this->group->cn ? ' readonly="readonly"' : '' ?> value="<?= $this->escape(stripslashes($this->group->cn ?? '')) ?>" />
                                <span class="hint"><?php echo Lang::txt('COM_GROUPS_CN_HINT'); ?></span>
                            </div>
                            <div class="input-wrap">
                                <label for="field-description"><?= Lang::txt('COM_GROUPS_TITLE') ?>:</label><br />
                                <input type="text" name="group[description]" id="field-description"
                                    value="<?= $this->escape(stripslashes($this->group->description ?? '')) ?>" />
                            </div>
                            <div class="input-wrap">
                                <label for="field-logo"><?php echo Lang::txt('COM_GROUPS_LOGO'); ?>:</label><br />
                                <input type="text" name="group[logo]" id="field-logo"
                                    value="<?= $this->escape($this->group->logo) ?>" />
                            </div>

                            <?php
                            $this->js('customfields');

                            $xml = Components\Groups\Models\Orm\Field::toXml($this->customFields);

                            $formInfo = ['control' => 'customfields'];

                            $form = new Hubzero\Form\Form('application', $formInfo);
                            $form->load($xml);
                            $form->bind($this->customAnswers);

                            foreach ($this->customFields as $field) {
                                $formfield = $form->getField($field->get('name'));

                                $hint = '';
                                if ($formfield->description && strtolower($formfield->type) != 'paragraph') {
                                    $hint = trim($formfield->description);
                                }

                                echo '<div class="input-wrap"'
                                    . ($hint ? ' data-hint="' . $this->escape($hint) . '"' : '') . '>';

                                if (strtolower($formfield->type) != 'paragraph') {
                                    echo $formfield->label;
                                }

                                if ($field->type == 'textarea') {
                                    $fieldName     = $field->get('name');
                                    $fieldValue    = isset($this->customAnswers[$fieldName])
                                        ? $this->customAnswers[$fieldName]
                                        : $field->get('default_value', '');
                                    $fieldNameAttr = $formInfo['control'] . '[' . $fieldName . ']';
                                    $fieldIdAttr   = $formInfo['control'] . '_' . $fieldName;

                                    echo $this->editor(
                                        $fieldNameAttr,
                                        $this->escape($fieldValue),
                                        35,
                                        8,
                                        $fieldIdAttr,
                                        ['class' => 'minimal no-footer images macros'],
                                    );
                                } else {
                                    echo $formfield->input;
                                }

                                if ($hint) {
                                    echo '<span class="hint">' . $hint . '</span>';
                                }
                                echo '</div>';
                            }
                            ?>
                        </fieldset>

                        <fieldset class="adminform">
                            <legend><span><?php echo Lang::txt('COM_GROUPS_PAGE_SETTINGS'); ?></span></legend>

                            <div class="input-wrap"
                                data-hint="<?= Lang::txt('COM_GROUPS_PAGES_SETTING_TRUSTEDCONTENT_HINT') ?>">
                                <?php
                                $labelText = Lang::txt('COM_GROUPS_PAGES_SETTING_TRUSTEDCONTENT');
                                ?>
                                <label for="param-page_trusted"><?= $labelText ?>:</label>
                                <select name="group[params][page_trusted]" id="param-page_trusted">
                                    <option <?= $trusted == 0 ? 'selected="selected"' : '' ?> value="0">
                                        <?= Lang::txt('COM_GROUPS_PAGES_PAGE_TRUSTEDCONTENT_NO') ?>
                                    </option>
                                    <option <?= $trusted == 1 ? 'selected="selected"' : '' ?> value="1">
                                        <?= Lang::txt('COM_GROUPS_PAGES_PAGE_TRUSTEDCONTENT_YES') ?>
                                    </option>
                                </select>
                            </div>

                            <div class="input-wrap"
                                data-hint="<?= Lang::txt('COM_GROUPS_PAGES_SETTING_COMMENTS_HINT') ?>">
                                <?php
                                $labelText = Lang::txt('COM_GROUPS_PAGES_SETTING_COMMENTS');
                                ?>
                                <label for="param-page_comments"><?= $labelText ?>:</label>
                                <select name="group[params][page_comments]" id="param-page_comments">
                                    <option <?= $comments == 0 ? 'selected="selected"' : '' ?> value="0">
                                        <?= Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_NO') ?>
                                    </option>
                                    <option <?= $comments == 1 ? 'selected="selected"' : '' ?> value="1">
                                        <?= Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_YES') ?>
                                    </option>
                                    <option <?= $comments == 2 ? 'selected="selected"' : '' ?> value="2">
                                        <?= Lang::txt('COM_GROUPS_PAGES_PAGE_COMMENTS_LOCK') ?>
                                    </option>
                                </select>
                            </div>

                            <div class="input-wrap"
                                data-hint="<?= Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_HINT') ?>">
                                <label
                                    for="param-page_author"><?= Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR') ?>:</label>
                                <select name="group[params][page_author]" id="param-page_author">
                                    <option <?= $author == 0 ? 'selected="selected"' : '' ?> value="0">
                                        <?= Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_NO') ?>
                                    </option>
                                    <option <?= $author == 1 ? 'selected="selected"' : '' ?> value="1">
                                        <?= Lang::txt('COM_GROUPS_PAGES_SETTING_AUTHOR_YES') ?>
                                    </option>
                                </select>
                            </div>
                            <div class="input-wrap" data-hint="<?php echo Lang::txt('COM_GROUPS_TEMPLATE_HINT'); ?>">
                                <label for="param-page_template"><?= Lang::txt('COM_GROUPS_TEMPLATE') ?>:</label>
                                <input type="text" name="group[params][page_template]"
                                    value="<?= $this->escape($gparams->get('page_template', '')) ?>"
                                    id="param-page_template" />
                                <span class="hint"><?php echo Lang::txt('COM_GROUPS_TEMPLATE_HINT'); ?></span>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col span5">
                        <table class="meta">
                            <tbody>
                                <tr>
                                    <th scope="row"><?php echo Lang::txt('COM_GROUPS_ID'); ?></th>
                                    <td><?php echo $this->escape($this->group->gidNumber); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php echo Lang::txt('COM_GROUPS_PUBLISHED'); ?></th>
                                    <td><?php echo ($this->group->published) ? 'Yes' : 'No'; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php echo Lang::txt('COM_GROUPS_APPROVED'); ?></th>
                                    <td><?php echo ($this->group->approved) ? 'Yes' : 'No'; ?></td>
                                </tr>
                                <?php if ($this->group->created) { ?>
                                    <tr>
                                        <th scope="row"><?php echo Lang::txt('COM_GROUPS_CREATED'); ?></th>
                                        <td>
                                            <?= $this->escape(
                                                date("l F d, Y @ g:ia", strtotime($this->group->created)),
                                            ) ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <?php if ($this->group->created_by) { ?>
                                    <tr>
                                        <th scope="row"><?php echo Lang::txt('COM_GROUPS_CREATED_BY'); ?></th>
                                        <td><?php
                                        $creator = User::getInstance($this->group->created_by);
                                        echo $this->escape($creator->get('name')); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <fieldset class="adminform">
                            <legend><span><?php echo Lang::txt('COM_GROUPS_MEMBERSHIP'); ?></span></legend>

                            <div class="input-wrap">
                                <input type="checkbox" name="group[params][membership_control]" id="field-membership_control" value="1" <?php if ($membership_control == 1) {
                                    ?>checked="checked"<?php
                                                                                                                                        } ?> />
                                <?php
                                $labelText = Lang::txt('COM_GROUPS_MEMBERSHIP_CONTROL');
                                ?>
                                <label for="field-membership_control"><?= $labelText ?></label>
                            </div>
                            <fieldset>
                                <legend><?php echo Lang::txt('COM_GROUPS_JOIN_POLICY'); ?>:</legend>
                                <div class="input-wrap">
                                    <?php
                                    $labelText = Lang::txt('COM_GROUPS_JOIN_POLICY_PUBLIC') . ' &mdash; '
                                        . Lang::txt('COM_GROUPS_JOIN_POLICY_PUBLIC_DESC');
                                    ?>
                                    <input type="radio" name="group[join_policy]" id="field-join_policy0"
                                        value="0"<?= $joinPolicyChecked(0) ?> /> <label
                                        for="field-join_policy0"><?= $labelText ?></label><br />
                                    <?php
                                    $labelText = Lang::txt('COM_GROUPS_JOIN_POLICY_RESTRICTED') . ' &mdash; '
                                        . Lang::txt('COM_GROUPS_JOIN_POLICY_RESTRICTED_DESC');
                                    ?>
                                    <input type="radio" name="group[join_policy]" id="field-join_policy1"
                                        value="1"<?= $joinPolicyChecked(1) ?> /> <label
                                        for="field-join_policy1"><?= $labelText ?></label><br />
                                    <?php
                                    $labelText = Lang::txt('COM_GROUPS_JOIN_POLICY_INVITE') . ' &mdash; '
                                        . Lang::txt('COM_GROUPS_JOIN_POLICY_INVITE_DESC');
                                    ?>
                                    <input type="radio" name="group[join_policy]" id="field-join_policy2"
                                        value="2"<?= $joinPolicyChecked(2) ?> /> <label
                                        for="field-join_policy2"><?= $labelText ?></label><br />
                                    <?php
                                    $labelText = Lang::txt('COM_GROUPS_JOIN_POLICY_CLOSED') . ' &mdash; '
                                        . Lang::txt('COM_GROUPS_JOIN_POLICY_CLOSED_DESC');
                                    ?>
                                    <input type="radio" name="group[join_policy]" id="field-join_policy3"
                                        value="3"<?= $joinPolicyChecked(3) ?> /> <label
                                        for="field-join_policy3"><?= $labelText ?></label>
                                </div>
                            </fieldset>
                            <div class="input-wrap">
                                <label for="restrict_msg"><?= Lang::txt('COM_GROUPS_EDIT_CREDENTIALS') ?>:</label><br />
                                <?= $this->editor('group[restrict_msg]', $this->escape(stripslashes($this->group->restrict_msg ? $this->group->restrict_msg : '')), 40, 10, 'restrict_msg', ['class' => 'minimal']) ?>
                            </div>
                        </fieldset>
                        <fieldset class="adminform">
                            <legend><span><?php echo Lang::txt('COM_GROUPS_ACCESS'); ?></span></legend>

                            <fieldset>
                                <legend><?php echo Lang::txt('COM_GROUPS_DISCOVERABILITY'); ?>:</legend>

                                <div class="input-wrap">
                                    <input type="radio" name="group[discoverability]" id="field-discoverability0"
                                        value="0"<?= $discoverabilityChecked(0) ?> />
                                    <?php
                                    $labelText = Lang::txt('COM_GROUPS_DISCOVERABILITY_VISIBLE');
                                    ?>
                                    <label for="field-discoverability0"><?= $labelText ?></label>
                                    <br />
                                    <input type="radio" name="group[discoverability]" id="field-discoverability1"
                                        value="1"<?= $discoverabilityChecked(1) ?> />
                                    <?php
                                    $labelText = Lang::txt('COM_GROUPS_DISCOVERABILITY_HIDDEN');
                                    ?>
                                    <label for="field-discoverability1"><?= $labelText ?></label>
                                </div>
                            </fieldset>

                            <div class="input-wrap">
                                <label for="field-plugins"><?= Lang::txt('COM_GROUPS_PLUGIN_ACCESS') ?>:</label><br />
                                <textarea name="group[plugins]" id="field-plugins" rows="10"
                                    cols="50"><?= $this->escape($this->group->plugins) ?></textarea>
                            </div>

                            <div class="input-wrap">
                                <?php
                                $labelText = Lang::txt('COM_GROUPS_SHOW_SYSTEM_USERS');
                                ?>
                                <label for="display_system_users"><?= $labelText ?>:</label><br />
                                <select name="group[params][display_system_users]" id="display_system_users">
                                    <option <?= $display_system_users == 'global' ? 'selected="selected"' : '' ?>
                                        value="global">
                                        <?= Lang::txt('COM_GROUPS_SHOW_SYSTEM_USERS_GLOBAL') ?>
                                    </option>
                                    <option <?= $display_system_users == 'no' ? 'selected="selected"' : '' ?>
                                        value="no">
                                        <?= Lang::txt('COM_GROUPS_SHOW_SYSTEM_USERS_NO') ?>
                                    </option>
                                    <option <?= $display_system_users == 'yes' ? 'selected="selected"' : '' ?>
                                        value="yes">
                                        <?= Lang::txt('COM_GROUPS_SHOW_SYSTEM_USERS_YES') ?>
                                    </option>
                                </select>
                            </div>
                        </fieldset>

                        <?php if ($emailForumComments) : ?>
                            <fieldset class="adminform">
                                <legend><span><?php echo Lang::txt('COM_GROUPS_EMAIL_SETTINGS'); ?></span></legend>

                                <fieldset>
                                    <legend><?php echo Lang::txt('COM_GROUPS_DISCUSSION_EMAILS'); ?>:</legend>

                                    <div class="input-wrap">
                                        <input type="hidden" name="group[discussion_email_autosubscribe]" value="0" />
                                        <input type="checkbox" name="group[discussion_email_autosubscribe]" id="field-membership_control" value="1" <?php if ($autoEmailResponses == 1) {
                                            ?>checked="checked"<?php
                                                                                                                                                    } ?> />
                                        <?php
                                        $labelText = Lang::txt('COM_GROUPS_DISCUSSION_EMAIL_AUTOSUBSCRIBE');
                                        ?>
                                        <label for="field-membership_control"><?= $labelText ?></label>
                                    </div>
                                </fieldset>
                            </fieldset>
                        <?php endif; ?>
                    </div>
                </div><!-- / .grid -->
            </div>

            <div id="page-files" class="tab">
                <fieldset class="adminform">
                    <?php if ($this->group->gidNumber) { ?>
                        <?php
                        $spanText = Lang::txt(
                            'COM_GROUPS_MEDIA_PATH',
                            substr(PATH_APP, strlen(PATH_ROOT)) . DS
                                . trim(Component::params('com_groups')->get('uploadpath', '/site/groups'), DS) . DS
                                . $this->group->get('gidNumber'),
                        );
                        ?>
                        <legend><span><?= $spanText ?></span></legend>
                        <?php
                        $sourceUrl = Route::url(
                            'index.php?option=' . $this->option . '&controller=media&tmpl=component&gidNumber='
                                . $this->group->gidNumber . '&t=' . Date::toUnix(),
                        );
                        ?>
                        <iframe width="100%" height="500" name="media" id="media" frameborder="0"
                            src="<?= $sourceUrl ?>"></iframe>
                    <?php } else { ?>
                        <p class="warning"><?php echo Lang::txt('COM_GROUPS_MEDIA_FILES_WARNING'); ?></p>
                    <?php } ?>
                </fieldset>
            </div>
        </div>

        <?php /*if ($canDo->get('core.admin')): ?>
        <div class="col span12">
            <fieldset class="panelform">
                <legend><span><?php echo Lang::txt('COM_GROUPS_FIELDSET_RULES'); ?></span></legend>
                <?php echo $this->form->getLabel('rules'); ?>
                <?php echo $this->form->getInput('rules'); ?>
            </fieldset>
        </div>
        <div class="clr"></div>
        <?php endif;*/ ?>

        <?php echo Html::input('token'); ?>
    </form>
</div>
