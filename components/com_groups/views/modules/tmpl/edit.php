<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

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
	<h2><?php echo ($this->module->get('id')) ? JText::_('COM_GROUPS_PAGES_EDIT_MODULE') : JText::_('COM_GROUPS_PAGES_ADD_MODULE'); ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="icon-prev prev btn" href="<?php echo JRoute::_($base_link); ?>">
				<?php echo JText::_('COM_GROUPS_ACTION_BACK_TO_MANAGE_MODULES'); ?>
			</a></li>
		</ul>
	</div>
</header>

<section class="main section edit-group-module">
	<?php foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $notification['message']; ?></p>
	<?php } ?>

	<form action="<?php echo JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=modules&task=save'); ?>" method="POST" id="hubForm" class="full">
		<div class="grid">
			<div class="col span9">
				<fieldset>
					<legend><?php echo JText::_('COM_GROUPS_PAGES_MODULE_DETAILS'); ?></legend>

					<label for="field-title">
						<strong><?php echo JText::_('COM_GROUPS_PAGES_MODULE_TITLE'); ?>:</strong> <span class="required"><?php echo JText::_('COM_GROUPS_FIELD_REQUIRED'); ?></span>
						<input type="text" name="module[title]" id="field-title" value="<?php echo $this->escape(stripslashes($this->module->get('title'))); ?>" />
					</label>
					<label for="field-content">
						<strong><?php echo JText::_('COM_GROUPS_PAGES_MODULE_CONTENT'); ?>:</strong> <span class="required"><?php echo JText::_('COM_GROUPS_FIELD_REQUIRED'); ?></span>
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
								'fileBrowserBrowseUrl'        => JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=filebrowser&tmpl=component'),
								'fileBrowserImageBrowseUrl'   => JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=media&task=filebrowser&tmpl=component'),
								'allowPhpTags'                => $allowPhp,
								'allowScriptTags'             => $allowScripts
							);

							// if super group add to templates
							if ($this->group->isSuperGroup())
							{
								$config['templates_replace'] = false;
								$config['templates_files']   = array('pagelayouts' => '/site/groups/' . $this->group->get('gidNumber') . '/template/assets/js/pagelayouts.js');
							}

							// display with ckeditor
							jimport( 'joomla.html.editor' );
							$editor = JEditor::getInstance( 'ckeditor' );
							echo $editor->display('module[content]', stripslashes($this->module->get('content')), '100%', '100px', 0, 0, false, 'field-content', null, null, $config);
						?>
					</label>
				</fieldset>

				<fieldset>
					<legend><?php echo JText::_('COM_GROUPS_PAGES_MODULE_MENU_ASSIGNMENT'); ?></legend>
					<label for="field-assignment">
						<strong><?php echo JText::_('COM_GROUPS_PAGES_MODULE_ASSIGNMENT'); ?>:</strong> <span class="required"><?php echo JText::_('COM_GROUPS_FIELD_REQUIRED'); ?></span>
						<select name="menu[assignment]" id="field-assignment" class="fancy-select">
							<option value="0"><?php echo JText::_('COM_GROUPS_PAGES_MODULE_ASSIGNMENT_ALL'); ?></option>
							<option <?php if (!in_array(0, $activeMenu)) { echo 'selected="selected"'; } ?> value=""><?php echo JText::_('COM_GROUPS_PAGES_MODULE_ASSIGNMENT_SELECTED'); ?></option>
						</select>
					</label>

					<label for="field-assignment-menu"><strong><?php echo JText::_('COM_GROUPS_PAGES_MODULE_SELECTION'); ?>:</strong> <span class="optional"><?php echo JText::_('COM_GROUPS_FIELD_OPTIONAL'); ?></span></label>
					<fieldset class="assignment" <?php if (in_array(0, $activeMenu)) : ?>disabled="disabled"<?php endif; ?>>
						<label>
							<button id="selectall"><?php echo JText::_('COM_GROUPS_PAGES_MODULE_SELECTION_ALL'); ?></button>
							<button id="clearselection"><?php echo JText::_('COM_GROUPS_PAGES_MODULE_SELECTION_CLEAR'); ?></button>
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
					<legend><?php echo JText::_('COM_GROUPS_PAGES_MODULE_PUBLISH'); ?></legend>

					<label for="field-state">
						<strong><?php echo JText::_('COM_GROUPS_PAGES_MODULE_STATUS'); ?>:</strong> <span class="optional"><?php echo JText::_('COM_GROUPS_FIELD_OPTIONAL')?></span>
						<select name="module[state]" id="field-state" class="fancy-select">
							<option value="1"><?php echo JText::_('COM_GROUPS_PAGES_MODULE_STATUS_PUBLISHED'); ?></option>
							<option value="0"><?php echo JText::_('COM_GROUPS_PAGES_MODULE_STATUS_UNPUBLISHED'); ?></option>
						</select>
					</label>
				</fieldset>
				<div class="form-controls cf">
					<a href="<?php echo $base_link; ?>" class="cancel"><?php echo JText::_('COM_GROUPS_PAGES_CANCEL'); ?></a>
					<button type="submit" class="btn btn-info opposite save icon-save"><?php echo JText::_('COM_GROUPS_PAGES_SAVE_MODULE'); ?></button>
				</div>

				<fieldset>
					<legend><?php echo JText::_('COM_GROUPS_PAGES_MODULE_SETTINGS'); ?></legend>
					<label for="field-position">
						<strong><?php echo JText::_('COM_GROUPS_PAGES_MODULE_POSITION'); ?>:</strong> <span class="optional"><?php echo JText::_('COM_GROUPS_FIELD_OPTIONAL')?></span>
						<input type="text" name="module[position]" id="field-position" value="<?php echo $this->escape(stripslashes($this->module->get('position'))); ?>" />
					</label>
					<?php if ($this->module->get('id')) : ?>
						<label for="field-ordering">
							<strong><?php echo JText::_('COM_GROUPS_PAGES_MODULE_ORDERING'); ?>:</strong> <span class="optional"><?php echo JText::_('COM_GROUPS_FIELD_OPTIONAL')?></span>
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
		<input type="hidden" name="return" value="<?php echo JRequest::getVar('return', '','get'); ?>" />
		<input type="hidden" name="task" value="save" />
	</form>
</section>