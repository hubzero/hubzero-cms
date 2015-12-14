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

Toolbar::title(Lang::txt('Solr Search: Overview'));
Toolbar::custom('saveHubType', 'save', '', 'Add new HubType', false);
Toolbar::cancel();

$this->css('solr');
$option = $this->option;

\Submenu::addEntry(
	Lang::txt('Overview'),
	'index.php?option='.$option.'&task=configure'
);
\Submenu::addEntry(
	Lang::txt('Search Index'),
	'index.php?option='.$option.'&task=searchindex'
);
\Submenu::addEntry(
	Lang::txt('Index Blacklist'),
	'index.php?option='.$option.'&task=manageBlacklist'
);
?>
<div class="grid">
<div class="col span6">
<form name="adminForm" class="editform" id="item-form" action="<?php echo Route::url('index.php'); ?> method="post">
<fieldset class="adminform">
<div class="grid">
<legend>HubType Details</legend>
<?php foreach ($this->model as $key => $value): ?>
	<?php if ($key != 'id' && $key != 'created_by' && $key != 'created'): ?>
	<div class="col span4">
	<div class="input_wrap">
	<label for="<?php echo $key; ?>"><?php echo ucwords(str_replace('_', ' ', $key)); ?>: </label>
	<input type="text" name="<?php echo $key; ?>" placeholder="Enter <?php echo $key; ?>" />
	</div><!-- /.input_wrap -->
	</div>
	<?php endif; ?>
<?php endforeach; ?>
</div>
</fieldset>
<input type="hidden" name="task" value="saveHubType" />
<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
</form>
</div>
</div> <!-- /.grid -->


