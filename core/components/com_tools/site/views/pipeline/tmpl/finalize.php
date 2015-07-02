<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

// get tool access text
$toolaccess = \Components\Tools\Helpers\Html::getToolAccess($this->status['exec'], $this->status['membergroups']);
// get source code access text
$codeaccess = \Components\Tools\Helpers\Html::getCodeAccess($this->status['code']);
// get wiki access text
$wikiaccess = \Components\Tools\Helpers\Html::getWikiAccess($this->status['wiki']);

$this->css('pipeline.css')
     ->js('pipeline.js');
?>
<header id="content-header">
	<h2><?php echo $this->escape($this->title); ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="icon-status status btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=status&app=' . $this->status['toolname']); ?>"><?php echo Lang::txt('COM_TOOLS_TOOL_STATUS'); ?></a></li>
			<li><a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=create'); ?>"><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<?php \Components\Tools\Helpers\Html::writeApproval('Approve'); ?>

	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>

	<h4><?php echo Lang::txt('COM_TOOLS_CONTRIBTOOL_FINAL_REVIEW'); ?>:</h4>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=finalizeversion&app=' . $this->status['toolname']); ?>" method="post" id="versionForm" name="versionForm">
		<fieldset class="versionfield">
			<div class="grid">
				<div class="col span-half">
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
					<input type="hidden" name="task" value="finalizeversion" />
					<input type="hidden" name="newstate" value="<?php echo \Components\Tools\Helpers\Html::getStatusNum('Approved') ?>" />
					<input type="hidden" name="id" value="<?php echo $this->status['toolid'] ?>" />
					<input type="hidden" name="app" value="<?php echo $this->status['toolname'] ?>" />
					<?php echo Html::input('token'); ?>
					<div>
						<h4>Tool Information <a class="edit button" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&app=' . $this->status['toolname']); ?>" title="Edit this version information">Edit</a></h4>
						<p><span class="heading"><?php echo Lang::txt('COM_TOOLS_TITLE'); ?>: </span><span class="desc"><?php echo $this->escape(stripslashes($this->status['title'])); ?></span></p>
						<p><span class="heading"><?php echo Lang::txt('COM_TOOLS_VERSION'); ?>: </span><span class="desc"><?php echo $this->escape(stripslashes($this->status['version'])); ?></span>
							<span class="actionlink">[<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=versions&action=confirm&app=' . $this->status['toolname']); ?>">edit</a>]</span></p>
						<p><span class="heading"><?php echo Lang::txt('COM_TOOLS_DESCRIPTION'); ?>: </span><span class="desc"><?php echo $this->escape(stripslashes($this->status['description'])); ?></span></p>
						<p><span class="heading"><?php echo Lang::txt('COM_TOOLS_TOOL_ACCESS'); ?>: </span><span class="desc"> <?php echo $toolaccess; ?></span></p>
						<p><span class="heading"><?php echo Lang::txt('COM_TOOLS_SOURCE_CODE'); ?>: </span><span class="desc"> <?php echo $codeaccess; ?></span></p>
						<p><span class="heading"><?php echo Lang::txt('COM_TOOLS_WIKI_ACCESS'); ?>: </span><span class="desc"> <?php echo $wikiaccess; ?></span></p>
						<p><span class="heading"><?php echo Lang::txt('COM_TOOLS_SCREEN_SIZE'); ?>: </span><span class="desc"> <?php echo $this->status['vncGeometry']; ?></span></p>
						<p><span class="heading"><?php echo Lang::txt('COM_TOOLS_DEVELOPERS'); ?>: </span><span class="desc"> <?php echo \Components\Tools\Helpers\Html::getDevTeam($this->status['developers']); ?></span></p>
						<p><span class="heading"><?php echo Lang::txt('COM_TOOLS_AUTHORS'); ?>: </span><span class="desc"> <?php echo \Components\Tools\Helpers\Html::getDevTeam($this->status['authors']); ?></span></p>
						<p><a href="<?php echo Route::url('index.php?option=com_resources&alias=' . $this->status['toolname'] . '&rev=dev'); ?>"><?php echo Lang::txt('COM_TOOLS_PREVIEW_RES_PAGE'); ?></a></p>
					</div>
				</div>
				<?php if ($this->status['license']) { ?>
				<div class="col span-half omega">
					<h4>
						<?php echo Lang::txt('COM_TOOLS_TOOL_LICENSE'); ?>
						<span class="actionlink">
							[<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=license&app=' . $this->status['toolname'] . '&action=confirm'); ?>"><?php echo Lang::txt('COM_TOOLS_EDIT'); ?></a>]
						</span>
					</h4>
					<pre class="licensetxt"><?php echo $this->escape(stripslashes($this->status['license'])); ?></pre>
				</div>
				<?php } ?>
			</div><!-- / .grid -->
			<div class="moveon">
				<input type="submit" value="<?php echo Lang::txt('COM_TOOLS_APPROVE_THIS_TOOL'); ?>" />
			</div>
		</fieldset>
	</form>
</section>