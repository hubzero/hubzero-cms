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

$this->css();
?>
<h3 class="section-header"><?php echo Lang::txt('PLG_MEMBERS_PROJECTS'); ?></h3>

<?php if (User::authorise('core.create', 'com_projects')) { ?>
    <ul id="page_options" class="pluginOptions">
        <li>
            <a class="icon-add add btn showinbox"
                href="<?php echo Route::url('index.php?option=com_projects&task=start'); ?>">
                <?php echo Lang::txt('PLG_MEMBERS_PROJECTS_ADD'); ?>
            </a>
        </li>
    </ul>
<?php } ?>

<?php if (User::get('id') == $this->user->get('id')) {
    $memberId = $this->user->get('id');
    $allUrl = Route::url(
        'index.php?option=com_members&id=' . $memberId
        . '&active=projects&action=all'
    );
    $updatesUrl = Route::url(
        'index.php?option=com_members&id=' . $memberId
        . '&active=projects&action=updates'
    );
    $listLabel = Lang::txt('PLG_MEMBERS_PROJECTS_LIST')
        . ' (' . $this->total . ')';
    ?>
    <ul class="sub-menu">
        <li class="active">
            <a href="<?php echo $allUrl; ?>">
                <?php echo $listLabel; ?>
            </a>
        </li>
        <li>
            <a href="<?php echo $updatesUrl; ?>">
                <?php echo Lang::txt('PLG_MEMBERS_PROJECTS_UPDATES_FEED'); ?>
                <?php if ($this->newcount) {
                    echo '<span class="s-new">'
                        . $this->newcount . '</span>';
                } ?>
            </a>
        </li>
    </ul>
<?php } ?>

<div id="s-projects">
    <div class="container">
        <?php
        $filterBy = $this->filters['filterby'];
        $isActive = (!$filterBy || $filterBy == 'active')
            ? ' class="active"' : '';
        $isArchived = ($filterBy == 'archived')
            ? ' class="active"' : '';
        $filterAllUrl = Route::url(
            'index.php?option=com_members&id=' . $memberId
            . '&active=projects&action=all'
        );
        $filterArchivedUrl = Route::url(
            'index.php?option=com_members&id=' . $memberId
            . '&active=projects&action=all&filterby=archived'
        );
        $filterLabel = Lang::txt(
            'JGLOBAL_FILTER_AND_SORT_RESULTS'
        );
        ?>
        <nav class="entries-filters"
            aria-label="<?php echo $filterLabel; ?>">
            <ul class="entries-menu filter-options">
                <li>
                    <a<?php echo $isActive; ?>
                        data-status="all"
                        href="<?php echo $filterAllUrl; ?>">
                        <?php
                        echo Lang::txt(
                            'PLG_MEMBERS_PROJECTS_FILTER_STATUS_ACTIVE'
                        );
                        ?>
                    </a>
                </li>
                <li>
                    <a<?php echo $isArchived; ?>
                        data-status="manager"
                        href="<?php echo $filterArchivedUrl; ?>">
                        <?php
                        echo Lang::txt(
                            'PLG_MEMBERS_PROJECTS_FILTER_STATUS_ARCHIVED'
                        );
                        ?>
                    </a>
                </li>
            </ul>
        </nav>

        <?php

        if (count($this->invites)) {
            ?>
            <?php
            $inviteCaption = Lang::txt('PLG_MEMBERS_PROJECTS_INVITED')
                . ' <span>(' . count($this->invites) . ')</span>';
            ?>
            <table class="entries">
                <caption><?php echo $inviteCaption; ?></caption>
                <tbody>
                    <?php

                    foreach ($this->invites as $invite) {
                        $row = new \Components\Projects\Models\Project(
                            $invite->projectid
                        );
                        $rowUrl = Route::url($row->link());
                        $thumbUrl = Route::url($row->link('thumb'));
                        $rowTitle = $this->escape($row->get('title'));
                        $rowAlias = $row->get('alias');
                        $titleAttr = $rowTitle
                            . ' (' . $rowAlias . ')';
                        $acceptUrl = Route::url(
                            'index.php?option=com_projects'
                            . '&alias=' . $invite->alias
                            . '&confirm=' . $invite->invited_code
                            . '&email=' . $invite->invited_email
                        );
                        $acceptLabel = Lang::txt(
                            'PLG_MEMBERS_PROJECTS_ACCEPT'
                        );
                        ?>
                        <tr class="mline">
                            <td class="th_image">
                                <a href="<?php echo $rowUrl; ?>"
                                    title="<?php echo $titleAttr; ?>">
                                    <img src="<?php echo $thumbUrl; ?>"
                                        alt="<?php echo htmlentities($rowTitle); ?>"
                                        class="project-image"/>
                                </a>
                            </td>
                            <td class="th_privacy">
                                <?php if (!$row->isPublic()) {
                                    echo '<span class="privacy-icon">'
                                        . '&nbsp;</span>';
                                } ?>
                            </td>
                            <td class="th_title">
                                <a href="<?php echo $rowUrl; ?>"
                                    title="<?php echo $titleAttr; ?>"
                                    ><?php echo $rowTitle; ?></a>
                            </td>
                            <td>
                                <a class="btn btn-success"
                                    href="<?php echo $acceptUrl; ?>"
                                    ><?php echo $acceptLabel; ?></a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }

        if ($this->which == 'all') {
            // Show owned projects first
            $this->view('list')
                ->set('option', $this->option)
                ->set('rows', $this->owned)
                ->set('which', 'owned')
                ->set('config', $this->config)
                ->set('user', $this->user)
                ->set('filters', $this->filters)
                ->display();

            echo '</div><div class="container">';
        }

        // Show rows
        $this->view('list')
            ->set('option', $this->option)
            ->set('rows', $this->rows)
            ->set('config', $this->config)
            ->set('user', $this->user)
            ->set('which', $this->filters['which'])
            ->set('filters', $this->filters)
            ->display();
        ?>
    </div>
</div>
