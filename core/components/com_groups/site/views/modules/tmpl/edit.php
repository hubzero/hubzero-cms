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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// add styles & scripts
$this->css()
	 ->js()
     ->css('jquery.fancyselect.css', 'system')
     ->js('jquery.fancyselect', 'system');

// define base link
$base_link = 'index.php?option=com_groups&cn='.$this->group->get('cn').'&task=pages#modules';

// get module menus
$menus = $this->module->menu('list');
$activeMenu = (!$this->module->get('id')) ? array(0) : array();
foreach ($menus as $menu)
{
	$activeMenu[] = $menu->get('pageid');
}
?>
<header id="content-header">
	<h2><?php echo ($this->module->get('id')) ? Lang::txt('COM_GROUPS_PAGES_EDIT_MODULE') : Lang::txt('COM_GROUPS_PAGES_ADD_MODULE'); ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="icon-prev prev btn" href="<?php echo Route::url($base_link); ?>">
				<?php echo Lang::txt('COM_GROUPS_ACTION_BACK_TO_MANAGE_MODULES'); ?>
			</a></li>
		</ul>
	</div>
</header>

<section class="main section edit-group-module">
	<?php foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $notification['message']; ?></p>
	<?php } ?>

	<form action="<?php echo Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=save'); ?>" method="post" id="hubForm" class="full">
		<div class="grid">
			<div class="col span9">
				<fieldset>
					<legend><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_DETAILS'); ?></legend>

					<label for="field-title">
						<strong><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_TITLE'); ?>:</strong> <span class="required"><?php echo Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?></span>
						<input type="text" name="module[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->module->get('title'))); ?>" />
					</label>
					<label for="field-content">
						<strong><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_CONTENT'); ?>:</strong> <span class="required"><?php echo Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?></span>
						<?php
							$allowPhp      = true;
							$allowScripts  = true;
							$startupMode   = 'wysiwyg';
							$showSourceBtn = true;

							// only allow super groups to use php & scrips
							// strip out php and scripts if somehow it made it through
							if (!$this->group->isSuperGroup())
							{
								$allowPhp     = false;
								$allowScripts = false;
								$content      = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $this->module->get('content'));
								$content      = preg_replace('/<\?[\s\S]*?\?>/', '', $this->module->get('content'));
							}

							// open in source mode if contains php or scripts
							if (strstr(stripslashes($this->module->get('content')), '<script>') ||
								strstr(stripslashes($this->module->get('content')), '<?php'))
							{
								$startupMode  = 'source';
								//$showSourceBtn = false;
							}

							//build config
							$config = array(
								'startupMode'                 => $startupMode,
								'sourceViewButton'            => $showSourceBtn,
								'contentCss'                  => $this->stylesheets,
								'fileBrowserWindowWidth'      => 1200,
								'fileBrowserBrowseUrl'        => Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=filebrowser&tmpl=component'),
								'fileBrowserImageBrowseUrl'   => Route::url('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=filebrowser&tmpl=component'),
								'allowPhpTags'                => $allowPhp,
								'allowScriptTags'             => $allowScripts
							);

							// if super group add to templates
							if ($this->group->isSuperGroup())
							{
								$config['templates_replace'] = false;
								$config['templates_files']   = array('pagelayouts' => substr(PATH_APP, strlen(PATH_ROOT)) . '/site/groups/' . $this->group->get('gidNumber') . '/template/assets/js/pagelayouts.js');
							}

							// display with ckeditor
							$editor = new \Hubzero\Html\Editor('ckeditor');
							echo $editor->display('module[content]', stripslashes($this->module->get('content')), '100%', '100px', 0, 0, false, 'field-content', null, null, $config);
						?>
					</label>
				</fieldset>

				<fieldset>
					<legend><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_MENU_ASSIGNMENT'); ?></legend>
					<label for="field-assignment">
						<strong><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_ASSIGNMENT'); ?>:</strong> <span class="required"><?php echo Lang::txt('COM_GROUPS_FIELD_REQUIRED'); ?></span>
						<select name="menu[assignment]" id="field-assignment" class="fancy-select">
							<option value="0"><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_ASSIGNMENT_ALL'); ?></option>
							<option <?php if (!in_array(0, $activeMenu)) { echo 'selected="selected"'; } ?> value=""><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_ASSIGNMENT_SELECTED'); ?></option>
						</select>
					</label>

					<label for="field-assignment-menu"><strong><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_SELECTION'); ?>:</strong> <span class="optional"><?php echo Lang::txt('COM_GROUPS_FIELD_OPTIONAL'); ?></span></label>
					<fieldset class="assignment" <?php if (in_array(0, $activeMenu)) : ?>disabled="disabled"<?php endif; ?>>
						<label>
							<button id="selectall"><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_SELECTION_ALL'); ?></button>
							<button id="clearselection"><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_SELECTION_CLEAR'); ?></button>
						</label>
						<?php foreach ($this->pages as $page) : ?>
							<label>
								<?php $ckd = (in_array($page->get('id'), $activeMenu) || in_array(0, $activeMenu)) ? 'checked="checked"' : ''; ?>
								<input type="checkbox" class="option" <?php echo $ckd; ?> name="menu[assigned][]" value="<?php echo $page->get('id'); ?>" /> <?php echo $page->get('title'); ?>
							</label>
						<?php endforeach; ?>
					</fieldset>
				</fieldset>
			</div>
			<div class="col span3 omega">
				<fieldset>
					<legend><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_PUBLISH'); ?></legend>

					<label for="field-state">
						<strong><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_STATUS'); ?>:</strong> <span class="optional"><?php echo Lang::txt('COM_GROUPS_FIELD_OPTIONAL')?></span>
						<select name="module[state]" id="field-state" class="fancy-select">
							<option value="1"><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_STATUS_PUBLISHED'); ?></option>
							<option value="0"><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_STATUS_UNPUBLISHED'); ?></option>
						</select>
					</label>
				</fieldset>
				<div class="form-controls cf">
					<a href="<?php echo $base_link; ?>" class="cancel"><?php echo Lang::txt('COM_GROUPS_PAGES_CANCEL'); ?></a>
					<button type="submit" class="btn btn-info opposite save icon-save"><?php echo Lang::txt('COM_GROUPS_PAGES_SAVE_MODULE'); ?></button>
				</div>

				<fieldset>
					<legend><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_SETTINGS'); ?></legend>
					<label for="field-position">
						<strong><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_POSITION'); ?>:</strong> <span class="optional"><?php echo Lang::txt('COM_GROUPS_FIELD_OPTIONAL')?></span>
						<input type="text" name="module[position]" id="field-position" value="<?php echo $this->escape(stripslashes($this->module->get('position'))); ?>" />
					</label>
					<?php if ($this->module->get('id')) : ?>
						<label for="field-ordering">
							<strong><?php echo Lang::txt('COM_GROUPS_PAGES_MODULE_ORDERING'); ?>:</strong> <span class="optional"><?php echo Lang::txt('COM_GROUPS_FIELD_OPTIONAL')?></span>
							<select name="module[ordering]" id="field-ordering" class="fancy-select">
								<?php foreach ($this->order as $k => $order) : ?>
									<?php $sel = ($order->get('title') == $this->module->get('title')) ? 'selected="selected"' : ''; ?>
									<option <?php echo $sel ;?> value="<?php echo ($k + 1); ?>"><?php echo ($k + 1) . '. ' . $order->get('title'); ?></option>
								<?php endforeach; ?>
							</select>
						</label>
					<?php endif;?>
				</fieldset>
			</div>
		</div>

		<input type="hidden" name="module[id]" value="<?php echo $this->module->get('id'); ?>" />
		<input type="hidden" name="option" value="com_groups" />
		<input type="hidden" name="controller" value="modules" />
		<input type="hidden" name="return" value="<?php echo Request::getVar('return', '','get'); ?>" />
		<input type="hidden" name="task" value="save" />
	</form>
</section>