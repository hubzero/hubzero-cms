<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>

<header id="content-header">
	<h2><?php echo Lang::txt(strtoupper($this->option)); ?></h2>

	<div id="content-header-extra">
		<ul>
			<?php if (isset($this->id) && $this->id != 0) : ?>
				<li>
					<a href="<?php echo Route::url('index.php?option=com_newsletter&id=' . $this->id . '&task=output'); ?>" class="btn icon-file">
						<?php echo Lang::txt('COM_NEWSLETTER_VIEW_SAVEASPDF'); ?>
					</a>
				</li>
			<?php endif; ?>
			<li>
				<a href="<?php echo Route::url('index.php?option=com_newsletter&task=subscribe'); ?>" class="btn icon-feed">
					<?php echo Lang::txt('COM_NEWSLETTER_VIEW_SUBSCRIBE_TO_MAILINGLISTS'); ?>
				</a>
			</li>
		</ul>
	</div>
</header>

<section class="main section">
	<div class="section-inner hz-layout-with-aside">
		<div class="subject newsletter">
			<?php
			if ($this->getError())
			{
				echo '<p class="error">' . $this->getError() . '</p>';
			}
			?>

			<h3><?php echo $this->escape($this->title); ?></h3>

			<?php if ($this->newsletter != '') : ?>
				<div class="container">
					<iframe id="newsletter-iframe" width="100%" height="0" title="<?php echo $this->escape($this->title); ?>" src="<?php echo Route::url('index.php?option=com_newsletter&id=' . $this->id . '&no_html=1'); ?>"></iframe>
				</div>
			<?php else : ?>
				<p class="info">
					<?php echo Lang::txt('COM_NEWSLETTER_VIEW_NO_NEWSLETTERS'); ?>
				</p>
			<?php endif; ?>
		</div><!-- /.subject -->
		<aside class="aside">
		<div class="container">
			<h3><?php echo Lang::txt('COM_NEWSLETTER_VIEW_PAST_NEWSLETTERS'); ?></h3>
			<ul>
				<?php foreach ($this->newsletters as $newsletter) : ?>
					<?php if ($newsletter->published) : ?>
						<li>
							<a class="<?php if ($this->id == $newsletter->id) { echo "active"; } ?>" href="<?php echo Route::url('index.php?option=com_newsletter&id=' . $newsletter->id); ?>">
								<?php echo $newsletter->name; ?>
							</a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="container">
			<h3><?php echo Lang::txt('COM_NEWSLETTER_VIEW_NEWSLETTER_HELP'); ?></h3>
			<ul>
				<li>
					<a class="popup" href="<?php echo Route::url('index.php?option=com_help&component=newsletter&page=index'); ?>">
						<?php echo Lang::txt('COM_NEWSLETTER_VIEW_NEWSLETTER_HELP'); ?>
					</a>
				</li>
			</ul>
		</div>
	</aside><!-- /.aside -->
	</div>
</section><!-- /.main .section -->