<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$toolbarElements = [
	'title' => [Lang::txt('COM_SEARCH_HEADING_BOOSTS')],
	'addNew' => ['new'],
	'spacer' => [],
	'preferences' => [$this->option, '550']
];

$this->view('_toolbar', 'shared')
	->set('elements', $toolbarElements)
	->display();

$boosts = $this->boosts;

$this->view('_submenu', 'shared')
	->display();
?>

<form action="<?php echo "index.php?option=$this->option&controller=$this->controller"; ?>"
	method="post"
	name="adminForm"
	id="adminForm">

	<?php
		$this->view('_boosts_list')
			->set('boosts', $boosts)
			->display();
	?>

	<input type="hidden" name="option" value="com_search" />
	<input type="hidden" name="controller" value="boosts" />
	<input type="hidden" name="task" value="list" />
</form>
