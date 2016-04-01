<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$base = Request::getVar('REQUEST_URI', rtrim(Request::base(true), '/'), 'server');

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
			<div class="quote">
				<?php if (isset($this->filters['id']) && $this->filters['id'] != '') { ?>
					<div class="breadcrumbs">
						<p>
							<a href="<?php echo rtrim(str_replace('quoteid=' . $this->filters['id'], '', $base), '?'); ?>" class="breadcrumbs"><?php echo Lang::txt('MOD_QUOTES_NOTABLE_QUOTES'); ?></a>
							&rsaquo;
							<strong><?php echo $this->escape(stripslashes($quote->fullname)); ?></strong>
						</p>
					</div>
				<?php } ?>
				<blockquote cite="<?php echo $this->escape(stripslashes($quote->fullname)); ?>">
					<?php if (isset($this->filters['id']) && $this->filters['id'] != '') { ?>
						<?php echo stripslashes($quote->quote); ?>
					<?php } else { ?>
						<p>
							<?php
							if (!trim($quote->short_quote))
							{
								$quote->short_quote = \Hubzero\Utility\String::truncate($quote->quote, 250);
							}
							$quote->short_quote = html_entity_decode(stripslashes($quote->short_quote));
							$quote->short_quote = strip_tags($quote->short_quote);
							?>
							<?php if ($quote->short_quote != $quote->quote) { ?>
								<?php echo $this->escape(rtrim($quote->short_quote, '.')); ?>
								 &#8230;
								<a href="<?php echo $base . (strstr($base, '?') ? '&amp;' : '?'); ?>quoteid=<?php echo $quote->id; ?>" title="<?php echo Lang::txt('MOD_QUOTES_VIEW_QUOTE_BY', $this->escape(stripslashes($quote->fullname))); ?>">
									<?php echo Lang::txt('MOD_QUOTES_MORE'); ?>
								</a>
							<?php } else { ?>
								<?php echo $this->escape($quote->short_quote); ?>
							<?php } ?>
						</p>
					<?php } ?>
				</blockquote>
				<p class="cite">
					<?php
					$user = $quote->user_id ? \Hubzero\User\Profile::getInstance($quote->user_id) : new \Hubzero\User\Profile();
					$userPicture = $user ? $user->getPicture() : $user->getPicture(true);
					echo '<img src="' . $userPicture . '" alt="' . $quote->fullname . '" width="40" height="40" />';
					?>
					<cite><?php echo $this->escape(stripslashes($quote->fullname)); ?> <span><?php echo $this->escape(stripslashes($quote->org)); ?></span></cite>
				</p>
				<?php
				if (is_dir(PATH_APP . DS . $this->path . $quote->id))
				{
					$pictures = scandir(PATH_APP . DS .$this->path . $quote->id);
					array_shift($pictures);
					array_shift($pictures);

					foreach ($pictures as $picture)
					{
						$file = PATH_APP . DS . $this->path . $quote->id . DS . $picture;
						if (file_exists($file))
						{
							$this_size = filesize($file);

							list($ow, $oh, $type, $attr) = getimagesize($file);

							// scale if image is bigger than 120w x120h
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
							?>
							<a class="fancybox-inline" href="<?php echo $this->path . $quote->id . DS . $picture; ?>">
								<img  src="<?php echo $this->path . $quote->id . DS . $picture . '" height="' . $mh . '" width="' . $mw; ?>" alt="" />
							</a>
							<?php
						}
					}
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
jQuery(document).ready(function() {
	$('.fancybox-inline').fancybox();
	$('#shuffle').cycle({
		fx: '<?php echo $this->params->get('cycle_fx', 'fade'); ?>',
		timeout: '<?php echo $this->params->get('cycle_speed', 1000); ?>',
	});
});
</script>