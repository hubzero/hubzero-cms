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

$open             = ($this->code == '@OPEN') ? 1 : 0 ;
$this->codeaccess = ($this->code == '@OPEN') ? 'open' : 'closed';
$newstate         = ($this->action == 'confirm') ? 'Approved' :  $this->status['state'];
//$this->statuspath = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app='.$this->status['toolid']);

$codeChoices = array(
	'@OPEN' => 'open source (anyone can access code)',
	'@DEV'  => 'closed code'
);

$licenseChoices = array(
	'c1' => Lang::txt('Load a standard license')
);
if ($this->licenses)
{
	foreach ($this->licenses as $l)
	{
		if ($l->name != 'default')
		{
			$licenseChoices[$l->name] = $l->title;
		}
	}
}

$this->css('pipeline.css')
     ->js('pipeline.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="icon-status status btn" href="<?php echo Route::url('index.php?option='.$this->option.'&controller='.$this->controller.'&task=status&app='.$this->status['toolname']); ?>"><?php echo Lang::txt('TOOL_STATUS'); ?></a></li>
			<li><a class="icon-add btn add" href="<?php echo Route::url('index.php?option='.$this->option.'&controller='.$this->controller.'&task=create'); ?>"><?php echo Lang::txt('CONTRIBTOOL_NEW_TOOL'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>
	<?php
		if ($this->action == 'confirm')
		{
			\Components\Tools\Helpers\Html::writeApproval('Confirm license');
		}
		//$license = ($this->status['license'] && !$open) ? $this->status['license'] : '' ;
	?>
	<div class="grid">
		<div class="col span-half">
			<h3>
				<?php echo ($this->action == 'edit') ? Lang::txt('Specify license for next tool release:') : Lang::txt('Please confirm your license for this tool release:'); ?>
			</h3>
			<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=license'); ?>" method="post" id="versionForm" name="versionForm">
				<fieldset class="versionfield">
					<label><?php echo Lang::txt('CODE_ACCESS'); ?>:</label>
					<?php echo \Components\Tools\Helpers\Html::formSelect('t_code', 't_code', $codeChoices, $this->code, 'shifted', ''); ?>

					<div id="lic_cl"><?php echo Lang::txt('LICENSE'); ?>:</div>
					<div class="licinput" >
						<textarea name="license" cols="50" rows="15" id="license"><?php echo stripslashes($this->license_choice['text']); ?></textarea>
						<?php
						if ($this->licenses)
						{
							foreach ($this->licenses as $l)
							{
								echo '<input type="hidden" name="' . $l->name . '" id="' . $l->name . '" value="'.stripslashes(htmlentities($l->text)).'" />' . "\n";
							}
						}
						?>
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
						<input type="hidden" name="task" value="savelicense" />
						<input type="hidden" name="curcode" id="curcode" value="<?php echo $open; ?>" />
						<input type="hidden" name="newstate" value="<?php echo $this->escape($newstate); ?>" />
						<input type="hidden" name="action" value="<?php echo $this->escape($this->action); ?>" />
						<input type="hidden" name="toolid" value="<?php echo$this->escape( $this->status['toolid']); ?>" />
						<input type="hidden" name="alias" value="<?php echo $this->escape($this->status['toolname']); ?>" />
						<?php echo Html::input('token'); ?>
					</div>
					<div id="lic">
						<label><?php echo Lang::txt('LICENSE_TEMPLATE'); ?>:</label>
						<?php echo \Components\Tools\Helpers\Html::formSelect('templates', 'templates',  $licenseChoices, $this->license_choice['template'],'shifted',''); ?>
					</div>
					<div id="legendnotes">
						<p>
							<?php echo Lang::txt('LICENSE_TEMPLATE_TIP'); ?>:
							<br />[<?php echo Lang::txt('YEAR'); ?>]
							<br />[<?php echo Lang::txt('OWNER'); ?>]
							<br />[<?php echo Lang::txt('ORGANIZATION'); ?>]
							<br />[<?php echo strtoupper(Lang::txt('ONE_LINE_DESCRIPTION')); ?>]
							<br />[<?php echo Lang::txt('URL'); ?>]
						</p>
						<label><input type="checkbox" name="authorize" value="1" /> <?php echo Lang::txt('LICENSE_CERTIFY').' <strong>'.Lang::txt('OPEN_SOURCE').'</strong> '.Lang::txt('LICENSE_UNDER_SPECIFIED'); ?></label>
					</div>
					<div class="moveon">
						<input type="submit" value="<?php echo Lang::txt('Save'); ?>" />
					</div>
				</fieldset>
			</form>
		</div><!-- / .col span-half -->
		<div class="col span-half omega">
			<h3><?php echo Lang::txt('CONTRIBTOOL_LICENSE_WHAT_OPTIONS'); ?></h3>
			<p class="opensource">
				<?php echo '<strong>'.ucfirst(Lang::txt('OPEN_SOURCE')).'</strong><br />'.Lang::txt('CONTRIBTOOL_LICENSE_IF_YOU_CHOOSE').' <a href="http://www.opensource.org/" rel="external" title="Open Source Initiative">'.strtolower(Lang::txt('OPEN_SOURCE')).'</a>, '.Lang::txt('CONTRIBTOOL_LICENSE_OPEN_TXT'); ?>
			</p>
			<p class="error">
				<?php echo Lang::txt('CONTRIBTOOL_LICENSE_ATTENTION'); ?>
			</p>
			<p class="closedsource">
				<strong><?php echo ucfirst(Lang::txt('CLOSED_SOURCE')); ?></strong><br /><?php echo Lang::txt('CONTRIBTOOL_LICENSE_CLOSED_TXT'); ?>
			</p>
		</div><!-- / .col span-half omega -->
	</div><!-- / .grid -->
</section><!-- / .main section -->