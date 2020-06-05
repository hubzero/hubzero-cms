<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_HELP'), 'help');

$this->css()
	->js();
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<div class="grid col-row">
		<div class="col span4">
			<h3><?php echo Lang::txt('COM_HELP_USERS'); ?></h3>
			<ul>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_members'); ?>"><?php echo Lang::txt('COM_HELP_USER_ACCOUNTS'); ?></a>
				</li>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_groups'); ?>"><?php echo Lang::txt('COM_HELP_USER_GROUPS'); ?></a>
				</li>
			</ul>
			<h3><?php echo Lang::txt('COM_HELP_MENUS'); ?></h3>
			<ul>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_menus'); ?>"><?php echo Lang::txt('COM_HELP_MENU_MANAGER'); ?></a>
				</li>
			</ul>
			<h3><?php echo Lang::txt('COM_HELP_CONTENT'); ?></h3>
			<ul>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_content'); ?>"><?php echo Lang::txt('COM_HELP_ARTICLE_MANAGER'); ?></a>
				</li>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_categories'); ?>"><?php echo Lang::txt('COM_HELP_CATEGORY_MANAGER'); ?></a>
				</li>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_media'); ?>"><?php echo Lang::txt('COM_HELP_MEDIA_MANAGER'); ?></a>
				</li>
			</ul>
			<h3><?php echo Lang::txt('COM_HELP_EXTENSIONS'); ?></h3>
			<ul>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_modules'); ?>"><?php echo Lang::txt('COM_HELP_MODULE_MANAGER'); ?></a>
				</li>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_plugins'); ?>"><?php echo Lang::txt('COM_HELP_PLUGIN_MANAGER'); ?></a>
				</li>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_templates'); ?>"><?php echo Lang::txt('COM_HELP_TEMPLATE_MANAGER'); ?></a>
				</li>
				<li>
					<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_languages'); ?>"><?php echo Lang::txt('COM_HELP_LANGUAGE_MANAGER'); ?></a>
				</li>
			</ul>
			<h3><?php echo Lang::txt('Components'); ?></h3>
			<ul>
				<?php foreach ($this->components as $component) { ?>
					<li>
						<a target="help-page" href="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=' . $component->element); ?>"><?php echo Lang::txt($component->text); ?></a>
					</li>
				<?php } ?>
			</ul>
		</div>
		<div class="col span8">
			<iframe id="help-page" src="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=help&component=com_help&page=index'); ?>"></iframe>
		</div>
	</div>
</form>