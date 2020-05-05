<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//add styles and scripts
$this->css()
     ->js();
?>
<?php if ($this->rows->count() > 0) : ?>
	<div class="scontainer">
		<?php foreach ($this->rows as $row) : ?>
			<?php
				$this->view('item')
				     ->set('option', $this->option)
				     ->set('group', $this->group)
				     ->set('authorized', $this->authorized)
				     ->set('announcement', $row)
				     ->set('showClose', true)
				     ->display();
			?>
		<?php endforeach; ?>
	</div>
<?php endif;
