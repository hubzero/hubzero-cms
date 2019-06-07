<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_MEMBERS_PENDING_APPROVAL'); ?></h2>
</header>

<section class="main section">
	<p>
		<?php echo Lang::txt('COM_MEMBERS_PENDING_APPROVAL_MESSAGE', Route::url('index.php?option=com_support&task=new')); ?>
	</p>
</section>