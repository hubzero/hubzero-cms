<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div class="projects-container">
	<?php
	foreach ($this->rows as $row):
		if ($row->get('owned_by_group') && !$row->groupOwner()):
			continue; // owner group has been deleted
		endif;

		// Display List of items
		$this->view('_item')
			->set('option', $this->option)
			->set('filters', $this->filters)
			->set('row', $row)
			->display();
	endforeach;
	?>
</div>
