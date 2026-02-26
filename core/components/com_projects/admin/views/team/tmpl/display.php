<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

$group = $this->project->groupOwner();

$members = array();
$managers = array();
if ($group) {
    $members = $group->get('members');
    $managers = $group->get('managers');
}

$groupSynced = ($group && $this->project->get('sync_group'));

Toolbar::title(Lang::txt('COM_PROJECTS') . ': ' . Lang::txt('COM_PROJECTS_TEAM'), 'projects');
$routeUrl = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&tmpl=component&task=new&project='
    . $this->filters['project']
);
Toolbar::appendButton('Popup', 'new', 'COM_PROJECTS_TEAM_NEW', $routeUrl, 570, 170);
Toolbar::spacer();
Toolbar::deleteList('COM_PROJECTS_TEAM_DELETE', 'delete');
Toolbar::spacer();
Toolbar::help('team');

Html::behavior('tooltip');

$this->css();
?>

<?php
$routeUrl2 = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
?>
<form action="<?php echo $routeUrl2; ?>" method="post" name="adminForm" id="adminForm">
    <?php if ($groupSynced) { ?>
        <fieldset id="filter-bar">
            <p class="warning">
                <?php
                $groupUrl = Route::url(
                    'index.php?option=com_groups&controller=membership&gid='
                    . $group->get('cn')
                );
                $groupLink = '<a href="' . $groupUrl . '">'
                    . $group->get('description')
                    . ' (' . $group->get('cn') . ')</a>';
                $syncMsg = 'Membership is synced with group "%s".'
                    . '<br />Addition or removal of members in that'
                    . ' group must handled through the group\'s'
                    . ' membership interface.';
                echo Lang::txt($syncMsg, $groupLink);
                ?>
            </p>
        </fieldset>
    <?php } ?>

    <table class="adminlist">
        <thead>
            <tr>
                <?php
                $projUrl = Route::url('index.php?option=' . $this->option);
                $projTxt = Lang::txt('COM_PROJECTS');
                $projAlias = $this->escape(stripslashes($this->project->get('alias')));
                $projTitle = $this->escape(stripslashes($this->project->get('title')));
                ?>
                <th colspan="8">
                    <a href="<?php echo $projUrl; ?>"><?php echo $projTxt; ?></a>
                    &gt; (<?php echo $projAlias; ?>)
                    <?php echo $projTitle; ?>
                </th>
            </tr>
            <tr>
                <?php //if (!$groupSynced) { ?>
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
                <?php //} ?>
                <?php
                $sortDir = @$this->filters['sort_Dir'];
                $sortCol = @$this->filters['sort'];
                ?>
                <th scope="col" class="priority-4"><?php
                    echo Html::grid('sort', 'COM_PROJECTS_TEAM_USERID', 'uidNumber', $sortDir, $sortCol);
                ?></th>
                <th scope="col"><?php
                    echo Html::grid('sort', 'COM_PROJECTS_TEAM_NAME', 'name', $sortDir, $sortCol);
                ?></th>
                <th scope="col" class="priority-3"><?php
                    echo Html::grid('sort', 'COM_PROJECTS_TEAM_USERNAME', 'username', $sortDir, $sortCol);
                ?></th>
                <th scope="col" class="priority-5"><?php echo Lang::txt('COM_PROJECTS_TEAM_ROLE'); ?></th>
                <th scope="col" class="priority-4"><?php echo Lang::txt('COM_PROJECTS_TEAM_JOINED'); ?></th>
                <th scope="col" class="priority-4"><?php echo Lang::txt('COM_PROJECTS_TEAM_LAST_VISIT'); ?></th>
                <th
                    scope="col"
                    class="priority-4"
                ><?php echo Lang::txt('COM_PROJECTS_TEAM_ADDED_AS_PART_OF_GROUP'); ?></th>
                <?php /*if (!$groupSynced) { ?>
                    <th scope="col"><?php echo Lang::txt('COM_PROJECTS_TEAM_ACTION'); ?></th>
                <?php }*/ ?>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="8"><?php
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
        $k = 0;
        $i = 0;
        foreach ($this->rows as $row) {
            $username = $row->username ? $row->username : $row->invited_email;
            $creator  = $this->project->owner('id') == $row->userid ? 1 : 0;

            switch ($row->role) {
                case \Components\Projects\Models\Orm\Owner::ROLE_MANAGER:
                    $role = Lang::txt('COM_PROJECTS_TEAM_LABEL_OWNER');
                    break;
                case \Components\Projects\Models\Orm\Owner::ROLE_REVIEWER:
                    $role = Lang::txt('COM_PROJECTS_TEAM_LABEL_REVIEWER');
                    break;
                case \Components\Projects\Models\Orm\Owner::ROLE_COLLABORATOR:
                default:
                    $role = Lang::txt('COM_PROJECTS_TEAM_LABEL_COLLABORATOR');
                    break;
            }

            $disabled = false;
            if (
                $row->native
                && (($groupSynced
                && in_array($row->userid, $members))
                || ($this->managers_count == 1
                && $row->role == 1))
            ) {
                $disabled = true;
            }
            ?>
            <tr class="<?php echo "row$k"; ?>">
                <?php //if (!$groupSynced) { ?>
                    <td>
                        <?php if ($disabled) { ?>
                            <!-- <input type="hidden" name="id[]" id="cb
                                <?php echo $i; ?>" value="<?php echo $row->id; ?>" /> -->
                        <?php } else { ?>
                            <input
                                type="checkbox"
                                name="id[]"
                                id="cb<?php echo $i; ?>"
                                value="<?php echo $row->id; ?>"
                                class="checkbox-toggle"
                            />
                            <label
                                for="cb<?php echo $i; ?>"
                                class="sr-only visually-hidden"
                            ><?php echo $row->id; ?></label>
                        <?php } ?>
                    </td>
                <?php //} ?>
                <td class="priority-4">
                    <?php echo $this->escape($row->userid); ?>
                </td>
                <td>
                    <?php if (isset($row->username)) : ?>
                        <?php
                        $routeUrl5 = Route::url(
                            'index.php?option=com_members'
                            . '&controller=members&task=edit&id='
                            . $row->userid
                        );
                        ?>
                        <a href="<?php echo $routeUrl5; ?>">
                            <?php echo $this->escape(stripslashes($row->fullname)); ?>
                        </a>
                    <?php else : ?>
                        <span>
                            <?php echo $this->escape(stripslashes($row->fullname)); ?>
                        </span>
                    <?php endif; ?>
                </td>
                <td class="priority-3">
                    <span>
                        <?php echo $this->escape(stripslashes($username)); ?>
                    </span>
                </td>
                <td class="priority-5">
                    <span>
                        <?php
                        // echo ($creator && !$this->project->groupOwner()
                        //     ? '<span class="prominent">'
                        //     . Lang::txt('COM_PROJECTS_TEAM_OWNER')
                        //     . '</span> / ' : '')
                        //     . $this->escape($role);
                        ?>
                    <?php if ($creator) { ?>
                        <?php
                        $langTxt7 = Lang::txt('COM_PROJECTS_TEAM_OWNER');
                        ?>
                        <span class="prominent"><?php echo $langTxt7 . ' / ' . $this->escape($role); ?></span>
                    <?php } else { ?>
                        <select name="role[<?php echo $row->userid; ?>]" onchange="Hubzero.submitbutton('update');">
                            <?php
                            $managerRole = \Components\Projects\Models\Orm\Owner::ROLE_MANAGER;
                            $inviteeRole = \Components\Projects\Models\Orm\Owner::ROLE_INVITEE;
                            $reviewerRole = \Components\Projects\Models\Orm\Owner::ROLE_REVIEWER;
                            $managerSel = ($row->role == $managerRole) ? ' selected="selected"' : '';
                            $collabSel = ($row->role == $inviteeRole || !$row->role) ? ' selected="selected"' : '';
                            $reviewerSel = ($row->role == $reviewerRole) ? ' selected="selected"' : '';
                            $ownerLbl = Lang::txt('COM_PROJECTS_TEAM_LABEL_OWNER');
                            $collabLbl = Lang::txt('COM_PROJECTS_TEAM_LABEL_COLLABORATOR');
                            $reviewerLbl = Lang::txt('COM_PROJECTS_TEAM_LABEL_REVIEWER');
                            ?>
                            <option value="1"<?php echo $managerSel; ?>><?php echo $ownerLbl; ?></option>
                            <option value="0"<?php echo $collabSel; ?>><?php echo $collabLbl; ?></option>
                            <option value="5"<?php echo $reviewerSel; ?>><?php echo $reviewerLbl; ?></option>
                        </select>
                    <?php } ?>
                    </span>
                </td>
                <td class="priority-4">
                    <?php
                    $invitedTxt = '<span class="invited">'
                        . Lang::txt('COM_PROJECTS_TEAM_INVITED') . '</span>';
                    echo $row->status == 1
                        ? Date::of($row->added)->toLocal('M d, Y')
                        : $invitedTxt;
                    ?>
                </td>
                <td class="priority-4">
                    <?php
                    $timecheck = gmdate('Y-m-d H:i:s', time() - (15 * 60));
                    $lastvisit = $row->lastvisit && $row->lastvisit != '0000-00-00 00:00:00'
                                ? Date::of($row->lastvisit)->relative()
                                : Lang::txt('COM_PROJECTS_TEAM_NEVER');
                    echo $lastvisit;
                    ?>
                </td>
                <td>
                    <?php echo $row->groupdesc ? \Hubzero\Utility\Str::truncate($row->groupdesc, 30) : ''; ?>
                    <span class="block mini short prominent">
                        <?php echo ($row->groupname) ? $row->groupname : Lang::txt('COM_PROJECTS_NONE'); ?></span>
                </td>
                <?php
                /*
                if (!$groupSynced) { ?>
                    <td>
                        <?php
                        $demoteUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&task=demote&project='
                            . $this->filters['project']
                            . '&id=' . $row->userid
                            . '&' . Session::getFormToken() . '=1'
                        );
                        $deleteUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&controller=' . $this->controller
                            . '&task=delete&project='
                            . $this->filters['project']
                            . '&id=' . $row->userid
                            . '&' . Session::getFormToken() . '=1'
                        );
                        switch ($row->role) {
                            case '1':
                                ?>
                                <a class="state demote"
                                    href="<?php echo $demoteUrl; ?>">
                                    <span><?php
                                        echo Lang::txt(
                                            'COM_PROJECTS_TEAM_DEMOTE'
                                        );
                                    ?></span>
                                </a>
                                <?php
                                break;
                            case '5':
                            case '2':
                            default:
                                $confirmJs = "javascript:if "
                                    . "(confirm('Cancel membership?'))"
                                    . "{return true;}"
                                    . "else{return false;}";
                                ?>
                                <a class="state trash"
                                    onclick="<?php echo $confirmJs; ?>"
                                    href="<?php echo $deleteUrl; ?>">
                                    <span><?php
                                        echo Lang::txt(
                                            'COM_PROJECTS_TEAM_REMOVE'
                                        );
                                    ?></span>
                                </a>
                                <?php
                                break;
                        }
                        ?>
                    </td>
                <?php }
                */ ?>
            </tr>
            <?php
            $k = 1 - $k;
            $i++;
        }
        ?>
        </tbody>
    </table>

    <input type="hidden" name="project" value="<?php echo $this->filters['project']; ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
    <input type="hidden" name="task" value="" autocomplete="off" />
    <input type="hidden" name="boxchecked" value="0" />

    <input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sortdir']); ?>" />
    <?php echo Html::input('token'); ?>
</form>
