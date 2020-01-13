<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$action = $this->action;
$page = $this->page;
$submitValue = $this->submitValue;
?>

<form id="hubForm" class="full" method="post" action="<?php echo $action; ?>">

	<?php
		$this->view('_page_info_fields')
			->set('page', $page)
			->display();
	?>

	<div class="row button-container">
		<input type="submit" class="btn btn-success" value="<?php echo $submitValue; ?>">
	</div>

</form>
