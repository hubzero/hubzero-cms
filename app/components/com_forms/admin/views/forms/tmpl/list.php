<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$component = $this->option;
$controller = $this->controller;
$permissions = $this->permissions;
$toolbarTitle = $this->title;

Toolbar::title($toolbarTitle);

if ($permissions->get('core.manage'))
{
	Toolbar::preferences($component, '550');
}

?>

<form action="<?php echo 'TODO'; ?>" method="post" name="adminForm">

	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo ''; ?>" placeholder="..." />

		<input type="submit" value="<?php echo "Search"; ?>" />
		<button id="clear-search" type="button">
			<?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?>
		</button>
	</fieldset>

	<?php echo Html::input('token'); ?>

	<!-- Filtering dependencies -->
	<!-- Toolbar dependencies -->
	<input type="hidden" name="controller" value="<?php echo $controller; ?>" />
	<input type="hidden" name="option" value="<?php echo $component ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" />

</form>
