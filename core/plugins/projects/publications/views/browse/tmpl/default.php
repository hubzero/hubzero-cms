<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

// Sorting and paging
$sortbyDir  = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$sortAppend = '&sortdir=' . urlencode($sortbyDir);

// Check used space against quota (percentage)
$inuse = round(($this->dirsize * 100 ) / $this->quota);
if ($inuse < 1) {
    $inuse = round((($this->dirsize * 100 ) / $this->quota), 1);
    if ($inuse < 0.1) {
        $inuse = 0.0;
    }
}
$inuse = ($inuse > 100) ? 100 : $inuse;
$approachingQuota = $this->project->config('approachingQuota', 85);
$approachingQuota = intval($approachingQuota) > 0 ? $approachingQuota : 85;
$warning = ($inuse > $approachingQuota) ? 1 : 0;

$showStats = false;

$i = 1;

?>
<div id="plg-header">
    <h3 class="publications"><?php echo $this->title; ?></h3>
</div>

<?php if ($this->project->access('content')) { ?>
    <ul id="page_options" class="pluginOptions">
            <?php if ($this->new_pubs) { ?>
                <li>
                    <a class="icon-add btn"
                        href="/pubs/#/prjs/<?php echo $this->project->get('id'); ?>"
                        ><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION'); ?></a>
                </li>
            <?php } else { ?>
                <li>
                    <a class="icon-add btn"
                        href="<?php echo Route::url($this->project->link('publications') . '&action=start'); ?>"
                        ><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION'); ?></a>
                    </li>
            <?php } ?>
    </ul>
<?php } ?>

