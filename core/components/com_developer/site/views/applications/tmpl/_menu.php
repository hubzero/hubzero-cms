<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$tabs = array(
	'details' => Lang::txt('COM_DEVELOPER_API_APPLICATION_TAB_DETAILS'),
	'tokens'  => Lang::txt('COM_DEVELOPER_API_APPLICATION_TAB_TOKENS')//,
	//'stats'   => Lang::txt('COM_DEVELOPER_API_APPLICATION_TAB_STATS')
);
?>

<nav class="sub-menu-cont cf">
	<ul class="sub-menu left">
		<?php foreach ($tabs as $alias => $name) : ?>
			<li class="<?php echo ($this->active == $alias) ? 'active' : ''; ?>">
				<a href="<?php echo Route::url($this->application->link() . '&active=' . $alias); ?>">
					<span><?php echo $name; ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<ul class="sub-menu right">
		<li>
			<a class="icon-settings" href="<?php echo Route::url($this->application->link('edit')); ?>">
				<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_SETTINGS'); ?>
			</a>
		</li>
	</ul>
</nav>