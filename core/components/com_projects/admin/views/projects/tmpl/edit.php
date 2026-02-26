<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Plugin;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;
use Hubzero\Facades\Event;

// No direct access
defined('_HZEXEC_') or die();

// Push some styles to the template
Html::behavior('framework');
Html::behavior('switcher', 'submenu');

Hubzero\Document\Assets::addPluginStylesheet('projects', 'files', 'diskspace.css');
Hubzero\Document\Assets::addPluginScript('projects', 'files', 'diskspace.js');
Hubzero\Document\Assets::addPluginScript('projects', 'files');

// Connections enabled?
$p_params = Plugin::params('projects', 'files');

$service = 'google';
$cEnabled = $p_params->get('enable_' . $service, 0);
$connected = $this->params->get($service . '_token');

Toolbar::title(Lang::txt('Projects')
    . ': '
    . stripslashes($this->model->get('title'))
    . ' ('
    . $this->model->get('alias')
    . ', #'
    . $this->model->get('id')
    . ')', 'projects');

if (User::authorise('core.edit', $this->option)) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();

// Determine status & options
$status = '';

if ($this->model->isActive()) {
    $status   = '<span class="active">'
        . Lang::txt('COM_PROJECTS_ACTIVE')
        . '</span> '
        . Lang::txt('COM_PROJECTS_SINCE')
        . ' '
        . Date::of($this->model->get('created'))->toLocal();
} elseif ($this->model->isDeleted()) {
    $status  = '<span class="deleted">' . Lang::txt('COM_PROJECTS_DELETED') . '</span> ';
} elseif ($this->model->inSetup()) {
    $status  = '<span class="setup">' . Lang::txt('Setup') . '</span> ' . Lang::txt('in progress');
} elseif ($this->model->isInactive()) {
    $text = $this->suspended ? Lang::txt('COM_PROJECTS_SUSPENDED') : Lang::txt('COM_PROJECTS_INACTIVE');
    $status = '<span class="inactive">' . $text . '</span> ';
    if ($this->suspended) {
        $status .= $this->suspended == 1
            ? ' (' . Lang::txt('COM_PROJECTS_BY_ADMIN') . ')'
            : ' (' . Lang::txt('COM_PROJECTS_BY_PROJECT_MANAGER') . ')';
    }
} elseif ($this->model->isPending()) {
    $status  = '<span class="inactive">' . Lang::txt('COM_PROJECTS_PENDING_APPROVAL') . '</span> ';
}

$sysgroup = $this->config->get('group_prefix', 'pr-') . $this->model->get('alias');
$quota    = $this->params->get('quota');
$quota    = $quota
    ? $quota
    : \Components\Projects\Helpers\Html::convertSize(
        floatval($this->config->get('defaultQuota', '1')),
        'GB',
        'b'
    );

$pubQuota = $this->params->get('pubQuota');
$pubQuota = $pubQuota
    ? $pubQuota
    : \Components\Projects\Helpers\Html::convertSize(
        floatval($this->config->get('pubQuota', '1')),
        'GB',
        'b'
    );

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
$this->css();

// Get groups project owner belongs to
$groups = \Hubzero\User\Helper::getGroups($this->model->get('owned_by_user'), 'members', 1);
if ($this->model->groupOwner()) {
    $groups[] = $this->model->groupOwner();
}

?>

<?php
$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
$invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
?>
<form
    action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="editform form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
