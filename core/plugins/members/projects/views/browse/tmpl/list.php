<?php

/**
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

$sortbyDir = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';

switch ($this->which) {
    case 'group':
        $title = Lang::txt('PLG_MEMBERS_PROJECTS_SHOW_GROUP');
        break;
    case 'owned':
        $title = Lang::txt('PLG_MEMBERS_PROJECTS_SHOW_OWNED');
        break;
    case 'other':
        $title = Lang::txt('PLG_MEMBERS_PROJECTS_SHOW_OTHER');
        break;
    default:
    case 'all':
        $title = Lang::txt('PLG_MEMBERS_PROJECTS_SHOW_ALL');
        break;
}

$isUser = false;
if (User::get('id') == $this->user->get('id')) {
    $isUser = true;
}
?>

    <table class="entries">
        <caption><?php echo $title . ' <span>(' . count($this->rows) . ')</span>'; ?></caption>
<?php if (count($this->rows) > 0) { ?>
        <thead>
            <tr>
                <th class="th_image" colspan="2"></th>
    <?php
    $sortBase = 'index.php?option=com_members&id='
    . $this->user->get('id')
    . '&active=projects&action=all&sortdir='
    . $sortbyDir;
    $titleSortUrl = Route::url($sortBase . '&sortby=title');
    $statusSortUrl = Route::url($sortBase . '&sortby=status');
    $roleSortUrl = Route::url($sortBase . '&sortby=role');
    $titleLabel = Lang::txt('PLG_MEMBERS_PROJECTS_TITLE');
    $statusLabel = Lang::txt('PLG_MEMBERS_PROJECTS_STATUS');
    $roleLabel = Lang::txt('PLG_MEMBERS_PROJECTS_MY_ROLE');
    ?>
                <th<?php if ($this->filters['sortby'] == 'title') {
                    echo ' class="activesort"';
                   } ?>>
                    <a href="<?php echo $titleSortUrl; ?>"
                        class="re_sort"><?php echo $titleLabel; ?></a>
                </th>
                <?php if ($this->which == 'owned') { ?>
                    <th<?php if ($this->filters['sortby'] == 'status') {
                        echo ' class="activesort"';
                       } ?>>
                        <a href="<?php echo $statusSortUrl; ?>"
                            class="re_sort"><?php echo $statusLabel; ?></a>
                    </th>
                <?php } ?>
                <?php if ($isUser) { ?>
                    <th<?php if ($this->filters['sortby'] == 'role') {
                        echo ' class="activesort"';
                       } ?>>
                        <a href="<?php echo $roleSortUrl; ?>"
                            class="re_sort"><?php echo $roleLabel; ?></a>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php

            $i = 0;
            foreach ($this->rows as $row) {
                $role = $row->access('manager')
                    ? Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_MANAGER')
                    : Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_COLLABORATOR');

                $role = $row->access('readonly') && !$row->isArchived()
                    ? Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_REVIEWER')
                    : $role;

                $setup = $row->inSetup() ? Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_SETUP') : '';

                $i++;
                ?>
                <tr class="mline">
                    <td class="th_image">
                        <a href="<?php echo Route::url($row->link()); ?>"
                            title="<?php echo $this->escape($row->get('title')) . ' (' . $row->get('alias') . ')'; ?>">
                            <img src="<?php echo Route::url($row->link('thumb')); ?>"
                                alt="<?php echo htmlentities($this->escape($row->get('title'))); ?>"
                                class="project-image"/>
                        </a>
                        <?php if ($isUser) { ?>
                            <?php if ($row->get('newactivity') && $row->isActive() && !$setup) {
                                ?><span class="s-new"><?php echo $row->get('newactivity'); ?></span><?php
                            } ?>
                        <?php } ?>
                    </td>
                    <td class="th_privacy">
                        <?php if (!$row->isPublic()) {
                            echo '<span class="privacy-icon">&nbsp;</span>';
                        } ?>
                    </td>
                    <td class="th_title">
                        <a href="<?php echo Route::url($row->link()); ?>"
                            title="<?php echo $this->escape($row->get('title')) . ' (' . $row->get('alias') . ')'; ?>"
                            ><?php echo $this->escape($row->get('title')); ?></a>
                <?php
                $ownerDisplay = $row->groupOwner()
                ? $row->groupOwner('description')
                : $row->owner('name');
                ?>
                        <?php if ($this->which != 'owned') { ?>
                            <span class="block"><?php echo $ownerDisplay; ?></span>
                        <?php } ?>
                    </td>
                    <?php if ($this->which == 'owned') { ?>
                        <td class="th_status">
                            <?php

                            $html = '';
                            if ($row->access('owner')) {
                                if ($row->isActive()) {
                                    $html .= '<span class="active"><a href="'
                                        . Route::url($row->link())
                                        . '" title="'
                                        . Lang::txt('PLG_MEMBERS_PROJECTS_GO_TO_PROJECT')
                                        . '">&raquo; '
                                        . Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_ACTIVE')
                                        . '</a></span>';
                                } elseif ($row->inSetup()) {
                                    $html .= '<span class="setup"><a href="'
                                        . Route::url($row->link('setup'))
                                        . '" title="'
                                        . Lang::txt('PLG_MEMBERS_PROJECTS_CONTINUE_SETUP')
                                        . '">&raquo; '
                                        . Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_SETUP')
                                        . '</a></span> ';
                                }
                            }
                            if ($row->isInactive()) {
                                $html .= '<span class="suspended">'
                                    . Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_SUSPENDED')
                                    . '</span> ';
                            } elseif ($row->isPending()) {
                                $html .= '<span class="pending">'
                                    . Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_PENDING')
                                    . '</span> ';
                            } elseif ($row->isArchived()) {
                                $html .= '<span class="archived">'
                                    . Lang::txt('PLG_MEMBERS_PROJECTS_STATUS_ARCHIVED')
                                    . '</span> ';
                            }

                            echo $html;
                            ?>
                        </td>
                    <?php } ?>
                    <?php if ($isUser) { ?>
                        <td class="th_role">
                            <?php echo $role; ?>
                        </td>
                    <?php } ?>
                </tr>
                <?php
            }
            ?>
        </tbody>
<?php } else { ?>
        <tbody>
            <tr>
                <td>
                    <p class="noresults"><?php echo Lang::txt('PLG_MEMBERS_PROJECTS_NO_PROJECTS'); ?></p>
                </td>
            </tr>
        </tbody>
<?php } ?>
    </table>