<?php
if (count($this->rows) > 0) {
    ?>
    <form action="<?php echo Route::url($this->project->link('publications')); ?>" method="post" id="plg-form" >
        <div class="container">
            <?php
            $showingLabel = ucfirst(Lang::txt('COM_PROJECTS_SHOWING'));
            $rowCount = count($this->rows);
            $allLabel = ($this->total <= $rowCount)
                ? Lang::txt('PLG_PROJECTS_PUBLICATIONS_ALL') . ' '
                : '';
            $outOfLabel = ($this->total > $rowCount)
                ? Lang::txt('COM_PROJECTS_OUT_OF') . ' ' . $this->total
                : '';
            $pubsLabel = Lang::txt(
                'PLG_PROJECTS_PUBLICATIONS_PUBLICATIONS_S'
            );
            ?>
            <div class="list-menu">
                <p class="msg-total">
                    <?php echo $showingLabel; ?>
                    <?php echo $allLabel; ?>
                    <span class="prominent">
                        <?php echo $rowCount; ?>
                    </span>
                    <?php echo $outOfLabel; ?>
                    <?php echo $pubsLabel; ?>
                </p>
            </div>
            <?php
            $pubLink = $this->project->link('publications');
            $sortByLabel = Lang::txt('COM_PROJECTS_SORT_BY');

            $titleActive = ($this->filters['sortby'] == 'title')
                ? ' activesort' : '';
            $titleUrl = Route::url(
                $pubLink . $sortAppend . '&sortby=title'
            );
            $titleSort = $sortByLabel . ' '
                . Lang::txt('PLG_PROJECTS_PUBLICATIONS_TITLE');
            $titleLabel = Lang::txt(
                'PLG_PROJECTS_PUBLICATIONS_TITLE'
            );

            $idActive = ($this->filters['sortby'] == 'id')
                ? ' activesort' : '';
            $idUrl = Route::url(
                $pubLink . $sortAppend . '&sortby=id'
            );
            $idSort = $sortByLabel . ' '
                . Lang::txt('PLG_PROJECTS_PUBLICATIONS_ID');
            $idLabel = Lang::txt('PLG_PROJECTS_PUBLICATIONS_ID');

            $typeActive = ($this->filters['sortby'] == 'type')
                ? ' activesort' : '';
            $typeUrl = Route::url(
                $pubLink . $sortAppend . '&sortby=type'
            );
            $typeSort = $sortByLabel . ' '
                . Lang::txt('PLG_PROJECTS_PUBLICATIONS_TYPE');
            $typeLabel = Lang::txt(
                'PLG_PROJECTS_PUBLICATIONS_CONTENT_TYPE'
            );

            $statusActive = ($this->filters['sortby'] == 'status')
                ? ' class="activesort"' : '';
            $statusUrl = Route::url(
                $pubLink . $sortAppend . '&sortby=status'
            );
            $statusSort = $sortByLabel . ' '
                . Lang::txt('PLG_PROJECTS_PUBLICATIONS_STATUS');
            $statusLabel = Lang::txt(
                'PLG_PROJECTS_PUBLICATIONS_STATUS'
            );

            $releasesLabel = Lang::txt(
                'PLG_PROJECTS_PUBLICATIONS_RELEASES'
            );
            ?>
            <table id="filelist" class="listing">
                <thead>
                    <tr>
                        <th></th>
                        <th class="thtype<?php echo $titleActive; ?>">
                            <a href="<?php echo $titleUrl; ?>"
                                class="re_sort"
                                title="<?php echo $titleSort; ?>"
                                ><?php echo $titleLabel; ?></a>
                        </th>
                        <th class="thtype<?php echo $idActive; ?>">
                            <a href="<?php echo $idUrl; ?>"
                                class="re_sort"
                                title="<?php echo $idSort; ?>"
                                ><?php echo $idLabel; ?></a>
                        </th>
                        <th class="thtype<?php echo $typeActive; ?>">
                            <a href="<?php echo $typeUrl; ?>"
                                class="re_sort"
                                title="<?php echo $typeSort; ?>"
                                ><?php echo $typeLabel; ?></a>
                        </th>
                        <th<?php echo $statusActive; ?> colspan="2">
                            <a href="<?php echo $statusUrl; ?>"
                                class="re_sort"
                                title="<?php echo $statusSort; ?>"
                                ><?php echo $statusLabel; ?></a>
                        </th>
                        <th class="condensed centeralign">
                            <?php echo $releasesLabel; ?>
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($this->rows as $row) {
                        if ($row->isPublished()) {
                            $showStats = true;
                        }
                        $this->view('_row')
                             ->set('project', $this->project)
                             ->set('pub', $this->pub)
                             ->set('row', $row)
                             ->set('i', $i)
                            ->set('new_pubs', $this->new_pubs)
                             ->display();
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
            <?php
            // Pagination
            $pageNav = new \Hubzero\Pagination\Paginator(
                $this->total,
                $this->filters['start'],
                $this->filters['limit']
            );
            $pageNav->setAdditionalUrlParam('sortby', $this->filters['sortby']);
            $pageNav->setAdditionalUrlParam('sortdir', $this->filters['sortdir']);

            $pagenavhtml = $pageNav->render();
            ?>
            <fieldset>
                <input type="hidden" name="sortby" value="<?php echo $this->escape($this->filters['sortby']); ?>" />
                <input type="hidden" name="sortdir" value="<?php echo $this->escape($this->filters['sortdir']); ?>" />
                <?php echo $pagenavhtml; ?>
            </fieldset>
        </form>
    </div>
    <?php
} else {
    echo '<p class="noresults">'
        . Lang::txt('PLG_PROJECTS_PUBLICATIONS_NO_PUBS_FOUND')
        . ' <span class="addnew"><a href="'
        . Route::url($this->project->link('publications') . '&action=start')
        . '"  >'
        . Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION')
        . '</a></span></p>';

    // Show intro banner with publication steps
    $this->view('intro')
         ->set('option', $this->option)
         ->set('project', $this->project)
         ->set('pub', $this->pub)
         ->display();
}
?>

<?php
if (count($this->rows) > 0) {
    $diskUrl = Route::url(
        $this->project->link('publications') . '&action=diskspace'
    );
    $diskTooltip = Lang::txt(
        'PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_TOOLTIP'
    );
    $warningClass = $warning ? 'class="quota-warning"' : '';
    $usedLabel = $inuse . '% '
        . Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_USED');
    $quotaLabel = Lang::txt(
        'PLG_PROJECTS_PUBLICATIONS_DISK_USAGE_QUOTA'
    ) . ': ' . \Hubzero\Utility\Number::formatBytes($this->quota);
    $statsUrl = Route::url(
        $this->project->link('publications') . '&action=stats'
    );
    $statsLabel = Lang::txt(
        'PLG_PROJECTS_PUBLICATIONS_VIEW_USAGE_STATS'
    );
    ?>
    <p class="extras">
        <span class="leftfloat">
        <?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_DISK_USAGE'); ?>
        <a href="<?php echo $diskUrl; ?>"
            title="<?php echo $diskTooltip; ?>"
            ><span id="indicator-wrapper"
                <?php echo $warningClass; ?>
                ><span id="indicator-area"
                    class="used:<?php echo $inuse; ?>"
                    >&nbsp;</span>
                <span id="indicator-value">
                    <span>
                        <?php echo $usedLabel; ?>
                    </span>
                </span>
            </span>
        </a>
        <span class="show-quota">
            <?php echo $quotaLabel; ?>
        </span>
        </span>
    </p>
    <?php if ($showStats) { ?>
        <p class="viewallstats mini">
            <a href="<?php echo $statsUrl; ?>">
                <?php echo $statsLabel; ?>
                &raquo;
            </a>
        </p>
    <?php } ?>
<?php }
