<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js('quotes.js');

$base = rtrim(Request::base(true), '/');
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_FEEDBACK'); ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-add btn add" href="<?php echo Route::url('index.php?option=com_feedback&task=success_story'); ?>">
				<?php echo Lang::txt('COM_FEEDBACK_ADD_YOUR_STORY'); ?>
			</a>
		</p>
	</div>
</header><!-- / #content-header -->

<section class="main section">
	<div class="section-inner">
		<?php if ($this->quotes->count() > 0) { ?>
			<?php foreach ($this->quotes as $quote) { ?>
				<div class="quote" id="<?php echo $quote->get('id'); ?>">
					<div class="grid">
						<div class="col span2 omega">
							<p class="cite">
								<?php
								if (!$quote->get('short_quote'))
								{
									$quote->set('short_quote', $quote->get('quote'));
								}
								$quote->set('org', str_replace('<br>', '<br />', $quote->get('org')));
								$user = $quote->user;
								echo '<img src="' . $user->picture() . '" alt="' . $this->escape($user->get('name')) . '" width="50" height="50" /><br />';
								?>
								<cite><?php echo $this->escape(stripslashes($quote->get('fullname'))); ?></cite>
								<br /><?php echo $this->escape(stripslashes($quote->get('org'))); ?>
							</p>
						</div>
						<div class="col span10 omega">
						<?php if (isset($this->filters['id']) && $this->filters['id'] != '') { ?>
							<div class="breadcrumbs">
								<p>
									<a href="<?php echo $base; ?>/about/quotes" class="breadcrumbs"><?php echo Lang::txt('MOD_QUOTES_NOTABLE_QUOTES'); ?></a>
									&rsaquo;
									<strong><?php echo $this->escape(stripslashes($quote->get('fullname'))); ?></strong>
								</p>
							</div>
							<blockquote cite="<?php echo $this->escape(stripslashes($quote->get('fullname'))); ?>">
								<p>
									<?php echo $this->escape(stripslashes($quote->get('quote'))); ?>
								</p>
							</blockquote>
						<?php } else { ?>
							<?php if ($quote->get('short_quote') != $quote->get('quote')) { ?>
								<div class="quote-short" id="<?php echo $quote->get('id'); ?>-short">
									<blockquote cite="<?php echo $this->escape(stripslashes($quote->get('fullname'))); ?>">
										<p>
											<?php
											$content = rtrim(stripslashes($quote->get('short_quote')), '.');
											echo $this->escape(strip_tags($content)); ?>
											&#8230;
											<a href="#" id="<?php echo $quote->id; ?>" class="show-more" title="<?php echo Lang::txt('MOD_QUOTES_VIEW_QUOTE_BY', $this->escape(stripslashes($quote->get('fullname')))); ?>">
												<?php echo Lang::txt('COM_FEEDBACK_MORE'); ?>
											</a>
										</p>
									</blockquote>
								</div>
								<div class="quote-long hide" id="<?php echo $quote->id; ?>-long">
									<blockquote cite="<?php echo $this->escape(stripslashes($quote->get('fullname'))); ?>">
										<?php echo stripslashes($quote->get('quote')); ?>
									</blockquote>
								</div>
							<?php } else { ?>
								<blockquote cite="<?php echo $this->escape(stripslashes($quote->get('fullname'))); ?>">
									<?php echo stripslashes($quote->get('short_quote')); ?>
								</blockquote>
							<?php } ?>
						<?php } ?>
						<?php
						$pictures = $quote->files();

						foreach ($pictures as $picture)
						{
							list($ow, $oh, $type, $attr) = getimagesize($picture->getPathname());

							// Scale if image is bigger than 120w x120h
							$num = max($ow/120, $oh/120);
							if ($num > 1)
							{
								$mw = round($ow/$num);
								$mh = round($oh/$num);
							}
							else
							{
								$mw = $ow;
								$mh = $oh;
							}

							$img = substr($picture->getPathname(), strlen(PATH_ROOT));

							echo '<a class="fancybox-inline" href="' . $img . '">';
							echo '<img  src="' . $img . '" height="' . $mh . '" width="' . $mw . '" alt="" />';
							echo '</a>';
						}
						?>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } else { ?>
			<p><?php echo Lang::txt('COM_FEEDBACK_NO_QUOTES_FOUND'); ?></p>
		<?php } ?>

		<input type="hidden" id="quoteid" name="quoteid" value="<?php echo $this->quoteId; ?>" />
	</div>
</section>
