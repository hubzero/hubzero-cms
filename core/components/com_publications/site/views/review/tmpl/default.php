<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	->css('jquery.fancybox.css', 'system')
	->js();

$rawToken = isset($this->rawToken) ? $this->rawToken : null;
$record = isset($this->record) ? $this->record : null;
$tokenMatch = isset($this->tokenMatch) ? $this->tokenMatch : null;
$expired = isset($this->expired) ? $this->expired : false;
$publication = isset($this->publication) ? $this->publication : null;
$message = isset($this->message) ? $this->message : '';

if (!$rawToken)
{
	?>
	<section class="main section">
		<div class="subject">
			<div class="grid">
				<div class="col span12">
					<h2><?php echo ucwords(Lang::txt('COM_PUBLICATIONS_REVIEW_TITLE_MISSING_TOKEN')); ?></h2>
					<p><?php echo $this->escape($message); ?></p>
				</div>
			</div>
		</div>
	</section>
	<?php
	return;
}

if (!$record)
{
	?>
	<section class="main section">
		<div class="subject">
			<div class="grid">
				<div class="col span12">
					<h2><?php echo ucwords(Lang::txt('COM_PUBLICATIONS_REVIEW_TITLE_INVALID_TOKEN')); ?></h2>
					<p><?php echo $this->escape($message); ?></p>
				</div>
			</div>
		</div>
	</section>
	<?php
	return;
}

if (!$tokenMatch)
{
	?>
	<section class="main section">
		<div class="subject">
			<div class="grid">
				<div class="col span12">
					<h2><?php echo ucwords(Lang::txt('COM_PUBLICATIONS_REVIEW_TITLE_TOKEN_PUB_MISMATCH')); ?></h2>
					<p><?php echo $this->escape($message); ?></p>
				</div>
			</div>
		</div>
	</section>
	<?php
	return;
	
}

if ($expired)
{
	?>
	<section class="main section">
		<div class="subject">
			<div class="grid">
				<div class="col span12">
					<h2><?php echo ucwords(Lang::txt('COM_PUBLICATIONS_REVIEW_TITLE_URL_EXPIRED')); ?></h2>
					<p><?php echo $this->escape($message); ?></p>
				</div>
			</div>
		</div>
	</section>
	<?php
	return;
}

if (!$publication)
{
	?>
	<section class="main section">
		<div class="subject">
			<div class="grid">
				<div class="col span12">
					<h2><?php echo ucwords(Lang::txt('COM_PUBLICATIONS_REVIEW_TITLE_PUBLICATION_NOT_FOUND')); ?></h2>
					<p><?php echo $this->escape($message); ?></p>
				</div>
			</div>
		</div>
	</section>
	<?php
	return;
}

$this->publication->attachments();
$this->publication->license();

// Build a protected download url with token
$downloadUrl = 
	'index.php?option=com_publications&controller=review&task=download'
	. '&token=' . urlencode($rawToken)
	. '&pubid=' . $this->publication->get('id')
	. '&vernum=' . $this->publication->get('version_number');

?>
<section class="main section noborder" id="anon-review-content">
	<div class="main section">
		<div class="pub-content">
			<div class="col span8">
				<?php if (!empty($this->publication->version->title)) : ?>
					<h1><?php echo $this->escape($this->publication->version->title); ?></h1>
				<?php endif; ?>

				<?php if (!empty($this->publication->version->abstract)) : ?>
					<p><?php echo $this->escape($this->publication->version->abstract); ?></p>
				<?php endif; ?>
				
				<?php if (!empty($this->publication->version->description)) : ?>
					<p><?php echo $this->escape($this->publication->describe("clean")); ?></p>
				<?php endif; ?>

				<?php
				$tags = $this->publication->getTagsOfPublication();

				if (!empty($tags)) : ?>
					<ol class="tags">
						<?php foreach ($tags as $tag) : ?>
							<li class="tag"><?php echo $this->escape($tag->raw_tag); ?></li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>
			</div>
			<?php if ($this->publication->base === 'files'): ?>
			<div class="col span8">
				<?php if ($this->publication->version->downloadDisabled): ?>
					<p><?php echo Lang::txt('COM_PUBLICATIONS_DOWNLOAD_DATASET_DISABLED'); ?></p>
				<?php else: ?>
					<a href="<?php echo $this->escape(Route::url($downloadUrl, false)); ?>" class="icon-download btn btn-primary">
						<?php echo Lang::txt('COM_PUBLICATIONS_REVIEW_DOWNLOAD_DATA'); ?>
					</a>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<?php if ($this->publication->isPending()): ?>
			<div class="col span8">
				<p class="warning">
					<?php echo $this->escape(Lang::txt('COM_PUBLICATIONS_REVIEW_NOTICE_PENDING')); ?>
				</p>
			</div>
			<?php elseif ($this->publication->isPublished()): ?>
				<div class="col span8">
					<p class="warning">
						<?php echo $this->escape(Lang::txt('COM_PUBLICATIONS_REVIEW_NOTICE_PUBLISHED')); ?>
					</p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>