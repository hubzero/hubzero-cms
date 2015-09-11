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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

$exec_pu = $this->config->get('exec_pu', 1);

$execChoices[''] = Lang::txt('COM_TOOLS_SELECT_TOP');
$execChoices['@OPEN'] =  ucfirst(Lang::txt('COM_TOOLS_TOOLACCESS_OPEN'));
$execChoices['@US'] = ucfirst(Lang::txt('COM_TOOLS_TOOLACCESS_US'));
$execChoices['@D1'] = ucfirst(Lang::txt('COM_TOOLS_TOOLACCESS_D1'));
if ($exec_pu)
{
	$execChoices['@PU'] = ucfirst(Lang::txt('COM_TOOLS_TOOLACCESS_PU'));
}
$execChoices['@GROUP'] = ucfirst(Lang::txt('COM_TOOLS_RESTRICTED')).' '.Lang::txt('COM_TOOLS_TO').' '.Lang::txt('COM_TOOLS_GROUP_OR_GROUPS');

$codeChoices[''] = Lang::txt('COM_TOOLS_SELECT_TOP');
$codeChoices['@OPEN'] = ucfirst(Lang::txt('COM_TOOLS_OPEN_SOURCE')). ' ('.Lang::txt('COM_TOOLS_OPEN_SOURCE_TIPS').')';
$codeChoices['@DEV'] = ucfirst(Lang::txt('COM_TOOLS_ACCESS_RESTRICTED'));

$wikiChoices[''] = Lang::txt('COM_TOOLS_SELECT_TOP');
$wikiChoices['@OPEN'] = ucfirst(Lang::txt('COM_TOOLS_ACCESS_OPEN'));
$wikiChoices['@DEV'] = ucfirst(Lang::txt('COM_TOOLS_ACCESS_RESTRICTED'));

$this->css('pipeline.css')
     ->js('pipeline.js');
?>
<header id="content-header">
	<h2><?php echo $this->escape($this->title); ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
		<?php if ($this->id) { ?>
			<li><a class="icon-status status btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $this->defaults['toolname']); ?>"><?php echo Lang::txt('COM_TOOLS_TOOL_STATUS'); ?></a></li>
		<?php } ?>
			<li class="last"><a class="icon-main main-page btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=pipeline'); ?>"><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_ALL_TOOLS'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<?php if ($this->getError()) { ?>
<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>

