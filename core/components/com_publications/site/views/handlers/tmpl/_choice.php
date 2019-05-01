<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
// no direct access
defined('_HZEXEC_') or die();

$class = $this->item->assigned && $this->item->active ? ' assigned' : ' unassigned';
?>

<div class="handlertype-<?php echo $this->handler->get('_name') . $class; ?>">
	<h3><?php echo $this->configs->label; ?></h3>
	<p class="manage-handler">
		<a href="<?php echo Route::url('index.php?option=com_projects&alias='
				. $this->publication->project_alias . '&active=publications&pid=' . $this->publication->id) . '?vid=' . $this->publication->version_id . '&amp;action=handler&amp;h=' . $this->handler->get('_name') . '&amp;p=' . $this->props; ?>" class="showinbox box-expanded"><?php echo ($this->item->assigned && $this->item->active) ? Lang::txt('COM_PUBLICATIONS_HANDLER_VIEW_MANAGE') : Lang::txt('COM_PUBLICATIONS_HANDLER_ACTIVATE'); ?></a>
	</p>
</div>