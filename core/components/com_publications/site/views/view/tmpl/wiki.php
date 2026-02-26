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
     ->css('wiki.css')
     ->js();

$html = $this->page->pagehtml;

// Com_wiki adds /projects - strip it out
$html = str_replace('projects/projects/', 'projects/', $html);

// Fix up images
$html = str_replace(
    $this->page->scope . DS . $this->page->pagename,
    'wiki/' . $this->page->id,
    $html
);
?>
<div class="wiki-wrap">
    <?php $_v1 = Route::url('index.php?option=' . $this->option . '&id=' . $this->publication->id); ?>
    <?php $_v2 = Lang::txt('COM_PUBLICATIONS_BACK_TO_PUBLICATION'); ?>
    <?php $_v3 = $this->publication->title; ?>
    <p class="wiki-back"><a href="<?php echo $_v1; ?>"><?php echo $_v2; ?>  &ldquo;<?php echo $_v3; ?>&rdquo;</a></p>
    <div class="wiki-content">
        <h1 class="page-title"><?php echo $this->page->title; ?></h1>
        <div class="wikipage"><?php echo $html; ?></div>
    </div>
</div>
