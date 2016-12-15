<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('Solr Search: '. $this->type . ' Documents' ));
Toolbar::back();
Toolbar::preferences($this->option, '550');
$this->css('solr');

\Submenu::addEntry(
	Lang::txt('Overview'),
	'index.php?option='.$this->option.'&task=configure'
);
\Submenu::addEntry(
	Lang::txt('Search Index'),
	'index.php?option='.$this->option.'&task=searchindex'
);
\Submenu::addEntry(
	Lang::txt('Index Blacklist'),
	'index.php?option='.$this->option.'&task=manageBlacklist'
);
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span6 rtl">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
				<input type="text" name="filter" id="filter_search" value="<?php echo $this->escape($this->filter); ?>" placeholder="<?php echo Lang::txt('COM_SEARCH_FILTER_SEARCH_PLACEHOLDER'); ?>" />
				<input type="submit" value="<?php echo Lang::txt('COM_SEARCH_GO'); ?>" />
				<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
		</div>
	</fieldset>

	<?php 
	$this->view('_recordtable')
		->set('documents', $this->documents)
		->set('type', $this->type)
		->set('blacklist', $this->blacklist)
		->set('pagination', $this->pagination->render())
		->display();
	?>



	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
	<input type="hidden" name="filter_order" value="<?php //echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php //echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>
