<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (!isset($this->rows) || !count($this->rows))
{
	return;
}

?>
<div class="mod-story-latest">
	<ul class="story-latest">
<?php foreach ($this->rows as $row) { ?>
		<li>
			<a href="<?php echo Route::url($row->link()); ?>"><?php echo $this->escape($row->get('title')); ?></a>
<?php if ($this->params->get('show_kicker', 1) && $row->get('kicker')) { ?>
			<span class="story-latest-kicker"><?php echo $this->escape($row->get('kicker')); ?></span>
<?php } ?>
<?php if ($this->params->get('show_date', 1)) { ?>
			<time datetime="<?php echo $this->escape($row->get('publish_up')); ?>"><?php
				echo Date::of($row->get('publish_up'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
			?></time>
<?php } ?>
		</li>
<?php } ?>
	</ul>
</div>
