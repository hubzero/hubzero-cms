<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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
$fields = new JForm($this->module->module);
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