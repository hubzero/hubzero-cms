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

// define base links
$base = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->group->cn);

// define title
$text = ($this->task == 'edit' ? Lang::txt('COM_GROUPS_PAGES_EDIT_PAGE') : Lang::txt('COM_GROUPS_PAGES_NEW_PAGE'));

// create toolbar
$canDo = \Components\Groups\Helpers\Permissions::getActions('group');
Toolbar::title(Lang::txt('COM_GROUPS').': ' . $text, 'groups.png');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('page');

// include modal for raw version links
Html::behavior('modal', 'a.version', array('handler' => 'iframe', 'fullScreen'=>true));
?>

<form action="<?php echo $base; ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_DETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-title"><?php echo Lang::txt('COM_GROUPS_PAGES_TITLE'); ?>:</label><br />
					<input type="text" name="page[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->page->get('title'))); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-alias"><?php echo Lang::txt('COM_GROUPS_PAGES_ALIAS'); ?>:</label><br />
					<input type="text" name="page[alias]" id="field-alias" value="<?php echo $this->escape(stripslashes($this->page->get('alias'))); ?>" />
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_SETTINGS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-category"><?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY'); ?>:</label><br />
					<select name="page[category]" id="field-category">
						<option value=""><?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_OPTION_NULL'); ?></option>
						<?php foreach ($this->categories as $pageCategory) : ?>
							<?php $sel = ($this->page->get('category') == $pageCategory->get('id')) ? 'selected="selected"' : ''; ?>
							<option <?php echo $sel; ?> value="<?php echo $pageCategory->get('id'); ?>"><?php echo $pageCategory->get('title'); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<?php if (!$this->page->get('home')) : ?>
					<div class="input-wrap">
						<label for="field-order"><?php echo Lang::txt('COM_GROUPS_PAGES_PARENT'); ?>:</label><br />
						<select name="page[parent]" id="field-order">
							<?php if (!count($this->pages)) { ?>
							<option value="0"><?php echo Lang::txt('COM_GROUPS_PAGES_TEMPLATE_OPTION_NULL'); ?></option>
							<?php } ?>
							<?php foreach ($this->pages as $page) : ?>
								<?php if ($page->get('id') == $this->page->get('id')) { continue; } ?>
								<?php $sel = ($this->page->get('parent') == $page->get('id')) ? 'selected="selected"' : ''; ?>
								<option <?php echo $sel; ?> value="<?php echo $page->get('id'); ?>">
									<?php echo $page->heirarchyIndicator(' &ndash; ') . $page->get('title'); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				<?php endif; ?>

				<?php if ($this->group->isSuperGroup()) : ?>
					<div class="input-wrap">
						<label for="field-order"><?php echo Lang::txt('COM_GROUPS_PAGES_TEMPLATE'); ?>:</label><br />
						<select name="page[template]" id="field-order">
							<option value=""><?php echo Lang::txt('COM_GROUPS_PAGES_TEMPLATE_OPTION_NULL'); ?></option>
							<?php foreach ($this->pageTemplates as $name => $file) : ?>
								<?php
									$tmpl = str_replace('.php', '', $file);
									$sel  = ($this->page->get('template') == $tmpl) ? 'selected="selected"' : ''; ?>
								<option <?php echo $sel; ?> value="<?php echo $tmpl; ?>"><?php echo $name; ?></option>
							<?php endforeach;?>
						</select>
					</div>
				<?php endif; ?>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_ACCESS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('COM_GROUPS_PAGES_STATE'); ?>:</label><br />
					<select name="page[state]" id="field-state" <?php if ($this->page->get('home') == 1) { echo 'disabled="disabled"'; } ?>>
						<?php
						$states = array(
							1 => Lang::txt('COM_GROUPS_PAGES_STATE_PUBLISHED'),
							0 => Lang::txt('COM_GROUPS_PAGES_STATE_UNPUBLISHED'),
							2 => Lang::txt('COM_GROUPS_PAGES_STATE_DELETED')
						);

						foreach ($states as $k => $v)
						{
							$sel = ($this->page->get('state') == $k) ? 'selected="selected"' : '';
							echo '<option '.$sel.' value="'.$k.'">'.$v.'</option>';
						}
						?>
					</select>
				</div>
				<div class="input-wrap">
					<label for="field-privacy"><?php echo Lang::txt('COM_GROUPS_PAGES_PRIVACY'); ?>:</label><br />
					<?php
						$access = \Hubzero\User\Group\Helper::getPluginAccess($this->group, 'overview');
						switch ($access)
						{
							case 'anyone':     $name = 'Any HUB Visitor';      break;
							case 'registered': $name = 'Registered HUB Users'; break;
							case 'members':    $name = 'Group Members Only';   break;
						}
					?>
					<select name="page[privacy]" id="page[privacy]">
						<option value="default" <?php if ($this->page->get('privacy') == "default") { echo 'selected="selected"'; } ?>>
							<?php echo Lang::txt('COM_GROUPS_PAGES_PRIVACY_OPTION_INHERIT', $name); ?>
						</option>
						<option value="members" <?php if ($this->page->get('privacy') == "members") { echo 'selected="selected"'; } ?>>
							<?php echo Lang::txt('COM_GROUPS_PAGES_PRIVACY_OPTION_PRIVATE'); ?>
						</option>
					</select>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_CONTENT'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-content"><?php echo Lang::txt('COM_GROUPS_PAGES_CONTENT'); ?>:</label><br />
					<textarea name="pageversion[content]" id="field-content" rows="30"><?php echo $this->escape(stripslashes($this->version->get('content'))); ?></textarea>
					<input type="hidden" name="pageversion[version]" value="<?php echo $this->version->get('version'); ?>" />
				</div>
			</fieldset>
		</div>
		<div class="col span6">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_GROUPS_PAGES_OWNER'); ?></th>
						<td><?php echo $this->group->get('description'); ?></td>
					</tr>
					<?php if ($this->page->get('id')) : ?>
						<tr>
							<th><?php echo Lang::txt('COM_GROUPS_PAGES_ID'); ?></th>
							<td><?php echo $this->page->get('id'); ?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_GROUPS_PAGES_CURRENT_VERSION'); ?></th>
							<td><?php echo $this->version->get('version'); ?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_GROUPS_PAGES_CREATED'); ?></th>
							<td><?php echo Date::of($this->firstversion->get('created'))->toLocal('F j, Y @ g:ia'); ?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_GROUPS_PAGES_CREATED_BY'); ?></th>
							<td>
								<?php
									$profile = User::getInstance($this->firstversion->get('created_by'));
									echo (is_object($profile)) ? $profile->get('name') . ' (' . $profile->get('id') . ')' : Lang::txt('COM_GROUPS_PAGES_SYSTEM');
								?>
							</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_GROUPS_PAGES_LAST_MODIFIED'); ?></th>
							<td><?php echo Date::of($this->version->get('created'))->toLocal('F j, Y @ g:ia'); ?></td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_GROUPS_PAGES_LAST_MODIFIED_BY'); ?></th>
							<td>
								<?php
									$profile = User::getInstance($this->version->get('created_by'));
									echo (is_object($profile)) ? $profile->get('name') . ' (' . $profile->get('id') . ')' : Lang::txt('COM_GROUPS_PAGES_SYSTEM');
								?>
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
			<?php if ($this->page->get('id')) : ?>
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_GROUPS_PAGES_PAGE_VERSIONS'); ?></span></legend>

					<table class="admintable">
						<thead>
							<tr>
								<th scope="col"><?php echo Lang::txt('COM_GROUPS_PAGES_VERSION_NUMBER'); ?></th>
								<th scope="col"><?php echo Lang::txt('COM_GROUPS_PAGES_VERSION_CREATED'); ?></th>
								<th scope="col"><?php echo Lang::txt('COM_GROUPS_PAGES_VERSION_APPROVED'); ?></th>
								<th scope="col"><?php echo Lang::txt('COM_GROUPS_PAGES_VERSION_VIEW'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->page->versions() as $version) : ?>
								<tr>
									<td><?php echo $version->get('version'); ?></td>
									<td>
										<?php
											$profile = User::getInstance($version->get('created_by'));
											$name = ((is_object($profile)) ? $profile->get('name') : Lang::txt('COM_GROUPS_PAGES_SYSTEM'));
											echo Lang::txt('COM_GROUPS_PAGES_VERSION_CREATED_DETAILS', $name, Date::of($version->get('created'))->toLocal());
										?>
									</td>
									<td>
										<?php
											if ($version->get('approved'))
											{
												$profile = User::getInstance($version->get('approved_by'));
												$name = ((is_object($profile)) ? $profile->get('name') : Lang::txt('COM_GROUPS_PAGES_SYSTEM'));
												echo Lang::txt('COM_GROUPS_PAGES_VERSION_APPROVED_DETAILS', $name, Date::of($version->get('approved_on'))->toLocal());
											}
											else
											{
												echo Lang::txt('COM_GROUPS_PAGES_VERSION_NOT_APPROVED');
											}
										?>
									</td>
									<td>
										<a class="version" href="<?php echo $base; ?>&amp;task=raw&amp;pageid=<?php echo $this->page->get('id'); ?>&amp;version=<?php echo $version->get('version'); ?>">
											<?php echo Lang::txt('COM_GROUPS_PAGES_VERSION_VIEW_RAW'); ?>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</fieldset>
			<?php endif; ?>
		</div>
	</div>

	<input type="hidden" name="page[id]" value="<?php echo $this->page->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="task" value="save" />
	<?php echo Html::input('token'); ?>
</form>