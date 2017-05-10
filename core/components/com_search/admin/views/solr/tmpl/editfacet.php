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

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('Solr Search: Edit Facet'));
Toolbar::spacer();

Toolbar::custom('savefacet', 'save', 'savefacet', 'COM_SEARCH_SAVE_FACET', false);
Toolbar::custom('managefacets', 'cancel', 'cancel', 'COM_SEARCH_CANCEL', false);
//Toolbar::cancel();
Toolbar::spacer();

$option = $this->option;

\Submenu::addEntry(
	Lang::txt('Overview'),
	'index.php?option='.$option.'&task=configure',
	true
);
\Submenu::addEntry(
	Lang::txt('Search Index'),
	'index.php?option='.$option.'&task=searchindex'
);
\Submenu::addEntry(
	Lang::txt('Index Blacklist'),
	'index.php?option='.$option.'&task=manageBlacklist'
);
\Submenu::addEntry(
	Lang::txt('Manage Search Facets'),
	'index.php?option='.$option.'&task=manageFacets'
);
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=manageFacets'); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<!-- Name -->
				<div class="input-wrap">
					<label for="field-name"><?php echo Lang::txt('COM_SEARCH_FIELD_NAME'); ?>:</label><br />
						<input type="text" name="fields[name]" id="field-name" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->facet->name)); ?>" />
				</div> <!-- /.input-wrap -->

				<!-- Facet -->
				<div class="input-wrap">
					<label for="field-facet"><?php echo Lang::txt('COM_SEARCH_FIELD_FACET'); ?>:</label><br />
						<input type="text" name="fields[facet]" id="field-facet" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->facet->facet)); ?>" />
				</div>

				<!-- Parent -->
				<div class="input-wrap">
					<label for="field-parent"><?php echo Lang::txt('COM_SEARCH_FIELD_PARENT'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<select name="fields[parent_id]" id="field-parent">
						<option value="0"><?php echo Lang::txt('COM_SEARCH_FIELD_PARENT_SELECT'); ?></option>
						<?php
							$parents = $this->facet->toplevel();
							foreach ($parents as $parent)
							{
						?>
								<option value="<?php echo $parent->id; ?>"><?php echo $parent->name; ?></option>
						<?php
							}
						?>
					</select>
				</div> <!-- /.input-wrap -->

			</fieldset> <!-- /.adminform -->
		</div><!-- /.col span7 -->
	</div><!-- /.grid -->
	<?php echo Html::input('token'); ?>
	<input type="hidden" name="fields[protected]" value="0" />
	<input type="hidden" name="fields[id]" value="<?php echo $this->facet->id; ?>" />
	<input type="hidden" name="option" value="com_search" />
	<input type="hidden" name="controller" value="solr" />
	<input type="hidden" name="task" value="saveFacet" autocomplete="" />
	<input type="hidden" name="action" value="editfacet" autocomplete="" />
</form>

