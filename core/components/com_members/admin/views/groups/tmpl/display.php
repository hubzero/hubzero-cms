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
?>
<div id="groups">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller' . $this->controller . '&id=' . $this->id); ?>" method="post">
		<table>
			<tbody>
				<tr>
					<td>
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
						<input type="hidden" name="tmpl" value="component" />
						<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
						<input type="hidden" name="task" value="add" />
						<?php echo Html::input('token'); ?>

						<select name="gid" style="max-width: 15em;">
							<option value=""><?php echo Lang::txt('COM_MEMBERS_SELECT'); ?></option>
							<?php
							foreach ($this->rows as $row)
							{
								echo '<option value="' . $row->gidNumber . '">' . $row->description . ' (' . $row->cn . ')</option>' . "\n";
							}
							?>
						</select>
						<select name="tbl">
							<option value="invitees"><?php echo Lang::txt('COM_MEMBERS_GROUPS_INVITEES'); ?></option>
							<option value="applicants"><?php echo Lang::txt('COM_MEMBERS_GROUPS_APPLICANTS'); ?></option>
							<option value="members" selected="selected"><?php echo Lang::txt('COM_MEMBERS_GROUPS_MEMBERS'); ?></option>
							<option value="managers"><?php echo Lang::txt('COM_MEMBERS_GROUPS_MANAGERS'); ?></option>
						</select>

						<input type="submit" value="<?php echo Lang::txt('COM_MEMBERS_GROUPS_ADD'); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</form>

	<br />

	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller' . $this->controller . '&id=' . $this->id); ?>" method="post">
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		<input type="hidden" name="task" value="update" />

		<table class="paramlist admintable">
			<tbody>
				<?php
				$applicants = \Hubzero\User\Helper::getGroups($this->id, 'applicants');
				$invitees   = \Hubzero\User\Helper::getGroups($this->id, 'invitees');
				$members    = \Hubzero\User\Helper::getGroups($this->id, 'members');
				$managers   = \Hubzero\User\Helper::getGroups($this->id, 'managers');

				$applicants = (is_array($applicants)) ? $applicants : array();
				$invitees   = (is_array($invitees))   ? $invitees   : array();
				$members    = (is_array($members))    ? $members    : array();
				$managers   = (is_array($managers))   ? $managers   : array();

				$groups = array_merge($applicants, $invitees);
				$managerids = array();
				foreach ($managers as $manager)
				{
					$groups[] = $manager;
					$managerids[] = $manager->cn;
				}
				foreach ($members as $mem)
				{
					if (!in_array($mem->cn,$managerids))
					{
						$groups[] = $mem;
					}
				}

				$db = App::get('db');

				if (count($groups) > 0)
				{
					foreach ($groups as $group)
					{
						?>
						<tr>
							<td>
								<a href="<?php echo Route::url('index.php?option=com_groups&controller=manage&task=edit&id=' . $group->cn); ?>" target="_parent">
									<?php echo $this->escape($group->description) . ' (' . $this->escape($group->cn) . ')'; ?>
								</a>
								<?php
								$db->setQuery("SELECT * FROM `#__xgroups_memberoption` WHERE userid=" . $db->quote($this->id) . " AND gidNumber=" . $db->quote($group->gidNumber));
								$options = $db->loadObjectList();
								if ($options)
								{
									foreach ($options as $option)
									{
										?>
										<div style="padding-left:1em;">
											<label for="memberoption-<?php echo $this->escape($option->id); ?>"><?php echo $this->escape($option->optionname); ?></label>
											<input name="memberoption[<?php echo $this->escape($option->id); ?>]" id="memberoption-<?php echo $this->escape($option->id); ?>" size="3" value="<?php echo $this->escape($option->optionvalue); ?>" />
											<input type="submit" value="<?php echo Lang::txt('COM_MEMBERS_UPDATE'); ?>" />
										</div>
										<?php
									}
								}
								?>
							</td>
							<td>
								<?php
								$seen[] = $group->cn;

								if ($group->registered)
								{
									$status = Lang::txt('COM_MEMBERS_GROUPS_APPLICANT');
									if ($group->regconfirmed)
									{
										$status = Lang::txt('COM_MEMBERS_GROUPS_MEMBER');
										if ($group->manager)
										{
											$status = Lang::txt('COM_MEMBERS_GROUPS_MANAGER');
										}
									}
								}
								else
								{
									$status = Lang::txt('COM_MEMBERS_GROUPS_INVITEE');
								}
								echo $status;
								?>
							</td>
							<td>
								<a class="state trash icon-trash" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=remove&tmpl=component&id=' . $this->id . '&gid=' . $group->cn . '&' . Session::getFormToken() . '=1'); ?>">
									<span><?php echo Lang::txt('COM_MEMBERS_GROUPS_REMOVE'); ?></span>
								</a>
							</td>
						</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>

		<?php echo Html::input('token'); ?>
	</form>
</div>