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

// No direct access.
defined('_HZEXEC_') or die();

$open             = ($this->code == '@OPEN') ? 1 : 0 ;
$this->codeaccess = ($this->code == '@OPEN') ? 'open' : 'closed';
$newstate         = ($this->action == 'confirm') ? 'Approved' :  $this->status['state'];

$codeChoices = array(
	'@OPEN' => Lang::txt('COM_TOOLS_OPEN_SOURCE'),
	'@DEV'  => Lang::txt('COM_TOOLS_CLOSED_SOURCE')
);

$licenseChoices = array(
	'0' => Lang::txt('Choose a template')
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
			<li><a class="icon-status btn" href="<?php echo Route::url('index.php?option='.$this->option.'&controller='.$this->controller.'&task=status&app='.$this->status['toolname']); ?>"><?php echo Lang::txt('COM_TOOLS_TOOL_STATUS'); ?></a></li>
			<li><a class="icon-add btn" href="<?php echo Route::url('index.php?option='.$this->option.'&controller='.$this->controller.'&task=create'); ?>"><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL'); ?></a></li>
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
			<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=license&app=' . $this->status['toolname']); ?>" method="post" id="licenseForm" name="licenseForm">
				<fieldset class="versionfield">
					<label><?php echo Lang::txt('COM_TOOLS_CODE_ACCESS'); ?>:</label>
					<?php echo \Components\Tools\Helpers\Html::formSelect('t_code', 't_code', $codeChoices, $this->code, 'shifted', ''); ?>
					<span id="choice-icon">&nbsp;</span>
					<div id="closed-source">
						<h4><?php echo Lang::txt('COM_TOOLS_LICENSE_ARE_YOU_SURE'); ?></h4>
						<div class="why-open"><?php echo Lang::txt('COM_TOOLS_LICENSE_WHY_OPEN_SOURCE'); ?></div>
						<label><?php echo Lang::txt('COM_TOOLS_LICENSE_CLOSED_REASON'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
							<textarea name="reason" cols="30" rows="5" id="reason"></textarea>
						</label>
					</div>
					<div id="open-source">
						<div id="lic">
							<label><?php echo Lang::txt('COM_TOOLS_LICENSE_TEMPLATE'); ?>:</label>
							<?php echo \Components\Tools\Helpers\Html::formSelect('templates', 'templates',  $licenseChoices, $this->license_choice['template'], 'shifted', ''); ?>
						</div>
						<div class="licinput">
							<label>
								<?php echo Lang::txt('COM_TOOLS_LICENSE_TEXT'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
								<textarea name="license" cols="50" rows="15" id="license" placeholder="<?php echo Lang::txt('COM_TOOLS_ENTER_LICENSE_TEXT'); ?>"> <?php echo $this->escape(stripslashes($this->license_choice['text'])); ?></textarea>
							</label>
							<?php
							if ($this->licenses)
							{
								foreach ($this->licenses as $l)
								{
									echo '<div class="hidden" id="' . $l->name . '" >' . $this->escape(stripslashes($l->text)) . '</div>' . "\n";
								}
							}
							?>
							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
							<input type="hidden" name="task" value="savelicense" />
							<input type="hidden" name="curcode" id="curcode" value="<?php echo $open; ?>" />
							<input type="hidden" name="newstate" value="<?php echo $newstate; ?>" />
							<input type="hidden" name="action" value="<?php echo $this->action; ?>" />
							<input type="hidden" name="toolid" value="<?php echo $this->status['toolid']; ?>" />
							<input type="hidden" name="alias" value="<?php echo $this->status['toolname']; ?>" />
							<?php echo Html::input('token'); ?>
						</div>
						<div id="legendnotes">
							<h3><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_LICENSE_TEMPLATE_CHOICE'); ?></h3>
							<p>
								<?php echo Lang::txt('COM_TOOLS_LICENSE_TEMPLATE_TIP'); ?>:
								<br />[<?php echo strtoupper(Lang::txt('COM_TOOLS_YEAR')); ?>]
								<br />[<?php echo strtoupper(Lang::txt('COM_TOOLS_OWNER')); ?>]
								<br />[<?php echo strtoupper(Lang::txt('COM_TOOLS_ORGANIZATION')); ?>]
								<br />[<?php echo strtoupper(Lang::txt('COM_TOOLS_ONE_LINE_DESCRIPTION')); ?>]
								<br />[<?php echo strtoupper(Lang::txt('COM_TOOLS_URL')); ?>]
							</p>
						</div>
						<label for="field-authorize">
							<input type="checkbox" name="authorize" id="field-authorize" value="1" />
							<span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span> <?php echo Lang::txt('COM_TOOLS_LICENSE_CERTIFY') . ' '.Lang::txt('COM_TOOLS_LICENSE_UNDER_SPECIFIED'); ?>
						</label>
					</div>
					<div class="moveon">
						<input type="submit" value="<?php echo Lang::txt('COM_TOOLS_SAVE'); ?>" />
					</div>
				</fieldset>
			</form>
		</div><!-- / .col span-half -->
		<div class="col span-half omega">
			<h3><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_LICENSE_WHAT_OPTIONS'); ?></h3>
			<p class="opensource">
				<?php echo '<strong>'.ucfirst(Lang::txt('COM_TOOLS_OPEN_SOURCE')).'</strong><br />'.Lang::txt('COM_TOOLS_CONTRIBTOOL_LICENSE_IF_YOU_CHOOSE').' <a href="http://www.opensource.org/" rel="external" title="Open Source Initiative">'.strtolower(Lang::txt('COM_TOOLS_OPEN_SOURCE')).'</a>, '.Lang::txt('COM_TOOLS_CONTRIBTOOL_LICENSE_OPEN_TXT'); ?>
			</p>
			<p class="closedsource">
				<strong><?php echo ucfirst(Lang::txt('COM_TOOLS_CLOSED_SOURCE')); ?></strong><br /><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_LICENSE_CLOSED_TXT'); ?>
			</p>
		</div><!-- / .col span-half -->
	</div><!-- / .grid -->
</section><!-- / .main section -->