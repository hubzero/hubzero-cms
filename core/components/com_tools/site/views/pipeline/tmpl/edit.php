<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

$exec_pu = $this->config->get('exec_pu', 1);

$execChoices[''] = Lang::txt('COM_TOOLS_SELECT_TOP');
$execChoices['@OPEN'] = ucfirst(Lang::txt('COM_TOOLS_TOOLACCESS_OPEN'));
$execChoices['@US'] = ucfirst(Lang::txt('COM_TOOLS_TOOLACCESS_US'));
$execChoices['@D1'] = ucfirst(Lang::txt('COM_TOOLS_TOOLACCESS_D1'));
if ($exec_pu) {
    $execChoices['@PU'] = ucfirst(Lang::txt('COM_TOOLS_TOOLACCESS_PU'));
}
$execChoices['@GROUP'] = ucfirst(Lang::txt('COM_TOOLS_RESTRICTED'))
    . ' ' . Lang::txt('COM_TOOLS_TO')
    . ' ' . Lang::txt('COM_TOOLS_GROUP_OR_GROUPS');

$codeChoices[''] = Lang::txt('COM_TOOLS_SELECT_TOP');
$codeChoices['@OPEN'] = ucfirst(Lang::txt('COM_TOOLS_OPEN_SOURCE'))
    . ' (' . Lang::txt('COM_TOOLS_OPEN_SOURCE_TIPS') . ')';
$codeChoices['@DEV'] = ucfirst(Lang::txt('COM_TOOLS_ACCESS_RESTRICTED'));

$wikiChoices[''] = Lang::txt('COM_TOOLS_SELECT_TOP');
$wikiChoices['@OPEN'] = ucfirst(Lang::txt('COM_TOOLS_ACCESS_OPEN'));
$wikiChoices['@DEV'] = ucfirst(Lang::txt('COM_TOOLS_ACCESS_RESTRICTED'));

if (!is_array($this->defaults['developers'])) {
    $this->defaults['developers'] = explode(',', $this->defaults['developers']);
    $this->defaults['developers'] = array_map('trim', $this->defaults['developers']);
}

$this->css('pipeline.css')
     ->js('pipeline.js');
