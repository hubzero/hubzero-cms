<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('boostsList');

$toolbarElements = [
	'title' => [Lang::txt('COM_SEARCH_HEADING_BOOSTS')],
	'addNew' => ['new'],
	'spacer' => [],
	'preferences' => [$this->option, '550']
];

$this->view('_toolbar', 'shared')
	->set('elements', $toolbarElements)
	->display();

$this->view('_submenu', 'shared')
	->display();

$boosts = $this->boosts;
$sortField = $this->sortField;
$sortDirection = $this->sortDirection;

$this->view('_tag_search_notice')
	->display();
?>

<form action="<?php echo "index.php?option=$this->option&controller=$this->controller"; ?>"
	method="post"
	name="adminForm"
	id="adminForm">

	<?php
		$this->view('_boosts_list')
			->set('boosts', $boosts)
			->set('sortField', $sortField)
			->set('sortDirection', $sortDirection)
			->display();
	?>

	<input type="hidden" name="option" value="com_search" />
	<input type="hidden" name="controller" value="boosts" />
	<input type="hidden" name="task" value="list" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($sortField); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($sortDirection); ?>" />
</form>
