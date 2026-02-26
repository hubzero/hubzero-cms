<?php

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = $this->offering->link() . '&active=pages';
?>
<div class="pages-menu">
    <ul>
    <?php if (count($this->pages) > 0) { ?>
        <?php
        foreach ($this->pages as $page) {
            ?>
            <?php
            if ($page->get('section_id')) {
                $pageClass = 'page-section';
            } elseif ($page->get('offering_id')) {
                $pageClass = 'page-offering';
            } else {
                $pageClass = 'page-courses';
            }
            if ($page->get('url') == $this->page->get('url')) {
                $pageClass .= ' active';
            }
            $pageUrl = Route::url(
                $base . '&unit=' . $page->get('url')
            );
            $pageTitle = $this->escape(
                stripslashes($page->get('title'))
            );
            ?>
        <li>
            <a class="<?php echo $pageClass; ?> page"
                href="<?php echo $pageUrl; ?>"><?php echo $pageTitle; ?></a>
        </li>
            <?php
        }
        ?>
    <?php } else { ?>
        <li>
            <a class="active page"
                href="<?php echo $base; ?>"><?php echo Lang::txt('PLG_COURSES_PAGES_NONE_FOUND'); ?></a>
        </li>
    <?php } ?>
    </ul>
<?php if ($this->offering->access('manage', 'section')) { ?>
    <p>
        <a class="icon-add add btn" href="<?php echo Route::url($base . '&unit=add'); ?>">
            <?php echo Lang::txt('PLG_COURSES_PAGES_ADD_PAGE'); ?>
        </a>
    </p>
<?php } ?>
</div>
