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

$this->css('pipeline.css')
     ->js('pipeline.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="icon-status status btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $this->status['toolname']); ?>"><?php echo Lang::txt('COM_TOOLS_TOOL_STATUS'); ?></a></li>
			<li><a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=create'); ?>"><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<?php
	($this->status['published'] != 1 && !$this->status['version']) ?  $hint = '1.0' :$hint = '' ; // if tool is under dev and no version was specified before
	$statuspath = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $this->status['toolname']);

	$newstate = ($this->action == 'edit') ? $this->status['state']: \Components\Tools\Helpers\Html::getStatusNum('Approved') ;
	$submitlabel = ($this->action == 'edit') ? Lang::txt('COM_TOOLS_SAVE') : Lang::txt('COM_TOOLS_USE_THIS_VERSION');
	if ($this->action == 'confirm')
	{
		\Components\Tools\Helpers\Html::writeApproval(Lang::txt('COM_TOOLS_CONFIRM_VERSION'));
	}

	$rconfig = Component::params( 'com_resources' );
	$hubDOIpath = $rconfig->get('doi');
	?>
	<div class="grid">
		<div class="col span-half">
			<?php if ($this->error) { ?>
				<p class="error"><?php echo $this->error; ?></p>
			<?php } ?>

			<?php if ($this->action != 'dev' && $this->status['state'] != \Components\Tools\Helpers\Html::getStatusNum('Published')) { ?>
				<?php if ($this->action == 'confirm' or $this->action == 'edit') { ?>
					<h4><?php echo Lang::txt('COM_TOOLS_VERSION_PLS_CONFIRM'); ?> <?php echo($this->action == 'edit') ? Lang::txt('COM_TOOLS_NEXT'): Lang::txt('COM_TOOLS_THIS'); ?> <?php echo Lang::txt('COM_TOOLS_TOOL_RELEASE'); ?>:</h4>
				<?php } else if ($this->action == 'new' && $this->status['toolname']) { // new version is required ?>
					<h4><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_ENTER_UNIQUE_VERSION'); ?>:</h4>
				<?php } ?>
					<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=saveversion&app=' . $this->status['toolname']); ?>" method="post" id="versionForm">
						<fieldset class="versionfield">
							<label for="newversion"><?php echo ucfirst(Lang::txt('COM_TOOLS_VERSION')); ?>: </label>
							<input type="text" name="newversion" id="newversion" value="<?php echo $this->status['version']; ?>" size="20" maxlength="15" />
							<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
							<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
							<input type="hidden" name="task" value="saveversion" />
							<input type="hidden" name="newstate" value="<?php echo $this->escape($newstate); ?>" />
							<input type="hidden" name="action" value="<?php echo $this->escape($this->action); ?>" />
							<input type="hidden" name="id" value="<?php echo $this->escape($this->status['toolid']); ?>" />
							<input type="hidden" name="toolname" value="<?php echo $this->escape($this->status['toolname']); ?>" />
							<?php echo Html::input('token'); ?>
							<input type="submit" value="<?php echo $submitlabel ?>" />
						</fieldset>
					</form>
			<?php } ?>

		<h3><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_EXISTING_VERSIONS'); ?>:</h3>

		<?php if ($this->versions && $this->status['toolname']) { // show versions ?>
			<table id="tktlist">
				<thead>
					<tr>
						<th scope="row"><?php echo ucfirst(Lang::txt('COM_TOOLS_VERSION')); ?></th>
						<th scope="row"><?php echo ucfirst(Lang::txt('COM_TOOLS_RELEASED')); ?></th>
						<th scope="row"><?php echo ucfirst(Lang::txt('COM_TOOLS_SUBVERSION')); ?></th>
						<th scope="row"><?php echo ucfirst(Lang::txt('COM_TOOLS_PUBLISHED')); ?></th>
						<th scope="row"></th>
					</tr>
				</thead>
				<tbody>
				<?php
					$i=0;
					foreach ($this->versions as $t)
					{
						// get tool access text
						$toolaccess = \Components\Tools\Helpers\Html::getToolAccess($t->toolaccess, $this->status['membergroups']);
						// get source code access text
						$codeaccess = \Components\Tools\Helpers\Html::getCodeAccess($t->codeaccess);
						// get wiki access text
						$wikiaccess = \Components\Tools\Helpers\Html::getWikiAccess($t->wikiaccess);

						$handle = (isset($t->doi) && $t->doi) ? $hubDOIpath.'r'.$this->status['resourceid'].'.'.$t->doi : '' ;

						$t->version = ($t->state==3 && $t->version==$this->status['currentversion']) ? Lang::txt('COM_TOOLS_NO_LABEL') : $t->version;
				?>
					<tr id="displays_<?php echo $i; ?>">
						<td>
							<span class="showcontrols">
								<a href="#confdiv_<?php echo $i; ?>" class="expand" id="exp_<?php echo $i; ?>">&nbsp;&nbsp;</a>
							</span>
							<?php echo ($t->version) ? $t->version : Lang::txt('COM_TOOLS_NA'); ?>
						</td>
						<td>
							<?php if ($t->state != 3) { ?>
								<?php echo $t->released ? Date::of($t->released)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) : 'N/A'; ?>
							<?php } else { ?>
								<span class="yes"><?php echo Lang::txt('COM_TOOLS_UNDER_DEVELOPMENT'); ?></span>
							<?php } ?>
						</td>
						<td>
							<?php if ($t->state!=3 or ($t->state==3 && $t->revision != $this->status['currentrevision'])) { echo $t->revision; } else { echo '-'; } ?>
						</td>
						<td>
							<span class="<?php echo ($t->state=='1' ? 'toolpublished' : 'toolunpublished'); ?>"></span>
						</td>
						<td>
							<?php if ($t->state == 1 && $this->admin) { ?>
								<span class="actionlink">
									<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&app=' . $this->status['toolname'] . '&editversion=current'); ?>"><?php echo Lang::txt('COM_TOOLS_EDIT'); ?></a>
								</span>
							<?php } else if ($t->state == 3) { ?>
								<span class="actionlink">
									<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&app=' . $this->status['toolname'] . '&editversion=dev'); ?>"><?php echo Lang::txt('COM_TOOLS_EDIT');?></a>
								</span>
							<?php } ?>
						</td>
					</tr>
					<tr id="configure_<?php echo $i; ?>" class="config hide">
						<td id="conftdone_<?php echo $i; ?>"></td>
						<td colspan="4" id="conftdtwo_<?php echo $i; ?>">
							<div id="confdiv_<?php echo $i; ?>" class="vmanage">
								<p><span class="heading"><?php echo ucfirst(Lang::txt('COM_TOOLS_TITLE')); ?>: </span><span class="desc"><?php echo $t->title; ?></span></p>
								<p><span class="heading"><?php echo ucfirst(Lang::txt('COM_TOOLS_DESCRIPTION')); ?>: </span><span class="desc"><?php echo $t->description; ?></span></p>
								<p><span class="heading"><?php echo ucfirst(Lang::txt('COM_TOOLS_AUTHORS')); ?>: </span><span class="desc"><?php echo \Components\Tools\Helpers\Html::getDevTeam($t->authors); ?></span></p>
								<p><span class="heading"><?php echo ucfirst(Lang::txt('COM_TOOLS_TOOL_ACCESS')); ?>: </span><span class="desc"><?php echo $toolaccess; ?></span></p>
								<p><span class="heading"><?php echo ucfirst(Lang::txt('COM_TOOLS_CODE_ACCESS')); ?>: </span><span class="desc"><?php echo $codeaccess; ?></span></p>
								<?php if ($handle) { echo ' <p><span class="heading">'.Lang::txt('COM_TOOLS_DOI').': </span><span class="desc"><a href="http://hdl.handle.net/'.$handle.'">'.$handle.'</a></span></p>'; } ?>
							</div>
						</td>
					</tr>
				<?php
						$i++;
					} // end foreach
				?>
			</tbody>
		</table>
	<?php
	} else { // no versions found
		echo (Lang::txt('COM_TOOLS_CONTRIBTOOL_NO_VERSIONS').' '.$this->status['toolname']. '. '.ucfirst(Lang::txt('COM_TOOLS_GO_BACK_TO')).' <a href="'.$statuspath.'">'.strtolower(Lang::txt('COM_TOOLS_TOOL_STATUS')).'</a>.');
	}
	?>
		</div>
		<div class="col span-half omega">
			<h3><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_VERSION_WHY_NEED_NUMBER'); ?></h3>
			<p><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_VERSION_WHY_NEED_NUMBER_ANSWER'); ?></p>
			<h3><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_VERSION_HOW_DECIDE'); ?></h3>
			<p><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_VERSION_HOW_DECIDE_ANSWER_ONE'); ?></p>
			<p><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_VERSION_HOW_DECIDE_ANSWER_TWO'); ?></p>
			<p><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_VERSION_HOW_DECIDE_ANSWER_THREE'); ?></p>
		</div>
	</div><!-- / .grid -->
</section>