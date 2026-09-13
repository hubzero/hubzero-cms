<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<div class="karma-profile">

	<h3><?php echo Lang::txt('PLG_MEMBERS_KARMA'); ?></h3>

<?php if (!$this->scales) { ?>
	<p><?php echo Lang::txt('PLG_MEMBERS_KARMA_NOTHING'); ?></p>
<?php } else { ?>
	<dl class="karma-scales">
<?php foreach ($this->scales as $entry) { ?>
		<dt><?php echo $this->escape($entry['scale']->get('title')); ?></dt>
		<dd><?php echo $this->escape($entry['value']); ?></dd>
<?php } ?>
	</dl>
<?php } ?>

<?php if ($this->isSelf) { ?>
	<?php if ($this->standing) { ?>
	<h4><?php echo Lang::txt('PLG_MEMBERS_KARMA_STANDING'); ?></h4>
	<dl class="karma-standing">
	<?php foreach ($this->standing as $alias => $value) { ?>
		<dt><?php echo $this->escape($alias); ?></dt>
		<dd><?php echo $this->escape(is_null($value) ? Lang::txt('PLG_MEMBERS_KARMA_UNSET') : $value); ?></dd>
	<?php } ?>
	</dl>
	<?php } ?>

	<p><a href="<?php echo Route::url('index.php?option=com_karma'); ?>"><?php echo Lang::txt('PLG_MEMBERS_KARMA_MANAGE'); ?></a></p>
<?php } ?>

</div>
