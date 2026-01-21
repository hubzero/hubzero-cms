<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Groups\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_GROUPS'), 'groups');

if ($canDo->get('core.admin')) {
    Toolbar::preferences('com_groups', '550');
    Toolbar::spacer();
}
if ($canDo->get('core.manage') && $this->config->get('super_gitlab', 0)) {
    Toolbar::custom('update', 'refresh', '', 'COM_GROUPS_UPDATE_CODE');
    Toolbar::spacer();
}
if ($canDo->get('core.edit.state')) {
    Toolbar::publishList();
    Toolbar::unpublishList();
    Toolbar::archiveList();
    Toolbar::spacer();
}
if ($canDo->get('core.create')) {
    Toolbar::addNew();
}
if ($canDo->get('core.edit')) {
    Toolbar::editList();
}
if ($canDo->get('core.delete')) {
    Toolbar::deleteList('COM_GROUPS_DELETE_CONFIRM', 'delete');
}
Toolbar::spacer();
Toolbar::help('groups');

$this->css();

Html::behavior('tooltip');

$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
?>

<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="grid">
            <div class="col span4">
                <label for="filter_search">
                    <?php echo Lang::txt('COM_GROUPS_SEARCH'); ?>:
                </label>
                <?php
                $searchVal = $this->escape($this->filters['search']);
                $searchTxt = Lang::txt('COM_GROUPS_SEARCH');
                ?>
                <input
                    type="text"
                    name="search"
                    id="filter_search"
                    class="filter"
                    value="<?php echo $searchVal; ?>"
                    placeholder="<?php echo $searchTxt; ?>"
                />

                <input
                    type="submit"
                    value="<?php echo Lang::txt('COM_GROUPS_GO'); ?>"
                />
                <button type="button" class="filter-clear">
                    <?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?>
                </button>
            </div>
            <div class="col span8 align-right">
                <?php
                $typeFilter = $this->filters['type'][0];
                $typeTxt = Lang::txt('COM_GROUPS_TYPE');
                ?>
                <label for="filter-type">
                    <?php echo $typeTxt; ?>:
                </label>
                <select
                    name="type"
                    id="filter-type"
                    class="filter filter-submit"
                >
                    <option
                        value="all"
                        <?php if ($typeFilter == 'all') {
                            echo 'selected="selected"';
                        } ?>
                    ><?php echo $typeTxt; ?></option>
                    <option
                        value="hub"
                        <?php if ($typeFilter == 'hub') {
                            echo 'selected="selected"';
                        } ?>
                    >Hub</option>
                    <option
                        value="super"
                        <?php if ($typeFilter == 'super') {
                            echo 'selected="selected"';
                        } ?>
                    >Super</option>
                    <?php if ($canDo->get('core.admin')) { ?>
                        <option
                            value="system"
                            <?php if ($typeFilter == 'system') {
                                echo 'selected="selected"';
                            } ?>
                        >System</option>
                    <?php } ?>
                    <option
                        value="project"
                        <?php if ($typeFilter == 'project') {
                            echo 'selected="selected"';
                        } ?>
                    >Project</option>
                    <option
                        value="course"
                        <?php if ($typeFilter == 'course') {
                            echo 'selected="selected"';
                        } ?>
                    >Course</option>
                </select>

                <?php
                $discFilter = $this->filters['discoverability'];
                $discTxt = Lang::txt('COM_GROUPS_DISCOVERABILITY');
                $visTxt = Lang::txt('COM_GROUPS_DISCOVERABILITY_VISIBLE');
                $hidTxt = Lang::txt('COM_GROUPS_DISCOVERABILITY_HIDDEN');
                ?>
                <label for="filter-discoverability">
                    <?php echo $discTxt; ?>:
                </label>
                <select
                    name="discoverability"
                    id="filter-discoverability"
                    class="filter filter-submit"
                >
                    <option
                        value=""
                        <?php if ($discFilter == null) {
                            echo 'selected="selected"';
                        } ?>
                    ><?php echo $discTxt; ?></option>
                    <option
                        value="0"
                        <?php if ($discFilter == 0 && $discFilter != null) {
                            echo 'selected="selected"';
                        } ?>
                    ><?php echo $visTxt; ?></option>
                    <option
                        value="1"
                        <?php if ($discFilter == 1) {
                            echo 'selected="selected"';
                        } ?>
                    ><?php echo $hidTxt; ?></option>
                </select>

                <?php
                $policyFilter = $this->filters['policy'];
                $policyTxt = Lang::txt('COM_GROUPS_JOIN_POLICY');
                $publicTxt = Lang::txt('COM_GROUPS_JOIN_POLICY_PUBLIC');
                $restrictTxt = Lang::txt('COM_GROUPS_JOIN_POLICY_RESTRICTED');
                $inviteTxt = Lang::txt('COM_GROUPS_JOIN_POLICY_INVITE');
                $closedTxt = Lang::txt('COM_GROUPS_JOIN_POLICY_CLOSED');
                ?>
                <label for="filter-policy">
                    <?php echo $policyTxt; ?>:
                </label>
                <select
                    name="policy"
                    id="filter-policy"
                    class="filter filter-submit"
                >
                    <option
                        value=""
                        <?php if ($policyFilter == '') {
                            echo 'selected="selected"';
                        } ?>
                    ><?php echo $policyTxt; ?></option>
                    <option
                        value="open"
                        <?php if ($policyFilter == 'open') {
                            echo 'selected="selected"';
                        } ?>
                    ><?php echo $publicTxt; ?></option>
                    <option
                        value="restricted"
                        <?php if ($policyFilter == 'restricted') {
                            echo 'selected="selected"';
                        } ?>
                    ><?php echo $restrictTxt; ?></option>
                    <option
                        value="invite"
                        <?php if ($policyFilter == 'invite') {
                            echo 'selected="selected"';
                        } ?>
                    ><?php echo $inviteTxt; ?></option>
                    <option
                        value="closed"
                        <?php if ($policyFilter == 'closed') {
                            echo 'selected="selected"';
                        } ?>
                    ><?php echo $closedTxt; ?></option>
                </select>

                <?php
                $stateFilter = $this->filters['state'];
                $stateTxt = Lang::txt('COM_GROUPS_STATE');
                $unpubTxt = Lang::txt('COM_GROUPS_UNPUBLISHED');
                $pubTxt = Lang::txt('COM_GROUPS_PUBLISHED');
                $archTxt = Lang::txt('COM_GROUPS_ARCHIVED');
                ?>
                <label for="filter-state">
                    <?php echo $stateTxt; ?>:
                </label>
                <select
                    name="state"
                    id="filter-state"
                    class="filter filter-submit"
                >
                    <option
                        value="-1"
                        <?php if ($stateFilter == -1) {
                            echo 'selected="selected"';
                        } ?>
                    ><?php echo $stateTxt; ?></option>
                    <option
                        value="0"
                        <?php if ($stateFilter == 0) {
                            echo 'selected="selected"';
                        } ?>
                    ><?php echo $unpubTxt; ?></option>
                    <option
                        value="1"
                        <?php if ($stateFilter == 1) {
                            echo 'selected="selected"';
                        } ?>
                    ><?php echo $pubTxt; ?></option>
                    <option
                        value="2"
                        <?php if ($stateFilter == 2) {
                            echo 'selected="selected"';
                        } ?>
                    ><?php echo $archTxt; ?></option>
                </select>
            </div>
        </div>
    </fieldset>

    <table class="adminlist">
        <thead>
            <tr>
                <th scope="col">
                    <input
                        type="checkbox"
                        name="checkall-toggle"
                        id="checkall-toggle"
                        value=""
                        class="checkbox-toggle toggle-all"
                    />
                    <label
                        for="checkall-toggle"
                        class="sr-only visually-hidden"
                    ><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
                </th>
                <?php
                $sortDir = @$this->filters['sort_Dir'];
                $sort = @$this->filters['sort'];
                $idSort = Html::grid(
                    'sort',
                    'COM_GROUPS_ID',
                    'gidNumber',
                    $sortDir,
                    $sort
                );
                $nameSort = Html::grid(
                    'sort',
                    'COM_GROUPS_NAME',
                    'description',
                    $sortDir,
                    $sort
                );
                $cnSort = Html::grid(
                    'sort',
                    'COM_GROUPS_CN',
                    'cn',
                    $sortDir,
                    $sort
                );
                $typeSort = Html::grid(
                    'sort',
                    'COM_GROUPS_TYPE',
                    'type',
                    $sortDir,
                    $sort
                );
                $pubSort = Html::grid(
                    'sort',
                    'COM_GROUPS_PUBLISHED',
                    'published',
                    $sortDir,
                    $sort
                );
                $appSort = Html::grid(
                    'sort',
                    'COM_GROUPS_APPROVED',
                    'approved',
                    $sortDir,
                    $sort
                );
                ?>
                <th scope="col" class="priority-6">
                    <?php echo $idSort; ?>
                </th>
                <th scope="col" class="priority-4">
                    <?php echo $nameSort; ?>
                </th>
                <th scope="col">
                    <?php echo $cnSort; ?>
                </th>
                <th scope="col" class="priority-5">
                    <?php echo $typeSort; ?>
                </th>
                <th scope="col" class="priority-3">
                    <?php echo $pubSort; ?>
                </th>
                <th scope="col" class="priority-3">
                    <?php echo $appSort; ?>
                </th>
                <th scope="col">
                    <?php echo Lang::txt('COM_GROUPS_MEMBERS'); ?>
                </th>
                <th scope="col">
                    <?php echo Lang::txt('COM_GROUPS_PAGES'); ?>
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="9"><?php
                // Initiate paging
                echo $this->pagination(
                    $this->total,
                    $this->filters['start'],
                    $this->filters['limit']
                );
                ?></td>
            </tr>
        </tfoot>
        <tbody>
        <?php
        $database = App::get('db');
        $p = new \Components\Groups\Tables\Page($database);
        $k = 0;
        for ($i = 0, $n = count($this->rows); $i < $n; $i++) {
            $row =& $this->rows[$i];

            $group = new \Hubzero\User\Group();
            $group->read($row->gidNumber);

            switch ($row->type) {
                case '0':
                    $type = '<span class="group-type system">'
                        . Lang::txt('COM_GROUPS_TYPE_SYSTEM')
                        . '</span>';
                    break;
                case '1':
                    $type = '<span class="group-type hub">'
                        . Lang::txt('COM_GROUPS_TYPE_HUB')
                        . '</span>';
                    break;
                case '2':
                    $type = '<span class="group-type project">'
                        . Lang::txt('COM_GROUPS_TYPE_PROJECT')
                        . '</span>';
                    break;
                case '3':
                    $type = '<span class="group-type super">'
                        . Lang::txt('COM_GROUPS_TYPE_SUPER')
                        . '</span>';
                    break;
                case '4':
                    $type = '<span class="group-type course">'
                        . Lang::txt('COM_GROUPS_TYPE_COURSE')
                        . '</span>';
                    break;
            }

            $pages = $p->count(array('gidNumber' => $row->gidNumber));

            //get group invite emails
            $inviteemails = \Hubzero\User\Group\InviteEmail::all()
                ->whereEquals('gidNumber', $group->get('gidNumber'))
                ->total();

            //get group membership
            $members    = $group->get('members');
            $managers   = $group->get('managers');
            $applicants = $group->get('applicants');
            $invitees   = $group->get('invitees');

            //remove any managers from members list
            $true_members = array_diff($members, $managers);

            //build membership tooltip
            $membersTxt = Lang::txt('COM_GROUPS_MEMBERS');
            $managersTxt = Lang::txt('COM_GROUPS_MANAGERS');
            $applicantsTxt = Lang::txt('COM_GROUPS_APPLICANTS');
            $inviteesTxt = Lang::txt('COM_GROUPS_INVITEES');
            $tip  = '<table><tbody>';
            $tip .= '<tr><th>' . $membersTxt . '</th>'
                . '<td>' . count($true_members) . '</td></tr>';
            $tip .= '<tr><th>' . $managersTxt . '</th>'
                . '<td>' . count($managers) . '</td></tr>';
            $tip .= '<tr><th>' . $applicantsTxt . '</th>'
                . '<td>' . count($applicants) . '</td></tr>';
            $tip .= '<tr><th>' . $inviteesTxt . '</th>'
                . '<td>' . (count($invitees) + $inviteemails)
                . '</td></tr>';
            $tip .= '</tbody></table>';

            $editUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=edit&id=' . $row->cn
            );
            $noneTxt = Lang::txt('COM_GROUPS_NONE');
            $descDisplay = $row->description
                ? $this->escape(stripslashes($row->description))
                : '<span class="empty-field smallsub">'
                    . $noneTxt . '</span>';
            $token = Session::getFormToken();
            $publishUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=publish&id=' . $row->cn
                . '&' . $token . '=1'
            );
            $unpublishUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=unpublish&id=' . $row->cn
                . '&' . $token . '=1'
            );
            $approveUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=approve&id=' . $row->cn
                . '&' . $token . '=1'
            );
            $unapproveUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=' . $this->controller
                . '&task=unapprove&id=' . $row->cn
                . '&' . $token . '=1'
            );
            $memberUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=membership&gid=' . $row->cn
            );
            $pagesUrl = Route::url(
                'index.php?option=' . $this->option
                . '&controller=pages&gid=' . $row->cn
            );
            $memberTip = Lang::txt('COM_GROUPS_MANAGE_MEMBERSHIP')
                . '::' . $tip;
            $publishTxt = Lang::txt('COM_GROUPS_PUBLISH');
            $unpublishTxt = Lang::txt('COM_GROUPS_UNPUBLISH');
            $approveTxt = Lang::txt('COM_GROUPS_APPROVE');
            $unapproveTxt = Lang::txt('COM_GROUPS_UNAPPROVE');
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <td>
                    <input
                        type="checkbox"
                        name="id[]"
                        id="cb<?php echo $i; ?>"
                        value="<?php echo $this->escape($row->cn); ?>"
                        class="checkbox-toggle"
                    />
                    <label
                        for="cb<?php echo $i; ?>"
                        class="sr-only visually-hidden"
                    ><?php echo $this->escape($row->cn); ?></label>
                </td>
                <td class="priority-6">
                    <?php echo $this->escape($row->gidNumber); ?>
                </td>
                <td class="priority-4">
                    <?php if ($canDo->get('core.edit')) { ?>
                        <a href="<?php echo $editUrl; ?>">
                            <?php echo $descDisplay; ?>
                        </a>
                    <?php } else { ?>
                        <span>
                            <?php echo $descDisplay; ?>
                        </span>
                    <?php } ?>
                </td>
                <td>
                    <?php if ($canDo->get('core.edit')) { ?>
                        <a href="<?php echo $editUrl; ?>">
                            <?php echo $this->escape($row->cn); ?>
                        </a>
                    <?php } else { ?>
                        <?php echo $this->escape($row->cn); ?>
                    <?php } ?>
                </td>
                <td class="priority-5">
                    <?php echo $type; ?>
                </td>
                <td class="priority-3">
                    <?php if ($canDo->get('core.edit.state')) { ?>
                        <?php if ($row->published == 2) { ?>
                            <a
                                class="jgrid"
                                href="<?php echo $publishUrl; ?>"
                                title="<?php echo $publishTxt; ?>"
                            >
                                <span class="state archive">
                                    <span class="text">
                                        <?php echo Lang::txt('COM_GROUPS_ARCHIVED'); ?>
                                    </span>
                                </span>
                            </a>
                        <?php } elseif ($row->published == 1) { ?>
                            <a
                                class="jgrid"
                                href="<?php echo $unpublishUrl; ?>"
                                title="<?php echo $unpublishTxt; ?>"
                            >
                                <span class="state publish">
                                    <span class="text">
                                        <?php echo Lang::txt('COM_GROUPS_PUBLISHED'); ?>
                                    </span>
                                </span>
                            </a>
                        <?php } else { ?>
                            <a
                                class="jgrid"
                                href="<?php echo $publishUrl; ?>"
                                title="<?php echo $publishTxt; ?>"
                            >
                                <span class="state unpublish">
                                    <span class="text">
                                        <?php echo Lang::txt('COM_GROUPS_UNPUBLISHED'); ?>
                                    </span>
                                </span>
                            </a>
                        <?php } ?>
                    <?php } ?>
                </td>
                <td class="priority-3">
                    <?php if ($canDo->get('core.edit.state')) { ?>
                        <?php if (!$group->get('approved')) { ?>
                            <a
                                class="jgrid state no"
                                href="<?php echo $approveUrl; ?>"
                                title="<?php echo $approveTxt; ?>"
                            >
                                <span class="not-approved">
                                    <span class="text">
                                        <?php echo $approveTxt; ?>
                                    </span>
                                </span>
                            </a>
                        <?php } else { ?>
                            <a
                                class="jgrid state yes"
                                href="<?php echo $unapproveUrl; ?>"
                                title="<?php echo $unapproveTxt; ?>"
                            >
                                <span class="approved">
                                    <span class="text">
                                        <?php echo Lang::txt('COM_GROUPS_APPROVED'); ?>
                                    </span>
                                </span>
                            </a>
                        <?php } ?>
                    <?php } ?>
                </td>
                <td>
                    <?php if ($canDo->get('core.manage')) { ?>
                        <a
                            class="glyph member hasTip"
                            href="<?php echo $memberUrl; ?>"
                            title="<?php echo $memberTip; ?>"
                        >
                            <?php echo count($members); ?>
                        </a>
                    <?php } else { ?>
                        <span
                            class="glyph member"
                            title="<?php echo $memberTip; ?>"
                        >
                            <?php echo count($members); ?>
                        </span>
                    <?php } ?>
                </td>
                <td>
                    <?php if ($canDo->get('core.manage')) { ?>
                        <a href="<?php echo $pagesUrl; ?>">
                            <?php echo Lang::txt('COM_GROUPS_PAGES_COUNT', $pages); ?>
                        </a>
                    <?php } ?>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
        }
        ?>
        </tbody>
    </table>

    <input type="hidden" name="option" value="<?php echo $this->option ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

    <?php echo Html::input('token'); ?>
</form>