<section class="section">
	<div class="section-inner">
		<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm" enctype="multipart/form-data">
			<div class="explaination">
				<?php if (!$this->id) { ?>
					<h3><?php echo Lang::txt('COM_TOOLS_SIDE_WHAT_TOOLNAME'); ?></h3>
					<p><?php echo Lang::txt('COM_TOOLS_SIDE_TIPS_TOOLNAME'); ?></p>
				<?php } else { ?>
					<p><?php echo Lang::txt('COM_TOOLS_SIDE_EDIT_TOOL'); ?></p>
				<?php } ?>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_TOOLS_LEGEND_ABOUT'); ?>:</legend>

				<input type="hidden" name="toolid" value="<?php echo $this->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="<?php echo ($this->id) ? 'save' : 'register'; ?>" />
				<input type="hidden" name="editversion" value="<?php echo $this->editversion; ?>" />
				<?php echo Html::input('token'); ?>

				<label for="t_toolname">
					<?php echo Lang::txt('COM_TOOLS_TOOLNAME'); ?>:
					<?php if ($this->id) { ?>
						<input type="hidden" name="tool[toolname]" id="t_toolname" value="<?php echo $this->defaults['toolname']; ?>" />
						<strong><?php echo $this->defaults['toolname']; ?> (<?php echo ($this->editversion == 'current') ? Lang::txt('COM_TOOLS_CURRENT_VERSION') : Lang::txt('COM_TOOLS_DEV_VERSION'); ?>)</strong>
						<?php if (isset($this->defaults['published']) && $this->defaults['published']) { ?>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=versions&app='.$this->id); ?>"><?php echo Lang::txt('COM_TOOLS_ALL_VERSIONS'); ?></a>
						<?php } ?>
					<?php } else { ?>
						<span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
						<input type="text" name="tool[toolname]" id="t_toolname" maxlength="15" value="<?php echo $this->escape($this->defaults['toolname']); ?>" />
						<span class="hint"><?php echo Lang::txt('COM_TOOLS_HINT_TOOLNAME'); ?></span>
					<?php } ?>
				</label>

				<label for="t_title">
					<?php echo Lang::txt('COM_TOOLS_TITLE') ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
					<input type="text" name="tool[title]" id="t_title" maxlength = "127" value="<?php echo $this->escape(stripslashes($this->defaults['title'])); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_TOOLS_HINT_TITLE'); ?></span>
				</label>

				<label for="t_version">
					<?php echo Lang::txt('COM_TOOLS_VERSION') ?>:
					<?php if ($this->editversion == 'current') { ?>
						<input type="hidden" name="tool[version]" id="t_version" value="<?php echo $this->escape($this->defaults['version']); ?>" />
						<strong><?php echo $this->defaults['version']; ?></strong>
						<span class="hint"><?php echo Lang::txt('COM_TOOLS_HINT_VERSION_PUBLISHED'); ?></span>
					<?php } else { ?>
						<input type="text" name="tool[version]" id="t_version" maxlength="15" value="<?php echo $this->escape($this->defaults['version']); ?>" />
						<span class="hint"><?php echo Lang::txt('COM_TOOLS_HINT_VERSION'); ?></span>
					<?php } ?>
				</label>

				<label for="t_description">
					<?php echo Lang::txt('COM_TOOLS_AT_A_GLANCE') ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
					<input type="text" name="tool[description]" id="t_description" maxlength="256" value="<?php echo $this->escape(stripslashes($this->defaults['description'])); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_TOOLS_HINT_DESCRIPTION'); ?></span>
				</label>

				<?php if ($this->id && isset($this->defaults['resourceid'])) { ?>
					<p>
						<?php echo Lang::txt('COM_TOOLS_DESCRIPTION'); ?>:
						<a class="icon-preview btn btn-secondary" href="<?php echo Route::url('index.php?option=com_resources&id=' . $this->defaults['resourceid'] . '&rev=dev'); ?>"><?php echo Lang::txt('COM_TOOLS_PREVIEW') ?></a> |
						<a class="icon-edit btn btn-secondary" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=resource&app=' . $this->defaults['toolname']); ?>"><?php echo Lang::txt('COM_TOOLS_TODO_EDIT_PAGE') ?></a>
					</p>
				<?php } ?>

				<fieldset>
					<legend><?php echo ($this->id) ? Lang::txt('COM_TOOLS_APPLICATION_SCREEN_SIZE'): Lang::txt('COM_TOOLS_SUGGESTED_SCREEN_SIZE')  ?>:</legend>
					<div class="inline">
						<label for="vncGeometryX"><?php echo Lang::txt('COM_TOOLS_MARKER_WIDTH'); ?> <input type="text" name="tool[vncGeometryX]" id="vncGeometryX" size="4" maxlength="4" value="<?php echo $this->defaults['vncGeometryX']; ?>" /></label> x
						<label for="vncGeometryY"><?php echo Lang::txt('COM_TOOLS_MARKER_HEIGHT'); ?> <input type="text" name="tool[vncGeometryY]" id="vncGeometryY" size="4" maxlength="4" value="<?php echo $this->defaults['vncGeometryY']; ?>" /></label>
					</div>
					<p class="hint"><?php echo Lang::txt('COM_TOOLS_HINT_VNC'); ?></p>
				</fieldset>
			</fieldset>

			<fieldset>
				<legend><?php echo Lang::txt('COM_TOOLS_LEGEND_ACCESS'); ?>:</legend>

				<label for="t_exec">
					<?php echo Lang::txt('COM_TOOLS_TOOL_ACCESS'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
					<?php echo \Components\Tools\Helpers\Html::formSelect('tool[exec]', 't_exec', $execChoices, $this->defaults['exec'], 'groupchoices'); ?>
				</label>

				<p><?php echo Lang::txt('COM_TOOLS_SIDE_TIPS_TOOLACCESS'); ?></p>

				<div id="groupname" <?php echo ($this->defaults['exec']=='@GROUP') ? 'style="display:block"': 'style="display:none"'; ?>>
					<input type="text" name="tool[membergroups]" id="t_groups" value="<?php echo \Components\Tools\Helpers\Html::getGroups($this->defaults['membergroups'], $this->id); ?>" />
					<p class="hint"><?php echo Lang::txt('COM_TOOLS_HINT_GROUPS'); ?></p>
				</div>

				<label for="t_code">
					<?php echo Lang::txt('COM_TOOLS_CODE_ACCESS'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
					<?php echo \Components\Tools\Helpers\Html::formSelect('tool[code]', 't_code', $codeChoices, $this->defaults['code']); ?>
				</label>

				<?php echo Lang::txt('COM_TOOLS_SIDE_TIPS_CODEACCESS'); ?>

				<label for="t_wiki">
					<?php echo Lang::txt('COM_TOOLS_WIKI_ACCESS'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
					<?php echo \Components\Tools\Helpers\Html::formSelect('tool[wiki]', 't_wiki', $wikiChoices, $this->defaults['wiki']); ?>
				</label>

				<p><?php echo Lang::txt('COM_TOOLS_SIDE_TIPS_WIKIACCESS'); ?></p>

				<label for="t_team">
					<?php echo Lang::txt('COM_TOOLS_DEVELOPMENT_TEAM'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
					<input type="text" name="tool[developers]" id="t_team" value="<?php echo \Components\Tools\Helpers\Html::getDevTeam($this->defaults['developers'], $this->id);  ?>" />
					<span class="hint"><?php echo Config::get('sitename') . ' ' . Lang::txt('COM_TOOLS_HINT_TEAM'); ?></span>
				</label>
			</fieldset>

			<p class="submit">
				<input type="submit" class="btn btn-success" value="<?php echo (!$this->id) ? Lang::txt('COM_TOOLS_REGISTER_TOOL') : Lang::txt('COM_TOOLS_SAVE_CHANGES'); ?>" />

				<?php if ($this->id) { ?>
					<a class="btn btn-secondary" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $this->defaults['toolname']); ?>" title="<?php echo Lang::txt('COM_TOOLS_HINT_CANCEL'); ?>">
						<?php echo Lang::txt('COM_TOOLS_CANCEL'); ?>
					</a>
				<?php } ?>
			</p>
		</form>
	</div><!-- / .subject -->
</section><!-- / .section -->