<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Pathway;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

$this->css();

// Add the "all" category
$all = array(
    'category' => '',
    'title'    => Lang::txt('COM_WHATSNEW_ALL_CATEGORIES'),
    'total'    => $this->total
);

array_unshift($this->cats, $all);

// An array for storing all the links we make
$links = array();

// Loop through each category
foreach ($this->cats as $cat) {
    // Only show categories that have returned search results
    if ($cat['total'] > 0) {
        // If we have a specific category, prepend it to the search term
        if ($cat['category']) {
            $blob = $cat['category'] . ':' . $this->period;
        } else {
            $blob = $this->period;
        }

        // Is this the active category?
        $a = '';
        if ($cat['category'] == $this->active) {
            $a = ' class="active"';

            Pathway::append(
                $cat['title'],
                'index.php?option=' . $this->option . '&period=' . urlencode(stripslashes($blob))
            );
        }

        // Build the HTML
        $catUrl = Route::url(
            'index.php?option=' . $this->option . '&period=' . urlencode(stripslashes($blob))
        );
        $l = "\t" . '<li' . $a . '><a href="' . $catUrl . '">'
            . $this->escape($cat['title'])
            . ' <span class="item-count">' . $cat['total'] . '</span></a>';

        // Are there sub-categories?
        if (isset($cat['_sub']) && is_array($cat['_sub'])) {
            // An array for storing the HTML we make
            $k = array();

            // Loop through each sub-category
            foreach ($cat['_sub'] as $subcat) {
                // Only show sub-categories that returned search results
                if ($subcat['total'] > 0) {
                    // If we have a specific category, prepend it to the search term
                    if ($subcat['category']) {
                        $blob = $subcat['category'] . ':' . $this->period;
                    } else {
                        $blob = $this->period;
                    }

                    // Is this the active category?
                    $a = '';
                    if ($subcat['category'] == $this->active) {
                        $a = ' class="active"';

                        Pathway::append(
                            $subcat['title'],
                            'index.php?option=' . $this->option . '&period=' . urlencode(stripslashes($blob))
                        );
                    }

                    // Build the HTML
                    $subUrl = Route::url(
                        'index.php?option=' . $this->option . '&period=' . urlencode(stripslashes($blob))
                    );
                    $k[] = "\t\t\t" . '<li' . $a . '><a href="' . $subUrl . '">'
                        . $this->escape($subcat['title'])
                        . ' <span class="item-count">' . $subcat['total'] . '</span></a></li>';
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
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
    <?php $formAction = Route::url('index.php?option=' . $this->option); ?>
    <form action="<?php echo $formAction; ?>" method="get" class="section-inner hz-layout-with-aside">
        <div class="subject">
            <div class="container">
            <?php
            $foundresults = false;
            $dopaging = false;
            $html = '';
            foreach ($this->results as $k => $category) {
                $ci = $k + 1; // cats index offset by "all" at position 0
                $amt = count($category);

                if ($amt > 0 && isset($this->cats[$ci])) {
                    $foundresults = true;

                    // Is this category the active category?
                    if (!$this->active || $this->active == $this->cats[$ci]['category']) {
                        // It is - get some needed info
                        $name  = $this->cats[$ci]['title'];
                        $total = $this->cats[$ci]['total'];
                        $divid = 'search' . $this->cats[$ci]['category'];

                        if ($this->active == $this->cats[$ci]['category']) {
                            $dopaging = true;
                        }
                    } else {
                        // It is not - does this category have sub-categories?
                        if (isset($this->cats[$ci]['_sub']) && is_array($this->cats[$ci]['_sub'])) {
                            // It does - loop through them and see if one is the active category
                            foreach ($this->cats[$ci]['_sub'] as $sub) {
                                if ($this->active == $sub['category']) {
                                    // Found an active category
                                    $name  = $sub['title'];
                                    $total = $sub['total'];
                                    $divid = 'search' . $sub['category'];

                                    $dopaging = true;
                                    break;
                                }
                            }
                        }
                    }

                    $num = ($total > 1)
                        ? Lang::txt('COM_WHATSNEW_RESULTS', $total)
                        : Lang::txt('COM_WHATSNEW_RESULT', $total);
                    $this->total = $num;

                    // A function for category specific items that may be needed
                    // Check if a function exist (using old style plugins)
                    $f = 'plgWhatsnew' . ucfirst($this->cats[$ci]['category']) . 'Doc';
                    if (function_exists($f)) {
                        $f();
                    }
                    // Check if a method exist (using JPlugin style)
                    $catName = ucfirst($this->cats[$ci]['category']);
                    $obj = 'Plugins\\Whatsnew\\' . $catName . '\\' . $catName;
                    if (method_exists($obj, 'documents')) {
                        $html .= call_user_func(array($obj, 'documents'));
                    }

                    $act = ($this->active) ? $this->active : $this->cats[$ci]['category'];

                    $feedPeriod = urlencode(strToLower($act) . ':' . stripslashes($this->period));
                    $feed = Route::url(
                        'index.php?option=' . $this->option . '&task=feed.rss&period=' . $feedPeriod
                    );
                    if (substr($feed, 0, 4) != 'http') {
                        $feed = rtrim(Request::getSchemeAndHttpHost(), '/') . '/' . ltrim($feed, '/');
                    }
                    $feed = str_replace('https:://', 'http://', $feed);

                    // Build the category HTML
                    $html .= '<div class="container-block" id="' . $divid . '">' . "\n";
                    $feedLink = '<a class="icon-feed feed" href="' . $feed . '">'
                        . Lang::txt('COM_WHATSNEW_FEED') . '</a>';
                    $html .= '<h3 id="rel-' . $divid . '">'
                        . $this->escape($name) . ' ' . $feedLink . '</h3>' . "\n";

                    // Does this category have custom output?
                    // Check if a function exist (using old style plugins)
                    $func = 'plgWhatsnew' . ucfirst($this->cats[$ci]['category']) . 'Before';
                    if (function_exists($func)) {
                        $html .= $func($this->period);
                    }
                    // Check if a method exist (using JPlugin style)
                    $catName = ucfirst($this->cats[$ci]['category']);
                    $obj = 'Plugins\\Whatsnew\\' . $catName . '\\' . $catName;
                    if (method_exists($obj, 'before')) {
                        $html .= call_user_func(array($obj,'before'), $this->period);
                    }

                    $html .= '<ol class="entries">' . "\n";
                    foreach ($category as $row) {
                        $row->href = str_replace('&amp;', '&', $row->href);
                        $row->href = str_replace('&', '&amp;', $row->href);

                        // Does this category have a unique output display?
                        $func = 'plgWhatsnew' . ucfirst($row->section == null ? '' : $row->section) . 'Out';
                        // Check if a method exist (using JPlugin style)
                        $catName = ucfirst($this->cats[$ci]['category']);
                        $obj = 'Plugins\\Whatsnew\\' . $catName . '\\' . $catName;

                        if (function_exists($func)) {
                            $html .= $func($row, $this->period);
                        } elseif (method_exists($obj, 'out')) {
                            $html .= call_user_func(array($obj,'out'), $row, $this->period);
                        } else {
                            if (strstr($row->href, 'index.php')) {
                                $row->href = Route::url($row->href);
                            }

                            $html .= "\t" . '<li>' . "\n";
                            $html .= "\t\t" . '<p class="title"><a href="' . $row->href . '">'
                                . stripslashes($row->title) . '</a></p>' . "\n";
                            if ($row->text) {
                                $truncated = \Hubzero\Utility\Str::truncate(
                                    strip_tags(\Hubzero\Utility\Sanitize::stripAll(stripslashes($row->text))),
                                    200
                                );
                                $html .= "\t\t" . '<p>' . $truncated . '</p>' . "\n";
                            }
                            $base = rtrim(Request::getSchemeAndHttpHost(), '/');
                            $html .= "\t\t" . '<p class="href">'
                                . $base . '/' . ltrim($row->href, '/') . '</p>' . "\n";
                            $html .= "\t" . '</li>' . "\n";
                        }
                    }
                    $html .= '</ol>' . "\n";

                    // Initiate paging if we we're displaying an active category
                    if ($dopaging) {
                        $pageNav = $this->pagination(
                            $this->total,
                            $this->start,
                            $this->limit
                        );

                        $pageNav->setAdditionalUrlParam('category', urlencode(strToLower($this->active)));
                        $pageNav->setAdditionalUrlParam('period', $this->period);

                        $html .= $pageNav->render();
                        $html .= '<div class="clearfix"></div>';
                    } else {
                        $html .= '<p class="moreresults">' . Lang::txt('COM_WHATSNEW_TOP_SHOWN', $amt);
                        // Add a "more" link if necessary
                        $ttl = 0;
                        if (isset($this->totals[$k])) {
                            if (is_array($this->totals[$k])) {
                                foreach ($this->totals[$k] as $t) {
                                    $ttl += $t;
                                }
                            } else {
                                $ttl = $this->totals[$k];
                            }
                        }
                        if ($ttl > 5) {
                            $morePeriod = urlencode(
                                strToLower($this->cats[$ci]['category']) . ':' . stripslashes($this->period)
                            );
                            $moreUrl = Route::url(
                                'index.php?option=' . $this->option . '&period=' . $morePeriod
                            );
                            $html .= ' | <a href="' . $moreUrl . '">'
                                . Lang::txt('COM_WHATSNEW_SEE_MORE_RESULTS') . '</a>';
                        }
                        $html .= '</p>' . "\n\n";
                    }

                    // Does this category have custom output?
                    // Check if a function exist (using old style plugins)
                    $func = 'plgWhatsnew' . ucfirst($this->cats[$ci]['category']) . 'After';
                    if (function_exists($func)) {
                        $html .= $func($this->period);
                    }
                    // Check if a method exist (using JPlugin style)
                    $catName = ucfirst($this->cats[$ci]['category']);
                    $obj = 'Plugins\\Whatsnew\\' . $catName . '\\' . $catName;
                    if (method_exists($obj, 'after')) {
                        $html .= call_user_func(array($obj,'after'), $this->period);
                    }

                    $html .= '</div><!-- / #' . $divid . ' -->' . "\n";
                }
            }
            echo $html;
            ?>
            <?php if (!$foundresults) { ?>
                <p class="warning"><?php echo Lang::txt('COM_WHATSNEW_NO_RESULTS'); ?></p>
            <?php } ?>
            </div><!-- / .container -->
        </div><!-- / .subject -->
        <aside class="aside">
            <fieldset>
                <legend><?php echo Lang::txt('COM_WHATSNEW_FILTER'); ?></legend>
                <label for="period">
                    <?php echo Lang::txt('COM_WHATSNEW_TIME_PERIOD'); ?>
                    <?php
                    echo Html::select(
                        'genericlist',
                        $this->periodlist,
                        'period',
                        '',
                        'value',
                        'text',
                        $this->period
                    );
                    ?>
                </label>
                <p class="submit"><input type="submit" value="<?php echo Lang::txt('COM_WHATSNEW_GO'); ?>" /></p>
            </fieldset>

        <?php if (count($links) > 0) { ?>
            <div class="container">
                <ul class="sub-nav">
                    <?php echo implode("\n", $links); ?>
                </ul>
            </div>
        <?php } ?>
            <input type="hidden" name="category" value="<?php echo $this->escape($this->active); ?>" />
        </aside><!-- / .aside -->
    </form>
</section><!-- / .main section -->
