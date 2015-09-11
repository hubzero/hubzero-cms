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

$canDo = \Components\Tags\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_TAGS') . ': ' . Lang::txt('COM_TAGS_RELATIONSHIPS'), 'tags.png');

$base = str_replace('/administrator', '', rtrim(Request::base(true), '/'));

Html::behavior('tooltip');

$this->css('tag_graph.css');
?>
<form id="tag-sel" action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="get">
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_TAGS_FIND_TAG'); ?></span></legend>
		<table class="admintable">
			<tfoot>
				<tr>
					<td colspan="3">
						<button type="submit" id="center"><?php echo Lang::txt('COM_TAGS_LOOKUP'); ?></button>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<th class="key"><label><?php echo Lang::txt('COM_TAGS_TAG'); ?>:</label></th>
					<td><input type="text" id="center-node" class="tag-entry" value="<?php echo $this->get('preload'); ?>" /></td>
					<td><?php echo Lang::txt('COM_TAGS_TAG_RELATIONSHIP'); ?></td>
				</tr>
				<tr>
					<th class="key"><?php echo Lang::txt('COM_TAGS_SHOW_RELATIONSHIPS'); ?>:</th>
					<td><label><input type="radio" name="relationship" id="hierarchical" checked="checked" /> <?php echo Lang::txt('COM_TAGS_RELATIONSHIP_HIERARCHICAL'); ?></label></td>
					<td><label><input type="radio" name="relationship" id="implicit" /> <?php echo Lang::txt('COM_TAGS_RELATIONSHIP_IMPLICIT'); ?></label></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
</form>

<fieldset class="adminform">
	<legend><span><?php echo Lang::txt('COM_TAGS_RELATIONSHIP_GRAPH'); ?></span></legend>
	<div id="graph"></div>
</fieldset>

<div id="metadata-cont">
	<div class="col width-100">
		<form id="metadata" action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_TAGS_RELATIONSHIP_METADATA'); ?></span></legend>
				<table class="admintable">
					<tfoot>
						<tr>
							<td colspan="2">
								<input type="hidden" class="tag-id" name="tag" value="" />
								<input type="hidden" value="update" name="task" />
								<button type="submit"><?php echo Lang::txt('COM_TAGS_RELATIONSHIP_UPDATE'); ?></button>
							</td>
						</tr>
					</tfoot>
					<tbody>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_TAGS_RELATIONSHIP_DESCRIPTION'); ?>:</th>
							<td><textarea cols="100" rows="4" id="description" name="description"></textarea></td>
						</tr>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_TAGS_RELATIONSHIP_LABELED'); ?>:</th>
							<td><ul id="labeled" class="textboxlist-holder act"></ul></td>
						</tr>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_TAGS_RELATIONSHIP_LABELS'); ?>:</th>
							<td><ul id="labels" class="textboxlist-holder act"></ul></td>
						</tr>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_TAGS_RELATIONSHIP_PARENTS'); ?>:</th>
							<td><ul id="parents" class="textboxlist-holder act"></ul></td>
						</tr>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_TAGS_RELATIONSHIP_CHILDREN'); ?>:</th>
							<td><ul id="children" class="textboxlist-holder act"></ul></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</form>
	</div>
	<div class="clr"></div>
</div>

<form name="adminForm" method="get" action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>">
	<input type="hidden" value="<?php echo $this->option; ?>" name="option" />
	<input type="hidden" value="<?php echo $this->controller; ?>" name="controller" />
	<input type="hidden" value="" name="task" />
	<input type="hidden" value="0" name="boxchecked" />
	<input type="hidden" name="plgAutocompleterCss" id="plgAutocompleterCss" value="<?php echo $base; ?>/core/plugins/hubzero/autocompleter/autocompleter.css" />
</form>

<script src="<?php echo $base; ?>/core/components/<?php echo $this->option; ?>/admin/assets/js/d3/d3.min.js"></script>
<script src="<?php echo $base; ?>/core/components/<?php echo $this->option; ?>/admin/assets/js/d3/d3.layout.min.js"></script>
<script src="<?php echo $base; ?>/core/components/<?php echo $this->option; ?>/admin/assets/js/d3/d3.geom.min.js"></script>
<script src="<?php echo $base; ?>/core/components/<?php echo $this->option; ?>/admin/assets/js/tag_graph.js"></script>
<script type="text/javascript">
var plgAutocompleterCss = '<?php echo $base; ?>/core/plugins/hubzero/autocompleter/autocompleter.css';
</script>
<script src="<?php echo $base; ?>/core/plugins/hubzero/autocompleter/autocompleter.js"></script>