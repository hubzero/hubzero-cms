<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Component;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('resources.css', 'com_resources')
     ->js('resources.js', 'com_resources');

$config = Component::params('com_resources');

// An array for storing all the links we make
$links = array();
$html = '';

if ($this->cats) {
    // Loop through each category
    foreach ($this->cats as $cat) {
        // Only show categories that have returned search results
        if ($cat['total'] > 0) {
            // Is this the active category?
            $a = ($cat['category'] == $this->active) ? ' class="active"' : '';

            // If we have a specific category, prepend it to the search term
            $blob = ($cat['category']) ? $cat['category'] : '';

            // Build the HTML
            $catUrl = Route::url(
                'index.php?option=' . $this->option
                . '&cn=' . $this->group->get('cn')
                . '&active=resources&area='
                . urlencode(stripslashes($blob))
            );
            $catTitle = $this->escape(stripslashes($cat['title']));
            $l = "\t" . '<li' . $a . '>'
                . '<a href="' . $catUrl
                . '&limit=' . $this->limit . '">'
                . $catTitle
                . ' <span class="item-count">'
                . $cat['total'] . '</span></a>';

            // Are there sub-categories?
            if (isset($cat['_sub']) && is_array($cat['_sub'])) {
                // An array for storing the HTML we make
                $k = array();
                // Loop through each sub-category
                foreach ($cat['_sub'] as $subcat) {
                    // Only show sub-categories that returned search results
                    if ($subcat['total'] > 0) {
                        // Is this the active category?
                        $a = ($subcat['category'] == $this->active) ? ' class="active"' : '';

                        // If we have a specific category, prepend it to the search term
                        $blob = ($subcat['category']) ? $subcat['category'] : '';

                        // Build the HTML
                        $subUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&cn=' . $this->group->get('cn')
                            . '&active=resources&area='
                            . urlencode(stripslashes($blob))
                        );
                        $subTitle = $this->escape(
                            stripslashes($subcat['title'])
                        );
                        $k[] = "\t\t\t" . '<li' . $a . '>'
                            . '<a href="' . $subUrl
                            . '&limit=' . $this->limit . '">'
                            . $subTitle
                            . ' <span class="item-count">'
                            . $subcat['total']
                            . '</span></a></li>';
                    }
                }
                // Do we actually have any links?
                // NOTE: this method prevents returning empty list tags "<ul></ul>"
                if (count($k) > 0) {
                    $l .= "\t\t" . '<ul>' . "\n";
                    $l .= implode("\n", $k);
                    $l .= "\t\t" . '</ul>' . "\n";
                }
            }
            $l .= '</li>';

            $links[] = $l;
        }
    }
}

?>

<?php if ($this->group->published == 1) { ?>
    <?php
    $draftUrl = Route::url(
        'index.php?option=com_resources&task=draft&group='
        . $this->group->get('cn')
    );
    $startText = Lang::txt(
        'PLG_GROUPS_RESOURCES_START_A_CONTRIBUTION'
    );
    ?>
    <ul id="page_options">
        <li>
            <a class="icon-add add btn"
               href="<?php echo $draftUrl; ?>"
            ><?php echo $startText; ?></a>
        </li>
    </ul>
<?php } ?>

