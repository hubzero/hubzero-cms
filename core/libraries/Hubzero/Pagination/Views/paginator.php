<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\App;
use Hubzero\Facades\Lang;

defined('_HZEXEC_') or die();

// Initialise variables.
$limits = [];

// Make the option list.
if ($this->limits) {
    foreach ($this->limits as $val) {
        $limits[] = \Hubzero\Html\Builder\Select::option($val);
    }
}

/**
 * Method to create an active pagination link to the item
 *
 * @param   Item    $item  The object with which to make an active link.
 * @return  string  HTML link
 */
$itemLink = function ($item, $prefix) {
    if (App::isAdmin()) {
        return '<a data-prefix="' . htmlspecialchars((string) $prefix, ENT_QUOTES, 'UTF-8') . '"'
            . ' data-start="' . ($item->base > 0 ? $item->base : 0) . '">' . $item->text . '</a>';
    } else {
        return '<a href="' . $item->link . '" ' . ($item->rel ? 'rel="' . $item->rel . '" ' : '')
            . 'class="pagenav">' . $item->text . '</a>';
    }
};

// The counter: which results are showing.
$fromResult = $this->start + 1;

// If the limit is reached before the end of the list.
if ($this->start + $this->limit < $this->total) {
    $toResult = $this->start + $this->limit;
} else {
    $toResult = $this->total;
}

// If there are results found.
if ($this->total > 0) {
    $counterText = Lang::txt('JLIB_HTML_RESULTS_OF', $fromResult, $toResult, $this->total);
} else {
    $counterText = Lang::txt('JLIB_HTML_NO_RECORDS_FOUND');
}

// Build the select list.
$selected = $this->viewall ? 0 : $this->limit;

if (App::isAdmin()) {
    $attr = 'class="inputbox" size="1" onchange="this.form.submit()"';
} else {
    $attr = 'class="inputbox" size="1"';
}

$limitId = htmlspecialchars((string) $this->prefix, ENT_QUOTES, 'UTF-8') . 'limit';
$limitControl = \Hubzero\Html\Builder\Select::genericlist(
    $limits,
    $this->pages->prefix . 'limit',
    $attr,
    'value',
    'text',
    $selected,
);

if (!App::isAdmin()) {
    $limitControl .= ' <button type="submit" class="pagination-go">'
        . Lang::txt('JLIB_HTML_PAGINATION_GO') . '</button>';
}

$showStart = $this->pages->start->base !== null
    && $this->pages->start->base !== $this->pages->previous->base;

// Show page 1 link and ellipsis
$showFirstEllipsis = $this->pages->ellipsis && $this->pages->i > 1;
$firstPage = isset($this->pages->pages[1]) ? $this->pages->pages[1] : null;

// The numbered pages, laid out as the markup below is.
$pad = str_repeat(' ', 20);
$pageItems = '';
for (; $this->pages->i <= $this->pages->stoploop && $this->pages->i <= $this->pages->total; $this->pages->i++) {
    if (isset($this->pages->pages[$this->pages->i])) {
        $page = $this->pages->pages[$this->pages->i];
        if ($page->base !== null) {
            $pageItems .= $pad . '<li class="page">' . $itemLink($page, $this->prefix) . '</li>' . "\n" . $pad;
        } else {
            $pageItems .= $pad . '<li class="page active"><strong aria-current="page">' . $page->text
                . '</strong></li>' . "\n" . $pad;
        }
    }
}

// Show last page link
$showLastEllipsis = $this->pages->ellipsis && ($this->pages->i - 1) < $this->pages->total;
$lastPage = isset($this->pages->pages[$this->pages->total])
    ? $this->pages->pages[$this->pages->total]
    : null;
?>
<nav class="pagination" aria-label="<?php echo Lang::txt('JGLOBAL_PAGINATION_LABEL'); ?>">
    <ul class="list-footer">
        <li class="counter">
            <?php echo $counterText; ?>
        </li>
        <li class="limit">
            <label for="<?php echo $limitId; ?>"><?php echo Lang::txt('JGLOBAL_DISPLAY_NUM'); ?></label>
            <?php echo $limitControl; ?>
        </li>
        <?php if (App::isAdmin()) { ?>
            <?php if ($showStart) { ?>
            <li class="pagination-start start">
                    <?php echo $itemLink($this->pages->start, $this->prefix); ?>
            </li>
            <?php } ?>
        <li class="pagination-prev prev">
            <?php if ($this->pages->previous->base !== null) { ?>
                <?php echo $itemLink($this->pages->previous, $this->prefix); ?>
            <?php } else { ?>
                <span class="pagenav"><?php echo $this->pages->previous->text; ?></span>
            <?php } ?>
        </li>
        <?php } else { ?>
        <li class="pagination-prev prev">
            <?php if ($this->pages->previous->base !== null) { ?>
                <a href="<?php echo $this->pages->previous->link; ?>"
                    aria-label="<?php echo Lang::txt('JLIB_HTML_PAGINATION_PREVIOUS_PAGE'); ?>">&lsaquo;</a>
            <?php } else { ?>
                <span class="disabled" aria-hidden="true">&lsaquo;</span>
            <?php } ?>
        </li>
        <?php } ?>
        <?php if ($showFirstEllipsis) { ?>
            <?php if ($firstPage && $firstPage->base !== null) { ?>
                <li class="page"><?php echo $itemLink($firstPage, $this->prefix); ?></li>
            <?php } ?>
            <li class="page ellipsis" aria-hidden="true"><span>&hellip;</span></li>
        <?php } ?>
        <?php echo $pageItems; ?>
        <?php if ($showLastEllipsis) { ?>
            <li class="page ellipsis" aria-hidden="true"><span>&hellip;</span></li>
            <?php if ($lastPage && $lastPage->base !== null) { ?>
                <li class="page"><?php echo $itemLink($lastPage, $this->prefix); ?></li>
            <?php } ?>
        <?php } ?>
        <?php if (App::isAdmin()) { ?>
        <li class="pagination-next next">
            <?php if ($this->pages->next->base !== null) { ?>
                <?php echo $itemLink($this->pages->next, $this->prefix); ?>
            <?php } else { ?>
                <span class="pagenav"><?php echo $this->pages->next->text; ?></span>
            <?php } ?>
        </li>
            <?php if ($this->pages->end->base !== null && $this->pages->end->base !== $this->pages->next->base) { ?>
            <li class="pagination-end end">
                    <?php echo $itemLink($this->pages->end, $this->prefix); ?>
            </li>
            <?php } ?>
        <?php } else { ?>
        <li class="pagination-next next">
            <?php if ($this->pages->next->base !== null) { ?>
                <a href="<?php echo $this->pages->next->link; ?>"
                    aria-label="<?php echo Lang::txt('JLIB_HTML_PAGINATION_NEXT_PAGE'); ?>">&rsaquo;</a>
            <?php } else { ?>
                <span class="disabled" aria-hidden="true">&rsaquo;</span>
            <?php } ?>
        </li>
        <?php } ?>
    </ul>
    <input type="hidden" name="<?php echo $this->prefix; ?>limitstart" value="<?php echo $this->start; ?>" />
</nav>