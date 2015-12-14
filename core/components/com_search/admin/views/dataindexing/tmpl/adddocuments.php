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
Toolbar::title(Lang::txt('Solr Search: Add Document Type - ' . $this->type));
Toolbar::back();
Toolbar::spacer();

//Toolbar::save();
Toolbar::custom('saveschema', 'save', '', 'Index data-type', false);

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

$this->css('solr');
?>
<style>
.odd-row {
	background-color: #FFFFFF;
	width: 8px; 
}

.even-row {
	width: 8px;
}

#container{
	width: 100%;
	overflow-x: scroll;
}

.resetColumn{
    font-family: "Fontcons";
		content: "\21BB";
		margin-right: auto;
		margin-left: auto;
}

</style>

<div id="container">
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="updateRepositoryForm">
<table class="searchDocument">
	<thead>
		<th>Database Fields</th>
		<th>Hub Search Document Fields</th>
	</thead>
			<?php foreach ($this->typeStructure[0]['structure'] as $name => $dbType): ?>
				<tr>
					<td><?php echo $name; ?></td>
					<td>
						<select name="input[<?php echo $name; ?>]">
						<option value=''> - </option>
					<?php foreach ($this->hubDocument as $column => $value): ?>
							<?php if ($name != 'id'): ?>
							<option value="<?php echo $column; ?>" <?php echo ($column === $name ? 'selected="selected"' : ''); ?>><?php echo $column; ?></option>
							<?php elseif ($name == 'id' && $column == 'hubID'): ?>
							<option value="<?php echo $column; ?>" <?php echo 'selected="selected"'; ?>><?php echo $column; ?></option>
							<?php endif; ?>
					<?php endforeach; ?>
					</select>
					</td>
				</tr>
			<?php endforeach; ?>
</table>

<input type="hidden" name="option" value="<?php echo $this->option ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
<input type="hidden" name="type" value="<?php echo $this->type ?>" />
<input type="hidden" name="task" value="saveschema" />

<?php echo Html::input('token'); ?>
</form>
</div><!-- /#container -->
