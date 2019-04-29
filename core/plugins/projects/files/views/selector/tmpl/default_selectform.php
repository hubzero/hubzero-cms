<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Components\Projects\Models\Orm\Project;
?>
	<form id="select-form" class="select-form" method="post" enctype="multipart/form-data" action="">
		<fieldset>
			<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />
			<input type="hidden" id="selecteditems" name="selecteditems" value="" />
			<input type="hidden" id="filterUrl" name="filterUrl" value="<?php echo $this->filterUrl;?>" />
			<?php foreach ($this->hiddenFields as $field): ?>
				<input type="hidden" name="<?php echo $field['name'];?>" value="<?php echo $field['value'];?>" 
					<?php echo !empty($field['value']) ? 'id="' . $field['value'] . '"' : '';?> />
			<?php endforeach; ?>
		</fieldset>

		<p class="requirement" id="req"><?php echo $this->req; ?></p>

		<?php if ($this->showCons) : ?>
			<p class="info connection-message"><?php echo Lang::txt('PLG_PROJECTS_FILES_CONNECTION_NOTE'); ?></p>
		<?php endif; ?>

		<div id="content-selector" class="content-selector">
			<?php
				if ($this->showCons && empty($this->directory) && !Request::getInt('cid'))
				{
					echo $this->connections;
				}
				else
				{
					// Show files
					$view = new \Hubzero\Plugin\View(
						array(
							'folder'  => 'projects',
							'element' => 'files',
							'name'    => 'selector',
							'layout'  => 'selector'
						)
					);
					$view->option       = $this->option;
					$view->model        = $this->model;
					$view->items        = $this->items;
					$view->requirements = $params;
					$view->publication  = $this->publication;
					$view->selected     = $selected;
					$view->allowed      = $allowed;
					$view->used         = $used;

					echo $view->loadTemplate();
				}
			?>
		</div>
	</form>
