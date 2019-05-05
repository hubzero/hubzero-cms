<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('Solr Search Indexed Documents' ));
Toolbar::back();
Toolbar::preferences($this->option, '550');
$this->css('solr');

Submenu::addEntry(
	Lang::txt('Overview'),
	'index.php?option=' . $this->option . '&task=configure'
);
Submenu::addEntry(
	Lang::txt('Searchable Components'),
	'index.php?option=' . $this->option . '&task=display&controller=searchable',
	true
);
Submenu::addEntry(
	Lang::txt('Index Blacklist'),
	'index.php?option='.$this->option.'&task=manageBlacklist'
);
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="filter" id="filter_search" value="<?php echo $this->escape($this->filter); ?>" placeholder="<?php echo Lang::txt('COM_SEARCH_FILTER_SEARCH_PLACEHOLDER'); ?>" />
		<input type="submit" value="<?php echo Lang::txt('COM_SEARCH_GO'); ?>" />
	</fieldset>

	<?php 
	$this->view('_recordtable')
		->set('documents', $this->documents)
		->set('blacklist', $this->blacklist)
		->set('pagination', $this->pagination)
		->set('facet', $this->facet)
		->display();
	?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="facet" value="<?php echo $this->facet; ?>" /> 

	<?php echo Html::input('token'); ?>
</form>
