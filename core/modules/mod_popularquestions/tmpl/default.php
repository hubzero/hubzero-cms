<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<div<?php echo ($this->cssId) ? ' id="' . $this->cssId . '"' : '';
	echo ($this->cssClass) ? ' class="' . $this->cssClass . '"' : ''; ?>>
	<?php if (count($this->rows) > 0) { ?>
		<ul class="questions">
		<?php
		foreach ($this->rows as $row)
		{
			$name = Lang::txt('JANONYMOUS');
			if (!$row->get('anonymous'))
			{
				$name = $row->creator()->get('name');
			}
			$rcount = $row->responses()->where('state', '<', 2)->count();
			?>
			<li>
				<?php if ($this->style == 'compact') { ?>
					<a href="<?php echo Route::url($row->link()); ?>"><?php echo $this->escape(strip_tags($row->subject)); ?></a>
				<?php } else { ?>
					<h4><a href="<?php echo Route::url($row->link()); ?>"><?php echo $this->escape(strip_tags($row->subject)); ?></a></h4>
					<p class="entry-details">
						<?php echo Lang::txt('MOD_POPULARQUESTIONS_ASKED_BY', $this->escape($name)); ?> @
						<span class="entry-time"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('time'); ?></time></span> on
						<span class="entry-date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time></span>
						<span class="entry-details-divider">&bull;</span>
						<span class="entry-comments">
							<a href="<?php echo Route::url($row->link() . '#answers'); ?>" title="<?php echo Lang::txt('MOD_RECENTQUESTIONS_RESPONSES', $rcount); ?>">
								<?php echo $rcount; ?>
							</a>
						</span>
					</p>
					<p class="entry-tags"><?php echo Lang::txt('MOD_POPULARQUESTIONS_TAGS'); ?>:</p>
					<?php echo $row->tags('cloud'); ?>
				<?php } ?>
			</li>
			<?php
		}
		?>
		</ul>
	<?php } else { ?>
		<p><?php echo Lang::txt('MOD_POPULARQUESTIONS_NO_RESULTS'); ?></p>
	<?php } ?>
</div>