?>
<header id="content-header">
    <h2><?php echo $this->escape($this->title); ?></h2>

    <div id="content-header-extra">
        <ul id="useroptions">
        <?php if ($this->id) {
            $statusUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=status&app=' . $this->defaults['toolname']
            );
            ?>
            <li><a class="icon-status status btn" href="<?php echo $statusUrl; ?>">
                <?php echo Lang::txt('COM_TOOLS_TOOL_STATUS'); ?></a></li>
        <?php } ?>
            <?php
            $pipelineUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=pipeline'
            );
            ?>
            <li class="last"><a class="icon-main main-page btn" href="<?php echo $pipelineUrl; ?>">
                <?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_ALL_TOOLS'); ?></a></li>
        </ul>
    </div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<?php if ($this->getError()) { ?>
    <p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>

<section class="section">
    <div class="section-inner">
        <?php
        $formAction = Route::url('index.php?option=' . $this->option);
        ?>
        <form action="<?php echo $formAction; ?>" method="post" id="hubForm"
            enctype="multipart/form-data">
            <div class="explaination">
                <?php if (!$this->id) { ?>
                    <h3><?php echo Lang::txt('COM_TOOLS_SIDE_WHAT_TOOLNAME'); ?></h3>
                    <p><?php echo Lang::txt('COM_TOOLS_SIDE_TIPS_TOOLNAME'); ?></p>
                <?php } else { ?>
                    <p><?php echo Lang::txt('COM_TOOLS_SIDE_EDIT_TOOL'); ?></p>
                <?php } ?>
            </div>
            <fieldset>
                <legend><?php echo Lang::txt('COM_TOOLS_LEGEND_ABOUT'); ?>:</legend>

                <input type="hidden" name="toolid" value="<?php echo $this->id; ?>" />
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
                <input type="hidden" name="task"
                    value="<?php echo ($this->id) ? 'save' : 'register'; ?>" />
                <input type="hidden" name="editversion"
                    value="<?php echo $this->editversion; ?>" />
                <?php echo Html::input('token'); ?>

                <div class="form-group">
                    <label for="t_toolname">
                        <?php echo Lang::txt('COM_TOOLS_TOOLNAME'); ?>:
                        <?php if ($this->id) {
                            $toolname = $this->defaults['toolname'];
                            $verLabel = ($this->editversion == 'current')
                                ? Lang::txt('COM_TOOLS_CURRENT_VERSION')
                                : Lang::txt('COM_TOOLS_DEV_VERSION');
                            ?>
                            <input type="hidden" name="tool[toolname]" id="t_toolname"
                                value="<?php echo $toolname; ?>" />
                            <strong><?php echo $toolname; ?>
                                (<?php echo $verLabel; ?>)</strong>
                            <?php if (
                            isset($this->defaults['published'])
                                && $this->defaults['published']
) {
                                     $allVerUrl = Route::url(
                                         'index.php?option=' . $this->option
                                         . '&controller=' . $this->controller
                                         . '&task=versions&app=' . $toolname
                                     );
                                        ?>
                                <a href="<?php echo $allVerUrl; ?>">
                                     <?php echo Lang::txt('COM_TOOLS_ALL_VERSIONS'); ?></a>
                            <?php } ?>
                        <?php } else { ?>
                            <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                            <input type="text" name="tool[toolname]" id="t_toolname"
                                maxlength="15" class="form-control"
                                value="<?php echo $this->escape($this->defaults['toolname']); ?>" />
                            <span class="hint"><?php echo Lang::txt('COM_TOOLS_HINT_TOOLNAME'); ?></span>
                        <?php } ?>
                    </label>
                </div>

                <div class="form-group">
                    <label for="t_title">
                        <?php echo Lang::txt('COM_TOOLS_TITLE') ?>:
                        <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                        <?php $titleVal = $this->escape(stripslashes($this->defaults['title'])); ?>
                        <input type="text" name="tool[title]" id="t_title"
                            maxlength="127" class="form-control"
                            value="<?php echo $titleVal; ?>" />
                        <span class="hint"><?php echo Lang::txt('COM_TOOLS_HINT_TITLE'); ?></span>
                    </label>
                </div>

                <div class="form-group">
                    <label for="t_version">
                        <?php echo Lang::txt('COM_TOOLS_VERSION') ?>:
                        <?php if ($this->editversion == 'current') { ?>
                            <input type="hidden" name="tool[version]" id="t_version"
                                value="<?php echo $this->escape($this->defaults['version']); ?>" />
                            <strong><?php echo $this->defaults['version']; ?></strong>
                            <span class="hint">
                                <?php echo Lang::txt('COM_TOOLS_HINT_VERSION_PUBLISHED'); ?></span>
                        <?php } else { ?>
                            <input type="text" name="tool[version]" id="t_version"
                                maxlength="15" class="form-control"
                                value="<?php echo $this->escape($this->defaults['version']); ?>" />
                            <span class="hint"><?php echo Lang::txt('COM_TOOLS_HINT_VERSION'); ?></span>
                        <?php } ?>
                    </label>
                </div>

                <div class="form-group">
                    <label for="t_description">
                        <?php echo Lang::txt('COM_TOOLS_AT_A_GLANCE') ?>:
                        <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                        <?php $descVal = $this->escape(stripslashes($this->defaults['description'])); ?>
                        <input type="text" name="tool[description]" id="t_description"
                            maxlength="256" class="form-control"
                            value="<?php echo $descVal; ?>" />
                        <span class="hint"><?php echo Lang::txt('COM_TOOLS_HINT_DESCRIPTION'); ?></span>
                    </label>
                </div>

                <?php if ($this->id && isset($this->defaults['resourceid'])) {
                    $previewUrl = Route::url(
                        'index.php?option=com_resources&id='
                        . $this->defaults['resourceid'] . '&rev=dev'
                    );
                    $editResUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=' . $this->controller
                        . '&task=resource&app=' . $this->defaults['toolname']
                    );
                    ?>
                    <p>
                        <?php echo Lang::txt('COM_TOOLS_DESCRIPTION'); ?>:
                        <a class="icon-preview btn btn-secondary" href="<?php echo $previewUrl; ?>">
                            <?php echo Lang::txt('COM_TOOLS_PREVIEW') ?></a>
                        <a class="icon-edit btn btn-secondary" href="<?php echo $editResUrl; ?>">
                            <?php echo Lang::txt('Edit Resource Page') ?></a>
                    </p>
                <?php } ?>

                <fieldset>
                    <legend><?php echo ($this->id)
                        ? Lang::txt('COM_TOOLS_APPLICATION_SCREEN_SIZE')
                        : Lang::txt('COM_TOOLS_SUGGESTED_SCREEN_SIZE');
                    ?>:</legend>
                    <div class="form-group">
                        <div class="inline">
                            <label for="vncGeometryX">
                                <?php echo Lang::txt('COM_TOOLS_MARKER_WIDTH'); ?>
                                <input type="text" name="tool[vncGeometryX]" id="vncGeometryX"
                                    size="4" maxlength="4" class="form-control"
                                    value="<?php echo $this->defaults['vncGeometryX']; ?>" />
                            </label> x
                            <label for="vncGeometryY">
                                <?php echo Lang::txt('COM_TOOLS_MARKER_HEIGHT'); ?>
                                <input type="text" name="tool[vncGeometryY]" id="vncGeometryY"
                                    size="4" maxlength="4" class="form-control"
                                    value="<?php echo $this->defaults['vncGeometryY']; ?>" />
                            </label>
                        </div>
                    </div>
                    <p class="hint"><?php echo Lang::txt('COM_TOOLS_HINT_VNC'); ?></p>
                </fieldset>

                <?php if ($this->config->get('access-admin-component')) {
                    $hostreqVal = $this->escape(stripslashes($this->defaults['hostreq']));
                    ?>
                    <div class="form-group">
                        <label for="t_hostreq">
                            <?php echo Lang::txt('COM_TOOLS_HOSTREQ') ?>:</span>
                            <input type="text" name="tool[hostreq]" id="t_hostreq"
                                class="form-control" value="<?php echo $hostreqVal; ?>" />
                            <span class="hint">
                                <?php echo Lang::txt('COM_TOOLS_HINT_HOSTREQ'); ?></span>
                        </label>
                    </div>
                <?php } else {
                    $hostreqVal = $this->escape(stripslashes($this->defaults['hostreq']));
                    ?>
                    <input type="hidden" name="tool[hostreq]" id="t_hostreq"
                        value="<?php echo $hostreqVal; ?>" />
                <?php } ?>
            </fieldset>

            <?php if ($this->id) { ?>
                <fieldset class="hidden">
                    <legend><?php echo Lang::txt('COM_TOOLS_EDIT_REPO_HOST'); ?>:</legend>

                    <?php if ($this->config->get('github', 1)) {
                        $gitExtChecked = (!$this->defaults['repohost']
                            || $this->defaults['repohost'] == 'gitExternal')
                            ? 'checked="checked"' : 'disabled';
                        ?>
                        <div class="form-group form-check">
                            <label for="tool_repohost_gitexternal" class="form-check-label">
                                <input readonly type="radio" name="tool[repohost]"
                                    id="tool_repohost_gitexternal"
                                    value="gitExternal" <?php echo $gitExtChecked; ?>
                                    class="option form-check-input" />
                                <?php echo Lang::txt('COM_TOOLS_EDIT_EXT_GIT'); ?>
                            </label>
                        </div>
                    <?php } ?>

                    <?php if (file_exists('/usr/bin/addrepo.sh')) {
                        $gitLocalChecked = ($this->defaults['repohost'] == 'gitLocal')
                            ? 'checked="checked"' : 'disabled';
                        ?>
                        <div class="form-group form-check">
                            <label for="tool_repohost_gitlocal" class="form-check-label">
                                <input readonly type="radio" name="tool[repohost]"
                                    id="tool_repohost_gitlocal"
                                    value="gitLocal" <?php echo $gitLocalChecked; ?>
                                    class="option form-check-input" />
                                <?php echo Lang::txt('COM_TOOLS_EDIT_LOCAL_GIT'); ?>
                            </label>
                        </div>
                    <?php } ?>

                    <?php
                    $svnLocalChecked = ($this->defaults['repohost'] == 'svnLocal')
                        ? 'checked="checked"' : 'disabled';
                    ?>
                    <div class="form-group form-check">
                        <label for="tool_repohost_svnlocal" class="form-check-label">
                            <input readonly type="radio" name="tool[repohost]"
                                id="tool_repohost_svnlocal"
                                value="svnLocal" <?php echo $svnLocalChecked; ?>
                                class="option form-check-input" />
                            <?php echo Lang::txt('COM_TOOLS_EDIT_LOCAL_SUBVERSION'); ?>
                        </label>
                    </div>

                </fieldset>

                <?php if ($this->config->get('github', 1)) { ?>
                    <div id="gitExternalExplanation" class="explaination">
                        <?php if (file_exists('/usr/bin/addrepo.sh')) { ?>
                            <p><?php echo Lang::txt('COM_TOOLS_EDIT_EXT_GIT_DESCR_WITHPRIV'); ?></p>
                        <?php } else { ?>
                            <p><?php echo Lang::txt('COM_TOOLS_EDIT_EXT_GIT_DESCR'); ?></p>
                        <?php } ?>
                    </div>
                    <fieldset class="hidden">
                        <legend><?php echo Lang::txt('COM_TOOLS_EDIT_GIT_URL'); ?>:</legend>
                        <label for="github">
                            <?php echo Lang::txt('COM_TOOLS_EDIT_GIT_SOURCE') ?>:
                            <?php $gitPasteTxt = Lang::txt('COM_TOOLS_EDIT_GIT_SOURCE_PASTE'); ?>
                            <input readonly type="text" name="tool[github]" id="github"
                                placeholder="<?php echo $gitPasteTxt; ?>"
                                value="<?php echo $this->defaults['github']; ?>" />
                        </label>
                        <?php if (file_exists('/usr/bin/addrepo.sh')) { ?>
                            <p class="hint"><?php echo Lang::txt('COM_TOOLS_EDIT_URL_GITPUBPRIV'); ?></p>
                        <?php } else { ?>
                            <p class="hint"><?php echo Lang::txt('COM_TOOLS_EDIT_URL_GITPUB'); ?></p>
                        <?php } ?>
                    </fieldset>
                <?php } ?>
                <fieldset>
                    <legend><?php echo Lang::txt('COM_TOOLS_EDIT_PUB_OPT'); ?>:</legend>
                    <?php
                    $chkStd = (!$this->defaults['publishType']
                        || $this->defaults['publishType'] == 'standard')
                        ? 'checked="checked"' : '';
                    ?>
                    <div class="form-group form-check">
                        <label for="tool_publishType_standard" class="form-check-label">
                            <input type="radio" name="tool[publishType]"
                                id="tool_publishType_standard"
                                value="standard" <?php echo $chkStd; ?>
                                class="option form-check-input" />
                            <?php echo Lang::txt('COM_TOOLS_EDIT_PUB_OPT_RAPP'); ?>
                        </label>
                    </div>
                    <?php if ($this->config->get('jupyter', 1)) {
                        $chkJup = ($this->defaults['publishType'] == 'jupyter')
                            ? 'checked="checked"' : '';
                        ?>
                        <div class="form-group form-check">
                            <label for="tool_publishType_jupyter" class="form-check-label">
                                <input type="radio" name="tool[publishType]"
                                    id="tool_publishType_jupyter"
                                    value="jupyter" <?php echo $chkJup; ?>
                                    class="option form-check-input" />
                                <?php echo Lang::txt('COM_TOOLS_EDIT_PUB_OPT_JUP'); ?>
                            </label>
                    </div>
                    <?php } ?>

                    <?php
                    $simtoolFile = '/usr/share/hubzero-forge/svn/trunk/middleware/invoke.simtool';
                    if ($this->config->get('simtool', 1) && is_file($simtoolFile)) {
                        $chkSim = ($this->defaults['publishType'] == 'simtool')
                            ? 'checked="checked"' : '';
                        ?>
                    <div class="form-group form-check">
                        <label for="tool_publishType_simtool" class="form-check-label">
                            <input type="radio" name="tool[publishType]"
                                id="tool_publishType_simtool"
                                value="simtool" <?php echo $chkSim; ?>
                                class="option form-check-input" />
                            <?php echo Lang::txt('COM_TOOLS_EDIT_PUB_OPT_SIM'); ?>
                        </label>
                    </div>
                    <?php } ?>
                </fieldset>
            <?php } else { ?>
                <fieldset>
                    <legend><?php echo Lang::txt('COM_TOOLS_EDIT_REPO_HOST'); ?>:</legend>

                    <?php if ($this->config->get('github', 1)) {
                        $gitExtChk = (!$this->defaults['repohost']
                            || $this->defaults['repohost'] == 'gitExternal')
                            ? 'checked="checked"' : '';
                        ?>
                        <div class="form-group form-check">
                            <label for="tool_repohost_gitexternal" class="form-check-label">
                                <input type="radio" name="tool[repohost]"
                                    id="tool_repohost_gitexternal"
                                    value="gitExternal" <?php echo $gitExtChk; ?>
                                    class="option form-check-input" />
                                <?php echo Lang::txt('COM_TOOLS_EDIT_EXT_GIT'); ?>
                            </label>
                        </div>
                    <?php } ?>

                    <?php if (file_exists('/usr/bin/addrepo.sh')) {
                        $gitLocalChk = ($this->defaults['repohost'] == 'gitLocal')
                            ? 'checked="checked"' : '';
                        ?>
                        <div class="form-group form-check">
                            <label for="tool_repohost_gitlocal" class="form-check-label">
                                <input type="radio" name="tool[repohost]"
                                    id="tool_repohost_gitlocal"
                                    value="gitLocal" <?php echo $gitLocalChk; ?>
                                    class="option form-check-input" />
                                <?php echo Lang::txt('COM_TOOLS_EDIT_LOCAL_GIT'); ?>
                            </label>
                        </div>
                    <?php } ?>

                    <?php
                    $svnLocalChk = ($this->defaults['repohost'] == 'svnLocal')
                        ? 'checked="checked"' : '';
                    ?>
                    <div class="form-group form-check">
                        <label for="tool_repohost_svnlocal" class="form-check-label">
                            <input type="radio" name="tool[repohost]"
                                id="tool_repohost_svnlocal"
                                value="svnLocal" <?php echo $svnLocalChk; ?>
                                class="option form-check-input" />
                            <?php echo Lang::txt('COM_TOOLS_EDIT_LOCAL_SUBVERSION'); ?>
                        </label>
                    </div>

                </fieldset>

                <?php if ($this->config->get('github', 1)) { ?>
                    <div id="gitExternalExplanation" class="explaination">
                        <?php if (file_exists('/usr/bin/addrepo.sh')) { ?>
                            <p><?php echo Lang::txt('COM_TOOLS_EDIT_EXT_GIT_DESCR_WITHPRIV'); ?></p>
                        <?php } else { ?>
                            <p><?php echo Lang::txt('COM_TOOLS_EDIT_EXT_GIT_DESCR'); ?></p>
                        <?php } ?>
                    </div>
                    <fieldset id="gitExternalInput">
                        <legend><?php echo Lang::txt('COM_TOOLS_EDIT_GIT_URL'); ?>:</legend>
                        <label for="github">
                            <?php echo Lang::txt('COM_TOOLS_EDIT_GIT_SOURCE') ?>:
                            <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                            <?php $gitPasteTxt2 = Lang::txt('COM_TOOLS_EDIT_GIT_SOURCE_PASTE'); ?>
                            <input type="text" name="tool[github]" id="github"
                                placeholder="<?php echo $gitPasteTxt2; ?>"
                                value="<?php echo $this->defaults['github']; ?>" />
                        </label>
                        <?php if (file_exists('/usr/bin/addrepo.sh')) { ?>
                            <p class="hint"><?php echo Lang::txt('COM_TOOLS_EDIT_URL_GITPUBPRIV'); ?></p>
                        <?php } else { ?>
                            <p class="hint"><?php echo Lang::txt('COM_TOOLS_EDIT_URL_GITPUB'); ?></p>
                        <?php } ?>
                    </fieldset>
                <?php } ?>

                <fieldset>
                    <legend><?php echo Lang::txt('COM_TOOLS_EDIT_PUB_OPT'); ?>:</legend>
                    <?php
                    $chkStd2 = (!$this->defaults['publishType']
                        || $this->defaults['publishType'] == 'standard')
                        ? 'checked="checked"' : '';
                    ?>
                    <div class="form-group form-check">
                        <label for="tool_publishType_standard" class="form-check-label">
                            <input type="radio" name="tool[publishType]"
                                id="tool_publishType_standard"
                                value="standard" <?php echo $chkStd2; ?>
                                class="option form-check-input" />
                            <?php echo Lang::txt('COM_TOOLS_EDIT_PUB_OPT_RAPP'); ?>
                        </label>
                    </div>

                    <?php if ($this->config->get('jupyter', 1)) {
                        $chkJup2 = ($this->defaults['publishType'] == 'jupyter')
                            ? 'checked="checked"' : '';
                        ?>
                        <div class="form-group form-check">
                            <label for="tool_publishType_jupyter" class="form-check-label">
                                <input type="radio" name="tool[publishType]"
                                    id="tool_publishType_jupyter"
                                    value="jupyter" <?php echo $chkJup2; ?>
                                    class="option form-check-input" />
                                <?php echo Lang::txt('COM_TOOLS_EDIT_PUB_OPT_JUP'); ?>
                            </label>
                    </div>
                    <?php } ?>

                    <?php
                    $simtoolFile2 = '/usr/share/hubzero-forge/svn/trunk/middleware/invoke.simtool';
                    if ($this->config->get('simtool', 1) && is_file($simtoolFile2)) {
                        $chkSim2 = ($this->defaults['publishType'] == 'simtool')
                            ? 'checked="checked"' : '';
                        ?>
                    <div class="form-group form-check">
                        <label for="tool_publishType_simtool" class="form-check-label">
                            <input type="radio" name="tool[publishType]"
                                id="tool_publishType_simtool"
                                value="simtool" <?php echo $chkSim2; ?>
                                class="option form-check-input" />
                            <?php echo Lang::txt('COM_TOOLS_EDIT_PUB_OPT_SIM'); ?>
                        </label>
                    </div>
                    <?php } ?>
                </fieldset>
            <?php } ?>



            <fieldset>
                <legend><?php echo Lang::txt('COM_TOOLS_LEGEND_ACCESS'); ?>:</legend>

                <div class="form-group">
                    <label for="t_exec">
                    <?php echo Lang::txt('COM_TOOLS_TOOL_ACCESS'); ?>:
                    <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                        <span class="question-mark tooltips"
                            title="<?php echo Lang::txt('COM_TOOLS_SIDE_TIPS_TOOLACCESS'); ?>">
                            What should I choose?</span>
                        <?php echo \Components\Tools\Helpers\Html::formSelect(
                            'tool[exec]',
                            't_exec',
                            $execChoices,
                            $this->defaults['exec'],
                            'groupchoices'
                        ); ?>
                    </label>
                </div>

                <div id="groupname" class="<?php echo ($this->defaults['exec'] == '@GROUP')
                    ? '' : 'hide'; ?>">
                    <div class="form-group">
                        <?php $groupsVal = \Components\Tools\Helpers\Html::getGroups(
                            $this->defaults['membergroups'],
                            $this->id
                        ); ?>
                        <input type="text" name="tool[membergroups]" id="t_groups"
                            class="form-control" value="<?php echo $groupsVal; ?>" />
                        <p class="hint"><?php echo Lang::txt('COM_TOOLS_HINT_GROUPS'); ?></p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="t_code">
                        <?php echo Lang::txt('COM_TOOLS_CODE_ACCESS'); ?>:
                        <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                        <span class="question-mark tooltips"
                            title="<?php echo Lang::txt('COM_TOOLS_SIDE_TIPS_CODEACCESS'); ?>">
                            What should I choose?</span>
                        <?php echo \Components\Tools\Helpers\Html::formSelect(
                            'tool[code]',
                            't_code',
                            $codeChoices,
                            $this->defaults['code']
                        ); ?>
                    </label>
                </div>

                <div class="form-group">
                    <label for="t_wiki">
                        <?php echo Lang::txt('COM_TOOLS_WIKI_ACCESS'); ?>:
                        <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                        <span class="question-mark tooltips"
                            title="<?php echo Lang::txt('COM_TOOLS_SIDE_TIPS_WIKIACCESS'); ?>">
                            What should I choose?</span>
                        <?php echo \Components\Tools\Helpers\Html::formSelect(
                            'tool[wiki]',
                            't_wiki',
                            $wikiChoices,
                            $this->defaults['wiki']
                        ); ?>
                    </label>
                </div>

                <div class="form-group">
                    <label for="t_team">
                        <?php echo Lang::txt('COM_TOOLS_DEVELOPMENT_TEAM'); ?>:
                        <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                        <?php $devTeamVal = \Components\Tools\Helpers\Html::getDevTeam(
                            $this->defaults['developers'],
                            $this->id
                        ); ?>
                        <input type="text" name="tool[developers]" id="t_team"
                            class="form-control" value="<?php echo $devTeamVal; ?>" />
                        <span class="hint"><?php echo Lang::txt('COM_TOOLS_HINT_TEAM'); ?></span>
                    </label>
                </div>
            </fieldset>

            <p class="submit">
                <?php $submitLabel = (!$this->id)
                    ? Lang::txt('COM_TOOLS_REGISTER_TOOL')
                    : Lang::txt('COM_TOOLS_SAVE_CHANGES');
                ?>
                <input type="submit" class="btn btn-success" value="<?php echo $submitLabel; ?>" />

                <?php if ($this->id) {
                    $cancelUrl = Route::url(
                        'index.php?option=' . $this->option
                        . '&controller=' . $this->controller
                        . '&task=status&app=' . $this->defaults['toolname']
                    );
                    ?>
                    <a class="btn btn-secondary" href="<?php echo $cancelUrl; ?>"
                        title="<?php echo Lang::txt('COM_TOOLS_HINT_CANCEL'); ?>">
                        <?php echo Lang::txt('JCANCEL'); ?>
                    </a>
                <?php } ?>
            </p>
        </form>
    </div><!-- / .subject -->
</section><!-- / .section -->
