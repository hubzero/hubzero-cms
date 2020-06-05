<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

$logo = $this->group->getLogo();
?>
<div id="group-owner" class="container">
	<div class="group-content">
		<h3><?php echo $this->escape(stripslashes($this->group->get('description'))); ?></h3>
		<p class="group-img">
			<img src="<?php echo $logo; ?>" width="50" alt="<?php echo Lang::txt('PLG_RESOURCES_GROUPS_IMAGE', $this->escape(stripslashes($this->group->get('description')))); ?>" />
		</p>
		<p class="group-descripion"><?php echo Lang::txt('PLG_RESOURCES_GROUPS_BELONGS_TO_GROUP', '<a href="' . Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')) . '">' . $this->escape(stripslashes($this->group->get('description'))) . '</a>'); ?></p>
	</div>
</div>