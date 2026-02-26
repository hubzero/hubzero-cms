<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

$addUrl = Route::url(
    'index.php?option=' . $this->option . '&task=add'
);
$yearUrl = Route::url(
    'index.php?option=' . $this->option . '&year=' . $this->year
);
$monthUrl = Route::url(
    'index.php?option=' . $this->option
    . '&year=' . $this->year . '&month=' . $this->month
);
$weekUrl = Route::url(
    'index.php?option=' . $this->option
    . '&year=' . $this->year . '&month=' . $this->month
    . '&day=' . $this->day . '&task=week'
);
$dayUrl = Route::url(
    'index.php?option=' . $this->option
    . '&year=' . $this->year . '&month=' . $this->month
    . '&day=' . $this->day
);

$detailsUrl = Route::url(
    'index.php?option=' . $this->option
    . '&task=details&id=' . $this->event->id
);
$registerUrl = Route::url(
    'index.php?option=' . $this->option
    . '&task=details&id=' . $this->event->id
    . '&page=register'
);
$overviewTxt = Lang::txt('EVENTS_OVERVIEW');
$registerTxt = Lang::txt('EVENTS_REGISTER');
?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>

    <?php if ($this->authorized) { ?>
    <div id="content-header-extra">
        <ul id="useroptions">
            <li class="last">
                <a class="icon-add add btn" href="<?php echo $addUrl; ?>">
                    <?php echo Lang::txt('EVENTS_ADD_EVENT'); ?>
                </a>
            </li>
        </ul>
    </div><!-- / #content-header-extra -->
    <?php } ?>
</header><!-- / #content-header -->

<nav>
    <ul class="sub-menu">
        <li<?php if ($this->task == 'year') {
            echo ' class="active"';
           } ?>>
            <a href="<?php echo $yearUrl; ?>">
                <span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_YEAR'); ?></span>
            </a>
        </li>
        <li<?php if ($this->task == 'month') {
            echo ' class="active"';
           } ?>>
            <a href="<?php echo $monthUrl; ?>">
                <span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_MONTH'); ?></span>
            </a>
        </li>
        <li<?php if ($this->task == 'week') {
            echo ' class="active"';
           } ?>>
            <a href="<?php echo $weekUrl; ?>">
                <span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_WEEK'); ?></span>
            </a>
        </li>
        <li<?php if ($this->task == 'day') {
            echo ' class="active"';
           } ?>>
            <a href="<?php echo $dayUrl; ?>">
                <span><?php echo Lang::txt('EVENTS_CAL_LANG_REP_DAY'); ?></span>
            </a>
        </li>
    </ul>
</nav>

<section class="main section">
    <h3><?php echo stripslashes($this->event->title); ?></h3>
<?php
    $html  = '<div id="sub-sub-menu">' . "\n";
    $html .= '<ul>' . "\n";
    $html .= "\t" . '<li';
if ($this->page->alias == '') {
    $html .= ' class="active"';
}
    $html .= '><a class="tab" href="' . $detailsUrl . '">'
        . '<span>' . $overviewTxt . '</span>'
        . '</a></li>' . "\n";
if ($this->pages) {
    foreach ($this->pages as $p) {
        $pageUrl = Route::url(
            'index.php?option=' . $this->option
            . '&task=details&id=' . $this->event->id
            . '&page=' . $p->alias
        );
        $html .= "\t" . '<li';
        if ($this->page->alias == $p->alias) {
            $html .= ' class="active"';
        }
        $pageTitle = trim(stripslashes($p->title));
        $html .= '><a class="tab" href="' . $pageUrl . '">'
            . '<span>' . $pageTitle . '</span>'
            . '</a></li>' . "\n";
    }
}
    $html .= "\t" . '<li';
if ($this->page->alias == 'register') {
    $html .= ' class="active"';
}
    $html .= '><a class="tab" href="' . $registerUrl . '">'
        . '<span>' . $registerTxt . '</span>'
        . '</a></li>' . "\n";
    $html .= '</ul>' . "\n";
    $html .= '<div class="clear"></div>' . "\n";
    $html .= '</div>' . "\n";
    echo $html;
?>
<?php if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
    <form method="post" action="index.php" id="hubForm">
        <p class="warning"><?php echo Lang::txt('EVENTS_CLOSED_REGISTRATION'); ?></p>
    </form>
</section><!-- / .main section -->
