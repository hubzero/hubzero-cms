<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$privacyTxt = !$this->model->isPublic()
	? Lang::txt('COM_PROJECTS_PRIVATE')
	: Lang::txt('COM_PROJECTS_PUBLIC');

if (!$this->model->isPublic())
{
	$privacy = '<span class="private">' . ucfirst($privacyTxt) . '</span>';
}
else
{
	$privacy = '<a href="' . Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&preview=1') . '" title="' . Lang::txt('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE') . '">' . ucfirst($privacyTxt) . '</a>';
}

$start = ($this->showPrivacy == 2 && $this->model->access('member')) ? '<span class="h-privacy">' . $privacy . '</span> ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) : ucfirst(Lang::txt('COM_PROJECTS_PROJECT'));

?>
<div id="content-header" <?php if (!$this->showPic) { echo 'class="nopic"'; } ?>>
<?php if ($this->showPic) { ?>
	<div class="pthumb"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>" title="<?php echo Lang::txt('COM_PROJECTS_VIEW_UPDATES'); ?>"><img src="<?php echo	Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&controller=media&media=thumb'); ?>" alt="<?php echo $this->escape($this->model->get('title')); ?>" /></a></div>
<?php } ?>
	<div class="ptitle">
		<h2><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>"><?php echo \Hubzero\Utility\String::truncate($this->escape($this->model->get('title')), 50); ?> <span>(<?php echo $this->model->get('alias'); ?>)</span></a></h2>
		<?php if ($this->goBack)  { ?>
		<h3 class="returnln"><?php echo Lang::txt('COM_PROJECTS_RETURN_TO'); ?> <a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>"><?php echo Lang::txt('COM_PROJECTS_PROJECT_PAGE'); ?></a></h3>
		<?php } else { ?>
		<h3 <?php if ($this->showUnderline) { echo 'class="returnln"'; } ?>>
		<?php echo $start .' ' . Lang::txt('COM_PROJECTS_BY').' ';
			if ($this->model->groupOwner())
			{
				if ($cn = $this->model->groupOwner('cn'))
				{
					echo ' ' . Lang::txt('COM_PROJECTS_GROUP')
						. ' <a href="/groups/' . $cn . '">' . $cn . '</a>';
				}
				else
				{
					echo Lang::txt('COM_PROJECTS_UNKNOWN') . ' ' . Lang::txt('COM_PROJECTS_GROUP');
				}
			}
			else
			{
				echo '<a href="/members/' . $this->model->owner('id') . '">' . $this->model->owner('name') . '</a>';
			}
		?>
		<?php if ($this->showPrivacy == 1) { ?>
			<span class="privacy <?php if (!$this->model->isPublic()) { echo 'private'; } ?>"><?php if ($this->model->isPublic()) {  ?><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&preview=1'); ?>"><?php } ?><?php echo $privacyTxt; ?><?php if ($this->model->isPublic()) {  ?></a><?php } ?> <?php echo strtolower(Lang::txt('COM_PROJECTS_PROJECT')); ?>
			</span>
		<?php } ?>
		</h3>
		<?php } ?>
	</div>
</div><!-- / #content-header -->
