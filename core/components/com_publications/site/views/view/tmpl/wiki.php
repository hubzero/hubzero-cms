<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('wiki.css')
     ->js();

$html = $this->page->pagehtml;

// Com_wiki adds /projects - strip it out
$html = str_replace('projects/projects/', 'projects/', $html);

// Fix up images
$html = str_replace($this->page->scope . DS . $this->page->pagename, 'wiki/' . $this->page->id, $html);
?>
<div class="wiki-wrap">
	<p class="wiki-back"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->publication->id); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_BACK_TO_PUBLICATION'); ?>  &ldquo;<?php echo $this->publication->title; ?>&rdquo;</a></p>
	<div class="wiki-content">
		<h1 class="page-title"><?php echo $this->page->title; ?></h1>
		<div class="wikipage"><?php echo $html; ?></div>
	</div>
</div>
