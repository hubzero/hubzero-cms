<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Pathway;
use Hubzero\Facades\Route;

defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{
 *     controller: mixed,
 *     course: mixed,
 *     offering: mixed,
 *     option: mixed,
 *     page: mixed,
 *     pages: mixed,
 * } $this
 */

$this
     ->css()
     ->js();

$base = $this->offering->link() . '&active=pages';

$this
     ->view('default_menu')
     ->set('option', $this->option)
     ->set('controller', $this->controller)
     ->set('course', $this->course)
     ->set('offering', $this->offering)
     ->set('page', $this->page)
     ->set('pages', $this->pages)
     ->display();
?>
<div class="pages-wrap">
<?php
if (!$this->page) {
    ?>
    <div class="pages-content">
        <div id="pages-introduction">
            <div class="instructions">
                <p><?php echo Lang::txt('PLG_COURSES_PAGES_NONE_FOUND'); ?></p>
            </div>
        </div>
    </div><!-- / .pages-content -->
    <?php
} else {
    Pathway::append(
        stripslashes($this->page->get('title')),
        $base . '&unit=' . $this->page->get('url'),
    );

    $authorized = false;
    if ($this->page->get('offering_id')) {
        // If they're a course level manager
        if ($this->offering->access('manage')) {
            $authorized = true;
        } elseif ($this->offering->access('manage', 'section') && $this->page->get('section_id')) {
            // If they're a section manager and the page is a section page
            $authorized = true;
        }
    }
    ?>
    <?php if ($authorized) { ?>
        <ul class="manager-options">
            <li>
                <?php
                $actionUrl = Route::url($base . '&unit=' . $this->page->get('url') . '&b=delete');
                $confirmText = Lang::txt('PLG_COURSES_PAGES_CONFIRM_DELETE');
                ?>
                <button type="submit" form="hz-post-action" formaction="<?= $actionUrl ?>" class="icon-delete delete"
                    title="<?= Lang::txt('PLG_COURSES_PAGES_DELETE') ?>"
                    data-confirm="<?= $confirmText ?>"><?= Lang::txt('PLG_COURSES_PAGES_DELETE') ?></button>
            </li>
            <li>
                <a class="icon-edit edit"
                    href="<?= Route::url($base . '&unit=' . $this->page->get('url') . '&b=edit') ?>"
                    title="<?= Lang::txt('PLG_COURSES_PAGES_EDIT') ?>">
                    <?php echo Lang::txt('PLG_COURSES_PAGES_EDIT'); ?>
                </a>
            </li>
        </ul>
    <?php } ?>
    <div class="pages-content">
        <?php echo $this->page->content('parsed'); ?>
    </div><!-- / .pages-content -->
    <?php
}
?>
</div><!-- / .pages-wrap -->
