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
	<h2><?php echo Lang::txt('COM_STORY'); ?></h2>
</header>

<section class="main section">

<?php if (!count($this->rows)) { ?>
	<p class="warning"><?php echo Lang::txt('COM_STORY_NOTHING_YET'); ?></p>
<?php } else { ?>
	<div class="story-river">
<?php foreach ($this->rows as $row) { $text = $row->text(); ?>
		<article class="story">
			<h3 class="story-title">
				<a href="<?php echo Route::url($row->link()); ?>"><?php echo $this->escape($row->get('title')); ?></a>
			</h3>
<?php if ($row->get('kicker')) { ?>
			<p class="story-kicker"><?php echo $this->escape($row->get('kicker')); ?></p>
<?php } ?>
			<p class="story-byline">
				<time datetime="<?php echo $this->escape($row->get('publish_up')); ?>"><?php
					echo Date::of($row->get('publish_up'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
				?></time>
<?php if ($topic = $row->topic()) { if ($topic->get('id')) { ?>
				<span class="story-topic"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&view=topics&topic=' . $topic->get('alias')); ?>"><?php echo $this->escape($topic->get('title')); ?></a></span>
<?php } } ?>
			</p>
<?php if ($text->get('intro')) { ?>
			<div class="story-intro"><?php echo $text->get('intro'); ?></div>
<?php } ?>
			<p class="story-more">
				<a href="<?php echo Route::url($row->link()); ?>"><?php echo Lang::txt('COM_STORY_READ_ON'); ?></a>
			</p>
		</article>
<?php } ?>
	</div>

	<?php echo $this->rows->pagination; ?>
<?php } ?>

</section>
