<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

//include modal
Html::behavior('modal');

//set title
Toolbar::title(Lang::txt('COM_NEWSLETTER'), 'newsletter.png');

//add buttons to toolbar
Toolbar::addNew();
Toolbar::editList();
Toolbar::custom('duplicate', 'copy', '', 'COM_NEWSLETTER_TOOLBAR_COPY');
Toolbar::deleteList('COM_NEWSLETTER_DELETE_CHECK', 'delete');
Toolbar::spacer();
Toolbar::publishList();
Toolbar::unpublishList();
Toolbar::spacer();
Toolbar::custom('preview', 'preview', '', 'COM_NEWSLETTER_TOOLBAR_PREVIEW');
Toolbar::custom('sendtest', 'sendtest', '', 'COM_NEWSLETTER_TOOLBAR_SEND_TEST');
Toolbar::custom('sendnewsletter', 'send', '', 'COM_NEWSLETTER_TOOLBAR_SEND');
Toolbar::spacer();
Toolbar::preferences($this->option, '550');

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
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->newsletters); ?>);" /></th>
				<th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_NAME'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_FORMAT'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATE'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_PUBLIC'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_SENT'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_TRACKING'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if (count($this->newsletters) > 0) : ?>
				<?php foreach ($this->newsletters as $k => $newsletter) : ?>
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
									foreach ($this->templates as $template)
									{
										if ($template->id == $newsletter->template)
										{
											$activeTemplate  = $template->name;
										}
									}
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