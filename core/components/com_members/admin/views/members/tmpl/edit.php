<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Members\Helpers\Admin::getActions('component');

$text = ($this->profile->isNew() ? Lang::txt('JACTION_CREATE') : Lang::txt('JACTION_EDIT'));

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . $text, 'user');
if ($canDo->get('core.edit') || $canDo->get('core.create'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
if ($canDo->get('core.create') && $canDo->get('core.manage'))
{
	Toolbar::save2new();
}
Toolbar::cancel();
Toolbar::divider();
Toolbar::help('user');

$this->css();

Html::behavior('switcher', 'submenu');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">

	<nav role="navigation" class="sub-navigation">
		<div id="submenu-box">
			<div class="submenu-box">
				<div class="submenu-pad">
					<ul id="submenu" class="member-nav">
						<li><a href="#page-account" id="account" class="active"><?php echo Lang::txt('COM_MEMBERS_SECTION_ACCOUNT'); ?></a></li>
						<li><a href="#page-profile" id="profile"><?php echo Lang::txt('COM_MEMBERS_SECTION_PROFILE'); ?></a></li>
						<?php if (User::authorise('core.admin', $this->option) || User::authorise('core.edit', $this->option)): ?>
							<li><a href="#page-password" id="password"><?php echo Lang::txt('COM_MEMBERS_SECTION_PASSWORD'); ?></a></li>
						<?php endif; ?>
						<?php if (!$this->profile->isNew()): ?>
							<li><a href="#page-groups" id="groups"><?php echo Lang::txt('COM_MEMBERS_SECTION_GROUPS'); ?></a></li>
							<li><a href="#page-hosts" id="hosts"><?php echo Lang::txt('COM_MEMBERS_SECTION_HOSTS'); ?></a></li>
							<li><a href="#page-messaging" id="messaging"><?php echo Lang::txt('COM_MEMBERS_SECTION_MESSAGING'); ?></a></li>
							<?php
							foreach ($this->tabs as $tab):
								if (!$tab):
									continue;
								endif;
								?>
								<li>
									<a href="#page-<?php echo $tab['name']; ?>" id="<?php echo $this->escape($tab['name']); ?>">
										<?php echo $this->escape($tab['label']); ?>
									</a>
								</li>
								<?php
							endforeach;
							?>
						<?php endif; ?>
					</ul>
					<div class="clr"></div>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</nav><!-- / .sub-navigation -->

	<div id="member-document">
		<div id="page-account" class="tab">
			<?php echo $this->loadTemplate('user'); ?>
		</div>

		<div id="page-profile" class="tab">
			<?php echo $this->loadTemplate('profile'); ?>
		</div>

		<?php if (User::authorise('core.admin', $this->option) || User::authorise('core.edit', $this->option)): ?>
			<div id="page-password" class="tab">
				<?php echo $this->loadTemplate('password'); ?>
			</div>
		<?php endif; ?>

		<?php if (!$this->profile->isNew()): ?>
			<div id="page-groups" class="tab">
				<?php echo $this->loadTemplate('groups'); ?>
			</div>

			<div id="page-hosts" class="tab">
				<?php echo $this->loadTemplate('hosts'); ?>
			</div>

			<div id="page-messaging" class="tab">
				<?php echo $this->loadTemplate('messaging'); ?>
			</div>
			<?php
			foreach ($this->tabs as $tab):
				if (!$tab):
					continue;
				endif;
				?>
				<div id="page-<?php echo $this->escape($tab['name']); ?>" class="tab">
					<?php echo $tab['content']; ?>
				</div>
				<?php
			endforeach;
			?>
		<?php endif; ?>
	</div>

	<input type="hidden" name="id" value="<?php echo $this->profile->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
