<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$topic = $this->story->topic();

?>
<header id="content-header">
	<h2><?php echo $this->escape($this->story->get('title')); ?></h2>
<?php if ($this->story->get('kicker')) { ?>
	<p class="story-kicker"><?php echo $this->escape($this->story->get('kicker')); ?></p>
<?php } ?>
</header>

<section class="main section">

	<article class="story story-full">
		<p class="story-byline">
			<time datetime="<?php echo $this->escape($this->story->get('publish_up')); ?>"><?php
				echo Date::of($this->story->get('publish_up'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			?></time>
<?php if ($topic->get('id')) { ?>
			<span class="story-topic"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&view=topics&topic=' . $topic->get('alias')); ?>"><?php echo $this->escape($topic->get('title')); ?></a></span>
<?php } ?>
<?php if ($this->story->get('submitter_id')) { ?>
			<span class="story-submitter"><?php echo Lang::txt('COM_STORY_SUBMITTED_BY', $this->escape(User::getInstance($this->story->get('submitter_id'))->get('name'))); ?></span>
<?php } ?>
		</p>

<?php if ($this->text->get('intro')) { ?>
		<div class="story-intro"><?php echo $this->text->get('intro'); ?></div>
<?php } ?>

<?php if ($this->text->get('body')) { ?>
		<div class="story-body"><?php echo $this->text->get('body'); ?></div>
<?php } ?>

<?php if ($this->text->get('related')) { ?>
		<aside class="story-related">
			<h4><?php echo Lang::txt('COM_STORY_RELATED'); ?></h4>
			<?php echo $this->text->get('related'); ?>
		</aside>
<?php } ?>
	</article>

	<p class="story-back"><a href="<?php echo Route::url('index.php?option=' . $this->option); ?>"><?php echo Lang::txt('COM_STORY_BACK'); ?></a></p>

</section>