<section class="section">
    <?php
    $formUrl = Route::url(
        'index.php?option=' . $this->option
        . '&cn=' . $this->group->get('cn')
        . '&active=resources'
    );
    ?>
    <form method="get" action="<?php echo $formUrl; ?>">

        <input type="hidden" name="area" value="<?php echo $this->escape($this->active); ?>" />

        <div class="container">
            <?php
            $filterLabel = Lang::txt(
                'JGLOBAL_FILTER_AND_SORT_RESULTS'
            );
            ?>
            <nav class="entries-filters"
                 aria-label="<?php echo $filterLabel; ?>"
            >
                <ul class="entries-menu filter-options">
                    <?php if (count($links) > 0) { ?>
                        <?php
                        $catFilterUrl = Route::url(
                            'index.php?option=' . $this->option
                            . '&cn=' . $this->group->get('cn')
                            . '&active=resources&area='
                            . urlencode(stripslashes($this->active))
                            . '&sort=' . $this->sort
                            . '&access=' . $this->active
                            . '&limit=' . $this->limit
                        );
                        $catFilterText = Lang::txt(
                            'PLG_GROUPS_RESOURCES_CATEGORIES'
                        );
                        ?>
                        <li class="filter-categories">
                            <a href="<?php echo $catFilterUrl; ?>"
                            ><?php echo $catFilterText; ?></a>
                            <ul>
                                <?php echo implode("\n", $links); ?>
                            </ul>
                        </li>
                    <?php } ?>
                    <?php
                    $activeArea = urlencode(
                        stripslashes($this->active)
                    );
                    $cn = $this->group->get('cn');
                    $baseParams = 'index.php?option=' . $this->option
                        . '&cn=' . $cn
                        . '&active=resources&area=' . $activeArea
                        . '&sort=' . $this->sort;
                    $allClass = ($this->access == 'all')
                        ? ' class="active"' : '';
                    $allUrl = Route::url(
                        $baseParams . '&access=all'
                        . '&limit=' . $this->limit
                    );
                    $allText = Lang::txt(
                        'PLG_GROUPS_RESOURCES_ACCESS_ALL'
                    );
                    ?>
                    <li>
                        <a<?php echo $allClass; ?>
                            href="<?php echo $allUrl; ?>"
                        >
                            <?php echo $allText; ?>
                        </a>
                    </li>
                    <?php
                    $publicClass = ($this->access == 'public')
                        ? ' class="active"' : '';
                    $publicUrl = Route::url(
                        $baseParams . '&access=public'
                        . '&limit=' . $this->limit
                    );
                    $publicText = Lang::txt(
                        'PLG_GROUPS_RESOURCES_ACCESS_PUBLIC'
                    );
                    ?>
                    <li>
                        <a<?php echo $publicClass; ?>
                            href="<?php echo $publicUrl; ?>"
                        >
                            <?php echo $publicText; ?>
                        </a>
                    </li>
                    <?php
                    $protectedClass = ($this->access == 'protected')
                        ? ' class="active"' : '';
                    $protectedUrl = Route::url(
                        $baseParams . '&access=protected'
                        . '&limit=' . $this->limit
                    );
                    $protectedText = Lang::txt(
                        'PLG_GROUPS_RESOURCES_ACCESS_PROTECTED'
                    );
                    ?>
                    <li>
                        <a<?php echo $protectedClass; ?>
                            href="<?php echo $protectedUrl; ?>"
                        >
                            <?php echo $protectedText; ?>
                        </a>
                    </li>
                    <?php
                    $privateClass = ($this->access == 'private')
                        ? ' class="active"' : '';
                    $privateUrl = Route::url(
                        $baseParams . '&access=private'
                        . '&limit=' . $this->limit
                    );
                    $privateText = Lang::txt(
                        'PLG_GROUPS_RESOURCES_ACCESS_PRIVATE'
                    );
                    ?>
                    <li>
                        <a<?php echo $privateClass; ?>
                            href="<?php echo $privateUrl; ?>"
                        >
                            <?php echo $privateText; ?>
                        </a>
                    </li>
                    <?php
                    $sharedClass = ($this->access == 'shared')
                        ? ' class="active"' : '';
                    $sharedUrl = Route::url(
                        $baseParams . '&access=shared'
                        . '&limit=' . $this->limit
                    );
                    $sharedText = Lang::txt(
                        'PLG_GROUPS_RESOURCES_ACCESS_SHARED'
                    );
                    ?>
                    <li>
                        <a<?php echo $sharedClass; ?>
                            href="<?php echo $sharedUrl; ?>"
                        >
                            <?php echo $sharedText; ?>
                        </a>
                    </li>
                </ul>

                <ul class="entries-menu">
                    <?php
                    $sortBase = 'index.php?option=' . $this->option
                        . '&cn=' . $cn
                        . '&active=resources&area=' . $activeArea;
                    $dateClass = ($this->sort == 'date')
                        ? 'active ' . ($this->sortdir == 'desc'
                            ? 'icon-arrow-up' : 'icon-arrow-down')
                        : 'icon-arrow-down';
                    $dateSortdir = ($this->sort == 'date')
                        ? ($this->sortdir == 'desc' ? 'asc' : 'desc')
                        : 'asc';
                    $dateUrl = Route::url(
                        $sortBase . '&sort=date'
                        . '&sortdir=' . $dateSortdir
                        . '&access=' . $this->access
                        . '&limit=' . $this->limit
                    );
                    $dateText = Lang::txt(
                        'PLG_GROUPS_RESOURCES_SORT_BY_DATE'
                    );
                    ?>
                    <li>
                        <a class="<?php echo $dateClass; ?>"
                           href="<?php echo $dateUrl; ?>"
                           title="Sort by newest to oldest"
                        >
                            <?php echo $dateText; ?>
                        </a>
                    </li>
                    <?php
                    $titleClass = ($this->sort == 'title')
                        ? 'active ' . ($this->sortdir == 'desc'
                            ? 'icon-arrow-up' : 'icon-arrow-down')
                        : 'icon-arrow-down';
                    $titleSortdir = ($this->sort == 'title')
                        ? ($this->sortdir == 'desc' ? 'asc' : 'desc')
                        : 'asc';
                    $titleUrl = Route::url(
                        $sortBase . '&sort=title'
                        . '&sortdir=' . $titleSortdir
                        . '&access=' . $this->access
                        . '&limit=' . $this->limit
                    );
                    $titleText = Lang::txt(
                        'PLG_GROUPS_RESOURCES_SORT_BY_TITLE'
                    );
                    ?>
                    <li>
                        <a class="<?php echo $titleClass; ?>"
                           href="<?php echo $titleUrl; ?>"
                           title="Sort by title"
                        >
                            <?php echo $titleText; ?>
                        </a>
                    </li>
                    <?php if ($config->get('show_ranking')) { ?>
                        <?php
                        $rankClass = ($this->sort == 'ranking')
                            ? 'active ' . ($this->sortdir == 'desc'
                                ? 'icon-arrow-up'
                                : 'icon-arrow-down')
                            : 'icon-arrow-down';
                        $rankSortdir = ($this->sort == 'ranking')
                            ? ($this->sortdir == 'desc'
                                ? 'asc' : 'desc')
                            : 'asc';
                        $rankUrl = Route::url(
                            $sortBase . '&sort=ranking'
                            . '&sortdir=' . $rankSortdir
                            . '&access=' . $this->access
                            . '&limit=' . $this->limit
                        );
                        $rankText = Lang::txt(
                            'PLG_GROUPS_RESOURCES_SORT_BY_RANKING'
                        );
                        ?>
                        <li>
                            <a class="<?php echo $rankClass; ?>"
                               href="<?php echo $rankUrl; ?>"
                               title="Sort by popularity"
                            >
                                <?php echo $rankText; ?>
                            </a>
                        </li>
                    <?php } else { ?>
                        <?php
                        $rateClass = ($this->sort == 'rating')
                            ? 'active ' . ($this->sortdir == 'desc'
                                ? 'icon-arrow-up'
                                : 'icon-arrow-down')
                            : 'icon-arrow-down';
                        $rateSortdir = ($this->sort == 'rating')
                            ? ($this->sortdir == 'desc'
                                ? 'asc' : 'desc')
                            : 'asc';
                        $rateUrl = Route::url(
                            $sortBase . '&sort=rating'
                            . '&sortdir=' . $rateSortdir
                            . '&access=' . $this->access
                            . '&limit=' . $this->limit
                        );
                        $rateText = Lang::txt(
                            'PLG_GROUPS_RESOURCES_SORT_BY_RATING'
                        );
                        ?>
                        <li>
                            <a class="<?php echo $rateClass; ?>"
                               href="<?php echo $rateUrl; ?>"
                               title="Sort by popularity"
                            >
                                <?php echo $rateText; ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>

            <div class="container-block">
                <?php
                $html = '';
                $k = 0;
                foreach ($this->results as $category) {
                    $amt = count($category);

                    if ($amt > 0) {
                        $html .= '<ol class="resources results">' . "\n";
                        foreach ($category as $row) {
                            $k++;
                            $html .= $this->view('_item')
                                        ->set('row', $row)
                                        ->set('authorized', $this->authorized)
                                        ->loadTemplate();
                        }
                        $html .= '</ol>' . "\n";
                    }
                }
                echo $html;

                if (!$k) {
                    echo '<p class="warning">'
                        . Lang::txt('PLG_GROUPS_RESOURCES_NONE')
                        . '</p>';
                }
                ?>
            </div><!-- / .container-block -->
            <?php
            $pageNav = $this->pagination(
                $this->total,
                $this->limitstart,
                $this->limit
            );
            $pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
            $pageNav->setAdditionalUrlParam('active', 'resources');
            $pageNav->setAdditionalUrlParam('area', urlencode(stripslashes($this->active)));
            $pageNav->setAdditionalUrlParam('sort', $this->sort);
            $pageNav->setAdditionalUrlParam('access', $this->access);
            echo $pageNav->render();
            ?>
            <div class="clearfix"></div>
        </div><!-- / .container -->
    </form>
</section>
