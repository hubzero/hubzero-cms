<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css()
     ->js();

// Can't view wishes on a private list if not list admin
if (!$this->wishlist->isPublic() && !$this->wishlist->access('manage')) { ?>
	<header id="content-header">
		<h2><?php echo Lang::txt('COM_WISHLIST_PRIVATE_LIST'); ?></h2>
	</header><!-- / #content-header -->
	<section class="main section">
		<p class="error"><?php echo Lang::txt('COM_WISHLIST_ALERTNOTAUTH_PRIVATE_LIST'); ?></p>
	</section><!-- / .main section -->
<?php } else { ?>
	<header id="content-header">
		<h2><?php echo $this->title; ?></h2>

		<div id="content-header-extra">
			<ul id="useroptions">
				<li class="last">
					<a class="icon-wish nav_wishlist btn" href="<?php echo Route::url($this->wishlist->link()); ?>">
						<?php echo Lang::txt('COM_WISHLIST_WISHES_ALL'); ?>
					</a>
				</li>
			</ul>
		</div><!-- / #content-header-extra -->
	</header><!-- / #content-header -->

	<section class="main section">
		<form id="hubForm" method="post"  action="<?php echo Route::url($this->wishlist->link('savesettings')); ?>">
			<div class="explaination">
				<p><?php echo Lang::txt('COM_WISHLIST_SETTINGS_INFO'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_WISHLIST_INFORMATION'); ?></legend>

				<div class="form-group">
					<label for="field-title">
						<?php echo Lang::txt('COM_WISHLIST_TITLE'); ?>:
				<?php if ($this->wishlist->get('category') == 'resource') { ?>
						<span class="highighted"><?php echo $this->wishlist->get('title'); ?></span>
						<input name="fields[title]" id="field-title" type="hidden" value="<?php echo $this->escape($this->wishlist->get('title')); ?>" />
					</label>
					<p class="hint"><?php echo Lang::txt('COM_WISHLIST_TITLE_NOTE'); ?></p>
				<?php } else { ?>
						<input name="fields[title]" id="field-title" class="form-control" type="text" value="<?php echo $this->escape($this->wishlist->get('title')); ?>" />
					</label>
				<?php } ?>
				</div>

				<div class="form-group">
					<label for="field-description">
						<?php echo Lang::txt('COM_WISHLIST_DESC'); ?> (<?php echo Lang::txt('COM_WISHLIST_OPTIONAL'); ?>):
						<textarea name="fields[description]" id="field-description" class="form-control" rows="10" cols="50"><?php echo $this->escape($this->wishlist->get('description')); ?></textarea>
					</label>
				</div>

				<fieldset>
					<legend><?php echo Lang::txt('COM_WISHLIST_THIS_LIST_IS'); ?>:</legend>

					<div class="form-group form-check">
						<label for="field-public-yes" class="form-check-label">
							<input class="option form-check-input" type="radio" name="fields[public]" id="field-public-yes" value="1" <?php
							if ($this->wishlist->get('public') == 1) {
								echo ' checked="checked"';
							}
							if ($this->wishlist->get('category') == 'resource' or ($this->wishlist->get('category') == 'general' && $this->wishlist->get('referenceid') == 1)) {
								echo ' disabled="disabled"';
							} ?> /> <?php echo Lang::txt('COM_WISHLIST_PUBLIC'); ?>
						</label>
					</div>

					<div class="form-group form-check">
						<label for="field-public-no" class="form-check-label">
							<input class="option form-check-input" type="radio" name="fields[public]" id="field-public-no" value="0" <?php
							if ($this->wishlist->get('public') == 0) {
								echo ' checked="checked"';
							}
							if ($this->wishlist->get('category') =='resource' or ($this->wishlist->get('category') == 'general' && $this->wishlist->get('referenceid') == 1)) {
								echo ' disabled="disabled"';
							} ?> /> <?php echo Lang::txt('COM_WISHLIST_PRIVATE'); ?>
						</label>
					</div>
				</fieldset>
			</fieldset>
			<div class="clear"></div>

			<?php $owners = $this->wishlist->getOwners(); ?>

			<div class="explaination">
				<p><?php echo Lang::txt('COM_WISHLIST_SETTINGS_EDIT_GROUPS'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_WISHLIST_OWNER_GROUPS'); ?></legend>
				<div class="field-wrap">
					<table class="tktlist">
						<thead>
							<tr>
								<th scope="col"></th>
								<th scope="col"><?php echo Lang::txt('COM_WISHLIST_SETTINGS_GROUP_CN'); ?></th>
								<th scope="col"><?php echo Lang::txt('COM_WISHLIST_GROUP_NUM_MEMBERS'); ?></th>
								<th scope="col"><?php echo Lang::txt('COM_WISHLIST_GROUP_OPTIONS'); ?></th>
							</tr>
						</thead>
						<tbody>
					<?php
					$allmembers = array();
					$groups = $owners['groups'];
					if (count($groups) > 0)
					{
						$k = 1;
						for ($i = 0, $n = count($groups); $i < $n; $i++)
						{
							$instance = \Hubzero\User\Group::getInstance($groups[$i]);

							$members  = $instance->get('members');

							$allmembers = array_merge($allmembers, $members);
							?>
							<tr>
								<th scope="row"><?php echo $k; ?>.</th>
								<td><?php echo $this->escape($instance->get('cn')); ?></td>
								<td><?php echo count($members); ?></td>
								<td>
									<?php echo ($n>1 && !in_array($groups[$i], $this->wishlist->owners('groups', 1))) ? '<a href="'.Route::url($this->wishlist->link('savesettings') . '&action=delete&group='.$groups[$i]) . '" class="delete">'.Lang::txt('COM_WISHLIST_OPTION_REMOVE').'</a>' : ''; ?>
								</td>
							</tr>
							<?php
							$k++;
						}
					} else { ?>
							<tr>
								<td colspan="4"><?php echo Lang::txt('COM_WISHLIST_NO_OWNER_GROUPS_FOUND'); ?>.</td>
							</tr>
					<?php } ?>
						</tbody>
					</table>
				</div>

				<label for="field_newgroups">
					<?php echo Lang::txt('COM_WISHLIST_SETTINGS_ADD_GROUPS'); ?>:
					<?php
					$mc = Event::trigger('hubzero.onGetMultiEntry', array(array('groups', 'newgroups', 'field_newgroups', '', '')));
					if (count($mc) > 0) {
						echo $mc[0];
					} else { ?>
					<input type="text" name="newgroups" id="field_newgroups" value="" />
					<?php } ?>
					<span class="hint"><?php echo Lang::txt('COM_WISHLIST_GROUP_HINT'); ?></span>
				</label>
			</fieldset>
			<div class="clear"></div>

			<div class="explaination">
				<p><?php echo Lang::txt('COM_WISHLIST_INDIVIDUALS_HINT'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_WISHLIST_INDIVIDUALS'); ?></legend>
				<div class="field-wrap">
					<table class="tktlist">
						<thead>
							<tr>
								<th scope="col"></th>
								<th scope="col"><?php echo Lang::txt('COM_WISHLIST_IND_NAME'); ?></th>
								<th scope="col"><?php echo Lang::txt('COM_WISHLIST_IND_LOGIN'); ?></th>
								<th scope="col"><?php echo Lang::txt('COM_WISHLIST_GROUP_OPTIONS'); ?></th>
							</tr>
						</thead>
						<tbody>
					<?php
					$allmembers = array_unique($allmembers);

					$individuals = $owners['individuals'];

					$native = $this->wishlist->getOwners(null, 1);

					// if we have people outside of groups
					if (count($individuals) > count($allmembers))
					{
						$k = 1;
						for ($i = 0, $n = count($individuals); $i < $n; $i++)
						{
							if (!in_array($individuals[$i], $allmembers))
							{
								$kuser = User::getInstance($individuals[$i]);
							?>
							<tr>
								<td><?php echo $k; ?>.</td>
								<td><?php echo $this->escape($kuser->get('name')); ?></td>
								<td><?php echo $this->escape($kuser->get('username')); ?></td>
								<td>
									<?php echo ($n> 1 && !in_array($individuals[$i], $native['individuals']))  ? '<a href="'.Route::url($this->wishlist->link('savesettings') . '&action=delete&user=' . $individuals[$i]).'" class="delete">'.Lang::txt('COM_WISHLIST_OPTION_REMOVE').'</a>' : ''; ?>
								</td>
							</tr>
							<?php
								$k++;
							}
						}
					} else { ?>
							<tr>
								<td colspan="4"><?php echo Lang::txt('COM_WISHLIST_NO_IND_FOUND'); ?></td>
							</tr>
					<?php } ?>
						</tbody>
					</table>
				</div>

				<div class="form-group">
					<label for="field_newowners">
						<?php echo Lang::txt('COM_WISHLIST_ADD_IND'); ?>:
						<?php
						$mc = Event::trigger('hubzero.onGetMultiEntry', array(array('members', 'newowners', 'field_newowners', '', '')));
						if (count($mc) > 0) {
							echo $mc[0];
						} else { ?>
						<input type="text" name="newowners" id="field_newowners" class="form-control" value="" />
						<?php } ?>
						<span class="hint"><?php echo Lang::txt('COM_WISHLIST_ENTER_LOGINS'); ?></span>
					</label>
				</div>
			</fieldset>
			<div class="clear"></div>

			<?php if ($this->wishlist->config('allow_advisory', 0)) { ?>
				<div class="explaination">
					<p><?php echo Lang::txt('COM_WISHLIST_ADD_ADVISORY_INFO'); ?></p>
				</div>
				<fieldset>
					<legend><?php echo Lang::txt('COM_WISHLIST_ADVISORY'); ?></legend>
					<div class="field-wrap">
						<table class="tktlist">
							<thead>
								<tr>
									<th scope="col"></th>
									<th scope="col"><?php echo Lang::txt('COM_WISHLIST_IND_NAME'); ?></th>
									<th scope="col"><?php echo Lang::txt('COM_WISHLIST_IND_LOGIN'); ?></th>
									<th scope="col"><?php echo Lang::txt('COM_WISHLIST_GROUP_OPTIONS'); ?></th>
								</tr>
							</thead>
							<tbody>
						<?php
						// if we have people outside of groups
						$advisory = $owners['advisory'];
						if (count($advisory) > 0)
						{
							$k=1;

							for ($i=0, $n=count($advisory); $i < $n; $i++)
							{
								if (!in_array($advisory[$i], $allmembers))
								{
									$quser = User::getInstance($advisory[$i]);
								?>
								<tr>
									<td><?php echo $k; ?>.</td>
									<td><?php echo $this->escape($quser->get('name')); ?></td>
									<td><?php echo $this->escape($quser->get('username')); ?></td>
									<td>
										<a href="<?php echo Route::url($this->wishlist->link('savesettings') . '&action=delete&user=' . $advisory[$i]); ?>" class="delete"><?php echo Lang::txt('COM_WISHLIST_OPTION_REMOVE'); ?></a>
									</td>
								</tr>
								<?php
									$k++;
								}
							}
						} else { ?>
								<tr>
									<td colspan="4"><?php echo Lang::txt('COM_WISHLIST_NO_ADVISORY_FOUND'); ?></td>
								</tr>
						<?php } ?>
							</tbody>
						</table>
					</div>

					<div class="form-group">
						<label for="field_newadvisory">
							<?php echo Lang::txt('COM_WISHLIST_ADD_ADVISORY_MEMBERS'); ?>:
							<?php
							$mc = Event::trigger('hubzero.onGetMultiEntry', array(array('members', 'newadvisory', 'field_newadvisory', '', '')));
							if (count($mc) > 0) {
								echo $mc[0];
							} else { ?>
							<input type="text" name="newadvisory" id="field_newadvisory" class="form-control" value="" />
							<?php } ?>
							<span><?php echo Lang::txt('COM_WISHLIST_ENTER_LOGINS'); ?></span>
						</label>
					</div>
				<?php if ($this->wishlist->get('category') == 'resource' or ($this->wishlist->get('category') == 'general' && $this->wishlist->get('referenceid') == 1)) { ?>
					<input type="hidden" name="fields[public]" value="<?php echo $this->wishlist->get('public'); ?>" />
				<?php } ?>
				</fieldset>
				<div class="clear"></div>
			<?php } // -- end if allow advisory ?>

			<p class="submit">
				<input class="btn btn-success" type="submit" name="submit" value="<?php echo Lang::txt('COM_WISHLIST_SAVE'); ?>" />

				<a class="btn btn-secondary" href="<?php echo Route::url($this->wishlist->link()); ?>">
					<?php echo Lang::txt('JCANCEL'); ?>
				</a>
			</p>

			<input type="hidden" name="listid" value="<?php echo $this->wishlist->get('id'); ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->wishlist->get('id'); ?>" />

			<?php echo Html::input('token'); ?>
		</form>
	</section>
<?php } // end if authorized 