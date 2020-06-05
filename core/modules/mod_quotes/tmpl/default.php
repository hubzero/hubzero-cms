<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$base = filter_var(Hubzero\Utility\Uri::getInstance()->toString(), FILTER_SANITIZE_URL);

?>
<?php if ($this->params->get('button', 0) == 1) { ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li>
				<a href="<?php echo Route::url('index.php?option=com_feedback&task=success_story'); ?>" class="icon-add btn add">
					<?php echo Lang::txt('MOD_QUOTES_ADD_YOUR_STORY'); ?>
				</a>
			</li>
		</ul>
	</div>
<?php } ?>

<div id="quotes-container">
	<?php if ($this->params->get('cycle', 0) == 1) { ?>
		<div id="shuffle">
	<?php } ?>
	<?php if (count($this->quotes) > 0) { ?>
		<?php foreach ($this->quotes as $quote) { ?>
			<?php
				$fullQuote = $this->escape((stripslashes($quote->get('quote'))));
				$fullQuote = preg_replace("/&lt;br ?\/&gt;|\n/", "<br/>", $fullQuote);
			 ?>
			<div class="quote">
				<?php if (isset($this->filters['id']) && $this->filters['id'] != '') { ?>
					<div class="breadcrumbs">
						<p>
							<a href="<?php echo rtrim(str_replace('quoteid=' . $this->filters['id'], '', $base), '?'); ?>" class="breadcrumbs"><?php echo Lang::txt('MOD_QUOTES_NOTABLE_QUOTES'); ?></a>
							&rsaquo;
							<strong><?php echo $this->escape(stripslashes($quote->get('fullname'))); ?></strong>
						</p>
					</div>
				<?php } ?>
				<blockquote cite="<?php echo $this->escape(stripslashes($quote->get('fullname'))); ?>">
					<?php if (isset($this->filters['id']) && $this->filters['id'] != '') { ?>
						<?php echo $fullQuote; ?>
					<?php } else { ?>
						<p>
							<?php
							if (!trim($quote->get('short_quote')))
							{
								$quote->set('short_quote', Hubzero\Utility\Str::truncate($quote->get('quote'), 250));
							}
							$quote->set('short_quote', html_entity_decode(stripslashes($quote->get('short_quote'))));
							$quote->set('short_quote', strip_tags($quote->get('short_quote')));
							?>
							<?php if ($quote->get('short_quote') != $quote->get('quote')) { ?>
								<?php echo $this->escape(rtrim($quote->get('short_quote'), '.')); ?>
								 &#8230;
								<a href="<?php echo $base . (strstr($base, '?') ? '&amp;' : '?'); ?>quoteid=<?php echo $quote->get('id'); ?>" title="<?php echo Lang::txt('MOD_QUOTES_VIEW_QUOTE_BY', $this->escape(stripslashes($quote->get('fullname')))); ?>">
									<?php echo Lang::txt('MOD_QUOTES_MORE'); ?>
								</a>
							<?php } else { ?>
								<?php echo $this->escape($quote->get('short_quote')); ?>
							<?php } ?>
						</p>
					<?php } ?>
				</blockquote>
				<p class="cite">
					<?php
					$user = $quote->user;
					$userPicture = $user->picture();
					echo '<img src="' . $userPicture . '" alt="' . $this->escape($quote->get('fullname')) . '" width="40" height="40" />';
					?>
					<cite><?php echo $this->escape(stripslashes($quote->get('fullname'))); ?> <span><?php echo $this->escape(stripslashes($quote->get('org'))); ?></span></cite>
				</p>
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
				<?php if ($this->params->get('cycle', 0) == 0) { ?>
					<hr />
				<?php } ?>
			</div>
		<?php } ?>
	<?php } else { ?>
		<p><?php echo Lang::txt('MOD_QUOTES_NO_QUOTES_FOUND'); ?></p>
	<?php } ?>
	<?php if ($this->params->get('cycle', 0) == 1) { ?>
		</div>
	<?php } ?>
</div>
<script type="text/javascript">
jQuery(document).ready(function($) {
	$('.fancybox-inline').fancybox();
	$('#shuffle').cycle({
		fx: '<?php echo $this->params->get('cycle_fx', 'fade'); ?>',
		timeout: '<?php echo $this->params->get('cycle_speed', 1000); ?>',
	});
});
</script>
