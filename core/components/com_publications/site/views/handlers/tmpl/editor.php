<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
// no direct access
defined('_HZEXEC_') or die();

$url = Route::url($this->publication->link('edit'));

?>
<div id="abox-content" class="handler-wrap">
	<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_HANDLER') . ' - ' . $this->editor->configs->label; ?></h3>
	<?php
	// Display error  message
	if ($this->getError()) {
		echo '<p class="error">' . $this->getError() . '</p>';
	} else { // No error
	?>
	<form id="<?php echo $this->ajax ? 'hubForm-ajax' : 'plg-form'; ?>" method="post" action="<?php echo $url; ?>">
	<div id="handler-status" class="handler-status">
		<?php echo $this->editor->drawStatus(); ?>
	</div>
	<div id="handler-content" class="handler-content">
		<?php echo $this->editor->drawEditor(); ?>
	</div>
	</form>
	<?php } ?>
</div>