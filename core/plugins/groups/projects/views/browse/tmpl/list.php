<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

switch ($this->which) {
    case 'group':
        $title = Lang::txt('PLG_GROUPS_PROJECTS_SHOW_GROUP');
        break;
    case 'owned':
        $title = Lang::txt('PLG_GROUPS_PROJECTS_SHOW_OWNED');
        break;
    case 'other':
        $title = Lang::txt('PLG_GROUPS_PROJECTS_SHOW_OTHER');
        break;
    default:
    case 'all':
        $title = Lang::txt('PLG_GROUPS_PROJECTS_SHOW_ALL');
        break;
}
?>

    <table class="entries">
        <caption><?php echo $title . ' (' . count($this->rows) . ')'; ?></caption>
<?php if (count($this->rows) > 0) { ?>
        <thead>
            <tr>
                <th class="th_image" colspan="2"></th>
                <th><?php echo Lang::txt('PLG_GROUPS_PROJECTS_TITLE'); ?></th>
                <th><?php echo Lang::txt('PLG_GROUPS_PROJECTS_STATUS'); ?></th>
                <th><?php echo Lang::txt('PLG_GROUPS_PROJECTS_MY_ROLE'); ?></th>
                <th><?php echo Lang::txt('PLG_GROUPS_PROJECTS_MEMBERSHIP'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            foreach ($this->rows as $row) {
                $role = $row->access('member')
                    ? ($row->access('manager') ? Lang::txt('PLG_GROUPS_PROJECTS_STATUS_MANAGER') :
                    Lang::txt('PLG_GROUPS_PROJECTS_STATUS_COLLABORATOR'))
                    : Lang::txt('PLG_GROUPS_PROJECTS_STATUS_NOTMEMBER');

                $role = $row->access('readonly') && !$row->isArchived()
                    ? Lang::txt('PLG_GROUPS_PROJECTS_STATUS_REVIEWER')
                    : $role;

                $setup = $row->inSetup() ? Lang::txt('PLG_GROUPS_PROJECTS_STATUS_SETUP') : '';

                $i++;
                ?>
                <tr class="mline">
                    <td class="th_image">
                        <?php if ($row->access('member') || $row->access('readonly')) { ?>
                            <?php
                            $rowUrl = Route::url($row->link());
                            $rowThumb = Route::url($row->link('thumb'));
                            $rowTitle = $this->escape($row->get('title'));
                            $rowAlias = $row->get('alias');
                            $titleWithAlias = $rowTitle . ' (' . $rowAlias . ')';
                            ?>
                            <a href="<?php echo $rowUrl; ?>"
                                title="<?php echo $titleWithAlias; ?>">
                                <img src="<?php echo $rowThumb; ?>"
                                    alt="<?php echo $rowTitle; ?>"
                                    class="project-image"/>
                            </a>
                        <?php } else { ?>
                            <img src="<?php echo $rowThumb; ?>"
                                alt="<?php echo $rowTitle; ?>"
                                class="project-image"/>
                        <?php } ?>
                        <?php if ($row->get('newactivity') && $row->isActive() && !$setup) { ?>
                            <span class="s-new"><?php echo $row->get('newactivity'); ?></span>
                        <?php } ?>
                    </td>
                    <td class="th_privacy">
                        <?php if (!$row->isPublic()) {
                            echo '<span class="privacy-icon">&nbsp;</span>';
                        } ?>
                    </td>
                    <td class="th_title">
                        <?php if ($row->access('member') || $row->access('readonly')) { ?>
                            <a href="<?php echo $rowUrl; ?>"
                                title="<?php echo $titleWithAlias; ?>">
                                <?php echo $rowTitle; ?>
                            </a>
                        <?php } else { ?>
                            <?php echo $rowTitle; ?>
                        <?php } ?>
                <?php
                $ownerDisplay = $row->groupOwner()
                ? $row->groupOwner('description')
                : $row->owner('name');
                ?>
                        <?php if ($this->which != 'owned') { ?>
                            <span class="block"><?php echo $ownerDisplay; ?></span>
                        <?php } ?>
                    </td>
                    <td class="th_status">
                        <?php
                        $html = '';
                        if ($row->access('owner')) {
                            if ($row->isActive()) {
                                $html .= '<span class="active"><a href="'
                                    . Route::url($row->link())
                                    . '" title="'
                                    . Lang::txt('PLG_GROUPS_PROJECTS_GO_TO_PROJECT')
                                    . '">&raquo; '
                                    . Lang::txt('PLG_GROUPS_PROJECTS_STATUS_ACTIVE')
                                    . '</a></span>';
                            } elseif ($row->inSetup()) {
                                $html .= '<span class="setup"><a href="'
                                    . Route::url($row->link('setup'))
                                    . '" title="'
                                    . Lang::txt('PLG_GROUPS_PROJECTS_CONTINUE_SETUP')
                                    . '">&raquo; '
                                    . Lang::txt('PLG_GROUPS_PROJECTS_STATUS_SETUP')
                                    . '</a></span> ';
                            }
                        }
                        if ($row->isInactive()) {
                            $html .= '<span class="suspended">'
                                . Lang::txt('PLG_GROUPS_PROJECTS_STATUS_SUSPENDED')
                                . '</span> ';
                        } elseif ($row->isPending()) {
                            $html .= '<span class="pending">'
                                . Lang::txt('PLG_GROUPS_PROJECTS_STATUS_PENDING')
                                . '</span> ';
                        } elseif ($row->isArchived()) {
                            $html .= '<span class="archived">'
                                . Lang::txt('PLG_GROUPS_PROJECTS_STATUS_ARCHIVED')
                                . '</span> ';
                        }

                        echo $html;
                        ?>
                    </td>
                    <td class="th_role">
                        <?php echo $role; ?>
                    </td>
                <?php
                $membership = $row->get('sync_group')
                ? '<span class="synced">'
                . Lang::txt('PLG_GROUPS_PROJECTS_GROUP_SYNCED')
                . '</span>'
                : '<span class="selected">'
                . Lang::txt('PLG_GROUPS_PROJECTS_GROUP_SELECTED')
                . '</span>';
                ?>
                    <td class="th_membership">
                        <?php echo $membership; ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
<?php } else { ?>
        <tbody>
            <tr>
                <td>
                    <p class="noresults"><?php echo Lang::txt('PLG_GROUPS_PROJECTS_NO_PROJECTS'); ?></p>
                </td>
            </tr>
        </tbody>
<?php } ?>
    </table>
