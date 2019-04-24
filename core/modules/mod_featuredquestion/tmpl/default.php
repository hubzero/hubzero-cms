<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

if ($this->getError()) { ?>
	<p class="error"><?php echo Lang::txt('MOD_FEATUREDQUESTION_MISSING_CLASS'); ?></p>
<?php } else {
	if ($this->row) {
		$name = Lang::txt('JANONYMOUS');
		if (!$this->row->get('anonymous'))
		{
			$name = $this->row->creator->get('name');
		}

		$rcount = $this->row->responses()->where('state', '<', 2)->count();
		$when = Date::of($this->row->get('created'))->relative();
	?>
	<div class="<?php echo $this->cls; ?>">
		<h3><?php echo Lang::txt('MOD_FEATUREDQUESTION'); ?></h3>
		<?php if (is_file(PATH_APP . $this->thumb)) { ?>
			<p class="featured-img">
				<a href="<?php echo Route::url($this->row->link()); ?>">
					<img width="50" height="50" src="<?php echo $this->thumb; ?>" alt="" />
				</a>
			</p>
		<?php } ?>
		<p>
			<a href="<?php echo Route::url($this->row->link()); ?>">
				<?php echo $this->escape(strip_tags($this->row->subject)); ?>
			</a>
			<?php if ($this->row->get('question')) { ?>
				: <?php echo \Hubzero\Utility\Str::truncate($this->escape(strip_tags($this->row->question)), $this->txt_length); ?>
			<?php } ?>
			<br />
			<span><?php echo Lang::txt('MOD_FEATUREDQUESTION_ASKED_BY', $name); ?></span> -
			<span><?php echo Lang::txt('MOD_FEATUREDQUESTION_AGO', $when); ?></span> -
			<span><?php echo ($rcount == 1) ? Lang::txt('MOD_FEATUREDQUESTION_RESPONSE', $rcount) : Lang::txt('MOD_FEATUREDQUESTION_RESPONSES', $rcount); ?></span>
		</p>
	</div>
	<?php
	}
}
