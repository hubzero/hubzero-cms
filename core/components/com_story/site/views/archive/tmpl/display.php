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
	<h2><?php echo $this->filters['year'] ? Lang::txt('COM_STORY_ARCHIVE_FOR', $this->escape($this->label)) : Lang::txt('COM_STORY_ARCHIVE'); ?></h2>
</header>

<section class="main section">

<?php if (!count($this->rows)) { ?>
	<p class="warning"><?php echo Lang::txt('COM_STORY_NOTHING_THEN'); ?></p>
<?php } else { ?>
	<ul class="story-archive">
<?php foreach ($this->rows as $row) { ?>
		<li>
			<time datetime="<?php echo $this->escape($row->get('publish_up')); ?>"><?php echo Date::of($row->get('publish_up'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
			<a href="<?php echo Route::url($row->link()); ?>"><?php echo $this->escape($row->get('title')); ?></a>
		</li>
<?php } ?>
	</ul>

	<?php echo $this->rows->pagination; ?>
<?php } ?>

</section>
