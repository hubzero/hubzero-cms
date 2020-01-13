<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$item = $this->item;
$created = $item->get('created');
?>

<span class="date">
	<?php
		$this->view('_date', 'shared')
			->set('date', $created)
			->set('format', 'F jS Y, g:iA')
			->display();
	?>
</span>
