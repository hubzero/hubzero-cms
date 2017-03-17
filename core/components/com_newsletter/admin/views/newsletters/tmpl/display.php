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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Newsletter\Helpers\Permissions::getActions('newsletter');

//set title
Toolbar::title(Lang::txt('COM_NEWSLETTER'), 'newsletter');

if ($canDo->get('core.create'))
{
	Toolbar::addNew();
	Toolbar::custom('duplicate', 'copy', '', 'COM_NEWSLETTER_TOOLBAR_COPY');
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList('COM_NEWSLETTER_DELETE_CHECK', 'delete');
	Toolbar::spacer();
}
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
	Toolbar::spacer();
}
Toolbar::custom('preview', 'preview', '', 'COM_NEWSLETTER_TOOLBAR_PREVIEW');
Toolbar::custom('sendtest', 'sendtest', '', 'COM_NEWSLETTER_TOOLBAR_SEND_TEST');
Toolbar::custom('sendnewsletter', 'send', '', 'COM_NEWSLETTER_TOOLBAR_SEND');
if ($canDo->get('core.admin'))
{
	Toolbar::spacer();
	Toolbar::preferences($this->option, '550');
}

Html::behavior('modal');

// add js
$this->js();
?>

<script type="text/javascript">

Joomla.submitbutton = function(pressbutton)
{
	if (pressbutton == 'preview')
	{
		var id = '',
			ids = document.getElementsByName('id[]');
		for (var i=0; i< ids.length;i++)
		{
			if (id == '' && ids[i].type == 'checkbox' && ids[i].checked)
			{
				id = parseInt(ids[i].value);
			}
		}

		HUB.Administrator.Newsletter.newsletterPreview( id );
		return;
	}
	submitform( pressbutton );
}
</script>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_NAME'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_FORMAT'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATE'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_PUBLIC'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SENT'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_TRACKING'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ($this->rows->count() > 0) : ?>
				<?php foreach ($this->rows as $k => $newsletter) : ?>
					<tr>
						<td>
							<input type="checkbox" name="id[]" id="cb<?php echo $k; ?>" value="<?php echo $newsletter->id; ?>" onclick="isChecked(this.checked);" />
						</td>
						<td>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $newsletter->id); ?>">
								<?php echo $this->escape($newsletter->name); ?>
							</a>
						</td>
						<td class="priority-3">
							<?php echo ($newsletter->type == 'html') ? Lang::txt('COM_NEWSLETTER_FORMAT_HTML') : Lang::txt('COM_NEWSLETTER_FORMAT_PLAIN'); ?>
						</td>
						<td class="priority-4">
							<?php
								$activeTemplate = '';
								if ($newsletter->template == '-1')
								{
									$activeTemplate = Lang::txt('COM_NEWSLETTER_NO_TEMPLATE');
								}
								else
								{
									$activeTemplate = $newsletter->template->name;
								}

								echo ($activeTemplate) ? $activeTemplate : Lang::txt('COM_NEWSLETTER_NO_TEMPLATE_FOUND');
							?>
						</td>
						<td class="priority-2">
							<?php if ($newsletter->published) : ?>
								<span class="state yes"><span><?php echo Lang::txt('JYES'); ?></span></span>
							<?php else : ?>
								<span class="state no"><span><?php echo Lang::txt('JNO'); ?></span></span>
							<?php endif; ?>
						</td>
						<td>
							<?php if ($newsletter->sent) : ?>
								<span class="state yes"><span><?php echo Lang::txt('JYES'); ?></span></span>
							<?php else : ?>
								<span class="state no"><span><?php echo Lang::txt('JNO'); ?></span></span>
							<?php endif; ?>
						</td>
						<td class="priority-3">
							<?php if ($newsletter->tracking) : ?>
								<span class="state yes"><span><?php echo Lang::txt('JYES'); ?></span></span>
							<?php else : ?>
								<span class="state no"><span><?php echo Lang::txt('JNO'); ?></span></span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="7">
						<?php echo Lang::txt('COM_NEWSLETTER_NO_NEWSLETTER'); ?>
						<a onclick="javascript:submitbutton('add');" href="#"><?php echo Lang::txt('COM_NEWSLETTER_CREATE_NEWSLETTER'); ?></a>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="add" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>