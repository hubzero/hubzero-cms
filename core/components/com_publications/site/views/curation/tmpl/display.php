<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

// Sorting and paging
$sortbyDir  = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$whatsleft  = $this->total - $this->filters['start'] - $this->filters['limit'];
$prev_start = $this->filters['start'] - $this->filters['limit'];
$prev_start = $prev_start < 0 ? 0 : $prev_start;
$next_start = $this->filters['start'] + $this->filters['limit'];

// URL
$route  = 'index.php?option=' . $this->option . '&controller=curation';

$pa = new \Components\Publications\Tables\Author($this->database);

$this
    ->css()
    ->js()
    ->css('jquery.fancybox.css', 'system')
    ->css('curation.css')
    ->js('curation.js');

?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section curation">
    <p><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_LIST_INSTRUCT'); ?></p>

    <div class="container">
        <nav class="entries-filters" aria-label="<?php echo Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS'); ?>">
            <ul class="entries-menu filter-options">
                <li>
                    <?php
                    $linkUrl = Route::url($route);
                    ?>
                    <a<?= ($this->filters['curator'] != 'owner') ? ' class="active"' : '' ?> href="<?= $linkUrl ?>">
                        <?php echo Lang::txt('All'); ?>
                    </a>
                </li>
                <li>
                    <?php
                    $linkUrl = Route::url($route . '&assigned=1');
                    ?>
                    <a<?= ($this->filters['curator'] == 'owner') ? ' class="active"' : '' ?> href="<?= $linkUrl ?>">
                        <?php echo Lang::txt('Assigned to me'); ?>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="container-block">
            <?php if (count($this->rows) > 0) { ?>
                <div class="publist">
                    <table class="listing">
                        <thead>
                            <tr>
                                <th class="thtype<?php echo $this->filters['sortby'] == 'id' ? ' activesort' : ''; ?>">
                                    <?php
                                    $linkUrl = Route::url($route . '&t_sortby=id&t_sortdir=' . $sortbyDir);
                                    $titleText = Lang::txt('COM_PUBLICATIONS_CURATION_SORT_BY') . ' '
                                        . Lang::txt('COM_PUBLICATIONS_CURATION_ID');
                                    ?>
                                    <a href="<?= $linkUrl ?>" class="re_sort" title="<?= $titleText ?>">
                                        <?php echo Lang::txt('COM_PUBLICATIONS_CURATION_ID'); ?>
                                    </a>
                                </th>
                                <th></th>
                                <th<?php echo $this->filters['sortby'] == 'title' ? ' class="activesort"' : ''; ?>>
                                    <?php
                                    $linkUrl = Route::url($route . '&t_sortby=title&t_sortdir=' . $sortbyDir);
                                    $titleText = Lang::txt('COM_PUBLICATIONS_CURATION_SORT_BY') . ' '
                                        . Lang::txt('COM_PUBLICATIONS_CURATION_TITLE');
                                    ?>
                                    <a href="<?= $linkUrl ?>" class="re_sort" title="<?= $titleText ?>">
                                        <?php echo Lang::txt('COM_PUBLICATIONS_CURATION_TITLE'); ?>
                                    </a>
                                </th>
                                <th></th>
                                <th class="thtype<?= $this->filters['sortby'] == 'type' ? ' activesort' : '' ?>">
                                    <?php
                                    $linkUrl = Route::url($route . '&t_sortby=type&t_sortdir=' . $sortbyDir);
                                    $titleText = Lang::txt('COM_PUBLICATIONS_CURATION_SORT_BY') . ' '
                                        . Lang::txt('COM_PUBLICATIONS_CURATION_CONTENT_TYPE');
                                    ?>
                                    <a href="<?= $linkUrl ?>" class="re_sort" title="<?= $titleText ?>">
                                        <?php echo Lang::txt('COM_PUBLICATIONS_CURATION_CONTENT_TYPE'); ?>
                                    </a>
                                </th>
                                <th<?php echo $this->filters['sortby'] == 'submitted' ? ' class="activesort"' : ''; ?>>
                                    <?php
                                    $linkUrl = Route::url($route . '&t_sortby=submitted&t_sortdir=' . $sortbyDir);
                                    $titleText = Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTED') . ' '
                                        . Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTED');
                                    ?>
                                    <a href="<?= $linkUrl ?>" class="re_sort" title="<?= $titleText ?>">
                                        <?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTED'); ?>
                                    </a>
                                </th>
                                <th<?php echo $this->filters['sortby'] == 'status' ? ' class="activesort"' : ''; ?>>
                                    <?php
                                    $linkUrl = Route::url($route . '&t_sortby=status&t_sortdir=' . $sortbyDir);
                                    $titleText = Lang::txt('COM_PUBLICATIONS_CURATION_SORT_BY') . ' '
                                        . Lang::txt('COM_PUBLICATIONS_CURATION_STATUS');
                                    ?>
                                    <a href="<?= $linkUrl ?>" class="re_sort" title="<?= $titleText ?>">
                                        <?php echo Lang::txt('COM_PUBLICATIONS_CURATION_STATUS'); ?>
                                    </a>
                                </th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($this->rows as $row) {
                                $submitted  = $row->reviewed && $row->state == 5
                                            ? strtolower(Lang::txt('COM_PUBLICATIONS_CURATION_RESUBMITTED'))
                                            : strtolower(Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTED'));
                                $submitted .= ' <span class="prominent">' . Date::of($row->submitted)->toLocal('M d, Y')
                                    . '</span> ';

                                // Get submitter
                                $submitter  = $pa->getSubmitter($row->version_id, $row->created_by);
                                $submitter->name = $submitter->name ?: Lang::txt('JUNKNOWN');
                                $submitted .= ' <span class="block">'
                                    . Lang::txt('COM_PUBLICATIONS_CURATION_BY', $this->escape($submitter->name))
                                    . '</span>';

                                if ($row->state == 7) {
                                    $reviewed = '';

                                    if (!empty($row->reviewed_by)) {
                                        $reviewed = strtolower(Lang::txt('COM_PUBLICATIONS_CURATION_REVIEWED'))
                                            . ' <span class="prominent">' . Date::of($row->reviewed)->toLocal('M d, Y')
                                            . '</span> ';

                                        $reviewer = User::getInstance($row->reviewed_by);
                                        $name = $reviewer->get('name');
                                        $name = $name ?: Lang::txt('JUNKNOWN');
                                        $reviewed .= $reviewer
                                            ? ' <span class="block">'
                                                . Lang::txt('COM_PUBLICATIONS_CURATION_BY', $this->escape($name))
                                                . '</span>'
                                            : '';
                                    }
                                }

                                $class = $row->state == 5 ? 'status-pending' : 'status-wip';

                                $abstract  = $row->abstract ? stripslashes($row->abstract) : '';

                                // Is user authorize to edit assignment?
                                $assign = (
                                    $this->authorized == 'curator'
                                    || $this->authorized == 'admin'
                                    || (
                                        $this->authorized == 'limited'
                                        && in_array($row->master_type, $this->filters['master_type'])
                                    )
                                )
                                    ? true
                                    : false;
                                ?>
                                    <tr class="mline mini faded" id="tr_<?php echo $row->id; ?>">
                                        <td>
                                        <?php echo $row->id; ?>
                                        </td>
                                        <td class="pub-image">
                                            <?php
                                            $sourceUrl = Route::url(
                                                'index.php?option=com_publications&id=' . $row->id . '&v='
                                                    . $row->version_id,
                                            ) . '/Image:thumb';
                                            ?>
                                            <img width="30" height="30" src="<?= $sourceUrl ?>" alt="" />
                                        </td>
                                        <td>
                                        <?php if ($row->state == 5) { ?>
                                                <?php
                                                $linkUrl = Route::url($route . '&id=' . $row->id);
                                                ?>
                                                <a href="<?= $linkUrl ?>"
                                                    <?= $abstract ? 'title="' . $this->escape($abstract) . '"' : '' ?>>
                                        <?php } ?>
                                        <?php echo $this->escape($row->title); ?>
                                        <?php if ($row->state == 5) { ?>
                                                </a>
                                        <?php } ?>
                                        </td>
                                        <td>
                                            v.<?php echo $this->escape($row->version_label); ?>
                                        </td>
                                        <td>
                                            <span class="icon <?= $row->base ?>">&nbsp;</span><?= $row->base ?>
                                        </td>
                                        <td>
                                            <span class="block"><?php echo $submitted; ?></span>
                                        <?php if ($row->reviewed && $row->state == 5) { ?>
                                                <span class="item-updated"></span>
                                        <?php } ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusLabelText = $row->state == 5
                                                ? Lang::txt('COM_PUBLICATIONS_CURATION_STATUS_PENDING')
                                                : Lang::txt('COM_PUBLICATIONS_CURATION_PENDING_AUTHOR_CHANGES');
                                            ?>
                                            <span class="status-icon <?= $class ?>"></span> <span
                                                class="status-label"><?= $statusLabelText ?></span>
                                        </td>
                                        <td>
                                        <?php
                                        $owner = $row->curator ? User::getInstance($row->curator) : null;
                                        if ($owner) {
                                            ?>
                                            <?php echo Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGNED_TO'); ?>
                                                <?php if ($assign) { ?>
                                                    <?php
                                                    $assignUrl = Route::url(
                                                        $route . '&id=' . $row->id . '&task=assign&vid='
                                                            . $row->version_id . '&ajax=1&no_html=1',
                                                    );
                                                    $titleText = Lang::txt(
                                                        'COM_PUBLICATIONS_CURATION_CHANGE_ASSIGNMENT',
                                                    );
                                                    ?>
                                                    <a href="<?= $assignUrl ?>" class="fancybox"
                                                        title="<?= $titleText ?>">
                                                <?php } ?>
                                                <?php echo $this->escape($owner->get('name')); ?>
                                                <?php if ($assign) { ?>
                                                    </a>
                                                <?php } ?>
                                                <?php
                                        } elseif ($assign) {
                                            ?>
                                                <?php
                                                $assignUrl = Route::url(
                                                    $route . '&id=' . $row->id . '&task=assign&vid=' . $row->version_id
                                                        . '&ajax=1&no_html=1',
                                                );
                                                $titleText = Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN');
                                                $linkText = Lang::txt('COM_PUBLICATIONS_CURATION_ASSIGN');
                                                ?>
                                                <a href="<?= $assignUrl ?>" class="btn icon-assign btn-secondary fancybox" title="<?= $titleText ?>"><?= $linkText ?></a>
                                                <?php
                                        }
                                        ?>
                                        </td>
                                        <td class="nowrap">
                                        <?php if ($row->state == 5) : ?>
                                                <?php
                                                $linkUrl = Route::url(
                                                    $route . '&id=' . $row->id . '&vid=' . $row->version_id,
                                                );
                                                $titleText = Lang::txt('COM_PUBLICATIONS_CURATION_OVER_REVIEW');
                                                $linkText = Lang::txt('COM_PUBLICATIONS_CURATION_REVIEW');
                                                ?>
                                                <a href="<?= $linkUrl ?>" class="btn icon-next btn-secondary btn-primary" title="<?= $titleText ?>"><?= $linkText ?></a>
                                        <?php endif; ?>
                                            <?php echo $row->state == 7 ? $reviewed : ''; ?>
                                            <?php
                                            $historyUrl = Route::url(
                                                $route . '&id=' . $row->id . '&task=history&ajax=1&no_html=1',
                                            );
                                            $titleText = Lang::txt('COM_PUBLICATIONS_CURATION_OVER_HISTORY');
                                            $linkText = Lang::txt('COM_PUBLICATIONS_CURATION_HISTORY');
                                            ?>
                                            <a href="<?= $historyUrl ?>" class="btn btn-secondary icon-history fancybox" title="<?= $titleText ?>"><?= $linkText ?></a>
                                            <?php
                                            $linkUrl = Route::url(
                                                'index.php?option=com_publications&id=' . $row->id . '&v='
                                                    . $row->version_number,
                                            );
                                            $titleText = Lang::txt('COM_PUBLICATIONS_CURATION_VIEW_PUB_PAGE');
                                            ?>
                                            <a href="<?= $linkUrl ?>" class="public-page"
                                                title="<?= $titleText ?>">&nbsp;</a>
                                        </td>
                                    </tr>
                                    <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
                $pn = $this->pageNav->render();
                $pn = str_replace('/?/&amp;', '/?', $pn);
                $f = 'task=display';
                foreach ($this->filters as $k => $v) {
                    $f .= ($v && ($k == 'tag' || $k == 'category')) ? '&amp;' . $k . '=' . $v : '';
                }
                $pn = str_replace('?', '?' . $f . '&amp;', $pn);
                echo $pn;
                ?>
            <?php } else { ?>
                <p class="noresults"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_NO_RESULTS'); ?></p>
            <?php } ?>
        </div>
        <div class="clearfix"></div>
    </div>
</section>