>

    <nav role="navigation" class="sub-navigation">
        <div id="submenu-box">
            <div class="submenu-box">
                <div class="submenu-pad">
                    <ul id="submenu" class="coursesection">
                        <li><a href="#page-details" onclick="return false;" id="details" class="active">
                            <?php echo Lang::txt('JDETAILS'); ?></a></li>
                        <?php
                        $langTxt4 = Lang::txt('COM_PROJECTS_IMAGE');
                        ?>
                        <li><a href="#page-images" onclick="return false;" id="images"><?php echo $langTxt4; ?></a></li>
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
                <legend><span><?php echo Lang::txt('COM_PROJECTS_BASIC_INFO'); ?></span></legend>

                <div class="input-wrap">
                    <label for="title"><?php echo Lang::txt('COM_PROJECTS_TITLE'); ?>
                        : <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
                    <input
                        type="text"
                        name="title"
                        id="title"
                        size="60"
                        maxlength="250"
                        class="required"
                        value="<?php echo $this->escape(stripslashes($this->model->get('title'))); ?>"
                    />
                </div>

                <div class="input-wrap">
                    <label for="alias"><?php echo Lang::txt('COM_PROJECTS_ALIAS'); ?>:</label>
                    <input
                        type="text"
                        name="alias"
                        id="alias"
                        size="60"
                        maxlength="250"
                        value="<?php echo $this->escape($this->model->get('alias')); ?>"
                        readonly="readonly"
                        disabled="disabled"
                    />
                </div>

                <div class="input-wrap">
                    <label for="about"><?php echo Lang::txt('COM_PROJECTS_ABOUT'); ?>:</label>
                    <?php echo $this->editor('about', $this->model->about('raw'), 35, 25, 'about'); ?>
                </div>

                <div class="input-wrap">
                    <label for="tags"><?php echo Lang::txt('COM_PROJECTS_TAGS'); ?>:</label>
                    <?php
                    $tf = Event::trigger(
                        'hubzero.onGetMultiEntry',
                        array(array('tags', 'tags', 'actags', '', $this->tags))
                    );

                    if (count($tf) > 0) {
                        echo $tf[0];
                    } else { ?>
                        <input type="text" name="tags" id="tags" value="<?php echo $this->escape($this->tags); ?>" />
                    <?php } ?>
                </div>

                <?php if (Plugin::isEnabled('projects', 'tools') or $this->publishing) { ?>
                    <div class="input-wrap">
                        <?php echo Lang::txt('COM_PROJECTS_TYPE'); ?>
                        <select name="type">
                            <?php foreach ($this->types as $type) {
                                if (
                                    ($type->id == 3 && !$this->publishing) ||
                                    ($type->id == 2 && !Plugin::isEnabled('projects', 'tools'))
                                ) {
                                    continue;
                                }
                                $selected = $type->id == $this->model->get('type') ? ' selected="selected"' : '';
                                ?>
                                <option value="<?php echo $type->id; ?>
                                    " <?php echo $selected; ?>><?php echo $type->type ?></option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } ?>

                <div class="input-wrap">
                    <label for="owned_by_user">
                        <?php echo Lang::txt('COM_PROJECTS_OWNER_LEAD'); ?>:
                        <select name="owned_by_user" class="block">
                            <?php
                            $ownerId = $this->model->get('owned_by_user');
                            $teamFilters = array('status' => 1);
                            foreach ($this->model->team($teamFilters, true) as $member) {
                                $isOwner = ($member->userid == $ownerId);
                                $sel = $isOwner ? ' selected="selected"' : '';
                                $ownerSuffix = $isOwner
                                    ? ' (' . Lang::txt('PLG_PROJECTS_TEAM_CURRENT_OWNER') . ')'
                                    : '';
                                ?>
                                <option
                                    value="<?php echo $member->userid; ?>"
                                    <?php echo $sel; ?>
                                ><?php echo $member->fullname . $ownerSuffix; ?></option>
                            <?php } ?>
                        </select>
                    </label>
                </div>

                <?php if (!empty($groups)) {
                    $used = array();
                    ?>
                    <div class="input-wrap">
                        <label for="owned_by_group">
                            <?php echo Lang::txt('PLG_PROJECTS_TEAM_CHANGE_OWNER_CHOOSE_GROUP'); ?>:
                            <select name="owned_by_group" class="block">
                                <option value="0" <?php if (!$this->model->groupOwner()) {
                                    echo 'selected="selected"';
                                                  } ?>><?php echo Lang::txt('PLG_PROJECTS_TEAM_NO_GROUP'); ?></option>
                                <?php foreach ($groups as $g) {
                                    if (in_array($g->gidNumber, $used)) {
                                        continue;
                                    }
                                    $used[] = $g->gidNumber; ?>
                                    <option value="<?php echo $g->gidNumber; ?>
                                        " <?php if ($g->gidNumber == $this->model->get('owned_by_group')) {
                                            echo 'selected="selected"';
                                          } ?>><?php echo \Hubzero\Utility\Str::truncate($g->description, 30)
                                                       . ' ('
                                                       . $g->cn
                                                       . ')'; ?></option>
                                <?php } ?>
                            </select>
                        </label>
                    </div>
                <?php } ?>

                <div class="input-wrap">
                    <label><?php echo Lang::txt('COM_PROJECTS_SYS_GROUP'); ?>:</label>
                    <?php echo $sysgroup; ?>
                </div>
            </fieldset>

            <fieldset class="adminform">
                <legend><?php echo Lang::txt('COM_PROJECTS_PARAMETERS'); ?></legend>

                <div class="input-wrap">
                    <?php
                    /*
                    <label><?php echo Lang::txt('COM_PROJECTS_PRIVACY'); ?>:</label>
                    <select name="private">
                        <option value="-1"
                            <?php if ($this->model->get('private') < 0) {
                                echo ' selected="selected"';
                            } ?>
                        ><?php echo Lang::txt('COM_PROJECTS_OPEN'); ?></option>
                        <option value="0"
                            <?php if ($this->model->get('private') == 0) {
                                echo ' selected="selected"';
                            } ?>
                        ><?php echo Lang::txt('COM_PROJECTS_PUBLIC'); ?></option>
                        <option value="1"
                            <?php if (!$this->model->isPublic()) {
                                echo ' selected="selected"';
                            } ?>
                        ><?php echo Lang::txt('COM_PROJECTS_PRIVATE'); ?></option>
                    </select>
                    */ ?>
                    <label for="field-access"><?php echo Lang::txt('COM_PROJECTS_PRIVACY'); ?>:</label>
                    <select name="access" id="field-access">
                        <?php echo Html::select(
                            'options',
                            Html::access('assetgroups'),
                            'value',
                            'text',
                            $this->model->get('access')
                        ); ?>
                    </select>
                </div>

                <div class="input-wrap">
                    <input type="hidden" name="params[team_public]" value="0" />
                    <input
                        type="checkbox"
                        class="option"
                        name="params[team_public]"
                        id="param-team_public"
                        value="1"
                        <?php if ($this->params->get('team_public')) {
                            echo ' checked="checked"';
                        } ?> />
                    <label for="param-team_public"><?php echo Lang::txt('COM_PROJECTS_TEAM_PUBLIC'); ?></label>
                </div>
                <div class="input-wrap">
                    <input type="hidden" name="params[publications_public]" value="0" />
                    <input
                        type="checkbox"
                        class="option"
                        name="params[publications_public]"
                        id="param-publications_public"
                        value="1"
                        <?php if ($this->params->get('publications_public')) {
                            echo ' checked="checked"';
                        } ?> />
                    <?php
                    $langTxt10 = Lang::txt('COM_PROJECTS_PUBLICATIONS_PUBLIC');
                    ?>
                    <label for="param-publications_public"><?php echo $langTxt10; ?></label>
                </div>
                <div class="input-wrap">
                    <label for="param-layout"><?php echo Lang::txt('COM_PROJECTS_LAYOUT'); ?>:</label>
                    <select name="params[layout]" id="param-layout">
                        <option value="standard" <?php if ($this->params->get('layout', 'standard') == 'standard') {
                            echo ' selected="selected"';
                                                 } ?>><?php echo Lang::txt('COM_PROJECTS_LAYOUT_STANDARD'); ?></option>
                        <option value="extended" <?php if ($this->params->get('layout') == 'extended') {
                            echo ' selected="selected"';
                                                 } ?>><?php echo Lang::txt('COM_PROJECTS_LAYOUT_EXTENDED'); ?></option>
                    </select>
                </div>

                <?php if ($this->config->get('restricted_data', 0)) { ?>
                    <div class="input-wrap">
                        <label><?php echo Lang::txt('COM_PROJECTS_SENSITIVE_DATA'); ?>:</label>
                        <?php echo strtoupper($this->params->get('restricted_data', 'no')); ?>
                        <?php if ($this->params->get('restricted_data') == 'yes') {
                            ?> (
                            <?php if ($this->params->get('hipaa_data')  == 'yes') {
                                echo 'HIPAA';
                            } ?>
                            <?php if ($this->params->get('ferpa_data')  == 'yes') {
                                echo 'FERPA';
                            } ?>
                            <?php if ($this->params->get('export_data') == 'yes') {
                                echo 'Export Controlled';
                            } ?>
                            <?php if ($this->params->get('irb_data') == 'yes') {
                                echo 'IRB';
                            } ?>
                            )
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if ($this->config->get('grantinfo', 0)) { ?>
                    <div class="input-wrap">
                        <?php
                        $langTxt11 = Lang::txt('COM_PROJECTS_TERMS_GRANT_TITLE');
                        ?>
                        <label for="param-grant_title"><?php echo $langTxt11; ?>:</label>
                        <?php
                        $grantTitleVal = $this->escape(
                            html_entity_decode($this->params->get('grant_title', ''))
                        );
                        ?>
                        <input
                            name="params[grant_title]"
                            id="param-grant_title"
                            maxlength="250"
                            type="text"
                            value="<?php echo $grantTitleVal; ?>"
                            class="long"
                        />
                    </div>
                    <div class="input-wrap">
                        <label for="param-grant_PI"><?php echo Lang::txt('COM_PROJECTS_TERMS_GRANT_PI'); ?>:</label>
                        <?php $grantPiVal = $this->escape(html_entity_decode($this->params->get('grant_PI', ''))); ?>
                        <input
                            name="params[grant_PI]"
                            id="param-grant_PI"
                            maxlength="250"
                            type="text"
                            value="<?php echo $grantPiVal; ?>"
                            class="long"
                        />
                    </div>
                    <div class="input-wrap">
                        <?php
                        $langTxt12 = Lang::txt('COM_PROJECTS_SETUP_TERMS_AWARD_NUMBER');
                        ?>
                        <label for="param-award_number"><?php echo $langTxt12; ?>:</label>
                        <?php
                        $awardNumVal = $this->escape(
                            html_entity_decode($this->params->get('award_number', ''))
                        );
                        ?>
                        <input
                            name="params[award_number]"
                            id="param-award_number"
                            maxlength="250"
                            type="text"
                            value="<?php echo $awardNumVal; ?>"
                            class="long"
                        />
                    </div>
                    <div class="input-wrap">
                        <?php
                        $langTxt13 = Lang::txt('COM_PROJECTS_TERMS_GRANT_AGENCY');
                        ?>
                        <label for="param-grant_agency"><?php echo $langTxt13; ?>:</label>
                        <?php $agencyVal = $this->escape(html_entity_decode($this->params->get('grant_agency', ''))); ?>
                        <input
                            name="params[grant_agency]"
                            id="param-grant_agency"
                            maxlength="250"
                            type="text"
                            value="<?php echo $agencyVal; ?>"
                            class="long"
                        />
                    </div>
                    <div class="input-wrap">
                        <?php
                        $langTxt14 = Lang::txt('COM_PROJECTS_TERMS_GRANT_BUDGET');
                        ?>
                        <label for="param-grant_budget"><?php echo $langTxt14; ?>:</label>
                        <?php $budgetVal = $this->escape(html_entity_decode($this->params->get('grant_budget', ''))); ?>
                        <input
                            name="params[grant_budget]"
                            id="param-grant_budget"
                            maxlength="250"
                            type="text"
                            value="<?php echo $budgetVal; ?>"
                            class="long"
                        />
                    </div>
                    <div class="input-wrap">
                        <label><?php echo Lang::txt('COM_PROJECTS_TERMS_GRANT_APPROVAL_CODE'); ?>:</label>
                        <?php
                        $approval = $this->escape(html_entity_decode($this->params->get('grant_approval', '')));
                        echo $approval ? $approval : Lang::txt('COM_PROJECTS_NA');
                        ?>
                    </div>
                <?php } ?>
            </fieldset>

            <?php if (!$this->model->inSetup()) { ?>
                <fieldset class="adminform">
                    <legend><?php echo Lang::txt('COM_PROJECTS_FILES'); ?></legend>

                    <div class="input-wrap">
                        <?php
                        $langTxt8 = Lang::txt('COM_PROJECTS_FILES_GBYTES');
                        $langTxt9 = Lang::txt('Files Quota');
                        ?>
                        <label for="param-quota"><?php echo $langTxt9; ?>: <?php echo ' (' . $langTxt8 . ')'; ?></label>
                        <?php $quotaGB = \Components\Projects\Helpers\Html::convertSize($quota, 'b', 'GB', 2); ?>
                        <input
                            name="params[quota]"
                            id="param-quota"
                            maxlength="100"
                            type="text"
                            value="<?php echo $quotaGB; ?>"
                            class="short"
                        />
                    </div>

                    <div class="input-wrap">
                        <label for="param-pubQuota"><?php echo Lang::txt('Publications Quota'); ?>: <?php echo ' ('
                            . Lang::txt('COM_PROJECTS_FILES_GBYTES')
                            . ')'; ?></label>
                        <?php $pubQuotaGB = \Components\Projects\Helpers\Html::convertSize($pubQuota, 'b', 'GB', 2); ?>
                        <input
                            name="params[pubQuota]"
                            id="param-pubQuota"
                            maxlength="100"
                            type="text"
                            value="<?php echo $pubQuotaGB; ?>"
                            class="short"
                        />
                    </div>

                    <?php if ($this->diskusage) { ?>
                        <div class="input-wrap">
                            <?php echo $this->diskusage; ?>
                        </div>
                    <?php } ?>

                    <div class="input-wrap">
                        <?php
                        $gitgcUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&task=gitgc&id=' . $this->model->get('id')
                        );
                        $gitgcTxt = Lang::txt('git gc --aggressive');
                        $minutesTxt = Lang::txt('Takes minutes to run');
                        ?>
                        <?php echo Lang::txt('Maintenance options:'); ?>
                        &nbsp;
                        <a href="<?php echo $gitgcUrl; ?>"><?php echo $gitgcTxt; ?></a>
                        [<?php echo $minutesTxt; ?>]
                    </div>

                    <?php if ($cEnabled) { ?>
                        <div class="input-wrap">
                            <?php echo Lang::txt('COM_PROJECTS_CONNECTIONS'); ?>
                                : <strong><?php echo $connected ? $service : 'not connected'; ?></strong> &nbsp;
                            <?php if ($connected) { ?>
                                <?php
                                $syncUrl = Route::url(
                                    'index.php?option=' . $this->option
                                    . '&controller=' . $this->controller
                                    . '&task=fixsync&id=' . $this->model->get('id')
                                );
                                $syncTxt = Lang::txt('download sync log');
                                $fixTxt = Lang::txt('Also fixes stalled sync');
                                ?>
                                <a href="<?php echo $syncUrl; ?>"><?php echo $syncTxt; ?></a>
                                &nbsp; [<?php echo $fixTxt; ?>]
                            <?php } ?>
                        </div>
                    <?php } ?>
                </fieldset>
            <?php } ?>
        </div>
        <div class="col span5">
            <table class="meta">
                <tbody>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_PROJECTS_CREATED'); ?>:</th>
                        <td><?php echo $this->model->get('created'); ?>
                            <?php
                            $byTxt = Lang::txt('COM_PROJECTS_BY');
                            $creatorName = $this->model->creator('name');
                            $creatorUser = $this->model->creator('username');
                            echo $byTxt . ' ' . $creatorName . ' (' . $creatorUser . ')';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_PROJECTS_STATUS'); ?></th>
                        <td><?php echo $status; ?></td>
                    </tr>
                <?php if (isset($this->counts['files'])) : ?>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_PROJECTS_FILES'); ?>:</th>
                        <td><?php echo $this->counts['files']; ?></td>
                    </tr>
                <?php endif; ?>
                <?php if (isset($this->counts['publications'])) : ?>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_PROJECTS_PUBLICATIONS'); ?>:</th>
                        <td><?php echo $this->counts['publications']; ?></td>
                    </tr>
                <?php endif; ?>
                <?php if (isset($this->counts['todo'])) : ?>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_PROJECTS_TODOS'); ?>:</th>
                        <td><?php echo $this->counts['todo']; ?> <?php if ($this->counts['todos_completed'] > 0) {
                            $langTxt28 = Lang::txt('COM_PROJECTS_TODOS_COMPLETED');
                            ?>( +<?php echo $this->counts['todos_completed']; ?> <?php echo $langTxt28; ?>)<?php
                            } ?></td>
                    </tr>
                <?php endif; ?>
                <?php if (isset($this->counts['notes'])) : ?>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_PROJECTS_NOTES'); ?>:</th>
                        <td><?php echo $this->counts['notes']; ?></td>
                    </tr>
                <?php endif; ?>
                <?php if (isset($this->counts['activity'])) : ?>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_PROJECTS_ACTIVITIES_IN_FEED'); ?>:</th>
                        <td><?php echo $this->counts['activity']; ?></td>
                    </tr>
                <?php endif; ?>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_PROJECTS_LAST_ACTIVITY'); ?>:</th>
                        <td><?php if ($this->last_activity) {
                            $activity = preg_replace('/said/', "posted an update", $this->last_activity->description);
                            $activity = preg_replace('/&#58;/', "", $activity);
                            ?>
                            <?php
                            $timeAgo = \Components\Projects\Helpers\Html::timeAgo(
                                $this->last_activity->created
                            );
                            $agoTxt = Lang::txt('COM_PROJECTS_AGO');
                            $actorName = $this->last_activity->creator->name;
                            ?>
                            <?php echo $this->last_activity->created; ?>
                            (<?php echo $timeAgo . ' ' . $agoTxt; ?>)
                            <br />
                            <span class="actor"><?php echo $actorName; ?></span>
                            <?php echo $activity; ?>
                            <?php } else {
                                echo Lang::txt('COM_PROJECTS_NA');
                            }?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <fieldset class="adminform">
                <legend><?php echo Lang::txt('COM_PROJECTS_STATUS'); ?></legend>

                <div class="input-wrap">
                    <label for="message"><?php echo Lang::txt('COM_PROJECTS_MESSAGE'); ?>:</label>
                    <textarea name="message" id="message" rows="5" cols="50"></textarea>
                </div>

                <div class="input-wrap">
                    <?php echo Lang::txt('COM_PROJECTS_OPTIONS'); ?>:<br />

                    <?php
                    $msgTxt = Lang::txt('COM_PROJECTS_OPTION_SEND_MESSAGE');
                    ?>
                    <input type="hidden" name="admin_action" value="" />
                    <input
                        type="submit"
                        value="<?php echo $msgTxt; ?>"
                        class="btn"
                        id="do-message"
                    />
                    <span class="breaker"> | </span>
                    <?php if ($this->model->isActive()) { ?>
                        <?php $suspendTxt = Lang::txt('COM_PROJECTS_OPTION_SUSPEND'); ?>
                        <input
                            type="submit"
                            value="<?php echo $suspendTxt; ?>"
                            class="btn"
                            id="do-suspend"
                        />
                    <?php } elseif ($this->model->isInactive() || $this->model->isDeleted()) { ?>
                        <?php
                        $reinstateTxt = Lang::txt('COM_PROJECTS_OPTION_REINSTATE');
                        $activateTxt = Lang::txt('COM_PROJECTS_OPTION_ACTIVATE');
                        $reinstateVal = $this->suspended ? $reinstateTxt : $activateTxt;
                        ?>
                        <input
                            type="submit"
                            value="<?php echo $reinstateVal; ?>"
                            class="btn"
                            id="do-reinstate"
                        />
                    <?php } ?>
                    <?php if (!$this->model->isDeleted()) { ?>
                        <?php $deleteTxt = Lang::txt('COM_PROJECTS_OPTION_DELETE'); ?>
                        <input
                            type="submit"
                            value="<?php echo $deleteTxt; ?>"
                            class="btn"
                            id="do-delete"
                        />
                    <?php } ?>
                    <?php if ($this->model->isArchived()) { ?>
                        <?php $unarchTxt = Lang::txt('COM_PROJECTS_OPTION_UNARCHIVE'); ?>
                        <input
                            type="submit"
                            value="<?php echo $unarchTxt; ?>"
                            class="btn"
                            id="do-unarchive"
                        />
                    <?php } else { ?>
                        <?php $archTxt = Lang::txt('COM_PROJECTS_OPTION_ARCHIVE'); ?>
                        <input
                            type="submit"
                            value="<?php echo $archTxt; ?>"
                            class="btn"
                            id="do-archive"
                        />
                    <?php } ?>
                </div>
            </fieldset>

            <fieldset class="adminform">
                <legend><?php echo Lang::txt('COM_PROJECTS_TEAM') . ' (' . $this->counts['team'] . ')'; ?></legend>
                <table>
                    <tbody>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_PROJECTS_MANAGERS'); ?>:</th>
                            <td><?php echo $this->managers ? $this->managers : Lang::txt('COM_PROJECTS_NA'); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_PROJECTS_COLLABORATORS'); ?>:</th>
                            <td><?php echo $this->members ? $this->members : Lang::txt('COM_PROJECTS_NA'); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_PROJECTS_AUTHORS'); ?>:</th>
                            <td><?php echo $this->authors ? $this->authors : Lang::txt('COM_PROJECTS_NA'); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_PROJECTS_REVIEWERS'); ?>:</th>
                            <td><?php echo $this->reviewers ? $this->reviewers : Lang::txt('COM_PROJECTS_NA'); ?></td>
                        </tr>
                    </tbody>
                </table>

                <fieldset>
                    <legend><?php echo Lang::txt('COM_PROJECTS_ADD_MEMBER'); ?></legend>

                    <div class="input-wrap">
                        <label for="newmember"><?php echo Lang::txt('COM_PROJECTS_ADD_MEMBER_USERNAME'); ?></label>
                        <input type="text" name="newmember" id="newmember" value="" />
                    </div>

                    <div class="input-wrap">
                        <label for="field-role"><?php echo Lang::txt('COM_PROJECTS_ADD_MEMBER_ROLE'); ?></label>
                        <select name="role" id="field-role">
                            <option value="1"><?php echo Lang::txt('COM_PROJECTS_ADD_MEMBER_ROLE_MANAGER'); ?></option>
                            <?php
                            $langTxt32 = Lang::txt('COM_PROJECTS_ADD_MEMBER_ROLE_COLLABORATOR');
                            ?>
                            <option value="0"><?php echo $langTxt32; ?></option>
                        </select>
                    </div>
                </fieldset>
            </fieldset>
        </div>
    </div>

    <?php
    $eraseUrl = Route::url(
        'index.php?option=' . $this->option
        . '&controller=' . $this->controller
        . '&task=erase&id=' . $this->model->get('id')
    );
    $eraseTxt = Lang::txt('COM_PROJECTS_ERASE_PROJECT');
    $eraseNotice = Lang::txt('COM_PROJECTS_ERASE_NOTICE');
    ?>
    <p class="notice">
        <a class="button" href="<?php echo $eraseUrl; ?>"><?php echo $eraseTxt; ?></a>
        <?php echo $eraseNotice; ?>
    </p>

        </div>
        <div id="page-images" class="tab">
            <?php if ($this->model->get('picture')) { ?>
                <fieldset class="adminform">
                    <legend><?php echo Lang::txt('COM_PROJECTS_IMAGE_THUMB'); ?></legend>
                    <?php
                    $width  = 50;
                    $height = 50;
                    $size = 0;
                    $path = $this->model->picture('thumb', true);
                    if (file_exists($path)) {
                        list($width, $height) = getimagesize($path);
                        $size = filesize($path);
                    }
                    ?>
                    <p><img src="<?php echo $this->model->picture('thumb'); ?>
                        " width="50" alt="<?php echo Lang::txt('COM_PROJECTS_IMAGE_THUMB'); ?>" /></p>
                    <table>
                        <tbody>
                            <tr>
                                <th><?php echo Lang::txt('COM_PROJECTS_IMAGE_WIDTH'); ?>:</th>
                                <td><?php echo $width; ?> px</td>
                            </tr>
                            <tr>
                                <th><?php echo Lang::txt('COM_PROJECTS_IMAGE_HEIGHT'); ?>:</th>
                                <td><?php echo $height; ?> px</td>
                            </tr>
                            <tr>
                                <th><?php echo Lang::txt('COM_PROJECTS_IMAGE_SIZE'); ?>:</th>
                                <td><?php echo Hubzero\Utility\Number::formatBytes($size); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
                <fieldset class="adminform">
                    <legend><?php echo Lang::txt('COM_PROJECTS_IMAGE_MASTER'); ?></legend>
                    <?php
                    $width  = 200;
                    $height = 200;
                    $size = 0;
                    $path = $this->model->picture('master', true);
                    if (file_exists($path)) {
                        list($width, $height) = getimagesize($path);
                        $size = filesize($path);
                    }
                    ?>
                    <p><img src="<?php echo $this->model->picture('master'); ?>
                        " width="200" alt="<?php echo Lang::txt('COM_PROJECTS_IMAGE_MASTER'); ?>" /></p>
                    <table>
                        <tbody>
                            <tr>
                                <th scope="row"><?php echo Lang::txt('COM_PROJECTS_IMAGE_WIDTH'); ?>:</th>
                                <td><?php echo $width; ?> px</td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo Lang::txt('COM_PROJECTS_IMAGE_HEIGHT'); ?>:</th>
                                <td><?php echo $height; ?> px</td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo Lang::txt('COM_PROJECTS_IMAGE_SIZE'); ?>:</th>
                                <td><?php echo Hubzero\Utility\Number::formatBytes($size); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
                <fieldset class="adminform">
                    <legend><?php echo Lang::txt('COM_PROJECTS_IMAGE_ORIGINAL'); ?></legend>
                    <?php
                    $width  = 200;
                    $height = 200;
                    $size = 0;
                    $path = $this->model->picture('original', true);
                    if (file_exists($path)) {
                        list($width, $height) = getimagesize($path);
                        $size = filesize($path);
                    }
                    ?>
                    <?php $langTxt1 = Lang::txt('COM_PROJECTS_IMAGE_ORIGINAL'); ?>
                    <p><img src="<?php echo $this->model->picture('original'); ?>" alt="<?php echo $langTxt1; ?>" /></p>
                    <table>
                        <tbody>
                            <tr>
                                <th scope="row"><?php echo Lang::txt('COM_PROJECTS_IMAGE_WIDTH'); ?>:</th>
                                <td><?php echo $width; ?> px</td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo Lang::txt('COM_PROJECTS_IMAGE_HEIGHT'); ?>:</th>
                                <td><?php echo $height; ?> px</td>
                            </tr>
                            <tr>
                                <th scope="row"><?php echo Lang::txt('COM_PROJECTS_IMAGE_SIZE'); ?>:</th>
                                <td><?php echo Hubzero\Utility\Number::formatBytes($size); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            <?php } else { ?>
                <fieldset class="adminform">
                    <?php echo Lang::txt('COM_PROJECTS_IMAGE_NONE'); ?>
                </fieldset>
            <?php } ?>
        </div>
    </div>

    <input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="apply" />

    <?php echo Html::input('token'); ?>
</form>
