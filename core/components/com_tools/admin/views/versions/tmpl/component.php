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

Html::behavior('modal');

$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();
?>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('a.edit-asset').on('click', function(e) {
		e.preventDefault();

		window.parent.$.fancybox.open($(this).attr('href'), {type: 'iframe', size: {x: 570, y: 550}});
	});
});
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="4" style="text-align:right;">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=addZone&version=' . $this->version . '&tmpl=component'); ?>" class="button edit-asset" rel="{type: 'iframe', size: {x: 570, y: 550}}"><?php echo Lang::txt('COM_TOOLS_ADD_ZONE'); ?></a>
				</th>
			</tr>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_TOOLS_COL_ZONE_NAME'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_TOOLS_COL_PUBLISH_UP'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_TOOLS_COL_PUBLISH_DOWN'); ?></th>
				<th scope="col">X</th>
			</tr>
		</thead>
		<tbody>
<?php
$i = 0;
$k = 0;
foreach ($this->rows as $row)
{
	// Grab the zone name
	$zone = new \Components\Tools\Tables\Zones($mwdb);
	$zone->load($row->zone_id);
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<a class="edit-asset" rel="{handler: 'iframe', size: {x: 570, y: 550}}" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=editZone&id=' . $row->id . '&tmpl=component'); ?>">
						<?php echo $this->escape(stripslashes($zone->title)); ?>
					</a>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->publish_up)); ?>
				</td>
				<td>
					<span><?php echo $this->escape(stripslashes($row->publish_down)); ?></span>
				</td>
				<td>
					<a class="state trash" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=removeZone&id=' . $row->id . '&version=' . $this->version . '&tmpl=component&' . Session::getFormToken() . '=1'); ?>">
						<span><?php echo Lang::txt('X'); ?></span>
					</a>
				</td>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" id="task" value="edit" />

	<?php echo Html::input('token'); ?>
</form>