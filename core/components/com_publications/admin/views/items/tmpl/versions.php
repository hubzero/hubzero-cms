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

$this->css();

Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATION_MANAGER') . ' - ' . Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ': #' . $this->pub->id . ' - ' . Lang::txt('COM_PUBLICATIONS_VERSIONS'), 'addedit.png');
Toolbar::spacer();
Toolbar::cancel();

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
<?php if ($this->config->get('enabled') == 0) { ?>
	<p class="warning"><?php echo Lang::txt('COM_PUBLICATIONS_COMPONENT_DISABLED'); ?></p>
<?php } ?>
<p class="crumbs"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_PUBLICATION_MANAGER'); ?></a> &raquo; <a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id[]=' . $this->pub->id); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ' #' . $this->pub->id; ?></a> &raquo; <?php echo Lang::txt('COM_PUBLICATIONS_VERSIONS'); ?></p>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th class="tdmini"></th>
				<th class="tdmini"><?php echo Lang::txt('COM_PUBLICATIONS_VERSION'); ?></th>
				<th><?php echo Lang::txt('COM_PUBLICATIONS_TITLE'); ?></th>
				<th><?php echo Lang::txt('COM_PUBLICATIONS_STATUS'); ?></th>
				<th><?php echo Lang::txt('COM_PUBLICATIONS_DOI'); ?></th>
				<th><?php echo ucfirst(Lang::txt('COM_PUBLICATIONS_OPTIONS')); ?></th>
			</tr>
		 </thead>
		<tbody>
		<?php
		$k = 0;
			foreach ($this->versions as $v) {
			// Get DOI
			$doi = $v->doi ? 'doi:' . $v->doi : '';
			$doi_notice = $doi ? $doi : Lang::txt('COM_PUBLICATIONS_NA');

			// Version status
			$status = $this->pub->getStatusName($v->state);
			$class  = $this->pub->getStatusCss($v->state);
			$date   = $this->pub->getStatusDate($v);

			?>
			<tr class="mini <?php if ($v->main == 1) { echo ' vprime'; } ?>">
				<td class="centeralign"><?php echo $v->version_number ? $v->version_number : ''; ?></td>
				<td><?php echo $v->version_label; ?></td>
				<td><?php echo $v->title; ?></td>
				<td class="v-status">
					<span class="<?php echo $class; ?>"><?php echo $status; ?></span>
					<?php if ($date) { echo '<span class="block faded">'.$date.'</span>';  } ?>
				</td>
				<td><?php echo $doi_notice; ?></td>
				<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id[]=' . $this->pub->id . '&version=' . $v->version_number); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_MANAGE_VERSION'); ?></a></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
