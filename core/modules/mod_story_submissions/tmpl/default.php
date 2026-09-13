<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!isset($this->rows))
{
	return;
}

?>
<div class="mod-story-submissions">
	<p class="story-queue-depth">
		<a href="<?php echo Route::url('index.php?option=com_story&view=queue'); ?>"><?php
			echo Lang::txt('MOD_STORY_SUBMISSIONS_DEPTH', (int) $this->depth);
		?></a>
	</p>

<?php if (count($this->rows)) { ?>
	<ul class="story-queue-top">
<?php foreach ($this->rows as $row) { ?>
		<li<?php echo $row->get('attention_needed') ? ' class="needs-attention"' : ''; ?>>
			<?php echo $this->escape($row->get('subject')); ?>
			<span class="story-queue-weight"><?php echo round((float) $row->get('editor_popularity'), 1); ?></span>
		</li>
<?php } ?>
	</ul>
<?php } else { ?>
	<p class="info"><?php echo Lang::txt('MOD_STORY_SUBMISSIONS_EMPTY'); ?></p>
<?php } ?>
</div>
