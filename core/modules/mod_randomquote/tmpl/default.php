<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(true), '/');

if ($this->quote)
{
	?>
	<div class="<?php echo $this->module->module; ?>"<?php if ($this->params->get('moduleid')) { echo ' id="' . $this->params->get('moduleid') . '"'; } ?>>
		<blockquote cite="<?php echo $this->escape(stripslashes($this->quote->get('fullname'))); ?>">
			<p>
				<?php
				$text = stripslashes($this->escape($this->quote->get('quote'))) . ' ';
				$text = substr($text, 0, $this->charlimit);
				$text = substr($text, 0, strrpos($text, ' '));

				echo $text;
				?>
				<?php if (strlen($this->quote->get('quote')) > $this->charlimit) { ?>
					<a href="<?php echo $base; ?>/about/quotes/?quoteid=<?php echo $this->quote->get('id'); ?>" title="<?php echo Lang::txt('MOD_RANDOMQUOTE_VIEW_FULL', $this->escape(stripslashes($this->quote->get('fullname')))); ?>" class="showfullquote">
						<?php echo Lang::txt('MOD_RANDOMQUOTE_VIEW'); ?>
					</a>
				<?php } ?>
			</p>
		</blockquote>
		<p class="cite">
			<cite><?php echo $this->escape(stripslashes($this->quote->get('fullname'))); ?></cite>,
			<?php echo $this->escape(stripslashes($this->quote->get('org'))); ?>
			<span>-</span>
			<span><?php echo Lang::txt('MOD_RANDOMQUOTE_IN', $base . '/about/quotes'); ?></span>
		</p>
	</div>
	<?php
}