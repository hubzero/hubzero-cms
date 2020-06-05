<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// create user params registry
$params = new \Hubzero\Config\Registry($this->module->params);

// load module params fields
$manifest = PATH_APP . DS . 'modules' . DS . $this->module->module . DS . $this->module->module . '.xml';
if (!file_exists($manifest))
{
	$manifest = PATH_CORE . DS . 'modules' . DS . $this->module->module . DS . $this->module->module . '.xml';
}
$fields = new Hubzero\Form\Form($this->module->module);
$fields->loadFile($manifest, true, 'config/fields');

// This is done first as the 'renderModule' method loads the language file
// which is needed when rendering the params below
$module = Module::render($this->module, array('style' => 'none'));

// create settings sub view
$view = $this->view('parameters');
$view->admin  = $this->admin;
$view->module = $this->module;
$view->params = $params->toArray();
$view->fields = $fields->getFieldset('basic');
$settingsHtml = trim($view->loadTemplate());
?>

<div class="module <?php echo strtolower($this->module->module) . ' ' . $params->get('moduleclass_sfx'); ?>  draggable sortable"
	 data-row="<?php echo $this->module->positioning->row; ?>"
	 data-col="<?php echo $this->module->positioning->col; ?>"
	 data-sizex="<?php echo $this->module->positioning->size_x; ?>"
	 data-sizey="<?php echo $this->module->positioning->size_y; ?>"
	 data-moduleid="<?php echo $this->module->id; ?>">

	<div class="inner">

		<div class="module-title">
			<h3><?php echo $this->escape($this->module->title); ?></h3>
			<ul class="module-links">
				<?php if ($settingsHtml != '') : ?>
					<li>
						<a class="settings" title="Module Settings" href="javascript:void(0);">
							<span><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_MODULE_SETTINGS'); ?></span>
						</a>
					</li>
				<?php endif; ?>
				<li>
					<a class="remove" title="Remove Module" href="javascript:void(0);">
						<span><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_MODULE_REMOVE'); ?></span>
					</a>
				</li>
			</ul>
		</div>

		<div class="module-main">
			<?php echo $settingsHtml; ?>
			<div class="module-content">
				<?php
				if ($this->admin)
				{
					echo '<div class="custom">' . Lang::txt('PLG_MEMBERS_DASHBOARD_MODULE_ADMIN_CONTENT') . '</div>';
				}
				elseif ($this->module->module == 'mod_custom')
				{
					echo '<div class="custom">' . $this->module->content . '</div>';
				}
				else
				{
					$this->module->user = false;
					echo $module;
				}
				?>
			</div>
		</div><!-- /.module-main -->
	</div>
</div>