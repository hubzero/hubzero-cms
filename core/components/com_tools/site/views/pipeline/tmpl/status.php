<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

// get configurations/ defaults
$developer_site = $this->config->get('developer_site', 'hubFORGE');
$live_site = rtrim(Request::base(), '/');
$developer_url = $live_site = 'https://' . preg_replace('#^(https://|http://)#', '', $live_site);
$project_path  = $this->config->get('project_path', '/tools/');
$dev_suffix    = $this->config->get('dev_suffix', '_dev');

// get status name
\Components\Tools\Helpers\Html::getStatusName($this->status['state'], $state);
\Components\Tools\Helpers\Html::getStatusClass($this->status['state'], $this->statusClass);

$this
     ->css('pipeline.css')
     ->js('pipeline.js');

$view = $this;

// The attribute that marks the current value in the form fields below
$stateSelected = function ($value) use ($view) {
    if ($view->status['state'] == $value) {
        return ' selected="selected"';
    }

    return '';
};

$prioritySelected = function ($value) use ($view) {
    if ($view->status['priority'] == $value) {
        return ' selected="selected"';
    }

    return '';
};
?>
<header id="content-header">
    <h2><?php echo $this->title; ?> - <span class="state_hed"><?php echo $state; ?></span></h2>

    <div id="content-header-extra">
        <ul id="useroptions">
            <?php
            $pipelineUrl = Route::url(
                'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=pipeline',
            );
            ?>
            <li><a class="icon-main main-page btn"
                href="<?= $pipelineUrl ?>"><?= Lang::txt('COM_TOOLS_CONTRIBTOOL_ALL_TOOLS') ?></a></li>
            <?php
            $createUrl = Route::url(
                'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=create',
            );
            ?>
            <li><a class="icon-add add btn"
                href="<?= $createUrl ?>"><?= Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL') ?></a></li>
        </ul>
    </div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
    <?php
    if (\Components\Tools\Helpers\Html::toolActive($this->status['state'])) {
        $states = [
            Lang::txt('COM_TOOLS_REGISTERED'),
            Lang::txt('COM_TOOLS_CREATED'),
            Lang::txt('COM_TOOLS_UPLOADED'),
            Lang::txt('COM_TOOLS_INSTALLED'),
            Lang::txt('COM_TOOLS_APPROVED'),
            Lang::txt('COM_TOOLS_PUBLISHED'),
        ]; // regular state list

        if ($state == Lang::txt('COM_TOOLS_RETIRED')) {
            $states[] = Lang::txt('COM_TOOLS_RETIRED');
        }

        if ($state == Lang::txt('COM_TOOLS_UPDATED')) {
            $states[2] = Lang::txt('COM_TOOLS_UPDATED');
        }

        $key = array_keys($states, $state);
        ?>
    <div class="clear"></div>
    <ol id="steps">
        <li class="steps_hed"><?php echo Lang::txt('COM_TOOLS_STATUS'); ?>:</li>
        <?php
        for ($i = 0, $n = count($states); $i < $n; $i++) {
            $cls = 'done';
            if (strtolower($state) == strtolower($states[$i])) {
                $cls = 'active';
            } elseif (count($key) == 0 or $i > $key[0]) {
                $cls = 'future';
            }
            ?>
            <li class="<?php echo $cls; ?>">
                <?php echo $states[$i]; ?>
            </li>
            <?php
        }
        ?>
    </ol>
    <div class="clear"></div>
        <?php
    }
    ?>

    <div class="toolinfo_note">
        <?php echo $this->msg ? '<p class="passed">' . $this->msg . '</p>' : ''; ?>
        <?= \Components\Tools\Helpers\Html::getNumofTools($this->status)
            ? '<p>' . \Components\Tools\Helpers\Html::getNumofTools($this->status) . '.</p>'
            : '' ?>
    </div><!-- / .toolinfo_note -->

    <div class="grid">
        <div class="col span7">
            <div class="toolinfo<?php echo $this->statusClass; ?>">
                <table id="toolstatus">
                    <tbody>
                        <tr>
                            <th colspan="2" class="toolinfo_hed">
                                <?php echo Lang::txt('COM_TOOLS_TOOL_INFO'); ?>
                            <?php if (\Components\Tools\Helpers\Html::toolActive($this->status['state'])) { ?>
                                <?php
                                $editUrl = Route::url(
                                    'index.php?option=' . $this->option . '&controller=' . $this->controller
                                        . '&task=edit&app=' . $this->status['toolname'],
                                );
                                $titleText = Lang::txt('COM_TOOLS_EDIT_TIPS');
                                ?>
                                <a class="edit button" href="<?= $editUrl ?>"
                                    title="<?= $titleText ?>"><?= Lang::txt('COM_TOOLS_EDIT') ?></a>
                            <?php } ?>
                            </th>
                        </tr>
                        <tr>
                            <th><?php echo Lang::txt('COM_TOOLS_TITLE'); ?></th>
                            <td>
                                <?= $this->escape(stripslashes($this->status['title'])) . ' ('
                                    . $this->status['toolname'] . ' - ' . strtolower(Lang::txt('COM_TOOLS_ID')) . ' #'
                                    . $this->status['toolid'] . ')' ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo Lang::txt('COM_TOOLS_VERSION'); ?></th>
                            <td><?php echo ($this->status['version']) ? Lang::txt('COM_TOOLS_THIS_VERSION') . ' ' . $this->status['version'] : Lang::txt('COM_TOOLS_THIS_VERSION') . ': ' . Lang::txt('COM_TOOLS_NO_LABEL');
                            if (!$this->status['published'] or ($this->status['version'] != $this->status['currentversion'] && \Components\Tools\Helpers\Html::toolActive($this->status['state']))) {
                                echo ' (' . Lang::txt('COM_TOOLS_UNDER_DEVELOPMENT') . ')';
                            }
                            if ($this->status['published']) {
                                echo ' [<a class="tool-versions" href="'
                                    . Route::url(
                                        'index.php?option=' . $this->option . '&controller=' . $this->controller
                                            . '&task=versions&app=' . $this->status['toolname'],
                                    ) . '">' . strtolower(Lang::txt('COM_TOOLS_ALL_VERSIONS')) . '</a>]';
                            }  ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo Lang::txt('COM_TOOLS_AT_A_GLANCE'); ?></th>
                            <td><?php echo $this->escape(stripslashes($this->status['description'])); ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Lang::txt('COM_TOOLS_DESCRIPTION'); ?></th>
                            <td>
                                <?php
                                $linkUrl = Route::url(
                                    'index.php?option=com_resources&id=' . $this->status['resourceid'] . '&rev=dev',
                                );
                                ?>
                                <a class="preview-resource"
                                    href="<?= $linkUrl ?>"><?= Lang::txt('COM_TOOLS_PREVIEW') ?></a> |
                                <?php
                                $resourceUrl = Route::url(
                                    'index.php?option=' . $this->option . '&controller=' . $this->controller
                                        . '&task=resource&step=1&app=' . $this->status['toolname'],
                                );
                                ?>
                                <a class="edit-resource"
                                    href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_EDIT_THIS_PAGE') ?></a>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo Lang::txt('COM_TOOLS_VNC_GEOMETRY'); ?></th>
                            <td><?php echo $this->status['vncGeometryX'] . 'x' . $this->status['vncGeometryY'];?></td>
                        </tr>
                        <?php if ($this->config->get('access-admin-component')) { ?>
                            <tr>
                                <th><?php echo Lang::txt('COM_TOOLS_HOSTREQ'); ?></th>
                                <td><?php echo $this->status['hostreq']; ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <th><?php echo Lang::txt('COM_TOOLS_REPOHOST'); ?></th>
                            <td><?php echo $this->status['repohost']; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Lang::txt('COM_TOOLS_TOOL_EXEC'); ?></th>
                            <td>
                                <?= \Components\Tools\Helpers\Html::getToolAccess(
                                    $this->status['exec'],
                                    $this->status['membergroups'],
                                ) ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo Lang::txt('COM_TOOLS_SOURCE_CODE'); ?></th>
                            <td><?php echo \Components\Tools\Helpers\Html::getCodeAccess($this->status['code']); ?>
                            <?php
                            if (
                                \Components\Tools\Helpers\Html::toolActive($this->status['state'])
                                && \Components\Tools\Helpers\Html::toolWIP($this->status['state'])
                            ) { ?>
                                [<a class="license-tool" href="<?= Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=license&app=' . $this->status['toolname']) ?>"><?= Lang::txt('COM_TOOLS_CHANGE_LICENSE') ?></a>]
                            <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo Lang::txt('COM_TOOLS_PROJECT_AREA'); ?></th>
                            <td><?php echo \Components\Tools\Helpers\Html::getWikiAccess($this->status['wiki']); ?></td>
                        </tr>
                        
                        <?php if ($this->status['github']) { ?>
                            <tr>
                                <th><?php echo Lang::txt('Git URL:'); ?></th>
                                <td><a href="<?= $this->status['github'] ?>"><?= $this->status['github'] ?></a></td>
                            </tr>
                        <?php } ?>
                        
                        <tr>
                            <th><?php echo Lang::txt('Publishing Option'); ?></th>
                            <td><?php echo $this->status['publishType']; ?></td>
                        </tr>

                        <tr>
                            <th><?php echo Lang::txt('COM_TOOLS_DEVELOPMENT_TEAM'); ?></th>
                            <td><?= \Components\Tools\Helpers\Html::getDevTeam($this->status['developers']) ?></td>
                        </tr>
                        <tr>
                            <th colspan="2" class="toolinfo_hed"><?= Lang::txt('COM_TOOLS_DEVELOPER_TOOLS') ?></th>
                        </tr>
                        <tr>
                            <th colspan="2">
                            <!-- / tool admin icons -->
                                <ul class="developer actions">
                                    <?php
                                    $ticketUrl = Route::url(
                                        'index.php?option=com_support&task=ticket&id=' . $this->status['ticketid'],
                                    );
                                    ?>
                                    <li class="history"><a href="<?= $ticketUrl ?>"
                                        title="<?= Lang::txt('COM_TOOLS_HISTORY_TIPS') ?>">History</a></li>
                                    <?php if ($this->status['state'] != 'Registered') { // hide for tools in registered status?>
                                        <li class="wiki"><a href="<?= $developer_url . $project_path . $this->status['toolname'] ?>/wiki" title="<?= Lang::txt('COM_TOOLS_WIKI_TIPS') ?>">Wiki</a></li>
                                        <li class="sourcecode"><a href="<?= $developer_url . $project_path . $this->status['toolname'] ?>/browser" title="<?= Lang::txt('COM_TOOLS_SOURCE_TIPS') ?>"><?= Lang::txt('COM_TOOLS_SOURCE') ?></a></li>
                                        <li class="timeline"><a href="<?= $developer_url . $project_path . $this->status['toolname'] ?>/timeline" title="<?= Lang::txt('COM_TOOLS_TIMELINE_TIPS') ?>"><?= Lang::txt('COM_TOOLS_TIMELINE') ?></a></li>
                                    <?php } else { ?>
                                        <li class="wiki"><span
                                            class="disabled"><?= Lang::txt('COM_TOOLS_WIKI') ?></span></li>
                                        <li class="sourcecode"><span
                                            class="disabled"><?= Lang::txt('COM_TOOLS_SOURCE_CODE') ?></span></li>
                                        <li class="timeline"><span
                                            class="disabled"><?= Lang::txt('COM_TOOLS_TIMELINE') ?></span></li>
                                    <?php } ?>
                                    <li class="message"><a href="javascript:void(0);" title="<?= Lang::txt('COM_TOOLS_SEND_MESSAGE') . ' ' . Lang::txt('COM_TOOLS_TO') ?> <?= ($this->config->get('access-admin-component')) ? strtolower(Lang::txt('COM_TOOLS_DEVELOPMENT_TEAM')) : Lang::txt('COM_TOOLS_SITE_ADMIN') ?>" class="showmsg"><?= Lang::txt('COM_TOOLS_MESSAGE') ?></a></li>
                                    <?php if ($this->status['published'] != 1 && \Components\Tools\Helpers\Html::toolActive($this->status['state'])) {  // show cancel option only for tools under development?>
                                        <li class="canceltool"><a href="javascript:void(0);"
                                            title="<?= Lang::txt('COM_TOOLS_CANCEL_TIPS') ?>"
                                            class="showcancel"><?= Lang::txt('JCANCEL') ?></a></li>
                                    <?php } ?>
                                </ul>
                                <div id="ctCancel">
                                    <p class="error">
                                        <span
                                            class="cancel_warning"><?= Lang::txt('COM_TOOLS_CANCEL_WARNING') ?> </span>
                                        <?php
                                        $cancelUrl = Route::url(
                                            'index.php?option=' . $this->option . '&controller=' . $this->controller
                                                . '&task=cancel&app=' . $this->status['toolname'],
                                        );
                                        ?>
                                        <a href="<?= $cancelUrl ?>"><?= Lang::txt('COM_TOOLS_CANCEL_YES') ?></a>
                                        <span class="boundary">|</span> <a href="javascript:void(0);"
                                            class="hidecancel"><?= Lang::txt('COM_TOOLS_CANCEL_NO') ?></a>
                                    </p>
                                </div>
                                <div id="ctComment">
                                    <span class="closebox"><a href="javascript:void(0);" class="hidemsg">x</a></span>
                                    <h4><?= Lang::txt('COM_TOOLS_SEND_MESSAGE') . ' ' . Lang::txt('COM_TOOLS_TO') ?> <?= ($this->config->get('access-admin-component')) ? strtolower(Lang::txt('COM_TOOLS_DEVELOPMENT_TEAM')) : strtolower(Lang::txt('COM_TOOLS_SITE_ADMIN')) ?>:</h4>
                                    <?php
                                    $statusUrl = Route::url(
                                        'index.php?option=' . $this->option . '&controller=' . $this->controller
                                            . '&task=status&app=' . $this->status['toolname'],
                                    );
                                    ?>
                                    <form action="<?= $statusUrl ?>" method="post" id="commentForm">
                                    <?php if ($this->config->get('access-admin-component')) { ?>
                                        <fieldset>
                                            <label><input type="checkbox" name="access"
                                                value="1" /> <?= Lang::txt('COM_TOOLS_COMMENT_PRIVACY_TIPS') ?></label>
                                        </fieldset>
                                    <?php } ?>
                                        <fieldset>
                                            <textarea name="comment" cols="50" rows="5"></textarea>
                                        </fieldset>
                                        <fieldset>
                                            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                                            <input type="hidden" name="controller" value="<?= $this->controller ?>" />
                                            <input type="hidden" name="task" value="message" />
                                            <input type="hidden" name="id" value="<?= $this->status['toolid'] ?>" />
                                            <input type="hidden" name="app" value="<?= $this->status['toolname'] ?>" />
                                            <?php echo Html::input('token'); ?>
                                            <input type="submit" value="<?= Lang::txt('COM_TOOLS_SEND_MESSAGE') ?>" />
                                        </fieldset>
                                    </form>
                                </div>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>

            <?php if ($this->config->get('access-admin-component')) { ?>
                <div class="admin-container">
                    <?php
                    $statusUrl = Route::url(
                        'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app='
                            . $this->status['toolname'],
                    );
                    ?>
                    <form action="<?= $statusUrl ?>" method="post" id="hubForm" class="full">
                        <fieldset>
                            <legend><?php echo Lang::txt('COM_TOOLS_ADMIN_CONTROLS');?></legend>

                            <div class="admin grid">
                                <?php
                                $addrepoUrl = Route::url(
                                    'index.php?option=' . $this->option . '&controller=admin&task=addrepo&app='
                                        . $this->status['toolname'],
                                );
                                $titleText = Lang::txt('COM_TOOLS_COMMAND_ADD_REPO_TIPS');
                                ?>
                                <div class="col span3" id="createtool"><a class="icon-create btn admincall"
                                    data-action-txt="<?= Lang::txt('Creating tool project area...') ?>"
                                    href="<?= $addrepoUrl ?>"
                                    title="<?= $titleText ?>"><?= Lang::txt('COM_TOOLS_COMMAND_ADD_REPO') ?></a></div>
                                <?php
                                $installUrl = Route::url(
                                    'index.php?option=' . $this->option . '&controller=admin&task=install&app='
                                        . $this->status['toolname'],
                                );
                                $titleText = Lang::txt('COM_TOOLS_COMMAND_INSTALL_TIPS');
                                ?>
                                <div class="col span3" id="installtool"><a class="icon-install btn admincall"
                                    data-action-txt="<?= Lang::txt('Installing tool...') ?>" href="<?= $installUrl ?>"
                                    title="<?= $titleText ?>"><?= Lang::txt('COM_TOOLS_COMMAND_INSTALL') ?></a></div>
                                <?php
                                $publishUrl = Route::url(
                                    'index.php?option=' . $this->option . '&controller=admin&task=publish&app='
                                        . $this->status['toolname'],
                                );
                                $titleText = Lang::txt('COM_TOOLS_COMMAND_PUBLISH_TIPS');
                                ?>
                                <div class="col span3" id="publishtool"><a class="icon-publish btn admincall"
                                    data-action-txt="<?= Lang::txt('Publishing tool...') ?>" href="<?= $publishUrl ?>"
                                    title="<?= $titleText ?>"><?= Lang::txt('COM_TOOLS_COMMAND_PUBLISH') ?></a></div>
                                <?php
                                $retireUrl = Route::url(
                                    'index.php?option=' . $this->option . '&controller=admin&task=retire&app='
                                        . $this->status['toolname'],
                                );
                                $titleText = Lang::txt('COM_TOOLS_COMMAND_RETIRE_TIPS');
                                ?>
                                <div class="col span3 omega" id="retiretool"><a class="icon-retire btn admincall"
                                    data-action-txt="<?= Lang::txt('Retiring tool...') ?>" href="<?= $retireUrl ?>"
                                    title="<?= $titleText ?>"><?= Lang::txt('COM_TOOLS_COMMAND_RETIRE') ?></a></div>
                            </div>
                            <div id="ctSending"></div>
                            <div id="ctSuccess"></div>

                            <?php
                            $status = $this->status;
                            $selState = function ($v) use ($status) {
                                echo ($status['state'] == $v) ? ' selected="selected"' : '';
                            };
                            $selPri = function ($v) use ($status) {
                                echo ($status['priority'] == $v) ? ' selected="selected"' : '';
                            };
    ?>
                            <div class="grid">
                                <div class="col span6">
                                    <div class="form-group">
                                        <label for="tool-newstate">
                                            <?php echo Lang::txt('COM_TOOLS_FLIP_STATUS');?>:
                                            <select name="newstate" id="tool-newstate" class="form-control">
                                                <option value="1"<?= $stateSelected(1) ?>>
                                                    <?= Lang::txt('COM_TOOLS_REGISTERED') ?>
                                                </option>
                                                <option value="2"<?= $stateSelected(2) ?>>
                                                    <?= Lang::txt('COM_TOOLS_CREATED') ?>
                                                </option>
                                                <option value="3"<?= $stateSelected(3) ?>>
                                                    <?= Lang::txt('COM_TOOLS_UPLOADED') ?>
                                                </option>
                                                <option value="4"<?= $stateSelected(4) ?>>
                                                    <?= Lang::txt('COM_TOOLS_INSTALLED') ?>
                                                </option>
                                                <option value="5"<?= $stateSelected(5) ?>>
                                                    <?= Lang::txt('COM_TOOLS_UPDATED') ?>
                                                </option>
                                                <option value="6"<?= $stateSelected(6) ?>>
                                                    <?= Lang::txt('COM_TOOLS_APPROVED') ?>
                                                </option>
                                                <option value="7"<?= $stateSelected(7) ?>>
                                                    <?= Lang::txt('COM_TOOLS_PUBLISHED') ?>
                                                </option>
                                                <?php if ($this->status['published'] == 1) { // admin can retire only tools that have a published flag on?>
                                                    <option value="8"<?= $stateSelected(8) ?>>
                                                        <?= Lang::txt('COM_TOOLS_RETIRED') ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </label>
                                    </div>
                                </div>
                                <div class="col span6 omega">
                                    <div class="form-group">
                                        <label for="tool-priority">
                                            <?php echo Lang::txt('COM_TOOLS_PRIORITY');?>:
                                            <select name="priority" id="tool-priority" class="form-control">
                                                <option value="3"<?= $prioritySelected(3) ?>>
                                                    <?= Lang::txt('COM_TOOLS_NORMAL') ?>
                                                </option>
                                                <option value="2"<?= $prioritySelected(2) ?>>
                                                    <?= Lang::txt('COM_TOOLS_HIGH') ?>
                                                </option>
                                                <option value="1"<?= $prioritySelected(1) ?>>
                                                    <?= Lang::txt('COM_TOOLS_CRITICAL') ?>
                                                </option>
                                                <option value="4"<?= $prioritySelected(4) ?>>
                                                    <?= Lang::txt('COM_TOOLS_LOW') ?>
                                                </option>
                                                <option value="5"<?= $prioritySelected(5) ?>>
                                                    <?= Lang::txt('COM_TOOLS_LOWEST') ?>
                                                </option>
                                            </select>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                            <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
                            <input type="hidden" name="task" value="update" />
                            <input type="hidden" name="id" value="<?php echo $this->status['toolid']; ?>" />
                            <input type="hidden" name="app" value="<?php echo $this->status['toolname']; ?>" />

                            <?php echo Html::input('token'); ?>

                            <div class="form-group">
                                <label for="comment">
                                    <?= Lang::txt('COM_TOOLS_MESSAGE_TO_DEV_TEAM') . ' ('
                                        . Lang::txt('COM_TOOLS_OPTIONAL') . ')' ?>
                                    <textarea name="comment" id="comment" class="form-control" cols="40"
                                        rows="5"></textarea>
                                </label>
                            </div>

                            <p class="submit">
                                <input type="submit" class="btn btn-success"
                                    value="<?= Lang::txt('COM_TOOLS_APPLY_CHANGE') ?>" />
                            </p>
                        </fieldset>
                    </form>
                </div>
            <?php } ?>
        </div><!-- / .col span-half -->
        <div class="col span5 omega">
            <div id="whatsnext">
                <h2 class="nextaction"><?php echo Lang::txt('COM_TOOLS_WHAT_NEXT');?></h2>
                <?php
                $statusUrl = Route::url(
                    'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app='
                        . $this->status['toolname'],
                );
                ?>
                <form action="<?= $statusUrl ?>" method="post" id="statusForm">
                    <fieldset>
                        <input type="hidden" name="option" value="<?php echo $this->option ?>" />
                        <input type="hidden" name="task" value="update" />
                        <?php echo Html::input('token'); ?>
                        <input type="hidden" name="id" value="<?php echo $this->status['toolid']?>" />
                        <input type="hidden" name="toolname" value="<?php echo $this->status['toolname']?>" />
                        <input type="hidden" name="newstate" id="newstate" value="" />
                    </fieldset>
                </form>
                <?php
                $sitename = Config::get('sitename');
                $hubShortURL = rtrim(str_replace('https://', '', Request::base()), '/'); //$hubShortURL;

                // get tool access text
                $toolaccess = \Components\Tools\Helpers\Html::getToolAccess(
                    $this->status['exec'],
                    $this->status['membergroups'],
                );
                $live_site = rtrim(Request::base(), '/');
                $developer_url = $live_site = "https://" . preg_replace('#^(https://|http://)#', '', $live_site);

                // get configurations/ defaults
                $developer_site = $this->config->get('developer_site', 'hubFORGE');
                $rappture_url   = $this->config->get('rappture_url', '');
                $learn_url      = $this->config->get('learn_url', '');
                $project_path   = $this->config->get('project_path', '/tools/');
                $dev_suffix     = $this->config->get('dev_suffix', '_dev');

                // set common paths
                $this->statuspath = Route::url(
                    'index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app='
                        . $this->status['toolname'],
                );
                $testpath = Route::url(
                    'index.php?option=' . $this->option . '&controller=sessions&task=invoke&app='
                        . $this->status['toolname'] . '&version=test',
                );

                switch ($this->status['state']) {
                    //  registered
                    case 1:
                        ?>
                    <p>
                        <?= Lang::txt('COM_TOOLS_TEAM_WILL_CREATE') ?> <a class="developer-site" href="<?= $developer_url ?>/tools"><?= $developer_site ?></a>, <?= Lang::txt('COM_TOOLS_WHATSNEXT_REGISTERED_INSTRUCTIONS') ?>.
                        <?= Lang::txt('COM_TOOLS_WHATSNEXT_IT_HAS_BEEN') ?> <?= \Components\Tools\Helpers\Html::timeAgo($this->status['changed']) ?> <?= Lang::txt('COM_TOOLS_WHATSNEXT_SINCE_YOUR_REQUEST') ?>.
                        <?= Lang::txt('COM_TOOLS_WHATSNEXT_YOU_WILL_RECEIVE_RESPONSE') ?> 24 <?= Lang::txt('COM_TOOLS_HOURS') ?>
                    </p>
                    <h4><?php echo Lang::txt('COM_TOOLS_WHATSNEXT_REMAINING_STEPS'); ?>:</h4>
                    <ul>
                        <li class="complete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_REGISTER'); ?>
                            <?php echo $sitename; ?>
                        </li>
                        <li class="incomplete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_UPLOAD_CODE'); ?>
                            <?php echo $developer_site; ?>
                        </li>
                        <?php if ($this->status['resource_modified'] == '1') { ?>
                        <li class="complete">
                            <?php echo Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE'); ?>.
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&task=preview&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="preview-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_PREVIEW') ?></a> |
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&step=1&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="edit-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_TODO_EDIT_PAGE') ?>...</a>
                        </li>
                        <?php } else { ?>
                        <li class="todo">
                            <?php echo Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE'); ?>.
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&step=1&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="create-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_TODO_CREATE_PAGE') ?>...</a>
                        </li>
                        <?php } ?>
                        <li class="incomplete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_TEST_AND_APPROVE'); ?>
                        </li>
                        <li class="incomplete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_PUBLISH'); ?> <?php echo $hubShortURL; ?>
                        </li>
                    </ul>
                        <?php
                        break;

                    //  created
                    case 2:
                        ?>
                    <p>
                        <?= ucfirst(Lang::txt('COM_TOOLS_THE')) ?> <?= $sitename ?>  <?= Lang::txt('COM_TOOLS_WHATSNEXT_AREA_CREATED1') ?>
                        <a href="<?= $developer_url . $project_path . $this->status['toolname'] ?>/wiki"><?= Lang::txt('COM_TOOLS_PROJECT_AREA') ?></a>
                        <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_AREA_CREATED2'); ?>
                        <a href="<?php echo $developer_url . '/tools'; ?>"><?php echo $developer_site; ?></a>.
                    </p>
                    <p>
                        <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_FOLLOW_STEPS'); ?>:
                    </p>
                    <ul>
                        <?php if (!empty($learn_url)) { ?>
                        <li><a href="<?= $learn_url ?>"><?= Lang::txt('COM_TOOLS_LEARN_MORE') ?></a> <?= Lang::txt('COM_TOOLS_WHATSNEXT_ABOUT_UPLOADING') ?></li>
                        <?php } ?>
                        <?php if (!empty($rappture_url)) { ?>
                        <li><?= Lang::txt('COM_TOOLS_LEARN_MORE') ?> <?= Lang::txt('COM_TOOLS_ABOUT') ?> <?= Lang::txt('COM_TOOLS_THE') ?> <a href="<?= $rappture_url ?>">Rappture toolkit</a>.</li>
                        <?php } ?>
                    <li><?php echo Lang::txt('COM_TOOLS_WHATSNEXT_GIT_INSTRUCTIONS'); ?></li>
                    <li><a class="developer-wiki" href="<?= $developer_url . $project_path . $this->status['toolname'] ?>/wiki/GettingStarted"><?= Lang::txt('COM_TOOLS_WHATSNEXT_FOLLOW_THESE_INSTRUCTIONS') ?></a> <?= Lang::txt('COM_TOOLS_WHATSNEXT_TO_ACCESS_CODE') ?>.</li>
                    </ul>
                    <h2><?php echo Lang::txt('COM_TOOLS_WHATSNEXT_WE_ARE_WAITING'); ?></h2>
                    <p><?php echo Lang::txt('COM_TOOLS_WHATSNEXT_CREATED_LET_US_KNOW'); ?>:</p>
                    <ul>
                        <li class="todo">
                            <span id="Uploaded">
                                <?php
                                $updateUrl = Route::url(
                                    'index.php?option=' . $this->option . '&controller=' . $this->controller
                                        . '&task=update&newstate=Uploaded&app=' . $this->status['toolname'],
                                );
                                ?>
                                <a href="<?= $updateUrl ?>" class="flip">
                                    <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_CREATED_CODE_UPLOADED'); ?>
                                </a>
                            </span>
                        </li>
                    </ul>
                    <h4><?php echo Lang::txt('COM_TOOLS_WHATSNEXT_REMAINING_STEPS'); ?>:</h4>
                    <ul>
                        <li class="complete"><?= Lang::txt('COM_TOOLS_WHATSNEXT_REGISTER') ?> <?= $sitename ?></li>
                        <li class="incomplete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_UPLOAD_COMMIT_FINAL_CODE'); ?>
                            <span id="Uploaded_">
                                <?php
                                $updateUrl = Route::url(
                                    'index.php?option=' . $this->option . '&controller=' . $this->controller
                                        . '&task=update&newstate=Uploaded&app=' . $this->status['toolname'],
                                );
                                ?>
                                <a href="<?= $updateUrl ?>" class="flip">
                                    <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_DONE'); ?>
                                </a>
                            </span>
                            <br /><a class="developer-wiki" href="<?= $developer_url . $project_path . $this->status['toolname'] ?>/wiki/GettingStarted"><?= Lang::txt('COM_TOOLS_WHATSNEXT_UPLOAD_HOW_DO_I_DO_THIS') ?></a>
                        </li>
                        <?php if ($this->status['resource_modified'] == '1') { ?>
                        <li class="complete">
                            <?php echo Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE'); ?>.
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&task=preview&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="preview-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_PREVIEW') ?></a> |
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&step=1&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="edit-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_TODO_EDIT_PAGE') ?>...</a>
                        </li>
                        <?php } else { ?>
                        <li class="todo">
                            <?php echo Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE'); ?>.
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&step=1&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="create-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_TODO_CREATE_PAGE') ?>...</a>
                        </li>
                        <?php } ?>
                        <li class="incomplete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_TEST_AND_APPROVE'); ?>
                        </li>
                        <li class="incomplete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_PUBLISH'); ?> <?php echo $hubShortURL; ?>
                        </li>
                    </ul>
                        <?php
                        break;

                    //  uploaded
                    case 3:
                        ?>
                    <p>
                        <?= ucfirst(Lang::txt('COM_TOOLS_THE')) ?> <?= $sitename ?> <?= Lang::txt('COM_TOOLS_WHATSNEXT_UPLOADED_TEAM_NEEDS') ?> <?= $sitename ?> <?= Lang::txt('COM_TOOLS_WHATSNEXT_UPLOADED_SO_YOU_CAN_TEST') ?>.
                        <?= Lang::txt('COM_TOOLS_WHATSNEXT_IT_HAS_BEEN') ?> <?= \Components\Tools\Helpers\Html::timeAgo($this->status['changed']) ?> <?= Lang::txt('COM_TOOLS_WHATSNEXT_SINCE_LAST_STATUS_CHANGE') ?>.
                        <?= Lang::txt('COM_TOOLS_WHATSNEXT_YOU_WILL_RECEIVE_RESPONSE') ?> 3 <?= Lang::txt('COM_TOOLS_DAYS') ?>.
                    </p>
                    <h4><?php echo Lang::txt('COM_TOOLS_WHATSNEXT_REMAINING_STEPS'); ?>:</h4>
                    <ul>
                        <li class="complete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_REGISTER'); ?> <?php echo $sitename; ?>
                        </li>
                        <li class="complete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_UPLOAD_CODE'); ?> <?php echo $developer_site; ?>
                        </li>
                        <?php if ($this->status['resource_modified'] == '1') { ?>
                        <li class="complete">
                            <?php echo Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE'); ?>.
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&task=preview&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="preview-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_PREVIEW') ?></a> |
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&step=1&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="edit-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_TODO_EDIT_PAGE') ?>...</a>
                        </li>
                        <?php } else { ?>
                        <li class="todo">
                            <?php echo Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE'); ?>.
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&step=1&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="create-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_TODO_CREATE_PAGE') ?>...</a>
                        </li>
                        <?php } ?>
                        <li class="incomplete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_TEST_AND_APPROVE'); ?>
                        </li>
                        <li class="incomplete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_PUBLISH'); ?> <?php echo $hubShortURL; ?>
                        </li>
                    </ul>
                        <?php
                        break;

                    //  installed
                    case 4:
                        ?>
                    <p>
                        <?= Lang::txt('COM_TOOLS_WHATSNEXT_INSTALLED_CODE_READY') ?> <?= $hubShortURL ?>. <?= Lang::txt('COM_TOOLS_WHATSNEXT_INSTALLED_PLS_TEST') ?>:
                    </p>
                    <ul>
                        <li class="todo">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_INSTALLED_TEST'); ?>:
                            <a class="btn btn-primary launchtool"
                                href="<?= $testpath ?>"><?= Lang::txt('COM_TOOLS_LAUNCH_TOOL') ?></a>
                        </li>
                        <li class="todo">
                        <?php if ($this->status['resource_modified']) { ?>
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&task=preview&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="preview-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_TODO_REVIEW_RES_PAGE') ?></a>
                        <?php } else { ?>
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&step=1&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="create-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_TODO_CREATE_PAGE') ?></a>
                            <p class="warning">
                                <?= Lang::txt('COM_TOOLS_PLEASE') ?> <a href="<?= Route::url('index.php?option=' . $this->option . '&task=resource&step=1&app=' . $this->status['toolname']) ?>"><?= strtolower(Lang::txt('COM_TOOLS_CREATE')) ?></a> <?= Lang::txt('COM_TOOLS_WHATSNEXT_PAGE_DESC') ?>.
                            </p>
                        <?php } ?>
                        </li>
                    </ul>
                    <h2><?php echo Lang::txt('COM_TOOLS_WHATSNEXT_WE_ARE_WAITING'); ?></h2>
                    <p><?php echo Lang::txt('COM_TOOLS_WHATSNEXT_INSTALLED_CLICK_AFTER_TESTING'); ?>:</p>
                    <ul>
                        <?php if ($this->status['resource_modified']) { ?>
                        <li class="todo">
                            <?php
                            $updateUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=' . $this->controller
                                    . '&task=update&newstate=Approved&app=' . $this->status['toolname'],
                            );
                            ?>
                            <span id="Approved"><a href="<?= $updateUrl ?>"
                                class="flip" ><?= Lang::txt('COM_TOOLS_WHATSNEXT_INSTALLED_TOOL_WORKS') ?></a></span>
                        </li>
                        <?php } else { ?>
                        <li class="todo_disabled">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_INSTALLED_TOOL_WORKS'); ?>
                        </li>
                        <?php } ?>
                    </ul>
                    <p><?php echo Lang::txt('COM_TOOLS_WHATSNEXT_INSTALLED_NEED_CHANGES'); ?>:</p>
                    <ul>
                        <li class="todo">
                            <?php
                            $updateUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=' . $this->controller
                                    . '&task=update&newstate=Updated&app=' . $this->status['toolname'],
                            );
                            ?>
                            <span id="Updated"><a href="<?= $updateUrl ?>"
                                class="flip" ><?= Lang::txt('COM_TOOLS_WHATSNEXT_CODE_FIXED_PLS_INSTALL') ?>.</a></span>
                        </li>
                    </ul>
                    <h4><?php echo Lang::txt('COM_TOOLS_WHATSNEXT_REMAINING_STEPS'); ?>:</h4>
                    <ul>
                        <li class="complete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_REGISTER'); ?> <?php echo $sitename; ?>
                        </li>
                        <li class="complete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_UPLOAD_CODE'); ?> <?php echo $developer_site; ?>
                        </li>
                        <?php if ($this->status['resource_modified'] == '1') { ?>
                        <li class="complete">
                            <?php echo Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE'); ?>.
                            <?php
                            $linkUrl = Route::url(
                                'index.php?option=com_resources&id=' . $this->status['resourceid'] . '&rev=dev',
                            );
                            ?>
                            <a class="preview-resource"
                                href="<?= $linkUrl ?>"><?= Lang::txt('COM_TOOLS_PREVIEW') ?></a> |
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&step=1&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="edit-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_TODO_EDIT_PAGE') ?>...</a>
                        </li>
                        <?php } else { ?>
                        <li class="todo">
                            <?php echo Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE'); ?>.
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&step=1&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="create-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_TODO_CREATE_PAGE') ?>...</a>
                        </li>
                        <?php } ?>
                        <li class="todo">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_TEST_AND_APPROVE'); ?>.
                        <?php if ($this->status['resource_modified'] == '1') { ?>
                            <?php
                            $updateUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=' . $this->controller
                                    . '&task=update&newstate=Approved&app=' . $this->status['toolname'],
                            );
                            ?>
                            <span id="Approved_"><a href="<?= $updateUrl ?>"
                                class="flip"><?= Lang::txt('COM_TOOLS_WHATSNEXT_I_APPROVE') ?></a></span>
                        <?php } else { ?>
                            <span class="disabled"><?php echo Lang::txt('COM_TOOLS_WHATSNEXT_I_APPROVE'); ?></span>
                        <?php } ?>
                            | <span id="Updated_"><a href="<?= Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=update&newstate=Updated&app=' . $this->status['toolname']) ?>" class="flip"><?= Lang::txt('COM_TOOLS_WHATSNEXT_CHANGES_MADE') ?></a></span>
                        </li>
                        <li class="incomplete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_PUBLISH'); ?> <?php echo $hubShortURL; ?>
                        </li>
                    </ul>
                        <?php
                        break;

                    //  updated
                    case 5:
                        ?>
                    <p>
                        <?= ucfirst(Lang::txt('COM_TOOLS_THE')) ?> <?= $sitename ?> <?= Lang::txt('COM_TOOLS_WHATSNEXT_UPLOADED_TEAM_NEEDS') ?> <?= $sitename ?> <?= Lang::txt('COM_TOOLS_WHATSNEXT_UPLOADED_SO_YOU_CAN_TEST') ?>.
                        <?= Lang::txt('COM_TOOLS_WHATSNEXT_IT_HAS_BEEN') ?> <?= \Components\Tools\Helpers\Html::timeAgo($this->status['changed']) ?> <?= Lang::txt('COM_TOOLS_WHATSNEXT_SINCE_LAST_STATUS_CHANGE') ?>.
                        <?= Lang::txt('COM_TOOLS_WHATSNEXT_YOU_WILL_RECEIVE_RESPONSE') ?> 3 <?= Lang::txt('COM_TOOLS_DAYS') ?>.
                    </p>
                    <h4><?php echo Lang::txt('COM_TOOLS_WHATSNEXT_REMAINING_STEPS'); ?>:</h4>
                    <ul>
                        <li class="complete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_REGISTER'); ?> <?php echo $sitename; ?>
                        </li>
                        <li class="complete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_UPLOAD_CODE'); ?> <?php echo $developer_site; ?>
                        </li>
                        <?php if ($this->status['resource_modified'] == '1') { ?>
                        <li class="complete">
                            <?php echo Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE'); ?>.
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&task=preview&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="preview-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_PREVIEW') ?></a> |
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&step=1&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="edit-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_TODO_EDIT_PAGE') ?>...</a>
                        </li>
                        <?php } else { ?>
                        <li class="todo">
                            <?php echo Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE'); ?>.
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&step=1&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="create-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_TODO_CREATE_PAGE') ?>...</a>
                        </li>
                        <?php } ?>
                        <li class="incomplete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_TEST_AND_APPROVE'); ?>
                        </li>
                        <li class="incomplete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_PUBLISH'); ?> <?php echo $hubShortURL; ?>
                        </li>
                    </ul>
                        <?php
                        break;

                    //  approved
                    case 6:
                        ?>
                    <p>
                        <?= ucfirst(Lang::txt('COM_TOOLS_THE')) . ' ' . $sitename . ' '
                            . Lang::txt('COM_TOOLS_WHATSNEXT_APPROVED_TEAM_WILL_FINALIZE') . ' '
                            . Lang::txt('COM_TOOLS_WHATSNEXT_IT_HAS_BEEN') . ' '
                            . \Components\Tools\Helpers\Html::timeAgo($this->status['changed']) . ' '
                            . Lang::txt('COM_TOOLS_WHATSNEXT_APPROVED_SINCE') . '  '
                            . Lang::txt('COM_TOOLS_WHATSNEXT_APPROVED_WHAT_WILL_HAPPEN') . ' ' . $toolaccess ?>.
                    </p>
                    <p>
                        <?= Lang::txt('COM_TOOLS_WHATSNEXT_APPROVED_PLS_CLICK') ?> <?= $sitename ?>: <br />
                        <?php
                        $linkUrl = Route::url(
                            'index.php?option=' . $this->option . '&app=' . $this->status['toolname'],
                        );
                        ?>
                        <a class="view-tool" href="<?= $linkUrl ?>"><?= Route::url('index.php?option=' . $this->option . '&app=' . $this->status['toolname']) ?></a>
                    </p>
                    <h4><?php echo Lang::txt('COM_TOOLS_WHATSNEXT_REMAINING_STEPS'); ?>:</h4>
                    <ul>
                        <li class="complete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_REGISTER'); ?> <?php echo $sitename; ?>
                        </li>
                        <li class="complete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_UPLOAD_CODE'); ?> <?php echo $developer_site; ?>
                        </li>
                        <?php if ($this->status['resource_modified'] == '1') { ?>
                        <li class="complete">
                            <?php echo Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE'); ?>.
                            <?php
                            $linkUrl = Route::url(
                                'index.php?option=com_resources&id=' . $this->status['resourceid'] . '&rev=dev',
                            );
                            ?>
                            <a class="preview-resource"
                                href="<?= $linkUrl ?>"><?= Lang::txt('COM_TOOLS_PREVIEW') ?></a> |
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&step=1&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="edit-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_TODO_EDIT_PAGE') ?>...</a>
                        </li>
                        <?php } else { ?>
                        <li class="todo">
                            <?php echo Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE'); ?>.
                            <?php
                            $resourceUrl = Route::url(
                                'index.php?option=' . $this->option . '&task=resource&step=1&app='
                                    . $this->status['toolname'],
                            );
                            ?>
                            <a class="create-resource"
                                href="<?= $resourceUrl ?>"><?= Lang::txt('COM_TOOLS_TODO_CREATE_PAGE') ?>...</a>
                        </li>
                        <?php } ?>
                        <li class="complete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_TEST_AND_APPROVE'); ?>
                        </li>
                        <li class="incomplete">
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_PUBLISH'); ?> <?php echo $hubShortURL; ?>
                            <?php
                            $updateUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=' . $this->controller
                                    . '&task=update&newstate=Updated&app=' . $this->status['toolname'],
                            );
                            ?>
                            <br /><span id="Updated"><a href="<?= $updateUrl ?>"
                                class="flip" ><?= Lang::txt('COM_TOOLS_WHATSNEXT_WAIT') ?></a></span>
                        </li>
                    </ul>
                        <?php
                        break;

                    //  published
                    case 7:
                        ?>
                    <p>
                        <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_PUBLISHED_MSG'); ?>: <br />
                        <?php
                        $linkUrl = Route::url(
                            'index.php?option=' . $this->option . '&controller=' . $this->controller . '&app='
                                . $this->status['toolname'],
                        );
                        ?>
                        <a class="view-tool" href="<?= $linkUrl ?>"><?= Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&app=' . $this->status['toolname']) ?></a>
                    </p>
                    <h3><?php echo Lang::txt('COM_TOOLS_WHATSNEXT_YOUR_OPTIONS'); ?>:</h3>
                    <ul class="youroptions">
                        <li>
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_CHANGES_MADE'); ?>
                            <?php
                            $updateUrl = Route::url(
                                'index.php?option=' . $this->option . '&controller=' . $this->controller
                                    . '&task=update&newstate=Updated&app=' . $this->status['toolname'],
                            );
                            ?>
                            <span id="Updated"><a href="<?= $updateUrl ?>"
                                class="flip"><?= Lang::txt('COM_TOOLS_WHATSNEXT_PUBLISHED_PLS_INSTALL') ?></a></span>
                        </li>
                    </ul>
                        <?php
                        break;

                    //  retired
                    case 8:
                        ?>
                    <p>
                        <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_RETIRED_FROM'); ?> <?php echo $hubShortURL; ?>.
                        <?= Lang::txt('COM_TOOLS_CONTACT') ?> <?= $sitename ?> <?= Lang::txt('COM_TOOLS_CONTACT_SUPPORT_TO_REPUBLISH') ?>.
                    </p>
                    <h3><?php echo Lang::txt('COM_TOOLS_WHATSNEXT_YOUR_OPTIONS'); ?>:</h3>
                    <ul class="youroptions">
                        <li>
                            <?php echo Lang::txt('COM_TOOLS_WHATSNEXT_RETIRED_WANT_REPUBLISH'); ?>.
                            <span id="Updated">
                                <?php
                                $updateUrl = Route::url(
                                    'index.php?option=' . $this->option . '&controller=' . $this->controller
                                        . '&task=update&newstate=Updated&app=' . $this->status['toolname'],
                                );
                                ?>
                                <a href="<?= $updateUrl ?>"
                                    class="flip"><?= Lang::txt('COM_TOOLS_WHATSNEXT_RETIRED_PLS_REPUBLISH') ?></a>
                            </span>
                        </li>
                    </ul>
                        <?php
                        break;

                    //  abandoned
                    case 9:
                        ?>
                    <p>
                        <?= Lang::txt('COM_TOOLS_WHATSNEXT_ABANDONED_MSG') ?> <?= $sitename ?> <?= Lang::txt('COM_TOOLS_WHATSNEXT_ABANDONED_CONTACT') ?>.
                    </p>
                        <?php
                        break;
                }
                ?>
            </div><!-- / #whatsnext -->
        </div><!-- / .col span-half omega -->
    </div><!-- / .grid -->
</section><!-- / .main section -->
