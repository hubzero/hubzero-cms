<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{cats: mixed, no_html: mixed, sections: mixed, title: mixed} $this
 */

if (!$this->no_html) {
    $this
         ->css()
         ->js();
    ?>
<header id="content-header">
    <h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<nav aria-label="<?php echo Lang::txt('COM_USAGE_CATEGORIES'); ?>">
    <ul class="sub-menu">
        <?php
        if ($this->cats) {
            $i = 1;
            $cs = [];
            foreach ($this->cats as $cat) {
                $name = key($cat);
                if ($cat[$name] != '') {
                    $isActive = (strtolower($name) == $this->task);
                    ?>
                    <?php
                    $linkUrl = Route::url('index.php?option=' . $this->option . '&task=' . $name);
                    $ariaCurrentAttr = $isActive ? ' aria-current="page"' : '';
                    ?>
                <li id="sm-<?= $i ?>"<?= $isActive ? ' class="active"' : '' ?>><a class="tab"
                    href="<?= $linkUrl ?>"<?= $ariaCurrentAttr ?>><span><?= $cat[$name] ?></span></a></li>
                    <?php
                    $i++;
                    $cs[] = $name;
                }
            }
        }
        ?>
    </ul>
</nav>

<?php } ?>

<?php
$h = 'hide';
$c = 'main';
if ($this->sections) {
    $k = 0;
    foreach ($this->sections as $section) {
        if ($section != '') {
            if ($this->no_html) {
                echo $section;
            } else {
                $cls  = ($c) ? $c . ' ' : '';
                if (key($this->cats[$k]) != $this->task) {
                    $cls .= ($h) ? $h . ' ' : '';
                }
                ?>
                <section class="<?php echo $cls; ?>section" id="statistics">
                    <?php echo $section; ?>
                </section><!-- / #statistics.<?php echo $cls; ?>section -->
                <?php
            }
        }
        $k++;
    }
}
