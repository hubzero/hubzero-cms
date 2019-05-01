<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$controller = Request::getCmd('controller', 'registration');
?>
<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li>
			<a<?php if ($controller == 'registration') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=com_members&controller=registration'); ?>"><?php echo Lang::txt('COM_MEMBERS_REGISTRATION_CONFIG'); ?></a>
		</li>
		<li>
			<a<?php if ($controller == 'incremental') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=com_members&controller=incremental'); ?>"><?php echo Lang::txt('COM_MEMBERS_INCREMENTAL'); ?></a>
		</li>
		<li>
			<a<?php if ($controller == 'premis') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=com_members&controller=premis'); ?>"><?php echo Lang::txt('COM_MEMBERS_PREMIS'); ?></a>
		</li>
	</ul>
</nav><!-- / .sub-navigation -->