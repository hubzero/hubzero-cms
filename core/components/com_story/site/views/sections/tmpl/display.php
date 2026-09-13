<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<header id="content-header">
	<h2><?php echo $this->section ? $this->escape($this->section->get('title')) : Lang::txt('COM_STORY_SECTIONS'); ?></h2>
</header>

<section class="main section">

<?php if (!$this->section) { ?>
	<?php if (!count($this->sections)) { ?>
	<p class="warning"><?php echo Lang::txt('COM_STORY_NO_SECTIONS'); ?></p>
	<?php } else { ?>
	<ul class="story-sections">
	<?php foreach ($this->sections as $section) { ?>
		<li>
			<a href="<?php echo Route::url('index.php?option=' . $this->option . '&view=sections&section=' . $section->get('alias')); ?>"><?php echo $this->escape($section->get('title')); ?></a>
		<?php if ($section->get('description')) { ?>
			<span class="hint"><?php echo $this->escape($section->get('description')); ?></span>
		<?php } ?>
		</li>
	<?php } ?>
	</ul>
	<?php } ?>
<?php } else { ?>
	<?php if (!count($this->rows)) { ?>
	<p class="warning"><?php echo Lang::txt('COM_STORY_NOTHING_YET'); ?></p>
	<?php } else { ?>
	<div class="story-river">
	<?php foreach ($this->rows as $row) { ?>
		<article class="story">
			<h3 class="story-title"><a href="<?php echo Route::url($row->link()); ?>"><?php echo $this->escape($row->get('title')); ?></a></h3>
		<?php if ($row->get('kicker')) { ?>
			<p class="story-kicker"><?php echo $this->escape($row->get('kicker')); ?></p>
		<?php } ?>
			<p class="story-byline"><time datetime="<?php echo $this->escape($row->get('publish_up')); ?>"><?php echo Date::of($row->get('publish_up'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></p>
		</article>
	<?php } ?>
	</div>

	<?php echo $this->rows->pagination; ?>
	<?php } ?>
<?php } ?>

</section>
