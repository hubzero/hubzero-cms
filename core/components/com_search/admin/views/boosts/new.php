<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SEARCH_HEADING_BOOST_NEW'));
Toolbar::apply('create');
Toolbar::cancel('list');

$action = "index.php?option=$this->option&controller=$this->controller";
$boost = $this->boost;
$typeOptions = $this->typeOptions;
sort($typeOptions);
?>

<form action="<?php echo $action; ?>"
	method="post"
	name="adminForm"
	id="adminForm">

	<div class="grid">
		<div class="col span7">
			<?php
				$this->view('_boost_details_form')
					->set('boost', $boost)
					->set('typeOptions', $typeOptions)
					->display();
			?>
		</div>
	</div>

	<input type="hidden" name="option" value="com_search" />
	<input type="hidden" name="controller" value="boosts" />
	<input type="hidden" name="task" value="new" />
</form>
