<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$this->css();
?>

<div class="<?php echo $this->module->module; ?>">
	<?php if (count($this->unapproved) > 0) : ?>
		<div class="pending-users">
			<a href="<?php echo Route::url('index.php?option=com_members&approved=0'); ?>">
				<span class="count"><?php echo count($this->unapproved); ?></span>
				<?php echo Lang::txts('MOD_USERS_REQUIRE_APPROVAL', count($this->unapproved)); ?>
			</a>
		</div>
	<?php else : ?>
		<div class="none"><?php echo Lang::txt('MOD_USERS_ALL_CLEAR'); ?></div>
	<?php endif; ?>
</div>