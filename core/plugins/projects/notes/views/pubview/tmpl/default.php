<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	 ->js();

$html = $this->revision->get('pagehtml');

// Com_wiki adds /projects - strip it out
$html = str_replace('projects/projects/', 'projects/', $html);

// Fix up images
$html = str_replace($this->page->get('scope') . DS . $this->page->get('pagename'), 'wiki/' . $this->page->get('id'), $html);

?>

<div class="wiki-wrap">
	<p class="wiki-back"><?php echo Lang::txt('PLG_PROJECTS_NOTES_PUBLIC_VIEW'); ?> <span class="goback"><a href="<?php echo $this->model->link(); ?>"><?php echo Lang::txt('PLG_PROJECTS_NOTES_BACK_TO_PROJECT'); ?></a></span></p>
	<div class="wiki-content">
		<h1 class="page-title"><?php echo $this->page->get('title'); ?></h1>
		<div class="wikipage"><?php echo $html; ?></div>
	</div>
</div>
